<?php
include "../include/session.php";
requireAdmin();
include "../include/db.php";
include "../config.php";
include "../include/admin_nav_sidebar.php";

// ── 1. Total counts ──────────────────────────────────────────────────────────
$total_users      = total($conn, 'users')['total'];
$total_posts      = total($conn, 'posts')['total'];
$total_categories = total($conn, 'categories')['total'];
$total_comments   = total($conn, 'comments')['total'];

// ── 2. Growth: this month vs last month ──────────────────────────────────────
$growth_sql = "
    SELECT
        SUM(CASE WHEN MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW()) THEN 1 ELSE 0 END) AS this_month,
        SUM(CASE WHEN MONTH(created_at)=MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                  AND YEAR(created_at)=YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))  THEN 1 ELSE 0 END) AS last_month
    FROM ";

$u_growth_res = mysqli_query($conn, $growth_sql . "users");
$u_growth = $u_growth_res ? mysqli_fetch_assoc($u_growth_res) : ['this_month' => 0, 'last_month' => 0];

$p_growth_res = mysqli_query($conn, $growth_sql . "posts");
$p_growth = $p_growth_res ? mysqli_fetch_assoc($p_growth_res) : ['this_month' => 0, 'last_month' => 0];

function calc_growth($this_m, $last_m) {
    $this_m = (int)($this_m ?? 0);
    $last_m = (int)($last_m ?? 0);
    return round(($this_m - $last_m) / max($last_m, 1) * 100);
}
$users_growth = calc_growth($u_growth['this_month'] ?? 0, $u_growth['last_month'] ?? 0);
$posts_growth = calc_growth($p_growth['this_month'] ?? 0, $p_growth['last_month'] ?? 0);

function growth_badge($pct) {
    if ($pct > 0)  return '<span class="text-green-600 text-xs font-bold bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-full">+' . $pct . '%</span>';
    if ($pct < 0)  return '<span class="text-red-500  text-xs font-bold bg-red-50   dark:bg-red-900/20   px-2 py-1 rounded-full">' . $pct . '%</span>';
    return '<span class="text-slate-400 text-xs font-bold bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-full">0%</span>';
}

// ── 3. Recent 5 posts ────────────────────────────────────────────────────────
$recent_posts_res = mysqli_query($conn,
    "SELECT p.id, p.title, p.status, p.created_at,
            u.name AS author_name, u.profile_image
     FROM posts p
     LEFT JOIN users u ON p.author_id = u.id
     ORDER BY p.created_at DESC LIMIT 5");
$recent_posts = [];
if ($recent_posts_res) {
    while ($r = mysqli_fetch_assoc($recent_posts_res)) $recent_posts[] = $r;
}

// ── 4. Recent activity: 5 comments + 3 new users, merged & sorted ────────────
$comments_res = mysqli_query($conn,
    "SELECT c.comment, c.created_at,
            u.name AS user_name, u.profile_image,
            p.title AS post_title
     FROM comments c
     LEFT JOIN users u ON c.user_id = u.id
     LEFT JOIN posts p ON c.post_id = p.id
     ORDER BY c.created_at DESC LIMIT 5");
$activity = [];
if ($comments_res) {
    while ($r = mysqli_fetch_assoc($comments_res)) {
        $activity[] = ['type' => 'comment', 'time' => strtotime($r['created_at']), 'data' => $r];
    }
}

$new_users_res = mysqli_query($conn,
    "SELECT name, profile_image, created_at, role
     FROM users ORDER BY created_at DESC LIMIT 3");
if ($new_users_res) {
    while ($r = mysqli_fetch_assoc($new_users_res)) {
        $activity[] = ['type' => 'user', 'time' => strtotime($r['created_at']), 'data' => $r];
    }
}
usort($activity, fn($a, $b) => $b['time'] - $a['time']);

// ── 5. Top 5 authors by total views ─────────────────────────────────────────
$authors_res = mysqli_query($conn,
    "SELECT u.name, u.profile_image,
            SUM(p.views) AS total_views, COUNT(p.id) AS post_count
     FROM users u
     INNER JOIN posts p ON p.author_id = u.id
     GROUP BY u.id, u.name, u.profile_image
     ORDER BY total_views DESC LIMIT 5");
$top_authors = [];
if ($authors_res) {
    while ($r = mysqli_fetch_assoc($authors_res)) $top_authors[] = $r;
}

// ── 6. Trending posts (last 7 days) ─────────────────────────────────────────
$trending_res = mysqli_query($conn,
    "SELECT id, title, slug, views FROM posts
     WHERE status='published' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
     ORDER BY views DESC LIMIT 5");
$trending_posts = [];
if ($trending_res) {
    while ($r = mysqli_fetch_assoc($trending_res)) $trending_posts[] = $r;
}

// ── 7. Monthly views trend (last 6 months) ───────────────────────────────────
$chart_data = [];
$current_year = date('Y');
$current_month = date('m');
for ($i = 5; $i >= 0; $i--) {
    $time = mktime(0, 0, 0, $current_month - $i, 1, $current_year);
    $key = date('Y-m', $time);
    $label = date('M Y', $time);
    $chart_data[$key] = [
        'label' => $label,
        'total' => 0
    ];
}

$views_trend_res = mysqli_query($conn,
    "SELECT DATE_FORMAT(created_at,'%b %Y') AS label, DATE_FORMAT(created_at,'%Y-%m') as sort_key, SUM(views) AS total
     FROM posts
     WHERE status='published' AND created_at >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 5 MONTH), '%Y-%m-01')
     GROUP BY DATE_FORMAT(created_at,'%Y-%m'), DATE_FORMAT(created_at,'%b %Y')
     ORDER BY sort_key ASC");

if ($views_trend_res) {
    while ($r = mysqli_fetch_assoc($views_trend_res)) {
        $key = $r['sort_key'];
        if (isset($chart_data[$key])) {
            $chart_data[$key]['total'] = (int)$r['total'];
        }
    }
}

$chart_labels = [];
$chart_views  = [];
foreach ($chart_data as $data) {
    $chart_labels[] = $data['label'];
    $chart_views[]  = $data['total'];
}

// ── 8. Monthly new users (last 6 months) ─────────────────────────────────────
$user_data = [];
for ($i = 5; $i >= 0; $i--) {
    $time = mktime(0, 0, 0, $current_month - $i, 1, $current_year);
    $key = date('Y-m', $time);
    $label = date('M Y', $time);
    $user_data[$key] = [
        'label' => $label,
        'total' => 0
    ];
}

$users_trend_res = mysqli_query($conn,
    "SELECT DATE_FORMAT(created_at,'%b %Y') AS label, DATE_FORMAT(created_at,'%Y-%m') as sort_key, COUNT(*) AS total
     FROM users
     WHERE created_at >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 5 MONTH), '%Y-%m-01')
     GROUP BY DATE_FORMAT(created_at,'%Y-%m'), DATE_FORMAT(created_at,'%b %Y')
     ORDER BY sort_key ASC");

if ($users_trend_res) {
    while ($r = mysqli_fetch_assoc($users_trend_res)) {
        $key = $r['sort_key'];
        if (isset($user_data[$key])) {
            $user_data[$key]['total'] = (int)$r['total'];
        }
    }
}

$user_labels = [];
$user_counts = [];
foreach ($user_data as $data) {
    $user_labels[] = $data['label'];
    $user_counts[] = $data['total'];
}

// helpers
function status_badge($status) {
    $map = [
        'published' => 'bg-green-100  text-green-700  dark:bg-green-900/30  dark:text-green-400',
        'draft'     => 'bg-blue-100   text-blue-700   dark:bg-blue-900/30   dark:text-blue-400',
        'pending'   => 'bg-amber-100  text-amber-700  dark:bg-amber-900/30  dark:text-amber-400',
        'review'    => 'bg-amber-100  text-amber-700  dark:bg-amber-900/30  dark:text-amber-400',
        'rejected'  => 'bg-red-100    text-red-700    dark:bg-red-900/30    dark:text-red-400',
    ];
    $key = strtolower(trim($status));
    $cls = $map[$key] ?? 'bg-slate-100 text-slate-600';
    return '<span class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full uppercase ' . $cls . '">' . htmlspecialchars($status) . '</span>';
}

function author_avatar($name, $img, $size = 8) {
    $safe = htmlspecialchars($name ?? 'U');
    $initials = strtoupper(implode('', array_map(fn($w) => $w[0], explode(' ', trim($safe)))));
    $initials = substr($initials, 0, 2);
    if ($img) {
        return '<img src="' . htmlspecialchars($img) . '" alt="' . $safe . '" class="h-' . $size . ' w-' . $size . ' rounded-full object-cover border border-slate-200 dark:border-slate-700">';
    }
    return '<div class="h-' . $size . ' w-' . $size . ' rounded-full bg-primary/10 text-primary text-[10px] flex items-center justify-center font-bold">' . $initials . '</div>';
}

function time_ago($ts) {
    $diff = time() - $ts;
    if ($diff < 60)       return $diff . 's ago';
    if ($diff < 3600)     return floor($diff/60) . 'm ago';
    if ($diff < 86400)    return floor($diff/3600) . 'h ago';
    if ($diff < 604800)   return floor($diff/86400) . 'd ago';
    return date('M j', $ts);
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Blog Fusion – Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary":           "#7C3AED",
                        "primary-hover":     "#6D28D9",
                        "background-light":  "#f8f7ff",
                        "background-dark":   "#0f172a",
                        "indigo-accent":     "#4F46E5",
                    },
                    fontFamily: { "display": ["Public Sans", "sans-serif"] },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
<div class="flex h-screen overflow-hidden">

    <?= slidebar('dashboard'); ?>
    <div id="overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <?= ad_navbar(); ?>

        <!-- ═══════════════════ DASHBOARD CONTENT ═══════════════════ -->
        <div class="flex-1 overflow-y-auto p-6 lg:p-8">

            <!-- Page header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-3xl font-black tracking-tight">Dashboard Overview</h2>
                    <p class="text-slate-500 mt-1">Welcome back — here's what's happening today.</p>
                </div>
                <a href="posts.php"
                   class="bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/20 transition-all sm:self-center self-start">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    New Post
                </a>
            </div>

            <!-- ── STAT CARDS ──────────────────────────────────────────── -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <!-- Total Users -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-12 w-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined">group</span>
                        </div>
                        <?= growth_badge($users_growth) ?>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Total Users</p>
                    <h3 class="text-2xl font-bold mt-1"><?= number_format($total_users) ?></h3>
                    <p class="text-xs text-slate-400 mt-1">vs last month</p>
                </div>

                <!-- Total Posts -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-12 w-12 bg-indigo-600/10 text-indigo-600 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined">article</span>
                        </div>
                        <?= growth_badge($posts_growth) ?>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Total Posts</p>
                    <h3 class="text-2xl font-bold mt-1"><?= number_format($total_posts) ?></h3>
                    <p class="text-xs text-slate-400 mt-1">vs last month</p>
                </div>

                <!-- Categories -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-12 w-12 bg-amber-500/10 text-amber-500 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined">category</span>
                        </div>
                        <span class="text-slate-400 text-xs font-bold bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-full">—</span>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Categories</p>
                    <h3 class="text-2xl font-bold mt-1"><?= number_format($total_categories) ?></h3>
                    <p class="text-xs text-slate-400 mt-1">all time</p>
                </div>

                <!-- Comments -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-12 w-12 bg-pink-500/10 text-pink-500 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined">forum</span>
                        </div>
                        <span class="text-slate-400 text-xs font-bold bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-full">—</span>
                    </div>
                    <p class="text-slate-500 text-sm font-medium">Comments</p>
                    <h3 class="text-2xl font-bold mt-1"><?= number_format($total_comments) ?></h3>
                    <p class="text-xs text-slate-400 mt-1">all time</p>
                </div>
            </div>

            <!-- ── ROW 2: Recent Posts + Activity Feed ─────────────────── -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">

                <!-- Recent Posts Table (xl: 2 cols) -->
                <div class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="font-bold text-lg">Recent Posts</h3>
                        <a href="posts.php" class="text-primary text-sm font-semibold hover:underline">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-slate-500 text-xs uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/50">
                                    <th class="px-6 py-4 font-semibold">Post Title</th>
                                    <th class="px-6 py-4 font-semibold">Author</th>
                                    <th class="px-6 py-4 font-semibold">Date</th>
                                    <th class="px-6 py-4 font-semibold text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <?php if (empty($recent_posts)): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-slate-400 text-sm">No posts yet.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($recent_posts as $post): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-sm truncate max-w-[220px]" title="<?= htmlspecialchars($post['title']) ?>">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <?= author_avatar($post['author_name'], '../'.$post['profile_image'], 6) ?>
                                            <span class="text-sm"><?= htmlspecialchars($post['author_name'] ?? 'Unknown') ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 whitespace-nowrap">
                                        <?= date('M j, Y', strtotime($post['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?= status_badge($post['status']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Activity Feed (xl: 1 col) -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800">
                        <h3 class="font-bold text-lg">Recent Activity</h3>
                    </div>
                    <div class="p-6 space-y-5 flex-1">
                        <?php if (empty($activity)): ?>
                            <p class="text-slate-400 text-sm text-center py-6">No recent activity.</p>
                        <?php else: ?>
                        <?php foreach ($activity as $item): ?>
                            <?php if ($item['type'] === 'comment'): $d = $item['data']; ?>
                            <div class="flex gap-3">
                                <div class="relative flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[18px]">chat</span>
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 h-3.5 w-3.5 bg-green-500 border-2 border-white dark:border-slate-900 rounded-full"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm leading-snug">
                                        <strong><?= htmlspecialchars($d['user_name'] ?? 'Someone') ?></strong>
                                        commented on
                                        <span class="text-primary font-medium truncate">"<?= htmlspecialchars(mb_strimwidth($d['post_title'] ?? '', 0, 30, '…')) ?>"</span>
                                    </p>
                                    <?php if (!empty($d['content'])): ?>
                                    <p class="text-xs text-slate-400 mt-1 italic truncate">"<?= htmlspecialchars(mb_strimwidth($d['content'], 0, 60, '…')) ?>"</p>
                                    <?php endif; ?>
                                    <p class="text-[10px] text-slate-500 mt-1.5 uppercase font-bold tracking-tighter"><?= time_ago($item['time']) ?></p>
                                </div>
                            </div>
                            <?php else: $d = $item['data']; ?>
                            <div class="flex gap-3">
                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-600/10 text-indigo-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm leading-snug">
                                        <strong><?= htmlspecialchars($d['name']) ?></strong>
                                        joined as <span class="capitalize"><?= htmlspecialchars($d['role'] ?? 'member') ?></span>.
                                    </p>
                                    <p class="text-[10px] text-slate-500 mt-1.5 uppercase font-bold tracking-tighter"><?= time_ago($item['time']) ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">
                        <a href="comments.php" class="w-full text-center block py-2 text-sm font-semibold text-slate-500 hover:text-primary transition-colors">
                            View Full Activity Log
                        </a>
                    </div>
                </div>
            </div>

            <!-- ── ROW 3: Traffic Chart · Author Leaderboard · Trending Posts ── -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Site Traffic Chart -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 flex flex-col">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="font-bold text-lg">Site Traffic</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Monthly views — last 6 months</p>
                        </div>
                        <div class="h-9 w-9 bg-primary/10 text-primary rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">trending_up</span>
                        </div>
                    </div>
                    <div class="flex-1 relative" style="min-height:200px;">
                        <canvas id="trafficChart"></canvas>
                    </div>
                </div>

                <!-- Author Leaderboard -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="font-bold text-lg">Top Authors</h3>
                        <span class="text-xs text-slate-400 font-medium uppercase tracking-wider">By Views</span>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($top_authors)): ?>
                        <p class="px-6 py-8 text-slate-400 text-sm text-center">No author data yet.</p>
                        <?php else: ?>
                        <?php foreach ($top_authors as $i => $author): ?>
                        <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                            <span class="text-sm font-black w-5 text-center <?= $i === 0 ? 'text-amber-500' : ($i === 1 ? 'text-slate-400' : ($i === 2 ? 'text-amber-700' : 'text-slate-400')) ?>">
                                #<?= $i + 1 ?>
                            </span>
                            <?= author_avatar($author['name'], BASE_URL.$author['profile_image'], 9) ?>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm truncate"><?= htmlspecialchars($author['name']) ?></p>
                                <p class="text-xs text-slate-400"><?= number_format($author['post_count']) ?> posts</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-primary"><?= number_format($author['total_views']) ?></p>
                                <p class="text-[10px] text-slate-400 uppercase font-semibold">views</p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Trending Posts -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="font-bold text-lg">Trending This Week</h3>
                        <span class="text-xs text-slate-400 font-medium uppercase tracking-wider">Top Views</span>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($trending_posts)): ?>
                        <p class="px-6 py-8 text-slate-400 text-sm text-center">No trending posts this week.</p>
                        <?php else: ?>
                        <?php foreach ($trending_posts as $i => $tp): ?>
                        <div class="px-6 py-4 flex items-start gap-3 hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                            <span class="mt-0.5 text-sm font-black w-5 text-center text-slate-300 dark:text-slate-600"><?= $i + 1 ?></span>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm leading-snug line-clamp-2"><?= htmlspecialchars($tp['title']) ?></p>
                                <div class="flex items-center gap-1 mt-1.5">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">visibility</span>
                                    <span class="text-xs text-slate-500 font-semibold"><?= number_format($tp['views']) ?> views</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- end row 3 -->

        </div>
        <!-- end dashboard content -->
    </main>
</div>

<script src="../assets/js/admin.js"></script>

<!-- ─── Chart.js Traffic Chart ─── -->
<script>
(function () {
    const labels = <?= json_encode($chart_labels) ?>;
    const views  = <?= json_encode($chart_views)  ?>;

    const isDark = document.documentElement.classList.contains('dark');
    const gridColor  = isDark ? 'rgba(148,163,184,0.1)' : 'rgba(148,163,184,0.25)';
    const labelColor = isDark ? '#94a3b8' : '#64748b';

    const ctx = document.getElementById('trafficChart');
    if (!ctx) return;

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0,   'rgba(124,58,237,0.35)');
    gradient.addColorStop(1,   'rgba(124,58,237,0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length ? labels : ['No data'],
            datasets: [{
                label: 'Page Views',
                data:  views.length  ? views  : [0],
                fill:  true,
                backgroundColor: gradient,
                borderColor: '#7C3AED',
                borderWidth: 2.5,
                tension: 0.42,
                pointBackgroundColor: '#7C3AED',
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor:  '#f8fafc',
                    bodyColor:   '#94a3b8',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed.y.toLocaleString() + ' views'
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: gridColor },
                    ticks: { color: labelColor, font: { size: 11 } }
                },
                y: {
                    grid: { color: gridColor },
                    ticks: { color: labelColor, font: { size: 11 },
                             callback: v => v >= 1000 ? (v/1000).toFixed(1) + 'k' : v }
                }
            }
        }
    });
})();
</script>
</body>
</html>
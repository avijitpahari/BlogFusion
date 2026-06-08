<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAuthor();
include BASE_PATH . 'include/db.php';
include BASE_PATH . 'include/author_nav_sidebar.php';

$id = $_SESSION['user_id'];

/* ── Time Range Filter ───────────────────────────────────── */
$range = $_GET['range'] ?? 'all';
$valid_ranges = ['24h', '7d', '30d', 'all'];
if (!in_array($range, $valid_ranges)) {
    $range = 'all';
}

$date_filter = "";
if ($range === '24h') {
    $date_filter = " AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
} elseif ($range === '7d') {
    $date_filter = " AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif ($range === '30d') {
    $date_filter = " AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
}

$posts_date_filter = $date_filter;
$comments_date_filter_sub = str_replace('created_at', 'c.created_at', $date_filter);
$reactions_date_filter_sub = str_replace('created_at', 'r.created_at', $date_filter);
$share_date_filter_sub = str_replace('created_at', 's.created_at', $date_filter);

/* ── Helper ─────────────────────────────────────────────── */
function fmt_num($n): string
{
    if ($n >= 1_000_000_000) return round($n / 1_000_000_000, 1) . 'B';
    if ($n >= 1_000_000)     return round($n / 1_000_000, 1) . 'M';
    if ($n >= 1_000)         return round($n / 1_000, 1) . 'K';
    return (string)(int)$n;
}

function time_ago_a(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return $diff . 's ago';
    if ($diff < 3600)   return floor($diff / 60) . 'm ago';
    if ($diff < 86400)  return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('M d', strtotime($datetime));
}

/* ── Aggregate Stats ─────────────────────────────────────── */
$row = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT
        COUNT(*)                                AS total_posts,
        SUM(views)                              AS total_views,
        SUM(status='published')                 AS published_count,
        SUM(status='draft')                     AS draft_count
     FROM posts WHERE author_id = $id" . $posts_date_filter
));
$total_posts     = (int)($row['total_posts'] ?? 0);
$total_views     = (int)($row['total_views'] ?? 0);
$published_count = (int)($row['published_count'] ?? 0);
$draft_count     = (int)($row['draft_count'] ?? 0);

$total_reactions = (int)(mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS t FROM reactions r
     INNER JOIN posts p ON r.post_id = p.id
     WHERE p.author_id = $id" . str_replace('created_at', 'r.created_at', $date_filter)
))['t'] ?? 0);

$total_comments = (int)(mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS t FROM comments c
     INNER JOIN posts p ON c.post_id = p.id
     WHERE p.author_id = $id" . str_replace('created_at', 'c.created_at', $date_filter)
))['t'] ?? 0);

$total_shares = (int)(mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COALESCE(SUM(s.share), 0) AS t FROM share s
     INNER JOIN posts p ON s.post_id = p.id
     WHERE p.author_id = $id" . str_replace('created_at', 's.created_at', $date_filter)
))['t'] ?? 0);

$avg_views    = $total_posts > 0 ? round($total_views    / $total_posts, 1) : 0;
$avg_reactions = $total_posts > 0 ? round($total_reactions / $total_posts, 1) : 0;

/* ── Top 5 Posts by Views ────────────────────────────────── */
$top_posts_res = mysqli_query($conn,
    "SELECT p.id, p.title, p.slug, p.image, p.views, p.created_at,
            (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id" . $comments_date_filter_sub . ") AS comment_count,
            (SELECT COUNT(*) FROM reactions r WHERE r.post_id = p.id" . $reactions_date_filter_sub . ") AS reaction_count,
            (SELECT COALESCE(SUM(s.share),0) FROM share s WHERE s.post_id = p.id" . $share_date_filter_sub . ") AS share_count
     FROM posts p
     WHERE p.author_id = $id AND p.status = 'published'" . $posts_date_filter . "
     ORDER BY p.views DESC
     LIMIT 5"
);
$top_posts = $top_posts_res ? mysqli_fetch_all($top_posts_res, MYSQLI_ASSOC) : [];

/* ── Reaction Breakdown by Emoji ─────────────────────────── */
$reaction_res = mysqli_query($conn,
    "SELECT r.emoji, COUNT(*) AS cnt
     FROM reactions r
     INNER JOIN posts p ON r.post_id = p.id
     WHERE p.author_id = $id" . str_replace('created_at', 'r.created_at', $date_filter) . "
     GROUP BY r.emoji"
);
$emoji_mapping = [
    '👍' => 'like',
    '❤️' => 'love',
    '😮' => 'wow',
    '😂' => 'haha',
    '😢' => 'sad',
    '😡' => 'angry'
];
$reaction_types = ['like' => 0, 'love' => 0, 'wow' => 0, 'haha' => 0, 'sad' => 0, 'angry' => 0];
if ($reaction_res) {
    while ($rrow = mysqli_fetch_assoc($reaction_res)) {
        $emoji = trim($rrow['emoji'] ?? '');
        $key = $emoji_mapping[$emoji] ?? strtolower($emoji);
        if (isset($reaction_types[$key])) $reaction_types[$key] = (int)$rrow['cnt'];
    }
}

/* ── Category Distribution ───────────────────────────────── */
$cat_res = mysqli_query($conn,
    "SELECT cat.name, COUNT(*) AS cnt
     FROM posts p
     LEFT JOIN categories cat ON cat.id = p.category_id
     WHERE p.author_id = $id" . $posts_date_filter . "
     GROUP BY cat.id, cat.name
     ORDER BY cnt DESC"
);
$cat_labels = [];
$cat_counts = [];
if ($cat_res) {
    while ($crow = mysqli_fetch_assoc($cat_res)) {
        $cat_labels[] = htmlspecialchars($crow['name'] ?? 'Uncategorized');
        $cat_counts[] = (int)$crow['cnt'];
    }
}

function get_author_trend($conn, $author_id, $table, $col = 'created_at', $range = 'all', $sum_views = false) {
    $val_col = $sum_views ? "SUM(views)" : "COUNT(*)";
    $author_filter = $table === 'posts' ? "author_id = $author_id" : "post_id IN (SELECT id FROM posts WHERE author_id = $author_id)";
    
    if ($range === '24h') {
        $trend = [];
        for ($i = 23; $i >= 0; $i--) {
            $time = strtotime("-$i hours");
            $ym = date('Y-m-d H:00', $time);
            $label = date('H:00', $time);
            $trend[$ym] = ['label' => $label, 'total' => 0];
        }
        $query = "SELECT DATE_FORMAT($col, '%Y-%m-%d %H:00') AS ym, $val_col AS total 
                  FROM $table 
                  WHERE $author_filter AND $col >= DATE_SUB(NOW(), INTERVAL 24 HOUR) 
                  GROUP BY ym";
    } elseif ($range === '7d') {
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $time = strtotime("-$i days");
            $ym = date('Y-m-d', $time);
            $label = date('D M j', $time);
            $trend[$ym] = ['label' => $label, 'total' => 0];
        }
        $query = "SELECT DATE_FORMAT($col, '%Y-%m-%d') AS ym, $val_col AS total 
                  FROM $table 
                  WHERE $author_filter AND $col >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                  GROUP BY ym";
    } elseif ($range === '30d') {
        $trend = [];
        for ($i = 29; $i >= 0; $i--) {
            $time = strtotime("-$i days");
            $ym = date('Y-m-d', $time);
            $label = date('M j', $time);
            $trend[$ym] = ['label' => $label, 'total' => 0];
        }
        $query = "SELECT DATE_FORMAT($col, '%Y-%m-%d') AS ym, $val_col AS total 
                  FROM $table 
                  WHERE $author_filter AND $col >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                  GROUP BY ym";
    } else {
        $trend = [];
        $current_year = date('Y');
        $current_month = date('m');
        for ($i = 5; $i >= 0; $i--) {
            $time = mktime(0, 0, 0, $current_month - $i, 1, $current_year);
            $ym = date('Y-m', $time);
            $label = date('M Y', $time);
            $trend[$ym] = ['label' => $label, 'total' => 0];
        }
        $query = "SELECT DATE_FORMAT($col, '%Y-%m') AS ym, $val_col AS total 
                  FROM $table 
                  WHERE $author_filter AND $col >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 5 MONTH), '%Y-%m-01') 
                  GROUP BY ym
                  ORDER BY ym ASC";
    }
    
    $res = mysqli_query($conn, $query);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $ym = $r['ym'];
            if (isset($trend[$ym])) {
                $trend[$ym]['total'] = (int)$r['total'];
            }
        }
    }
    
    $labels = [];
    $data = [];
    foreach ($trend as $val) {
        $labels[] = $val['label'];
        $data[] = $val['total'];
    }
    return ['labels' => $labels, 'data' => $data];
}

/* ── Post Output trend based on range ─────────────────── */
$trend_monthly = get_author_trend($conn, $id, 'posts', 'created_at', $range);
$month_labels = $trend_monthly['labels'];
$month_counts = $trend_monthly['data'];

/* ── Views trend based on range ────── */
$trend_views = get_author_trend($conn, $id, 'posts', 'created_at', $range, true);
$views_labels = $trend_views['labels'];
$views_data   = $trend_views['data'];

/* ── Recent 5 Comments on Author Posts ───────────────────── */
$recent_comments_res = mysqli_query($conn,
    "SELECT c.comment, c.created_at, p.title AS post_title,
            u.name AS actor_name, u.profile_image AS actor_img
     FROM comments c
     INNER JOIN posts p  ON c.post_id = p.id
     INNER JOIN users u  ON c.user_id = u.id
     WHERE p.author_id = $id" . str_replace('created_at', 'c.created_at', $date_filter) . "
     ORDER BY c.created_at DESC
     LIMIT 5"
);
$recent_comments = $recent_comments_res ? mysqli_fetch_all($recent_comments_res, MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="<?php echo defined('BASE_URL') ? BASE_URL : '/BlogFusion/'; ?>upload/site_image/logo2.png" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Analytics | Blog Fusion</title>
    <meta name="description" content="View your post performance analytics including views, reactions, comments, and shares." />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-secondary-container": "#fffbff", "surface-container-high": "#ede5f4",
                        "on-tertiary-container": "#ffdde7", "on-primary": "#ffffff",
                        "surface-container-low": "#f9f1ff", "surface-container": "#f3ebfa",
                        "primary-fixed-dim": "#d2bbff", "primary": "#630ed4", "tertiary": "#9b005c",
                        "on-secondary-fixed": "#0f0069", "on-tertiary-fixed-variant": "#8c0053",
                        "on-primary-container": "#ede0ff", "outline-variant": "#ccc3d8",
                        "inverse-primary": "#d2bbff", "on-primary-fixed": "#25005a",
                        "background": "#fef7ff", "primary-fixed": "#eaddff",
                        "inverse-surface": "#332f39", "tertiary-fixed-dim": "#ffb0cd",
                        "primary-container": "#7c3aed", "surface-container-lowest": "#ffffff",
                        "surface-container-highest": "#e8dfee", "on-primary-fixed-variant": "#5a00c6",
                        "inverse-on-surface": "#f6eefc", "secondary-fixed": "#e2dfff",
                        "on-tertiary-fixed": "#3e0022", "surface-bright": "#fef7ff",
                        "secondary": "#4b41e1", "surface-tint": "#732ee4",
                        "tertiary-container": "#bf2076", "on-surface-variant": "#4a4455",
                        "outline": "#7b7487", "surface-dim": "#dfd7e6",
                        "secondary-fixed-dim": "#c3c0ff", "on-surface": "#1d1a24",
                        "on-secondary-fixed-variant": "#3323cc", "on-tertiary": "#ffffff",
                        "tertiary-fixed": "#ffd9e4", "surface": "#fef7ff",
                        "on-error-container": "#93000a", "surface-variant": "#e8dfee",
                        "error": "#ba1a1a", "on-background": "#1d1a24",
                        "secondary-container": "#645efb", "on-secondary": "#ffffff",
                        "on-error": "#ffffff", "error-container": "#ffdad6"
                    },
                    borderRadius: { DEFAULT: "0.25rem", lg: "0.75rem", xl: "1rem", full: "9999px" },
                    fontFamily: { headline: ["Public Sans", "sans-serif"], body: ["Public Sans", "sans-serif"], label: ["Public Sans", "sans-serif"] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Public Sans', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(99,14,212,0.10); }
        @keyframes countUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-count { animation: countUp 0.5s ease-out both; }
        .chart-container { position: relative; }
        canvas { max-width: 100%; }
    </style>
</head>

<body class="bg-background text-on-background">

    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-black/40 z-40 md:hidden hidden transition-opacity" id="sidebar-overlay"
        onclick="toggleSidebar()"></div>

    <?= author_slidebar('analytics') ?>
    <?= author_navbar() ?>

    <main class="md:ml-64 pt-20 min-h-screen px-4 sm:px-6 pb-16 transition-all duration-300">

        <!-- Page Header -->
        <header class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
            <div>
                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Author Panel</p>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-on-surface">Analytics</h1>
                <p class="text-on-surface-variant text-sm mt-1">A complete view of your content performance.</p>
            </div>
            <form method="GET" class="flex items-center gap-2 text-xs text-on-surface-variant bg-surface-container px-3 py-1.5 rounded-xl border border-outline-variant/20">
                <span class="material-symbols-outlined text-base">calendar_today</span>
                <select name="range" onchange="this.form.submit()" class="bg-transparent border-none text-xs text-on-surface-variant focus:ring-0 cursor-pointer p-0 pr-6 font-medium">
                    <option value="24h" class="dark:bg-slate-900 bg-white" <?= $range === '24h' ? 'selected' : '' ?>>Last 24 Hours</option>
                    <option value="7d" class="dark:bg-slate-900 bg-white" <?= $range === '7d' ? 'selected' : '' ?>>Last 7 Days</option>
                    <option value="30d" class="dark:bg-slate-900 bg-white" <?= $range === '30d' ? 'selected' : '' ?>>Last 30 Days</option>
                    <option value="all" class="dark:bg-slate-900 bg-white" <?= $range === 'all' ? 'selected' : '' ?>>All Time</option>
                </select>
            </form>
        </header>

        <!-- ── Stat Cards Row ──────────────────────────────────────────── -->
        <section class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8" id="stat-cards">

            <!-- Total Views -->
            <div class="stat-card col-span-1 bg-gradient-to-br from-violet-600 to-purple-700 text-white p-5 rounded-2xl shadow-lg relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">visibility</span>
                    </div>
                </div>
                <p class="text-3xl font-black animate-count" data-target="<?= $total_views ?>" id="sc-views">0</p>
                <p class="text-white/70 text-xs font-semibold uppercase tracking-widest mt-1">Total Views</p>
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <span class="material-symbols-outlined text-8xl" style="font-variation-settings:'FILL' 1;">visibility</span>
                </div>
            </div>

            <!-- Total Reactions -->
            <div class="stat-card col-span-1 bg-gradient-to-br from-pink-500 to-rose-600 text-white p-5 rounded-2xl shadow-lg relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">favorite</span>
                    </div>
                </div>
                <p class="text-3xl font-black animate-count" data-target="<?= $total_reactions ?>" id="sc-reactions">0</p>
                <p class="text-white/70 text-xs font-semibold uppercase tracking-widest mt-1">Total Reactions</p>
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <span class="material-symbols-outlined text-8xl" style="font-variation-settings:'FILL' 1;">favorite</span>
                </div>
            </div>

            <!-- Total Comments -->
            <div class="stat-card col-span-1 bg-gradient-to-br from-blue-500 to-indigo-600 text-white p-5 rounded-2xl shadow-lg relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">forum</span>
                    </div>
                </div>
                <p class="text-3xl font-black animate-count" data-target="<?= $total_comments ?>" id="sc-comments">0</p>
                <p class="text-white/70 text-xs font-semibold uppercase tracking-widest mt-1">Total Comments</p>
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <span class="material-symbols-outlined text-8xl" style="font-variation-settings:'FILL' 1;">forum</span>
                </div>
            </div>

            <!-- Total Shares -->
            <div class="stat-card col-span-1 bg-gradient-to-br from-emerald-500 to-teal-600 text-white p-5 rounded-2xl shadow-lg relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">share</span>
                    </div>
                </div>
                <p class="text-3xl font-black animate-count" data-target="<?= $total_shares ?>" id="sc-shares">0</p>
                <p class="text-white/70 text-xs font-semibold uppercase tracking-widest mt-1">Total Shares</p>
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <span class="material-symbols-outlined text-8xl" style="font-variation-settings:'FILL' 1;">share</span>
                </div>
            </div>

        </section>

        <!-- ── Secondary Metrics Row ───────────────────────────────────── -->
        <section class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-10">
            <div class="stat-card bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-4 flex flex-col gap-1">
                <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Published</span>
                <span class="text-2xl font-extrabold text-primary"><?= $published_count ?></span>
                <span class="text-xs text-on-surface-variant">posts live</span>
            </div>
            <div class="stat-card bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-4 flex flex-col gap-1">
                <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Drafts</span>
                <span class="text-2xl font-extrabold text-amber-500"><?= $draft_count ?></span>
                <span class="text-xs text-on-surface-variant">in progress</span>
            </div>
            <div class="stat-card bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-4 flex flex-col gap-1">
                <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Avg Views</span>
                <span class="text-2xl font-extrabold text-indigo-600"><?= $avg_views ?></span>
                <span class="text-xs text-on-surface-variant">per post</span>
            </div>
            <div class="stat-card bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-4 flex flex-col gap-1">
                <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Avg Reactions</span>
                <span class="text-2xl font-extrabold text-rose-500"><?= $avg_reactions ?></span>
                <span class="text-xs text-on-surface-variant">per post</span>
            </div>
        </section>

        <!-- ── Charts Row 1: Views Trend + Reaction Breakdown ─────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">

            <!-- Views Trend Area Chart -->
            <div class="lg:col-span-8 bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-5 sm:p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Performance</p>
                        <h2 class="text-lg font-bold text-on-surface">
                            <?php
                            if ($range === '24h') echo 'Views by Publish Hour';
                            elseif ($range === '7d' || $range === '30d') echo 'Views by Publish Day';
                            else echo 'Views by Publish Month';
                            ?>
                        </h2>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-primary-fixed/30 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-lg">trending_up</span>
                    </div>
                </div>
                <div class="chart-container" style="height:260px;">
                    <canvas id="viewsChart"></canvas>
                </div>
                <?php if (empty($views_data)): ?>
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <span class="material-symbols-outlined text-3xl text-on-surface-variant mb-2">bar_chart</span>
                        <p class="text-sm text-on-surface-variant">
                            <?php
                            if ($range === '24h') echo 'No published posts found in the last 24 hours.';
                            elseif ($range === '7d') echo 'No published posts found in the last 7 days.';
                            elseif ($range === '30d') echo 'No published posts found in the last 30 days.';
                            else echo 'No published posts yet — publish a post to see your views trend.';
                            ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Reaction Breakdown Doughnut -->
            <div class="lg:col-span-4 bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-5 sm:p-6 shadow-sm flex flex-col">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Engagement</p>
                        <h2 class="text-lg font-bold text-on-surface">Reactions</h2>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-tertiary-fixed/30 flex items-center justify-center text-tertiary">
                        <span class="material-symbols-outlined text-lg">sentiment_satisfied</span>
                    </div>
                </div>
                <div class="chart-container flex-1 flex items-center justify-center" style="min-height:200px; max-height:220px;">
                    <canvas id="reactionChart"></canvas>
                </div>
                <!-- Legend -->
                <div class="grid grid-cols-3 gap-1 mt-4">
                    <?php
                    $emoji_map = [
                        'like'  => ['👍', 'Like',  '#818cf8'],
                        'love'  => ['❤️', 'Love',  '#f43f5e'],
                        'wow'   => ['😮', 'Wow',   '#f59e0b'],
                        'haha'  => ['😂', 'Haha',  '#10b981'],
                        'sad'   => ['😢', 'Sad',   '#6366f1'],
                        'angry' => ['😡', 'Angry', '#ef4444'],
                    ];
                    foreach ($emoji_map as $key => [$em, $label, $col]): ?>
                        <div class="flex items-center gap-1 text-[10px] font-semibold text-on-surface-variant">
                            <span class="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:<?= $col ?>"></span>
                            <?= $em ?> <?= $label ?>: <?= $reaction_types[$key] ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ── Charts Row 2: Monthly Publishing + Category Distribution ── -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-10">

            <!-- Monthly Publishing Bar Chart -->
            <div class="lg:col-span-7 bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-5 sm:p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Publishing</p>
                        <h2 class="text-lg font-bold text-on-surface">
                            <?php
                            if ($range === '24h') echo 'Posts Per Hour';
                            elseif ($range === '7d' || $range === '30d') echo 'Posts Per Day';
                            else echo 'Posts Per Month';
                            ?>
                        </h2>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-secondary-fixed/30 flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-lg">calendar_month</span>
                    </div>
                </div>
                <div class="chart-container" style="height:220px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
                <?php if (empty($month_counts)): ?>
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <p class="text-sm text-on-surface-variant">
                            <?php
                            if ($range === '24h') echo 'No posts found in the last 24 hours.';
                            elseif ($range === '7d') echo 'No posts found in the last 7 days.';
                            elseif ($range === '30d') echo 'No posts found in the last 30 days.';
                            else echo 'No posts found in the last 6 months.';
                            ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Category Distribution Doughnut -->
            <div class="lg:col-span-5 bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-5 sm:p-6 shadow-sm flex flex-col">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Content Mix</p>
                        <h2 class="text-lg font-bold text-on-surface">Category Distribution</h2>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-primary-fixed/30 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-lg">category</span>
                    </div>
                </div>
                <?php if (!empty($cat_counts)): ?>
                    <div class="chart-container flex-1 flex items-center justify-center" style="min-height:180px; max-height:200px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <?php
                        $palette = ['#7c3aed','#4b41e1','#9b005c','#10b981','#f59e0b','#ef4444','#06b6d4','#8b5cf6'];
                        foreach ($cat_labels as $i => $label):
                            $col = $palette[$i % count($palette)];
                        ?>
                            <div class="flex items-center gap-1 text-[10px] font-semibold text-on-surface-variant">
                                <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:<?= $col ?>"></span>
                                <?= $label ?>: <?= $cat_counts[$i] ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-8 text-center flex-1">
                        <span class="material-symbols-outlined text-3xl text-on-surface-variant mb-2">category</span>
                        <p class="text-sm text-on-surface-variant">No category data yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Top Posts Table + Recent Comments ──────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Top Posts Table -->
            <section class="lg:col-span-8 bg-surface-container-lowest border border-outline-variant/10 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-5 border-b border-outline-variant/10 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Leaderboard</p>
                        <h2 class="text-lg font-bold text-on-surface">Top Posts by Views</h2>
                    </div>
                    <a href="my-post.php" class="text-xs font-bold text-primary hover:underline">View All</a>
                </div>

                <?php if (empty($top_posts)): ?>
                    <div class="flex flex-col items-center justify-center py-16 text-center px-5">
                        <span class="material-symbols-outlined text-4xl text-on-surface-variant mb-3">article</span>
                        <p class="text-sm font-semibold text-on-surface">No published posts yet</p>
                        <p class="text-xs text-on-surface-variant mt-1 mb-5">Publish your first post to see analytics here.</p>
                        <a href="edit-post.php" class="px-5 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:opacity-90 transition-opacity">
                            Create Post
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest bg-surface-container-low">
                                    <th class="py-3 px-5 text-left">#</th>
                                    <th class="py-3 px-3 text-left">Post</th>
                                    <th class="py-3 px-3 text-right">
                                        <span class="material-symbols-outlined text-sm align-middle">visibility</span>
                                    </th>
                                    <th class="py-3 px-3 text-right">
                                        <span class="material-symbols-outlined text-sm align-middle">favorite</span>
                                    </th>
                                    <th class="py-3 px-3 text-right">
                                        <span class="material-symbols-outlined text-sm align-middle">forum</span>
                                    </th>
                                    <th class="py-3 px-3 text-right">
                                        <span class="material-symbols-outlined text-sm align-middle">share</span>
                                    </th>
                                    <th class="py-3 px-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_posts as $rank => $post):
                                    $rank_badges = ['🥇', '🥈', '🥉', '#4', '#5'];
                                    $badge = $rank_badges[$rank] ?? ('#' . ($rank + 1));
                                    $post_title = htmlspecialchars($post['title']);
                                    $post_img   = BASE_URL . $post['image'];
                                ?>
                                    <tr class="border-t border-outline-variant/10 hover:bg-surface-container-low transition-colors group">
                                        <td class="py-4 px-5">
                                            <span class="text-base font-bold"><?= $badge ?></span>
                                        </td>
                                        <td class="py-4 px-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-surface-container">
                                                    <?php if ($post['image']): ?>
                                                        <img src="<?= $post_img ?>" alt="<?= $post_title ?>" class="w-full h-full object-cover" />
                                                    <?php else: ?>
                                                        <div class="w-full h-full flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-on-surface-variant">article</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-on-surface line-clamp-1 text-sm"><?= $post_title ?></p>
                                                    <p class="text-xs text-on-surface-variant"><?= time_ago_a($post['created_at']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-3 text-right font-bold text-on-surface"><?= fmt_num($post['views']) ?></td>
                                        <td class="py-4 px-3 text-right text-rose-500 font-semibold"><?= fmt_num($post['reaction_count']) ?></td>
                                        <td class="py-4 px-3 text-right text-indigo-500 font-semibold"><?= fmt_num($post['comment_count']) ?></td>
                                        <td class="py-4 px-3 text-right text-emerald-600 font-semibold"><?= fmt_num($post['share_count']) ?></td>
                                        <td class="py-4 px-5 text-right">
                                            <a href="edit-post.php?id=<?= $post['id'] ?>"
                                               class="text-xs font-bold text-primary hover:underline opacity-0 group-hover:opacity-100 transition-opacity">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Recent Comments Feed -->
            <section class="lg:col-span-4 bg-surface-container-lowest border border-outline-variant/10 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-5 border-b border-outline-variant/10 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Activity</p>
                        <h2 class="text-lg font-bold text-on-surface">Recent Comments</h2>
                    </div>
                    <a href="comments.php" class="text-xs font-bold text-primary hover:underline">View All</a>
                </div>

                <?php if (empty($recent_comments)): ?>
                    <div class="flex flex-col items-center justify-center py-14 text-center px-5">
                        <span class="material-symbols-outlined text-4xl text-on-surface-variant mb-3">forum</span>
                        <p class="text-sm font-semibold text-on-surface">No comments yet</p>
                        <p class="text-xs text-on-surface-variant mt-1">Comments from readers will appear here.</p>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col divide-y divide-outline-variant/10">
                        <?php foreach ($recent_comments as $c): ?>
                            <div class="flex gap-3 px-5 py-4">
                                <img src="<?= BASE_URL ?><?= htmlspecialchars($c['actor_img']) ?>"
                                     alt="<?= htmlspecialchars($c['actor_name']) ?>"
                                     class="w-9 h-9 rounded-xl object-cover flex-shrink-0 bg-surface-container" />
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-xs font-bold text-on-surface"><?= htmlspecialchars($c['actor_name']) ?></span>
                                        <span class="text-[10px] text-on-surface-variant"><?= time_ago_a($c['created_at']) ?></span>
                                    </div>
                                    <p class="text-xs text-on-surface-variant line-clamp-2">"<?= htmlspecialchars($c['comment']) ?>"</p>
                                    <p class="text-[10px] text-outline mt-1 truncate">on <em class="font-semibold text-on-surface-variant"><?= htmlspecialchars($c['post_title']) ?></em></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

        </div><!-- end bottom row -->
    </main>

    <script>
        /* ── Sidebar toggle ──────────────────────────────────── */
        function toggleSidebar() {
            const sidebar = document.getElementById('main-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isClosed = sidebar.classList.contains('sidebar-closed');
            if (isClosed) {
                sidebar.classList.remove('sidebar-closed');
                sidebar.classList.add('sidebar-open');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('sidebar-closed');
                sidebar.classList.remove('sidebar-open');
                overlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        /* ── Animated stat counter ──────────────────────────── */
        function animateCount(el) {
            const target = parseInt(el.dataset.target, 10) || 0;
            const duration = 900;
            const start = performance.now();
            function step(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const val = Math.round(eased * target);
                // Format with K/M
                if (target >= 1000000)       el.textContent = (val / 1000000).toFixed(1) + 'M';
                else if (target >= 1000)     el.textContent = (val / 1000).toFixed(1) + 'K';
                else                         el.textContent = val.toLocaleString();
                if (progress < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        }
        document.querySelectorAll('[data-target]').forEach(el => {
            setTimeout(() => animateCount(el), 200);
        });

        /* ── Chart.js shared defaults ───────────────────────── */
        Chart.defaults.font.family = "'Public Sans', sans-serif";
        Chart.defaults.color = '#4a4455';

        /* ── 1. Views Area Chart ────────────────────────────── */
        const viewsLabels = <?= json_encode($views_labels) ?>;
        const viewsData   = <?= json_encode($views_data) ?>;
        if (viewsLabels.length > 0) {
            const viewsCtx = document.getElementById('viewsChart').getContext('2d');
            const viewsGrad = viewsCtx.createLinearGradient(0, 0, 0, 260);
            viewsGrad.addColorStop(0, 'rgba(99,14,212,0.25)');
            viewsGrad.addColorStop(1, 'rgba(99,14,212,0.00)');
            new Chart(viewsCtx, {
                type: 'line',
                data: {
                    labels: viewsLabels,
                    datasets: [{
                        label: 'Views',
                        data: viewsData,
                        fill: true,
                        backgroundColor: viewsGrad,
                        borderColor: '#630ed4',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#630ed4',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                        y: { grid: { color: '#e8dfee55' }, beginAtZero: true, ticks: { font: { size: 11 }, precision: 0 } }
                    }
                }
            });
        }

        /* ── 2. Reaction Doughnut ───────────────────────────── */
        const reactionData   = <?= json_encode(array_values($reaction_types)) ?>;
        const reactionLabels = ['Like 👍', 'Love ❤️', 'Wow 😮', 'Haha 😂', 'Sad 😢', 'Angry 😡'];
        const reactionColors = ['#818cf8','#f43f5e','#f59e0b','#10b981','#6366f1','#ef4444'];
        const reactionCtx = document.getElementById('reactionChart').getContext('2d');
        new Chart(reactionCtx, {
            type: 'doughnut',
            data: {
                labels: reactionLabels,
                datasets: [{ data: reactionData, backgroundColor: reactionColors, borderWidth: 2, borderColor: '#fff' }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` } }
                }
            }
        });

        /* ── 3. Monthly Publishing Bar Chart ─────────────────── */
        const monthLabels = <?= json_encode($month_labels) ?>;
        const monthCounts = <?= json_encode($month_counts) ?>;
        if (monthLabels.length > 0) {
            const monthCtx = document.getElementById('monthlyChart').getContext('2d');
            const barGrad = monthCtx.createLinearGradient(0, 0, 0, 220);
            barGrad.addColorStop(0, '#7c3aed');
            barGrad.addColorStop(1, '#4b41e1');
            new Chart(monthCtx, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Posts Published',
                        data: monthCounts,
                        backgroundColor: barGrad,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                        y: { grid: { color: '#e8dfee55' }, beginAtZero: true, ticks: { precision: 0, font: { size: 11 } } }
                    }
                }
            });
        }

        /* ── 4. Category Distribution Doughnut ───────────────── */
        const catLabels = <?= json_encode($cat_labels) ?>;
        const catCounts = <?= json_encode($cat_counts) ?>;
        const catColors = ['#7c3aed','#4b41e1','#9b005c','#10b981','#f59e0b','#ef4444','#06b6d4','#8b5cf6'];
        if (catLabels.length > 0) {
            const catCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(catCtx, {
                type: 'doughnut',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catCounts,
                        backgroundColor: catColors.slice(0, catLabels.length),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} posts` } }
                    }
                }
            });
        }
    </script>
</body>
</html>

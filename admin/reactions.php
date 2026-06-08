<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAdmin();
include BASE_PATH . 'include/db.php';
include BASE_PATH . 'include/admin_nav_sidebar.php';

/* ── Helper ─────────────────────────────────────────────── */
function fmt($n): string {
    if ($n >= 1_000_000) return round($n / 1_000_000, 1) . 'M';
    if ($n >= 1_000)     return round($n / 1_000, 1) . 'K';
    return (string)(int)$n;
}
function time_ago_r(string $dt): string {
    $d = time() - strtotime($dt);
    if ($d < 60)    return $d . 's ago';
    if ($d < 3600)  return floor($d / 60) . 'm ago';
    if ($d < 86400) return floor($d / 3600) . 'h ago';
    return floor($d / 86400) . 'd ago';
}

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

$reactions_date_filter = $date_filter;
$reactions_date_filter_r = str_replace('created_at', 'r.created_at', $date_filter);
$posts_date_filter_p = str_replace('created_at', 'p.created_at', $date_filter);

/* ── Stats ───────────────────────────────────────────────── */
$total_reactions = (int)(mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS t FROM reactions WHERE 1=1" . $reactions_date_filter))['t'] ?? 0);

$avg_per_post = 0;
$post_count_r = (int)(mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(DISTINCT post_id) AS t FROM reactions WHERE 1=1" . $reactions_date_filter))['t'] ?? 0);
if ($post_count_r > 0) $avg_per_post = round($total_reactions / $post_count_r, 1);

$emoji_mapping = [
    '👍' => 'like',
    '❤️' => 'love',
    '😮' => 'wow',
    '😂' => 'haha',
    '😢' => 'sad',
    '😡' => 'angry'
];

$top_emoji_row = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT emoji, COUNT(*) AS cnt FROM reactions WHERE 1=1" . $reactions_date_filter . " GROUP BY emoji ORDER BY cnt DESC LIMIT 1"));
$top_emoji      = $top_emoji_row['emoji']  ?? '-';
$top_emoji      = $emoji_mapping[trim($top_emoji)] ?? $top_emoji;
$top_emoji_cnt  = (int)($top_emoji_row['cnt'] ?? 0);

$most_reacted_row = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT p.title, COUNT(r.id) AS cnt FROM reactions r
     INNER JOIN posts p ON r.post_id = p.id
     WHERE 1=1" . $reactions_date_filter_r . "
     GROUP BY r.post_id ORDER BY cnt DESC LIMIT 1"));
$most_reacted_title = $most_reacted_row['title'] ?? 'N/A';
$most_reacted_cnt   = (int)($most_reacted_row['cnt'] ?? 0);

/* ── Reaction Type Breakdown ─────────────────────────────── */
$emoji_res = mysqli_query($conn, "SELECT emoji, COUNT(*) AS cnt FROM reactions WHERE 1=1" . $reactions_date_filter . " GROUP BY emoji");
$emoji_types = ['like'=>0,'love'=>0,'wow'=>0,'haha'=>0,'sad'=>0,'angry'=>0];
if ($emoji_res) {
    while ($er = mysqli_fetch_assoc($emoji_res)) {
        $emoji = trim($er['emoji'] ?? '');
        $k = $emoji_mapping[$emoji] ?? strtolower($emoji);
        if (isset($emoji_types[$k])) $emoji_types[$k] = (int)$er['cnt'];
    }
}

/* ── Top 10 Most Reacted Posts ───────────────────────────── */
$top_posts_res = mysqli_query($conn,
    "SELECT p.id, p.title, p.slug, p.image,
            u.name AS author_name,
            COUNT(r.id) AS total_reactions,
            SUM(r.emoji='👍')  AS like_cnt,
            SUM(r.emoji='❤️')  AS love_cnt,
            SUM(r.emoji='😮')   AS wow_cnt,
            SUM(r.emoji='😂')  AS haha_cnt,
            SUM(r.emoji='😢')   AS sad_cnt,
            SUM(r.emoji='😡') AS angry_cnt
     FROM reactions r
     INNER JOIN posts p ON r.post_id = p.id
     LEFT JOIN users u ON p.author_id = u.id
     WHERE 1=1" . $reactions_date_filter_r . "
     GROUP BY r.post_id, p.id, p.title, p.slug, p.image, u.name
     ORDER BY total_reactions DESC
     LIMIT 10");
$top_posts = $top_posts_res ? mysqli_fetch_all($top_posts_res, MYSQLI_ASSOC) : [];

/* ── Per-Author Reaction Stats ───────────────────────────── */
$author_stats_res = mysqli_query($conn,
    "SELECT u.name, u.profile_image,
            COUNT(r.id) AS total_reactions,
            COUNT(DISTINCT p.id) AS post_count
     FROM reactions r
     INNER JOIN posts p ON r.post_id = p.id
     INNER JOIN users u ON p.author_id = u.id
     WHERE 1=1" . $reactions_date_filter_r . "
     GROUP BY p.author_id, u.name, u.profile_image
     ORDER BY total_reactions DESC
     LIMIT 5");
$author_stats = $author_stats_res ? mysqli_fetch_all($author_stats_res, MYSQLI_ASSOC) : [];

/* ── Recent 15 Reactions ─────────────────────────────────── */
$recent_res = mysqli_query($conn,
    "SELECT r.emoji, r.created_at,
            u.name AS user_name, u.profile_image AS user_img,
            p.title AS post_title
     FROM reactions r
     INNER JOIN users u ON r.user_id = u.id
     INNER JOIN posts p ON r.post_id = p.id
     WHERE 1=1" . $reactions_date_filter_r . "
     ORDER BY r.created_at DESC
     LIMIT 15");
$recent_reactions = $recent_res ? mysqli_fetch_all($recent_res, MYSQLI_ASSOC) : [];

/* ── Reaction Trend based on Range ───────────────────────── */
$trend_labels = [];
$trend_data   = [];
if ($range === '24h') {
    $trend = [];
    for ($i = 23; $i >= 0; $i--) {
        $time = strtotime("-$i hours");
        $ym = date('Y-m-d H:00', $time);
        $label = date('H:00', $time);
        $trend[$ym] = ['label' => $label, 'total' => 0];
    }
    $query = "SELECT DATE_FORMAT(created_at, '%Y-%m-%d %H:00') AS ym, COUNT(*) AS total 
              FROM reactions 
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) 
              GROUP BY ym";
    $res = mysqli_query($conn, $query);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $ym = $r['ym'];
            if (isset($trend[$ym])) {
                $trend[$ym]['total'] = (int)$r['total'];
            }
        }
    }
    foreach ($trend as $val) {
        $trend_labels[] = $val['label'];
        $trend_data[] = $val['total'];
    }
} elseif ($range === '7d') {
    $trend = [];
    for ($i = 6; $i >= 0; $i--) {
        $time = strtotime("-$i days");
        $ym = date('Y-m-d', $time);
        $label = date('D M j', $time);
        $trend[$ym] = ['label' => $label, 'total' => 0];
    }
    $query = "SELECT DATE_FORMAT(created_at, '%Y-%m-%d') AS ym, COUNT(*) AS total 
              FROM reactions 
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
              GROUP BY ym";
    $res = mysqli_query($conn, $query);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $ym = $r['ym'];
            if (isset($trend[$ym])) {
                $trend[$ym]['total'] = (int)$r['total'];
            }
        }
    }
    foreach ($trend as $val) {
        $trend_labels[] = $val['label'];
        $trend_data[] = $val['total'];
    }
} elseif ($range === '30d') {
    $trend = [];
    for ($i = 29; $i >= 0; $i--) {
        $time = strtotime("-$i days");
        $ym = date('Y-m-d', $time);
        $label = date('M j', $time);
        $trend[$ym] = ['label' => $label, 'total' => 0];
    }
    $query = "SELECT DATE_FORMAT(created_at, '%Y-%m-%d') AS ym, COUNT(*) AS total 
              FROM reactions 
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
              GROUP BY ym";
    $res = mysqli_query($conn, $query);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $ym = $r['ym'];
            if (isset($trend[$ym])) {
                $trend[$ym]['total'] = (int)$r['total'];
            }
        }
    }
    foreach ($trend as $val) {
        $trend_labels[] = $val['label'];
        $trend_data[] = $val['total'];
    }
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
    $query = "SELECT DATE_FORMAT(created_at,'%Y-%m') AS ym, COUNT(*) AS total
              FROM reactions
              WHERE created_at >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 5 MONTH), '%Y-%m-01')
              GROUP BY ym
              ORDER BY ym ASC";
    $res = mysqli_query($conn, $query);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $ym = $r['ym'];
            if (isset($trend[$ym])) {
                $trend[$ym]['total'] = (int)$r['total'];
            }
        }
    }
    foreach ($trend as $val) {
        $trend_labels[] = $val['label'];
        $trend_data[] = $val['total'];
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo defined('BASE_URL') ? BASE_URL : '/BlogFusion/'; ?>upload/site_image/logo2.png" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Reaction Management — Blog Fusion Admin</title>
    <meta name="description" content="Monitor reactions, track engagement, and view the most reacted posts on Blog Fusion." />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#7C3AED",
                        "primary-hover": "#6D28D9",
                        "background-light": "#f8f7ff",
                        "background-dark": "#0f172a",
                    },
                    fontFamily: { "display": ["Public Sans", "sans-serif"] },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
    <style>
        .emoji-bar { transition: width 0.6s cubic-bezier(.4,0,.2,1); }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
<div class="flex h-screen overflow-hidden">
    <?= slidebar('reactions') ?>
    <div id="overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <?= ad_navbar() ?>
        <div class="flex-1 overflow-y-auto p-6 lg:p-8">

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-black tracking-tight">Reaction Management</h1>
                    <p class="text-slate-500 mt-1">Monitor engagement, track reactions, and analyse reader sentiment.</p>
                </div>
                <form method="GET" class="sm:self-center self-start flex items-center gap-2 text-xs text-slate-500 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-1.5 rounded-xl">
                    <span class="material-symbols-outlined text-base text-slate-400">calendar_today</span>
                    <select name="range" onchange="this.form.submit()" class="bg-transparent border-none text-xs text-slate-600 dark:text-slate-300 focus:ring-0 cursor-pointer p-0 pr-6 font-medium">
                        <option value="24h" class="dark:bg-slate-900 bg-white" <?= $range === '24h' ? 'selected' : '' ?>>Last 24 Hours</option>
                        <option value="7d" class="dark:bg-slate-900 bg-white" <?= $range === '7d' ? 'selected' : '' ?>>Last 7 Days</option>
                        <option value="30d" class="dark:bg-slate-900 bg-white" <?= $range === '30d' ? 'selected' : '' ?>>Last 30 Days</option>
                        <option value="all" class="dark:bg-slate-900 bg-white" <?= $range === 'all' ? 'selected' : '' ?>>All Time</option>
                    </select>
                </form>
            </div>

            <!-- ── Stat Cards ──────────────────────────────────────── -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-gradient-to-br from-violet-600 to-purple-700 text-white p-5 rounded-2xl shadow-lg relative overflow-hidden">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined">favorite</span>
                    </div>
                    <p class="text-3xl font-black"><?= fmt($total_reactions) ?></p>
                    <p class="text-white/70 text-xs font-bold uppercase tracking-widest mt-1">Total Reactions</p>
                    <div class="absolute -right-4 -bottom-4 opacity-10">
                        <span class="material-symbols-outlined text-8xl" style="font-variation-settings:'FILL' 1;">favorite</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
                    <div class="w-10 h-10 bg-pink-100 text-pink-500 rounded-xl flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined">article</span>
                    </div>
                    <p class="text-2xl font-black"><?= fmt($most_reacted_cnt) ?></p>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Top Post Reactions</p>
                    <p class="text-[11px] text-slate-400 mt-0.5 truncate"><?= htmlspecialchars($most_reacted_title) ?></p>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
                    <div class="w-10 h-10 bg-amber-100 text-amber-500 rounded-xl flex items-center justify-center mb-3 text-2xl">
                        <?php
                        $emoji_icons = ['like'=>'👍','love'=>'❤️','wow'=>'😮','haha'=>'😂','sad'=>'😢','angry'=>'😡'];
                        echo $emoji_icons[$top_emoji] ?? '😍';
                        ?>
                    </div>
                    <p class="text-2xl font-black"><?= fmt($top_emoji_cnt) ?></p>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Top Emoji</p>
                    <p class="text-[11px] text-slate-400 mt-0.5"><?= ucfirst($top_emoji) ?> reaction</p>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
                    <div class="w-10 h-10 bg-indigo-100 text-indigo-500 rounded-xl flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined">equalizer</span>
                    </div>
                    <p class="text-2xl font-black"><?= $avg_per_post ?></p>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Avg Per Post</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">across <?= $post_count_r ?> posts</p>
                </div>
            </div>

            <!-- ── Charts Row ──────────────────────────────────────── -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">

                <!-- Reaction Trend Line Chart -->
                <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Trend</p>
                            <h2 class="text-lg font-bold">
                                <?php
                                if ($range === '24h') echo 'Hourly Reactions';
                                elseif ($range === '7d' || $range === '30d') echo 'Daily Reactions';
                                else echo 'Monthly Reactions';
                                ?>
                            </h2>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-purple-100 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-lg">show_chart</span>
                        </div>
                    </div>
                    <div style="height:240px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                    <?php if (empty($trend_data)): ?>
                        <div class="flex flex-col items-center py-8 text-center">
                            <p class="text-sm text-slate-400">
                                <?php
                                if ($range === '24h') echo 'No reaction data found in the last 24 hours.';
                                elseif ($range === '7d') echo 'No reaction data found in the last 7 days.';
                                elseif ($range === '30d') echo 'No reaction data found in the last 30 days.';
                                else echo 'No reaction data found in the last 6 months.';
                                ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Emoji Breakdown Doughnut -->
                <div class="lg:col-span-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 flex flex-col">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Breakdown</p>
                            <h2 class="text-lg font-bold">By Emoji</h2>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-pink-100 flex items-center justify-center text-pink-500">
                            <span class="material-symbols-outlined text-lg">pie_chart</span>
                        </div>
                    </div>
                    <div class="flex-1 flex items-center justify-center" style="min-height:180px; max-height:200px;">
                        <canvas id="emojiChart"></canvas>
                    </div>
                    <!-- Emoji legend bars -->
                    <div class="mt-5 space-y-2">
                        <?php
                        $emoji_map = [
                            'like'=>['👍','Like','#818cf8'],
                            'love'=>['❤️','Love','#f43f5e'],
                            'wow' =>['😮','Wow', '#f59e0b'],
                            'haha'=>['😂','Haha','#10b981'],
                            'sad' =>['😢','Sad', '#6366f1'],
                            'angry'=>['😡','Angry','#ef4444'],
                        ];
                        $max_emoji = max(array_values($emoji_types)) ?: 1;
                        foreach ($emoji_map as $key => [$em, $label, $col]):
                            $val = $emoji_types[$key];
                            $pct = round($val / $max_emoji * 100);
                        ?>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-6 text-center"><?= $em ?></span>
                            <span class="w-10 text-slate-500 font-semibold"><?= $label ?></span>
                            <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                <div class="emoji-bar h-full rounded-full" style="width:<?= $pct ?>%; background:<?= $col ?>"></div>
                            </div>
                            <span class="w-6 text-right font-bold text-slate-600"><?= $val ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ── Bottom Row: Top Posts + Author Stats + Recent Feed ── -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

                <!-- Top 10 Most Reacted Posts -->
                <section class="xl:col-span-7 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Leaderboard</p>
                            <h2 class="text-lg font-bold">Most Reacted Posts</h2>
                        </div>
                    </div>
                    <?php if (empty($top_posts)): ?>
                        <div class="flex flex-col items-center py-16 text-center px-5">
                            <span class="material-symbols-outlined text-4xl text-slate-300 mb-3">favorite_border</span>
                            <p class="text-sm font-semibold text-slate-400">No reactions yet</p>
                        </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-xs text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/50">
                                    <th class="px-5 py-3 text-left">#</th>
                                    <th class="px-3 py-3 text-left">Post</th>
                                    <th class="px-3 py-3 text-right">Total</th>
                                    <th class="px-3 py-3 text-center">👍</th>
                                    <th class="px-3 py-3 text-center">❤️</th>
                                    <th class="px-3 py-3 text-center">😮</th>
                                    <th class="px-3 py-3 text-center">😂</th>
                                    <th class="px-3 py-3 text-center">😢</th>
                                    <th class="px-3 py-3 text-center">😡</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_posts as $rank => $post): ?>
                                <tr class="border-t border-slate-100 dark:border-slate-800 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-5 py-3">
                                        <span class="font-bold text-sm"><?= ['🥇','🥈','🥉','4','5','6','7','8','9','10'][$rank] ?? ($rank+1) ?></span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <?php if ($post['image']): ?>
                                                <img src="<?= BASE_URL ?><?= htmlspecialchars($post['image']) ?>" class="w-8 h-8 rounded-lg object-cover flex-shrink-0" />
                                            <?php endif; ?>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-sm line-clamp-1"><?= htmlspecialchars($post['title']) ?></p>
                                                <p class="text-[11px] text-slate-400"><?= htmlspecialchars($post['author_name']) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-right font-black text-primary"><?= $post['total_reactions'] ?></td>
                                    <td class="px-3 py-3 text-center text-slate-600"><?= $post['like_cnt'] ?></td>
                                    <td class="px-3 py-3 text-center text-rose-500"><?= $post['love_cnt'] ?></td>
                                    <td class="px-3 py-3 text-center text-amber-500"><?= $post['wow_cnt'] ?></td>
                                    <td class="px-3 py-3 text-center text-green-500"><?= $post['haha_cnt'] ?></td>
                                    <td class="px-3 py-3 text-center text-indigo-400"><?= $post['sad_cnt'] ?></td>
                                    <td class="px-3 py-3 text-center text-red-500"><?= $post['angry_cnt'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </section>

                <!-- Right column: Author Stats + Recent Feed -->
                <div class="xl:col-span-5 flex flex-col gap-6">

                    <!-- Per-Author Reaction Stats -->
                    <section class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Authors</p>
                            <h2 class="text-base font-bold">Top by Reactions</h2>
                        </div>
                        <?php if (empty($author_stats)): ?>
                            <div class="py-10 text-center text-sm text-slate-400">No author data yet.</div>
                        <?php else: ?>
                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            <?php foreach ($author_stats as $i => $au): ?>
                            <div class="flex items-center gap-3 px-5 py-4">
                                <span class="text-sm font-bold text-slate-400 w-5"><?= $i + 1 ?></span>
                                <img src="<?= BASE_URL ?><?= htmlspecialchars($au['profile_image'] ?? 'upload/profile-images/default.png') ?>"
                                     class="w-9 h-9 rounded-xl object-cover flex-shrink-0" />
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold truncate"><?= htmlspecialchars($au['name']) ?></p>
                                    <p class="text-[11px] text-slate-400"><?= $au['post_count'] ?> posts</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black text-primary"><?= fmt($au['total_reactions']) ?></p>
                                    <p class="text-[11px] text-slate-400">reactions</p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </section>

                    <!-- Recent Reactions Feed -->
                    <section class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex-1">
                        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Live Feed</p>
                            <h2 class="text-base font-bold">Recent Reactions</h2>
                        </div>
                        <?php if (empty($recent_reactions)): ?>
                            <div class="py-10 text-center text-sm text-slate-400">No recent reactions.</div>
                        <?php else: ?>
                        <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-72 overflow-y-auto">
                            <?php foreach ($recent_reactions as $rx):
                                $emap = ['like'=>'👍','love'=>'❤️','wow'=>'😮','haha'=>'😂','sad'=>'😢','angry'=>'😡'];
                                $emoji_key = $emoji_mapping[trim($rx['emoji'])] ?? strtolower($rx['emoji']);
                                $icon = $emap[$emoji_key] ?? $rx['emoji'];
                            ?>
                            <div class="flex items-center gap-3 px-5 py-3">
                                <img src="<?= BASE_URL ?><?= htmlspecialchars($rx['user_img'] ?? 'upload/profile-images/default.png') ?>"
                                     class="w-8 h-8 rounded-lg object-cover flex-shrink-0" />
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold truncate"><?= htmlspecialchars($rx['user_name']) ?></p>
                                    <p class="text-[11px] text-slate-400 truncate">on "<?= htmlspecialchars($rx['post_title']) ?>"</p>
                                </div>
                                <div class="text-center flex-shrink-0">
                                    <span class="text-lg"><?= $icon ?></span>
                                    <p class="text-[10px] text-slate-400"><?= time_ago_r($rx['created_at']) ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </section>
                </div>

            </div><!-- end bottom row -->
        </div>
    </main>
</div>

<script src="<?= BASE_URL ?>assets/js/admin.js"></script>
<script>
    Chart.defaults.font.family = "'Public Sans', sans-serif";
    Chart.defaults.color = '#64748b';

    /* ── Trend Line Chart ──────────────────────────────── */
    const trendLabels = <?= json_encode($trend_labels) ?>;
    const trendData   = <?= json_encode($trend_data) ?>;
    if (trendLabels.length > 0) {
        const tCtx  = document.getElementById('trendChart').getContext('2d');
        const tGrad = tCtx.createLinearGradient(0, 0, 0, 240);
        tGrad.addColorStop(0, 'rgba(124,58,237,0.18)');
        tGrad.addColorStop(1, 'rgba(124,58,237,0)');
        new Chart(tCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Reactions',
                    data: trendData,
                    fill: true,
                    backgroundColor: tGrad,
                    borderColor: '#7c3aed',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#7c3aed',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#e2e8f055' }, beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    /* ── Emoji Doughnut ────────────────────────────────── */
    const emojiData   = <?= json_encode(array_values($emoji_types)) ?>;
    const emojiLabels = ['Like 👍','Love ❤️','Wow 😮','Haha 😂','Sad 😢','Angry 😡'];
    const emojiColors = ['#818cf8','#f43f5e','#f59e0b','#10b981','#6366f1','#ef4444'];
    new Chart(document.getElementById('emojiChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: emojiLabels,
            datasets: [{ data: emojiData, backgroundColor: emojiColors, borderWidth: 2, borderColor: '#fff' }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` } }
            }
        }
    });
</script>
</body>
</html>

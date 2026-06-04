<?php
include "../include/session.php";
requireAdmin();
include "../include/db.php";
include "../include/admin_nav_sidebar.php";

/* ══════════════════════════════════════════════════════════════════════════════
   SITE ANALYTICS — Data Layer
   All queries pull real data from the database.
══════════════════════════════════════════════════════════════════════════════ */

// ── 1. Headline KPIs ──────────────────────────────────────────────────────────
$total_views    = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(views) AS t FROM posts"))['t'] ?? 0);
$total_posts    = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM posts"))['t'] ?? 0);
$published      = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM posts WHERE status='published'"))['t'] ?? 0);
$draft_count    = $total_posts - $published;
$total_users    = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM users"))['t'] ?? 0);
$total_comments = (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM comments"))['t'] ?? 0);
$total_reactions= (int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM reactions"))['t'] ?? 0);
$total_categories=(int)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM categories"))['t'] ?? 0);

// avg views per post
$avg_views = $published > 0 ? round($total_views / $published) : 0;

// ── 2. Month-over-month growth ────────────────────────────────────────────────
function mom_growth($conn, $table, $col = 'created_at') {
    $sql = "SELECT
        SUM(CASE WHEN MONTH($col)=MONTH(NOW()) AND YEAR($col)=YEAR(NOW()) THEN 1 ELSE 0 END) AS this_m,
        SUM(CASE WHEN MONTH($col)=MONTH(DATE_SUB(NOW(),INTERVAL 1 MONTH))
                  AND YEAR($col)=YEAR(DATE_SUB(NOW(),INTERVAL 1 MONTH)) THEN 1 ELSE 0 END) AS last_m
        FROM $table";
    $r = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    $t = (int)($r['this_m'] ?? 0);
    $l = (int)($r['last_m'] ?? 0);
    return ['this' => $t, 'last' => $l, 'pct' => round(($t - $l) / max($l, 1) * 100)];
}
$growth_users    = mom_growth($conn, 'users');
$growth_posts    = mom_growth($conn, 'posts');
$growth_comments = mom_growth($conn, 'comments');
$growth_views_row= mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT SUM(CASE WHEN MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW()) THEN views ELSE 0 END) AS this_m,
            SUM(CASE WHEN MONTH(created_at)=MONTH(DATE_SUB(NOW(),INTERVAL 1 MONTH))
                      AND YEAR(created_at)=YEAR(DATE_SUB(NOW(),INTERVAL 1 MONTH)) THEN views ELSE 0 END) AS last_m
     FROM posts"));
$gvt = (int)($growth_views_row['this_m'] ?? 0);
$gvl = (int)($growth_views_row['last_m'] ?? 0);
$growth_views = ['this' => $gvt, 'last' => $gvl, 'pct' => round(($gvt - $gvl) / max($gvl, 1) * 100)];

function badge($pct) {
    if ($pct > 0)  return '<span class="inline-flex items-center gap-0.5 text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded-full"><span class="material-symbols-outlined text-[13px]">arrow_upward</span>+'.$pct.'%</span>';
    if ($pct < 0)  return '<span class="inline-flex items-center gap-0.5 text-xs font-bold text-red-500 bg-red-50 dark:bg-red-900/20 px-2 py-0.5 rounded-full"><span class="material-symbols-outlined text-[13px]">arrow_downward</span>'.$pct.'%</span>';
    return '<span class="text-xs font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">0%</span>';
}

function fill_12m_trend($conn, $query) {
    $trend = [];
    for ($i = 11; $i >= 0; $i--) {
        $time = strtotime("-$i months");
        $ym = date('Y-m', $time);
        $label = date('M Y', $time);
        $trend[$ym] = ['label' => $label, 'total' => 0];
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

// ── 3. Monthly views (last 12 months) ────────────────────────────────────────
$trend_views = fill_12m_trend($conn,
    "SELECT DATE_FORMAT(created_at,'%Y-%m') AS ym, SUM(views) AS total FROM posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY ym");
$v12_labels = $trend_views['labels'];
$v12_data = $trend_views['data'];

// ── 4. Monthly new users (last 12 months) ─────────────────────────────────────
$trend_users = fill_12m_trend($conn,
    "SELECT DATE_FORMAT(created_at,'%Y-%m') AS ym, COUNT(*) AS total FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY ym");
$u12_labels = $trend_users['labels'];
$u12_data = $trend_users['data'];

// ── 5. Monthly new posts (last 12 months) ────────────────────────────────────
$trend_posts = fill_12m_trend($conn,
    "SELECT DATE_FORMAT(created_at,'%Y-%m') AS ym, COUNT(*) AS total FROM posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY ym");
$p12_labels = $trend_posts['labels'];
$p12_data = $trend_posts['data'];

// ── 6. Monthly comments (last 12 months) ─────────────────────────────────────
$trend_comments = fill_12m_trend($conn,
    "SELECT DATE_FORMAT(created_at,'%Y-%m') AS ym, COUNT(*) AS total FROM comments WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY ym");
$c12_labels = $trend_comments['labels'];
$c12_data = $trend_comments['data'];

// ── 7. Top 10 posts by views ──────────────────────────────────────────────────
$top_posts_res = mysqli_query($conn,
    "SELECT p.id, p.title, p.slug, p.views, p.image, p.created_at, p.status,
            u.name AS author_name, u.profile_image,
            cat.name AS category_name,
            (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count,
            (SELECT COUNT(*) FROM reactions r WHERE r.post_id = p.id) AS reaction_count
     FROM posts p
     LEFT JOIN users u ON p.author_id = u.id
     LEFT JOIN categories cat ON p.category_id = cat.id
     WHERE p.status = 'published'
     ORDER BY p.views DESC LIMIT 10");
$top_posts = $top_posts_res ? mysqli_fetch_all($top_posts_res, MYSQLI_ASSOC) : [];

// ── 8. Views by category ─────────────────────────────────────────────────────
$cat_views_res = mysqli_query($conn,
    "SELECT cat.name, SUM(p.views) AS total_views, COUNT(p.id) AS post_count
     FROM posts p
     INNER JOIN categories cat ON p.category_id = cat.id
     WHERE p.status='published'
     GROUP BY cat.id, cat.name ORDER BY total_views DESC LIMIT 8");
$cat_labels = []; $cat_views_data = []; $cat_counts = [];
if ($cat_views_res) while ($r = mysqli_fetch_assoc($cat_views_res)) {
    $cat_labels[] = $r['name']; $cat_views_data[] = (int)$r['total_views']; $cat_counts[] = (int)$r['post_count'];
}

// ── 9. User role breakdown ────────────────────────────────────────────────────
$role_res = mysqli_query($conn, "SELECT role, COUNT(*) AS cnt FROM users GROUP BY role");
$roles = ['admin' => 0, 'author' => 0, 'user' => 0];
if ($role_res) while ($r = mysqli_fetch_assoc($role_res)) $roles[$r['role']] = (int)$r['cnt'];

// ── 10. Top authors by views ──────────────────────────────────────────────────
$authors_res = mysqli_query($conn,
    "SELECT u.name, u.profile_image, u.role,
            SUM(p.views) AS total_views,
            COUNT(p.id) AS post_count,
            (SELECT COUNT(*) FROM comments c INNER JOIN posts pp ON c.post_id=pp.id WHERE pp.author_id=u.id) AS comment_count,
            (SELECT COUNT(*) FROM reactions rx INNER JOIN posts pp ON rx.post_id=pp.id WHERE pp.author_id=u.id) AS reaction_count
     FROM users u
     INNER JOIN posts p ON p.author_id = u.id
     GROUP BY u.id, u.name, u.profile_image, u.role
     ORDER BY total_views DESC LIMIT 10");
$top_authors = $authors_res ? mysqli_fetch_all($authors_res, MYSQLI_ASSOC) : [];
$max_author_views = !empty($top_authors) ? (int)$top_authors[0]['total_views'] : 1;

// ── 11. Engagement rate (reactions + comments) per post ──────────────────────
$engagement_res = mysqli_query($conn,
    "SELECT p.title, p.views,
            (SELECT COUNT(*) FROM reactions r WHERE r.post_id=p.id) +
            (SELECT COUNT(*) FROM comments c WHERE c.post_id=p.id) AS engagements
     FROM posts p WHERE p.status='published' AND p.views > 0
     ORDER BY (engagements / p.views) DESC LIMIT 5");
$engagement_posts = $engagement_res ? mysqli_fetch_all($engagement_res, MYSQLI_ASSOC) : [];

// ── 12. Recent 7 days daily views (using posts created/modified proxy) ────────
// We'll show daily new posts + comments for last 7 days
$daily_posts_res = mysqli_query($conn,
    "SELECT DATE(created_at) AS day, COUNT(*) AS cnt FROM posts
     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY day ASC");
$daily_posts_labels = []; $daily_posts_data = [];
if ($daily_posts_res) while ($r = mysqli_fetch_assoc($daily_posts_res)) {
    $daily_posts_labels[] = date('D M j', strtotime($r['day'])); $daily_posts_data[] = (int)$r['cnt'];
}

$daily_comments_res = mysqli_query($conn,
    "SELECT DATE(created_at) AS day, COUNT(*) AS cnt FROM comments
     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY day ASC");
$daily_comment_labels = []; $daily_comment_data = [];
if ($daily_comments_res) while ($r = mysqli_fetch_assoc($daily_comments_res)) {
    $daily_comment_labels[] = date('D M j', strtotime($r['day'])); $daily_comment_data[] = (int)$r['cnt'];
}

// ── 13. Post status breakdown ─────────────────────────────────────────────────
$status_res = mysqli_query($conn, "SELECT status, COUNT(*) AS cnt FROM posts GROUP BY status");
$status_data = ['published' => 0, 'draft' => 0];
if ($status_res) while ($r = mysqli_fetch_assoc($status_res)) $status_data[$r['status']] = (int)$r['cnt'];

// ── 14. Newest registered users ───────────────────────────────────────────────
$new_users_res = mysqli_query($conn,
    "SELECT name, email, profile_image, role, created_at FROM users ORDER BY created_at DESC LIMIT 6");
$new_users = $new_users_res ? mysqli_fetch_all($new_users_res, MYSQLI_ASSOC) : [];

// Helpers
function fmt_num($n) {
    if ($n >= 1_000_000) return round($n/1_000_000, 1).'M';
    if ($n >= 1_000)     return round($n/1_000, 1).'K';
    return (string)(int)$n;
}
function time_since($dt) {
    $d = time() - strtotime($dt);
    if ($d < 60)    return $d.'s ago';
    if ($d < 3600)  return floor($d/60).'m ago';
    if ($d < 86400) return floor($d/3600).'h ago';
    if ($d < 604800)return floor($d/86400).'d ago';
    return date('M j, Y', strtotime($dt));
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Site Analytics — Blog Fusion Admin</title>
    <meta name="description" content="Comprehensive site analytics dashboard for Blog Fusion admin panel." />
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
                        "primary":          "#7C3AED",
                        "primary-hover":    "#6D28D9",
                        "background-light": "#f8f7ff",
                        "background-dark":  "#0f172a",
                        "indigo-accent":    "#4F46E5",
                    },
                    fontFamily: { "display": ["Public Sans", "sans-serif"] },
                    borderRadius: { "DEFAULT":"0.25rem","lg":"0.5rem","xl":"0.75rem","full":"9999px" },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .bar-fill { transition: width 0.8s cubic-bezier(.4,0,.2,1); }
        .stat-card { transition: box-shadow 0.2s, transform 0.2s; }
        .stat-card:hover { box-shadow: 0 8px 32px 0 rgba(124,58,237,0.10); transform: translateY(-2px); }
        .gradient-purple { background: linear-gradient(135deg, #7C3AED 0%, #4F46E5 100%); }
        .gradient-indigo  { background: linear-gradient(135deg, #4F46E5 0%, #0ea5e9 100%); }
        .gradient-pink    { background: linear-gradient(135deg, #f43f5e 0%, #f59e0b 100%); }
        .gradient-emerald { background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%); }
        .gradient-amber   { background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); }
        .gradient-cyan    { background: linear-gradient(135deg, #0ea5e9 0%, #7C3AED 100%); }
        canvas { display: block; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
<div class="flex h-screen overflow-hidden">

    <?= slidebar('analytics'); ?>
    <div id="overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <?= ad_navbar(); ?>

        <div class="flex-1 overflow-y-auto p-6 lg:p-8">

            <!-- ── PAGE HEADER ────────────────────────────────────────────── -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-black tracking-tight">Site Analytics</h1>
                    <p class="text-slate-500 mt-1 text-sm">Full-spectrum performance metrics for your blog platform.</p>
                </div>
                <div class="flex items-center gap-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-500">
                    <span class="material-symbols-outlined text-base text-primary">calendar_today</span>
                    <span>Updated: <strong><?= date('M j, Y, g:i a') ?></strong></span>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════════
                 ROW 1 — 6 KPI STAT CARDS
            ══════════════════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">

                <!-- Total Views -->
                <div class="stat-card col-span-1 sm:col-span-2 md:col-span-1 gradient-purple text-white p-5 rounded-2xl shadow-lg relative overflow-hidden">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-[22px]">visibility</span>
                    </div>
                    <p class="text-3xl font-black leading-none"><?= fmt_num($total_views) ?></p>
                    <p class="text-white/70 text-[11px] font-bold uppercase tracking-widest mt-1">Total Views</p>
                    <p class="text-white/60 text-xs mt-1"><?= badge($growth_views['pct']) ?> <span class="opacity-70 ml-1">vs last month</span></p>
                    <div class="absolute -right-3 -bottom-3 opacity-10 pointer-events-none">
                        <span class="material-symbols-outlined text-7xl" style="font-variation-settings:'FILL' 1;">visibility</span>
                    </div>
                </div>

                <!-- Published Posts -->
                <div class="stat-card gradient-indigo text-white p-5 rounded-2xl shadow-lg relative overflow-hidden">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-[22px]">article</span>
                    </div>
                    <p class="text-3xl font-black leading-none"><?= fmt_num($published) ?></p>
                    <p class="text-white/70 text-[11px] font-bold uppercase tracking-widest mt-1">Published Posts</p>
                    <p class="text-white/60 text-xs mt-1"><?= badge($growth_posts['pct']) ?> <span class="opacity-70 ml-1">vs last month</span></p>
                    <div class="absolute -right-3 -bottom-3 opacity-10 pointer-events-none">
                        <span class="material-symbols-outlined text-7xl" style="font-variation-settings:'FILL' 1;">article</span>
                    </div>
                </div>

                <!-- Registered Users -->
                <div class="stat-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
                    <div class="w-10 h-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-[22px]">group</span>
                    </div>
                    <p class="text-2xl font-black"><?= fmt_num($total_users) ?></p>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-1">Users</p>
                    <div class="mt-1"><?= badge($growth_users['pct']) ?> <span class="text-[10px] text-slate-400 ml-1">vs last mo.</span></div>
                </div>

                <!-- Comments -->
                <div class="stat-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
                    <div class="w-10 h-10 bg-amber-500/10 text-amber-500 rounded-xl flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-[22px]">forum</span>
                    </div>
                    <p class="text-2xl font-black"><?= fmt_num($total_comments) ?></p>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-1">Comments</p>
                    <div class="mt-1"><?= badge($growth_comments['pct']) ?> <span class="text-[10px] text-slate-400 ml-1">vs last mo.</span></div>
                </div>

                <!-- Reactions -->
                <div class="stat-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
                    <div class="w-10 h-10 bg-pink-500/10 text-pink-500 rounded-xl flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-[22px]">favorite</span>
                    </div>
                    <p class="text-2xl font-black"><?= fmt_num($total_reactions) ?></p>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-1">Reactions</p>
                    <p class="text-[10px] text-slate-400 mt-1">all time</p>
                </div>

                <!-- Avg Views/Post -->
                <div class="stat-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-2xl shadow-sm">
                    <div class="w-10 h-10 bg-emerald-500/10 text-emerald-500 rounded-xl flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-[22px]">bar_chart</span>
                    </div>
                    <p class="text-2xl font-black"><?= fmt_num($avg_views) ?></p>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-1">Avg Views/Post</p>
                    <p class="text-[10px] text-slate-400 mt-1"><?= $published ?> live posts</p>
                </div>

            </div>

            <!-- ══════════════════════════════════════════════════════════════
                 ROW 2 — Views Trend (large) + Post Status Donut
            ══════════════════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">

                <!-- Monthly Views Area Chart (8 cols) -->
                <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Traffic</p>
                            <h2 class="text-lg font-bold">Monthly Page Views <span class="text-slate-400 font-normal text-sm ml-1">(last 12 months)</span></h2>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="flex items-center gap-1 text-xs text-slate-500"><span class="w-3 h-3 rounded-full bg-primary inline-block"></span>Views</span>
                            <div class="h-9 w-9 bg-primary/10 text-primary rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">trending_up</span>
                            </div>
                        </div>
                    </div>
                    <div style="height:260px;">
                        <canvas id="viewsChart"></canvas>
                    </div>
                </div>

                <!-- Post Status + User Role Donut (4 cols) -->
                <div class="lg:col-span-4 flex flex-col gap-6">

                    <!-- Post Status Donut -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 flex-1">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Posts</p>
                                <h2 class="text-base font-bold">Status Breakdown</h2>
                            </div>
                            <div class="w-9 h-9 bg-indigo-100 text-indigo-500 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">donut_large</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div style="width:110px;height:110px;flex-shrink:0;">
                                <canvas id="postStatusChart"></canvas>
                            </div>
                            <div class="flex flex-col gap-2 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                    <span class="text-slate-600 dark:text-slate-300">Published</span>
                                    <span class="font-black ml-auto text-emerald-600"><?= $status_data['published'] ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-amber-400 flex-shrink-0"></span>
                                    <span class="text-slate-600 dark:text-slate-300">Draft</span>
                                    <span class="font-black ml-auto text-amber-500"><?= $status_data['draft'] ?></span>
                                </div>
                                <div class="flex items-center gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                                    <span class="text-slate-400 text-xs">Total</span>
                                    <span class="font-black ml-auto"><?= $total_posts ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Role Donut -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 flex-1">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Users</p>
                                <h2 class="text-base font-bold">Role Breakdown</h2>
                            </div>
                            <div class="w-9 h-9 bg-primary/10 text-primary rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">pie_chart</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div style="width:110px;height:110px;flex-shrink:0;">
                                <canvas id="userRoleChart"></canvas>
                            </div>
                            <div class="flex flex-col gap-2 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-primary flex-shrink-0"></span>
                                    <span class="text-slate-600 dark:text-slate-300">Admins</span>
                                    <span class="font-black ml-auto text-primary"><?= $roles['admin'] ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-indigo-400 flex-shrink-0"></span>
                                    <span class="text-slate-600 dark:text-slate-300">Authors</span>
                                    <span class="font-black ml-auto text-indigo-500"><?= $roles['author'] ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-slate-300 flex-shrink-0"></span>
                                    <span class="text-slate-600 dark:text-slate-300">Members</span>
                                    <span class="font-black ml-auto"><?= $roles['user'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════════
                 ROW 3 — New Users + New Posts + Comments (3 line charts)
            ══════════════════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

                <!-- New Users Chart -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Growth</p>
                            <h3 class="font-bold">New Users</h3>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-black text-primary"><?= $growth_users['this'] ?></p>
                            <p class="text-[10px] text-slate-400">this month</p>
                        </div>
                    </div>
                    <div style="height:120px;"><canvas id="usersChart"></canvas></div>
                </div>

                <!-- New Posts Chart -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Content</p>
                            <h3 class="font-bold">New Posts</h3>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-black text-indigo-500"><?= $growth_posts['this'] ?></p>
                            <p class="text-[10px] text-slate-400">this month</p>
                        </div>
                    </div>
                    <div style="height:120px;"><canvas id="postsChart"></canvas></div>
                </div>

                <!-- Comments Chart -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Engagement</p>
                            <h3 class="font-bold">Comments</h3>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-black text-amber-500"><?= $growth_comments['this'] ?></p>
                            <p class="text-[10px] text-slate-400">this month</p>
                        </div>
                    </div>
                    <div style="height:120px;"><canvas id="commentsChart"></canvas></div>
                </div>

            </div>

            <!-- ══════════════════════════════════════════════════════════════
                 ROW 4 — Views by Category (bar) + Top Engagement Posts
            ══════════════════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">

                <!-- Category Views Bar Chart (7 cols) -->
                <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Breakdown</p>
                            <h2 class="text-lg font-bold">Views by Category</h2>
                        </div>
                        <div class="w-9 h-9 bg-amber-100 text-amber-500 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">bar_chart</span>
                        </div>
                    </div>
                    <div style="height:240px;"><canvas id="categoryChart"></canvas></div>
                </div>

                <!-- Top Engagement Posts (5 cols) -->
                <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Engagement</p>
                        <h2 class="text-base font-bold">Top Engagement Rate</h2>
                        <p class="text-xs text-slate-400 mt-0.5">(reactions + comments) ÷ views</p>
                    </div>
                    <?php if (empty($engagement_posts)): ?>
                    <p class="px-6 py-8 text-center text-slate-400 text-sm">No data yet.</p>
                    <?php else: ?>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php foreach ($engagement_posts as $i => $ep):
                            $rate = $ep['views'] > 0 ? round($ep['engagements'] / $ep['views'] * 100, 1) : 0;
                        ?>
                        <div class="px-5 py-4 flex items-center gap-3">
                            <span class="text-sm font-black w-5 text-center <?= $i===0?'text-amber-500':($i===1?'text-slate-400':($i===2?'text-amber-700':'text-slate-300')) ?>">#<?= $i+1 ?></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold line-clamp-1"><?= htmlspecialchars($ep['title']) ?></p>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="bar-fill h-full rounded-full bg-primary" style="width:<?= min($rate*5, 100) ?>%"></div>
                                    </div>
                                    <span class="text-[11px] text-slate-500 font-bold whitespace-nowrap"><?= $rate ?>%</span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm font-black text-primary"><?= fmt_num($ep['engagements']) ?></p>
                                <p class="text-[10px] text-slate-400">interact.</p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- ══════════════════════════════════════════════════════════════
                 ROW 5 — Top 10 Posts Table
            ══════════════════════════════════════════════════════════════════ -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Leaderboard</p>
                        <h2 class="text-lg font-bold">Top 10 Posts by Views</h2>
                    </div>
                    <a href="posts.php" class="text-primary text-sm font-semibold hover:underline flex items-center gap-1">
                        View All <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
                <?php if (empty($top_posts)): ?>
                <p class="px-6 py-10 text-center text-slate-400 text-sm">No published posts yet.</p>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/50">
                                <th class="px-5 py-3">#</th>
                                <th class="px-3 py-3">Post</th>
                                <th class="px-3 py-3 hidden md:table-cell">Category</th>
                                <th class="px-3 py-3 hidden lg:table-cell">Author</th>
                                <th class="px-3 py-3 text-right">Views</th>
                                <th class="px-3 py-3 text-right hidden sm:table-cell">Comments</th>
                                <th class="px-3 py-3 text-right hidden sm:table-cell">Reactions</th>
                                <th class="px-3 py-3 text-center hidden md:table-cell">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <?php foreach ($top_posts as $rank => $post):
                                $medals = ['🥇','🥈','🥉'];
                            ?>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-5 py-3 font-black text-center w-10">
                                    <?= isset($medals[$rank]) ? $medals[$rank] : '<span class="text-slate-400">'.($rank+1).'</span>' ?>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <?php if ($post['image']): ?>
                                        <img src="../<?= htmlspecialchars($post['image']) ?>" class="w-9 h-9 rounded-lg object-cover flex-shrink-0" onerror="this.src='https://placehold.co/36x36/e8dfee/7C3AED?text=P'" />
                                        <?php else: ?>
                                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                            <span class="material-symbols-outlined text-primary text-[16px]">article</span>
                                        </div>
                                        <?php endif; ?>
                                        <p class="font-semibold line-clamp-1 max-w-[180px]"><?= htmlspecialchars($post['title']) ?></p>
                                    </div>
                                </td>
                                <td class="px-3 py-3 hidden md:table-cell">
                                    <span class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-primary/10 text-primary uppercase">
                                        <?= htmlspecialchars($post['category_name'] ?? 'Uncategorized') ?>
                                    </span>
                                </td>
                                <td class="px-3 py-3 hidden lg:table-cell">
                                    <div class="flex items-center gap-2">
                                        <img src="../<?= htmlspecialchars($post['profile_image'] ?: 'upload/profile-images/default.png') ?>" class="w-6 h-6 rounded-full object-cover" onerror="this.src='../upload/profile-images/default.png'" />
                                        <span class="text-sm truncate max-w-[100px]"><?= htmlspecialchars($post['author_name'] ?? 'Unknown') ?></span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-right font-black text-primary"><?= number_format($post['views']) ?></td>
                                <td class="px-3 py-3 text-right text-slate-600 hidden sm:table-cell"><?= $post['comment_count'] ?></td>
                                <td class="px-3 py-3 text-right text-pink-500 hidden sm:table-cell"><?= $post['reaction_count'] ?></td>
                                <td class="px-3 py-3 text-center text-slate-400 text-xs hidden md:table-cell whitespace-nowrap"><?= date('M j, Y', strtotime($post['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- ══════════════════════════════════════════════════════════════
                 ROW 6 — Top Authors + Newest Users
            ══════════════════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">

                <!-- Top Authors -->
                <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Performance</p>
                            <h2 class="text-lg font-bold">Top Authors by Views</h2>
                        </div>
                        <a href="users.php" class="text-primary text-sm font-semibold hover:underline">View All</a>
                    </div>
                    <?php if (empty($top_authors)): ?>
                    <p class="px-6 py-8 text-center text-slate-400 text-sm">No author data yet.</p>
                    <?php else: ?>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php foreach ($top_authors as $i => $au):
                            $bar_pct = $max_author_views > 0 ? round((int)$au['total_views'] / $max_author_views * 100) : 0;
                            if ($i === 0) {
                                $rank_style = "bg-gradient-to-br from-yellow-400 to-amber-500 text-white shadow-sm shadow-yellow-500/20";
                                $rank_text  = "1";
                            } elseif ($i === 1) {
                                $rank_style = "bg-gradient-to-br from-slate-300 to-slate-400 text-white shadow-sm shadow-slate-400/20";
                                $rank_text  = "2";
                            } elseif ($i === 2) {
                                $rank_style = "bg-gradient-to-br from-amber-600 to-amber-700 text-white shadow-sm shadow-amber-700/20";
                                $rank_text  = "3";
                            } else {
                                $rank_style = "border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400";
                                $rank_text  = $i + 1;
                            }
                        ?>
                        <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full text-xs font-bold <?= $rank_style ?>">
                                <?= $rank_text ?>
                            </span>
                            <div class="relative flex-shrink-0">
                                <img src="../<?= htmlspecialchars($au['profile_image'] ?: 'upload/profile-images/default.png') ?>"
                                     class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-slate-800"
                                     onerror="this.src='../upload/profile-images/default.png'" />
                                <span class="absolute -bottom-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-500 text-[9px] text-white ring-2 ring-white dark:ring-slate-900" title="Active">
                                    <span class="material-symbols-outlined text-[10px] font-bold">check</span>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="font-bold text-sm text-slate-800 dark:text-slate-100 truncate"><?= htmlspecialchars($au['name']) ?></p>
                                    <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full <?= $au['role']==='admin' ? 'bg-violet-100 text-violet-750 dark:bg-violet-900/30 dark:text-violet-400' : 'bg-indigo-100 text-indigo-750 dark:bg-indigo-900/30 dark:text-indigo-400' ?>">
                                        <?= $au['role'] ?>
                                    </span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                                        <div class="bar-fill h-full rounded-full bg-gradient-to-r from-violet-500 to-indigo-600" style="width:<?= $bar_pct ?>%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm text-slate-400">visibility</span>
                                        <?= number_format($au['total_views']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <!-- Posts Badge -->
                                <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/40 px-2.5 py-1.5 rounded-xl border border-slate-100 dark:border-slate-800/60" title="Total Posts">
                                    <span class="material-symbols-outlined text-[15px] text-slate-400">article</span>
                                    <span class="text-xs text-slate-700 dark:text-slate-300 font-bold"><?= $au['post_count'] ?></span>
                                    <span class="text-[10px] text-slate-400 hidden xl:inline"><?= $au['post_count'] == 1 ? 'post' : 'posts' ?></span>
                                </div>
                                <!-- Comments Badge -->
                                <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/40 px-2.5 py-1.5 rounded-xl border border-slate-100 dark:border-slate-800/60" title="Total Comments Received">
                                    <span class="material-symbols-outlined text-[15px] text-slate-400">forum</span>
                                    <span class="text-xs text-slate-700 dark:text-slate-300 font-bold"><?= fmt_num($au['comment_count']) ?></span>
                                    <span class="text-[10px] text-slate-400 hidden xl:inline"><?= $au['comment_count'] == 1 ? 'cmt' : 'cmts' ?></span>
                                </div>
                                <!-- Reactions Badge -->
                                <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/40 px-2.5 py-1.5 rounded-xl border border-slate-100 dark:border-slate-800/60" title="Total Reactions Received">
                                    <span class="material-symbols-outlined text-[15px] text-rose-400">favorite</span>
                                    <span class="text-xs text-rose-500 font-bold"><?= fmt_num($au['reaction_count']) ?></span>
                                    <span class="text-[10px] text-slate-400 hidden xl:inline"><?= $au['reaction_count'] == 1 ? 'rxn' : 'rxns' ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Newest Users -->
                <div class="lg:col-span-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Latest</p>
                        <h2 class="text-base font-bold">Newest Members</h2>
                    </div>
                    <?php if (empty($new_users)): ?>
                    <p class="px-6 py-8 text-center text-slate-400 text-sm">No users yet.</p>
                    <?php else: ?>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php foreach ($new_users as $nu): ?>
                        <div class="px-5 py-3.5 flex items-center gap-3">
                            <img src="../<?= htmlspecialchars($nu['profile_image'] ?: 'upload/profile-images/default.png') ?>"
                                 class="w-9 h-9 rounded-xl object-cover flex-shrink-0"
                                 onerror="this.src='../upload/profile-images/default.png'" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold truncate"><?= htmlspecialchars($nu['name']) ?></p>
                                <p class="text-[11px] text-slate-400 truncate"><?= htmlspecialchars($nu['email']) ?></p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded-full <?= $nu['role']==='admin'?'bg-primary/10 text-primary':($nu['role']==='author'?'bg-indigo-100 text-indigo-600':'bg-slate-100 text-slate-500') ?>">
                                    <?= $nu['role'] ?>
                                </span>
                                <p class="text-[10px] text-slate-400 mt-1"><?= time_since($nu['created_at']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                        <a href="users.php" class="w-full text-center block text-sm font-semibold text-slate-500 hover:text-primary transition-colors">
                            View All Users →
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

            </div>

        </div><!-- end scroll area -->
    </main>
</div>

<script src="../assets/js/admin.js"></script>
<script>
Chart.defaults.font.family = "'Public Sans', sans-serif";
Chart.defaults.color = '#64748b';

const isDark   = document.documentElement.classList.contains('dark');
const gridCol  = isDark ? 'rgba(148,163,184,0.08)' : 'rgba(148,163,184,0.20)';
const tickCol  = isDark ? '#94a3b8' : '#64748b';

/* ── Gradient helper ──────────────────────────────────────────────── */
function makeGrad(ctx, color, alpha = 0.25) {
    const g = ctx.createLinearGradient(0, 0, 0, ctx.canvas.height);
    g.addColorStop(0,   color.replace(')', `, ${alpha})`).replace('rgb', 'rgba'));
    g.addColorStop(1,   color.replace(')', ', 0)').replace('rgb', 'rgba'));
    return g;
}

/* ── Sparkline helper ─────────────────────────────────────────────── */
function sparkLine(id, labels, data, color) {
    const ctx  = document.getElementById(id);
    if (!ctx || !data.length) return;
    const g = ctx.getContext('2d').createLinearGradient(0, 0, 0, 120);
    g.addColorStop(0, color + '30'); g.addColorStop(1, color + '00');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{ data, fill: true, backgroundColor: g,
                borderColor: color, borderWidth: 2,
                pointRadius: 0, pointHoverRadius: 4, tension: 0.4 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
            scales: {
                x: { display: false },
                y: { display: false, beginAtZero: true }
            }
        }
    });
}

/* ── 1. Monthly Views Area Chart ─────────────────────────────────── */
(function(){
    const ctx = document.getElementById('viewsChart');
    if (!ctx) return;
    const labels = <?= json_encode($v12_labels) ?>;
    const data   = <?= json_encode($v12_data)   ?>;
    const g = ctx.getContext('2d').createLinearGradient(0, 0, 0, 260);
    g.addColorStop(0, 'rgba(124,58,237,0.30)'); g.addColorStop(1, 'rgba(124,58,237,0)');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length ? labels : ['No data'],
            datasets: [{
                label: 'Page Views', data: data.length ? data : [0],
                fill: true, backgroundColor: g,
                borderColor: '#7C3AED', borderWidth: 2.5,
                tension: 0.42, pointBackgroundColor: '#7C3AED',
                pointRadius: 4, pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b', titleColor: '#f8fafc', bodyColor: '#94a3b8',
                    padding: 10, cornerRadius: 8,
                    callbacks: { label: c => ' ' + c.parsed.y.toLocaleString() + ' views' }
                }
            },
            scales: {
                x: { grid: { color: gridCol }, ticks: { color: tickCol, font: { size: 11 } } },
                y: { grid: { color: gridCol }, ticks: { color: tickCol, font: { size: 11 },
                     callback: v => v >= 1000 ? (v/1000).toFixed(1)+'k' : v } }
            }
        }
    });
})();

/* ── 2. Post Status Donut ─────────────────────────────────────────── */
new Chart(document.getElementById('postStatusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Published','Draft'],
        datasets: [{ data: [<?= $status_data['published'] ?>, <?= $status_data['draft'] ?>],
            backgroundColor: ['#10b981','#f59e0b'], borderWidth: 2, borderColor: '#fff' }]
    },
    options: { responsive: true, maintainAspectRatio: false, cutout: '72%',
               plugins: { legend: { display: false } } }
});

/* ── 3. User Role Donut ───────────────────────────────────────────── */
new Chart(document.getElementById('userRoleChart'), {
    type: 'doughnut',
    data: {
        labels: ['Admin','Author','Member'],
        datasets: [{ data: [<?= $roles['admin'] ?>, <?= $roles['author'] ?>, <?= $roles['user'] ?>],
            backgroundColor: ['#7C3AED','#818cf8','#cbd5e1'], borderWidth: 2, borderColor: '#fff' }]
    },
    options: { responsive: true, maintainAspectRatio: false, cutout: '72%',
               plugins: { legend: { display: false } } }
});

/* ── 4. Sparklines ───────────────────────────────────────────────── */
sparkLine('usersChart',    <?= json_encode($u12_labels) ?>, <?= json_encode($u12_data) ?>,   '#7C3AED');
sparkLine('postsChart',    <?= json_encode($p12_labels) ?>, <?= json_encode($p12_data) ?>,   '#4F46E5');
sparkLine('commentsChart', <?= json_encode($c12_labels) ?>, <?= json_encode($c12_data) ?>,   '#f59e0b');

/* ── 5. Category Views Bar Chart ─────────────────────────────────── */
(function(){
    const ctx = document.getElementById('categoryChart');
    if (!ctx) return;
    const labels = <?= json_encode($cat_labels)      ?>;
    const data   = <?= json_encode($cat_views_data)  ?>;
    const barColors = ['#7C3AED','#4F46E5','#0ea5e9','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899'];
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels.length ? labels : ['No categories'],
            datasets: [{
                label: 'Views', data: data.length ? data : [0],
                backgroundColor: barColors.map(c => c + 'cc'),
                borderColor: barColors,
                borderWidth: 1.5, borderRadius: 8, borderSkipped: false
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b', titleColor: '#f8fafc', bodyColor: '#94a3b8',
                    padding: 10, cornerRadius: 8,
                    callbacks: { label: c => ' ' + c.parsed.y.toLocaleString() + ' views' }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: tickCol, font: { size: 11 } } },
                y: { grid: { color: gridCol }, ticks: { color: tickCol, font: { size: 11 },
                     callback: v => v >= 1000 ? (v/1000).toFixed(1)+'k' : v } }
            }
        }
    });
})();
</script>

</body>
</html>

<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAuthor();
include BASE_PATH . 'include/db.php';
include BASE_PATH . 'include/author_nav_sidebar.php';
$id = $_SESSION['user_id'];

/* ── Stats ───────────────────────────────────────────────────── */
$total_comments = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as t FROM comments c
     INNER JOIN posts p ON c.post_id = p.id
     WHERE p.author_id = $id"
))['t'];

$total_reactions = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as t FROM reactions r
     INNER JOIN posts p ON r.post_id = p.id
     WHERE p.author_id = $id"
))['t'];

$total_posts = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as t FROM posts WHERE author_id = $id"
))['t'];

/* ── Activity Feed: merge comments + reactions, newest first ─── */
/*
   UNION query:
     type='comment'  → from comments joined to author's posts
     type='reaction' → from reactions joined to author's posts
   Each row brings: type, actor_user_id, content, post_title, created_at, extra_id
*/
$activity_sql = "
    SELECT
        'comment'        AS type,
        c.id             AS activity_id,
        c.user_id        AS actor_id,
        c.comment        AS content,
        p.title          AS post_title,
        p.id             AS post_id,
        c.created_at     AS created_at
    FROM comments c
    INNER JOIN posts p ON c.post_id = p.id
    WHERE p.author_id = $id

    UNION ALL

    SELECT
        'reaction'           AS type,
        r.id                 AS activity_id,
        r.user_id            AS actor_id,
        r.emoji      AS content,
        p.title              AS post_title,
        p.id                 AS post_id,
        r.created_at         AS created_at
    FROM reactions r
    INNER JOIN posts p ON r.post_id = p.id
    WHERE p.author_id = $id

    ORDER BY created_at DESC
    LIMIT 10
";
$activity_result = mysqli_query($conn, $activity_sql);
$activities = $activity_result ? mysqli_fetch_all($activity_result, MYSQLI_ASSOC) : [];

/* ── Recent Posts: latest 3 published posts with stats ──────── */
/*
   Fetches the 3 most recent published posts by this author.
   Each row includes:
     - post fields (id, title, short_description/content, image, created_at)
     - category name via LEFT JOIN
     - comment_count  → COUNT of comments on that post
     - reaction_count → COUNT of reactions on that post
*/
$recent_posts_sql = "
    SELECT
        p.id,
        p.title,
        p.slug,
        p.description,
        p.content,
        p.image,
        p.views,
        p.created_at,
        p.status,
        cat.name                     AS category_name,
        COUNT(DISTINCT c.id)         AS comment_count,
        COUNT(DISTINCT r.id)         AS reaction_count,
        COALESCE(SUM(s.share), 0)    AS share_count
    FROM posts p
    LEFT JOIN categories  cat ON cat.id  = p.category_id
    LEFT JOIN comments    c   ON c.post_id  = p.id
    LEFT JOIN reactions   r   ON r.post_id  = p.id
    LEFT JOIN share       s   ON s.post_id  = p.id
    WHERE p.author_id = $id
      AND p.status    = 'published'
    GROUP BY p.id, p.title, p.slug, p.description,
             p.content, p.image, p.views, p.created_at,
             p.status, cat.name
    ORDER BY p.created_at DESC
    LIMIT 3
";
$rp_result = mysqli_query($conn, $recent_posts_sql);
$recent_posts = $rp_result ? mysqli_fetch_all($rp_result, MYSQLI_ASSOC) : [];

/* ── Helper: human-readable time ago ────────────────────────── */
function time_ago(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60)
        return $diff . 's ago';
    if ($diff < 3600)
        return floor($diff / 60) . 'm ago';
    if ($diff < 86400)
        return floor($diff / 3600) . 'h ago';
    if ($diff < 604800)
        return floor($diff / 86400) . 'd ago';
    return date('M d', strtotime($datetime));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="<?php echo defined('BASE_URL') ? BASE_URL : '/BlogFusion/'; ?>upload/site_image/logo2.png" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Luminous | Author Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
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
        body {
            font-family: 'Public Sans', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body class="bg-background text-on-background selection:bg-primary-fixed selection:text-on-primary-fixed">

    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-black/40 z-40 md:hidden hidden transition-opacity" id="sidebar-overlay"
        onclick="toggleSidebar()"></div>

    <?= author_slidebar('dashboard') ?>
    <?= author_navbar(); ?>

    <main class="md:ml-64 pt-20 min-h-screen px-4 sm:px-6 pb-12 transition-all duration-300">

        <!-- Welcome Header -->
        <header class="mb-8 flex flex-col gap-1">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-on-surface">
                Good Morning, <?= htmlspecialchars($data1['name'] ?? 'Author') ?>
            </h1>
            <p class="text-on-surface-variant font-medium">Here's what's happening with your content today.</p>
        </header>

        <!-- ── Stats Bento Grid ────────────────────────────────── -->
        <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 mb-8">

            <!-- Total Posts -->
            <div
                class="bg-surface-container-lowest p-5 sm:p-6 rounded-xl flex flex-col gap-4 shadow-sm border border-outline-variant/10 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10">
                    <div class="w-12 h-12 rounded-lg bg-primary-fixed/30 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-2xl">post_add</span>
                    </div>
                    <span class="text-xs font-bold text-primary px-2 py-1 bg-primary-fixed/20 rounded-full">+12% this
                        week</span>
                </div>
                <div class="z-10">
                    <h3 class="text-on-surface-variant text-sm font-semibold uppercase tracking-wider">Total Posts</h3>
                    <p class="text-4xl font-extrabold text-on-surface mt-1"><?= $total_posts ?? 0 ?></p>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-9xl"
                        style="font-variation-settings:'FILL' 1;">post_add</span>
                </div>
            </div>

            <!-- Total Comments -->
            <div
                class="bg-surface-container-lowest p-5 sm:p-6 rounded-xl flex flex-col gap-4 shadow-sm border border-outline-variant/10 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10">
                    <div
                        class="w-12 h-12 rounded-lg bg-tertiary-fixed/30 flex items-center justify-center text-tertiary">
                        <span class="material-symbols-outlined text-2xl">forum</span>
                    </div>
                    <span class="text-xs font-bold text-tertiary px-2 py-1 bg-tertiary-fixed/20 rounded-full">+8.4%
                        today</span>
                </div>
                <div class="z-10">
                    <h3 class="text-on-surface-variant text-sm font-semibold uppercase tracking-wider">Total Comments
                    </h3>
                    <p class="text-4xl font-extrabold text-on-surface mt-1"><?= $total_comments ?? 0 ?></p>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-9xl"
                        style="font-variation-settings:'FILL' 1;">forum</span>
                </div>
            </div>

            <!-- Total Reactions -->
            <div
                class="bg-surface-container-lowest p-5 sm:p-6 rounded-xl flex flex-col gap-4 shadow-sm border border-outline-variant/10 relative overflow-hidden group sm:col-span-2 md:col-span-1">
                <div class="flex justify-between items-start z-10">
                    <div
                        class="w-12 h-12 rounded-lg bg-secondary-fixed/30 flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-2xl">favorite</span>
                    </div>
                    <span class="text-xs font-bold text-secondary px-2 py-1 bg-secondary-fixed/20 rounded-full">+24%
                        avg</span>
                </div>
                <div class="z-10">
                    <h3 class="text-on-surface-variant text-sm font-semibold uppercase tracking-wider">Total Reactions
                    </h3>
                    <p class="text-4xl font-extrabold text-on-surface mt-1"><?= $total_reactions ?? 0 ?></p>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-9xl"
                        style="font-variation-settings:'FILL' 1;">favorite</span>
                </div>
            </div>
        </section>

        <!-- ── Main Grid: Recent Posts + Activity Feed ─────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

            <!-- ── RECENT POSTS (fully dynamic) ──────────────── -->
            <section class="lg:col-span-8 flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-on-surface">Recent Posts</h2>
                    <a href="my-post.php" class="text-sm font-semibold text-primary hover:underline">View All Posts</a>
                </div>

                <?php if (empty($recent_posts)): ?>
                    <!-- Empty state -->
                    <div
                        class="flex flex-col items-center justify-center py-16 text-center bg-surface-container-low rounded-xl border border-outline-variant/10">
                        <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-3xl text-on-surface-variant">article</span>
                        </div>
                        <p class="text-sm font-semibold text-on-surface">No published posts yet</p>
                        <p class="text-xs text-on-surface-variant mt-1 mb-4">Start writing and publish your first post.</p>
                        <a href="edit-post.php"
                            class="px-5 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:opacity-90 transition-opacity">
                            Create Post
                        </a>
                    </div>

                <?php else:
                    /* ── Featured post: index 0 ── */
                    $featured = $recent_posts[0];
                    $f_cat = htmlspecialchars($featured['category_name'] ?? 'Uncategorized');
                    $f_title = htmlspecialchars($featured['title']);
                    $f_desc = htmlspecialchars($featured['description'] ?? strip_tags(substr($featured['content'], 0, 160)));
                    $f_img = BASE_URL . $featured['image'];
                    $f_ago = 'Published ' . time_ago($featured['created_at']);
                    $f_views = number_format($featured['views']);
                    $f_comments = number_format($featured['comment_count']);
                    $f_reactions = number_format($featured['reaction_count']);
                    $f_shares = number_format($featured['share_count']);
                    ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">

                        <!-- Featured post — full width row -->
                        <div class="col-span-1 sm:col-span-2 group cursor-pointer"
                            onclick="window.location.href='edit-post.php?id=<?= $featured['id'] ?>'">
                            <div
                                class="bg-surface-container-low rounded-xl p-4 sm:p-6 flex flex-col sm:flex-row gap-5 sm:gap-6 hover:bg-surface-container transition-colors">
                                <!-- Thumbnail -->
                                <div
                                    class="w-full sm:w-48 h-44 sm:h-48 rounded-lg overflow-hidden flex-shrink-0 bg-slate-200">
                                    <img alt="<?= $f_title ?>"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        src="<?= $f_img ?>"/>
                                </div>
                                <!-- Meta -->
                                <div class="flex flex-col justify-between py-1">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span
                                                class="px-2 py-0.5 bg-tertiary-container text-on-tertiary rounded text-[10px] font-bold uppercase tracking-widest">
                                                <?= $f_cat ?>
                                            </span>
                                            <span class="text-xs text-on-surface-variant font-medium"><?= $f_ago ?></span>
                                        </div>
                                        <h3 class="text-xl sm:text-2xl font-bold text-on-surface leading-tight">
                                            <?= $f_title ?>
                                        </h3>
                                        <p class="text-on-surface-variant text-sm line-clamp-2"><?= $f_desc ?></p>
                                    </div>
                                    <!-- Stats row — all from real DB columns -->
                                    <div class="flex items-center gap-4 sm:gap-6 mt-4 flex-wrap">
                                        <span class="flex items-center gap-1 text-xs font-semibold text-on-surface-variant">
                                            <span class="material-symbols-outlined text-lg">visibility</span>
                                            <?= $f_views ?>
                                        </span>
                                        <span class="flex items-center gap-1 text-xs font-semibold text-on-surface-variant">
                                            <span class="material-symbols-outlined text-lg">favorite</span>
                                            <?= $f_reactions ?>
                                        </span>
                                        <span class="flex items-center gap-1 text-xs font-semibold text-on-surface-variant">
                                            <span class="material-symbols-outlined text-lg">comment</span>
                                            <?= $f_comments ?>
                                        </span>
                                        <span class="flex items-center gap-1 text-xs font-semibold text-on-surface-variant">
                                            <span class="material-symbols-outlined text-lg">share</span>
                                            <?= $f_shares ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remaining posts (index 1, 2) as small cards -->
                        <?php for ($i = 1; $i < count($recent_posts); $i++):
                            $p = $recent_posts[$i];
                            $p_cat = htmlspecialchars($p['category_name'] ?? 'Uncategorized');
                            $p_ttl = htmlspecialchars($p['title']);
                            $p_dsc = htmlspecialchars($p['description'] ?? strip_tags(substr($p['content'], 0, 100)));
                            $p_img = BASE_URL . $p['image'];
                            $p_ago = time_ago($p['created_at']);
                            /* Alternate category badge colours */
                            $badge_colors = [
                                'bg-secondary-container text-on-secondary',
                                'bg-primary-container text-on-primary',
                            ];
                            $badge = $badge_colors[($i - 1) % 2];
                            ?>
                            <div class="group cursor-pointer" onclick="window.location.href='edit-post.php?id=<?= $p['id'] ?>'">
                                <div
                                    class="bg-surface-container-low rounded-xl p-4 flex flex-col gap-4 hover:bg-surface-container transition-colors h-full">
                                    <div class="w-full aspect-video rounded-lg overflow-hidden bg-slate-200">
                                        <img alt="<?= $p_ttl ?>"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                            src="<?= $p_img ?>" />
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span
                                                class="px-2 py-0.5 <?= $badge ?> rounded text-[10px] font-bold uppercase tracking-widest">
                                                <?= $p_cat ?>
                                            </span>
                                            <span class="text-xs text-on-surface-variant font-medium"><?= $p_ago ?></span>
                                        </div>
                                        <h3 class="text-base font-bold text-on-surface leading-tight line-clamp-2"><?= $p_ttl ?>
                                        </h3>
                                        <p class="text-on-surface-variant text-xs line-clamp-2"><?= $p_dsc ?></p>
                                        <!-- Mini stats: views + reactions + comments + shares -->
                                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                                            <span
                                                class="flex items-center gap-0.5 text-[11px] font-semibold text-on-surface-variant">
                                                <span class="material-symbols-outlined text-base">visibility</span>
                                                <?= number_format($p['views']) ?>
                                            </span>
                                            <span
                                                class="flex items-center gap-0.5 text-[11px] font-semibold text-on-surface-variant">
                                                <span class="material-symbols-outlined text-base">favorite</span>
                                                <?= number_format($p['reaction_count']) ?>
                                            </span>
                                            <span
                                                class="flex items-center gap-0.5 text-[11px] font-semibold text-on-surface-variant">
                                                <span class="material-symbols-outlined text-base">comment</span>
                                                <?= number_format($p['comment_count']) ?>
                                            </span>
                                            <span
                                                class="flex items-center gap-0.5 text-[11px] font-semibold text-on-surface-variant">
                                                <span class="material-symbols-outlined text-base">share</span>
                                                <?= number_format($p['share_count']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>

                        <!-- If only 1 post exists, show a "create" placeholder card -->
                        <?php if (count($recent_posts) === 1): ?>
                            <div class="col-span-1 sm:col-span-2 group">
                                <a href="edit-post.php"
                                    class="bg-surface-container-low rounded-xl p-6 flex flex-col items-center justify-center gap-3 hover:bg-surface-container transition-colors border-2 border-dashed border-outline-variant/30 h-40 text-center">
                                    <span class="material-symbols-outlined text-3xl text-on-surface-variant">add_circle</span>
                                    <p class="text-sm font-semibold text-on-surface-variant">Write another post</p>
                                </a>
                            </div>
                        <?php endif; ?>

                    </div><!-- end posts grid -->
                <?php endif; ?>

            </section>

            <!-- ── ACTIVITY FEED (fully dynamic) ──────────────── -->
            <section class="lg:col-span-4 flex flex-col gap-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-on-surface">Activity Feed</h2>
                    <button class="p-2 hover:bg-surface-container-high rounded-full transition-colors">
                        <span class="material-symbols-outlined text-xl">more_horiz</span>
                    </button>
                </div>

                <?php if (empty($activities)): ?>
                    <!-- Empty state -->
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center mb-4">
                            <span
                                class="material-symbols-outlined text-3xl text-on-surface-variant">notifications_none</span>
                        </div>
                        <p class="text-sm font-semibold text-on-surface">No activity yet</p>
                        <p class="text-xs text-on-surface-variant mt-1">Comments and reactions on your posts will appear
                            here.</p>
                    </div>

                <?php else: ?>
                    <div class="flex flex-col gap-1">

                        <?php foreach ($activities as $act):
                            /* Fetch the actor (commenter / reactor) */
                            $actor_row = data_featch($conn, $act['actor_id']);
                            $actor = $actor_row['data'] ?? null;
                            if (!$actor)
                                continue; // skip if user deleted
                    
                            $is_comment = ($act['type'] === 'comment');
                            $ago = time_ago($act['created_at']);
                            $post_title = htmlspecialchars($act['post_title']);
                            $actor_name = htmlspecialchars($actor['name']);
                            $actor_img = BASE_URL . $actor['profile_image'];
                            $comment_id = $act['activity_id'];
                            ?>

                            <!-- ── Single activity row ── -->
                            <div class="flex gap-3 p-3 rounded-2xl hover:bg-surface-container-low transition-colors group">

                                <!-- Avatar + badge -->
                                <div class="relative flex-shrink-0 mt-0.5">
                                    <img src="<?= $actor_img ?>" alt="<?= $actor_name ?>"
                                        class="w-10 h-10 rounded-xl object-cover bg-surface-container-high" />
                                    <!-- Badge: comment icon or heart icon -->
                                    <div class="absolute -bottom-1 -right-1 w-[18px] h-[18px] rounded-full flex items-center justify-center
                            <?= $is_comment ? 'bg-primary' : 'bg-tertiary' ?>">
                                        <span class="material-symbols-outlined text-white"
                                            style="font-size:11px; font-variation-settings:'FILL' 1;">
                                            <?= $is_comment ? 'mode_comment' : 'favorite' ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex flex-col gap-1 min-w-0 flex-1">
                                    <!-- Name + time -->
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-bold text-on-surface leading-tight"><?= $actor_name ?></span>
                                        <span class="text-[10px] text-on-surface-variant"><?= $ago ?></span>
                                    </div>

                                    <?php if ($is_comment): ?>
                                        <!-- Comment text -->
                                        <p class="text-sm text-on-surface-variant leading-relaxed line-clamp-2">
                                            "<?= htmlspecialchars($act['content']) ?>"
                                        </p>
                                        <!-- Post context pill -->
                                        <p class="text-[11px] text-outline mt-0.5 truncate">
                                            on &nbsp;<span
                                                class="font-semibold text-on-surface-variant italic"><?= $post_title ?></span>
                                        </p>
                                        <!-- Reply + Approve actions -->
                                        <div class="flex items-center gap-4 mt-1.5">
                                            <a href="comments.php" class="text-xs font-bold text-primary hover:underline">Reply</a>
                                        </div>

                                    <?php else: /* reaction */ ?>
                                        <!-- Reaction: show the reaction_type (like/love/wow etc.) -->
                                        <?php
                                        $icon_map = [
                                            'like' => ['👍', 'liked'],
                                            'love' => ['❤️', 'loved'],
                                            'wow' => ['😮', 'reacted wow to'],
                                            'haha' => ['😂', 'laughed at'],
                                            'sad' => ['😢', 'felt sad about'],
                                            'angry' => ['😡', 'reacted to'],
                                        ];
                                        $rtype = strtolower($act['content'] ?? 'like');
                                        $emoji = $icon_map[$rtype][0] ?? '❤️';
                                        $verb = $icon_map[$rtype][1] ?? 'reacted to';
                                        ?>
                                        <p class="text-sm text-on-surface-variant leading-relaxed">
                                            <?= $emoji ?>             <?= $verb ?> your post
                                            <span class="font-semibold italic text-on-surface">"<?= $post_title ?>"</span>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- Divider (not after last item) -->
                        <?php endforeach; ?>

                    </div><!-- end feed list -->
                <?php endif; ?>



            </section><!-- end activity feed -->

        </div><!-- end main grid -->
    </main>

    <!-- FAB — mobile only -->
    <a href="edit-post.php"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-br from-primary to-primary-container text-white rounded-2xl shadow-xl flex items-center justify-center hover:scale-105 active:scale-95 transition-all md:hidden">
        <span class="material-symbols-outlined text-2xl">add</span>
    </a>

    <script>
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
    </script>
</body>

</html>
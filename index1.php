<?php
require_once __DIR__ . '/config.php';
include BASE_PATH . 'include/session.php';
requireUser();
include BASE_PATH . 'include/data_fetch.php';
include BASE_PATH . 'include/db.php';
include_once BASE_PATH . 'include/functions.php';

global $conn;

$user_id = (int) $_SESSION['user_id'];
$currentUser = fetch_user_by_id($conn, $user_id);
$siteSettings = fetch_site_settings();
$siteName = $siteSettings['site_name'] ?? SITE_NAME;
$siteDescription = $siteSettings['description'] ?? 'A modern blog platform for readers, authors, and admins.';
$profileImage = normalize_image($currentUser['profile_image'] ?? '');
$profileName = $currentUser['name'] ?? 'User';

$searchQuery = trim($_GET['q'] ?? '');
$categorySlug = trim($_GET['category'] ?? '');

$latestPosts = fetch_latest_posts($conn, 6, $searchQuery !== '' ? $searchQuery : null, $categorySlug !== '' ? $categorySlug : null);
$heroPost = $latestPosts[0] ?? null;
if (!$heroPost) {
    $fallback = fetch_latest_posts($conn, 1);
    $heroPost = $fallback[0] ?? null;
}
$trendingPosts = fetch_top_posts($conn, 4);
$categoryStats = fetch_category_stats($conn);
$dashboardStats = fetch_dashboard_stats($conn);

$publishedCount = (int)($dashboardStats['published_posts'] ?? 0);
$totalCategories = (int)($dashboardStats['total_categories'] ?? 0);
$totalReactions = (int)($dashboardStats['total_reactions'] ?? 0);
$totalComments = (int)($dashboardStats['total_comments'] ?? 0);

$activeLabel = $searchQuery !== '' ? 'Search results for "' . escape_html($searchQuery) . '"' : ($categorySlug !== '' ? 'Category: ' . escape_html($categorySlug) : 'Latest Stories');
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo defined('BASE_URL') ? BASE_URL : '/BlogFusion/'; ?>upload/site_image/logo2.png" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?php echo escape_html($siteName); ?> | Insights into Tech, Programming &amp; Business</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#7C3AED",
                        secondary: "#4F46E5",
                        accent: "#EC4899",
                        "background-light": "#F3F4F6",
                        "background-dark": "#111827",
                    },
                    fontFamily: {
                        display: ["Public Sans", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        "2xl": "1rem",
                        "3xl": "1.5rem",
                        full: "9999px"
                    }
                }
            }
        };
    </script>
    <style>
        body { font-family: 'Public Sans', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark min-h-screen text-slate-900 dark:text-slate-100">
    <!-- Top Navbar -->
    <nav class="sticky top-0 z-50 w-full bg-white/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-16 flex items-center justify-between gap-4">
                <a href="<?php echo site_url('pages/index.php'); ?>" class="flex items-center gap-3 flex-shrink-0">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined text-2xl">auto_stories</span>
                    </div>
                    <span class="hidden md:block text-xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                        Blog<span class="text-primary">Fusion</span>
                    </span>
                </a>

                <form class="flex-1 max-w-xl hidden md:block" method="get" action="<?php echo site_url('pages/index.php'); ?>">
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                        <input
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm"
                            type="text"
                            name="q"
                            value="<?php echo escape_html($searchQuery); ?>"
                            placeholder="Search articles, topics..." />
                    </div>
                </form>

                <div class="flex items-center gap-2 sm:gap-4">
                    <div class="hidden lg:flex items-center gap-6 text-sm font-medium">
                        <a class="text-primary border-b-2 border-primary pb-0.5" href="<?php echo site_url('pages/index.php'); ?>">Home</a>
                        <a class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors" href="<?php echo site_url('pages/index.php'); ?>#latest">Latest</a>
                        <a class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors" href="<?php echo site_url('pages/index.php'); ?>#categories">Categories</a>
                        <a class="text-slate-600 dark:text-slate-400 hover:text-primary transition-colors" href="<?php echo site_url('pages/index.php'); ?>#trending">Trending</a>
                    </div>

                    <button class="md:hidden p-2 text-slate-600 dark:text-slate-400" type="button" aria-label="Search">
                        <span class="material-symbols-outlined">search</span>
                    </button>

                    <a href="<?php echo site_url('pages/index.php'); ?>" class="flex items-center gap-3 pl-2">
                        <div class="w-9 h-9 rounded-full overflow-hidden bg-slate-200 dark:bg-slate-700">
                            <img src="<?php echo escape_html($profileImage); ?>" alt="<?php echo escape_html($profileName); ?>" class="w-full h-full object-cover" />
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo escape_html($profileName); ?></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 capitalize"><?php echo escape_html($currentUser['role'] ?? 'user'); ?></p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </nav>


    <?php inject_project_toast(); ?>

    <main>
        <!-- Hero Section -->
        <section class="relative py-10 lg:py-16 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative rounded-3xl overflow-hidden bg-slate-900 shadow-2xl shadow-primary/10">
                    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_50%_50%,_var(--tw-gradient-stops))] from-primary via-secondary to-accent"></div>

                    <?php if ($heroPost): ?>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center relative z-10 p-6 md:p-10 lg:p-14">
                            <div class="order-2 lg:order-1 max-w-2xl">
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase bg-primary/20 text-primary mb-4">
                                    <?php echo escape_html($heroPost['category_name'] ?? 'Featured'); ?>
                                </span>
                                <h1 class="text-4xl md:text-6xl font-black text-white leading-tight tracking-tight mb-5">
                                    Welcome to <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-secondary to-accent"><?php echo escape_html($siteName); ?></span>
                                </h1>
                                <p class="text-lg text-slate-300 leading-relaxed max-w-xl">
                                    <?php echo escape_html($heroPost['description'] ?: excerpt_text($heroPost['content'], 28)); ?>
                                </p>

                                <div class="flex flex-wrap items-center gap-3 mt-6 text-sm text-slate-300">
                                    <span class="inline-flex items-center gap-2"><span class="material-symbols-outlined text-sm">person</span><?php echo escape_html($heroPost['author_name'] ?? 'Admin'); ?></span>
                                    <span class="inline-flex items-center gap-2"><span class="material-symbols-outlined text-sm">schedule</span><?php echo (int) reading_time($heroPost['content']); ?> min read</span>
                                    <span class="inline-flex items-center gap-2"><span class="material-symbols-outlined text-sm">calendar_month</span><?php echo date('d M Y', strtotime($heroPost['created_at'])); ?></span>
                                </div>

                                <div class="flex flex-wrap gap-4 mt-8">
                                    <a href="<?php echo site_url('pages/single-post.php?slug=' . urlencode($heroPost['slug'] ?? '')); ?>" class="h-12 px-8 rounded-xl bg-primary text-white font-bold hover:scale-105 transition-transform shadow-xl shadow-primary/30 flex items-center gap-2">
                                        Explore Posts <span class="material-symbols-outlined">trending_flat</span>
                                    </a>
                                    <a href="<?php echo site_url('pages/index.php'); ?>#categories" class="h-12 px-8 rounded-xl bg-white/10 text-white font-bold backdrop-blur-sm border border-white/20 hover:bg-white/20 transition-all flex items-center gap-2">
                                        Browse Categories <span class="material-symbols-outlined">category</span>
                                    </a>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-8">
                                    <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-4">
                                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Published</p>
                                        <p class="text-2xl font-black text-white mt-1"><?php echo $publishedCount; ?></p>
                                    </div>
                                    <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-4">
                                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Categories</p>
                                        <p class="text-2xl font-black text-white mt-1"><?php echo $totalCategories; ?></p>
                                    </div>
                                    <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-4">
                                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Reactions</p>
                                        <p class="text-2xl font-black text-white mt-1"><?php echo $totalReactions; ?></p>
                                    </div>
                                    <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-4">
                                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Comments</p>
                                        <p class="text-2xl font-black text-white mt-1"><?php echo $totalComments; ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="order-1 lg:order-2 relative">
                                <div class="absolute inset-0 bg-gradient-to-tr from-primary/30 via-secondary/20 to-accent/20 blur-3xl"></div>
                                <div class="relative rounded-3xl overflow-hidden shadow-2xl">
                                    <img
                                        src="<?php echo escape_html(normalize_image($heroPost['image'] ?? '')); ?>"
                                        alt="<?php echo escape_html($heroPost['title']); ?>"
                                        class="w-full h-[340px] md:h-[420px] object-cover" />
                                </div>
                                <div class="absolute bottom-5 left-5 right-5 rounded-2xl bg-white/15 backdrop-blur-md border border-white/15 p-4 text-white">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-200 mb-1">Featured Story</p>
                                    <p class="font-bold text-lg leading-snug"><?php echo escape_html($heroPost['title']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="p-10 md:p-16 text-center text-white relative z-10">
                            <h1 class="text-4xl md:text-6xl font-black leading-tight">Welcome to <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-secondary to-accent"><?php echo escape_html($siteName); ?></span></h1>
                            <p class="text-slate-300 mt-4">No published posts found yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Latest Posts -->
        <section id="latest" class="py-12 bg-white dark:bg-slate-900/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between gap-4 mb-10">
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2 tracking-tight">
                            <?php echo $activeLabel; ?>
                        </h2>
                        <div class="h-1.5 w-20 bg-primary rounded-full"></div>
                    </div>
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        <?php echo count($latestPosts); ?> post(s) shown
                    </div>
                </div>

                <?php if ($searchQuery !== '' || $categorySlug !== ''): ?>
                    <div class="mb-8 flex flex-wrap items-center gap-3">
                        <?php if ($searchQuery !== ''): ?>
                            <span class="px-4 py-2 rounded-full bg-primary/10 text-primary text-sm font-semibold">
                                Search: <?php echo escape_html($searchQuery); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($categorySlug !== ''): ?>
                            <span class="px-4 py-2 rounded-full bg-secondary/10 text-secondary text-sm font-semibold">
                                Category: <?php echo escape_html($categorySlug); ?>
                            </span>
                        <?php endif; ?>
                        <a href="<?php echo site_url('pages/index.php'); ?>" class="text-sm font-bold text-slate-500 hover:text-primary transition-colors">Clear filters</a>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php if (!empty($latestPosts)): ?>
                        <?php foreach ($latestPosts as $post): ?>
                            <?php
                                $postLink = site_url('pages/single-post.php?slug=' . urlencode($post['slug'] ?? ''));
                                $postImage = normalize_image($post['image'] ?? '');
                                $excerpt = $post['description'] ?: excerpt_text($post['content'], 24);
                                $tag = $post['category_name'] ?? 'Blog';
                            ?>
                            <article class="group flex flex-col bg-background-light dark:bg-slate-800 rounded-2xl overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all">
                                <a href="<?php echo escape_html($postLink); ?>" class="relative aspect-video overflow-hidden block">
                                    <img class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-500" src="<?php echo escape_html($postImage); ?>" alt="<?php echo escape_html($post['title']); ?>" />
                                    <div class="absolute top-4 left-4">
                                        <span class="px-3 py-1 bg-primary text-white text-xs font-bold rounded-lg uppercase">
                                            <?php echo escape_html($tag); ?>
                                        </span>
                                    </div>
                                </a>
                                <div class="p-6 flex-1 flex flex-col">
                                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 mb-3">
                                        <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-sm">person</span><?php echo escape_html($post['author_name'] ?? 'Admin'); ?></span>
                                        <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span><?php echo (int) reading_time($post['content']); ?> min</span>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-900 dark:text-white group-hover:text-primary transition-colors mb-3 line-clamp-2">
                                        <a href="<?php echo escape_html($postLink); ?>"><?php echo escape_html($post['title']); ?></a>
                                    </h3>
                                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-6 line-clamp-3 mb-5">
                                        <?php echo escape_html($excerpt); ?>
                                    </p>
                                    <div class="mt-auto flex items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-400">
                                        <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-sm">calendar_month</span><?php echo date('d M Y', strtotime($post['created_at'])); ?></span>
                                        <a class="font-bold text-primary hover:underline inline-flex items-center gap-1" href="<?php echo escape_html($postLink); ?>">
                                            Read More <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full p-10 rounded-3xl bg-slate-50 dark:bg-slate-800 text-center">
                            <p class="text-lg font-semibold text-slate-700 dark:text-slate-200">No posts found.</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Try another category or search keyword.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section id="categories" class="py-16 bg-slate-50 dark:bg-slate-950/70">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between gap-4 mb-10">
                    <div>
                        <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Popular Categories</h2>
                        <p class="text-slate-500 dark:text-slate-400 mt-2">Find the content that matters most to your journey.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach (array_slice($categoryStats, 0, 8) as $category): ?>
                        <a href="<?php echo site_url('pages/index.php?category=' . urlencode($category['slug'])); ?>"
                           class="group relative flex flex-col items-center justify-center p-8 rounded-2xl bg-white dark:bg-slate-800 shadow-lg border border-slate-100 dark:border-slate-700 hover:border-primary transition-all overflow-hidden">
                            <div class="absolute inset-0 bg-primary/5 group-hover:bg-primary/10 transition-colors"></div>
                            <span class="material-symbols-outlined text-4xl text-primary mb-4 relative z-10">category</span>
                            <h4 class="text-lg font-bold text-slate-900 dark:text-white relative z-10 text-center">
                                <?php echo escape_html($category['name']); ?>
                            </h4>
                            <p class="text-slate-500 dark:text-slate-400 text-xs relative z-10"><?php echo (int)$category['post_count']; ?> Articles</p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Trending Section -->
        <section id="trending" class="py-16 bg-slate-900 dark:bg-slate-950 text-white overflow-hidden relative">
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-96 h-96 bg-primary/20 blur-[100px] rounded-full"></div>
            <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-96 h-96 bg-accent/10 blur-[100px] rounded-full"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                    <div class="flex items-center gap-4">
                        <span class="material-symbols-outlined text-4xl text-accent">trending_up</span>
                        <h2 class="text-3xl font-black tracking-tight">Trending Now</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <?php foreach (array_slice($trendingPosts, 0, 4) as $post): ?>
                        <?php
                            $postLink = site_url('pages/single-post.php?slug=' . urlencode($post['slug'] ?? ''));
                            $postImage = normalize_image($post['image'] ?? '');
                            $excerpt = $post['description'] ?: excerpt_text($post['content'], 22);
                        ?>
                        <a href="<?php echo escape_html($postLink); ?>" class="group flex flex-col sm:flex-row gap-6 p-4 rounded-3xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all">
                            <div class="w-full sm:w-48 h-48 rounded-2xl overflow-hidden shrink-0">
                                <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="<?php echo escape_html($postImage); ?>" alt="<?php echo escape_html($post['title']); ?>" />
                            </div>
                            <div class="flex flex-col justify-center py-2">
                                <div class="flex items-center gap-2 text-accent text-xs font-bold uppercase mb-2">
                                    <span class="material-symbols-outlined text-xs">verified</span> <?php echo escape_html($post['category_name'] ?? 'Featured'); ?>
                                </div>
                                <h3 class="text-xl font-bold mb-3 group-hover:text-primary transition-colors"><?php echo escape_html($post['title']); ?></h3>
                                <p class="text-slate-400 text-sm mb-4 line-clamp-2"><?php echo escape_html($excerpt); ?></p>
                                <div class="flex items-center gap-4 text-slate-500 text-xs">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">favorite</span><?php echo (int)$post['reaction_count']; ?> reactions</span>
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">comment</span><?php echo (int)$post['comment_count']; ?> comments</span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-background-dark border-t border-slate-200 dark:border-slate-800 pt-16 pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <div class="flex flex-col gap-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white">
                            <span class="material-symbols-outlined">auto_awesome</span>
                        </div>
                        <span class="text-xl font-black tracking-tight text-slate-900 dark:text-white uppercase">Blog<span class="text-primary">Fusion</span></span>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                        <?php echo escape_html($siteDescription); ?>
                    </p>
                </div>

                <div>
                    <h5 class="text-slate-900 dark:text-white font-bold mb-6">Quick Links</h5>
                    <ul class="flex flex-col gap-4 text-sm font-medium text-slate-500 dark:text-slate-400">
                        <li><a class="hover:text-primary transition-colors" href="<?php echo site_url('pages/index.php'); ?>">Home</a></li>
                        <li><a class="hover:text-primary transition-colors" href="<?php echo site_url('pages/index.php'); ?>#latest">Latest Posts</a></li>
                        <li><a class="hover:text-primary transition-colors" href="<?php echo site_url('pages/index.php'); ?>#categories">Categories</a></li>
                        <li><a class="hover:text-primary transition-colors" href="<?php echo site_url('pages/index.php'); ?>#trending">Trending</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-slate-900 dark:text-white font-bold mb-6">Popular Topics</h5>
                    <ul class="flex flex-col gap-4 text-sm font-medium text-slate-500 dark:text-slate-400">
                        <?php foreach (array_slice($categoryStats, 0, 5) as $category): ?>
                            <li><a class="hover:text-primary transition-colors" href="<?php echo site_url('pages/index.php?category=' . urlencode($category['slug'])); ?>"><?php echo escape_html($category['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div>
                    <h5 class="text-slate-900 dark:text-white font-bold mb-6">Weekly Insights</h5>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Get the latest articles and hand-picked resources delivered to your inbox.</p>
                    <form class="flex flex-col gap-3">
                        <input class="h-11 rounded-xl border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 text-sm focus:ring-2 focus:ring-primary/50" placeholder="Email address" type="email" />
                        <button class="h-11 rounded-xl bg-primary text-white font-bold hover:bg-primary/90 transition-all" type="submit">Subscribe Now</button>
                    </form>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-slate-500 dark:text-slate-400 text-xs">© <?php echo date('Y'); ?> <?php echo escape_html($siteName); ?>. All rights reserved.</p>
                <div class="flex gap-6 text-xs text-slate-500 dark:text-slate-400">
                    <a class="hover:text-primary" href="#">Privacy Policy</a>
                    <a class="hover:text-primary" href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

<?php include "../include/session.php";
requireAuthor();
include "../include/db.php";
include "../include/functions.php";
include "../include/author_nav_sidebar.php";
include "../include/pagination.php";
//include "../include/data_fetch.php";

$table = 'posts';
$limit = 6;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;
$offset = ($page - 1) * $limit;

// Search/filter support
$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, trim($_GET['q'])) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, trim($_GET['status'])) : '';
$category_filter = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

$where = "WHERE author_id={$_SESSION['user_id']}";
if ($search) {
    $where .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
}
if ($status_filter && $status_filter !== 'all') {
    $where .= " AND status='$status_filter'";
}
if ($category_filter > 0) {
    $where .= " AND category_id=$category_filter";
}

// Fetch categories for filter dropdown
$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_query);
$categories = $categories_result ? mysqli_fetch_all($categories_result, MYSQLI_ASSOC) : [];


$query = "SELECT * FROM $table $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
$data = mysqli_num_rows($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$total_query = "SELECT COUNT(*) as total FROM $table $where";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];

$pagination = paginate($data, $total_records, $page, $offset, $limit);
$data24 = $pagination['data'];
$total_pages = $pagination['total_pages'];
$page = $pagination['current_page'];
$limit = $pagination['limit'];
$total_records = $pagination['total_records'];
$offset = $pagination['offset'];

function shortText($text, $limit = 80)
{
    $text = strip_tags($text);
    if (strlen($text) <= $limit)
        return $text;
    $text = substr($text, 0, $limit);
    $text = substr($text, 0, strrpos($text, ' '));
    return $text . "...";
}

function getCategoryColorClass($cat_name)
{
    $name = strtolower(trim($cat_name));
    if (strpos($name, 'tech') !== false || strpos($name, 'programming') !== false || strpos($name, 'web') !== false || strpos($name, 'ai') !== false || strpos($name, 'intelligence') !== false) {
        return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
    }
    if (strpos($name, 'food') !== false || strpos($name, 'recipe') !== false || strpos($name, 'cooking') !== false) {
        return 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300';
    }
    if (strpos($name, 'startup') !== false || strpos($name, 'business') !== false || strpos($name, 'finance') !== false || strpos($name, 'marketing') !== false) {
        return 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300';
    }
    if (strpos($name, 'health') !== false || strpos($name, 'fit') !== false || strpos($name, 'lifestyle') !== false) {
        return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
    }
    if (strpos($name, 'travel') !== false || strpos($name, 'tour') !== false || strpos($name, 'adventure') !== false) {
        return 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300';
    }
    // Default fallback
    return 'bg-secondary-fixed text-on-secondary-fixed-variant';
}

$has_active_filters = ($status_filter && $status_filter !== 'all') || ($category_filter > 0);

// ---- AJAX INSTANT SEARCH & FILTER ----
if (isset($_GET['ajax'])) {
    if (empty($data24)) {
        ?>
        <div class="py-20 flex flex-col items-center text-center px-6">
            <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-4xl text-on-surface-variant opacity-40">article</span>
            </div>
            <h3 class="text-lg font-bold text-on-surface mb-2">
                <?= ($search || $has_active_filters) ? 'No posts found matching the criteria' : 'No posts yet' ?>
            </h3>
            <p class="text-sm text-on-surface-variant mb-6">
                <?= ($search || $has_active_filters) ? 'Try adjusting your search query or filters.' : 'Create your first post to get started.' ?>
            </p>
            <?php if (!$search && !$has_active_filters): ?>
                <a href="edit-post.php"
                    class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-lg">add</span> Create Post
                </a>
            <?php endif; ?>
        </div>
        <?php
    } else {
        ?>
        <div class="overflow-x-auto">
            <table class="min-w-[640px] w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low/60 border-b border-outline-variant/10">
                        <th class="px-4 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant opacity-70">Post Overview</th>
                        <th class="px-4 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant opacity-70 hidden sm:table-cell">Category</th>
                        <th class="px-4 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant opacity-70 hidden md:table-cell">Date</th>
                        <th class="px-4 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant opacity-70">Status</th>
                        <th class="px-4 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant opacity-70 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-low/80">
                    <?php foreach ($data24 as $key => $row):
                        $cat = data($conn, 'categories', $row['category_id']);
                        ?>
                        <tr class="hover:bg-surface-container-low/50 transition-colors group" data-post-id="<?= $row['id'] ?>">
                            <td class="px-4 sm:px-6 py-4">
                                <div class="flex items-start gap-3">
                                    <?php if ($row['image']): ?>
                                        <img class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl object-cover shadow-sm shrink-0 bg-surface-container" src="../<?= htmlspecialchars($row['image']) ?>" onerror="this.src='https://placehold.co/48x48/e8dfee/630ed4?text=P'" alt="post" />
                                    <?php else: ?>
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-primary text-xl">article</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <p class="font-bold text-sm sm:text-[15px] text-on-surface group-hover:text-primary transition-colors line-clamp-1"><?= htmlspecialchars($row['title']) ?></p>
                                        <p class="text-[11px] text-on-surface-variant mt-0.5 line-clamp-1 hidden sm:block"><?= htmlspecialchars(shortText($row['content'])) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 hidden sm:table-cell">
                                <?php
                                $cat_name = $cat['name'] ?? 'Uncategorised';
                                $color_class = getCategoryColorClass($cat_name);
                                ?>
                                <span class="<?= $color_class ?> px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap"><?= htmlspecialchars($cat_name) ?></span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 hidden md:table-cell">
                                <p class="text-xs font-medium text-on-surface-variant whitespace-nowrap"><?= date("M d, Y", strtotime($row['created_at'])) ?></p>
                            </td>
                            <td class="px-4 sm:px-6 py-4">
                                <select onchange="changeStatus(<?= $row['id'] ?>, this.value, this)" 
                                        class="bg-surface-container border border-outline-variant/30 rounded-xl px-2.5 py-1.5 text-xs font-bold transition-all focus:ring-2 focus:ring-primary/20 focus:border-primary/30 outline-none <?= $row['status'] === 'published' ? 'text-green-600' : 'text-tertiary' ?>">
                                    <option value="published" <?= $row['status'] === 'published' ? 'selected' : '' ?> class="text-green-600 font-bold bg-surface">Published</option>
                                    <option value="draft" <?= $row['status'] === 'draft' ? 'selected' : '' ?> class="text-tertiary font-bold bg-surface">Draft</option>
                                </select>
                            </td>
                            <td class="px-4 sm:px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="edit-post.php?id=<?= $row['id'] ?>" title="Edit post" class="p-1.5 md:p-2 rounded-lg text-on-surface-variant hover:bg-primary-fixed hover:text-primary transition-all"><span class="material-symbols-outlined text-xl">edit</span></a>
                                    <button onclick="openDeleteModal(<?= $row['id'] ?>, '<?= addslashes(htmlspecialchars($row['title'])) ?>')" title="Delete post" class="p-1.5 md:p-2 rounded-lg text-on-surface-variant hover:bg-error-container hover:text-error transition-all"><span class="material-symbols-outlined text-xl">delete</span></button>
                                    <a href="../redirect-post.php?id=<?= $row['id'] ?>" target="_blank" title="View post" class="p-1.5 md:p-2 rounded-lg text-on-surface-variant hover:bg-surface-container-highest transition-all"><span class="material-symbols-outlined text-xl">visibility</span></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php pagination_links($total_pages, $limit, $total_records, $offset, 'posts'); ?>
        <?php
    }
    exit;
}

$sql258 = "SELECT COUNT(*) as total_posts, SUM(status='published') as total_published, SUM(status='draft') as total_draft FROM posts WHERE author_id={$_SESSION['user_id']};";
$deta = mysqli_fetch_assoc(mysqli_query($conn, $sql258));

// Feedback messages
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>My Posts - Luminous Editor</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-tint": "#732ee4", "secondary-fixed": "#e2dfff",
                        "on-primary-fixed": "#25005a", "on-background": "#1d1a24",
                        "surface": "#fef7ff", "on-primary": "#ffffff",
                        "tertiary-fixed": "#ffd9e4", "tertiary-container": "#bf2076",
                        "on-tertiary-fixed-variant": "#8c0053", "on-tertiary-fixed": "#3e0022",
                        "outline-variant": "#ccc3d8", "surface-variant": "#e8dfee",
                        "surface-container-highest": "#e8dfee", "on-error": "#ffffff",
                        "on-surface": "#1d1a24", "error-container": "#ffdad6",
                        "primary-fixed": "#eaddff", "tertiary-fixed-dim": "#ffb0cd",
                        "secondary": "#4b41e1", "primary-fixed-dim": "#d2bbff",
                        "inverse-on-surface": "#f6eefc", "surface-container": "#f3ebfa",
                        "primary": "#630ed4", "on-secondary": "#ffffff",
                        "on-error-container": "#93000a", "on-tertiary": "#ffffff",
                        "secondary-container": "#645efb", "on-secondary-fixed-variant": "#3323cc",
                        "primary-container": "#7c3aed", "on-secondary-fixed": "#0f0069",
                        "on-tertiary-container": "#ffdde7", "on-secondary-container": "#fffbff",
                        "outline": "#7b7487", "surface-dim": "#dfd7e6",
                        "surface-container-high": "#ede5f4", "surface-container-lowest": "#ffffff",
                        "surface-bright": "#fef7ff", "background": "#fef7ff",
                        "error": "#ba1a1a", "on-primary-fixed-variant": "#5a00c6",
                        "inverse-surface": "#332f39", "tertiary": "#9b005c",
                        "on-primary-container": "#ede0ff", "inverse-primary": "#d2bbff",
                        "secondary-fixed-dim": "#c3c0ff", "surface-container-low": "#f9f1ff",
                        "on-surface-variant": "#4a4455"
                    },
                    "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                    "fontFamily": { "headline": ["Public Sans"], "body": ["Public Sans"], "label": ["Public Sans"] }
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #e8dfee;
            border-radius: 10px;
        }

        /* Delete confirm modal */
        #deleteModal {
            transition: opacity 0.2s ease;
        }

        #deleteModal.hidden {
            display: none;
        }

        /* Status toggle badge */
        .status-badge {
            cursor: pointer;
            transition: all 0.2s;
        }

        .status-badge:hover {
            filter: brightness(0.9);
        }
    </style>
</head>

<body class="bg-surface text-on-surface min-h-screen">



    <!-- ── Delete Confirmation Modal ── -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-3xl shadow-2xl w-full max-w-sm p-8 text-center">
            <div class="w-16 h-16 bg-error-container rounded-full flex items-center justify-center mx-auto mb-5">
                <span class="material-symbols-outlined text-3xl text-error">delete_forever</span>
            </div>
            <h3 class="text-xl font-black text-on-surface mb-2">Delete Post?</h3>
            <p class="text-sm text-on-surface-variant mb-8">This action cannot be undone. The post will be permanently
                removed from your portfolio.</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()"
                    class="flex-1 py-3 rounded-xl bg-surface-container font-bold text-on-surface-variant hover:bg-surface-container-high transition-colors text-sm">
                    Cancel
                </button>
                <button id="deleteConfirmBtn" onclick="executeDelete()"
                    class="flex-1 py-3 rounded-xl bg-error text-on-error font-bold hover:opacity-90 transition-opacity text-sm flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-base">delete</span> Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-black/40 z-40 md:hidden hidden transition-opacity" id="sidebar-overlay"
        onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <?= author_slidebar('my-post') ?>

    <!-- Main Content Wrapper -->
    <div class="min-h-screen flex flex-col md:ml-64 transition-all duration-300">
        <?= author_navbar(); ?>

        <main class="flex-1 mt-20 px-4 sm:px-6 md:px-8 py-6 md:py-10">

            <!-- ── Feedback Banner ── -->
            <?php if ($msg === 'posted'): ?>
                <div
                    class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-2xl px-5 py-4 flex items-center gap-3 text-sm font-semibold">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                    Post published successfully!
                    <button onclick="this.parentElement.remove()" class="ml-auto text-green-600"><span
                            class="material-symbols-outlined text-base">close</span></button>
                </div>
            <?php elseif ($msg === 'deleted'): ?>
                <div
                    class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 flex items-center gap-3 text-sm font-semibold">
                    <span class="material-symbols-outlined text-red-600">delete</span>
                    Post deleted successfully.
                    <button onclick="this.parentElement.remove()" class="ml-auto text-red-600"><span
                            class="material-symbols-outlined text-base">close</span></button>
                </div>
            <?php elseif ($msg === 'status_updated'): ?>
                <div
                    class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 rounded-2xl px-5 py-4 flex items-center gap-3 text-sm font-semibold">
                    <span class="material-symbols-outlined text-blue-600">sync</span>
                    Post status updated successfully.
                    <button onclick="this.parentElement.remove()" class="ml-auto text-blue-600"><span
                            class="material-symbols-outlined text-base">close</span></button>
                </div>
            <?php endif; ?>

            <!-- ── Page Header ── -->
            <div class="mb-8 md:mb-10 flex flex-col lg:flex-row lg:items-end justify-between gap-5">
                <div>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight text-on-surface mb-2">My Posts
                    </h2>
                    <p class="text-on-surface-variant max-w-md text-sm sm:text-base">Manage your editorial portfolio.
                        Edit, organize, and monitor your published content.</p>
                </div>
                <a href="edit-post.php"
                    class="w-full sm:w-auto flex items-center justify-center gap-2 bg-gradient-to-br from-primary to-primary-container text-on-primary px-5 md:px-6 py-3 rounded-xl font-bold shadow-lg shadow-primary/20 transition-all hover:shadow-xl hover:scale-[1.02] active:scale-95 text-sm md:text-base">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Create New Post
                </a>
            </div>

            <!-- ── Stats & Search Bar ── -->
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 md:gap-5 mb-8">
                <!-- Search — spans 2 cols on xl -->
                <form method="GET" action="" class="col-span-2 relative">
                    <span
                        class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">search</span>
                    
                    <input id="searchInput" name="q" value="<?= htmlspecialchars($search) ?>"
                        class="w-full bg-surface-container-low border-none rounded-xl py-3.5 pl-12 pr-20 text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none"
                        placeholder="Search posts by title or keyword..." type="text" />
                    
                    <?php
                    // Build a URL to clear the search query but retain other filter parameters
                    $clear_search_params = $_GET;
                    unset($clear_search_params['q']);
                    $clear_search_url = "my-post.php?" . http_build_query($clear_search_params);
                    ?>
                    
                    <?php if ($search): ?>
                        <a href="<?= htmlspecialchars($clear_search_url) ?>"
                            class="absolute right-12 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-error transition-colors"
                            title="Clear search">
                            <span class="material-symbols-outlined text-lg">close</span>
                        </a>
                    <?php endif; ?>

                    <button type="button" onclick="toggleFilterPanel()"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors animate-fade-in"
                        title="Toggle filters">
                        <span class="material-symbols-outlined text-lg">tune</span>
                    </button>

                    <!-- Collapsible Filter Panel -->
                    <?php
                    $has_active_filters = ($status_filter && $status_filter !== 'all') || ($category_filter > 0);
                    ?>
                    <div id="filterPanel" class="hidden absolute top-full left-0 w-full bg-surface-container-low p-5 rounded-2xl border border-outline-variant/15 mt-2 shadow-xl z-20 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Status Filter -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant opacity-70 mb-2">Status</label>
                                <select name="status" class="w-full bg-surface-container-lowest border border-outline-variant/10 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none py-2 px-3">
                                    <option value="all" <?= $status_filter === 'all' || !$status_filter ? 'selected' : '' ?>>All Statuses</option>
                                    <option value="published" <?= $status_filter === 'published' ? 'selected' : '' ?>>Published</option>
                                    <option value="draft" <?= $status_filter === 'draft' ? 'selected' : '' ?>>Draft</option>
                                </select>
                            </div>
                            <!-- Category Filter -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant opacity-70 mb-2">Category</label>
                                <select name="category_id" class="w-full bg-surface-container-lowest border border-outline-variant/10 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none py-2 px-3">
                                    <option value="0" <?= $category_filter === 0 ? 'selected' : '' ?>>All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $category_filter === (int)$cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 border-t border-outline-variant/10 pt-3">
                            <a href="my-post.php" class="px-4 py-2 bg-surface-container-high hover:bg-surface-variant text-on-surface-variant text-xs font-bold rounded-xl transition-all">Clear All</a>
                            <button type="submit" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl hover:opacity-90 transition-all shadow-md shadow-primary/20">Apply Filters</button>
                        </div>
                    </div>
                </form>
                <!-- Total Posts -->
                <div
                    class="bg-surface-container-low rounded-xl p-4 flex items-center justify-between border border-outline-variant/10">
                    <div>
                        <p
                            class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-on-surface-variant opacity-60">
                            Total</p>
                        <p class="text-xl sm:text-2xl font-bold text-on-surface"><?= $deta['total_posts'] ?></p>
                    </div>
                    <div class="p-2 bg-primary/10 rounded-lg text-primary shrink-0">
                        <span class="material-symbols-outlined">article</span>
                    </div>
                </div>
                <!-- Published -->
                <div
                    class="bg-surface-container-low rounded-xl p-4 flex items-center justify-between border border-outline-variant/10">
                    <div>
                        <p
                            class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-on-surface-variant opacity-60">
                            Published</p>
                        <p class="text-xl sm:text-2xl font-bold text-on-surface"><?= $deta['total_published'] ?></p>
                    </div>
                    <div class="p-2 bg-green-100 rounded-lg text-green-600 shrink-0">
                        <span class="material-symbols-outlined">check_circle</span>
                    </div>
                </div>
            </div>

            <!-- ── Posts Table ── -->
            <div id="postsTableContainer"
                class="bg-surface-container-lowest rounded-2xl md:rounded-3xl shadow-sm border border-outline-variant/5 overflow-hidden">

                <?php if (empty($data24)): ?>
                    <!-- Empty State -->
                    <div class="py-20 flex flex-col items-center text-center px-6">
                        <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mb-5">
                            <span
                                class="material-symbols-outlined text-4xl text-on-surface-variant opacity-40">article</span>
                        </div>
                        <h3 class="text-lg font-bold text-on-surface mb-2">
                            <?= ($search || $has_active_filters) ? 'No posts found matching the criteria' : 'No posts yet' ?>
                        </h3>
                        <p class="text-sm text-on-surface-variant mb-6">
                            <?= ($search || $has_active_filters) ? 'Try adjusting your search query or filters.' : 'Create your first post to get started.' ?>
                        </p>
                        <?php if (!$search): ?>
                            <a href="edit-post.php"
                                class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 transition-opacity">
                                <span class="material-symbols-outlined text-lg">add</span> Create Post
                            </a>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-[640px] w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-container-low/60 border-b border-outline-variant/10">
                                    <th
                                        class="px-4 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant opacity-70">
                                        Post Overview</th>
                                    <th
                                        class="px-4 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant opacity-70 hidden sm:table-cell">
                                        Category</th>
                                    <th
                                        class="px-4 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant opacity-70 hidden md:table-cell">
                                        Date</th>
                                    <th
                                        class="px-4 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant opacity-70">
                                        Status</th>
                                    <th
                                        class="px-4 sm:px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant opacity-70 text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-container-low/80">
                                <?php foreach ($data24 as $key => $row):
                                    $cat = data($conn, 'categories', $row['category_id']);
                                    ?>
                                    <tr class="hover:bg-surface-container-low/50 transition-colors group"
                                        data-post-id="<?= $row['id'] ?>">
                                        <!-- Post Overview -->
                                        <td class="px-4 sm:px-6 py-4">
                                            <div class="flex items-start gap-3">
                                                <?php if ($row['image']): ?>
                                                    <img class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl object-cover shadow-sm shrink-0 bg-surface-container"
                                                        src="../<?= htmlspecialchars($row['image']) ?>"
                                                        onerror="this.src='https://placehold.co/48x48/e8dfee/630ed4?text=P'"
                                                        alt="post" />
                                                <?php else: ?>
                                                    <div
                                                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                                                        <span class="material-symbols-outlined text-primary text-xl">article</span>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="min-w-0">
                                                    <p
                                                        class="font-bold text-sm sm:text-[15px] text-on-surface group-hover:text-primary transition-colors line-clamp-1">
                                                        <?= htmlspecialchars($row['title']) ?>
                                                    </p>
                                                    <p
                                                        class="text-[11px] text-on-surface-variant mt-0.5 line-clamp-1 hidden sm:block">
                                                        <?= htmlspecialchars(shortText($row['content'])) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <!-- Category -->
                                        <td class="px-4 sm:px-6 py-4 hidden sm:table-cell">
                                            <?php
                                            $cat_name = $cat['name'] ?? 'Uncategorised';
                                            $color_class = getCategoryColorClass($cat_name);
                                            ?>
                                            <span
                                                class="<?= $color_class ?> px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                                                <?= htmlspecialchars($cat_name) ?>
                                            </span>
                                        </td>
                                        <!-- Date -->
                                        <td class="px-4 sm:px-6 py-4 hidden md:table-cell">
                                            <p class="text-xs font-medium text-on-surface-variant whitespace-nowrap">
                                                <?= date("M d, Y", strtotime($row['created_at'])) ?>
                                            </p>
                                        </td>
                                        <!-- Status -->
                                        <td class="px-4 sm:px-6 py-4">
                                             <select onchange="changeStatus(<?= $row['id'] ?>, this.value, this)" 
                                                     class="bg-surface-container border border-outline-variant/30 rounded-xl px-2.5 py-1.5 text-xs font-bold transition-all focus:ring-2 focus:ring-primary/20 focus:border-primary/30 outline-none <?= $row['status'] === 'published' ? 'text-green-600' : 'text-tertiary' ?>">
                                                 <option value="published" <?= $row['status'] === 'published' ? 'selected' : '' ?> class="text-green-600 font-bold bg-surface">Published</option>
                                                 <option value="draft" <?= $row['status'] === 'draft' ? 'selected' : '' ?> class="text-tertiary font-bold bg-surface">Draft</option>
                                             </select>
                                         </td>
                                        <!-- Actions -->
                                        <td class="px-4 sm:px-6 py-4">
                                            <div class="flex items-center justify-end gap-1">
                                                <!-- Edit -->
                                                <a href="edit-post.php?id=<?= $row['id'] ?>" title="Edit post"
                                                    class="p-1.5 md:p-2 rounded-lg text-on-surface-variant hover:bg-primary-fixed hover:text-primary transition-all">
                                                    <span class="material-symbols-outlined text-xl">edit</span>
                                                </a>
                                                <!-- Delete -->
                                                <button
                                                    onclick="openDeleteModal(<?= $row['id'] ?>, '<?= addslashes(htmlspecialchars($row['title'])) ?>')"
                                                    title="Delete post"
                                                    class="p-1.5 md:p-2 rounded-lg text-on-surface-variant hover:bg-error-container hover:text-error transition-all">
                                                    <span class="material-symbols-outlined text-xl">delete</span>
                                                </button>
                                                <!-- View -->
                                                <a href="../redirect-post.php?id=<?= $row['id'] ?>" target="_blank" title="View post" class="p-1.5 md:p-2 rounded-lg text-on-surface-variant hover:bg-surface-container-highest transition-all"><span class="material-symbols-outlined text-xl">visibility</span></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php pagination_links($total_pages, $limit, $total_records, $offset, 'posts'); ?>
                <?php endif; ?>
            </div>
        </main>

        <footer class="px-4 py-6 md:p-8 text-center">
            <p class="text-xs text-on-surface-variant opacity-40 uppercase tracking-[0.2em]">Luminous Editor © 2024 •
                Powered by fusion chroma design system</p>
        </footer>
    </div>

    <script>
        /* ── Sidebar toggle ── */
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

        /* ── Delete modal ── */
        let pendingDeleteId = null;
        function openDeleteModal(postId, title) {
            pendingDeleteId = postId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            pendingDeleteId = null;
        }
        document.getElementById('deleteModal').addEventListener('click', function (e) {
            if (e.target === this) closeDeleteModal();
        });
        function executeDelete() {
            if (!pendingDeleteId) return;
            const id = pendingDeleteId;
            closeDeleteModal();

            fetch(`../actions/author_post_delete.php?id=${id}&_token=<?= $_SESSION['user_id'] ?>&ajax=1`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Post deleted successfully.', 'success');
                        } else {
                            alert(data.message || 'Post deleted successfully.');
                        }
                        
                        const row = document.querySelector(`tr[data-post-id="${id}"]`) || document.querySelector(`button[onclick*="openDeleteModal(${id},"]`).closest('tr');
                        if (row) {
                            row.style.transition = 'all 0.5s ease';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(-20px)';
                            setTimeout(() => {
                                row.remove();
                                const tableBody = document.querySelector('tbody');
                                if (tableBody && tableBody.children.length === 0) {
                                    window.location.reload();
                                }
                            }, 500);
                        } else {
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    } else {
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Failed to delete post.', 'error');
                        } else {
                            alert(data.message || 'Failed to delete post.');
                        }
                    }
                })
                .catch(() => {
                    if (typeof showToast === 'function') {
                        showToast('Network error occurred.', 'error');
                    } else {
                        alert('Network error occurred.');
                    }
                });
        }

        /* ── Change status (AJAX select dropdown) ── */
        function changeStatus(postId, newStatus, selectEl) {
            selectEl.disabled = true;
            selectEl.style.opacity = '0.5';

            fetch('../actions/toggle_post_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + postId + '&status=' + newStatus
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (newStatus === 'published') {
                            selectEl.classList.remove('text-tertiary');
                            selectEl.classList.add('text-green-600');
                        } else {
                            selectEl.classList.remove('text-green-600');
                            selectEl.classList.add('text-tertiary');
                        }
                        showToast('Status updated to ' + newStatus.charAt(0).toUpperCase() + newStatus.slice(1), 'info');
                    } else {
                        showToast(data.message || 'Failed to update status', 'error');
                        // revert
                        selectEl.value = newStatus === 'published' ? 'draft' : 'published';
                        if (selectEl.value === 'published') {
                            selectEl.classList.add('text-green-600');
                            selectEl.classList.remove('text-tertiary');
                        } else {
                            selectEl.classList.add('text-tertiary');
                            selectEl.classList.remove('text-green-600');
                        }
                    }
                    selectEl.disabled = false;
                    selectEl.style.opacity = '1';
                })
                .catch(() => {
                    showToast('Network error', 'error');
                    // revert
                    selectEl.value = newStatus === 'published' ? 'draft' : 'published';
                    if (selectEl.value === 'published') {
                        selectEl.classList.add('text-green-600');
                        selectEl.classList.remove('text-tertiary');
                    } else {
                        selectEl.classList.add('text-tertiary');
                        selectEl.classList.remove('text-green-600');
                    }
                    selectEl.disabled = false;
                    selectEl.style.opacity = '1';
                });
        }

        /* ── Auto-dismiss flash banners ── */
        document.querySelectorAll('[data-autohide]').forEach(el => {
            setTimeout(() => el.remove(), 5000);
        });

        /* ── Toggle filter panel ── */
        function toggleFilterPanel() {
            const panel = document.getElementById('filterPanel');
            if (panel) panel.classList.toggle('hidden');
        }

        let debounceTimer;

        // Fetch posts via AJAX
        function fetchPosts(page = 1) {
            const searchInput = document.getElementById('searchInput');
            const statusSelect = document.querySelector('select[name="status"]');
            const categorySelect = document.querySelector('select[name="category_id"]');
            
            const q = searchInput ? searchInput.value.trim() : '';
            const status = statusSelect ? statusSelect.value : 'all';
            const categoryId = categorySelect ? categorySelect.value : '0';
            
            const url = `my-post.php?ajax=1&page=${page}&q=${encodeURIComponent(q)}&status=${status}&category_id=${categoryId}`;
            
            fetch(url)
                .then(res => res.text())
                .then(html => {
                    const container = document.getElementById('postsTableContainer');
                    if (container) {
                        container.innerHTML = html;
                        attachPaginationListeners();
                    }
                })
                .catch(err => {
                    console.error('AJAX search error:', err);
                });
        }

        // Debounced search trigger
        function handleSearchInput() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchPosts(1);
            }, 250);
        }

        // Intercept standard pagination link clicks
        function attachPaginationListeners() {
            const container = document.getElementById('postsTableContainer');
            if (!container) return;
            
            const links = container.querySelectorAll('a[href*="page="]');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    const urlParams = new URLSearchParams(href.substring(href.indexOf('?')));
                    const page = urlParams.get('page') || 1;
                    fetchPosts(page);
                    
                    // Smoothly scroll back to the table top
                    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            <?php if ($has_active_filters): ?>
                const panel = document.getElementById('filterPanel');
                if (panel) panel.classList.remove('hidden');
            <?php endif; ?>

            const searchInput = document.getElementById('searchInput');
            const topbarSearch = document.getElementById('topbarSearchInput');
            
            // Sync search inputs and listen to typing events
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    if (topbarSearch) {
                        topbarSearch.value = e.target.value;
                    }
                    handleSearchInput();
                });
            }
            
            if (topbarSearch) {
                topbarSearch.addEventListener('input', (e) => {
                    if (searchInput) {
                        searchInput.value = e.target.value;
                    }
                    handleSearchInput();
                });
            }

            // Sync filter selections
            const statusSelect = document.querySelector('select[name="status"]');
            if (statusSelect) {
                statusSelect.addEventListener('change', () => fetchPosts(1));
            }
            
            const categorySelect = document.querySelector('select[name="category_id"]');
            if (categorySelect) {
                categorySelect.addEventListener('change', () => fetchPosts(1));
            }

            // Attach listeners to initial pagination
            attachPaginationListeners();
        });
    </script>
</body>

</html>
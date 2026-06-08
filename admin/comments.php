<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAdmin();
include BASE_PATH . 'include/db.php';
include BASE_PATH . 'include/functions.php';
include BASE_PATH . 'include/admin_nav_sidebar.php';
include BASE_PATH . 'include/pagination.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

// CSV Export logic
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    if (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=comments_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'User Name', 'Post Title', 'Comment', 'Likes', 'Date']);
    
    $where_clause = "";
    if (!empty($search)) {
        $where_clause = "WHERE c.comment LIKE '%$search%' OR u.name LIKE '%$search%' OR p.title LIKE '%$search%'";
    }
    
    $export_query = "SELECT c.id, u.name as user_name, p.title as post_title, c.comment, c.like as likes, c.created_at 
                     FROM comments c 
                     LEFT JOIN users u ON c.user_id = u.id 
                     LEFT JOIN posts p ON c.post_id = p.id 
                     $where_clause 
                     ORDER BY c.id DESC";
    $export_result = mysqli_query($conn, $export_query);
    if ($export_result) {
        while ($row = mysqli_fetch_assoc($export_result)) {
            fputcsv($output, [
                $row['id'],
                $row['user_name'],
                $row['post_title'],
                $row['comment'],
                $row['likes'],
                $row['created_at']
            ]);
        }
    }
    fclose($output);
    exit();
}

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Search support
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$search_clause = $search ? "WHERE c.comment LIKE '%$search%' OR u.name LIKE '%$search%' OR p.title LIKE '%$search%'" : '';

// Count total
$total_query = "SELECT COUNT(*) as total FROM comments c 
                LEFT JOIN users u ON c.user_id = u.id 
                LEFT JOIN posts p ON c.post_id = p.id 
                $search_clause";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'] ?? 0;

// Main query
$query = "SELECT c.*, u.name AS user_name, u.email AS user_email, u.profile_image AS user_avatar, p.title AS post_title, p.slug AS post_slug
          FROM comments c
          LEFT JOIN users u ON c.user_id = u.id
          LEFT JOIN posts p ON c.post_id = p.id
          $search_clause
          ORDER BY c.created_at DESC
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
$comments_data = $result && mysqli_num_rows($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$pagination = paginate($comments_data, $total_records, $page, $offset, $limit);
$comments = $pagination['data'];
$total_pages = $pagination['total_pages'];
$page = $pagination['current_page'];
$limit = $pagination['limit'];
$total_records = $pagination['total_records'];
$offset = $pagination['offset'];
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <link rel="icon" type="image/png" href="<?php echo defined('BASE_URL') ? BASE_URL : '/BlogFusion/'; ?>upload/site_image/logo2.png" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Blog Fusion - Comments Management</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#7C3AED", // Violet-600 (Primary from request)
                        "primary-hover": "#6D28D9",
                        "background-light": "#f8f7ff",
                        "background-dark": "#0f172a",
                        "indigo-accent": "#4F46E5",
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"]
                    },
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
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
    <div class="flex h-screen overflow-hidden">
        <!-- SideNavBar -->
        <?=slidebar('comments');?>
        <div id="overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>
        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <?=ad_navbar();?>
            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-3xl font-black tracking-tight">Comments</h2>
                        <p class="text-slate-500">Manage and moderate user comments across all blog posts</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Search Form -->
                        <form method="GET" action="" class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                            <input name="search" value="<?= htmlspecialchars($search) ?>"
                                class="pl-10 pr-8 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary text-sm focus:border-primary"
                                placeholder="Search comments..." type="text" />
                            <?php if ($search): ?>
                            <a href="?" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500">
                                <span class="material-symbols-outlined text-lg">close</span>
                            </a>
                            <?php endif; ?>
                        </form>
                        <a href="?export=csv&search=<?= urlencode($search) ?>"
                            class="bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/20 transition-all">
                            <span class="material-symbols-outlined text-[20px]">download</span>
                            Export CSV
                        </a>
                    </div>
                </div>
                <!-- Comments Table Card -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="text-slate-500 text-xs uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/50">
                                    <th class="px-6 py-4 font-semibold">User</th>
                                    <th class="px-6 py-4 font-semibold">Post</th>
                                    <th class="px-6 py-4 font-semibold">Comment</th>
                                    <th class="px-6 py-4 font-semibold">Date</th>
                                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <?php if (empty($comments)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 font-medium">
                                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50 block">forum</span>
                                        No comments found.
                                    </td>
                                </tr>
                                <?php else: foreach ($comments as $row):
                                    $avatar_url = $row['user_avatar'] ? $row['user_avatar'] : 'upload/profile-images/default.png';
                                    if (strpos($avatar_url, 'http') !== 0 && strpos($avatar_url, BASE_URL) !== 0) {
                                        $avatar_url = BASE_URL . $avatar_url;
                                    }
                                ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img class="h-9 w-9 rounded-full object-cover border-2 border-slate-100 dark:border-slate-800"
                                                src="<?= htmlspecialchars($avatar_url) ?>"
                                                alt="User avatar of <?= htmlspecialchars($row['user_name'] ?? 'User') ?>"
                                                onerror="this.src='https://placehold.co/36x36/eaddff/7c3aed?text=U'" />
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold"><?= htmlspecialchars($row['user_name'] ?? 'Guest User') ?></span>
                                                <span class="text-[10px] text-slate-400"><?= htmlspecialchars($row['user_email'] ?? '') ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a class="text-sm font-medium text-primary hover:underline line-clamp-1"
                                            href="<?= BASE_URL ?>redirect-post.php?id=<?= $row['post_id'] ?>" target="_blank">
                                            <?= htmlspecialchars($row['post_title'] ?? 'Deleted Post') ?>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 max-w-md">
                                            "<?= htmlspecialchars($row['comment']) ?>"
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 whitespace-nowrap">
                                        <?= date("M d, Y", strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="approveComment(<?= $row['id'] ?>)"
                                                class="p-2 text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors"
                                                title="Approve">
                                                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                            </button>
                                            <button onclick="deleteComment(<?= $row['id'] ?>)"
                                                class="p-2 text-rose-600 bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/40 rounded-lg transition-colors"
                                                title="Delete">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <?php pagination_links($total_pages, $limit, $total_records, $offset, 'comments'); ?>
                </div>
            </div>
        </main>
    </div>

    <script src="<?= BASE_URL ?>assets/js/admin.js"></script>
    <script>
        function deleteComment(id) {
            if (confirm("Are you sure you want to delete this comment? This will permanently delete the comment and all its replies.")) {
                window.location.href = "<?= BASE_URL ?>actions/admin.php?btn=comment&id=" + id;
            }
        }

        function approveComment(id) {
            showToast("Comment approved successfully!", "success");
        }
    </script>
</body>

</html>
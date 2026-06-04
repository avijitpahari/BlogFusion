<?php include "../include/session.php"; 
requireAdmin();
include "../include/db.php";
include "../include/functions.php";
include "../include/admin_nav_sidebar.php";
include "../include/pagination.php";

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, trim($_GET['status'])) : '';

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Construct SQL query
$where = [];
if (!empty($search)) {
    $where[] = "(posts.title LIKE '%$search%' OR posts.description LIKE '%$search%' OR posts.content LIKE '%$search%')";
}
if ($category_id > 0) {
    $where[] = "posts.category_id = $category_id";
}
if (!empty($status) && $status !== 'All Status') {
    $where[] = "posts.status = '$status'";
}

$where_clause = "";
if (count($where) > 0) {
    $where_clause = "WHERE " . implode(" AND ", $where);
}

// Fetch posts
$query = "SELECT posts.*, categories.name as category_name, users.name as author_name, users.profile_image as author_image 
          FROM posts 
          LEFT JOIN categories ON posts.category_id = categories.id 
          LEFT JOIN users ON posts.author_id = users.id 
          $where_clause 
          ORDER BY posts.id DESC 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
$posts = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

// Total count
$total_query = "SELECT COUNT(*) as total FROM posts $where_clause";
$total_result = mysqli_query($conn, $total_query);
$total_records = 0;
if ($total_result) {
    $total_row = mysqli_fetch_assoc($total_result);
    $total_records = (int)$total_row['total'];
}

// Paginate
$pagination = paginate($posts, $total_records, $page, $offset, $limit);
$posts_paginated = $pagination['data'];
$total_pages = $pagination['total_pages'];
$page = $pagination['current_page'];

// Fetch categories for dropdown
$cat_query = "SELECT * FROM categories ORDER BY name ASC";
$cat_result = mysqli_query($conn, $cat_query);
$categories = $cat_result ? mysqli_fetch_all($cat_result, MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Blog Fusion - Admin Posts</title>
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
                        "primary": "#7C3AED", // Violet-600
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
    <link rel="stylesheet" href="../assets/css/admin.css"> 
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">

    <!-- ── Delete Confirmation Modal ── -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-sm p-8 text-center border border-slate-200 dark:border-slate-800">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-5">
                <span class="material-symbols-outlined text-3xl text-red-600">delete_forever</span>
            </div>
            <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2">Delete Post?</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-8">This action cannot be undone. The post will be permanently removed.</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()"
                    class="flex-1 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold transition-colors text-sm">
                    Cancel
                </button>
                <button id="deleteConfirmBtn" onclick="executeDelete()"
                    class="flex-1 py-3 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition-colors text-sm flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-base">delete</span> Delete
                </button>
            </div>
        </div>
    </div>

    <div class="flex h-screen overflow-hidden">
        <!-- SideNavBar -->
        <?=slidebar('posts');?>
        <div id="overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>
        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <?=ad_navbar();?>
            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-8">
                <!-- Page Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-3xl font-black tracking-tight">Posts</h2>
                        <p class="text-slate-500">Manage and organize your blog content</p>
                    </div>
                    <a href="edit-post.php"
                        class="bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/20 transition-all sm:self-center self-start">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        Add Post
                    </a>
                </div>
                <!-- Filters -->
                <form method="GET" action="posts.php" class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-8">
                    <div class="flex flex-wrap items-center gap-6">
                        <!-- Search Box -->
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Search Posts</label>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search title, content..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none text-slate-900 dark:text-slate-100">
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Filter by Category</label>
                            <div class="relative">
                                <select name="category" onchange="this.form.submit()"
                                    class="w-full appearance-none bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-primary focus:border-primary text-slate-900 dark:text-slate-100">
                                    <option value="0">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= ($category_id == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                            </div>
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Status</label>
                            <div class="relative">
                                <select name="status" onchange="this.form.submit()"
                                    class="w-full appearance-none bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-primary focus:border-primary text-slate-900 dark:text-slate-100">
                                    <option value="">All Status</option>
                                    <option value="published" <?= ($status == 'published') ? 'selected' : '' ?>>Published</option>
                                    <option value="draft" <?= ($status == 'draft') ? 'selected' : '' ?>>Draft</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex gap-2 self-end">
                            <button type="submit" class="bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-xl font-bold transition-all shadow-md shadow-primary/10 text-sm">
                                Apply
                            </button>
                            <a href="posts.php" class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 px-5 py-2.5 rounded-xl font-bold transition-colors text-sm flex items-center justify-center">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
                <!-- Posts Table -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="text-slate-500 text-xs uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/50">
                                    <th class="px-6 py-4 font-semibold w-24 hidden sm:table-cell">Image</th>
                                    <th class="px-6 py-4 font-semibold">Title</th>
                                    <th class="px-6 py-4 font-semibold hidden md:table-cell">Category</th>
                                    <th class="px-6 py-4 font-semibold hidden lg:table-cell">Author</th>
                                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <?php if (empty($posts_paginated)): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                            No posts found matching the criteria.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($posts_paginated as $row): ?>
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                            <td class="px-6 py-4 hidden sm:table-cell">
                                                <div class="w-14 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                                    <?php if ($row['image']): ?>
                                                        <img alt="Post thumbnail" class="w-full h-full object-cover" src="../<?= htmlspecialchars($row['image']) ?>" onerror="this.src='https://placehold.co/56x40/e8dfee/630ed4?text=P'" />
                                                    <?php else: ?>
                                                        <div class="w-full h-full bg-primary/10 flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-primary text-sm">article</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <p class="font-bold text-sm line-clamp-1"><?= htmlspecialchars($row['title']) ?></p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <p class="text-xs text-slate-500">Updated <?= date("M d, Y", strtotime($row['created_at'])) ?></p>
                                                    <span class="inline-flex px-1.5 py-0.5 text-[9px] font-bold rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 uppercase"><?= htmlspecialchars($row['status']) ?></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 hidden md:table-cell">
                                                <span class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-primary/10 text-primary uppercase"><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?></span>
                                            </td>
                                            <td class="px-6 py-4 hidden lg:table-cell">
                                                <div class="flex items-center gap-2">
                                                    <img alt="Author" class="w-6 h-6 rounded-full object-cover" src="../<?= htmlspecialchars($row['author_image'] ?: 'upload/profile-images/default.png') ?>" onerror="this.src='../upload/profile-images/default.png'" />
                                                    <span class="text-sm font-medium"><?= htmlspecialchars($row['author_name'] ?? 'Admin') ?></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end gap-2">
                                                    <a href="<?= site_url("redirect-post.php?id=" . $row['id']) ?>" target="_blank"
                                                        class="p-2 text-slate-400 hover:text-indigo-accent hover:bg-indigo-600/10 rounded-lg transition-colors"
                                                        title="View">
                                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                                    </a>
                                                    <a href="edit-post.php?id=<?= $row['id'] ?>"
                                                        class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                        title="Edit">
                                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                                    </a>
                                                    <button onclick="deletePost(<?= $row['id'] ?>)"
                                                        class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors"
                                                        title="Delete">
                                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <?php pagination_links($total_pages, $limit, $total_records, $offset, 'posts'); ?>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/admin.js"></script>
    <script>
        let pendingDeleteId = null;

        function deletePost(id) {
            pendingDeleteId = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            pendingDeleteId = null;
        }

        function executeDelete() {
            if (!pendingDeleteId) return;
            const id = pendingDeleteId;
            closeDeleteModal();

            fetch(`../actions/admin.php?btn=post&id=${id}&ajax=1`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Post deleted successfully.', 'success');
                        } else {
                            alert(data.message || 'Post deleted successfully.');
                        }
                        // Find the table row and remove it with an animation
                        const deleteBtn = document.querySelector(`button[onclick="deletePost(${id})"]`);
                        const row = deleteBtn ? deleteBtn.closest('tr') : null;
                        if (row) {
                            row.style.transition = 'all 0.5s ease';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(-20px)';
                            setTimeout(() => {
                                row.remove();
                                // If table is empty, reload page to show empty state
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
    </script>
</body>

</html>
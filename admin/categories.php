<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAdmin();
include BASE_PATH . 'include/db.php';

include BASE_PATH . 'include/admin_nav_sidebar.php';
include BASE_PATH . 'include/pagination.php';
$id = $_SESSION['user_id'];
// logic data
$table = 'categories';
$limit = 6;
// current page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;

$offset = ($page - 1) * $limit;

// fetch data
$query = "SELECT * FROM $table LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
$data = mysqli_num_rows($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

// total count
$total_query = "SELECT COUNT(*) as total FROM $table";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];

// call function
$pagination = paginate($data, $total_records, $page, $offset, $limit);
$data24 = $pagination['data'];
$total_pages = $pagination['total_pages'];
$page = $pagination['current_page'];
$limit = $pagination['limit'];
$total_records = $pagination['total_records'];
$offset = $pagination['offset'];

$req_limit = 6;
$req_page = isset($_GET['req_page']) ? (int) $_GET['req_page'] : 1;
if ($req_page < 1)
    $req_page = 1;

$total_req_query = "SELECT COUNT(*) as total FROM category_requests";
$total_req_result = mysqli_query($conn, $total_req_query);
$total_req_row = mysqli_fetch_assoc($total_req_result);
$total_req_records = $total_req_row['total'] ?? 0;
$total_req_pages = ceil($total_req_records / $req_limit);
if ($total_req_pages < 1)
    $total_req_pages = 1;

if ($req_page > $total_req_pages)
    $req_page = $total_req_pages;
$req_offset = ($req_page - 1) * $req_limit;

$sql56 = "SELECT cr.*, u.name AS author_name, u.profile_image AS author_avatar 
          FROM category_requests cr 
          LEFT JOIN users u ON cr.author_id = u.id 
          ORDER BY cr.id DESC
          LIMIT $req_limit OFFSET $req_offset";
$run56 = mysqli_query($conn, $sql56);
$all_data = mysqli_num_rows($run56) ? mysqli_fetch_all($run56, MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <link rel="icon" type="image/png" href="<?php echo defined('BASE_URL') ? BASE_URL : '/BlogFusion/'; ?>upload/site_image/logo2.png" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Blog Fusion - Categories Management</title>
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
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
    <div class="flex h-screen overflow-hidden">
        <!-- SideNavBar -->
        <?= slidebar('categories'); ?>
        <div id="overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>
        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <?= ad_navbar(); ?>
            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-3xl font-black tracking-tight">Categories</h2>
                        <p class="text-slate-500">Organize and manage your blog's taxonomy with ease.</p>
                    </div>
                    <button onclick="toggleModal('add-category-modal', true)"
                        class="bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/20 transition-all text-sm shrink-0">
                        <span class="material-symbols-outlined text-[20px]">add_circle</span>
                        Add Category
                    </button>
                </div>
                <div class="flex flex-col gap-8">
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800">
                            <h3 class="font-bold text-lg">Manage Categories</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="text-slate-500 text-xs uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/50">
                                        <th class="px-6 py-4 font-semibold">SL</th>
                                        <th class="px-6 py-4 font-semibold">Name</th>
                                        <th class="px-6 py-4 font-semibold">Slug</th>
                                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    <?php foreach ($data24 as $key => $row) { ?>
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                            <td class="px-6 py-4 text-sm text-slate-500 font-mono"><?= $row['id'] ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <!-- <div
                                                            class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-[18px]">tag</span>
                                                        </div> -->
                                                    <span class="font-medium"><?= $row['name'] ?></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-500 font-mono"><?= $row['slug'] ?>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end gap-1">
                                                    <button
                                                        class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                                                        onclick=' edit_data(<?php echo json_encode($row); ?>)'>
                                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                                    </button>
                                                    <button onclick='delete_user(<?= $row["id"]; ?>)' ;
                                                        class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-all">
                                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        pagination_links(
                            $total_pages,
                            $limit,
                            $total_records,
                            $offset,
                            'categories',
                            'page'
                        );
                        ?>
                    </div>

                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800">
                            <h3 class="font-bold text-lg">Category Requests from Authors</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="text-slate-500 text-xs uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/50">
                                        <th class="px-6 py-4 font-semibold">SL</th>
                                        <th class="px-6 py-4 font-semibold">User Name</th>
                                        <th class="px-6 py-4 font-semibold">Category Name</th>
                                        <th class="px-6 py-4 font-semibold">Slug</th>
                                        <th class="px-6 py-4 font-semibold">Description</details>
                                        </th>
                                        <th class="px-6 py-4 font-semibold">Status</th>
                                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    <?php foreach ($all_data as $row) {
                                        $avatar_url = !empty($row['author_avatar']) ? $row['author_avatar'] : 'upload/profile-images/default.png';
                                        if (strpos($avatar_url, 'http') !== 0 && strpos($avatar_url, BASE_URL) !== 0) {
                                            $avatar_url = BASE_URL . $avatar_url;
                                        }
                                        ?>
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                            <td class="px-6 py-4 text-sm text-slate-500 font-mono"><?= $row['id'] ?>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="size-10 rounded-full bg-primary/10 bg-cover bg-center border border-primary/20"
                                                        style="background-image: url('<?= htmlspecialchars($avatar_url) ?>')">
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">
                                                            <?php echo htmlspecialchars($row['author_name'] ?? 'Unknown Author'); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <span class="font-medium"><?= $row['category_name'] ?></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-800 font-mono"><?= isset($row['category_slug']) && !empty($row['category_slug'])
                                                ? $row['category_slug'] : 'No Slug'
                                                ?></td>
                                            <td class="px-6 py-4 text-sm text-slate-500 font-mono"><?= $row['reason'] ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="px-3 py-1 
                                                        <?php
                                                        if ($row['status'] == 'pending') {
                                                            echo 'bg-orange-100 text-orange-600';
                                                        } elseif ($row['status'] == 'approved') {
                                                            echo 'bg-green-100 text-green-600';
                                                        } else {
                                                            echo 'bg-red-100 text-red-600';
                                                        }
                                                        ?>
                                                          text-[10px]  uppercase rounded-full"><?= $row['status'] ?></span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end gap-1">
                                                    <button
                                                        class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                                                        id="status_model"
                                                        onclick="update_data('<?= $row['id'] ?>','<?= $row['status'] ?>')">
                                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        pagination_links(
                            $total_req_pages,
                            $req_limit,
                            $total_req_records,
                            $req_offset,
                            'requests',
                            'req_page'
                        );
                        ?>
                    </div>
                </div>
            </div>

            <!-- Add Category Modal -->
            <div aria-labelledby="add-modal-title" aria-modal="true"
                class="fixed inset-0 z-50 flex items-center justify-center hidden" id="add-category-modal"
                role="dialog">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-all"
                    onclick="toggleModal('add-category-modal', false)">
                </div>
                <!-- Modal Content -->
                <div
                    class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all border border-slate-200 dark:border-slate-800">
                    <!-- Modal Header -->
                    <div
                        class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="add-modal-title">Add New
                            Category</h3>
                        <button type="button"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                            onclick="toggleModal('add-category-modal', false)">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <!-- Modal Body -->
                    <form class="p-6 space-y-5" action="<?= BASE_URL ?>actions/admin.php?page=<?php echo $_GET['page'] ?? $page; ?>"
                        method="POST">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
                                for="add-name">Category Name</label>
                            <input
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                                id="add-name" oninput="slug_generator()" type="text" name="name" required
                                placeholder="e.g. Technology" />
                        </div>
                        <!-- Slug -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
                                for="add-slug">Slug</label>
                            <input
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                                id="add-slug" type="text" name="slug" required placeholder="e.g. technology" />
                        </div>
                        <!-- Modal Footer -->
                        <div
                            class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                            <button type="button"
                                class="px-5 py-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors"
                                onclick="toggleModal('add-category-modal', false)">
                                Cancel
                            </button>
                            <button
                                class="px-5 py-2 text-sm font-bold bg-primary hover:bg-primary-hover text-white rounded-xl shadow-lg shadow-primary/20 transition-all"
                                type="submit" name="category-add">
                                Add Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <!-- Edit User Modal -->
        <div aria-labelledby="modal-title" aria-modal="true"
            class="fixed inset-0 z-50 flex items-center justify-center hidden" id="edit-user-modal" role="dialog">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-all"
                onclick="toggleModal('edit-user-modal', false)">
            </div>
            <!-- Modal Content -->
            <div
                class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all border border-slate-200 dark:border-slate-800">
                <!-- Modal Header -->
                <div
                    class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="modal-title">Edit Blog Category
                    </h3>
                    <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                        onclick="toggleModal('edit-user-modal', false)">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <!-- Modal Body -->
                <form class="p-6 space-y-5" action="<?= BASE_URL ?>actions/admin.php?page=<?php echo $_GET['page'] ?? $page; ?>"
                    method="POST">
                    <input type="hidden" id="id" name="id" value="">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
                            for="edit-name">Category Name</label>
                        <input
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                            id="edit-name" oninput="edit_slug_generator()" type="text" value="" name="name" />
                    </div>
                    <!-- Slug -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
                            for="edit-slug">Slug</label>
                        <input
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                            id="edit-slug" type="text" value="" name="slug" />
                    </div>




                    <!-- Modal Footer -->
                    <div
                        class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button"
                            class="px-5 py-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors"
                            onclick="toggleModal('edit-user-modal', false)">
                            Cancel
                        </button>
                        <button
                            class="px-5 py-2 text-sm font-bold bg-primary hover:bg-primary-hover text-white rounded-xl shadow-lg shadow-primary/20 transition-all"
                            type="submit" name="category-edit" id="submit_btn">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden"
        id="status-edit-modal">
        <div
            class="bg-white dark:bg-slate-900 w-full max-w-sm p-8 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Update Status</h3>
                <button type="button"
                    class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors"
                    onclick="document.getElementById('status-edit-modal').classList.add('hidden')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="<?= BASE_URL ?>actions/admin.php" method="POST" class="space-y-4">
                <input type="hidden" id="request_id" name="request_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Select
                            Category
                            Status</label>
                        <select id="status" name="status"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-sm outline-none">
                            <option value="pending">Pending</option>
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </div>
                    <button type="submit" name="category_status_update"
                        class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 mt-4">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="<?= BASE_URL ?>assets/js/admin.js"></script>
    <script>
        function toggleModal(modalId, show) {
            const modal = document.getElementById(modalId);
            if (show) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }
        function edit_data(row) {
            document.getElementById("id").value = row.id;
            document.getElementById("edit-name").value = row.name;
            document.getElementById("edit-slug").value = row.slug;
            toggleModal('edit-user-modal', true);
        }
        function delete_user(id) {
            if (!confirm('Are you sure to delete this category?')) {
                return;
            }

            window.location.href = "<?= BASE_URL ?>actions/admin.php?id=" + id + "&btn=category";
        };
        function slug_generator() {
            let name = document.getElementById("add-name").value;
            let slug = name.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            document.getElementById("add-slug").value = slug;
        }
        function edit_slug_generator() {
            let name = document.getElementById("edit-name").value;
            let slug = name.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            document.getElementById("edit-slug").value = slug;
        }
        function statusModal(show) {

            const modal =
                document.getElementById(
                    'status-edit-modal'
                );

            if (show) {

                modal.classList.remove(
                    'hidden'
                );

                document.body.style.overflow =
                    'hidden';

            } else {

                modal.classList.add(
                    'hidden'
                );

                document.body.style.overflow =
                    'auto';
            }
        }
        function update_data(id, status) {

            document.getElementById('status').value = status;

            document.getElementById('request_id').value = id;

            statusModal(true);
        }


    </script>
</body>

</html>
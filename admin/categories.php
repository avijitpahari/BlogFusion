<?php include "../include/session.php";
requireAdmin();
include "../include/db.php";

include "../include/admin_nav_sidebar.php";
include "../include/pagination.php";

// logic data
$table = 'categories';
$limit = 4;
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
$pagination = paginate($data, $total_records, $page, $offset);
$data24 = $pagination['data'];
$total_pages = $pagination['total_pages'];
$page = $pagination['current_page'];
$limit = $pagination['limit'];
$total_records = $pagination['total_records'];
$offset = $pagination['offset'];
?>

<!DOCTYPE html>

<html class="light" lang="en">

<head>
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
    <link rel="stylesheet" href="../assets/css/admin.css">
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
                <div class="mb-8">
                    <h2 class="text-3xl font-black tracking-tight">Categories</h2>
                    <p class="text-slate-500">Organize and manage your blog's taxonomy with ease.</p>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <!-- Add Category Form -->
                    <div class="xl:col-span-1">
                        <form action="../actions/admin.php?page=<?php echo $_GET['page'] ?? $page; ?>" method="POST">
                        <div
                            class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                            <h3 class="font-bold text-lg mb-6">Add New Category</h3>
                            <div class="space-y-5">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Category
                                        Name</label>
                                    <input id="add-name" onchange="slug_generator()"
                                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-sm outline-none"
                                        placeholder="e.g. Technology" type="text"  name="name"/>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs font-bold uppercase tracking-wider text-slate-500">Slug</label>
                                    <input id="add-slug"
                                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-sm outline-none"
                                        placeholder="e.g. technology" type="text" name="slug" />
                                </div>
                                <button type="submit" name="category-add"
                                    class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 mt-4">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                                    Add Category
                                </button>
                            </div>
                        </div>
                        </form>
                    </div>
                    <!-- Categories List -->
                    <div class="xl:col-span-2">
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
                                                        <div
                                                            class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-[18px]">tag</span>
                                                        </div>
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
                                                        <button onclick='delete_user(<?= $row["id"]; ?>)';
                                                            class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-all">
                                                            <span
                                                                class="material-symbols-outlined text-[20px]">delete</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            $page = pagination_links(
                                $total_pages,
                                $limit,
                                $total_records,
                                $offset
                            );
                            ?>

                        </div>
                    </div>
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
                <form class="p-6 space-y-5" action="../actions/admin.php?page=<?php echo $_GET['page'] ?? $page; ?>"
                    method="POST">
                    <input type="hidden" id="id" name="id" value="">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
                            for="edit-name">Category Name</label>
                        <input
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                            id="edit-name" type="text" value="" name="name" />
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
                        <button
                            class="px-5 py-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors"
                            onclick="toggleModal('edit-user-modal', false)">
                            Cancel
                        </button>
                        <button
                            class="px-5 py-2 text-sm font-bold bg-primary hover:bg-primary-hover text-white rounded-xl shadow-lg shadow-primary/20 transition-all"
                            type="submit" name="category-edit" 
                            id="submit_btn">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
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

            window.location.href = "../actions/admin.php?id="+id+"&btn=category";
        };
        function slug_generator(){
            let name=document.getElementById("add-name").value ;
            
        }
    </script>
</body>

</html>
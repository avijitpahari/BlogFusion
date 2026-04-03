<?php include "../include/session.php";
requireAdmin();
include "../include/db.php";
include "../include/data_fetch.php";
$data_fetch=data_featch($conn,$_SESSION['user_id']);
$data=$data_fetch['data'];
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Users Management - Blog Fusion</title>
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
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside
            class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-y-auto">
            <div class="p-6 flex items-center gap-3">
                <div class="h-10 w-10 bg-primary rounded-xl flex items-center justify-center text-white">
                    <span class="material-symbols-outlined">auto_awesome</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-primary">Blog Fusion</h1>
                    <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Admin Portal</p>
                </div>
            </div>
            <nav class="mt-6 px-3 space-y-1">
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
                    href="dashboard.php">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-primary font-semibold sidebar-active rounded-lg transition-all"
                    href="users.php">
                    <span class="material-symbols-outlined">group</span>
                    <span>Users</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
                    href="posts.php">
                    <span class="material-symbols-outlined">article</span>
                    <span>Posts</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
                    href="categories.php">
                    <span class="material-symbols-outlined">category</span>
                    <span>Categories</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
                    href="comments.php">
                    <span class="material-symbols-outlined">comment</span>
                    <span>Comments</span>
                </a>
            </nav>
            <div class="mt-auto pt-10 px-3 pb-6">
                <a class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-lg transition-all"
                    href="../actions/logout.php">
                    <span class="material-symbols-outlined">logout</span>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Navbar -->
            <header
                class="h-16 flex items-center justify-between px-8 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
                <div class="max-w-md w-full">
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input
                            class="w-full pl-10 pr-4 py-2 bg-slate-100 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-sm"
                            placeholder="Search analytics or posts..." type="text" />
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <!-- ======button====== -->
                    <button id="themeToggle"
                        class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full flex items-center justify-center"
                        title="Toggle Theme">
                        <span id="themeIcon" class="material-symbols-outlined">light_mode</span>
                    </button>
                    <button class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span
                            class="absolute top-2 right-2 h-2 w-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
                    </button>
                    <div class="h-8 w-[1px] bg-slate-200 dark:bg-slate-700 mx-2"></div>
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold"><?= $data['name'] ?></p>
                            <p class="text-xs text-slate-500">Super Admin</p>
                        </div>
                        <img alt="Admin Avatar" class="h-10 w-10 rounded-full object-cover border-2 border-primary/20"
                            data-alt="Close up portrait of a professional male administrator" src="
                            <?php if ($data_fetch['image']) {
                                echo $data_fetch['image'];
                            } else {
                                echo '../upload/profile-images/default.png';
                            }
                            ?>" />
                    </div>
                </div>
            </header>
            <div class="flex-1 overflow-y-auto p-8">
                <!-- Page Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-black tracking-tight">Users</h2>
                        <p class="text-slate-500">Manage platform contributors and account access levels.</p>
                    </div>
                    <button
                        class="bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/20 transition-all">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span>Add User</span>
                    </button>
                </div>
                <!-- Filters -->
                <div
                    class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm mb-8 flex flex-wrap gap-4 items-center">
                    <div class="flex-1 min-w-[300px] relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">filter_list</span>
                        <input
                            class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary"
                            placeholder="Filter users by name, email or role..." type="text" />
                    </div>
                    <div class="flex gap-2">
                        <button
                            class="flex items-center gap-2 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                            <span>Date Joined</span>
                        </button>
                        <button
                            class="flex items-center gap-2 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            <span>Export</span>
                        </button>
                    </div>
                </div>
                <!-- Table Container -->
                <?php
                include "../include/db.php";

                // pagination setup
                $limit = 4; // rows per page
                $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

                if ($page < 1)
                    $page = 1;

                $offset = ($page - 1) * $limit;

                // fetch users
                $query = "SELECT * FROM users LIMIT $limit OFFSET $offset";
                $result = mysqli_query($conn, $query);

                // total count
                $total_query = "SELECT COUNT(*) as total FROM users";
                $total_result = mysqli_query($conn, $total_query);
                $total_data = mysqli_fetch_assoc($total_result);
                $total_users = $total_data['total'];

                $total_pages = ceil($total_users / $limit);
                ?>
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-8">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="text-slate-500 text-xs uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                                    <th class="px-6 py-4 font-semibold">User Name</th>
                                    <th class="px-6 py-4 font-semibold">Email Address</th>
                                    <th class="px-6 py-4 font-semibold">Role</th>
                                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                                </tr>
                            </thead>
                           
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">

                                        <!-- USER NAME + IMAGE -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">

                                                <div class="size-10 rounded-full bg-primary/10 bg-cover bg-center border border-primary/20"
                                                    style="background-image: url('../<?php echo $row['profile_image']; ?>')">
                                                </div>

                                                <div>
                                                    <p class="font-bold text-sm"><?php echo $row['name']; ?></p>
                                                    <p class="text-xs text-slate-500">
                                                        Joined <?php echo date("M d, Y", strtotime($row['created_at'])); ?>
                                                    </p>
                                                </div>

                                            </div>
                                        </td>

                                        <!-- EMAIL -->
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                            <?php echo $row['email']; ?>
                                        </td>

                                        <!-- ROLE -->
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full 
                                                <?php
                                                if ($row['role'] == "admin")
                                                    echo "bg-purple-100 text-purple-700";
                                                elseif ($row['role'] == "author")
                                                    echo "bg-blue-100 text-blue-700";
                                                else
                                                    echo "bg-slate-100 text-slate-600";
                                                ?>">
                                                <?php echo ucfirst($row['role']); ?>
                                            </span>
                                        </td>

                                        <!-- ACTIONS -->
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button class="p-2 hover:text-primary">
                                                    <span class="material-symbols-outlined" onclick="toggleModal('edit-user-modal', true)">edit_note</span>
                                                </button>
                                                <button class="p-2 hover:text-red-500">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </div>
                                        </td>

                                    </tr>

                                <?php } ?>

                            </tbody>
                        </table>
                    </div>
                    <div
                        class="bg-slate-50/50 dark:bg-slate-800/50 px-6 py-4 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                        <p class="text-sm text-slate-500">Showing <span
                                class="font-bold text-slate-900 dark:text-slate-100"><?php echo $offset + 1; ?></span>
                            to <span
                                class="font-bold text-slate-900 dark:text-slate-100"><?php echo min($offset + $limit, $total_users); ?></span>
                            of <span
                                class="font-bold text-slate-900 dark:text-slate-100"><?php echo $total_users; ?></span>
                            users</p>

                        
                            
                        <div class="px-6 py-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button
                                class="p-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors disabled:opacity-50"
                                disabled="">
                                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                            </button>
                            <button
                                class="h-9 w-9 flex items-center justify-center bg-primary text-white rounded-lg font-bold text-sm shadow-sm">1</button>
                            <button
                                class="h-9 w-9 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg font-medium text-sm transition-colors">2</button>
                            <button
                                class="h-9 w-9 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg font-medium text-sm transition-colors">3</button>
                            <button
                                class="p-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                            </button>
                        </div>
                    </div>
                    </div>
                </div>

                <?php
                include "../include/db.php";

                // 1️⃣ New Signups (this month)
                $signup_query = "SELECT COUNT(*) as total FROM users WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";

                $signup_result = mysqli_query($conn, $signup_query);
                $new_signups = mysqli_fetch_assoc($signup_result)['total'];


                // 2️⃣ Active Authors
                $author_query = "SELECT COUNT(*) as total FROM users WHERE role='author'";
                $author_result = mysqli_query($conn, $author_query);
                $active_authors = mysqli_fetch_assoc($author_result)['total'];


                // 3️⃣ Flagged Accounts (example condition)
                $flag_query = "SELECT COUNT(*) as total FROM users WHERE role='user'"; // change logic if needed
                $flag_result = mysqli_query($conn, $flag_query);
                $flagged_accounts = mysqli_fetch_assoc($flag_result)['total'];
                ?>

                <!-- Footer Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div
                                class="h-10 w-10 bg-green-500/10 text-green-600 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">person_add</span>
                            </div>
                            <span class="text-[10px] font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+12%
                                month</span>
                        </div>
                        <p class="text-slate-500 text-xs font-medium uppercase tracking-wider">New Signups</p>
                        <p class="text-2xl font-bold mt-1"><?php echo $new_signups; ?></p>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div
                                class="h-10 w-10 bg-blue-500/10 text-blue-600 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">verified_user</span>
                            </div>
                            <span
                                class="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-full uppercase tracking-tighter">Steady</span>
                        </div>
                        <p class="text-slate-500 text-xs font-medium uppercase tracking-wider">Active Authors</p>
                        <p class="text-2xl font-bold mt-1"><?= $active_authors; ?></p>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div
                                class="h-10 w-10 bg-amber-500/10 text-amber-600 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">report</span>
                            </div>
                            <span
                                class="text-[10px] font-bold text-red-500 bg-red-50 px-2 py-1 rounded-full uppercase tracking-tighter">2
                                pending</span>
                        </div>
                        <p class="text-slate-500 text-xs font-medium uppercase tracking-wider">Flagged Accounts</p>
                        <p class="text-2xl font-bold mt-1"><?= $flagged_accounts; ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- Edit User Modal -->
    <div aria-labelledby="modal-title" aria-modal="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden" id="edit-user-modal" role="dialog">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-all" onclick="toggleModal('edit-user-modal', false)">
        </div>
        <!-- Modal Content -->
        <div
            class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all border border-slate-200 dark:border-slate-800">
            <!-- Modal Header -->
            <div
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="modal-title">Edit User Profile</h3>
                <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                    onclick="toggleModal('edit-user-modal', false)">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <!-- Modal Body -->
            <form class="p-6 space-y-5">
                <!-- User Name -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
                        for="edit-name">User Name</label>
                    <input
                        class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                        id="edit-name" type="text" value="Sarah Chen" />
                </div>
                <!-- Email Address -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
                        for="edit-email">Email Address</label>
                    <input
                        class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                        id="edit-email" type="email" value="sarah.chen@blogfusion.io" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Role Dropdown -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
                            for="edit-role">Role</label>
                        <select
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none appearance-none"
                            id="edit-role">
                            <option selected="" value="admin">Admin</option>
                            <option value="author">Author</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <!-- Status Select -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
                            for="edit-status">Status</label>
                        <select
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none appearance-none"
                            id="edit-status">
                            <option selected="" value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </form>
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
                    onclick="toggleModal('edit-user-modal', false)">
                    Save Changes
                </button>
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
    </script>
</body>

</html>
<?php include "../include/session.php"; 
requireAdmin();
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
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
                    href="users.php">
                    <span class="material-symbols-outlined">group</span>
                    <span>Users</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
                    href="posts.php">
                    <span class="material-symbols-outlined">article</span>
                    <span>Posts</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-primary font-semibold sidebar-active rounded-lg transition-all"
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
        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header
                class="h-16 flex items-center justify-between px-8 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
                <div class="max-w-md w-full">
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input
                            class="w-full pl-10 pr-4 py-2 bg-slate-100 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-sm"
                            placeholder="Search categories..." type="text" />
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <!-- ======button====== -->
                    <button id="themeToggle"
                        class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full flex items-center justify-center"
                        title="Toggle Theme">
                        <span id="themeIcon" class="material-symbols-outlined">light_mode</span>
                    </button>
                    <button
                        class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span
                            class="absolute top-2 right-2 h-2 w-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
                    </button>
                    <div class="h-8 w-[1px] bg-slate-200 dark:bg-slate-700 mx-2"></div>
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold">Julian Pierce</p>
                            <p class="text-xs text-slate-500">Super Admin</p>
                        </div>
                        <img alt="Admin Avatar" class="h-10 w-10 rounded-full object-cover border-2 border-primary/20"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBg4BP--vCtSY6GpNicW6xVGSjGqV56vSduGJ3z_EUjilOkzVUjUf9INMznnr_7EnlKmXJ2sC1TAIu_lu30A-4Ug1604QBoFNpfBy26CHbVA6u_lrCl_jgqqkzb5O8tM5NbEexvJZt01svWEnNTBlnaFW_mIJmGfQq3tg5r_fg3QBNLV8h7Ze2VYWAjuNECooav2pc6Xi8o2P-j-9oslE6TDHEJV4et_6QkSdo1oWL_b9fdtdQmqw773F2RxHfBhBYWxfga59XIhr4" />
                    </div>
                </div>
            </header>
            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-8">
                <div class="mb-8">
                    <h2 class="text-3xl font-black tracking-tight">Categories</h2>
                    <p class="text-slate-500">Organize and manage your blog's taxonomy with ease.</p>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <!-- Add Category Form -->
                    <div class="xl:col-span-1">
                        <div
                            class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                            <h3 class="font-bold text-lg mb-6">Add New Category</h3>
                            <div class="space-y-5">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Category
                                        Name</label>
                                    <input
                                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-sm outline-none"
                                        placeholder="e.g. Technology" type="text" />
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs font-bold uppercase tracking-wider text-slate-500">Slug</label>
                                    <input
                                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-sm outline-none"
                                        placeholder="e.g. technology" type="text" />
                                </div>
                                <button
                                    class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 mt-4">
                                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                                    Add Category
                                </button>
                            </div>
                        </div>
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
                                            <th class="px-6 py-4 font-semibold">Name</th>
                                            <th class="px-6 py-4 font-semibold">Slug</th>
                                            <th class="px-6 py-4 font-semibold text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-[18px]">tag</span>
                                                    </div>
                                                    <span class="font-medium">Technology</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-500 font-mono">technology</td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end gap-1">
                                                    <button
                                                        class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all">
                                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                                    </button>
                                                    <button
                                                        class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-all">
                                                        <span
                                                            class="material-symbols-outlined text-[20px]">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-[18px]">tag</span>
                                                    </div>
                                                    <span class="font-medium">Lifestyle</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-500 font-mono">lifestyle</td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end gap-1">
                                                    <button
                                                        class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all">
                                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                                    </button>
                                                    <button
                                                        class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-all">
                                                        <span
                                                            class="material-symbols-outlined text-[20px]">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-[18px]">tag</span>
                                                    </div>
                                                    <span class="font-medium">Marketing</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-500 font-mono">marketing</td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end gap-1">
                                                    <button
                                                        class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all">
                                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                                    </button>
                                                    <button
                                                        class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-all">
                                                        <span
                                                            class="material-symbols-outlined text-[20px]">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-[18px]">tag</span>
                                                    </div>
                                                    <span class="font-medium">Health &amp; Wellness</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-500 font-mono">health-wellness</td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end gap-1">
                                                    <button
                                                        class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all">
                                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                                    </button>
                                                    <button
                                                        class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-all">
                                                        <span
                                                            class="material-symbols-outlined text-[20px]">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div
                                class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
                                <p class="text-xs text-slate-500">Showing 4 of 12 categories</p>
                                <div class="flex gap-2">
                                    <button
                                        class="px-4 py-1.5 text-xs font-semibold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors disabled:opacity-50"
                                        disabled="">Previous</button>
                                    <button
                                        class="px-4 py-1.5 text-xs font-semibold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">Next</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>

</html>
<?php include "../include/session.php"; 
requireAdmin();
include "../include/db.php";

include "../include/admin_nav_sidebar.php";
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
        <?=slidebar('categories');?>
        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <?=ad_navbar($data,$data_fetch);?>
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
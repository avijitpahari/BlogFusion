<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Blog Fusion - Admin Dashboard</title>
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
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
        }

        .sidebar-active {
            background-color: rgba(124, 58, 237, 0.1);
            border-right: 4px solid #7C3AED;
        }
    </style>
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
                <a class="flex items-center gap-3 px-4 py-3 text-primary font-semibold sidebar-active rounded-lg transition-all"
                    href="#">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
                    href="#">
                    <span class="material-symbols-outlined">group</span>
                    <span>Users</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
                    href="#">
                    <span class="material-symbols-outlined">article</span>
                    <span>Posts</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
                    href="#">
                    <span class="material-symbols-outlined">category</span>
                    <span>Categories</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
                    href="#">
                    <span class="material-symbols-outlined">comment</span>
                    <span>Comments</span>
                </a>
            </nav>
            <div class="mt-auto pt-10 px-3 pb-6">
                <a class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-lg transition-all"
                    href="#">
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
                            placeholder="Search analytics or posts..." type="text" />
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button
                        class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full flex items-center justify-center"
                        title="Toggle Theme">
                        <span class="material-symbols-outlined">light_mode</span>
                    </button><button
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
                            data-alt="Close up portrait of a professional male administrator"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBg4BP--vCtSY6GpNicW6xVGSjGqV56vSduGJ3z_EUjilOkzVUjUf9INMznnr_7EnlKmXJ2sC1TAIu_lu30A-4Ug1604QBoFNpfBy26CHbVA6u_lrCl_jgqqkzb5O8tM5NbEexvJZt01svWEnNTBlnaFW_mIJmGfQq3tg5r_fg3QBNLV8h7Ze2VYWAjuNECooav2pc6Xi8o2P-j-9oslE6TDHEJV4et_6QkSdo1oWL_b9fdtdQmqw773F2RxHfBhBYWxfga59XIhr4" />
                    </div>
                </div>
            </header>
            <!-- Dashboard Content -->
            <div class="flex-1 overflow-y-auto p-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-black tracking-tight">Dashboard Overview</h2>
                        <p class="text-slate-500">Welcome back, here's what's happening today.</p>
                    </div>
                    <button
                        class="bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/20 transition-all">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        New Post
                    </button>
                </div>
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div
                        class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="h-12 w-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined">group</span>
                            </div>
                            <span
                                class="text-green-500 text-xs font-bold bg-green-50 px-2 py-1 rounded-full">+12%</span>
                        </div>
                        <p class="text-slate-500 text-sm font-medium">Total Users</p>
                        <h3 class="text-2xl font-bold mt-1">12,840</h3>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="h-12 w-12 bg-indigo- accent bg-indigo-600/10 text-indigo-600 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined">article</span>
                            </div>
                            <span class="text-green-500 text-xs font-bold bg-green-50 px-2 py-1 rounded-full">+4%</span>
                        </div>
                        <p class="text-slate-500 text-sm font-medium">Total Posts</p>
                        <h3 class="text-2xl font-bold mt-1">1,452</h3>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="h-12 w-12 bg-amber-500/10 text-amber-500 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined">category</span>
                            </div>
                            <span class="text-slate-400 text-xs font-bold bg-slate-50 px-2 py-1 rounded-full">0%</span>
                        </div>
                        <p class="text-slate-500 text-sm font-medium">Categories</p>
                        <h3 class="text-2xl font-bold mt-1">24</h3>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="h-12 w-12 bg-pink-500/10 text-pink-500 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined">forum</span>
                            </div>
                            <span class="text-red-500 text-xs font-bold bg-red-50 px-2 py-1 rounded-full">-2%</span>
                        </div>
                        <p class="text-slate-500 text-sm font-medium">Comments</p>
                        <h3 class="text-2xl font-bold mt-1">45,902</h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <!-- Recent Posts Table -->
                    <div
                        class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                        <div
                            class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                            <h3 class="font-bold text-lg">Recent Posts</h3>
                            <button class="text-primary text-sm font-semibold hover:underline">View All</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="text-slate-500 text-xs uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/50">
                                        <th class="px-6 py-4 font-semibold">Post Title</th>
                                        <th class="px-6 py-4 font-semibold">Author</th>
                                        <th class="px-6 py-4 font-semibold">Date</th>
                                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-medium truncate max-w-[240px]">The Future of AI in Modern
                                                Design</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="h-6 w-6 rounded-full bg-primary/20 text-[10px] flex items-center justify-center font-bold text-primary">
                                                    SC</div>
                                                <span class="text-sm">Sarah Chen</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500">Oct 24, 2023</td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 uppercase">Published</span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-medium truncate max-w-[240px]">10 Productivity Hacks for
                                                Remote Teams</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="h-6 w-6 rounded-full bg-primary/20 text-[10px] flex items-center justify-center font-bold text-primary">
                                                    MK</div>
                                                <span class="text-sm">Marcus King</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500">Oct 23, 2023</td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase">Draft</span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-medium truncate max-w-[240px]">Mastering Tailwind CSS v4
                                                Features</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="h-6 w-6 rounded-full bg-primary/20 text-[10px] flex items-center justify-center font-bold text-primary">
                                                    EL</div>
                                                <span class="text-sm">Emma Laine</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500">Oct 22, 2023</td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 uppercase">Published</span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-medium truncate max-w-[240px]">The Rise of Minimalist
                                                Architecture</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="h-6 w-6 rounded-full bg-primary/20 text-[10px] flex items-center justify-center font-bold text-primary">
                                                    DO</div>
                                                <span class="text-sm">David Owen</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500">Oct 21, 2023</td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-amber-100 text-amber-700 uppercase">Review</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Recent Activity -->
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800">
                            <h3 class="font-bold text-lg">Recent Activity</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="flex gap-4">
                                <div class="relative">
                                    <div
                                        class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[20px]">chat</span>
                                    </div>
                                    <div
                                        class="absolute -bottom-1 -right-1 h-4 w-4 bg-green-500 border-2 border-white dark:border-slate-900 rounded-full">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm"><strong>Sarah Chen</strong> commented on "The Future of AI..."
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1 italic">"Great insights on the new LLM
                                        models!"</p>
                                    <p class="text-[10px] text-slate-500 mt-2 uppercase font-bold tracking-tighter">2
                                        mins ago</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div
                                    class="h-10 w-10 rounded-full bg-indigo-600/10 text-indigo-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm"><strong>New user</strong> registered as a Content Contributor.
                                    </p>
                                    <p class="text-[10px] text-slate-500 mt-2 uppercase font-bold tracking-tighter">45
                                        mins ago</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div
                                    class="h-10 w-10 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">system_update_alt</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm"><strong>System Update</strong> successfully completed.</p>
                                    <p class="text-xs text-slate-400 mt-1">Core engine updated to v2.4.0</p>
                                    <p class="text-[10px] text-slate-500 mt-2 uppercase font-bold tracking-tighter">3
                                        hours ago</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div
                                    class="h-10 w-10 rounded-full bg-red-500/10 text-red-500 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">report</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm"><strong>Flagged content</strong> reported in Category: Tech.</p>
                                    <p class="text-[10px] text-slate-500 mt-2 uppercase font-bold tracking-tighter">5
                                        hours ago</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">
                            <button
                                class="w-full text-center py-2 text-sm font-semibold text-slate-500 hover:text-primary transition-colors">
                                View Full Activity Log
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
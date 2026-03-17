<!DOCTYPE html>

<html class="light" lang="en">

<head>
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
                <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
                    href="categories.php">
                    <span class="material-symbols-outlined">category</span>
                    <span>Categories</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-primary font-semibold sidebar-active rounded-lg transition-all"
                    href="comments.php">
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
                            placeholder="Search comments, users or posts..." type="text" />
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
                            <p class="text-sm font-semibold">Julian Pierce</p>
                            <p class="text-xs text-slate-500">Super Admin</p>
                        </div>
                        <img alt="Admin Avatar" class="h-10 w-10 rounded-full object-cover border-2 border-primary/20"
                            data-alt="Close up portrait of a professional male administrator"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBg4BP--vCtSY6GpNicW6xVGSjGqV56vSduGJ3z_EUjilOkzVUjUf9INMznnr_7EnlKmXJ2sC1TAIu_lu30A-4Ug1604QBoFNpfBy26CHbVA6u_lrCl_jgqqkzb5O8tM5NbEexvJZt01svWEnNTBlnaFW_mIJmGfQq3tg5r_fg3QBNLV8h7Ze2VYWAjuNECooav2pc6Xi8o2P-j-9oslE6TDHEJV4et_6QkSdo1oWL_b9fdtdQmqw773F2RxHfBhBYWxfga59XIhr4" />
                    </div>
                </div>
            </header>
            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-black tracking-tight">Comments</h2>
                        <p class="text-slate-500">Manage and moderate user comments across all blog posts</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button
                            class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <span class="material-symbols-outlined text-[20px]">filter_list</span>
                            Filter
                        </button>
                        <button
                            class="bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/20 transition-all">
                            <span class="material-symbols-outlined text-[20px]">download</span>
                            Export CSV
                        </button>
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
                                <!-- Row 1 -->
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img class="h-9 w-9 rounded-full object-cover border-2 border-slate-100 dark:border-slate-800"
                                                data-alt="User avatar of Sarah Jenkins"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAeeYkzoFM9c3cMFc7AUbaMTaXNSPkLBAeWmqT3sVYUJkj5KsyUm3g8DLqc9nrwmjjFwHJu5PlM6aEETe9HqwOIaNuCsxGI-1DMJIjC5QeG8LjdJaO1d5c6Swewx45-9_on0axlvfs_v9r6x_qvuOziVq5xgoO8oJnFXoQ3dLW4P_Bd7JXysKqgUllF9BU_ffwgy83h-NzNlXLb2BpymoHN0OmzetEPfIHUndBvdRZAd7Kw8GtoLJtWDK0LbUDs8IXcp6_Bi88Y3kk" />
                                            <span class="text-sm font-semibold">Sarah Jenkins</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a class="text-sm font-medium text-primary hover:underline line-clamp-1"
                                            href="#">10 Tips for UI Design Mastery</a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 max-w-md">
                                            "This is exactly what I needed! The section on color theory and
                                            accessibility is particularly insightful. Thanks for sharing!"</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 whitespace-nowrap">Oct 12, 2023</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                class="p-2 text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors"
                                                title="Approve">
                                                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                            </button>
                                            <button
                                                class="p-2 text-rose-600 bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/40 rounded-lg transition-colors"
                                                title="Delete">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Row 2 -->
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img class="h-9 w-9 rounded-full object-cover border-2 border-slate-100 dark:border-slate-800"
                                                data-alt="User avatar of Marcus Thorne"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuC0W-l2cX7WZqE2S9OnRkEVautG7TyReOgH0Swj7_KxzIwUoVF6OSksz9VJkbI0Q_sWNhaaglR-78gMK_fSNqZICQOnMeCZdApdknSk_6ogIUAOd3v-TIx74BDicTK27UdsTdkGMQzqy5jA0b2iSjEDB3_JdhNl_NTJZUAWKuNLVCHQvInpJHlb3PwojOdYOehEutCP3fBQsndy-Mrxr4uds_M3nz3WVEyNxADbAbw52vOB7gS-CCouTmuxhaH3CJvHtyiFvlWLkYQ" />
                                            <span class="text-sm font-semibold">Marcus Thorne</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a class="text-sm font-medium text-primary hover:underline line-clamp-1"
                                            href="#">React vs Vue in 2024: A Comparison</a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 max-w-md">
                                            "I've been using Vue for 3 years, but your points about the React ecosystem
                                            are hard to ignore. Might be time to switch."</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 whitespace-nowrap">Oct 11, 2023</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                class="p-2 text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors"
                                                title="Approve">
                                                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                            </button>
                                            <button
                                                class="p-2 text-rose-600 bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/40 rounded-lg transition-colors"
                                                title="Delete">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Row 3 -->
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img class="h-9 w-9 rounded-full object-cover border-2 border-slate-100 dark:border-slate-800"
                                                data-alt="User avatar of Leo David"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD6UxyX9D0QluFOIb-2RVm86hoQl_9Ak9A6-gb3O1bIf6oKL3CoCAg9YVnOXQR2tA04tQ6AiT_LmCQz8TQdKkPK88u5tfZhmg9MSPtFcF8VnzCPNdngyO98g472jpIZuZtl3aJpkp0tGiLX2LTY3dEoKTxzQCvVxmcf_UaAb-cVgaryhmf903tX7vOj_E1D2uYclL1QLG6IlKMv69Yh_BgXA6rM9LR-rOPmbFVJcAHUTqrdxD3yJzU91l0ssJw_szL5c--9z0Tej3A" />
                                            <span class="text-sm font-semibold">Leo David</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a class="text-sm font-medium text-primary hover:underline line-clamp-1"
                                            href="#">Mastering CSS Grid Layouts</a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 max-w-md">
                                            "Could you elaborate on how to handle legacy browsers with these grid
                                            properties?"</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 whitespace-nowrap">Oct 10, 2023</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                class="p-2 text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors"
                                                title="Approve">
                                                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                            </button>
                                            <button
                                                class="p-2 text-rose-600 bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/40 rounded-lg transition-colors"
                                                title="Delete">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Row 4 -->
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img class="h-9 w-9 rounded-full object-cover border-2 border-slate-100 dark:border-slate-800"
                                                data-alt="User avatar of Emily Watson"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBrwGjCte6kI7iAdc6y-RTaamVj_QyFq8wsp48x5mPmg2y67xKv6qrG89bya5zRnpKgwEPv_QekAsljwUNA3VCfkAnd99Uhdq_epZO68B1RC7WIfQNwTSrfcTKCdxpkqru4JHUAzPzut0QoxKsoSa6Qz9RPBiTV6HmsP9oDluO3CHP8c-Wp5JV41MWp1-0SRXjI6seDohCSTLSTG_VXs8pV8CC0d7dWc4fum84eXrjHJHnJLETupQiyXhuZbu3mpx6zv4UAFPg-mv8" />
                                            <span class="text-sm font-semibold">Emily Watson</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a class="text-sm font-medium text-primary hover:underline line-clamp-1"
                                            href="#">The Future of AI in Content Creation</a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 max-w-md">
                                            "Excellent read. I think we need to be careful about the ethical
                                            implications mentioned in the fourth paragraph."</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 whitespace-nowrap">Oct 09, 2023</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                class="p-2 text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors"
                                                title="Approve">
                                                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                            </button>
                                            <button
                                                class="p-2 text-rose-600 bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/40 rounded-lg transition-colors"
                                                title="Delete">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div
                        class="px-6 py-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30 flex items-center justify-between">
                        <p class="text-sm text-slate-500">Showing <span
                                class="font-bold text-slate-900 dark:text-white">1</span> to <span
                                class="font-bold text-slate-900 dark:text-white">4</span> of <span
                                class="font-bold text-slate-900 dark:text-white">48</span> comments</p>
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
        </main>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>

</html>
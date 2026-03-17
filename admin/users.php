<?php include "../include/session.php"; 
requireAdmin();
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
                            <p class="text-sm font-semibold">Julian Pierce</p>
                            <p class="text-xs text-slate-500">Super Admin</p>
                        </div>
                        <img alt="Admin Avatar" class="h-10 w-10 rounded-full object-cover border-2 border-primary/20"
                            data-alt="Close up portrait of a professional male administrator"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBg4BP--vCtSY6GpNicW6xVGSjGqV56vSduGJ3z_EUjilOkzVUjUf9INMznnr_7EnlKmXJ2sC1TAIu_lu30A-4Ug1604QBoFNpfBy26CHbVA6u_lrCl_jgqqkzb5O8tM5NbEexvJZt01svWEnNTBlnaFW_mIJmGfQq3tg5r_fg3QBNLV8h7Ze2VYWAjuNECooav2pc6Xi8o2P-j-9oslE6TDHEJV4et_6QkSdo1oWL_b9fdtdQmqw773F2RxHfBhBYWxfga59XIhr4" />
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
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 rounded-full bg-primary/10 bg-cover bg-center border border-primary/20"
                                                data-alt="User avatar for Sarah Chen"
                                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCaIHZOqcEtt-9WpKFSq4jKLedBn8MO53lEukh3El229zvtHbZVgA70X0Ig4bJX08Y1NHKoDx2XvHp83Dgh-BD6DauBnqMSrDMwzHURHerPqMe5dELkjsp8NOIKblHWFA8Nte6ETEOFcKj0X2A0rBUqKoEBtffRBmwQgMfTYY7V7jC8-_FNcE4-MTFEPxm56cDlZqPtBfgr9twA8UYtmEeWDCR5ZQ9jP6BdVsiOe0WdYSBokpS57Pn5GrBXwykF8QHg7IOSd3MN0cM')">
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-slate-100 text-sm">Sarah
                                                    Chen</p>
                                                <p class="text-xs text-slate-500">Joined Oct 12, 2023</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                        sarah.chen@blogfusion.io</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-purple-100 text-purple-700 uppercase">Admin</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button class="p-2 text-slate-400 hover:text-primary transition-colors">
                                                <span class="material-symbols-outlined text-[20px]">edit_note</span>
                                            </button>
                                            <button class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                            <button
                                                class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold bg-slate-100 dark:bg-slate-800 rounded-lg">
                                                <span>Role</span>
                                                <span class="material-symbols-outlined text-[16px]">expand_more</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 rounded-full bg-primary/10 bg-cover bg-center border border-primary/20"
                                                data-alt="User avatar for Marcus Thorne"
                                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDXrWNPIwqoYr877uOIiMtiPlxNGLAlf06s7ROYWNdKU3tU_KRITuDNJ3X1W7Yp5w-7ZC9xwBKafmIGVCwKSJ61UVSZNY9WkiRd4HVblTfekjD_5PwerjHf7ZcEMrVpALVtVrWzy7SJV_wKf9MYqmqKIfK8KOvwOAJOXLtaeZkAuhcmQyiVJUQfzaVNbGgvhEKrM-f29nlBLjBT9XvjocMz8VxJxxXpWRYGLgjx7PfbJ3cjsBWfoVvfL25xH21zJgLPuL9IPk5IdM4')">
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-slate-100 text-sm">Marcus
                                                    Thorne</p>
                                                <p class="text-xs text-slate-500">Joined Sep 28, 2023</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">m.thorne@writer.net
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase">Author</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button class="p-2 text-slate-400 hover:text-primary transition-colors">
                                                <span class="material-symbols-outlined text-[20px]">edit_note</span>
                                            </button>
                                            <button class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                            <button
                                                class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold bg-slate-100 dark:bg-slate-800 rounded-lg">
                                                <span>Role</span>
                                                <span class="material-symbols-outlined text-[16px]">expand_more</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 rounded-full bg-primary/10 bg-cover bg-center border border-primary/20"
                                                data-alt="User avatar for Elena Rodriguez"
                                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuC2WvxKnBITAo9_bhm-9v09AEKU26Jw-rwNMbFXNvR5s35O3LxtciMsnDZfR8Yz-pSOMkDwCYCdUOrIE7YQvVPmAr-mH-gDc6c4cSq4OrkrwnKVkmv7RbP4OctFQvt8FD_bpxOZpY2VDaIiXK6ZStwnmqy9L7lmAH940LjT1DtyiKPId_GAn7IIzs6zpdnJVP-IsGsWiqqx4Hu79z_-DkGO5Gwh684EPC6CkdhX597JHlHiPkGEvHv_KgsfpAZY1vWR3dUZxWXhcPs')">
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-slate-100 text-sm">Elena
                                                    Rodriguez</p>
                                                <p class="text-xs text-slate-500">Joined Jan 05, 2024</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">elena.rod@gmail.com
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 uppercase">User</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button class="p-2 text-slate-400 hover:text-primary transition-colors">
                                                <span class="material-symbols-outlined text-[20px]">edit_note</span>
                                            </button>
                                            <button class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                            <button
                                                class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold bg-slate-100 dark:bg-slate-800 rounded-lg">
                                                <span>Role</span>
                                                <span class="material-symbols-outlined text-[16px]">expand_more</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 rounded-full bg-primary/10 bg-cover bg-center border border-primary/20"
                                                data-alt="User avatar for David Park"
                                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCFLkF1z1JpHlAqbglAETK0FQKfguUdJp6dto7XTSe355pIIUhFP28ThHBD6mSMdNSf0bsUf1CS3S8VyWHK69lRsNmOAsBRgmahPC1NKqlnA2-tz2foAy8BQtQHsKmTju6No41p5J_O9EHR-TIQlUORwWHIZGJhlW2InOyo-hzmD0_WL_2QZgRYyhQGlE8x-fTjIetjCPNKekTBcfa-2c9zCllCDEj0DCibYSFonp-d2pJutOj9Gx_SPV5PHz6qrg1c0y5JnioeUJI')">
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-slate-100 text-sm">David
                                                    Park</p>
                                                <p class="text-xs text-slate-500">Joined Nov 15, 2023</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                        park.david@agency.com</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase">Author</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button class="p-2 text-slate-400 hover:text-primary transition-colors">
                                                <span class="material-symbols-outlined text-[20px]">edit_note</span>
                                            </button>
                                            <button class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                            <button
                                                class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold bg-slate-100 dark:bg-slate-800 rounded-lg">
                                                <span>Role</span>
                                                <span class="material-symbols-outlined text-[16px]">expand_more</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        class="bg-slate-50/50 dark:bg-slate-800/50 px-6 py-4 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">
                        <p class="text-sm text-slate-500">Showing <span
                                class="font-bold text-slate-900 dark:text-slate-100">1</span> to <span
                                class="font-bold text-slate-900 dark:text-slate-100">4</span> of <span
                                class="font-bold text-slate-900 dark:text-slate-100">42</span> users</p>
                        <div class="flex gap-2">
                            <button
                                class="px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold shadow-sm opacity-50 cursor-not-allowed">Previous</button>
                            <button
                                class="px-3 py-1.5 bg-primary text-white rounded-lg text-xs font-bold shadow-sm">1</button>
                            <button
                                class="px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold shadow-sm">2</button>
                            <button
                                class="px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold shadow-sm">3</button>
                            <button
                                class="px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold shadow-sm">Next</button>
                        </div>
                    </div>
                </div>
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
                        <p class="text-2xl font-bold mt-1">128</p>
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
                        <p class="text-2xl font-bold mt-1">14</p>
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
                        <p class="text-2xl font-bold mt-1">3</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>

</html>
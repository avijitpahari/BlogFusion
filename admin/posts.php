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
    <div class="flex h-screen overflow-hidden">
        <!-- SideNavBar -->
        <?=slidebar('posts');?>
        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <?=ad_navbar();?>
            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-8">
                <!-- Page Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-black tracking-tight">Posts</h2>
                        <p class="text-slate-500">Manage and organize your blog content</p>
                    </div>
                    <button
                        class="bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/20 transition-all">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        Add Post
                    </button>
                </div>
                <!-- Filters -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-8">
                    <div class="flex flex-wrap items-center gap-6">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Filter
                                by Category</label>
                            <div class="relative">
                                <select
                                    class="w-full appearance-none bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-primary focus:border-primary">
                                    <option>All Categories</option>
                                    <option>Technology</option>
                                    <option>Programming</option>
                                    <option>Business</option>
                                    <option>Design</option>
                                </select>
                                <span
                                    class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label
                                class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Status</label>
                            <div class="relative">
                                <select
                                    class="w-full appearance-none bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-primary focus:border-primary">
                                    <option>All Status</option>
                                    <option>Published</option>
                                    <option>Draft</option>
                                    <option>Scheduled</option>
                                </select>
                                <span
                                    class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                            </div>
                        </div>
                        <div class="flex gap-2 self-end">
                            <button
                                class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 px-4 py-2.5 rounded-xl font-medium transition-colors text-sm">
                                Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Posts Table -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="text-slate-500 text-xs uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/50">
                                    <th class="px-6 py-4 font-semibold w-24">Image</th>
                                    <th class="px-6 py-4 font-semibold">Title</th>
                                    <th class="px-6 py-4 font-semibold">Category</th>
                                    <th class="px-6 py-4 font-semibold">Author</th>
                                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <!-- Row 1 -->
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div
                                            class="w-14 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                            <img alt="Post thumbnail" class="w-full h-full object-cover"
                                                data-alt="Close up of high tech computer motherboard with green lights"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDA8UlO_Fc3YRAJOFkJ17ORU97Myic_lj6ErGPnQlxtECsMTCJwbF6ydJ-42KObyWmhNnDidJYEt1bxLuQqj_pMYNKILU0ltY9zjvm6wOpUaJ6lOGI1PPlrkEVIXq-0QzDb8-061w2DVSikgDcOrZ9Ca1g3FNLJ2HNkZJcYDNRmOC3oThn85IjLvGubzbkGMCsRddQo8NYGhDx0mxRSrunwBuhcZ1sRsDkbiEXQu9WPWr6S1Iu8SrYtzZ76HeD5JYJFsbuPOsGzAjw" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-sm line-clamp-1">Future of AI in Cybersecurity</p>
                                        <p class="text-xs text-slate-500 mt-1">Updated 2 hours ago</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-primary/10 text-primary uppercase">Technology</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <img alt="Author" class="w-6 h-6 rounded-full"
                                                data-alt="Profile icon of a creative female blogger"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCM9b5EYYel2ThAKkShWf8_mczQgf0SE3TrTK_j28Ftvoy1lSHok0yekJUu1zGIJ3Er1H0o96XBxM1kxs7fdJ72WlufSX6FjDn0JgOwXEnRV1a_J2MqdXEsWag6XRyXmYdueQuen-py4b92qMObc1YUe0BJQ5DVXGFaxj6l3mFAQkozL3NGlfyYwgAgEYhYMgiQ6-eti9l50pfwW4KD16I4K_uDRXQx-4env9xCdEgWXO4Q1QznuySKTWomm13BDfdNAqRdiwHBJ8Y" />
                                            <span class="text-sm font-medium">Sarah Jenkins</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                class="p-2 text-slate-400 hover:text-indigo-accent hover:bg-indigo-600/10 rounded-lg transition-colors"
                                                title="View">
                                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                                            </button>
                                            <button
                                                class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                title="Edit">
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </button>
                                            <button
                                                class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors"
                                                title="Delete">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Row 2 -->
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div
                                            class="w-14 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                            <img alt="Post thumbnail" class="w-full h-full object-cover"
                                                data-alt="Person hands typing on a laptop with code on the screen"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDCdRniaShgB9zHH_1za7oHV4btgbjziGYdj-HLIrZpkCG5nFRQY3h45i9eC5VIq0GDS1e1a7HR_hYuQ-JlVbMHK8dz2cMxPvTjVMA_8DczZS_QUNY3bzZ7azMHE-wGtfRwmG5aon-AafO3dB9Catk1O6mliRYI0MIejBh8NT8AST7TEyAMkylSYtlnIO1szhAj2Z6YhR-zr-QyCojz_L2hyDLcDZzWZI0bGOc69ubwcBSQK1nDs19tgxE8YXaYI-4dv2AxQuree4A" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-sm line-clamp-1">Mastering Rust in 30 Days</p>
                                        <p class="text-xs text-slate-500 mt-1">Published Oct 24, 2023</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase">Programming</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <img alt="Author" class="w-6 h-6 rounded-full"
                                                data-alt="Profile icon of a male software developer"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBVE3C960ZXy7cA1O_rw-3fwHNvLkb6YXWNTzs8pSbY6YuVAams6JKzzm4-XpRYn0mdk_D2k9OneuYL5UrueKbhzz8ajwd9WIBBpxp2H6TDuaPITqowTkTFusjRhIWr7rYgwYeT9ate5uVPNVHTNk2pjd_XYUn9Az6y1lSfUOBeDqPOa5bgp-lyFizLrfblikAiOwY0yOMmoTajU5aUmyNrOxrECcIA2SMtOSPhV_R9GYKEY7mBTp4frhxP6vNmjnK0UjOE061YBiA" />
                                            <span class="text-sm font-medium">David Chen</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                class="p-2 text-slate-400 hover:text-indigo-accent hover:bg-indigo-600/10 rounded-lg transition-colors"
                                                title="View">
                                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                                            </button>
                                            <button
                                                class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                title="Edit">
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </button>
                                            <button
                                                class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors"
                                                title="Delete">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Row 3 -->
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div
                                            class="w-14 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                            <img alt="Post thumbnail" class="w-full h-full object-cover"
                                                data-alt="Stock market charts and data on a monitor"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCKZ_056oO7MyARJepxzipdHtxKIxlzaun6Kb8bw4YAetyDf0xXo6npJO1XUpcqsPN1NVI6o9jIkVYLxkRyzkAzKcCpc_8d1sBe6ObpKYbMIzXnBjstLnOfvOnFmQr3Rkxq_ea6bn1Eq9E0g5zt68QkXBAW7WcjWtG50yp0q8V5YCHPIjIXIzwpL5IK5g4WmSr2mxOsb3d-dN-Y5MQMCjAuLvn72i_wYtFD8ukV6f6JsTTDGd7x1XBjgfGe9dpojQEZc5rBAk79gXE" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-sm line-clamp-1">Startup Funding Trends 2024</p>
                                        <p class="text-xs text-slate-500 mt-1">Updated 1 day ago</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 uppercase">Business</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <img alt="Author" class="w-6 h-6 rounded-full"
                                                data-alt="Profile icon of a male business executive"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB8BHp9sOmbRJiALtAzAqgHfo0DC1DDk_bT_53FFCU9qP4PTGmpF8l2ZkNl-zWcycntcUGqI6k2y7u36Kga42baJCz92PmsIDds5z4kWgu_6DQnKOtmTc5DazSe_ewE83taPxg55wO06UFvvrMqQAZA-xANew-Z6j9LCpC_o_wyxePQ0Nr9OCqfIh0TXb1bw3vtcx735UN2yZetvrec5RqKawcWl_wVniOHw-zcPyftyRR4szwNOAKi_rrRBH7Otdw8oSgt_nR_aKY" />
                                            <span class="text-sm font-medium">Michael Ross</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                class="p-2 text-slate-400 hover:text-indigo-accent hover:bg-indigo-600/10 rounded-lg transition-colors"
                                                title="View">
                                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                                            </button>
                                            <button
                                                class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                title="Edit">
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </button>
                                            <button
                                                class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors"
                                                title="Delete">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Row 4 -->
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div
                                            class="w-14 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                            <img alt="Post thumbnail" class="w-full h-full object-cover"
                                                data-alt="Minimalist graphic design layout on a computer screen"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAg7JJPTV8QIosmwsKdIxUYWEeMuLgNFqVuIX4pXoANlA72Q4Pmlm93J8E0xa1Hoh9bAvGBo_09ES6Tahk07516Uhr_PcK0u05wgD-ax3e3edO-YTiHOAyoOx_TReWmAxvBOT8A3-9TP0qgrf1jIK82DQIW9UpoCJRXwoUWuJF-d-2E52PieSC956NCyM0OXVKRSDAQWCD5ViFgUka_PAmOgrVoKyboqFnFlC7Pn01TTBxInjpzJRXSodVMNifTOBhR87OO5PvLmW0" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-sm line-clamp-1">Minimalism in Web Design</p>
                                        <p class="text-xs text-slate-500 mt-1">Draft</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full bg-amber-100 text-amber-700 uppercase">Design</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <img alt="Author" class="w-6 h-6 rounded-full"
                                                data-alt="Profile icon of a female UI/UX designer"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB9n6bMCTJO4YbLJEb3G40NmjQBPhD-14-jlIeHLVYHaCcTuB-oI1yN5D2rsli4lw1gwj6dGvtKa0jTg6nbaI8Mbx2B8itpIfFDCyEHSqArzSdqYfWbuIZBxP2la93sSlMhhrxt1dDGZvnCL_zWP0pxHiDo3GcAeD6owWuAim-PXltROJ4QVYgRKVFzV4xdsEZfxCKzM6L4ORTqtWIVguiBHMfPsLfWqt8Dt_JDHzLptH8R02p_g0GkXILXk6Q_8jXxLQ9BEFZT1WM" />
                                            <span class="text-sm font-medium">Elena Gray</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                class="p-2 text-slate-400 hover:text-indigo-accent hover:bg-indigo-600/10 rounded-lg transition-colors"
                                                title="View">
                                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                                            </button>
                                            <button
                                                class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                title="Edit">
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </button>
                                            <button
                                                class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors"
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
                    <div class="px-6 py-4 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                        <p class="text-xs text-slate-500">Showing 1 to 4 of 48 entries</p>
                        <div class="flex gap-1">
                            <button
                                class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-700 transition-colors">
                                <span class="material-symbols-outlined text-sm">chevron_left</span>
                            </button>
                            <button class="px-3 py-1 rounded-lg bg-primary text-white text-xs font-bold">1</button>
                            <button
                                class="px-3 py-1 rounded-lg hover:bg-white dark:hover:bg-slate-700 text-xs font-medium">2</button>
                            <button
                                class="px-3 py-1 rounded-lg hover:bg-white dark:hover:bg-slate-700 text-xs font-medium">3</button>
                            <span class="px-2 py-1 text-slate-400 text-xs">...</span>
                            <button
                                class="px-3 py-1 rounded-lg hover:bg-white dark:hover:bg-slate-700 text-xs font-medium">12</button>
                            <button
                                class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-700 transition-colors">
                                <span class="material-symbols-outlined text-sm">chevron_right</span>
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
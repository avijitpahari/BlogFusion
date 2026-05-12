<?php include "../include/session.php"; 
requireAuthor();
include "../include/db.php";
include "../include/author_nav_sidebar.php";



?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Luminous | Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-secondary-container": "#fffbff",
                        "surface-container-high": "#ede5f4",
                        "on-tertiary-container": "#ffdde7",
                        "on-primary": "#ffffff",
                        "surface-container-low": "#f9f1ff",
                        "surface-container": "#f3ebfa",
                        "primary-fixed-dim": "#d2bbff",
                        "primary": "#630ed4",
                        "tertiary": "#9b005c",
                        "on-secondary-fixed": "#0f0069",
                        "on-tertiary-fixed-variant": "#8c0053",
                        "on-primary-container": "#ede0ff",
                        "outline-variant": "#ccc3d8",
                        "inverse-primary": "#d2bbff",
                        "on-primary-fixed": "#25005a",
                        "background": "#fef7ff",
                        "primary-fixed": "#eaddff",
                        "inverse-surface": "#332f39",
                        "tertiary-fixed-dim": "#ffb0cd",
                        "primary-container": "#7c3aed",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-highest": "#e8dfee",
                        "on-primary-fixed-variant": "#5a00c6",
                        "inverse-on-surface": "#f6eefc",
                        "secondary-fixed": "#e2dfff",
                        "on-tertiary-fixed": "#3e0022",
                        "surface-bright": "#fef7ff",
                        "secondary": "#4b41e1",
                        "surface-tint": "#732ee4",
                        "tertiary-container": "#bf2076",
                        "on-surface-variant": "#4a4455",
                        "outline": "#7b7487",
                        "surface-dim": "#dfd7e6",
                        "secondary-fixed-dim": "#c3c0ff",
                        "on-surface": "#1d1a24",
                        "on-secondary-fixed-variant": "#3323cc",
                        "on-tertiary": "#ffffff",
                        "tertiary-fixed": "#ffd9e4",
                        "surface": "#fef7ff",
                        "on-error-container": "#93000a",
                        "surface-variant": "#e8dfee",
                        "error": "#ba1a1a",
                        "on-background": "#1d1a24",
                        "secondary-container": "#645efb",
                        "on-secondary": "#ffffff",
                        "on-error": "#ffffff",
                        "error-container": "#ffdad6"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Public Sans", "sans-serif"],
                        "body": ["Public Sans", "sans-serif"],
                        "label": ["Public Sans", "sans-serif"]
                    }
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body class="bg-background text-on-background selection:bg-primary-fixed selection:text-on-primary-fixed">
    <!-- Sidebar Navigation -->
    <?= author_slidebar('dashboard') ?>
    <!-- Top App Bar -->
    <?= author_navbar(); ?>
    <!-- Main Content Canvas -->
    <main class="md:ml-64 pt-20 min-h-screen px-6 pb-12 transition-all duration-300">
        <!-- Welcome Header -->
        <header class="mb-8 flex flex-col gap-1">
            <h1 class="text-3xl font-bold tracking-tight text-on-surface">Good Morning, Alex</h1>
            <p class="text-on-surface-variant font-medium">Here's what's happening with your content today.</p>
        </header>
        <!-- Stats Bento Grid -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div
                class="bg-surface-container-lowest p-6 rounded-xl flex flex-col gap-4 shadow-sm border border-outline-variant/10 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10">
                    <div class="w-12 h-12 rounded-lg bg-primary-fixed/30 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-2xl" data-icon="post_add">post_add</span>
                    </div>
                    <span class="text-xs font-bold text-primary px-2 py-1 bg-primary-fixed/20 rounded-full">+12% this
                        week</span>
                </div>
                <div class="z-10">
                    <h3 class="text-on-surface-variant text-sm font-semibold uppercase tracking-wider">Total Posts</h3>
                    <p class="text-4xl font-extrabold text-on-surface mt-1">1,284</p>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-9xl" data-icon="post_add"
                        style="font-variation-settings: 'FILL' 1;">post_add</span>
                </div>
            </div>
            <div
                class="bg-surface-container-lowest p-6 rounded-xl flex flex-col gap-4 shadow-sm border border-outline-variant/10 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10">
                    <div
                        class="w-12 h-12 rounded-lg bg-tertiary-fixed/30 flex items-center justify-center text-tertiary">
                        <span class="material-symbols-outlined text-2xl" data-icon="forum">forum</span>
                    </div>
                    <span class="text-xs font-bold text-tertiary px-2 py-1 bg-tertiary-fixed/20 rounded-full">+8.4%
                        today</span>
                </div>
                <div class="z-10">
                    <h3 class="text-on-surface-variant text-sm font-semibold uppercase tracking-wider">Total Comments
                    </h3>
                    <p class="text-4xl font-extrabold text-on-surface mt-1">45,902</p>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-9xl" data-icon="forum"
                        style="font-variation-settings: 'FILL' 1;">forum</span>
                </div>
            </div>
            <div
                class="bg-surface-container-lowest p-6 rounded-xl flex flex-col gap-4 shadow-sm border border-outline-variant/10 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10">
                    <div
                        class="w-12 h-12 rounded-lg bg-secondary-fixed/30 flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-2xl" data-icon="favorite">favorite</span>
                    </div>
                    <span class="text-xs font-bold text-secondary px-2 py-1 bg-secondary-fixed/20 rounded-full">+24%
                        avg</span>
                </div>
                <div class="z-10">
                    <h3 class="text-on-surface-variant text-sm font-semibold uppercase tracking-wider">Total Likes</h3>
                    <p class="text-4xl font-extrabold text-on-surface mt-1">124.8k</p>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-9xl" data-icon="favorite"
                        style="font-variation-settings: 'FILL' 1;">favorite</span>
                </div>
            </div>
        </section>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Recent Posts Asymmetric Layout -->
            <section class="lg:col-span-8 flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-on-surface">Recent Posts</h2>
                    <button class="text-sm font-semibold text-primary hover:underline">View All Posts</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="col-span-1 sm:col-span-2 group cursor-pointer">
                        <div
                            class="bg-surface-container-low rounded-xl p-6 flex flex-col md:flex-row gap-6 hover:bg-surface-container transition-colors">
                            <div class="w-full md:w-48 h-48 rounded-lg overflow-hidden flex-shrink-0 bg-slate-200">
                                <img alt="Post thumbnail"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    data-alt="vibrant neon city lights at night with high contrast and futuristic aesthetic"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAaApYUgtdi4MDQqdVTDiiTfZv__wrx2iwKHTbOBPegYwuTGjT2Zukv2TIcOspTNT5RBvlUIAmEx4wJ7LmwDFYZoYJ0i1Yk9kioYzNmm7LU_bUWg9StGAt1IEWtrQnqrVpeNrFFcUqMRq1P29DRoLqvngl37bMkENNcHCik1BuXAwcsAnDEqWxTUOvgHHb28etUZe5S5PK7UyYCxLrwwJKX53bdHTjVorn9OKrgcK4EHf98Z-K4PF4Igozt1dXxApzwVGS8tuH3Bt8" />
                            </div>
                            <div class="flex flex-col justify-between py-1">
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="px-2 py-0.5 bg-tertiary-container text-on-tertiary rounded text-[10px] font-bold uppercase tracking-widest">Technology</span>
                                        <span class="text-xs text-on-surface-variant font-medium">Published 2h
                                            ago</span>
                                    </div>
                                    <h3 class="text-2xl font-bold text-on-surface leading-tight">The Future of
                                        Generative UI: Designing Beyond Components</h3>
                                    <p class="text-on-surface-variant text-sm line-clamp-2">Exploring how artificial
                                        intelligence is reshaping the way we think about user interface modularity and
                                        dynamic layout generation in modern applications.</p>
                                </div>
                                <div class="flex items-center gap-6 mt-4">
                                    <span class="flex items-center gap-1 text-xs font-semibold text-on-surface-variant">
                                        <span class="material-symbols-outlined text-lg"
                                            data-icon="visibility">visibility</span> 12.4k
                                    </span>
                                    <span class="flex items-center gap-1 text-xs font-semibold text-on-surface-variant">
                                        <span class="material-symbols-outlined text-lg"
                                            data-icon="comment">comment</span> 84
                                    </span>
                                    <span class="flex items-center gap-1 text-xs font-semibold text-on-surface-variant">
                                        <span class="material-symbols-outlined text-lg" data-icon="share">share</span>
                                        312
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="group cursor-pointer">
                        <div
                            class="bg-surface-container-low rounded-xl p-4 flex flex-col gap-4 hover:bg-surface-container transition-colors h-full">
                            <div class="w-full aspect-video rounded-lg overflow-hidden bg-slate-200">
                                <img alt="Post thumbnail"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    data-alt="clean minimalist workspace with a large window and sunlight filtering through sheer curtains"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDwsxMvun9jZ9yLLrCGUx1TKiCdF2LwspDk4OcsXT3fB530mEbCGD1BCEI8kJfpS6lBGWIGXYfCIOeRXhO53Wc4-6Wgp98HBpovNLrMntMSzf84OxT0FKiDCUn8Htyyt2dbl2dSViBKGRNoOkPYG_Bjg7-KucbU60KphLaMW85jN1mpPakNNoDycYdpzFNM8d7ruoJX__rYdCm1ER4S-IXQJinK27NQB7WBfSG_5BUbh-kVvIScQcEhPhEIvklJRpcSBO8B_Yx-v3U" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="px-2 py-0.5 bg-secondary-container text-on-secondary rounded text-[10px] font-bold uppercase tracking-widest">Creative</span>
                                    <span class="text-xs text-on-surface-variant font-medium">8h ago</span>
                                </div>
                                <h3 class="text-lg font-bold text-on-surface leading-tight">10 Principles of Luminous
                                    Design</h3>
                                <p class="text-on-surface-variant text-xs line-clamp-2">How to use light and tonal depth
                                    to create sophisticated editorial layouts.</p>
                            </div>
                        </div>
                    </div>
                    <div class="group cursor-pointer">
                        <div
                            class="bg-surface-container-low rounded-xl p-4 flex flex-col gap-4 hover:bg-surface-container transition-colors h-full">
                            <div class="w-full aspect-video rounded-lg overflow-hidden bg-slate-200">
                                <img alt="Post thumbnail"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    data-alt="close up of premium white textured paper with elegant typography and deep shadows"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDeYJuRUj3fifqQ9OVhWTGCh2tSfZXqG5XPdylVB2UFGUdwyNd_XmM6bje6zxvUpg6buSwxjb0gYJ8JuTATtYkc10_gk-ugOylkkISl8TMGmRWNV7Cdn3lvxK64aGDwPCwTjNGdUhst7Vz0bziHKVUmUMyNEHpPp9gFIfJBpXLor43MspWHl9WbruaLIi4RuiX_wWVqKjKztryTQjgwYaaMZH5N1db2srV0_3lL-1ArCjzjEd19OzQiCs67Cgnu9CJFngPNjBbeFGU" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="px-2 py-0.5 bg-primary-container text-on-primary rounded text-[10px] font-bold uppercase tracking-widest">Editorial</span>
                                    <span class="text-xs text-on-surface-variant font-medium">Yesterday</span>
                                </div>
                                <h3 class="text-lg font-bold text-on-surface leading-tight">Navigating the New Media
                                    Landscape</h3>
                                <p class="text-on-surface-variant text-xs line-clamp-2">Strategies for maintaining
                                    editorial integrity in the age of rapid content cycles.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Recent Comments Sidebar Feed -->
            <section class="lg:col-span-4 flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-on-surface">Activity Feed</h2>
                    <button class="p-2 hover:bg-surface-container-high rounded-full transition-colors">
                        <span class="material-symbols-outlined text-xl" data-icon="more_horiz">more_horiz</span>
                    </button>
                </div>
                <div class="flex flex-col gap-6">
                    <div class="flex gap-4 group">
                        <div class="relative flex-shrink-0">
                            <img alt="Sarah J." class="w-10 h-10 rounded-lg bg-surface-container-high"
                                data-alt="portrait of a young woman with smiling eyes and soft studio lighting"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAU8idqd-ioxhVvU28LZWUFC1m4tcRDnlNvfvwcU1K_cr3PdWLEtKV4YWZPN578DSkGTnB7AMaIrE4iTOEgqEWZZge1pSIxnQYlRGOwKJQ0th-uKq2X_V4ZHDuKeL3NFJPuL4jfdlwdT9SNJNzamX60LaXAWOhF5MW9cFyEpsmEVOytSAfNQ8I7dX0ITbgSVkLGNXcIGEvCXq145lfbfwP2J6VQlwP_0hsHuXJZVlbWqDgt7kXdVtn5Dsl0JofB7DhJDMj05PiYVRQ" />
                            <div
                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-primary text-white rounded-full flex items-center justify-center text-[8px]">
                                <span class="material-symbols-outlined text-[10px]" data-icon="mode_comment"
                                    style="font-variation-settings: 'FILL' 1;">mode_comment</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-on-surface">Sarah Jenkins</span>
                                <span class="text-[10px] text-on-surface-variant">15m ago</span>
                            </div>
                            <p class="text-sm text-on-surface-variant leading-relaxed">"This approach to component
                                architecture is truly game-changing. I especially loved the section on dynamic grids!"
                            </p>
                            <div class="flex items-center gap-4 mt-1">
                                <button class="text-xs font-bold text-primary">Reply</button>
                                <button class="text-xs font-medium text-on-surface-variant">Approve</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-4 group">
                        <div class="relative flex-shrink-0">
                            <img alt="Marcus T." class="w-10 h-10 rounded-lg bg-surface-container-high"
                                data-alt="professional headshot of a man with spectacles and warm natural lighting"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuApvk3O3Hb9b0QdFDlE4CwEKDq02GgF8Mfu12_ZoAPTXewBEojtQIQAXiBJ2Dyiq953WkVySQ7effaG_gXNESMW2Vhu-gkiFZYCWzWIcRIoR5KhWtotR_Q1QdwQMRd_FrupT2EIXyfbWbhXcV19rxuiJYjIUii6xhK1nczj-7wlrxiN9yl_9gfrD1rEPPkdvHRKZkTBiqrBQadl8t-XFx8Oq0xT96qBG4cjjHamqF-FjB8TSrzMkmtLQ-cumhd3h0JgCsffmrtx0Ok" />
                            <div
                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-tertiary text-white rounded-full flex items-center justify-center text-[8px]">
                                <span class="material-symbols-outlined text-[10px]" data-icon="favorite"
                                    style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-on-surface">Marcus Thorne</span>
                                <span class="text-[10px] text-on-surface-variant">1h ago</span>
                            </div>
                            <p class="text-sm text-on-surface-variant leading-relaxed">Liked your post <span
                                    class="font-semibold italic">"The Future of Generative UI"</span> and shared it with
                                their network.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 group">
                        <div class="relative flex-shrink-0">
                            <img alt="Elena V." class="w-10 h-10 rounded-lg bg-surface-container-high"
                                data-alt="artistic portrait of a woman with vibrant lighting and creative background"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD1BowBy75yTp9vXh8Wr069c2s_6Gej7ug7wXIkik_dik2SfeMqcStCK072pel9y9KoRSvTiREzIxWenU2QzOd5oTeP8dda3Bh5vHaxHxb5cnJxQaOLJF5YWnduzgpQDGJihp1WTNwi4iq-amWKKwmWjJFAZY3VX01VTKWQKgQM5QTXTGOQhVSSNKBlkhfx0t90OJLM2aKbc7WmSRtoOeHms0atJkyt1EL7mCD5RGSkI5uOMQ-INyGDb9o1QUylvHACj007oDum2ZQ" />
                            <div
                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-primary text-white rounded-full flex items-center justify-center text-[8px]">
                                <span class="material-symbols-outlined text-[10px]" data-icon="mode_comment"
                                    style="font-variation-settings: 'FILL' 1;">mode_comment</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-on-surface">Elena Vance</span>
                                <span class="text-[10px] text-on-surface-variant">3h ago</span>
                            </div>
                            <p class="text-sm text-on-surface-variant leading-relaxed">"Could you elaborate more on the
                                accessibility implications of these dynamic layouts? Great read!"</p>
                            <div class="flex items-center gap-4 mt-1">
                                <button class="text-xs font-bold text-primary">Reply</button>
                                <button class="text-xs font-medium text-on-surface-variant">Approve</button>
                            </div>
                        </div>
                    </div>
                    <div class="bg-surface-container-low p-5 rounded-xl mt-4">
                        <h4 class="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-3">Today's
                            Goal</h4>
                        <div class="flex flex-col gap-3">
                            <div class="flex justify-between items-center text-sm font-bold text-on-surface">
                                <span>3 Posts Left</span>
                                <span>70%</span>
                            </div>
                            <div class="w-full h-2 bg-white rounded-full overflow-hidden">
                                <div class="w-[70%] h-full bg-gradient-to-r from-primary to-primary-container"></div>
                            </div>
                            <p class="text-[11px] text-on-surface-variant italic">Reach 10 posts this week to maintain
                                your Editor Elite status.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <!-- Contextual FAB - ONLY for Dashboard as per mandate -->
    <button
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-br from-primary to-primary-container text-white rounded-2xl shadow-xl flex items-center justify-center group hover:scale-105 active:scale-95 transition-all md:hidden">
        <span class="material-symbols-outlined text-2xl" data-icon="add">add</span>
    </button>
</body>

</html>
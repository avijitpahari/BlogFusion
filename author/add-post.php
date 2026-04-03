<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&amp;display=swap"
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
                    colors: {
                        "tertiary-container": "#bf2076",
                        "on-tertiary": "#ffffff",
                        "tertiary-fixed-dim": "#ffb0cd",
                        "outline": "#7b7487",
                        "on-secondary-fixed": "#0f0069",
                        "on-tertiary-fixed": "#3e0022",
                        "secondary-fixed": "#e2dfff",
                        "inverse-surface": "#332f39",
                        "secondary": "#4b41e1",
                        "outline-variant": "#ccc3d8",
                        "on-primary-fixed": "#25005a",
                        "tertiary": "#9b005c",
                        "surface-container": "#f3ebfa",
                        "background": "#fef7ff",
                        "on-tertiary-fixed-variant": "#8c0053",
                        "surface-variant": "#e8dfee",
                        "secondary-fixed-dim": "#c3c0ff",
                        "primary-fixed": "#eaddff",
                        "surface-container-lowest": "#ffffff",
                        "on-surface": "#1d1a24",
                        "inverse-primary": "#d2bbff",
                        "surface-dim": "#dfd7e6",
                        "surface-tint": "#732ee4",
                        "primary-fixed-dim": "#d2bbff",
                        "primary-container": "#7c3aed",
                        "secondary-container": "#645efb",
                        "on-secondary-container": "#fffbff",
                        "on-primary-container": "#ede0ff",
                        "on-primary": "#ffffff",
                        "on-primary-fixed-variant": "#5a00c6",
                        "error": "#ba1a1a",
                        "inverse-on-surface": "#f6eefc",
                        "on-surface-variant": "#4a4455",
                        "surface": "#fef7ff",
                        "on-tertiary-container": "#ffdde7",
                        "on-background": "#1d1a24",
                        "surface-container-high": "#ede5f4",
                        "surface-bright": "#fef7ff",
                        "primary": "#630ed4",
                        "on-error": "#ffffff",
                        "on-secondary-fixed-variant": "#3323cc",
                        "surface-container-highest": "#e8dfee",
                        "surface-container-low": "#f9f1ff",
                        "on-error-container": "#93000a",
                        "on-secondary": "#ffffff",
                        "error-container": "#ffdad6",
                        "tertiary-fixed": "#ffd9e4"
                    },
                    fontFamily: {
                        "headline": ["Public Sans"],
                        "body": ["Public Sans"],
                        "label": ["Public Sans"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
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

        .glass-header {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
        }
    </style>
</head>

<body class="bg-background text-on-surface">
    <!-- Sidebar Overlay (Mobile) -->
    <div class="fixed inset-0 bg-on-surface/50 z-40 hidden md:hidden transition-opacity duration-300"
        id="sidebar-overlay" onclick="toggleSidebar()"></div>
    <!-- Sidebar Navigation Drawer -->

    <aside
        class="fixed left-0 top-0 z-50 h-screen w-72 bg-surface-container-low py-8 flex flex-col gap-2 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out"
        id="sidebar">
        <div class="px-8 mb-10 flex items-center justify-between">
            <span class="text-2xl font-black text-[#630ed4] tracking-tighter">Blog Fusion</span>
            <button class="md:hidden text-primary p-1" onclick="toggleSidebar()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <nav class="flex flex-col gap-1 px-4">
            <a class="text-on-surface-variant hover:bg-surface-container-high rounded-full px-6 py-3 flex items-center gap-4 transition-all duration-300"
                href="#">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                <span class="text-sm font-medium tracking-wide uppercase">Dashboard</span>
            </a>
            <a class="bg-[#eaddff] text-[#5a00c6] rounded-full px-6 py-3 flex items-center gap-4 transition-all duration-300"
                href="#">
                <span class="material-symbols-outlined">add_box</span>
                <span class="text-sm font-medium tracking-wide uppercase">Add Post</span>
            </a>
            <a class="text-on-surface-variant hover:bg-surface-container-high rounded-full px-6 py-3 flex items-center gap-4 transition-all duration-300"
                href="#">
                <span class="material-symbols-outlined">article</span>
                <span class="text-sm font-medium tracking-wide uppercase">My Posts</span>
            </a>
            <a class="text-on-surface-variant hover:bg-surface-container-high rounded-full px-6 py-3 flex items-center gap-4 transition-all duration-300"
                href="#">
                <span class="material-symbols-outlined">forum</span>
                <span class="text-sm font-medium tracking-wide uppercase">Comments</span>
            </a>
            <a class="text-on-surface-variant hover:bg-surface-container-high rounded-full px-6 py-3 flex items-center gap-4 transition-all duration-300"
                href="#">
                <span class="material-symbols-outlined">account_circle</span>
                <span class="text-sm font-medium tracking-wide uppercase">Profile</span>
            </a>
            <div class="mt-auto pt-10">
                <a class="text-on-surface-variant hover:bg-surface-container-high rounded-full px-6 py-3 flex items-center gap-4 transition-all duration-300"
                    href="#">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="text-sm font-medium tracking-wide uppercase">Logout</span>
                </a>
            </div>
        </nav>
    </aside>
    <!-- Main Content -->
    <main class="ml-64 pt-24 p-8 min-h-screen">
        <!-- TopAppBar -->
        <header
            class="fixed top-0 right-0 left-0 md:left-72 z-40 bg-[#fef7ff]/70 backdrop-blur-xl shadow-[0_32px_32px_rgba(29,26,36,0.06)] h-16 flex items-center justify-between px-6">
            <div class="flex items-center gap-4">
                <!-- Mobile Menu Trigger -->
                <button
                    class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-colors"
                    onclick="toggleSidebar()">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <h1 class="text-xl font-bold text-on-surface tracking-tighter">Write a Blog</h1>
            </div>
            <div class="flex items-center gap-2 md:gap-4">
                <div class="hidden sm:flex bg-surface-container-highest rounded-lg px-3 py-1.5 items-center gap-2">
                    <span class="material-symbols-outlined text-on-surface-variant text-lg">search</span>
                    <input
                        class="bg-transparent border-none focus:ring-0 text-sm w-48 text-on-surface-variant placeholder:text-on-surface-variant/60"
                        placeholder="Search insights..." type="text" />
                </div>
                <button class="text-primary hover:bg-[#f3ebfa] p-2 rounded-full transition-colors">
                    <span class="material-symbols-outlined">dark_mode</span>
                </button>
                <div
                    class="h-10 w-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary font-bold overflow-hidden">
                    <img alt="User" class="w-full h-full object-cover" data-alt="Portrait of a male professional"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBahPq2yRzfjYEHmMJVAjHJ-L7pbK5Yo6vd07wOqPJpxpvdG1bAUhP91a9AfblKeGPpvefjP2UQb-EuCEXsNxOZQY9MxHsGnvQHMiTIYEbaRQECGC8oeiR1ilGbgPQ2OkZ_Wjx88Vp4AihxbM3daNZV4AwrX299WMvyrtDR75URxzr9V3C5iq6IawXW1DGWHzXLxVngonTpF4kvw9ELXEhZ7tbcj6qf0uhKpBdM3zXG_tkTxXSNsIvxrVYWPUlC8s77QnPEz3Stvxk" />
                </div>
            </div>
        </header>
        <div class="max-w-6xl mx-auto">
            <header class="mb-10">
                <h2 class="text-3xl font-bold tracking-tight text-on-surface">Add New Post</h2>
                <p class="text-on-surface-variant mt-1">Craft your next masterpiece for the digital world.</p>
            </header>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Editor Area -->
                <div class="lg:col-span-8 space-y-6">
                    <div class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm border border-outline-variant/10">
                        <input
                            class="w-full text-4xl font-extrabold border-none focus:ring-0 placeholder:text-zinc-300 text-on-surface p-0 mb-8 font-headline tracking-tight"
                            placeholder="Enter post title..." type="text" />
                        <div class="border-t border-zinc-100 pt-6">
                            <!-- Toolbar Placeholder -->
                            <div class="flex flex-wrap gap-2 mb-4 p-2 bg-surface-container-low rounded-xl">
                                <button class="p-2 hover:bg-white rounded-lg transition-colors"><span
                                        class="material-symbols-outlined text-sm"
                                        data-icon="format_bold">format_bold</span></button>
                                <button class="p-2 hover:bg-white rounded-lg transition-colors"><span
                                        class="material-symbols-outlined text-sm"
                                        data-icon="format_italic">format_italic</span></button>
                                <button class="p-2 hover:bg-white rounded-lg transition-colors"><span
                                        class="material-symbols-outlined text-sm"
                                        data-icon="format_list_bulleted">format_list_bulleted</span></button>
                                <button class="p-2 hover:bg-white rounded-lg transition-colors"><span
                                        class="material-symbols-outlined text-sm"
                                        data-icon="format_quote">format_quote</span></button>
                                <button class="p-2 hover:bg-white rounded-lg transition-colors"><span
                                        class="material-symbols-outlined text-sm" data-icon="link">link</span></button>
                                <div class="w-px h-6 bg-zinc-200 mx-2 self-center"></div>
                                <button class="p-2 hover:bg-white rounded-lg transition-colors"><span
                                        class="material-symbols-outlined text-sm"
                                        data-icon="image">image</span></button>
                                <button class="p-2 hover:bg-white rounded-lg transition-colors"><span
                                        class="material-symbols-outlined text-sm" data-icon="code">code</span></button>
                            </div>
                            <textarea
                                class="w-full border-none focus:ring-0 text-lg leading-relaxed text-on-surface placeholder:text-zinc-300 resize-none p-0"
                                placeholder="Start writing your content here..." rows="20"></textarea>
                        </div>
                    </div>
                </div>
                <!-- Sidebar Area -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Publish Settings -->
                    <section class="bg-surface-container p-6 rounded-2xl space-y-4">
                        <h3 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-4">Publish
                            Settings</h3>
                        <div class="space-y-3">
                            <button
                                class="w-full py-3 px-4 bg-gradient-to-r from-primary to-primary-container text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center space-x-2">
                                <span class="material-symbols-outlined text-sm" data-icon="publish">publish</span>
                                <span>Publish Now</span>
                            </button>
                            <button
                                class="w-full py-3 px-4 bg-white text-primary font-bold rounded-xl border border-primary/10 hover:bg-surface-container-high transition-all">
                                Save as Draft
                            </button>
                        </div>
                        <div class="pt-4 space-y-2 border-t border-outline-variant/20">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-on-surface-variant">Status:</span>
                                <span class="font-bold text-tertiary">Draft</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-on-surface-variant">Visibility:</span>
                                <span class="font-bold">Public</span>
                            </div>
                        </div>
                    </section>
                    <!-- Category & Tags -->
                    <section class="bg-surface-container-low p-6 rounded-2xl space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant mb-2">Category</label>
                            <select
                                class="w-full bg-white border-outline-variant/30 rounded-xl py-3 px-4 text-sm focus:ring-primary focus:border-primary">
                                <option>Select Category</option>
                                <option>Technology</option>
                                <option>Lifestyle</option>
                                <option>Design</option>
                                <option>Productivity</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant mb-2">Tags</label>
                            <div class="relative">
                                <input
                                    class="w-full bg-white border-outline-variant/30 rounded-xl py-3 px-4 text-sm focus:ring-primary focus:border-primary"
                                    placeholder="Add tags..." type="text" />
                                <button
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-primary font-bold text-xs">ADD</button>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <span
                                    class="px-3 py-1 bg-primary-fixed text-on-primary-fixed-variant text-[10px] font-bold rounded-full uppercase tracking-tighter">Writing</span>
                                <span
                                    class="px-3 py-1 bg-primary-fixed text-on-primary-fixed-variant text-[10px] font-bold rounded-full uppercase tracking-tighter">Digital</span>
                            </div>
                        </div>
                    </section>
                    <!-- Featured Image -->
                    <section class="bg-surface-container-low p-6 rounded-2xl">
                        <label class="block text-sm font-bold text-on-surface-variant mb-4">Featured Image</label>
                        <div
                            class="relative group cursor-pointer border-2 border-dashed border-outline-variant/30 rounded-2xl aspect-video overflow-hidden flex flex-col items-center justify-center bg-white/50 hover:bg-white transition-all">
                            <img alt="Placeholder featured image"
                                class="absolute inset-0 w-full h-full object-cover opacity-20 group-hover:opacity-40 transition-opacity"
                                data-alt="Abstract minimal desk background for blog post"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDxLrYm9V-ga5euNadVlcjPXsXbEUzzJlHJ9BCKvnz0yNNAdyt4sNG9NvGm-5i6WWOoGEdOgccWmG8dsKSvlRucwGKB4_kyPQOFBNfLWoEZBQYgWVUtRVWPaZfktsKXhVEN9pftsfcAa-ZDp-MWoFVD94559OyTRI384qLky_BfeKxqj3OjaM9ByJLZR_mJBx8vCH6YTxuJFjblazrzDwDWKimubLaT_i35CKywXH8pMa4h7v5a7XPyQH2u_ZS9Xcnr8rH6BXpTKDY" />
                            <div class="relative z-10 flex flex-col items-center">
                                <span class="material-symbols-outlined text-3xl text-primary mb-2"
                                    data-icon="cloud_upload">cloud_upload</span>
                                <span class="text-xs font-bold text-on-surface-variant">Upload Image</span>
                                <span class="text-[10px] text-zinc-400 mt-1">Recommended 1200x630px</span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
    <!-- Floating Action Button -->
    <button
        class="fixed bottom-8 right-8 w-14 h-14 bg-gradient-to-br from-primary to-primary-container text-on-primary rounded-xl shadow-[0_12px_24px_rgba(99,14,212,0.3)] flex items-center justify-center hover:scale-105 active:scale-95 transition-all duration-200 z-30">
        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">add</span>
    </button>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('mobile-active');
            overlay.classList.toggle('hidden');
            overlay.classList.toggle('active');
        }
    </script>
</body>

</html>
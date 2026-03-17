<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Blog Fusion - Navigation</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&amp;display=swap"
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
                        "primary": "#7C3AED",
                        "background-light": "#f8f6f6",
                        "background-dark": "#221610",
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
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark min-h-screen">
    <!-- Sticky Top Navigation Bar -->
    <nav
        class="sticky top-0 z-50 w-full bg-white/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Logo -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white">
                        <span class="material-symbols-outlined text-2xl">auto_stories</span>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-slate-100 hidden md:block">
                        Blog<span class="text-primary">Fusion</span>
                    </span>
                </div>
                <!-- Center: Search Bar -->
                <div class="flex-1 max-w-md px-8 hidden sm:block">
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <span class="material-symbols-outlined text-xl">search</span>
                        </div>
                        <input
                            class="block w-full pl-10 pr-3 py-2 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm"
                            placeholder="Search articles, topics..." type="text" />
                    </div>
                </div>
                <!-- Right: Nav Links & Actions -->
                <div class="flex items-center gap-2 md:gap-6">
                    <!-- Navigation Links -->
                    <div class="hidden lg:flex items-center gap-6">
                        <a class="text-sm font-medium text-primary border-b-2 border-primary pb-0.5" href="#">Home</a>
                        <a class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors"
                            href="#">Explore</a>
                        <a class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors"
                            href="#">Bookmarks</a>
                    </div>
                    <div class="h-6 w-px bg-slate-200 dark:bg-slate-800 hidden lg:block mx-2"></div>
                    <!-- Create Button -->
                    <button
                        class="hidden md:flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all shadow-sm shadow-primary/20">
                        <span class="material-symbols-outlined text-sm">add_circle</span>
                        <span>Create Post</span>
                    </button>
                    <!-- Mobile Search Icon (only visible on small screens) -->
                    <button class="sm:hidden p-2 text-slate-600 dark:text-slate-400">
                        <span class="material-symbols-outlined">search</span>
                    </button>
                    <!-- User Profile -->
                    <div class="flex items-center gap-3 pl-2 cursor-pointer group">
                        <div class="relative">
                            <img alt="User profile avatar"
                                class="h-9 w-9 rounded-full object-cover border-2 border-transparent group-hover:border-primary transition-all shadow-sm"
                                src="upload/profile-images/1773478374Screenshot.png">
                            
                            <div class="absolute bottom-0 right-0 h-3 w-3 bg-green-500 border-2 border-white dark:border-background-dark rounded-full">
                            </div>
                        </div>
                        <span
                            class="material-symbols-outlined text-slate-400 text-lg hidden sm:block">expand_more</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</body>

</html>
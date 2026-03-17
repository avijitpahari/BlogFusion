<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&amp;display=swap"
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
                        "primary": "#7C3AED",
                        "secondary": "#4F46E5",
                        "accent": "#EC4899",
                        "background-light": "#F3F4F6",
                        "background-dark": "#111827",
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

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100">
    <!-- Main Content Spacer to push footer to bottom -->
    <div class="flex flex-col justify-end">
        <!-- Footer Component Start -->
        <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">
            <div class="max-w-7xl mx-auto px-6 pt-16 pb-8">
                <!-- Newsletter Section -->
                <div
                    class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center pb-12 border-b border-slate-200 dark:border-slate-800">
                    <div class="max-w-md">
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mb-2">Subscribe to
                            our Newsletter</h2>
                        <p class="text-slate-600 dark:text-slate-400">Stay updated with the latest stories and trends
                            from Blog Fusion.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input
                            class="flex-1 px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                            placeholder="Enter your email" type="email" />
                        <button
                            class="bg-primary hover:bg-primary/90 text-white font-semibold px-6 py-3 rounded-xl transition-colors flex items-center justify-center gap-2">
                            <span>Subscribe</span>
                            <span class="material-symbols-outlined text-lg">send</span>
                        </button>
                    </div>
                </div>
                <!-- Footer Links Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 py-12">
                    <!-- Brand Column -->
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center text-white">
                                <span class="material-symbols-outlined font-bold">bolt</span>
                            </div>
                            <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">Blog<span
                                    class="text-primary">Fusion</span></span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Connecting ideas and people. Blog Fusion is your go-to destination for high-quality insights
                            on technology, design, and modern development.
                        </p>
                        <div class="flex gap-4 mt-2">
                            <a class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all"
                                href="#">
                                <span class="material-symbols-outlined text-xl">share</span>
                            </a>
                            <a class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all"
                                href="#">
                                <span class="material-symbols-outlined text-xl">public</span>
                            </a>
                            <a class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all"
                                href="#">
                                <span class="material-symbols-outlined text-xl">thumb_up</span>
                            </a>
                        </div>
                    </div>
                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-6">Quick
                            Links</h3>
                        <ul class="space-y-4">
                            <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors flex items-center gap-2"
                                    href="#"><span class="material-symbols-outlined text-sm">chevron_right</span>
                                    Home</a></li>
                            <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors flex items-center gap-2"
                                    href="#"><span class="material-symbols-outlined text-sm">chevron_right</span>
                                    Explore</a></li>
                            <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors flex items-center gap-2"
                                    href="#"><span class="material-symbols-outlined text-sm">chevron_right</span>
                                    About</a></li>
                            <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors flex items-center gap-2"
                                    href="#"><span class="material-symbols-outlined text-sm">chevron_right</span>
                                    Contact</a></li>
                        </ul>
                    </div>
                    <!-- Categories -->
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-6">
                            Categories</h3>
                        <ul class="space-y-4">
                            <li><a class="text-slate-600 dark:text-slate-400 hover:text-accent dark:hover:text-accent transition-colors"
                                    href="#">Technology</a></li>
                            <li><a class="text-slate-600 dark:text-slate-400 hover:text-accent dark:hover:text-accent transition-colors"
                                    href="#">Design</a></li>
                            <li><a class="text-slate-600 dark:text-slate-400 hover:text-accent dark:hover:text-accent transition-colors"
                                    href="#">Development</a></li>
                            <li><a class="text-slate-600 dark:text-slate-400 hover:text-accent dark:hover:text-accent transition-colors"
                                    href="#">Marketing</a></li>
                        </ul>
                    </div>
                    <!-- Support/Contact Info -->
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-6">Get
                            in Touch</h3>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-primary">mail</span>
                                <span class="text-slate-600 dark:text-slate-400">hello@blogfusion.com</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-primary">location_on</span>
                                <span class="text-slate-600 dark:text-slate-400">123 Creative Lane, Innovation
                                    District<br />Tech City, TC 10101</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Bottom Bar -->
                <div
                    class="pt-8 border-t border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-slate-500 dark:text-slate-500 text-sm">
                        © 2024 Blog Fusion. All rights reserved.
                    </p>
                    <div class="flex gap-8">
                        <a class="text-slate-500 dark:text-slate-500 hover:text-primary text-sm transition-colors"
                            href="#">Privacy Policy</a>
                        <a class="text-slate-500 dark:text-slate-500 hover:text-primary text-sm transition-colors"
                            href="#">Terms of Service</a>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Footer Component End -->
    </div>
</body>

</html>
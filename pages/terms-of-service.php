<?php
session_start();
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/db.php';
include_once BASE_PATH . 'include/functions.php';

$siteSettings = fetch_site_settings();
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>upload/site_image/logo2.png" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?php echo escape_html($siteSettings['site_name'] ?? 'Blog Fusion'); ?> - Terms of Service</title>
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
                        "primary": "#7C3AED",
                        "secondary": "#4F46E5",
                        "accent": "#EC4899",
                        "bg-light": "#F9FAFB",
                        "bg-dark": "#0F0E17",
                        "card-light": "#FFFFFF",
                        "card-dark": "#161524",
                        "border-light": "#E5E7EB",
                        "border-dark": "#242335",
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "2xl": "1rem",
                        "3xl": "1.5rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Public Sans', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        html.dark .glass {
            background: rgba(22, 21, 36, 0.7);
        }
    </style>
</head>
<body class="bg-bg-light dark:bg-bg-dark text-slate-800 dark:text-slate-200 min-h-screen flex flex-col pt-20">

    <!-- NAVBAR -->
    <nav id="main-nav"
        class="fixed top-0 inset-x-0 z-50 border-b border-slate-200 dark:border-border-dark transition-all bg-white/80 dark:bg-bg-dark/80 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-18">

                <!-- Logo -->
                <a href="<?php echo site_url('index.php'); ?>" class="flex items-center gap-2.5 group flex-shrink-0">
                    <?php
                    $nav_logo = $siteSettings['logo'] ?? 'upload/site_image/logo1.png';
                    if (!preg_match('/^https?:\/\//i', $nav_logo)) {
                        $nav_logo = site_url($nav_logo);
                    }
                    ?>
                    <img class="h-16 w-auto max-w-full object-contain" src="<?php echo $nav_logo; ?>" alt="BlogFusion" />
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden lg:flex items-center gap-1">
                    <a href="<?php echo site_url('index.php'); ?>#hero"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-primary bg-primary/8 hover:bg-primary/12 transition-colors">Home</a>
                    <a href="<?php echo site_url('index.php'); ?>#latest"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-primary hover:bg-primary/5 transition-colors">Posts</a>
                    <a href="<?php echo site_url('index.php'); ?>#categories"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-primary hover:bg-primary/5 transition-colors">Categories</a>
                    <a href="<?php echo site_url('index.php'); ?>#trending"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-primary hover:bg-primary/5 transition-colors">Trending</a>
                    <a href="<?php echo site_url('index.php'); ?>#features"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-primary hover:bg-primary/5 transition-colors">Features</a>
                </div>

                <!-- Right actions -->
                <div class="flex items-center gap-2">
                    <!-- Dark mode toggle -->
                    <button id="dark-toggle" aria-label="Toggle dark mode"
                        class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/8 transition-colors">
                        <span class="material-symbols-outlined text-xl" id="dark-icon">dark_mode</span>
                    </button>
                    <a href="<?php echo site_url('pages/login.php'); ?>"
                        class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-border-dark hover:border-primary hover:text-primary transition-all">
                        Sign In
                    </a>
                    <a href="<?php echo site_url('pages/register.php'); ?>"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold text-white transition-all hover:scale-105 shadow-lg shadow-primary/25"
                        style="background: linear-gradient(135deg, #7C3AED, #4F46E5);">
                        <span class="material-symbols-outlined text-sm">bolt</span>
                        Get Started
                    </a>
                    <!-- Mobile menu btn -->
                    <button id="mobile-toggle"
                        class="lg:hidden w-9 h-9 flex items-center justify-center rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/8">
                        <span class="material-symbols-outlined" id="mobile-icon">menu</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu"
            class="lg:hidden border-t border-slate-100 dark:border-border-dark bg-white dark:bg-card-dark">
            <div class="px-4 py-4 flex flex-col gap-1">
                <a href="<?php echo site_url('index.php'); ?>#hero"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold text-primary bg-primary/8">Home</a>
                <a href="<?php echo site_url('index.php'); ?>#latest"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5">Posts</a>
                <a href="<?php echo site_url('index.php'); ?>#categories"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5">Categories</a>
                <a href="<?php echo site_url('index.php'); ?>#trending"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5">Trending</a>
                <a href="<?php echo site_url('index.php'); ?>#features"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5">Features</a>
                <div class="border-t border-slate-100 dark:border-border-dark mt-2 pt-3 grid grid-cols-2 gap-2">
                    <a href="<?php echo site_url('pages/login.php'); ?>"
                        class="text-center py-2.5 rounded-xl text-sm font-semibold border border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300">Sign
                        In</a>
                    <a href="<?php echo site_url('pages/register.php'); ?>"
                        class="text-center py-2.5 rounded-xl text-sm font-bold text-white"
                        style="background: linear-gradient(135deg,#7C3AED,#4F46E5);">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <main class="flex-grow max-w-4xl mx-auto px-6 py-12 w-full">
        <div class="glass border border-border-light dark:border-border-dark p-8 md:p-12 rounded-3xl shadow-xl space-y-8">
            <header class="border-b border-border-light dark:border-border-dark pb-6">
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">Terms of Service</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Last Updated: <?php echo date("F d, Y"); ?></p>
            </header>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">1. Agreement to Terms</h2>
                <p class="leading-relaxed text-slate-600 dark:text-slate-300">
                    These Terms of Service constitute a legally binding agreement made between you, whether personally or on behalf of an entity ("you") and <?php echo escape_html($siteSettings['site_name'] ?? 'Blog Fusion'); ?> ("we", "us", or "our"), concerning your access to and use of the website as well as any other media form, media channel, mobile website or mobile application related, linked, or otherwise connected thereto.
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">2. User Representations & Account Security</h2>
                <p class="leading-relaxed text-slate-600 dark:text-slate-300">
                    By using the Services, you represent and warrant that:
                </p>
                <ul class="list-disc pl-6 space-y-2 text-slate-600 dark:text-slate-300">
                    <li>All registration information you submit will be true, accurate, current, and complete.</li>
                    <li>You will maintain the accuracy of such information and promptly update it as necessary.</li>
                    <li>You will keep your password confidential and will be responsible for all use of your account and password.</li>
                    <li>Your use of the Services does not violate any applicable law or regulation.</li>
                </ul>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">3. User Generated Content</h2>
                <p class="leading-relaxed text-slate-600 dark:text-slate-300">
                    The Site may invite you to chat, contribute to, or participate in blogs, message boards, and other functionality, and may invite you to create, submit, post, display, transmit, publish, or distribute content, including comments, posts, and reactions.
                </p>
                <p class="leading-relaxed text-slate-600 dark:text-slate-300">
                    You retain ownership of all intellectual property rights in your comments and posts. However, by submitting content, you grant us a worldwide, non-exclusive, royalty-free, perpetual license to host, display, reproduce, and distribute your content across our platform.
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">4. Prohibited Activities</h2>
                <p class="leading-relaxed text-slate-600 dark:text-slate-300">
                    You may not access or use the Services for any purpose other than that for which we make the Services available. Prohibited activities include:
                </p>
                <ul class="list-disc pl-6 space-y-2 text-slate-600 dark:text-slate-300">
                    <li>Systematically retrieving data or other content from the Site to create or compile a collection, database, or directory.</li>
                    <li>Making any unauthorized use of the Services, including collecting usernames and/or email addresses of users by electronic or other means.</li>
                    <li>Using the Services to advertise or offer to sell goods and services.</li>
                    <li>Engaging in unauthorized framing of or linking to the Site.</li>
                </ul>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">5. Limitation of Liability</h2>
                <p class="leading-relaxed text-slate-600 dark:text-slate-300">
                    In no event will we or our directors, employees, or agents be liable to you or any third party for any direct, indirect, consequential, exemplary, incidental, special, or punitive damages, including lost profit, lost revenue, loss of data, or other damages arising from your use of the site.
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">6. Contact Information</h2>
                <p class="leading-relaxed text-slate-600 dark:text-slate-300">
                    In order to resolve a complaint regarding the Services or to receive further information regarding use of the Services, please contact us at <a href="mailto:<?php echo escape_html($siteSettings['contact_email'] ?? 'hello@blogfusion.com'); ?>" class="text-primary hover:underline"><?php echo escape_html($siteSettings['contact_email'] ?? 'hello@blogfusion.com'); ?></a>.
                </p>
            </section>
        </div>
    </main>

    <!-- FOOTER -->
    <?php include BASE_PATH . 'include/footer.php'; ?>

    <script>
        const html = document.documentElement;
        const darkToggle = document.getElementById('dark-toggle');
        const darkIcon = document.getElementById('dark-icon');

        function setDark(on, writeToStorage = true) {
            if (on) {
                html.classList.add('dark');
                if (darkIcon) darkIcon.textContent = 'light_mode';
                if (writeToStorage) localStorage.setItem('bf_dark', '1');
            } else {
                html.classList.remove('dark');
                if (darkIcon) darkIcon.textContent = 'dark_mode';
                if (writeToStorage) localStorage.setItem('bf_dark', '0');
            }
        }

        const localTheme = localStorage.getItem('bf_dark');
        if (localTheme === '1' || (!localTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            setDark(true, false);
        } else {
            setDark(false, false);
        }

        if (darkToggle) {
            darkToggle.addEventListener('click', () => {
                setDark(!html.classList.contains('dark'));
            });
        }
    </script>
</body>
</html>

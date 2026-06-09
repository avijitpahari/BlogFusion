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
    <link class="light" rel="icon" type="image/png" href="<?php echo BASE_URL; ?>upload/site_image/logo2.png" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?php echo escape_html($siteSettings['site_name'] ?? 'Blog Fusion'); ?> - Cookie Policy</title>
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
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">Cookie Policy</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Last Updated: <?php echo date("F d, Y"); ?></p>
            </header>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">1. What are Cookies?</h2>
                <p class="leading-relaxed text-slate-600 dark:text-slate-300">
                    Cookies are small text files that are placed on your computer or mobile device when you visit a website. They are widely used to make websites work more efficiently, as well as to provide reporting info. Cookies set by the website owner are called "first-party cookies". Cookies set by parties other than the website owner are called "third-party cookies".
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">2. Why We Use Cookies</h2>
                <p class="leading-relaxed text-slate-600 dark:text-slate-300">
                    We use cookies for several reasons. Some cookies are required for technical reasons in order for our Services to operate, and we refer to these as "essential" or "strictly necessary" cookies. Other cookies enable us to track and target the interests of our users to enhance their experience.
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">3. Types of Cookies We Use</h2>
                <div class="space-y-3">
                    <p class="leading-relaxed text-slate-600 dark:text-slate-300">
                        The specific types of first and third party cookies served through our Services and the purposes they perform are outlined below:
                    </p>
                    <ul class="list-disc pl-6 space-y-2 text-slate-600 dark:text-slate-300">
                        <li><strong>Essential Website Cookies:</strong> These cookies are strictly necessary to provide you with services available through our Site (such as maintaining login sessions and session security).</li>
                        <li><strong>Functionality Cookies:</strong> These cookies are used to enhance the performance and functionality of our Site but are non-essential to their use (such as remember theme preference).</li>
                        <li><strong>Analytics and Customization Cookies:</strong> These cookies collect information that is used either in aggregate form to help us understand how our Site is being used.</li>
                    </ul>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">4. Control Your Cookies</h2>
                <p class="leading-relaxed text-slate-600 dark:text-slate-300">
                    You have the right to decide whether to accept or reject cookies. You can set or amend your web browser controls to accept or refuse cookies. If you choose to reject cookies, you may still use our website though your access to some functionality and areas of our website may be restricted.
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">5. How Often Will We Update This Policy?</h2>
                <p class="leading-relaxed text-slate-600 dark:text-slate-300">
                    We may update this Cookie Policy from time to time in order to reflect changes to the cookies we use or for other operational, legal or regulatory reasons. Please therefore re-visit this Cookie Policy regularly to stay informed.
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">6. More Information</h2>
                <p class="leading-relaxed text-slate-600 dark:text-slate-300">
                    If you have any questions about our use of cookies or other technologies, please email us at <a href="mailto:<?php echo escape_html($siteSettings['contact_email'] ?? 'hello@blogfusion.com'); ?>" class="text-primary hover:underline"><?php echo escape_html($siteSettings['contact_email'] ?? 'hello@blogfusion.com'); ?></a>.
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

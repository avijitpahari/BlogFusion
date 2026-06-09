<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAdmin();
include BASE_PATH . 'include/db.php';

include BASE_PATH . 'include/admin_nav_sidebar.php';
include_once BASE_PATH . 'include/functions.php';

// Fetch current settings
$siteSettings = fetch_site_settings();
?>

<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>upload/site_image/logo2.png" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Blog Fusion - Site Settings</title>
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
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
    <div class="flex h-screen overflow-hidden">
        <!-- SideNavBar -->
        <?= slidebar('settings'); ?>
        <div id="overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>
        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <?= ad_navbar(); ?>
            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-3xl font-black tracking-tight">Site Settings</h2>
                        <p class="text-slate-500">Configure global metadata and contact information for your blog platform.</p>
                    </div>
                </div>

                <div class="max-w-4xl bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 md:p-8">
                    <form method="POST" action="<?= BASE_URL ?>actions/admin.php" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="settings-update" value="1" />
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Site Name -->
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Site Name</label>
                                <input type="text" name="site_name" value="<?php echo escape_html($siteSettings['site_name'] ?? 'Blog Fusion'); ?>" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none" required />
                            </div>

                            <!-- Contact Email -->
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Contact Email</label>
                                <input type="email" name="contact_email" value="<?php echo escape_html($siteSettings['contact_email'] ?? 'hello@blogfusion.com'); ?>" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none" required />
                            </div>

                            <!-- Logo Upload & Preview -->
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Site Logo</label>
                                <div class="flex items-center gap-6 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                                    <div class="w-16 h-16 rounded-xl bg-white border border-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                                        <?php
                                        $settings_logo = $siteSettings['logo'] ?? 'upload/site_image/logo2.png';
                                        if (!preg_match('/^https?:\/\//i', $settings_logo)) {
                                            $settings_logo = BASE_URL . $settings_logo;
                                        }
                                        ?>
                                        <img id="logo-preview" src="<?php echo $settings_logo; ?>" alt="Logo Preview" class="max-w-full max-h-full object-contain" />
                                    </div>
                                    <div class="space-y-1">
                                        <input type="file" name="logo" accept="image/*" onchange="previewLogo(event)" class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 file:cursor-pointer" />
                                        <p class="text-xs text-slate-400">Supports PNG, JPG, WEBP, or SVG. Replaces site-wide logos.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Site Description -->
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Site Description</label>
                                <textarea name="description" rows="3" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none resize-none" required><?php echo escape_html($siteSettings['description'] ?? ''); ?></textarea>
                            </div>

                            <!-- Contact Address -->
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Contact Address</label>
                                <textarea name="contact_address" rows="3" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none resize-none" required><?php echo escape_html($siteSettings['contact_address'] ?? 'Innovation District, Tech City, TC 10101'); ?></textarea>
                            </div>

                            <!-- Contact Working Hours -->
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Working Hours</label>
                                <input type="text" name="contact_hours" value="<?php echo escape_html($siteSettings['contact_hours'] ?? 'Mon–Fri, 9am–6pm IST'); ?>" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none" required />
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                            <button type="submit" class="bg-primary hover:bg-primary-hover text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/20 transition-all text-sm">
                                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    
    <script src="<?= BASE_URL ?>assets/js/admin.js"></script>
    <script>
        function previewLogo(event) {
            const input = event.target;
            const preview = document.getElementById('logo-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>

<?php
if (!function_exists('fetch_site_settings')) {
    include_once __DIR__ . '/db.php';
    include_once __DIR__ . '/functions.php';
}
$footerSettings = fetch_site_settings();
global $conn;
// Fetch dynamic categories
$footerCategories = fetch_category_stats($conn);
?>
<!-- Footer Component Start -->
<footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-6 pt-16 pb-8">
        <!-- Footer Links Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 py-12">
            <!-- Brand Column -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-2">
                    <?php
                    $footer_logo = $footerSettings['logo'] ?? 'upload/site_image/logo1.png';
                    if (!preg_match('/^https?:\/\//i', $footer_logo)) {
                        $footer_logo = site_url($footer_logo);
                    }
                    ?>
                    <img class="h-16 w-auto max-w-full object-contain" src="<?php echo $footer_logo; ?>" alt="BlogFusion" />
                </div>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">
                    <?php echo escape_html($footerSettings['description'] ?? 'Connecting ideas and people. Blog Fusion is your go-to destination for high-quality insights.'); ?>
                </p>
                <div class="flex gap-4 mt-2">
                    <button class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all border-none cursor-pointer"
                        onclick="footerShare()" aria-label="Share">
                        <span class="material-symbols-outlined text-xl">share</span>
                    </button>
                    <a class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all"
                        href="<?php echo site_url('index.php'); ?>" aria-label="Website">
                        <span class="material-symbols-outlined text-xl">public</span>
                    </a>
                    <button class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all border-none cursor-pointer"
                        onclick="footerLike()" aria-label="Like">
                        <span class="material-symbols-outlined text-xl" id="footer-like-icon">favorite</span>
                    </button>
                </div>
            </div>
            <!-- Quick Links -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-6">Quick Links</h3>
                <ul class="space-y-4 text-sm">
                    <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors flex items-center gap-2"
                            href="<?php echo site_url('index.php'); ?>"><span class="material-symbols-outlined text-sm">chevron_right</span>
                            Home</a></li>
                    <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors flex items-center gap-2"
                            href="<?php echo site_url('index.php#latest'); ?>"><span class="material-symbols-outlined text-sm">chevron_right</span>
                            Latest Posts</a></li>
                    <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors flex items-center gap-2"
                            href="<?php echo site_url('index.php#categories'); ?>"><span class="material-symbols-outlined text-sm">chevron_right</span>
                            Categories</a></li>
                    <li><a class="text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors flex items-center gap-2"
                            href="<?php echo site_url('index.php#trending'); ?>"><span class="material-symbols-outlined text-sm">chevron_right</span>
                            Trending</a></li>
                </ul>
            </div>
            <!-- Categories -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-6">Categories</h3>
                <ul class="space-y-4 text-sm">
                    <?php if (!empty($footerCategories)): ?>
                        <?php foreach (array_slice($footerCategories, 0, 4) as $cat): ?>
                            <li><a class="text-slate-600 dark:text-slate-400 hover:text-accent dark:hover:text-accent transition-colors"
                                    href="<?php echo site_url('pages/single-post.php?category=' . urlencode($cat['slug'])); ?>"><?php echo escape_html($cat['name']); ?></a></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><span class="text-slate-400">Technology</span></li>
                        <li><span class="text-slate-400">Design</span></li>
                        <li><span class="text-slate-400">Development</span></li>
                        <li><span class="text-slate-400">Marketing</span></li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- Support/Contact Info -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-6">Get in Touch</h3>
                <ul class="space-y-4 text-sm">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5">mail</span>
                        <span class="text-slate-600 dark:text-slate-400 break-all"><?php echo escape_html($footerSettings['contact_email'] ?? 'hello@blogfusion.com'); ?></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5">location_on</span>
                        <span class="text-slate-600 dark:text-slate-400"><?php echo nl2br(escape_html($footerSettings['contact_address'] ?? "123 Creative Lane, Innovation District\nTech City, TC 10101")); ?></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-0.5">schedule</span>
                        <span class="text-slate-600 dark:text-slate-400"><?php echo escape_html($footerSettings['contact_hours'] ?? 'Mon–Fri, 9am–6pm IST'); ?></span>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Bottom Bar -->
        <div class="pt-8 border-t border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-500 dark:text-slate-500 text-sm">
                © <?php echo date('Y'); ?> Blog Fusion. All rights reserved.
            </p>
            <div class="flex gap-8 text-sm">
                <a class="text-slate-500 dark:text-slate-500 hover:text-primary transition-colors" href="<?php echo site_url('pages/privacy-policy.php'); ?>">Privacy Policy</a>
                <a class="text-slate-500 dark:text-slate-500 hover:text-primary transition-colors" href="<?php echo site_url('pages/terms-of-service.php'); ?>">Terms of Service</a>
                <a class="text-slate-500 dark:text-slate-500 hover:text-primary transition-colors" href="<?php echo site_url('pages/cookie-policy.php'); ?>">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
<script>
function footerShare() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        if (typeof showToast === 'function') {
            showToast("🔗 Link copied to clipboard!", "success");
        } else {
            alert("🔗 Link copied to clipboard!");
        }
    }).catch(() => {
        if (typeof showToast === 'function') {
            showToast("Failed to copy link.", "error");
        }
    });
}

function footerLike() {
    const heartIcon = document.getElementById('footer-like-icon');
    if (!heartIcon) return;
    const isLiked = localStorage.getItem('bf_site_liked') === '1';
    if (isLiked) {
        localStorage.setItem('bf_site_liked', '0');
        heartIcon.style.fontVariationSettings = "'FILL' 0";
        heartIcon.style.color = '';
        if (typeof showToast === 'function') {
            showToast("💔 Removed from favorites.", "info");
        }
    } else {
        localStorage.setItem('bf_site_liked', '1');
        heartIcon.style.fontVariationSettings = "'FILL' 1";
        heartIcon.style.color = '#ef4444';
        if (typeof showToast === 'function') {
            showToast("💖 Thank you for your support!", "success");
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const heartIcon = document.getElementById('footer-like-icon');
    if (heartIcon && localStorage.getItem('bf_site_liked') === '1') {
        heartIcon.style.fontVariationSettings = "'FILL' 1";
        heartIcon.style.color = '#ef4444';
    }
});
</script>
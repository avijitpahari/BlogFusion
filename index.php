<?php
require_once __DIR__ . '/config.php';
include BASE_PATH . 'include/db.php';
include_once BASE_PATH . 'include/functions.php';

global $conn;

// ── Ensure testimonials table exists ──────────────────────────────────────
$conn->query("
    CREATE TABLE IF NOT EXISTS `testimonials` (
        `id`           INT AUTO_INCREMENT PRIMARY KEY,
        `name`         VARCHAR(100) NOT NULL,
        `role`         VARCHAR(100) NOT NULL DEFAULT 'Reader',
        `rating`       TINYINT(1)  NOT NULL DEFAULT 5,
        `review`       TEXT        NOT NULL,
        `avatar_color` VARCHAR(20) NOT NULL DEFAULT '#7C3AED',
        `is_approved`  TINYINT(1)  NOT NULL DEFAULT 0,
        `created_at`   TIMESTAMP   DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
");

// ── AJAX: submit testimonial ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_testimonial') {
    header('Content-Type: application/json');

    $name   = trim($_POST['name']   ?? '');
    $role   = trim($_POST['role']   ?? 'Reader');
    $rating = (int)($_POST['rating'] ?? 5);
    $review = trim($_POST['review']  ?? '');

    // Validation
    $errors = [];
    if (strlen($name)   < 2)   $errors[] = 'Name must be at least 2 characters.';
    if (strlen($name)   > 100) $errors[] = 'Name is too long (max 100 chars).';
    if (strlen($role)   > 100) $errors[] = 'Role is too long (max 100 chars).';
    if ($rating < 1 || $rating > 5) $errors[] = 'Rating must be between 1 and 5.';
    if (strlen($review) < 15)  $errors[] = 'Review must be at least 15 characters.';
    if (strlen($review) > 600) $errors[] = 'Review is too long (max 600 chars).';

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }

    // Avatar color from name hash
    $palette = ['#7C3AED','#4F46E5','#EC4899','#0EA5E9','#10B981','#F59E0B','#EF4444','#6366F1'];
    $color   = $palette[abs(crc32($name)) % count($palette)];

    $name_esc   = mysqli_real_escape_string($conn, $name);
    $role_esc   = mysqli_real_escape_string($conn, $role ?: 'Reader');
    $review_esc = mysqli_real_escape_string($conn, $review);
    $color_esc  = mysqli_real_escape_string($conn, $color);

    $ok = $conn->query("
        INSERT INTO testimonials (name, role, rating, review, avatar_color, is_approved)
        VALUES ('$name_esc', '$role_esc', $rating, '$review_esc', '$color_esc', 0)
    ");

    if ($ok) {
        echo json_encode(['success' => true, 'message' => 'Thank you! Your review has been submitted for approval.']);
    } else {
        echo json_encode(['success' => false, 'errors' => ['Database error. Please try again.']]);
    }
    exit;
}

// ── Fetch approved testimonials from DB ──────────────────────────────────
$testi_result = $conn->query("
    SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 12
");
$dbTestimonials = [];
if ($testi_result) {
    while ($row = $testi_result->fetch_assoc()) {
        $dbTestimonials[] = $row;
    }
}

// Count pending reviews (for admin awareness)
$pending_res   = $conn->query("SELECT COUNT(*) as c FROM testimonials WHERE is_approved = 0");
$pendingCount  = $pending_res ? (int)$pending_res->fetch_assoc()['c'] : 0;

// Total testimonials submitted
$total_testi_res = $conn->query("SELECT COUNT(*) as c FROM testimonials");
$totalTestimonials = $total_testi_res ? (int)$total_testi_res->fetch_assoc()['c'] : 0;

// --- Fetch live data ---
$latestPosts     = fetch_latest_posts($conn, 6);
$trendingPosts   = fetch_top_posts($conn, 4);
$categoryStats   = fetch_category_stats($conn);
$dashboardStats  = fetch_dashboard_stats($conn);
$siteSettings    = fetch_site_settings();

$publishedCount  = (int)($dashboardStats['published_posts']  ?? 0);
$totalCategories = (int)($dashboardStats['total_categories'] ?? 0);
$totalReactions  = (int)($dashboardStats['total_reactions']  ?? 0);
$totalComments   = (int)($dashboardStats['total_comments']   ?? 0);

// Hero Post
$heroPost = !empty($latestPosts) ? $latestPosts[0] : null;

// Category icon mapping
$cat_icons = [
    'technology'    => 'biotech',
    'programming'   => 'terminal',
    'tech'          => 'memory',
    'business'      => 'insights',
    'design'        => 'palette',
    'marketing'     => 'campaign',
    'science'       => 'science',
    'health'        => 'favorite',
    'travel'        => 'flight',
    'food'          => 'restaurant',
    'sports'        => 'sports_soccer',
    'finance'       => 'savings',
    'education'     => 'school',
    'lifestyle'     => 'self_improvement',
    'entertainment' => 'movie',
];

function getCatIcon($name) {
    global $cat_icons;
    $key = strtolower(trim($name));
    foreach ($cat_icons as $k => $icon) {
        if (str_contains($key, $k)) return $icon;
    }
    return 'category';
}

$catColors = ['#7C3AED','#4F46E5','#EC4899','#0EA5E9','#10B981','#F59E0B','#EF4444','#6366F1'];
?><!DOCTYPE html>
<html class="scroll-smooth" lang="en">

<head>
    <link rel="icon" type="image/png"
        href="<?php echo defined('BASE_URL') ? BASE_URL : '/BlogFusion/'; ?>upload/site_image/logo2.png" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BlogFusion — Where Ideas Collide &amp; Stories Come Alive</title>
    <meta name="description"
        content="BlogFusion is a modern blogging platform for tech, programming, business & design enthusiasts. Discover, learn, and connect with brilliant minds." />
    <meta property="og:title" content="BlogFusion — Where Ideas Come Alive" />
    <meta property="og:description"
        content="Read, explore and discover the best blogs on technology, programming, business and design." />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Instrument+Serif:ital@0;1&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#7C3AED',
                        'primary-dark': '#6D28D9',
                        secondary: '#4F46E5',
                        accent: '#EC4899',
                        'bg-light': '#FAFAFF',
                        'bg-dark': '#0F0E17',
                        'card-dark': '#1A1829',
                        'border-dark': '#2D2B3D',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Instrument Serif"', 'serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delay': 'float 6s ease-in-out 2s infinite',
                        'glow': 'glow 4s ease-in-out infinite alternate',
                        'counter': 'counter 2s ease-out',
                        'slide-in': 'slideIn 0.6s ease-out',
                        'fade-up': 'fadeUp 0.7s ease-out',
                    },
                    keyframes: {
                        float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-18px)' } },
                        glow: { '0%': { opacity: '0.5' }, '100%': { opacity: '0.9' } },
                        slideIn: { '0%': { transform: 'translateX(-40px)', opacity: '0' }, '100%': { transform: 'none', opacity: '1' } },
                        fadeUp: { '0%': { transform: 'translateY(30px)', opacity: '0' }, '100%': { transform: 'none', opacity: '1' } },
                    }
                }
            }
        };
    </script>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .font-serif {
            font-family: 'Instrument Serif', serif;
        }

        /* Gradient text */
        .grad-text {
            background: linear-gradient(135deg, #7C3AED 0%, #4F46E5 40%, #EC4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .grad-text-warm {
            background: linear-gradient(135deg, #EC4899 0%, #7C3AED 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .glass-light {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(124, 58, 237, 0.1);
        }

        /* Noise texture overlay */
        .noise::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            opacity: 0.35;
            pointer-events: none;
        }

        /* Animated mesh gradient background */
        .mesh-bg {
            background: radial-gradient(ellipse at 20% 30%, rgba(124, 58, 237, 0.25) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(79, 70, 229, 0.2) 0%, transparent 45%),
                radial-gradient(ellipse at 60% 80%, rgba(236, 72, 153, 0.15) 0%, transparent 45%),
                radial-gradient(ellipse at 0% 80%, rgba(14, 165, 233, 0.12) 0%, transparent 40%),
                #0F0E17;
        }

        /* Hero orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: orbPulse 8s ease-in-out infinite alternate;
        }

        @keyframes orbPulse {
            0% {
                transform: scale(1) translate(0, 0);
                opacity: 0.5;
            }

            100% {
                transform: scale(1.2) translate(15px, -15px);
                opacity: 0.8;
            }
        }

        /* Nav */
        #main-nav {
            transition: all 0.35s ease;
        }

        #main-nav.scrolled {
            background: rgba(15, 14, 23, 0.88) !important;
            box-shadow: 0 4px 40px rgba(124, 58, 237, 0.15);
        }

        html:not(.dark) #main-nav.scrolled {
            background: rgba(255, 255, 255, 0.92) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
        }

        /* Mobile menu */
        #mobile-menu {
            transition: max-height 0.35s ease, opacity 0.3s;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
        }

        #mobile-menu.open {
            max-height: 480px;
            opacity: 1;
        }

        /* Post card */
        .post-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .post-card:hover {
            transform: translateY(-5px);
        }

        .post-card:hover .card-img {
            transform: scale(1.06);
        }

        .card-img {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Category card */
        .cat-card {
            transition: transform 0.25s, box-shadow 0.25s;
        }

        .cat-card:hover {
            transform: translateY(-4px) scale(1.02);
        }

        /* Stat counter animation */
        .stat-num {
            display: inline-block;
            animation: popIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        @keyframes popIn {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Scroll animations */
        [data-reveal] {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        [data-reveal].visible {
            opacity: 1;
            transform: none;
        }

        [data-reveal-delay="1"] {
            transition-delay: 0.1s;
        }

        [data-reveal-delay="2"] {
            transition-delay: 0.2s;
        }

        [data-reveal-delay="3"] {
            transition-delay: 0.3s;
        }

        [data-reveal-delay="4"] {
            transition-delay: 0.4s;
        }

        /* Feature icon glow */
        .feature-icon {
            box-shadow: 0 0 30px rgba(124, 58, 237, 0.35);
        }

        /* Newsletter input */
        .nl-input:focus {
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.25);
        }

        /* Tag badge */
        .tag-badge {
            font-size: 10px;
            letter-spacing: 0.08em;
        }

        /* Reading time bar */
        .read-bar {
            height: 2px;
            background: linear-gradient(90deg, #7C3AED, #EC4899);
            border-radius: 99px;
        }

        /* Testimonial card */
        .testi-card {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.06), rgba(79, 70, 229, 0.04));
        }

        html.dark .testi-card {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.12), rgba(79, 70, 229, 0.08));
        }

        /* Testimonial carousel */
        .testi-slider { overflow: hidden; position: relative; }
        .testi-track  { display: flex; transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .testi-slide  { flex-shrink: 0; width: 100%; padding: 0 6px; }
        @media (min-width: 768px)  { .testi-slide { width: 50%;  } }
        @media (min-width: 1024px) { .testi-slide { width: 33.3333%; } }

        /* Star rating picker */
        .star-btn { cursor: pointer; transition: transform 0.15s, color 0.15s; font-size: 28px; color: #CBD5E1; }
        .star-btn:hover, .star-btn.active { color: #FBBF24; transform: scale(1.18); }
        .star-btn.dim  { color: #CBD5E1; }

        /* Testi form */
        .testi-form-input {
            width: 100%;
            padding: 12px 16px;
            border-radius: 14px;
            border: 1.5px solid rgba(124,58,237,0.15);
            background: rgba(124,58,237,0.04);
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: inherit;
        }
        html.dark .testi-form-input {
            background: rgba(124,58,237,0.08);
            border-color: rgba(124,58,237,0.2);
            color: #E8E6F4;
        }
        .testi-form-input:focus {
            outline: none;
            border-color: #7C3AED;
            box-shadow: 0 0 0 3px rgba(124,58,237,0.15);
        }
        .testi-form-input::placeholder { color: #94A3B8; }

        /* Dot indicator */
        .testi-dot { width: 8px; height: 8px; border-radius: 99px; background: #CBD5E1; transition: all 0.3s; cursor: pointer; }
        .testi-dot.active { width: 24px; background: #7C3AED; }

        /* Pending badge */
        .pending-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 99px;
            background: rgba(251,191,36,0.12); color: #B45309;
            font-size: 11px; font-weight: 700;
        }
        html.dark .pending-badge { background: rgba(251,191,36,0.15); color: #FCD34D; }

        /* Form success state */
        #testi-success { display: none; }
        #testi-success.show { display: flex; }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #7C3AED;
            border-radius: 99px;
        }

        /* DARK MODE */
        html.dark body {
            background: #0F0E17;
            color: #E8E6F4;
        }

        html.dark .glass-light {
            background: rgba(26, 24, 41, 0.75);
            border-color: rgba(124, 58, 237, 0.15);
        }

        html.dark .text-slate-600 {
            color: #A89EC4 !important;
        }

        html.dark .text-slate-700 {
            color: #C4BEDA !important;
        }

        html.dark .text-slate-500 {
            color: #8B82A8 !important;
        }

        html.dark .bg-white {
            background: #1A1829 !important;
        }

        html.dark .bg-slate-50 {
            background: #151422 !important;
        }

        html.dark .bg-slate-100 {
            background: #1D1B2C !important;
        }

        html.dark .border-slate-100 {
            border-color: #2D2B3D !important;
        }

        html.dark .border-slate-200 {
            border-color: #2D2B3D !important;
        }

        html.dark .text-slate-900 {
            color: #F0EDFF !important;
        }

        html.dark .text-slate-800 {
            color: #DDD9F5 !important;
        }

        html.dark #main-nav {
            border-color: #2D2B3D !important;
        }
    </style>
</head>

<body class="bg-bg-light dark:bg-bg-dark text-slate-900 dark:text-slate-100 overflow-x-hidden">

    <?php inject_project_toast(); ?>

    <!-- ══════════════════ NAVBAR ══════════════════ -->
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

    <main>
        <!-- ══════════════════ HERO ══════════════════ -->
        <section id="hero" class="relative min-h-screen mesh-bg noise flex items-center pt-16 overflow-hidden">

            <!-- Orbs -->
            <div class="orb w-[500px] h-[500px] bg-primary/30 top-[-100px] left-[-120px]"></div>
            <div class="orb w-[400px] h-[400px] bg-secondary/25 bottom-[-80px] right-[-80px]"
                style="animation-delay:3s;animation-duration:10s;"></div>
            <div class="orb w-[200px] h-[200px] bg-accent/25 top-1/2 right-1/4"
                style="animation-delay:5s;animation-duration:7s;"></div>

            <!-- Grid overlay -->
            <div class="absolute inset-0 opacity-[0.03]"
                style="background-image: linear-gradient(rgba(255,255,255,.6) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.6) 1px, transparent 1px); background-size: 60px 60px;">
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-10 py-20 lg:py-32">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">

                    <!-- Left content -->
                    <div class="text-center lg:text-left">
                        <!-- Badge -->
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass text-xs font-bold tracking-widest uppercase text-purple-300 mb-6 animate-fade-up">
                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                            Live Platform · <?php echo $publishedCount; ?> Articles Published
                        </div>

                        <!-- Headline -->
                        <h1
                            class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-white leading-[1.05] tracking-tight mb-6">
                            Where<br>
                            <span class="font-serif italic font-normal grad-text">Ideas</span>
                            <span class="text-white"> Collide</span><br>
                            &amp; Stories<br>
                            <span class="grad-text-warm">Come Alive</span>
                        </h1>

                        <p class="text-slate-400 text-lg leading-relaxed max-w-lg mx-auto lg:mx-0 mb-8">
                            BlogFusion is the ultimate space for curious minds — explore expert articles on
                            technology, programming, business, and design, crafted by passionate creators.
                        </p>

                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start mb-10">
                            <a href="<?php echo site_url('pages/register.php'); ?>"
                                class="group flex items-center justify-center gap-2 px-7 py-4 rounded-2xl text-white font-bold text-base transition-all hover:scale-105 hover:shadow-2xl hover:shadow-primary/40"
                                style="background: linear-gradient(135deg, #7C3AED, #4F46E5);">
                                Start Reading Free
                                <span
                                    class="material-symbols-outlined text-xl group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                            <a href="<?php echo site_url('index.php'); ?>#latest"
                                class="group flex items-center justify-center gap-2 px-7 py-4 rounded-2xl font-bold text-base glass text-slate-300 hover:text-white hover:border-primary/50 transition-all">
                                <span class="material-symbols-outlined text-xl">explore</span>
                                Browse Posts
                            </a>
                        </div>

                        <!-- Stats row -->
                        <div class="flex flex-wrap justify-center lg:justify-start gap-6">
                            <div class="text-center lg:text-left">
                                <div class="text-2xl font-black text-white stat-num">
                                    <?php echo number_format($publishedCount); ?>+</div>
                                <div class="text-xs text-slate-500 font-medium mt-0.5">Articles</div>
                            </div>
                            <div class="w-px h-10 bg-white/10 self-center hidden sm:block"></div>
                            <div class="text-center lg:text-left">
                                <div class="text-2xl font-black text-white stat-num">
                                    <?php echo number_format($totalCategories); ?>+</div>
                                <div class="text-xs text-slate-500 font-medium mt-0.5">Categories</div>
                            </div>
                            <div class="w-px h-10 bg-white/10 self-center hidden sm:block"></div>
                            <div class="text-center lg:text-left">
                                <div class="text-2xl font-black text-white stat-num">
                                    <?php echo number_format($totalReactions); ?>+</div>
                                <div class="text-xs text-slate-500 font-medium mt-0.5">Reactions</div>
                            </div>
                            <div class="w-px h-10 bg-white/10 self-center hidden sm:block"></div>
                            <div class="text-center lg:text-left">
                                <div class="text-2xl font-black text-white stat-num">
                                    <?php echo number_format($totalComments); ?>+</div>
                                <div class="text-xs text-slate-500 font-medium mt-0.5">Comments</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Hero Post Card or Visual -->
                    <div class="relative">
                        <?php if ($heroPost): ?>
                            <div class="relative animate-float max-w-md mx-auto lg:ml-auto">
                                <!-- Glow behind card -->
                                <div class="absolute inset-0 -m-6 rounded-[3rem]"
                                    style="background: radial-gradient(ellipse, rgba(124,58,237,0.4) 0%, transparent 70%); filter: blur(40px);">
                                </div>

                                <!-- Featured post card -->
                                <div class="relative rounded-3xl overflow-hidden glass border border-white/15 shadow-2xl">
                                    <div class="relative h-52 sm:h-64 overflow-hidden">
                                        <img src="<?php echo escape_html(normalize_image($heroPost['image'] ?? '')); ?>"
                                            alt="<?php echo escape_html($heroPost['title']); ?>"
                                            class="w-full h-full object-cover" />
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                                        <div class="absolute top-4 left-4">
                                            <span
                                                class="px-3 py-1 rounded-lg text-xs font-bold text-white uppercase tag-badge"
                                                style="background: linear-gradient(135deg,#7C3AED,#4F46E5);">
                                                <?php echo escape_html($heroPost['category_name'] ?? 'Featured'); ?>
                                            </span>
                                        </div>
                                        <div class="absolute bottom-4 left-4 right-4">
                                            <p class="text-white font-bold text-lg leading-snug line-clamp-2">
                                                <?php echo escape_html($heroPost['title']); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="p-5">
                                        <p class="text-slate-400 text-sm line-clamp-2 mb-4">
                                            <?php echo escape_html($heroPost['description'] ?: excerpt_text($heroPost['content'] ?? '', 100)); ?>
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2 text-slate-500 text-xs">
                                                <span class="material-symbols-outlined text-sm text-primary">person</span>
                                                <span
                                                    class="text-slate-400"><?php echo escape_html($heroPost['author_name'] ?? 'Author'); ?></span>
                                                <span>·</span>
                                                <span class="material-symbols-outlined text-sm">schedule</span>
                                                <span
                                                    class="text-slate-400"><?php echo reading_time($heroPost['content'] ?? ''); ?>
                                                    min</span>
                                            </div>
                                            <a href="<?php echo escape_html(site_url('pages/single-post.php?slug=' . urlencode($heroPost['slug'] ?? ''))); ?>"
                                                class="flex items-center gap-1 text-xs font-bold text-primary hover:text-accent transition-colors">
                                                Read <span class="material-symbols-outlined text-base">chevron_right</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Floating mini cards -->
                                <div
                                    class="absolute -top-4 -right-4 sm:-right-8 glass rounded-2xl px-4 py-2.5 shadow-xl border border-white/15 hidden sm:flex items-center gap-2">
                                    <span class="text-lg">🔥</span>
                                    <div>
                                        <div class="text-white text-xs font-bold">Trending Now</div>
                                        <div class="text-slate-400 text-[10px]"><?php echo $publishedCount; ?> active posts
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="absolute -bottom-4 -left-4 sm:-left-8 glass rounded-2xl px-4 py-2.5 shadow-xl border border-white/15 hidden sm:flex items-center gap-2">
                                    <span class="text-lg">✨</span>
                                    <div>
                                        <div class="text-white text-xs font-bold">New Daily</div>
                                        <div class="text-slate-400 text-[10px]">Fresh stories every day</div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- No posts yet — show visual placeholder -->
                            <div class="animate-float max-w-md mx-auto lg:ml-auto">
                                <div class="glass border border-white/15 rounded-3xl p-10 text-center shadow-2xl">
                                    <div class="w-24 h-24 rounded-3xl mx-auto mb-6 flex items-center justify-center"
                                        style="background: linear-gradient(135deg,#7C3AED,#EC4899);">
                                        <span class="material-symbols-outlined text-white text-5xl">auto_stories</span>
                                    </div>
                                    <h3 class="text-white text-2xl font-bold mb-2">Coming Soon</h3>
                                    <p class="text-slate-400 text-sm">Posts are being published. Check back shortly!</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1 opacity-40">
                <span class="text-slate-400 text-xs tracking-widest uppercase font-semibold">Scroll</span>
                <div class="w-px h-10 bg-gradient-to-b from-slate-400 to-transparent"></div>
            </div>
        </section>

        <!-- ══════════════════ STATS BAND ══════════════════ -->
        <section class="py-14 bg-white dark:bg-card-dark border-y border-slate-100 dark:border-border-dark">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-10">
                    <?php
                    $stats = [
                        ['icon' => 'article', 'value' => $publishedCount, 'label' => 'Published Articles', 'color' => '#7C3AED'],
                        ['icon' => 'category', 'value' => $totalCategories, 'label' => 'Topic Categories', 'color' => '#4F46E5'],
                        ['icon' => 'favorite', 'value' => $totalReactions, 'label' => 'Reader Reactions', 'color' => '#EC4899'],
                        ['icon' => 'chat_bubble', 'value' => $totalComments, 'label' => 'Community Comments', 'color' => '#0EA5E9'],
                    ];
                    foreach ($stats as $i => $stat): ?>
                        <div class="flex flex-col items-center lg:items-start gap-3 p-5 rounded-2xl bg-slate-50 dark:bg-bg-dark/50 border border-slate-100 dark:border-border-dark"
                            data-reveal data-reveal-delay="<?php echo $i + 1; ?>">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center"
                                style="background: <?php echo $stat['color']; ?>18;">
                                <span class="material-symbols-outlined"
                                    style="color:<?php echo $stat['color']; ?>; font-size:22px;"><?php echo $stat['icon']; ?></span>
                            </div>
                            <div>
                                <div class="text-3xl font-black text-slate-900 dark:text-white">
                                    <?php echo number_format($stat['value']); ?><span
                                        style="color:<?php echo $stat['color']; ?>">+</span></div>
                                <div class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                    <?php echo $stat['label']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ══════════════════ LATEST POSTS ══════════════════ -->
        <section id="latest" class="py-20 bg-bg-light dark:bg-bg-dark">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Section header -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12" data-reveal>
                    <div>
                        <span
                            class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-primary mb-3">
                            <span class="material-symbols-outlined text-sm">newspaper</span>
                            Fresh Content
                        </span>
                        <h2 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Latest <span class="grad-text font-serif italic font-normal">Stories</span>
                        </h2>
                        <p class="text-slate-500 dark:text-slate-400 mt-2 max-w-md">Hand-picked articles from our most
                            talented writers, published fresh every day.</p>
                    </div>
                    <a href="<?php echo site_url('pages/login.php'); ?>"
                        class="flex items-center gap-1.5 text-sm font-bold text-primary hover:text-accent transition-colors group flex-shrink-0">
                        View All Posts
                        <span
                            class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">chevron_right</span>
                    </a>
                </div>

                <?php if (!empty($latestPosts)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                        <?php foreach (array_slice($latestPosts, 0, 6) as $i => $post):
                            $postLink = site_url('pages/single-post.php?slug=' . urlencode($post['slug'] ?? ''));
                            $postImage = normalize_image($post['image'] ?? '');
                            $excerpt = $post['description'] ?: excerpt_text($post['content'] ?? '', 110);
                            $tag = $post['category_name'] ?? 'Blog';
                            $readTime = reading_time($post['content'] ?? '');
                            $isFirst = ($i === 0);
                            ?>
                            <article
                                class="post-card group bg-white dark:bg-card-dark rounded-2xl overflow-hidden border border-slate-100 dark:border-border-dark hover:shadow-2xl hover:shadow-primary/10 dark:hover:shadow-primary/20 <?php echo $isFirst ? 'md:col-span-2 lg:col-span-1' : ''; ?>"
                                data-reveal data-reveal-delay="<?php echo ($i % 3) + 1; ?>">
                                <!-- Image -->
                                <a href="<?php echo escape_html($postLink); ?>"
                                    class="relative block overflow-hidden <?php echo $isFirst ? 'h-52' : 'h-44'; ?>">
                                    <img class="card-img w-full h-full object-cover"
                                        src="<?php echo escape_html($postImage); ?>"
                                        alt="<?php echo escape_html($post['title']); ?>" loading="lazy" />
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                    </div>
                                    <!-- Category badge -->
                                    <div class="absolute top-3 left-3">
                                        <span
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-white uppercase tracking-wide"
                                            style="background:linear-gradient(135deg,#7C3AED,#4F46E5);"><?php echo escape_html($tag); ?></span>
                                    </div>
                                    <!-- Read time chip -->
                                    <div class="absolute top-3 right-3">
                                        <span
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-black/40 text-white backdrop-blur-sm flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">schedule</span>
                                            <?php echo $readTime; ?>m
                                        </span>
                                    </div>
                                </a>

                                <!-- Content -->
                                <div class="p-5">
                                    <!-- Meta -->
                                    <div class="flex items-center gap-2 text-xs text-slate-400 mb-3">
                                        <span class="material-symbols-outlined text-sm text-primary/70">person</span>
                                        <span
                                            class="font-medium text-slate-500 dark:text-slate-400"><?php echo escape_html($post['author_name'] ?? 'Author'); ?></span>
                                        <span class="text-slate-300 dark:text-slate-600">·</span>
                                        <span class="material-symbols-outlined text-sm">calendar_today</span>
                                        <span><?php echo date('d M Y', strtotime($post['created_at'])); ?></span>
                                    </div>

                                    <!-- Title -->
                                    <h3
                                        class="font-bold text-slate-900 dark:text-white text-base leading-snug mb-2 group-hover:text-primary dark:group-hover:text-primary transition-colors line-clamp-2">
                                        <a
                                            href="<?php echo escape_html($postLink); ?>"><?php echo escape_html($post['title']); ?></a>
                                    </h3>

                                    <!-- Excerpt -->
                                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed line-clamp-2 mb-4">
                                        <?php echo escape_html($excerpt); ?>
                                    </p>

                                    <!-- Reading bar + link -->
                                    <div class="flex items-center justify-between">
                                        <div class="read-bar w-16"></div>
                                        <a href="<?php echo escape_html($postLink); ?>"
                                            class="flex items-center gap-1 text-xs font-bold text-primary hover:text-accent transition-colors group/link">
                                            Read Article
                                            <span
                                                class="material-symbols-outlined text-base group-hover/link:translate-x-0.5 transition-transform">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-20 rounded-3xl bg-white dark:bg-card-dark border border-slate-100 dark:border-border-dark"
                        data-reveal>
                        <span
                            class="material-symbols-outlined text-6xl text-slate-300 dark:text-slate-600 block mb-4">article</span>
                        <h3 class="text-lg font-bold text-slate-500 dark:text-slate-400">No articles published yet</h3>
                        <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Be the first to publish!</p>
                        <a href="<?php echo site_url('pages/register.php'); ?>"
                            class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white"
                            style="background:linear-gradient(135deg,#7C3AED,#4F46E5);">
                            Start Writing <span class="material-symbols-outlined text-base">edit</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- ══════════════════ CATEGORIES ══════════════════ -->
        <section id="categories"
            class="py-20 bg-white dark:bg-card-dark border-y border-slate-100 dark:border-border-dark">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="text-center mb-12" data-reveal>
                    <span
                        class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-primary mb-3">
                        <span class="material-symbols-outlined text-sm">category</span>
                        Explore Topics
                    </span>
                    <h2 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Find Your <span class="grad-text font-serif italic font-normal">Niche</span>
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-3 max-w-md mx-auto">
                        Dive deep into any subject that sparks your curiosity — we cover it all.
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                    <?php
                    $catList = !empty($categoryStats) ? array_slice($categoryStats, 0, 8) : [];
                    foreach ($catList as $ci => $cat):
                        $icon = getCatIcon($cat['name']);
                        $color = $catColors[$ci % count($catColors)];
                        ?>
                        <a href="<?php echo escape_html(site_url('pages/login.php')); ?>"
                            class="cat-card group relative flex flex-col items-center text-center p-6 rounded-2xl border border-slate-100 dark:border-border-dark bg-slate-50 dark:bg-bg-dark hover:shadow-xl overflow-hidden cursor-pointer"
                            data-reveal data-reveal-delay="<?php echo ($ci % 4) + 1; ?>">
                            <!-- bg glow on hover -->
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"
                                style="background: linear-gradient(135deg, <?php echo $color; ?>0C, <?php echo $color; ?>05);">
                            </div>
                            <!-- Icon -->
                            <div class="relative w-14 h-14 rounded-2xl flex items-center justify-center mb-4 transition-transform group-hover:scale-110 group-hover:shadow-xl"
                                style="background: <?php echo $color; ?>18;">
                                <span class="material-symbols-outlined text-3xl"
                                    style="color:<?php echo $color; ?>;"><?php echo $icon; ?></span>
                            </div>
                            <h4
                                class="font-bold text-slate-900 dark:text-white text-sm relative z-10 group-hover:text-[<?php echo $color; ?>] transition-colors">
                                <?php echo escape_html($cat['name']); ?>
                            </h4>
                            <p class="text-xs text-slate-400 mt-1 relative z-10">
                                <?php echo (int) ($cat['total_posts'] ?? 0); ?> Articles
                            </p>
                        </a>
                    <?php endforeach; ?>

                    <?php if (empty($catList)): ?>
                        <?php
                        $placeholders = [
                            ['icon' => 'biotech', 'name' => 'Technology', 'count' => '0'],
                            ['icon' => 'terminal', 'name' => 'Programming', 'count' => '0'],
                            ['icon' => 'insights', 'name' => 'Business', 'count' => '0'],
                            ['icon' => 'palette', 'name' => 'Design', 'count' => '0'],
                        ];
                        foreach ($placeholders as $pi => $ph): ?>
                            <div class="cat-card group flex flex-col items-center text-center p-6 rounded-2xl border border-slate-100 dark:border-border-dark bg-slate-50 dark:bg-bg-dark"
                                data-reveal>
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4"
                                    style="background:<?php echo $catColors[$pi]; ?>18;">
                                    <span class="material-symbols-outlined text-3xl"
                                        style="color:<?php echo $catColors[$pi]; ?>;"><?php echo $ph['icon']; ?></span>
                                </div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm"><?php echo $ph['name']; ?></h4>
                                <p class="text-xs text-slate-400 mt-1"><?php echo $ph['count']; ?> Articles</p>
                            </div>
                        <?php endforeach; endif; ?>
                </div>
            </div>
        </section>

        <!-- ══════════════════ TRENDING ══════════════════ -->
        <section id="trending" class="py-20 bg-bg-light dark:bg-bg-dark">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12" data-reveal>
                    <div>
                        <span
                            class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-accent mb-3">
                            <span class="material-symbols-outlined text-sm">local_fire_department</span>
                            Hot Right Now
                        </span>
                        <h2 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Trending <span class="grad-text-warm font-serif italic font-normal">Now</span>
                        </h2>
                    </div>
                </div>

                <?php if (!empty($trendingPosts)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach (array_slice($trendingPosts, 0, 4) as $ti => $post):
                            $postLink = site_url('pages/single-post.php?slug=' . urlencode($post['slug'] ?? ''));
                            $postImage = normalize_image($post['image'] ?? '');
                            $excerpt = $post['description'] ?: excerpt_text($post['content'] ?? '', 90);
                            ?>
                            <a href="<?php echo escape_html($postLink); ?>"
                                class="group flex gap-4 sm:gap-5 p-4 sm:p-5 rounded-2xl bg-white dark:bg-card-dark border border-slate-100 dark:border-border-dark hover:shadow-xl hover:shadow-primary/10 dark:hover:shadow-primary/15 transition-all hover:-translate-y-1"
                                data-reveal data-reveal-delay="<?php echo ($ti % 2) + 1; ?>">

                                <!-- Rank -->
                                <div class="flex-shrink-0 w-10 text-3xl font-black self-center text-center leading-none"
                                    style="color:<?php echo $catColors[$ti]; ?>40;">
                                    <?php echo str_pad($ti + 1, 2, '0', STR_PAD_LEFT); ?>
                                </div>

                                <!-- Thumbnail -->
                                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl overflow-hidden flex-shrink-0">
                                    <img class="card-img w-full h-full object-cover"
                                        src="<?php echo escape_html($postImage); ?>"
                                        alt="<?php echo escape_html($post['title']); ?>" loading="lazy" />
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0 flex flex-col justify-center">
                                    <div
                                        class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-accent mb-2">
                                        <span class="material-symbols-outlined text-xs">trending_up</span>
                                        <?php echo escape_html($post['category_name'] ?? 'Popular'); ?>
                                    </div>
                                    <h3
                                        class="font-bold text-slate-900 dark:text-white text-sm leading-snug group-hover:text-primary dark:group-hover:text-primary transition-colors line-clamp-2 mb-2">
                                        <?php echo escape_html($post['title']); ?>
                                    </h3>
                                    <p class="text-slate-500 dark:text-slate-400 text-xs line-clamp-2 hidden sm:block mb-3">
                                        <?php echo escape_html($excerpt); ?></p>
                                    <div class="flex items-center gap-3 text-[11px] text-slate-400">
                                        <span class="flex items-center gap-1"><span
                                                class="material-symbols-outlined text-xs text-primary">visibility</span><?php echo number_format((int) ($post['views'] ?? 0)); ?>
                                            views</span>
                                        <span class="flex items-center gap-1"><span
                                                class="material-symbols-outlined text-xs text-accent">favorite</span><?php echo (int) ($post['reaction_count'] ?? 0); ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16 bg-white dark:bg-card-dark rounded-2xl border border-slate-100 dark:border-border-dark"
                        data-reveal>
                        <span
                            class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600 block mb-3">trending_up</span>
                        <p class="text-slate-400 dark:text-slate-500 font-medium">Trending posts will appear once there's
                            engagement</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- ══════════════════ FEATURES ══════════════════ -->
        <section id="features" class="py-24 relative overflow-hidden"
            style="background: linear-gradient(135deg, #0F0E17 0%, #1A1032 50%, #0F0E17 100%);">

            <!-- Background grid -->
            <div class="absolute inset-0 opacity-[0.04]"
                style="background-image: linear-gradient(rgba(255,255,255,.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.5) 1px, transparent 1px); background-size: 40px 40px;">
            </div>

            <!-- Orbs -->
            <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-primary/20 blur-[80px] pointer-events-none">
            </div>
            <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-accent/15 blur-[80px] pointer-events-none">
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

                <div class="text-center mb-16" data-reveal>
                    <span
                        class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-purple-400 mb-3">
                        <span class="material-symbols-outlined text-sm">auto_awesome</span>
                        Why BlogFusion?
                    </span>
                    <h2 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight">
                        Built for <span class="font-serif italic font-normal grad-text">Readers</span> &amp;<br
                            class="hidden sm:block"> <span
                            class="grad-text-warm font-serif italic font-normal">Creators</span>
                    </h2>
                    <p class="text-slate-400 mt-4 max-w-xl mx-auto">
                        Every feature is designed to enhance your reading experience and empower writers to share
                        brilliant ideas.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    $features = [
                        ['icon' => 'search', 'title' => 'Smart Discovery', 'desc' => 'Advanced search and filtering to help you find exactly the content you need, instantly.', 'color' => '#7C3AED'],
                        ['icon' => 'person_celebrate', 'title' => 'Top Authors', 'desc' => 'Connect with expert writers across technology, business, design, and science domains.', 'color' => '#4F46E5'],
                        ['icon' => 'emoji_emotions', 'title' => 'Rich Reactions', 'desc' => 'Go beyond likes — express how articles make you feel with full emoji reaction support.', 'color' => '#EC4899'],
                        ['icon' => 'bookmark', 'title' => 'Save & Read Later', 'desc' => 'Build your personal reading list and revisit your favourite articles any time.', 'color' => '#0EA5E9'],
                        ['icon' => 'chat_bubble', 'title' => 'Deep Discussions', 'desc' => 'Engage in meaningful comment threads that turn reading into a shared experience.', 'color' => '#10B981'],
                        ['icon' => 'dark_mode', 'title' => 'Dark Mode Native', 'desc' => 'Beautifully designed for any lighting — switch effortlessly between light and dark themes.', 'color' => '#F59E0B'],
                    ];
                    foreach ($features as $fi => $feat): ?>
                        <div class="group glass rounded-2xl p-6 hover:bg-white/10 transition-all hover:-translate-y-1"
                            data-reveal data-reveal-delay="<?php echo ($fi % 3) + 1; ?>">
                            <div class="w-12 h-12 rounded-xl feature-icon flex items-center justify-center mb-5 transition-transform group-hover:scale-110"
                                style="background: <?php echo $feat['color']; ?>22;">
                                <span class="material-symbols-outlined text-2xl"
                                    style="color:<?php echo $feat['color']; ?>;"><?php echo $feat['icon']; ?></span>
                            </div>
                            <h3 class="text-white font-bold text-base mb-2"><?php echo $feat['title']; ?></h3>
                            <p class="text-slate-400 text-sm leading-relaxed"><?php echo $feat['desc']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ══════════════════ TESTIMONIALS ══════════════════ -->
        <section id="testimonials" class="py-20 bg-bg-light dark:bg-bg-dark">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12" data-reveal>
                    <div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-primary mb-3">
                            <span class="material-symbols-outlined text-sm">format_quote</span>
                            Reader Reviews
                        </span>
                        <h2 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Loved by <span class="grad-text font-serif italic font-normal">Our Community</span>
                        </h2>
                        <p class="text-slate-500 dark:text-slate-400 mt-2">Real words from real readers — <?php echo $totalTestimonials; ?> review<?php echo $totalTestimonials !== 1 ? 's' : ''; ?> submitted.</p>
                    </div>
                    <button onclick="document.getElementById('write-review').scrollIntoView({behavior:'smooth'})"
                        class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold border-2 border-primary text-primary hover:bg-primary hover:text-white transition-all">
                        <span class="material-symbols-outlined text-base">rate_review</span>
                        Write a Review
                    </button>
                </div>

                <?php if (!empty($dbTestimonials)): ?>
                <!-- Carousel -->
                <div class="testi-slider" data-reveal>
                    <div class="testi-track" id="testi-track">
                        <?php foreach ($dbTestimonials as $ti => $testi):
                            $initials = '';
                            foreach (explode(' ', trim($testi['name'])) as $word) {
                                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
                            }
                            $initials = mb_substr($initials, 0, 2);
                            $rating   = (int)$testi['rating'];
                            $color    = $testi['avatar_color'] ?? '#7C3AED';
                            $color2   = $catColors[($ti + 1) % count($catColors)];
                        ?>
                        <div class="testi-slide">
                            <div class="testi-card rounded-2xl p-6 border border-slate-100 dark:border-border-dark h-full flex flex-col">
                                <!-- Stars -->
                                <div class="flex gap-0.5 mb-4">
                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <span class="<?php echo $s <= $rating ? 'text-yellow-400' : 'text-slate-200 dark:text-slate-700'; ?> text-base">★</span>
                                    <?php endfor; ?>
                                </div>
                                <!-- Review text -->
                                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-5 italic flex-1">
                                    "<?php echo escape_html(mb_strimwidth($testi['review'], 0, 280, '…')); ?>"
                                </p>
                                <!-- Author -->
                                <div class="flex items-center gap-3 mt-auto pt-4 border-t border-slate-100 dark:border-border-dark">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0"
                                        style="background: linear-gradient(135deg, <?php echo escape_html($color); ?>, <?php echo escape_html($color2); ?>);">
                                        <?php echo escape_html($initials); ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-sm text-slate-900 dark:text-white"><?php echo escape_html($testi['name']); ?></div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400"><?php echo escape_html($testi['role']); ?></div>
                                    </div>
                                    <div class="ml-auto text-[10px] text-slate-400">
                                        <?php echo date('d M Y', strtotime($testi['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Controls -->
                <div id="testi-controls" class="flex items-center justify-center gap-4 mt-8">
                    <button id="testi-prev" onclick="testiSlide(-1)"
                        class="w-10 h-10 rounded-full border-2 border-slate-200 dark:border-border-dark flex items-center justify-center text-slate-500 hover:border-primary hover:text-primary dark:text-slate-400 dark:hover:border-primary dark:hover:text-primary transition-all">
                        <span class="material-symbols-outlined text-xl">arrow_back</span>
                    </button>
                    <div id="testi-dots" class="flex gap-2 items-center">
                    </div>
                    <button id="testi-next" onclick="testiSlide(1)"
                        class="w-10 h-10 rounded-full border-2 border-slate-200 dark:border-border-dark flex items-center justify-center text-slate-500 hover:border-primary hover:text-primary dark:text-slate-400 dark:hover:border-primary dark:hover:text-primary transition-all">
                        <span class="material-symbols-outlined text-xl">arrow_forward</span>
                    </button>
                </div>

                <?php else: ?>
                <!-- No testimonials yet -->
                <div class="text-center py-16 rounded-2xl bg-white dark:bg-card-dark border border-dashed border-slate-200 dark:border-border-dark" data-reveal>
                    <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600 block mb-3">rate_review</span>
                    <h3 class="font-bold text-slate-500 dark:text-slate-400">No reviews yet</h3>
                    <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Be the first to share your experience!</p>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- ══════════════════ WRITE A REVIEW ══════════════════ -->
        <section id="write-review" class="py-20 bg-white dark:bg-card-dark border-y border-slate-100 dark:border-border-dark">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10" data-reveal>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-primary mb-3">
                        <span class="material-symbols-outlined text-sm">edit_note</span>
                        Share Your Experience
                    </span>
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Leave a <span class="grad-text font-serif italic font-normal">Review</span>
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Your review will be displayed after our team approves it (usually within 24h).</p>
                    <?php if ($pendingCount > 0): ?>
                    <div class="pending-badge mt-3 mx-auto w-fit">
                        <span class="material-symbols-outlined text-xs">hourglass_top</span>
                        <?php echo $pendingCount; ?> review<?php echo $pendingCount > 1 ? 's' : ''; ?> pending approval
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Review form card -->
                <div class="bg-slate-50 dark:bg-bg-dark rounded-3xl p-7 sm:p-10 border border-slate-100 dark:border-border-dark shadow-xl shadow-primary/5" data-reveal>

                    <!-- Success state -->
                    <div id="testi-success" class="flex-col items-center text-center py-6 gap-4">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2"
                            style="background:linear-gradient(135deg,#10B981,#0EA5E9);">
                            <span class="material-symbols-outlined text-white text-3xl">check_circle</span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">Thank you!</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm max-w-xs mx-auto">
                            Your review has been submitted successfully and will be visible after approval.
                        </p>
                        <button onclick="resetTestiForm()" class="mt-4 px-5 py-2 rounded-xl text-sm font-bold text-white" style="background:linear-gradient(135deg,#7C3AED,#4F46E5);">
                            Write Another
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="testi-form" onsubmit="submitTestimonial(event)" novalidate>

                        <!-- Name + Role -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2" for="testi-name">
                                    Your Name <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="testi-name" name="name" maxlength="100" required
                                    class="testi-form-input" placeholder="e.g. Priya Sharma" autocomplete="name" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2" for="testi-role">
                                    Your Role / Profession
                                </label>
                                <input type="text" id="testi-role" name="role" maxlength="100"
                                    class="testi-form-input" placeholder="e.g. Senior Developer" autocomplete="organization-title" />
                            </div>
                        </div>

                        <!-- Star rating -->
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-3">
                                Your Rating <span class="text-red-400">*</span>
                            </label>
                            <div class="flex items-center gap-2" id="star-container" role="group" aria-label="Star rating">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                <button type="button" class="star-btn" data-star="<?php echo $s; ?>"
                                    aria-label="<?php echo $s; ?> star<?php echo $s > 1 ? 's' : ''; ?>"
                                    onclick="setRating(<?php echo $s; ?>)">★</button>
                                <?php endfor; ?>
                                <span id="rating-label" class="text-xs text-slate-400 ml-2 font-medium">Click to rate</span>
                            </div>
                            <input type="hidden" id="testi-rating" name="rating" value="0" />
                        </div>

                        <!-- Review text -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider" for="testi-review">
                                    Your Review <span class="text-red-400">*</span>
                                </label>
                                <span id="char-count" class="text-xs text-slate-400">0 / 600</span>
                            </div>
                            <textarea id="testi-review" name="review" rows="4" maxlength="600" required
                                class="testi-form-input resize-none"
                                placeholder="Share your honest experience with BlogFusion — what did you love, what helped you most?"
                                oninput="updateCharCount(this)"></textarea>
                            <p class="text-xs text-slate-400 mt-1">Minimum 15 characters.</p>
                        </div>

                        <!-- Error area -->
                        <div id="testi-errors" class="hidden mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-900/15 border border-red-200 dark:border-red-800/40">
                            <ul id="testi-error-list" class="space-y-1"></ul>
                        </div>

                        <!-- Submit -->
                        <button type="submit" id="testi-submit"
                            class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl text-white font-bold text-sm transition-all hover:scale-[1.02] hover:shadow-xl hover:shadow-primary/30 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100"
                            style="background: linear-gradient(135deg, #7C3AED, #4F46E5);">
                            <span class="material-symbols-outlined text-xl" id="submit-icon">send</span>
                            <span id="submit-label">Submit Review</span>
                        </button>

                        <p class="text-center text-xs text-slate-400 mt-3">
                            <span class="material-symbols-outlined text-xs align-middle">lock</span>
                            Your review is safe — we never share personal info.
                        </p>
                    </form>
                </div>
            </div>
        </section>


        <!-- ══════════════════ FINAL CTA ══════════════════ -->
        <section class="py-24 bg-bg-light dark:bg-bg-dark">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-reveal>
                <span
                    class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-primary mb-4">
                    <span class="material-symbols-outlined text-sm">rocket_launch</span>
                    Join Today
                </span>
                <h2
                    class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-6">
                    Ready to Start Your<br>
                    <span class="font-serif italic font-normal grad-text">Reading Journey?</span>
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-lg max-w-xl mx-auto mb-10">
                    Create your free account and unlock access to hundreds of expert articles, curated picks, bookmarks,
                    and a passionate community.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo site_url('pages/register.php'); ?>"
                        class="group flex items-center justify-center gap-2 px-8 py-4 rounded-2xl text-white font-bold text-base transition-all hover:scale-105 hover:shadow-2xl hover:shadow-primary/40"
                        style="background: linear-gradient(135deg, #7C3AED, #4F46E5);">
                        <span class="material-symbols-outlined text-xl">person_add</span>
                        Create Free Account
                    </a>
                    <a href="<?php echo site_url('pages/login.php'); ?>"
                        class="flex items-center justify-center gap-2 px-8 py-4 rounded-2xl font-bold text-base border-2 border-slate-200 dark:border-border-dark text-slate-700 dark:text-slate-300 hover:border-primary hover:text-primary dark:hover:border-primary dark:hover:text-primary transition-all">
                        Sign In Instead
                        <span class="material-symbols-outlined text-xl">login</span>
                    </a>
                </div>
            </div>
        </section>

    </main>

    <!-- ══════════════════ FOOTER ══════════════════ -->
    <footer class="bg-white dark:bg-card-dark border-t border-slate-100 dark:border-border-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

                <!-- Brand -->
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-2.5 mb-4">
                        <?php
                        $footer_logo_idx = $siteSettings['logo'] ?? 'upload/site_image/logo1.png';
                        if (!preg_match('/^https?:\/\//i', $footer_logo_idx)) {
                            $footer_logo_idx = site_url($footer_logo_idx);
                        }
                        ?>
                        <img class="h-16 w-auto max-w-full object-contain" src="<?php echo $footer_logo_idx; ?>" alt="BlogFusion" />
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-5">
                        A modern blogging platform for curious minds — explore, learn, and connect with brilliant
                        thinkers worldwide.
                    </p>
                    <div class="flex gap-3">
                        <?php
                        $socials = [
                            ['icon' => 'share', 'label' => 'Share'],
                            ['icon' => 'public', 'label' => 'Web'],
                            ['icon' => 'favorite', 'label' => 'Like'],
                        ];
                        foreach ($socials as $soc): ?>
                            <a href="#" aria-label="<?php echo $soc['label']; ?>"
                                class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-bg-dark flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-primary hover:text-white dark:hover:bg-primary transition-all">
                                <span class="material-symbols-outlined text-lg"><?php echo $soc['icon']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h5 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-5">Quick
                        Links</h5>
                    <ul class="space-y-3">
                        <?php
                        $qlinks = [
                            ['label' => 'Home', 'href' => site_url('index.php')],
                            ['label' => 'Latest Posts', 'href' => site_url('index.php') . '#latest'],
                            ['label' => 'Categories', 'href' => site_url('index.php') . '#categories'],
                            ['label' => 'Trending', 'href' => site_url('index.php') . '#trending'],
                        ];
                        foreach ($qlinks as $ql): ?>
                            <li>
                                <a href="<?php echo escape_html($ql['href']); ?>"
                                    class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors group">
                                    <span
                                        class="material-symbols-outlined text-sm group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                                    <?php echo $ql['label']; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Categories -->
                <div>
                    <h5 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-5">
                        Categories</h5>
                    <ul class="space-y-3">
                        <?php foreach (array_slice($categoryStats, 0, 5) as $cat): ?>
                            <li>
                                <a href="<?php echo escape_html(site_url('pages/login.php')); ?>"
                                    class="text-sm text-slate-500 dark:text-slate-400 hover:text-accent dark:hover:text-accent transition-colors">
                                    <?php echo escape_html($cat['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($categoryStats)): ?>
                            <?php foreach (['Technology', 'Programming', 'Business', 'Design'] as $cn): ?>
                                <li><span class="text-sm text-slate-400"><?php echo $cn; ?></span></li>
                            <?php endforeach; endif; ?>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h5 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-5">Get in
                        Touch</h5>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 text-sm text-slate-500 dark:text-slate-400">
                            <span class="material-symbols-outlined text-primary mt-0.5 flex-shrink-0">mail</span>
                            <span><?php echo escape_html($siteSettings['contact_email'] ?? 'hello@blogfusion.com'); ?></span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-slate-500 dark:text-slate-400">
                            <span class="material-symbols-outlined text-primary mt-0.5 flex-shrink-0">location_on</span>
                            <span><?php echo nl2br(escape_html($siteSettings['contact_address'] ?? "Innovation District,\nTech City, TC 10101")); ?></span>
                        </li>
                        <li class="flex items-start gap-3 text-sm text-slate-500 dark:text-slate-400">
                            <span class="material-symbols-outlined text-primary mt-0.5 flex-shrink-0">schedule</span>
                            <span><?php echo escape_html($siteSettings['contact_hours'] ?? 'Mon–Fri, 9am–6pm IST'); ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom bar -->
            <div
                class="pt-8 border-t border-slate-100 dark:border-border-dark flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-xs text-slate-400">
                    © <?php echo date('Y'); ?> BlogFusion. All rights reserved. Made with <span
                        class="text-red-400">♥</span> for curious minds.
                </p>
                <div class="flex items-center gap-6 text-xs text-slate-400">
                    <a href="<?php echo site_url('pages/privacy-policy.php'); ?>" class="hover:text-primary transition-colors">Privacy Policy</a>
                    <a href="<?php echo site_url('pages/terms-of-service.php'); ?>" class="hover:text-primary transition-colors">Terms of Service</a>
                    <a href="<?php echo site_url('pages/cookie-policy.php'); ?>" class="hover:text-primary transition-colors">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ══════════════════ SCRIPTS ══════════════════ -->
    <script>
        // Dark mode
        const html = document.documentElement;
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

        // Restore preference (defaults to light theme)
        const saved = localStorage.getItem('bf_dark');
        if (saved === '1') {
            setDark(true, false);
        } else {
            setDark(false, false);
        }

        document.getElementById('dark-toggle')?.addEventListener('click', () => {
            setDark(!html.classList.contains('dark'), true);
        });

        // Navbar scroll effect
        const nav = document.getElementById('main-nav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 40) nav.classList.add('scrolled');
            else nav.classList.remove('scrolled');
        }, { passive: true });

        // Mobile menu
        const mobileToggle = document.getElementById('mobile-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileIcon = document.getElementById('mobile-icon');
        let menuOpen = false;

        mobileToggle?.addEventListener('click', () => {
            menuOpen = !menuOpen;
            if (menuOpen) {
                mobileMenu.classList.add('open');
                mobileIcon.textContent = 'close';
            } else {
                mobileMenu.classList.remove('open');
                mobileIcon.textContent = 'menu';
            }
        });

        // Close menu on link click
        mobileMenu?.querySelectorAll('a').forEach(a => {
            a.addEventListener('click', () => {
                menuOpen = false;
                mobileMenu.classList.remove('open');
                mobileIcon.textContent = 'menu';
            });
        });

        // Scroll-reveal
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        document.querySelectorAll('[data-reveal]').forEach(el => revealObserver.observe(el));

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="<?php echo site_url('index.php'); ?>#"]').forEach(a => {
            a.addEventListener('click', e => {
                const hash = a.getAttribute('href').split('#')[1];
                const target = document.getElementById(hash);
                if (target) {
                    e.preventDefault();
                    window.scrollTo({ top: target.offsetTop - 72, behavior: 'smooth' });
                }
            });
        });


        // ── TESTIMONIAL CAROUSEL ─────────────────────────────────────────
        (function() {
            const track  = document.getElementById('testi-track');
            if (!track) return;

            const slides = track.querySelectorAll('.testi-slide');
            const controls = document.getElementById('testi-controls');
            const dotsContainer = document.getElementById('testi-dots');
            let current  = 0;
            let total    = slides.length;
            let autoTimer;

            // 1 page is shown on mobile (<768px), 2 on tablet (<1024px), 3 on desktop
            function getVisible() {
                if (window.innerWidth >= 1024) return 3;
                if (window.innerWidth >= 768)  return 2;
                return 1;
            }

            function maxIndex() {
                return Math.max(0, total - getVisible());
            }

            function updateControls() {
                if (!controls || !dotsContainer) return;
                const visible = getVisible();
                const maxIdx = maxIndex();

                if (maxIdx <= 0) {
                    controls.style.display = 'none';
                    return;
                }

                controls.style.display = 'flex';

                let dotsHtml = '';
                for (let i = 0; i <= maxIdx; i++) {
                    const activeClass = i === current ? 'active' : '';
                    dotsHtml += `<div class="testi-dot ${activeClass}" onclick="testiGoTo(${i})"></div>`;
                }
                dotsContainer.innerHTML = dotsHtml;
            }

            function goTo(idx) {
                current = Math.max(0, Math.min(idx, maxIndex()));
                const pct = (100 / getVisible()) * current;
                track.style.transform = 'translateX(-' + pct + '%)';
                
                const dots = document.querySelectorAll('.testi-dot');
                dots.forEach((d, i) => d.classList.toggle('active', i === current));
            }

            window.testiSlide = function(dir) {
                goTo(current + dir);
                resetAuto();
            };

            window.testiGoTo = function(idx) {
                goTo(idx);
                resetAuto();
            };

            function resetAuto() {
                clearInterval(autoTimer);
                if (maxIndex() > 0) {
                    autoTimer = setInterval(() => {
                        goTo(current < maxIndex() ? current + 1 : 0);
                    }, 5000);
                }
            }

            window.addEventListener('resize', () => {
                updateControls();
                goTo(current);
            });

            updateControls();
            goTo(0);
            resetAuto();
        })();

        // ── STAR RATING ──────────────────────────────────────────────────
        let selectedRating = 0;
        const ratingLabels = ['', 'Poor', 'Fair', 'Good', 'Great', 'Excellent! ★'];

        function setRating(val) {
            selectedRating = val;
            document.getElementById('testi-rating').value = val;
            document.getElementById('rating-label').textContent = ratingLabels[val] || '';
            document.querySelectorAll('.star-btn').forEach(btn => {
                const s = parseInt(btn.dataset.star);
                btn.classList.toggle('active', s <= val);
            });
        }

        document.querySelectorAll('.star-btn').forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                const hov = parseInt(btn.dataset.star);
                document.querySelectorAll('.star-btn').forEach(b => {
                    b.classList.toggle('active', parseInt(b.dataset.star) <= hov);
                });
            });
            btn.addEventListener('mouseleave', () => {
                document.querySelectorAll('.star-btn').forEach(b => {
                    b.classList.toggle('active', parseInt(b.dataset.star) <= selectedRating);
                });
            });
        });

        // ── CHARACTER COUNTER ────────────────────────────────────────────
        function updateCharCount(el) {
            const len = el.value.length;
            const counter = document.getElementById('char-count');
            counter.textContent = len + ' / 600';
            counter.style.color = len > 550 ? '#EF4444' : len > 400 ? '#F59E0B' : '';
        }

        // ── TESTIMONIAL FORM SUBMIT ──────────────────────────────────────
        async function submitTestimonial(e) {
            e.preventDefault();

            const name   = document.getElementById('testi-name').value.trim();
            const role   = document.getElementById('testi-role').value.trim();
            const rating = parseInt(document.getElementById('testi-rating').value);
            const review = document.getElementById('testi-review').value.trim();

            // Client-side validation
            const errs = [];
            if (name.length < 2)   errs.push('Name must be at least 2 characters.');
            if (rating < 1)        errs.push('Please select a star rating.');
            if (review.length < 15) errs.push('Review must be at least 15 characters.');
            if (review.length > 600) errs.push('Review is too long (max 600 chars).');

            if (errs.length) {
                showTestiErrors(errs);
                return;
            }

            // Loading state
            const btn   = document.getElementById('testi-submit');
            const icon  = document.getElementById('submit-icon');
            const label = document.getElementById('submit-label');
            btn.disabled = true;
            icon.textContent  = 'hourglass_top';
            label.textContent = 'Submitting…';

            try {
                const body = new FormData();
                body.append('action', 'submit_testimonial');
                body.append('name',   name);
                body.append('role',   role || 'Reader');
                body.append('rating', rating);
                body.append('review', review);

                const res  = await fetch(window.location.pathname, { method: 'POST', body });
                const data = await res.json();

                if (data.success) {
                    hideTestiErrors();
                    document.getElementById('testi-form').style.display    = 'none';
                    document.getElementById('testi-success').classList.add('show');
                    if (typeof window.showToast === 'function') {
                        window.showToast('✅ Review submitted! Pending approval.', 'success');
                    }
                } else {
                    showTestiErrors(data.errors || ['Something went wrong. Please try again.']);
                    btn.disabled = false;
                    icon.textContent  = 'send';
                    label.textContent = 'Submit Review';
                }
            } catch (err) {
                showTestiErrors(['Network error. Please check your connection.']);
                btn.disabled = false;
                icon.textContent  = 'send';
                label.textContent = 'Submit Review';
            }
        }

        function showTestiErrors(errs) {
            const box  = document.getElementById('testi-errors');
            const list = document.getElementById('testi-error-list');
            list.innerHTML = errs.map(e =>
                '<li class="flex items-start gap-2 text-sm text-red-600 dark:text-red-400 font-medium">' +
                '<span class="material-symbols-outlined text-base mt-0.5">error</span>' + e + '</li>'
            ).join('');
            box.classList.remove('hidden');
            box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function hideTestiErrors() {
            document.getElementById('testi-errors').classList.add('hidden');
            document.getElementById('testi-error-list').innerHTML = '';
        }

        function resetTestiForm() {
            document.getElementById('testi-form').style.display     = '';
            document.getElementById('testi-success').classList.remove('show');
            document.getElementById('testi-form').reset();
            document.getElementById('testi-rating').value = '0';
            document.getElementById('char-count').textContent = '0 / 600';
            document.getElementById('rating-label').textContent = 'Click to rate';
            selectedRating = 0;
            document.querySelectorAll('.star-btn').forEach(b => b.classList.remove('active'));
            const btn   = document.getElementById('testi-submit');
            const icon  = document.getElementById('submit-icon');
            const label = document.getElementById('submit-label');
            btn.disabled = false;
            icon.textContent  = 'send';
            label.textContent = 'Submit Review';
            hideTestiErrors();
        }
    </script>

</body>

</html>
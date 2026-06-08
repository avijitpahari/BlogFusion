<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAuthor();
include BASE_PATH . 'include/db.php';

// ---- AJAX ACTIONS FOR PASSWORD ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    $user_id = (int)$_SESSION['user_id'];
    
    if ($action === 'change_password') {
        $old_pwd = $_POST['old_password'] ?? '';
        $new_pwd = $_POST['new_password'] ?? '';
        
        $pw_query = "SELECT password FROM users WHERE id = $user_id";
        $pw_res = mysqli_query($conn, $pw_query);
        $pw_row = mysqli_fetch_assoc($pw_res);
        
        if ($pw_row && password_verify($old_pwd, $pw_row['password'])) {
            $hashed = password_hash($new_pwd, PASSWORD_DEFAULT);
            $upd = mysqli_query($conn, "UPDATE users SET password = '$hashed' WHERE id = $user_id");
            if ($upd) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database update failed']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Incorrect current password']);
        }
        exit;
    }
}

include BASE_PATH . 'include/author_nav_sidebar.php';
global $data1;

// Fetch published posts and total views (Reach)
$stats_query = "
    SELECT 
        COUNT(id) AS published_posts,
        SUM(views) AS total_views
    FROM posts 
    WHERE author_id = {$data1['id']} AND status = 'published'
";
$stats_res = mysqli_query($conn, $stats_query);
$stats_row = mysqli_fetch_assoc($stats_res);

$published_posts = (int)($stats_row['published_posts'] ?? 0);
$total_views = (int)($stats_row['total_views'] ?? 0);

// Fetch comments on the author's posts
$comments_query = "
    SELECT COUNT(c.id) AS total_comments 
    FROM comments c 
    JOIN posts p ON c.post_id = p.id 
    WHERE p.author_id = {$data1['id']} AND p.status = 'published'
";
$comments_res = mysqli_query($conn, $comments_query);
$comments_row = mysqli_fetch_assoc($comments_res);
$total_comments = (int)($comments_row['total_comments'] ?? 0);

// Fetch reactions on the author's posts
$reactions_query = "
    SELECT COUNT(r.id) AS total_reactions 
    FROM reactions r 
    JOIN posts p ON r.post_id = p.id 
    WHERE p.author_id = {$data1['id']} AND p.status = 'published'
";
$reactions_res = mysqli_query($conn, $reactions_query);
$reactions_row = mysqli_fetch_assoc($reactions_res);
$total_reactions = (int)($reactions_row['total_reactions'] ?? 0);

// Fetch active posts count (posts that have views > 0 OR comments OR reactions)
$active_posts_query = "
    SELECT COUNT(DISTINCT p.id) AS active_posts
    FROM posts p
    LEFT JOIN comments c ON p.id = c.post_id
    LEFT JOIN reactions r ON p.id = r.post_id
    WHERE p.author_id = {$data1['id']} 
      AND p.status = 'published' 
      AND (p.views > 0 OR c.id IS NOT NULL OR r.id IS NOT NULL)
";
$active_posts_res = mysqli_query($conn, $active_posts_query);
$active_posts_row = mysqli_fetch_assoc($active_posts_res);
$active_posts = (int)($active_posts_row['active_posts'] ?? 0);

// Calculate Impact %
$impact_pct = 0;
if ($published_posts > 0) {
    $impact_pct = (int)round(($active_posts / $published_posts) * 80 + min(20, ($total_comments + $total_reactions) * 5));
    if ($impact_pct > 100) $impact_pct = 100;
}

// Function to format Reach
if (!function_exists('formatReach')) {
    function formatReach($n) {
        if ($n >= 1000000) {
            $val = round($n / 1000000, 1);
            return ($val == (int)$val ? (int)$val : $val) . 'M';
        }
        if ($n >= 1000) {
            $val = round($n / 1000, 1);
            return ($val == (int)$val ? (int)$val : $val) . 'k';
        }
        return $n;
    }
}

?>

<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <link rel="icon" type="image/png" href="<?php echo defined('BASE_URL') ? BASE_URL : '/BlogFusion/'; ?>upload/site_image/logo2.png" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>User Profile | Luminous Editor</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&amp;display=swap"
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
                        "tertiary-container": "#bf2076",
                        "secondary-fixed-dim": "#c3c0ff",
                        "inverse-surface": "#332f39",
                        "on-tertiary-fixed": "#3e0022",
                        "surface-container-low": "#f9f1ff",
                        "primary-container": "#7c3aed",
                        "surface-container-lowest": "#ffffff",
                        "secondary-container": "#645efb",
                        "on-primary-container": "#ede0ff",
                        "secondary": "#4b41e1",
                        "outline": "#7b7487",
                        "inverse-primary": "#d2bbff",
                        "on-error-container": "#93000a",
                        "on-background": "#1d1a24",
                        "primary": "#630ed4",
                        "primary-fixed-dim": "#d2bbff",
                        "inverse-on-surface": "#f6eefc",
                        "surface-container-highest": "#e8dfee",
                        "on-secondary-fixed-variant": "#3323cc",
                        "surface-tint": "#732ee4",
                        "on-tertiary-fixed-variant": "#8c0053",
                        "on-error": "#ffffff",
                        "background": "#fef7ff",
                        "on-primary-fixed-variant": "#5a00c6",
                        "on-primary": "#ffffff",
                        "tertiary-fixed": "#ffd9e4",
                        "on-secondary-container": "#fffbff",
                        "on-primary-fixed": "#25005a",
                        "tertiary-fixed-dim": "#ffb0cd",
                        "surface": "#fef7ff",
                        "surface-container": "#f3ebfa",
                        "surface-bright": "#fef7ff",
                        "secondary-fixed": "#e2dfff",
                        "on-tertiary-container": "#ffdde7",
                        "on-tertiary": "#ffffff",
                        "on-secondary-fixed": "#0f0069",
                        "surface-container-high": "#ede5f4",
                        "primary-fixed": "#eaddff",
                        "surface-variant": "#e8dfee",
                        "tertiary": "#9b005c",
                        "error": "#ba1a1a",
                        "on-surface-variant": "#4a4455",
                        "outline-variant": "#ccc3d8",
                        "surface-dim": "#dfd7e6",
                        "on-secondary": "#ffffff",
                        "on-surface": "#1d1a24",
                        "error-container": "#ffdad6"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Public Sans"],
                        "body": ["Public Sans"],
                        "label": ["Public Sans"]
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
        }

        .modal-backdrop {
            background: rgba(29, 26, 36, 0.4);
            backdrop-filter: blur(8px);
        }

        @media (max-width: 768px) {
            .sidebar-closed {
                transform: translateX(-100%);
            }

            .sidebar-open {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="bg-surface text-on-surface antialiased">
    <!-- Modal Component -->
    <div class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4" id="edit-profile-modal">
        <div class="absolute inset-0 modal-backdrop" onclick="closeModal()"></div>
        <div
            class="relative bg-surface-container-lowest w-full max-w-lg rounded-[2.5rem] shadow-2xl shadow-primary/20 overflow-hidden border border-outline-variant/20">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-on-surface">Edit Profile</h3>
                    <button
                        class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container-high transition-colors"
                        onclick="closeModal()">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form class="space-y-5" enctype="multipart/form-data" action="<?= BASE_URL ?>actions/author.php" method="POST">
                    <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1" for="name">Name</label>
                        <input
                            class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none"
                            id="edit_name" type="text" value="Alex Rivera" name="name" />
                    </div>
                    <!-- Email input removed -->
                    <input type="hidden" id="id" name="id">
                    <!-- <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1" for="bio">Bio</label>
                        <textarea
                            class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none resize-none"
                            id="bio"
                            rows="3">Editor-in-Chief at Blog Fusion. Digital storyteller and content strategist.</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1"
                                for="location">Location</label>
                            <input
                                class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none"
                                id="location" type="text" value="San Francisco, CA" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1"
                                for="website">Website</label>
                            <input
                                class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none"
                                id="website" placeholder="https://" type="text" />
                        </div>
                    </div> -->


                    <!-- === image === -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Profile Picture</label>
                        <div
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-accent transition duration-200">
                            <div class="space-y-1 text-center w-full relative group">
                                <!-- Placeholder UI -->
                                <div class="block" id="upload-placeholder">
                                    <svg aria-hidden="true" class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                        stroke="currentColor" viewbox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-indigo-500"
                                            for="file-upload">
                                            <span>Upload a file</span>
                                            <input accept="image/*" class="sr-only" id="file-upload" name="new_image"
                                                onchange="handleImagePreview(event)" type="file" />
                                            <input type="hidden" name="old_image" id="old-image" value="">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG up to 10MB</p>
                                </div>
                                <!-- Image Preview UI -->
                                <div class="hidden relative inline-block mx-auto" id="preview-container">
                                    <img alt="Profile Preview"
                                        class="h-32 w-32 object-cover rounded-full border-4 border-white shadow-md mx-auto"
                                        id="image-preview" src="" />
                                    <button
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 transition-colors"
                                        onclick="removeImage()" type="button">
                                        <span class="material-symbols-outlined text-sm">close</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

            </div>
            <div class="flex gap-3 p-8 pt-0">
                <button
                    class="flex-1 py-4 rounded-2xl font-bold bg-surface-container-high text-on-surface hover:bg-surface-container-highest transition-all active:scale-[0.98]"
                    onclick="closeModal()">
                    Cancel
                </button>
                <button type="submit" name="profile_update"
                    class="flex-1 py-4 rounded-2xl font-bold bg-primary text-white shadow-lg shadow-primary/20 hover:bg-primary-container transition-all active:scale-[0.98]">
                    Save Changes
                </button>
            </div>
            </form>
        </div>
    </div>

    <!-- Change Password Modal — 3-Step OTP Flow -->
    <div class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4" id="change-password-modal">
        <div class="absolute inset-0 modal-backdrop" onclick="closePwdModal()"></div>
        <div class="relative bg-surface-container-lowest w-full max-w-md rounded-[2.5rem] shadow-2xl shadow-primary/20 overflow-hidden border border-outline-variant/20">
            <div class="p-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <h3 class="text-2xl font-bold text-on-surface">Change Password</h3>
                        <p id="pwd-step-label" class="text-xs text-on-surface-variant mt-0.5">Step 1 of 3 · Verify Identity</p>
                    </div>
                    <button class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container-high transition-colors" onclick="closePwdModal()">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <!-- Step progress bar -->
                <div class="flex gap-1.5 mb-6 mt-3">
                    <div class="h-1 flex-1 rounded-full bg-primary transition-all" id="pbar-1"></div>
                    <div class="h-1 flex-1 rounded-full bg-surface-container transition-all" id="pbar-2"></div>
                    <div class="h-1 flex-1 rounded-full bg-surface-container transition-all" id="pbar-3"></div>
                </div>

                <!-- STEP 1: Enter current password -->
                <div id="pwd-step-1" class="space-y-5">
                    <div class="w-14 h-14 bg-primary-fixed/30 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-primary text-2xl">lock</span>
                    </div>
                    <p class="text-sm text-on-surface-variant">Enter your current password to receive a 6-digit verification code on your registered email.</p>
                    <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1">Current Password</label>
                        <input class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none" id="oldPwd" type="password" placeholder="••••••••" />
                    </div>
                    <button onclick="pwdSendOtp()" id="pwd-send-btn"
                        class="w-full py-3.5 rounded-2xl font-bold bg-primary text-white shadow-lg shadow-primary/20 hover:opacity-90 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">send</span> Send Verification Code
                    </button>
                    <p class="text-center text-xs text-on-surface-variant mt-4">
                        Forgot your current password? 
                        <button onclick="switchToForgotFlow()" class="text-primary font-bold hover:underline">Reset via Email</button>
                    </p>
                </div>

                <!-- STEP 2: Enter OTP -->
                <div id="pwd-step-2" class="space-y-5 hidden">
                    <div class="w-14 h-14 bg-tertiary-fixed/30 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-tertiary text-2xl">mark_email_read</span>
                    </div>
                    <p class="text-sm text-on-surface-variant">A 6-digit code was sent to <strong id="pwd-otp-email" class="text-on-surface"></strong>. Enter it below. <span class="text-primary font-semibold">Expires in 10 min.</span></p>
                    <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1">6-Digit Code</label>
                        <div class="flex gap-2 justify-between" id="otp-boxes">
                            <input type="text" maxlength="1" class="otp-digit w-12 h-14 text-center text-2xl font-black bg-surface-container rounded-xl border-2 border-transparent focus:border-primary focus:ring-0 outline-none transition-all" />
                            <input type="text" maxlength="1" class="otp-digit w-12 h-14 text-center text-2xl font-black bg-surface-container rounded-xl border-2 border-transparent focus:border-primary focus:ring-0 outline-none transition-all" />
                            <input type="text" maxlength="1" class="otp-digit w-12 h-14 text-center text-2xl font-black bg-surface-container rounded-xl border-2 border-transparent focus:border-primary focus:ring-0 outline-none transition-all" />
                            <input type="text" maxlength="1" class="otp-digit w-12 h-14 text-center text-2xl font-black bg-surface-container rounded-xl border-2 border-transparent focus:border-primary focus:ring-0 outline-none transition-all" />
                            <input type="text" maxlength="1" class="otp-digit w-12 h-14 text-center text-2xl font-black bg-surface-container rounded-xl border-2 border-transparent focus:border-primary focus:ring-0 outline-none transition-all" />
                            <input type="text" maxlength="1" class="otp-digit w-12 h-14 text-center text-2xl font-black bg-surface-container rounded-xl border-2 border-transparent focus:border-primary focus:ring-0 outline-none transition-all" />
                        </div>
                    </div>
                    <button onclick="pwdVerifyOtp()" id="pwd-verify-btn"
                        class="w-full py-3.5 rounded-2xl font-bold bg-primary text-white shadow-lg shadow-primary/20 hover:opacity-90 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">verified</span> Verify Code
                    </button>
                    <button onclick="pwdSendOtp()" class="w-full text-xs text-primary font-semibold hover:underline">Resend Code</button>
                </div>

                <!-- STEP 3: New Password -->
                <div id="pwd-step-3" class="space-y-5 hidden">
                    <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-green-600 text-2xl">lock_reset</span>
                    </div>
                    <p class="text-sm text-on-surface-variant">Identity verified! Set your new password below.</p>
                    <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1">New Password</label>
                        <input class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none" id="newPwd" type="password" placeholder="••••••••" oninput="checkStrength(this.value)" />
                        <div class="h-1.5 rounded-full bg-surface-container mt-2 overflow-hidden"><div id="sfill" class="h-full rounded-full transition-all duration-300" style="width:0%"></div></div>
                        <p id="stext" class="text-xs text-on-surface-variant mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1">Confirm New Password</label>
                        <input class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none" id="confirmPwd" type="password" placeholder="••••••••" />
                    </div>
                    <button onclick="updatePassword()" id="pwd-update-btn"
                        class="w-full py-3.5 rounded-2xl font-bold bg-primary text-white shadow-lg shadow-primary/20 hover:opacity-90 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">check_circle</span> Update Password
                    </button>
                </div>

                <!-- FORGOT STEP 1: Enter email/Request OTP -->
                <div id="forgot-step-1" class="space-y-5 hidden">
                    <div class="w-14 h-14 bg-primary-fixed/30 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-primary text-2xl">lock_reset</span>
                    </div>
                    <p class="text-sm text-on-surface-variant">We'll send a password verification code to your registered email address.</p>
                    <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1">Email Address</label>
                        <input class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none opacity-80 cursor-not-allowed" id="forgotEmail" type="email" value="<?= htmlspecialchars($data1['email']) ?>" readonly />
                    </div>
                    <button onclick="forgotSendOtp()" id="forgot-send-btn"
                        class="w-full py-3.5 rounded-2xl font-bold bg-primary text-white shadow-lg shadow-primary/20 hover:opacity-90 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">send</span> Send Verification Code
                    </button>
                    <p class="text-center text-xs text-on-surface-variant mt-4">
                        Remember your password? 
                        <button onclick="switchToNormalFlow()" class="text-primary font-bold hover:underline">Go back</button>
                    </p>
                </div>

                <!-- FORGOT STEP 2: Enter OTP & New Password -->
                <div id="forgot-step-2" class="space-y-5 hidden">
                    <div class="w-14 h-14 bg-tertiary-fixed/30 rounded-2xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-tertiary text-2xl">mark_email_read</span>
                    </div>
                    <p class="text-sm text-on-surface-variant">A 6-digit code was sent to <strong id="forgot-otp-email" class="text-on-surface"></strong>. Enter it below along with your new password.</p>
                    <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1">6-Digit Code</label>
                        <div class="flex gap-2 justify-between" id="forgot-otp-boxes">
                            <input type="text" maxlength="1" class="forgot-otp-digit w-12 h-14 text-center text-2xl font-black bg-surface-container rounded-xl border-2 border-transparent focus:border-primary focus:ring-0 outline-none transition-all" />
                            <input type="text" maxlength="1" class="forgot-otp-digit w-12 h-14 text-center text-2xl font-black bg-surface-container rounded-xl border-2 border-transparent focus:border-primary focus:ring-0 outline-none transition-all" />
                            <input type="text" maxlength="1" class="forgot-otp-digit w-12 h-14 text-center text-2xl font-black bg-surface-container rounded-xl border-2 border-transparent focus:border-primary focus:ring-0 outline-none transition-all" />
                            <input type="text" maxlength="1" class="forgot-otp-digit w-12 h-14 text-center text-2xl font-black bg-surface-container rounded-xl border-2 border-transparent focus:border-primary focus:ring-0 outline-none transition-all" />
                            <input type="text" maxlength="1" class="forgot-otp-digit w-12 h-14 text-center text-2xl font-black bg-surface-container rounded-xl border-2 border-transparent focus:border-primary focus:ring-0 outline-none transition-all" />
                            <input type="text" maxlength="1" class="forgot-otp-digit w-12 h-14 text-center text-2xl font-black bg-surface-container rounded-xl border-2 border-transparent focus:border-primary focus:ring-0 outline-none transition-all" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1">New Password</label>
                        <input class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none" id="forgotNewPwd" type="password" placeholder="••••••••" oninput="checkStrengthForgot(this.value)" />
                        <div class="h-1.5 rounded-full bg-surface-container mt-2 overflow-hidden"><div id="sfillForgot" class="h-full rounded-full transition-all duration-300" style="width:0%"></div></div>
                        <p id="stextForgot" class="text-xs text-on-surface-variant mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1">Confirm New Password</label>
                        <input class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none" id="forgotConfirmPwd" type="password" placeholder="••••••••" />
                    </div>
                    <button onclick="forgotVerifyAndReset()" id="forgot-reset-btn"
                        class="w-full py-3.5 rounded-2xl font-bold bg-primary text-white shadow-lg shadow-primary/20 hover:opacity-90 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">verified</span> Verify & Reset Password
                    </button>
                    <p class="text-center text-xs text-on-surface-variant mt-4">
                        Remember your password? 
                        <button onclick="switchToNormalFlow()" class="text-primary font-bold hover:underline">Go back</button>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <!-- Toast Component -->
    <div id="toast" class="hidden fixed top-6 left-1/2 -translate-x-1/2 z-[9999] px-5 py-3 rounded-2xl shadow-xl text-sm font-bold flex items-center gap-2 bg-on-surface text-surface transition-all duration-300"></div>
    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-black/40 z-40 md:hidden hidden transition-opacity" id="sidebar-overlay"
        onclick="toggleSidebar()"></div>
    <!-- SideNavBar Component -->
    <?= author_slidebar('profile') ?>
    <!-- TopNavBar Component -->
    <?= author_navbar(); ?>
    <!-- Main Content Area -->
    <main class="md:ml-64 pt-24 px-4 md:px-12 pb-12 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <!-- Section 1: Profile Overview -->
            <section class="bg-surface-container-low rounded-[2rem] p-6 md:p-10 relative overflow-hidden mb-8">
                <!-- Abstract Background Ornament -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-10 relative z-10">
                    <div class="relative">
                        <div
                            class="w-40 h-40 md:w-48 md:h-48 rounded-full border-4 border-surface-container-lowest shadow-xl shadow-primary/10 overflow-hidden">
                            <img class="h-full w-full object-cover"
                                data-alt="close-up of Alex Rivera smiling professionally against a clean studio background with soft natural lighting"
                                src="<?= BASE_URL . $data1['profile_image'] ?>" />
                        </div>
                        <div
                            class="absolute bottom-2 right-2 bg-primary text-white w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center border-4 border-surface-container-low">
                            <span class="material-symbols-outlined text-xs md:text-sm">verified</span>
                        </div>
                    </div>
                    <div class="flex-1 text-center md:text-left mt-4 md:mt-0">
                        <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4">
                            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-on-surface">
                                <?= $data1['name'] ?>
                            </h2>
                            <!-- <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full bg-secondary-container text-on-secondary-container text-xs font-bold tracking-widest uppercase self-center md:self-auto">
                                EDITOR-IN-CHIEF
                            </span> -->
                        </div>
                        <div class="space-y-3 mb-8">
                            <!-- Email display removed -->
                            <div
                                class="flex items-center justify-center md:justify-start gap-2 text-on-surface-variant">
                                <span class="material-symbols-outlined text-lg">calendar_today</span>
                                <span class="text-sm md:text-body-md">Joined
                                    <?php echo date("M d, Y", strtotime($data1['created_at'])); ?></span>
                            </div>
                            <!-- <div
                                class="flex items-center justify-center md:justify-start gap-2 text-on-surface-variant">
                                <span class="material-symbols-outlined text-lg">location_on</span>
                                <span class="text-sm md:text-body-md">San Francisco, CA</span>
                            </div> -->
                        </div>
                        <!-- Stats Strip - Asymmetrical layout -->
                        <div class="grid grid-cols-3 gap-3 md:gap-8 border-t border-outline-variant/30 pt-8">
                            <div>
                                <p
                                    class="text-[11px] md:text-xs uppercase tracking-widest text-on-surface-variant font-bold mb-1">
                                    Published Posts</p>
                                <p class="text-2xl md:text-3xl font-extrabold text-primary">
                                    <?= $published_posts ?>
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-[11px] md:text-xs uppercase tracking-widest text-on-surface-variant font-bold mb-1">
                                    Reach</p>
                                <p class="text-2xl md:text-3xl font-extrabold text-secondary">
                                    <?= formatReach($total_views) ?>
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-[11px] md:text-xs uppercase tracking-widest text-on-surface-variant font-bold mb-1">
                                    Impact</p>
                                <p class="text-2xl md:text-3xl font-extrabold text-tertiary">
                                    <?= $impact_pct ?>%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Section 2: Profile Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Edit Profile CTA -->
                <button
                    class="group flex items-center justify-between p-6 bg-gradient-to-br from-primary to-primary-container text-white rounded-3xl shadow-lg shadow-primary/20 transition-all hover:scale-[1.02] active:scale-95"
                    onclick="editauthor()">
                    <div class="flex items-center gap-4 text-left">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                            <span class="material-symbols-outlined">edit</span>
                        </div>
                        <div>
                            <p class="font-bold text-lg">Edit Profile</p>
                            <p class="text-white/70 text-sm">Update personal information</p>
                        </div>
                    </div>
                    <span
                        class="material-symbols-outlined opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
                </button>
                <!-- Change Password CTA -->
                <button onclick="openPwdModal()"
                    class="group flex items-center justify-between p-6 bg-surface-container text-on-surface rounded-3xl transition-all hover:bg-surface-container-high hover:scale-[1.02] active:scale-95">
                    <div class="flex items-center gap-4 text-left">
                        <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined">lock</span>
                        </div>
                        <div>
                            <p class="font-bold text-lg text-on-surface">Change Password</p>
                            <p class="text-on-surface-variant text-sm">Secure your account</p>
                        </div>
                    </div>
                    <span
                        class="material-symbols-outlined opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
                </button>
            </div>

        </div>
    </main>
    <script>
        function openModal() {
            document.getElementById('edit-profile-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            document.getElementById('edit-profile-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        function toggleSidebar() {
            const sidebar = document.getElementById('main-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isClosed = sidebar.classList.contains('sidebar-closed');

            if (isClosed) {
                sidebar.classList.remove('sidebar-closed');
                sidebar.classList.add('sidebar-open');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('sidebar-closed');
                sidebar.classList.remove('sidebar-open');
                overlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }
        function handleImagePreview(event) {
            const input = event.target;
            const placeholder = document.getElementById('upload-placeholder');
            const previewContainer = document.getElementById('preview-container');
            const previewImg = document.getElementById('image-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    placeholder.classList.add('hidden');
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        function editauthor() {
            let edit_name = document.getElementById("edit_name");
            let id = document.getElementById("id");
            let image_preview = document.getElementById("image-preview");
            let placeholder = document.getElementById('upload-placeholder');
            let previewContainer = document.getElementById('preview-container');
            let image = "<?= $data1['profile_image'] ?>";
            edit_name.value = "<?= $data1['name'] ?>";
            id.value = <?= $data1['id'] ?>;
            image_preview.src = '<?= BASE_URL ?>' + image;
            document.getElementById('old-image').value = image;
            placeholder.classList.add('hidden');
            previewContainer.classList.remove('hidden');

            openModal();
        }
        function removeImage() {
            const input = document.getElementById('file-upload');
            const placeholder = document.getElementById('upload-placeholder');
            const previewContainer = document.getElementById('preview-container');
            const previewImg = document.getElementById('image-preview');

            input.value = '';
            previewImg.src = '';
            previewContainer.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
        /* ── OTP-based Change Password ─────────────────────────── */
        let isForgotFlow = false;

        function openPwdModal() {
            document.getElementById('change-password-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            switchToNormalFlow();
        }
        function closePwdModal() {
            document.getElementById('change-password-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            switchToNormalFlow();
        }
        function switchToForgotFlow() {
            isForgotFlow = true;
            document.getElementById('pwd-step-label').textContent = 'Forgot Password · Step 1 of 2 · Send Code';
            
            // Hide normal steps & progress bars
            [1, 2, 3].forEach(i => document.getElementById('pwd-step-' + i).classList.add('hidden'));
            document.getElementById('pbar-3').classList.add('hidden');
            
            document.getElementById('pbar-1').className = "h-1 flex-1 rounded-full bg-primary transition-all";
            document.getElementById('pbar-2').className = "h-1 flex-1 rounded-full bg-surface-container transition-all";

            // Show forgot step 1
            document.getElementById('forgot-step-1').classList.remove('hidden');
            document.getElementById('forgot-step-2').classList.add('hidden');
        }
        function switchToNormalFlow() {
            isForgotFlow = false;
            document.getElementById('pbar-3').classList.remove('hidden');
            document.getElementById('forgot-step-1').classList.add('hidden');
            document.getElementById('forgot-step-2').classList.add('hidden');
            pwdGoToStep(1);
        }
        function pwdGoToStep(n) {
            [1,2,3].forEach(i => {
                document.getElementById('pwd-step-' + i).classList.toggle('hidden', i !== n);
                document.getElementById('pbar-' + i).classList.toggle('bg-primary', i <= n);
                document.getElementById('pbar-' + i).classList.toggle('bg-surface-container', i > n);
            });
            const labels = ['Step 1 of 3 · Verify Identity', 'Step 2 of 3 · Enter OTP Code', 'Step 3 of 3 · Set New Password'];
            document.getElementById('pwd-step-label').textContent = labels[n - 1];
            if (n === 1) { document.getElementById('oldPwd').value = ''; }
            if (n === 2) { document.querySelectorAll('.otp-digit').forEach(d => d.value = ''); setTimeout(()=>document.querySelector('.otp-digit').focus(), 100); }
            if (n === 3) { document.getElementById('newPwd').value = ''; document.getElementById('confirmPwd').value = ''; document.getElementById('sfill').style.width='0%'; document.getElementById('stext').textContent=''; }
        }
        /* OTP box: auto-advance & backspace */
        document.addEventListener('DOMContentLoaded', () => {
            // Normal OTP boxes
            document.querySelectorAll('.otp-digit').forEach((box, idx, all) => {
                box.addEventListener('input', () => {
                    box.value = box.value.replace(/\D/g, '').slice(-1);
                    if (box.value && idx < all.length - 1) all[idx + 1].focus();
                });
                box.addEventListener('keydown', e => {
                    if (e.key === 'Backspace' && !box.value && idx > 0) all[idx - 1].focus();
                });
                box.addEventListener('paste', e => {
                    const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                    all.forEach((b, i) => b.value = text[i] || '');
                    e.preventDefault();
                });
            });

            // Forgot OTP boxes
            document.querySelectorAll('.forgot-otp-digit').forEach((box, idx, all) => {
                box.addEventListener('input', () => {
                    box.value = box.value.replace(/\D/g, '').slice(-1);
                    if (box.value && idx < all.length - 1) all[idx + 1].focus();
                });
                box.addEventListener('keydown', e => {
                    if (e.key === 'Backspace' && !box.value && idx > 0) all[idx - 1].focus();
                });
                box.addEventListener('paste', e => {
                    const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                    all.forEach((b, i) => b.value = text[i] || '');
                    e.preventDefault();
                });
            });
        });
        function getOtpValue() {
            return Array.from(document.querySelectorAll('.otp-digit')).map(b => b.value).join('');
        }
        function getForgotOtpValue() {
            return Array.from(document.querySelectorAll('.forgot-otp-digit')).map(b => b.value).join('');
        }
        function setBtnLoading(id, loading, label) {
            const btn = document.getElementById(id);
            if (!btn) return;
            btn.disabled = loading;
            btn.innerHTML = loading
                ? '<span class="animate-spin material-symbols-outlined text-lg">progress_activity</span> Sending...'
                : label;
        }
        async function pwdSendOtp() {
            const oldPwd = document.getElementById('oldPwd').value;
            if (!oldPwd) { showToast('Please enter your current password'); return; }
            setBtnLoading('pwd-send-btn', true);
            const fd = new FormData();
            fd.append('action', 'send_otp');
            fd.append('old_password', oldPwd);
            try {
                const res  = await fetch('<?= BASE_URL ?>actions/author_password_otp.php', { method: 'POST', body: fd });
                const json = await res.json();
                if (json.success) {
                    document.getElementById('pwd-otp-email').textContent = json.email;
                    pwdGoToStep(2);
                } else {
                    showToast(json.error || 'Could not send OTP');
                }
            } catch(e) { showToast('Network error. Please try again.'); }
            finally { setBtnLoading('pwd-send-btn', false, '<span class="material-symbols-outlined text-lg">send</span> Send Verification Code'); }
        }
        async function pwdVerifyOtp() {
            const otp = getOtpValue();
            if (otp.length < 6) { showToast('Please enter the full 6-digit code'); return; }
            setBtnLoading('pwd-verify-btn', true);
            const fd = new FormData();
            fd.append('action', 'verify_otp');
            fd.append('otp', otp);
            try {
                const res  = await fetch('<?= BASE_URL ?>actions/author_password_otp.php', { method: 'POST', body: fd });
                const json = await res.json();
                if (json.success) {
                    pwdGoToStep(3);
                } else {
                    document.querySelectorAll('.otp-digit').forEach(b => b.classList.add('border-red-400'));
                    setTimeout(() => document.querySelectorAll('.otp-digit').forEach(b => b.classList.remove('border-red-400')), 1200);
                    showToast(json.error || 'Invalid OTP');
                }
            } catch(e) { showToast('Network error. Please try again.'); }
            finally { setBtnLoading('pwd-verify-btn', false, '<span class="material-symbols-outlined text-lg">verified</span> Verify Code'); }
        }
        async function updatePassword() {
            const newPwd     = document.getElementById('newPwd').value;
            const confirmPwd = document.getElementById('confirmPwd').value;
            if (!newPwd || !confirmPwd) { showToast('Please fill in both password fields'); return; }
            if (newPwd.length < 8) { showToast('Password must be at least 8 characters'); return; }
            if (newPwd !== confirmPwd) { showToast('Passwords do not match'); return; }
            setBtnLoading('pwd-update-btn', true);
            const fd = new FormData();
            fd.append('action', 'change_password');
            fd.append('new_password', newPwd);
            fd.append('confirm_password', confirmPwd);
            try {
                const res  = await fetch('<?= BASE_URL ?>actions/author_password_otp.php', { method: 'POST', body: fd });
                const json = await res.json();
                if (json.success) {
                    closePwdModal();
                    showToast('✅ Password updated successfully!');
                } else {
                    showToast(json.error || 'Password update failed');
                }
            } catch(e) { showToast('Network error. Please try again.'); }
            finally { setBtnLoading('pwd-update-btn', false, '<span class="material-symbols-outlined text-lg">check_circle</span> Update Password'); }
        }
        async function forgotSendOtp() {
            const email = document.getElementById('forgotEmail').value;
            if (!email) { showToast('Email address is missing.'); return; }
            setBtnLoading('forgot-send-btn', true);
            const fd = new FormData();
            fd.append('action', 'send_reset_otp');
            fd.append('email', email);
            try {
                const res  = await fetch('<?= BASE_URL ?>actions/author_forgot_password.php', { method: 'POST', body: fd });
                const json = await res.json();
                if (json.success) {
                    document.getElementById('forgot-otp-email').textContent = json.email;
                    document.getElementById('forgot-step-1').classList.add('hidden');
                    document.getElementById('forgot-step-2').classList.remove('hidden');
                    document.getElementById('pwd-step-label').textContent = 'Forgot Password · Step 2 of 2 · Reset Password';
                    
                    document.getElementById('pbar-1').className = "h-1 flex-1 rounded-full bg-primary transition-all";
                    document.getElementById('pbar-2').className = "h-1 flex-1 rounded-full bg-primary transition-all";
                    
                    document.querySelectorAll('.forgot-otp-digit').forEach(d => d.value = '');
                    document.getElementById('forgotNewPwd').value = '';
                    document.getElementById('forgotConfirmPwd').value = '';
                    document.getElementById('sfillForgot').style.width = '0%';
                    document.getElementById('stextForgot').textContent = '';
                    setTimeout(() => document.querySelector('.forgot-otp-digit').focus(), 100);
                } else {
                    showToast(json.error || 'Could not send reset OTP');
                }
            } catch(e) { showToast('Network error. Please try again.'); }
            finally { setBtnLoading('forgot-send-btn', false, '<span class="material-symbols-outlined text-lg">send</span> Send Verification Code'); }
        }
        async function forgotVerifyAndReset() {
            const otp = getForgotOtpValue();
            const newPwd = document.getElementById('forgotNewPwd').value;
            const confirmPwd = document.getElementById('forgotConfirmPwd').value;
            
            if (otp.length < 6) { showToast('Please enter the full 6-digit code'); return; }
            if (!newPwd || !confirmPwd) { showToast('Please enter and confirm your new password'); return; }
            if (newPwd.length < 8) { showToast('Password must be at least 8 characters'); return; }
            if (newPwd !== confirmPwd) { showToast('Passwords do not match'); return; }
            
            setBtnLoading('forgot-reset-btn', true);
            
            const fd1 = new FormData();
            fd1.append('action', 'verify_reset_otp');
            fd1.append('otp', otp);
            
            try {
                const res1 = await fetch('<?= BASE_URL ?>actions/author_forgot_password.php', { method: 'POST', body: fd1 });
                const json1 = await res1.json();
                if (!json1.success) {
                    document.querySelectorAll('.forgot-otp-digit').forEach(b => b.classList.add('border-red-400'));
                    setTimeout(() => document.querySelectorAll('.forgot-otp-digit').forEach(b => b.classList.remove('border-red-400')), 1200);
                    showToast(json1.error || 'Invalid verification code');
                    setBtnLoading('forgot-reset-btn', false, '<span class="material-symbols-outlined text-lg">verified</span> Verify & Reset Password');
                    return;
                }
                
                const fd2 = new FormData();
                fd2.append('action', 'reset_password');
                fd2.append('new_password', newPwd);
                fd2.append('confirm_password', confirmPwd);
                
                const res2 = await fetch('<?= BASE_URL ?>actions/author_forgot_password.php', { method: 'POST', body: fd2 });
                const json2 = await res2.json();
                if (json2.success) {
                    closePwdModal();
                    switchToNormalFlow();
                    showToast('✅ Password reset successfully!');
                } else {
                    showToast(json2.error || 'Failed to reset password');
                }
            } catch (e) {
                showToast('Network error. Please try again.');
            } finally {
                setBtnLoading('forgot-reset-btn', false, '<span class="material-symbols-outlined text-lg">verified</span> Verify & Reset Password');
            }
        }
        function checkStrength(pwd) {
            const fill = document.getElementById('sfill');
            const txt  = document.getElementById('stext');
            if (!pwd) { fill.style.width = '0%'; txt.textContent = ''; return; }
            let score = 0;
            if (pwd.length >= 8) score++;
            if (/[A-Z]/.test(pwd)) score++;
            if (/[0-9]/.test(pwd)) score++;
            if (/[^A-Za-z0-9]/.test(pwd)) score++;
            const pct = (score / 4) * 100;
            fill.style.width = pct + '%';
            if (score <= 1)      { fill.className = 'h-full rounded-full transition-all duration-300 bg-error'; txt.textContent = 'Weak'; txt.className = 'text-xs text-error mt-1'; }
            else if (score <= 3) { fill.className = 'h-full rounded-full transition-all duration-300 bg-amber-500'; txt.textContent = 'Medium'; txt.className = 'text-xs text-amber-500 mt-1'; }
            else                 { fill.className = 'h-full rounded-full transition-all duration-300 bg-green-500'; txt.textContent = 'Strong'; txt.className = 'text-xs text-green-500 mt-1'; }
        }
        function checkStrengthForgot(pwd) {
            const fill = document.getElementById('sfillForgot');
            const txt  = document.getElementById('stextForgot');
            if (!pwd) { fill.style.width = '0%'; txt.textContent = ''; return; }
            let score = 0;
            if (pwd.length >= 8) score++;
            if (/[A-Z]/.test(pwd)) score++;
            if (/[0-9]/.test(pwd)) score++;
            if (/[^A-Za-z0-9]/.test(pwd)) score++;
            const pct = (score / 4) * 100;
            fill.style.width = pct + '%';
            if (score <= 1)      { fill.className = 'h-full rounded-full transition-all duration-300 bg-error'; txt.textContent = 'Weak'; txt.className = 'text-xs text-error mt-1'; }
            else if (score <= 3) { fill.className = 'h-full rounded-full transition-all duration-300 bg-amber-500'; txt.textContent = 'Medium'; txt.className = 'text-xs text-amber-500 mt-1'; }
            else                 { fill.className = 'h-full rounded-full transition-all duration-300 bg-green-500'; txt.textContent = 'Strong'; txt.className = 'text-xs text-green-500 mt-1'; }
        }
        function showToast(msg) {
            const toast = document.getElementById('toast');
            if (!toast) return;
            toast.textContent = msg;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3500);
        }
    </script>
</body>

</html>
<?php
include "../include/session.php";
requireUser();
include "../config.php";
include "../include/db.php";
include "../include/data_fetch.php";
include_once "../include/functions.php";

global $conn;

$user_id = (int)$_SESSION['user_id'];

// ---- AJAX ACTIONS FOR NOTIFICATIONS / PASSWORD ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    if ($action === 'mark_notifications_read') {
        $upd = mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE user_id = $user_id");
        echo json_encode(['success' => (bool)$upd]);
        exit;
    }
    if ($action === 'delete_notification') {
        $notif_id = (int)$_POST['id'];
        $del = mysqli_query($conn, "DELETE FROM notifications WHERE id = $notif_id AND user_id = $user_id");
        echo json_encode(['success' => (bool)$del]);
        exit;
    }
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
    if ($action === 'send_reset_otp') {
        $email = $_POST['email'] ?? '';
        
        $u_query = "SELECT id, name, email FROM users WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'";
        $u_res = mysqli_query($conn, $u_query);
        $u_row = mysqli_fetch_assoc($u_res);
        
        if (!$u_row) {
            echo json_encode(['success' => false, 'error' => 'No account found with this email address']);
            exit;
        }
        
        $otp = str_pad((string)mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_otp_time'] = time();
        $_SESSION['reset_otp_email'] = $email;
        
        include_once "../include/send_mail.php";
        
        $mail_sent = false;
        try {
            send_password_reset_mail($email, $u_row['name'], $otp);
            $mail_sent = (bool)$mail->send();
        } catch (Exception $e) {
            error_log("PHPMailer error: " . $mail->ErrorInfo);
        }
        
        echo json_encode([
            'success' => true,
            'mail_sent' => $mail_sent,
            'debug_otp' => $otp
        ]);
        exit;
    }
    if ($action === 'verify_reset_otp') {
        $otp = $_POST['otp'] ?? '';
        $new_pwd = $_POST['new_password'] ?? '';
        $email = $_SESSION['reset_otp_email'] ?? '';
        
        if (empty($email) || !isset($_SESSION['reset_otp']) || !isset($_SESSION['reset_otp_time'])) {
            echo json_encode(['success' => false, 'error' => 'Session expired. Please request a new OTP.']);
            exit;
        }
        
        if (time() - $_SESSION['reset_otp_time'] > 600) {
            echo json_encode(['success' => false, 'error' => 'OTP has expired. Please request a new one.']);
            exit;
        }
        
        if ($otp !== $_SESSION['reset_otp']) {
            echo json_encode(['success' => false, 'error' => 'Incorrect verification code']);
            exit;
        }
        
        if (strlen($new_pwd) < 8) {
            echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters']);
            exit;
        }
        
        $hashed = password_hash($new_pwd, PASSWORD_DEFAULT);
        $upd = mysqli_query($conn, "UPDATE users SET password = '$hashed' WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'");
        
        if ($upd) {
            unset($_SESSION['reset_otp']);
            unset($_SESSION['reset_otp_time']);
            unset($_SESSION['reset_otp_email']);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database update failed']);
        }
        exit;
    }
}

/* ── USER ─────────────────────────────────────────── */
$user_query  = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$data        = mysqli_fetch_assoc($user_result);

$user_name    = htmlspecialchars($data['name'] ?? 'User');
$user_email   = htmlspecialchars($data['email'] ?? '');
$user_role    = ucfirst(htmlspecialchars($data['role'] ?? 'reader'));
$user_image   = !empty($data['profile_image']) ? '../' . $data['profile_image'] : null;
$user_initial = strtoupper(substr($data['name'] ?? 'U', 0, 2));
$joined_date  = date('M Y', strtotime($data['created_at'] ?? 'now'));
$first_name   = explode(' ', trim($data['name'] ?? 'User'))[0];

/* ── DASHBOARD STATS ──────────────────────────────── */
// Saved posts count
$saved_cnt_query  = "SELECT COUNT(*) as total FROM saved_posts WHERE user_id = $user_id";
$saved_cnt_result = mysqli_query($conn, $saved_cnt_query);
$saved_cnt_row    = mysqli_fetch_assoc($saved_cnt_result);
$saved_count      = (int)$saved_cnt_row['total'];

// Comments count (by this user)
$comment_cnt_query  = "SELECT COUNT(*) as total FROM comments WHERE user_id = $user_id";
$comment_cnt_result = mysqli_query($conn, $comment_cnt_query);
$comment_cnt_row    = mysqli_fetch_assoc($comment_cnt_result);
$comment_count      = (int)$comment_cnt_row['total'];

// Reactions count (by this user)
$reaction_cnt_query  = "SELECT COUNT(*) as total FROM reactions WHERE user_id = $user_id";
$reaction_cnt_result = mysqli_query($conn, $reaction_cnt_query);
$reaction_cnt_row    = mysqli_fetch_assoc($reaction_cnt_result);
$reaction_count      = (int)$reaction_cnt_row['total'];

/* ── RECENT ACTIVITY (saves + comments + reactions) ─ */
$activity_query  = "
    (SELECT 'save' as type, sp.created_at, p.title, p.slug
     FROM saved_posts sp JOIN posts p ON sp.post_id = p.id
     WHERE sp.user_id = $user_id)
    UNION ALL
    (SELECT 'comment' as type, c.created_at, p.title, p.slug
     FROM comments c JOIN posts p ON c.post_id = p.id
     WHERE c.user_id = $user_id)
    UNION ALL
    (SELECT 'reaction' as type, r.created_at, p.title, p.slug
     FROM reactions r JOIN posts p ON r.post_id = p.id
     WHERE r.user_id = $user_id)
    ORDER BY created_at DESC LIMIT 5";
$activity_result = mysqli_query($conn, $activity_query);
$activities      = [];
if ($activity_result) {
    while ($row = mysqli_fetch_assoc($activity_result)) {
        $activities[] = $row;
    }
}

function timeAgoHome($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)    return 'just now';
    if ($diff < 3600)  return (int)($diff/60)  . ' min ago';
    if ($diff < 86400) return (int)($diff/3600) . ' hrs ago';
    return date('M j', strtotime($datetime));
}

/* ── CATEGORIES ───────────────────────────────────── */
$cat_query  = "SELECT * FROM categories ORDER BY name ASC";
$cat_result = mysqli_query($conn, $cat_query);
$categories = [];
while ($cat = mysqli_fetch_assoc($cat_result)) {
    $categories[] = $cat;
}

/* ── SAVED POSTS (for PHP initial check) ──────────── */
$saved_slugs_query  = "SELECT p.slug FROM saved_posts sp JOIN posts p ON sp.post_id = p.id WHERE sp.user_id = $user_id";
$saved_slugs_result = mysqli_query($conn, $saved_slugs_query);
$saved_slugs        = [];
while ($sr = mysqli_fetch_assoc($saved_slugs_result)) {
    $saved_slugs[] = $sr['slug'];
}

/* ── NOTIFICATIONS ────────────────────────────────── */
$notif_query = "SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 20";
$notif_res = mysqli_query($conn, $notif_query);
$notifications = [];
$unread_count = 0;
if ($notif_res) {
    while ($n_row = mysqli_fetch_assoc($notif_res)) {
        $notifications[] = [
            'id' => (int)$n_row['id'],
            'title' => htmlspecialchars($n_row['title']),
            'message' => htmlspecialchars($n_row['message']),
            'type' => htmlspecialchars($n_row['type']),
            'is_read' => (int)$n_row['is_read'],
            'date' => timeAgoHome($n_row['created_at'])
        ];
        if (!(int)$n_row['is_read']) {
            $unread_count++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BlogFusion — User Panel</title>
    <meta name="description" content="Your personalised BlogFusion dashboard. Browse blogs, manage saves and profile." />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        "surface-bright": "#fef7ff", "surface-container": "#f3ebfa", "background": "#fef7ff",
                        "surface-container-highest": "#e8dfee", "secondary": "#4b41e1", "primary": "#630ed4",
                        "on-background": "#1d1a24", "on-surface-variant": "#4a4455", "tertiary-container": "#bf2076",
                        "surface": "#fef7ff", "on-surface": "#1d1a24", "primary-container": "#7c3aed",
                        "surface-container-high": "#ede5f4", "tertiary": "#9b005c", "surface-container-lowest": "#ffffff",
                        "primary-fixed": "#eaddff", "on-primary-container": "#ede0ff", "outline": "#7b7487",
                        "outline-variant": "#ccc3d8", "secondary-container": "#645efb", "tertiary-fixed": "#ffd9e4",
                        "on-primary-fixed-variant": "#5a00c6", "surface-container-low": "#f9f1ff",
                        "surface-dim": "#dfd7e6", "on-primary": "#ffffff", "error": "#ba1a1a",
                        "error-container": "#ffdad6", "on-error-container": "#93000a"
                    },
                    borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", "2xl": "1rem", "3xl": "1.5rem", full: "9999px" },
                    fontFamily: { headline: ["Public Sans"], display: ["Public Sans"], body: ["Public Sans"], label: ["Public Sans"] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Public Sans', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .ms-filled { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .page { display: none; animation: fadeIn .3s ease; }
        .page.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
        .nav-item { transition: all .18s; }
        .stat-gradient { background: linear-gradient(135deg, #630ed4, #7c3aed); }
        .blog-view { display: none; animation: fadeIn .4s ease; }
        .blog-view.active { display: block; }
        .reaction-btn.active { background: rgba(99,14,212,.12) !important; transform: scale(1.1); }
        .reaction-btn { transition: all .2s; }
        .live-dot { width: 6px; height: 6px; background: #630ed4; border-radius: 50%; display: inline-block; animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
        .filter-tab.active { color: #630ed4; font-weight: 700; border-bottom: 2px solid #630ed4; }
        .filter-tab { transition: all .18s; border-bottom: 2px solid transparent; }
        .blog-card { transition: all .22s; }
        .blog-card:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(99,14,212,.1); }
        .sidebar-pill.active { background: #eaddff; color: #5a00c6; font-weight: 700; }
        input[type=text],input[type=email],input[type=password],input[type=file],textarea { outline: none; transition: border-color .2s; }
        input:focus,textarea:focus { border-color: #630ed4 !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #ccc3d8; border-radius: 4px; }
        .comment-like.liked { color: #630ed4 !important; }
        .tag-pill { transition: all .15s; cursor: pointer; }
        .tag-pill:hover { background: #eaddff; color: #5a00c6; }
        .tag-pill.active-tag { background: #630ed4; color: #fff !important; }
        .cat-card { transition: all .22s; }
        .cat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(99,14,212,.13); }
        .comment-edit-area { display: none; }
        .comment-edit-area.open { display: block; }
        html.dark .tag-pill { background: #2a2534; color: #ccc3d8; }
        html.dark .tag-pill:hover { background: #3d1a7a; color: #d2bbff; }
        html.dark .tag-pill.active-tag { background: #630ed4; color: #fff !important; }
        .reply-comment { margin-left: 44px; border-left: 2px solid rgba(99,14,212,.2); padding-left: 14px; }
        /* ─── DARK MODE ─── */
        html.dark body { background: #1d1a24 !important; color: #ede5f4; }
        html.dark { background: #1d1a24; }
        html.dark .bg-surface-bright { background: #1d1a24 !important; }
        html.dark .bg-surface { background: #1d1a24 !important; }
        html.dark .bg-surface-container-low { background: #2a2534 !important; }
        html.dark .bg-surface-container-lowest { background: #1e1b28 !important; }
        html.dark .bg-surface-container-high { background: #332f3d !important; }
        html.dark .bg-surface-container-highest { background: #3d3849 !important; }
        html.dark .bg-surface-container { background: #241f30 !important; }
        html.dark .text-on-surface { color: #ede5f4 !important; }
        html.dark .text-on-surface-variant { color: #ccc3d8 !important; }
        html.dark .bg-white { background: #2a2534 !important; }
        html.dark #topbar-header { background: rgba(29,26,36,0.92) !important; border-color: rgba(100,90,120,0.25) !important; }
        html.dark .border-outline-variant\/10 { border-color: rgba(100,90,120,0.25) !important; }
        html.dark .border-outline-variant\/20 { border-color: rgba(100,90,120,0.3) !important; }
        html.dark input[type=text],html.dark input[type=email],html.dark input[type=password],html.dark textarea,html.dark select { background: rgba(50,45,62,0.8) !important; color: #ede5f4 !important; }
        html.dark .bg-primary-fixed { background: #3d1a7a !important; }
        html.dark .text-on-primary-fixed-variant { color: #d2bbff !important; }
        html.dark .sidebar-pill.active { background: #3d1a7a !important; color: #d2bbff !important; }
        html.dark .hover\:bg-surface-container-high:hover { background: #332f3d !important; }
        html.dark .hover\:bg-surface-container:hover { background: #241f30 !important; }
        html.dark .hover\:bg-surface-container-low:hover { background: #2a2534 !important; }
        html.dark #profileMenu { background: #2a2534 !important; }
        html.dark #topbarResults { background: #2a2534 !important; }
        #sidebarOverlay { backdrop-filter: blur(2px); }
        @media (max-width: 1023px) { .reaction-btn { width: 2.75rem; height: 2.75rem; } }
        body.single-blog-active #sidebar { display: none !important; }
        body.single-blog-active #topbar-header { display: none !important; }
        body.single-blog-active .lg\:ml-64 { margin-left: 0 !important; }

        /* ════════ TAG STRIP ════════ */
        .tag-strip-wrapper {
            background: var(--ts-bg, #f9f1ff);
            border: 1px solid rgba(204,195,216,0.35);
            border-radius: 18px;
            padding: 12px 16px 14px;
            min-width: 0;
            width: 100%;
            max-width: 100%;
            overflow: hidden;
        }
        html.dark .tag-strip-wrapper {
            background: #241f30;
            border-color: rgba(100,90,120,0.25);
        }
        .tag-strip-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .tag-strip-label {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #4a4455;
        }
        html.dark .tag-strip-label { color: #ccc3d8; }
        .tag-strip-icon {
            font-size: 15px !important;
            color: #630ed4;
        }
        .tag-clear-btn {
            display: flex;
            align-items: center;
            gap: 3px;
            font-size: 11px;
            font-weight: 800;
            color: #630ed4;
            background: rgba(99,14,212,.08);
            border: none;
            border-radius: 999px;
            padding: 3px 10px;
            cursor: pointer;
            transition: background .18s;
        }
        .tag-clear-btn:hover { background: rgba(99,14,212,.16); }
        .tag-strip-track-wrap {
            position: relative;
            display: flex;
            align-items: center;
            gap: 4px;
            min-width: 0;
            width: 100%;
            max-width: 100%;
        }
        .tag-strip-track {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 4px 2px;
            scrollbar-width: none;
            -ms-overflow-style: none;
            -webkit-overflow-scrolling: touch;
            flex: 1;
            min-width: 0;
        }
        .tag-strip-track::-webkit-scrollbar { display: none; }
        .tag-strip-fade-left,
        .tag-strip-fade-right {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 40px;
            pointer-events: none;
            z-index: 2;
        }
        .tag-strip-fade-left  { left: 28px;  background: linear-gradient(to right,  #f9f1ff, transparent); }
        .tag-strip-fade-right { right: 28px; background: linear-gradient(to left, #f9f1ff, transparent); }
        html.dark .tag-strip-fade-left  { background: linear-gradient(to right,  #241f30, transparent); }
        html.dark .tag-strip-fade-right { background: linear-gradient(to left, #241f30, transparent); }
        .tag-arrow {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 1.5px solid rgba(99,14,212,.2);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: all .18s;
            color: #630ed4;
            z-index: 3;
        }
        .tag-arrow:hover {
            background: #eaddff;
            border-color: #630ed4;
            box-shadow: 0 2px 12px rgba(99,14,212,.18);
        }
        .tag-arrow.hidden-arrow { opacity: 0; pointer-events: none; }
        html.dark .tag-arrow { background: #2a2534; border-color: rgba(210,187,255,.2); color: #d2bbff; }
        html.dark .tag-arrow:hover { background: #3d1a7a; border-color: #d2bbff; }

        @media (max-width: 767px) {
            .tag-arrow {
                display: none !important;
            }
            .tag-strip-fade-left {
                left: 0 !important;
                background: linear-gradient(to right, #f9f1ff, transparent) !important;
            }
            .tag-strip-fade-right {
                right: 0 !important;
                background: linear-gradient(to left, #f9f1ff, transparent) !important;
            }
            html.dark .tag-strip-fade-left {
                background: linear-gradient(to right, #241f30, transparent) !important;
            }
            html.dark .tag-strip-fade-right {
                background: linear-gradient(to left, #241f30, transparent) !important;
            }
            .tag-strip-track-wrap {
                gap: 0 !important;
            }
            .tag-strip-wrapper {
                padding: 10px 12px 12px;
            }
        }
        /* Tag chip */
        .tag-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            padding: 5px 12px 5px 8px;
            border-radius: 999px;
            border: 1.5px solid rgba(99,14,212,.15);
            background: #fff;
            cursor: pointer;
            transition: all .18s cubic-bezier(.4,0,.2,1);
            flex-shrink: 0;
            font-size: 12px;
            font-weight: 700;
            color: #4a4455;
        }
        html.dark .tag-chip { background: #2a2534; border-color: rgba(210,187,255,.15); color: #ccc3d8; }
        .tag-chip:hover {
            border-color: #630ed4;
            color: #630ed4;
            background: #f0e8ff;
            transform: translateY(-1px);
            box-shadow: 0 3px 12px rgba(99,14,212,.12);
        }
        html.dark .tag-chip:hover { background: #3d1a7a; color: #d2bbff; }
        .tag-chip-active {
            background: linear-gradient(135deg, #630ed4, #7c3aed) !important;
            border-color: transparent !important;
            color: #fff !important;
            box-shadow: 0 4px 18px rgba(99,14,212,.35);
            transform: translateY(-1px);
        }
        .tag-chip-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(99,14,212,.35);
            transition: background .18s;
            flex-shrink: 0;
        }
        .tag-chip-active .tag-chip-dot { background: rgba(255,255,255,.6); }
        .tag-chip:hover .tag-chip-dot  { background: #630ed4; }
        .tag-chip-active:hover .tag-chip-dot { background: rgba(255,255,255,.8); }
        .tag-chip-label { font-size: 12px; }
        .tag-chip-count {
            font-size: 10px;
            font-weight: 900;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            background: rgba(99,14,212,.1);
            color: #630ed4;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            transition: background .18s, color .18s;
        }
        .tag-chip-active .tag-chip-count {
            background: rgba(255,255,255,.25);
            color: #fff;
        }
        .tag-chip:hover:not(.tag-chip-active) .tag-chip-count {
            background: rgba(99,14,212,.18);
            color: #630ed4;
        }
        html.dark .tag-chip-count { background: rgba(210,187,255,.12); color: #d2bbff; }
    </style>
</head>

<body class="bg-surface text-on-surface overflow-x-hidden">

    <?php inject_project_toast(); ?>

    <div id="sidebarOverlay" onclick="closeSidebarMobile()" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>

    <div class="flex min-h-screen relative overflow-hidden">

        <!-- ════════════════ SIDEBAR ════════════════ -->
        <aside id="sidebar" class="w-64 bg-surface-container-low shrink-0 fixed top-0 left-0 h-screen flex flex-col z-40 transition-transform duration-300 -translate-x-full lg:translate-x-0">
            <div class="px-6 py-5 border-b border-outline-variant/20">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl stat-gradient flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-base ms-filled">edit_note</span>
                    </div>
                    <span class="text-lg font-black tracking-tight text-on-surface">Blog<span class="text-primary">Fusion</span></span>
                </div>
                <div class="text-[11px] text-on-surface-variant mt-1 font-medium">User Panel</div>
            </div>

            <!-- Sidebar user badge -->
            <div class="px-4 py-4 border-b border-outline-variant/10 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full shrink-0 overflow-hidden flex items-center justify-center text-white font-black text-sm <?= $user_image ? '' : 'stat-gradient' ?>">
                    <?php if ($user_image): ?>
                    <img src="<?= $user_image ?>" class="w-full h-full object-cover" onerror="this.style.display='none';this.parentElement.textContent='<?= $user_initial ?>';" />
                    <?php else: ?>
                    <?= $user_initial ?>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="font-bold text-sm text-on-surface"><?= $user_name ?></div>
                    <div class="text-[11px] px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed-variant font-bold inline-block mt-0.5"><?= $user_role ?></div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto no-scrollbar py-4 px-3 space-y-0.5">
                <p class="text-[10px] font-black tracking-widest uppercase text-on-surface-variant px-3 py-2">Main</p>
                <button onclick="showPage('dashboard',this);closeSidebarMobile()" class="nav-item sidebar-pill active w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-lg">grid_view</span> Dashboard
                </button>
                <button onclick="showPage('profile',this);closeSidebarMobile()" class="nav-item sidebar-pill w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-lg">person</span> Profile
                </button>
                <button onclick="showPage('blogs',this);closeSidebarMobile()" class="nav-item sidebar-pill w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-lg">article</span> Blogs
                </button>
                <button onclick="showPage('categories',this);renderCategories();closeSidebarMobile()" class="nav-item sidebar-pill w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-lg">category</span> Categories
                </button>
                <button onclick="showPage('saved',this);closeSidebarMobile()" class="nav-item sidebar-pill w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-lg">bookmark</span> Saved Posts
                </button>
                <p class="text-[10px] font-black tracking-widest uppercase text-on-surface-variant px-3 py-2 mt-2">Account</p>
                <button onclick="showPage('notifications',this);closeSidebarMobile();renderNotifications();" class="nav-item sidebar-pill w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high relative">
                    <span class="material-symbols-outlined text-lg">notifications</span> Notifications
                    <span id="notif-badge" class="<?= $unread_count > 0 ? '' : 'hidden' ?> absolute right-3 top-1/2 -translate-y-1/2 bg-error text-white text-[10px] font-black w-5 h-5 flex items-center justify-center rounded-full"><?= $unread_count ?></span>
                </button>
                <button onclick="showPage('settings',this);closeSidebarMobile()" class="nav-item sidebar-pill w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-lg">settings</span> Settings
                </button>
            </nav>

            <div class="px-3 py-4 border-t border-outline-variant/10">
                <button onclick="logout()" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-error hover:bg-error-container/30 transition-colors">
                    <span class="material-symbols-outlined text-lg">logout</span> Logout
                </button>
            </div>
        </aside>

        <div class="flex-1 min-w-0 lg:ml-64 flex flex-col min-h-screen">

            <!-- ════════════════ TOPBAR ════════════════ -->
            <header id="topbar-header" class="sticky top-0 z-20 bg-surface-container-lowest/90 backdrop-blur-xl border-b border-outline-variant/10 px-4 md:px-8 py-3 flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden w-9 h-9 flex items-center justify-center rounded-xl hover:bg-surface-container transition-colors shrink-0">
                    <span class="material-symbols-outlined text-on-surface-variant">menu</span>
                </button>
                <span class="lg:hidden font-black tracking-tight text-on-surface text-base">Blog<span class="text-primary">Fusion</span></span>

                <div class="relative flex-1 max-w-md hidden sm:block">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                    <input id="topbarSearch" type="text" placeholder="Search posts, topics..."
                        class="w-full pl-10 pr-4 py-2 bg-surface-container-highest/60 rounded-xl text-sm text-on-surface placeholder-on-surface-variant border border-transparent focus:border-primary/30"
                        oninput="handleTopbarSearch(this.value)"
                        onkeydown="if(event.key==='Enter')goSearchBlogs(this.value)" />
                    <div id="topbarResults" class="hidden absolute top-full mt-2 left-0 right-0 bg-white rounded-2xl shadow-2xl border border-outline-variant/20 z-50 overflow-hidden"></div>
                </div>

                <div class="ml-auto flex items-center gap-1.5 md:gap-2">
                    <button class="sm:hidden w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors" onclick="showToast('Use the blog search below')">
                        <span class="material-symbols-outlined text-on-surface-variant text-xl">search</span>
                    </button>
                    <button onclick="toggleDark()" id="darkToggleTopbar" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors" title="Toggle dark mode">
                        <span class="material-symbols-outlined text-on-surface-variant text-xl" id="darkIconTopbar">dark_mode</span>
                    </button>
                    <button onclick="showPage('notifications', document.querySelector('.sidebar-pill[onclick*=\'notifications\']')); renderNotifications()" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors relative" title="Notifications">
                        <span class="material-symbols-outlined text-on-surface-variant text-xl">notifications</span>
                        <span id="notif-dot-topbar" class="<?= $unread_count > 0 ? '' : 'hidden' ?> absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full"></span>
                    </button>
                    <div class="relative">
                        <button onclick="toggleProfileMenu()" class="flex items-center gap-1.5 pl-2 pr-3 py-1.5 rounded-full hover:bg-surface-container transition-colors">
                            <div class="w-7 h-7 rounded-full overflow-hidden shrink-0 <?= $user_image ? '' : 'stat-gradient flex items-center justify-center text-white text-xs font-black' ?>">
                                <?php if ($user_image): ?>
                                <img src="<?= $user_image ?>" class="w-full h-full object-cover" onerror="this.style.display='none'" />
                                <?php else: ?>
                                <?= $user_initial ?>
                                <?php endif; ?>
                            </div>
                            <span class="text-sm font-semibold text-on-surface hidden md:inline"><?= $first_name ?></span>
                            <span class="material-symbols-outlined text-on-surface-variant text-base">expand_more</span>
                        </button>
                        <div id="profileMenu" class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl shadow-2xl border border-outline-variant/20 p-2 z-50">
                            <button onclick="showPage('profile',null);closeProfileMenu()" class="w-full text-left px-3 py-2 rounded-xl hover:bg-surface-container text-sm flex items-center gap-2 text-on-surface transition-colors"><span class="material-symbols-outlined text-base">person</span> Profile</button>
                            <button onclick="showPage('settings',null);closeProfileMenu()" class="w-full text-left px-3 py-2 rounded-xl hover:bg-surface-container text-sm flex items-center gap-2 text-on-surface transition-colors"><span class="material-symbols-outlined text-base">settings</span> Settings</button>
                            <hr class="border-outline-variant/20 my-1" />
                            <button onclick="logout();closeProfileMenu()" class="w-full text-left px-3 py-2 rounded-xl hover:bg-error-container/20 text-sm text-error flex items-center gap-2 transition-colors"><span class="material-symbols-outlined text-base">logout</span> Logout</button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ════════════════ MAIN ════════════════ -->
            <main class="flex-1 min-w-0 w-full max-w-full px-4 py-5 md:px-8 md:py-8 bg-surface-bright">

                <!-- ══ DASHBOARD ══ -->
                <div class="page active min-w-0 w-full" id="page-dashboard">
                    <div class="mb-6 md:mb-8">
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Welcome back, <?= $first_name ?> 👋</h1>
                        <p class="text-on-surface-variant mt-1">Here's what's happening with your account.</p>
                    </div>

                    <!-- Stat cards -->
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 mb-6 md:mb-8">
                        <div class="bg-surface-container-lowest rounded-2xl p-4 md:p-5 relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-0.5 bg-primary"></div>
                            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl mb-2 md:mb-3 flex items-center justify-center" style="background:rgba(99,14,212,.1)">
                                <span class="material-symbols-outlined text-primary text-lg">bookmark</span>
                            </div>
                            <div class="text-2xl md:text-3xl font-black text-on-surface"><?= $saved_count ?></div>
                            <div class="text-xs text-on-surface-variant mt-1 font-medium">Saved Posts</div>
                        </div>
                        <div class="bg-surface-container-lowest rounded-2xl p-4 md:p-5 relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-0.5 bg-secondary"></div>
                            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl mb-2 md:mb-3 flex items-center justify-center" style="background:rgba(75,65,225,.1)">
                                <span class="material-symbols-outlined text-secondary text-lg">chat_bubble</span>
                            </div>
                            <div class="text-2xl md:text-3xl font-black text-on-surface"><?= $comment_count ?></div>
                            <div class="text-xs text-on-surface-variant mt-1 font-medium">Total Comments</div>
                        </div>
                        <div class="bg-surface-container-lowest rounded-2xl p-4 md:p-5 relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-0.5 bg-tertiary"></div>
                            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl mb-2 md:mb-3 flex items-center justify-center" style="background:rgba(155,0,92,.1)">
                                <span class="material-symbols-outlined text-tertiary text-lg">favorite</span>
                            </div>
                            <div class="text-2xl md:text-3xl font-black text-on-surface"><?= $reaction_count ?></div>
                            <div class="text-xs text-on-surface-variant mt-1 font-medium">Total Reactions</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
                        <!-- Recent activity -->
                        <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl p-5 md:p-6">
                            <h3 class="font-black text-base mb-4 md:mb-5 text-on-surface">Recent Activity</h3>
                            <div class="space-y-3 md:space-y-4">
                                <?php if (empty($activities)): ?>
                                <p class="text-sm text-on-surface-variant text-center py-6">No activity yet. Start reading and saving posts!</p>
                                <?php else: ?>
                                <?php foreach ($activities as $act):
                                    $icon  = $act['type'] === 'save' ? 'bookmark_added' : ($act['type'] === 'comment' ? 'chat' : 'favorite');
                                    $color = $act['type'] === 'save' ? 'rgba(99,14,212,.1)' : ($act['type'] === 'comment' ? 'rgba(75,65,225,.1)' : 'rgba(155,0,92,.1)');
                                    $text_color = $act['type'] === 'save' ? 'text-primary' : ($act['type'] === 'comment' ? 'text-secondary' : 'text-tertiary');
                                    $verb  = $act['type'] === 'save' ? 'You saved' : ($act['type'] === 'comment' ? 'You commented on' : 'You reacted to');
                                ?>
                                <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-surface-container-low transition-colors">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0" style="background:<?= $color ?>">
                                        <span class="material-symbols-outlined <?= $text_color ?> text-base"><?= $icon ?></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-semibold text-on-surface"><?= $verb ?> <span class="text-primary">"<?= htmlspecialchars(mb_strimwidth($act['title'], 0, 40, '…')) ?>"</span></div>
                                        <div class="text-xs text-on-surface-variant mt-0.5"><?= timeAgoHome($act['created_at']) ?></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="stat-gradient rounded-2xl p-5 md:p-6 text-white">
                                <div class="font-black text-lg mb-1">Explore Blogs</div>
                                <p class="text-primary-fixed/80 text-sm mb-4">Discover the latest posts from top authors.</p>
                                <button onclick="showPage('blogs',null)" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-sm font-bold transition-colors border border-white/20">Browse All Posts →</button>
                            </div>
                            <div class="bg-surface-container-lowest rounded-2xl p-5">
                                <h3 class="font-black text-xs mb-3 text-on-surface-variant uppercase tracking-widest">Quick Links</h3>
                                <div class="space-y-1">
                                    <button onclick="showPage('saved',null)" class="w-full text-left px-3 py-2 rounded-xl hover:bg-surface-container text-sm flex items-center gap-2 text-on-surface transition-colors"><span class="material-symbols-outlined text-base text-primary">bookmark</span> My Saved Posts</button>
                                    <button onclick="showPage('profile',null)" class="w-full text-left px-3 py-2 rounded-xl hover:bg-surface-container text-sm flex items-center gap-2 text-on-surface transition-colors"><span class="material-symbols-outlined text-base text-secondary">person</span> My Profile</button>
                                    <button onclick="showPage('categories',null);renderCategories()" class="w-full text-left px-3 py-2 rounded-xl hover:bg-surface-container text-sm flex items-center gap-2 text-on-surface transition-colors"><span class="material-symbols-outlined text-base text-tertiary">category</span> Categories</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══ PROFILE ══ -->
                <div class="page" id="page-profile">
                    <div class="mb-6 md:mb-8">
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">My Profile</h1>
                        <p class="text-on-surface-variant mt-1">Your personal information at a glance.</p>
                    </div>
                    <div class="bg-surface-container-lowest rounded-3xl p-6 md:p-8 mb-6 flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-8">
                        <div class="relative shrink-0">
                            <div class="w-20 h-20 md:w-24 md:h-24 rounded-full overflow-hidden ring-4 ring-surface-container-low <?= $user_image ? '' : 'stat-gradient flex items-center justify-center text-white text-2xl md:text-3xl font-black' ?>">
                                <?php if ($user_image): ?>
                                <img src="<?= $user_image ?>" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<?= $user_initial ?>'" />
                                <?php else: ?>
                                <?= $user_initial ?>
                                <?php endif; ?>
                            </div>
                            <div class="absolute bottom-0 right-0 w-6 h-6 md:w-7 md:h-7 bg-green-500 rounded-full border-2 border-white"></div>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h2 class="text-xl md:text-2xl font-black tracking-tight text-on-surface"><?= $user_name ?></h2>
                            <p class="text-on-surface-variant mt-1"><?= $user_email ?></p>
                            <div class="flex items-center gap-2 mt-3 justify-center md:justify-start flex-wrap">
                                <span class="px-3 py-1 rounded-full bg-primary-fixed text-on-primary-fixed-variant text-xs font-bold"><?= $user_role ?></span>
                                <span class="px-3 py-1 rounded-full bg-surface-container text-on-surface-variant text-xs font-medium">Joined <?= $joined_date ?></span>
                                <span class="px-3 py-1 rounded-full text-xs font-medium" style="background:rgba(34,197,94,.1);color:#16a34a">● Active</span>
                            </div>
                            <div class="flex flex-wrap gap-3 mt-5 md:mt-6 justify-center md:justify-start">
                                <button onclick="showPage('edit-profile',null)" class="flex items-center gap-2 px-4 md:px-5 py-2.5 stat-gradient text-white rounded-xl text-sm font-bold hover:opacity-90 transition-opacity active:scale-95">
                                    <span class="material-symbols-outlined text-base">edit</span> Edit Profile
                                </button>
                                <button onclick="showPage('change-password',null)" class="flex items-center gap-2 px-4 md:px-5 py-2.5 bg-surface-container text-on-surface rounded-xl text-sm font-bold hover:bg-surface-container-high transition-colors active:scale-95">
                                    <span class="material-symbols-outlined text-base">lock</span> Change Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══ EDIT PROFILE ══ -->
                <div class="page" id="page-edit-profile">
                    <div class="mb-6 md:mb-8 flex items-center gap-3">
                        <button onclick="showPage('profile',null)" class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-surface-container transition-colors shrink-0">
                            <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
                        </button>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Edit Profile</h1>
                            <p class="text-on-surface-variant mt-0.5">Update your personal information.</p>
                        </div>
                    </div>
                    <div class="max-w-2xl bg-surface-container-lowest rounded-3xl p-6 md:p-8">
                        <form method="POST" action="../actions/author.php" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update_profile" />
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Full Name</label>
                                    <input type="text" name="name" value="<?= $user_name ?>" class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Email</label>
                                    <input type="email" name="email" value="<?= $user_email ?>" class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface" />
                                </div>
                                <div class="space-y-1.5 md:col-span-2">
                                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Profile Picture</label>
                                    <input type="file" name="profile_image" accept="image/*" class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface" />
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-3 mt-6 md:mt-8">
                                <button type="submit" class="flex items-center gap-2 px-6 py-2.5 stat-gradient text-white rounded-xl text-sm font-bold hover:opacity-90 transition-opacity active:scale-95">
                                    <span class="material-symbols-outlined text-base">check</span> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ══ CHANGE PASSWORD ══ -->
                <div class="page" id="page-change-password">
                    <div class="mb-6 md:mb-8 flex items-center gap-3">
                        <button onclick="showPage('profile',null)" class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-surface-container transition-colors shrink-0">
                            <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
                        </button>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Change Password</h1>
                        </div>
                    </div>
                    <div class="max-w-md bg-surface-container-lowest rounded-3xl p-6 md:p-8 space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Current Password</label>
                            <input type="password" id="oldPwd" placeholder="••••••••" class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">New Password</label>
                            <input type="password" id="newPwd" placeholder="••••••••" oninput="checkStrength(this.value)" class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface" />
                            <div class="h-1.5 rounded-full bg-surface-container-high overflow-hidden mt-2">
                                <div id="sfill" class="h-full rounded-full transition-all duration-300" style="width:0%"></div>
                            </div>
                            <p id="stext" class="text-xs text-on-surface-variant"></p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Confirm New Password</label>
                            <input type="password" id="confirmPwd" placeholder="••••••••" class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface" />
                        </div>
                        <button onclick="updatePassword()" class="w-full py-3 stat-gradient text-white rounded-xl font-bold text-sm hover:opacity-90 transition-opacity active:scale-95">Update Password</button>
                        <p class="text-center text-xs text-on-surface-variant pt-1">Forgot your current password?
                            <button onclick="showPage('forgot-password',null)" class="text-primary font-bold hover:underline">Reset via Email</button>
                        </p>
                    </div>
                </div>

                <!-- ══ BLOGS ══ -->
                <div class="page min-w-0 w-full" id="page-blogs">
                    <div class="mb-5 md:mb-6 flex flex-col md:flex-row md:items-center gap-3 md:gap-4">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Blogs</h1>
                            <p class="text-on-surface-variant mt-0.5">Browse and read all published posts.</p>
                        </div>
                        <div class="relative md:ml-auto w-full md:w-72">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                            <input id="blogSearch" type="text" placeholder="Search blogs..." class="w-full pl-10 pr-4 py-2.5 bg-surface-container-highest/60 rounded-xl text-sm border border-transparent text-on-surface" oninput="filterBlogs()" />
                        </div>
                    </div>

                    <!-- Category filter -->
                    <div class="flex gap-0.5 flex-wrap mb-3 border-b border-outline-variant/20 pb-1 overflow-x-auto no-scrollbar">
                        <select name="category_id" onchange="setCat(this.value,this)"
                            class="filter-tab w-auto min-w-fit px-4 py-2 text-sm font-semibold text-on-surface-variant bg-transparent border border-outline-variant/20 rounded-lg appearance-none focus:outline-none focus:ring-0">
                            <option value="all">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['slug']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tag filter strip -->
                    <div class="tag-strip-wrapper mb-6">
                        <div class="tag-strip-header">
                            <div class="tag-strip-label">
                                <span class="tag-strip-icon material-symbols-outlined">sell</span>
                                <span>Filter by tag</span>
                            </div>
                            <button id="clearTagBtn" onclick="clearTag()" class="hidden tag-clear-btn">
                                <span class="material-symbols-outlined" style="font-size:14px">close</span> Clear
                            </button>
                        </div>
                        <div class="tag-strip-track-wrap">
                            <button class="tag-arrow" id="tagArrowLeft" onclick="scrollTagStrip(-1)" aria-label="Scroll left">
                                <span class="material-symbols-outlined">chevron_left</span>
                            </button>
                            <div class="tag-strip-fade-left"></div>
                            <div class="tag-strip-track" id="tagFilterRow"></div>
                            <div class="tag-strip-fade-right"></div>
                            <button class="tag-arrow" id="tagArrowRight" onclick="scrollTagStrip(1)" aria-label="Scroll right">
                                <span class="material-symbols-outlined">chevron_right</span>
                            </button>
                        </div>
                    </div>

                    <div id="blogGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5"></div>
                    <p id="noResults" class="hidden text-center text-on-surface-variant py-16 text-sm">No posts found matching your search.</p>
                </div>

                <!-- ══ BLOG POST VIEW (inline) ══ -->
                <div class="blog-view" id="blog-post-view">
                    <button onclick="closeBlogView()" class="flex items-center gap-2 mb-5 md:mb-6 text-sm font-bold text-primary hover:text-primary-container transition-colors">
                        <span class="material-symbols-outlined text-base">arrow_back</span> Back to Blogs
                    </button>
                    <header class="mb-6 md:mb-8 text-center max-w-3xl mx-auto px-2" id="post-header"></header>
                    <div class="w-full h-52 sm:h-64 md:h-96 rounded-2xl md:rounded-3xl overflow-hidden mb-8 md:mb-10 shadow-2xl shadow-primary/10" id="post-image-wrap"></div>
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-10">
                        <aside class="lg:col-span-4 order-2 lg:order-2 space-y-4 md:space-y-6">
                            <div class="lg:sticky lg:top-24 space-y-4 md:space-y-6">
                                <div class="p-5 md:p-6 rounded-2xl md:rounded-3xl bg-surface-container-low border border-outline-variant/10">
                                    <h3 class="text-[10px] font-black tracking-widest uppercase text-on-surface-variant mb-3 md:mb-4">Reactions</h3>
                                    <div class="flex flex-wrap gap-2 md:gap-3">
                                        <button onclick="react(this,'👍')" class="reaction-btn w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 text-xl relative" data-tooltip="Like"><span class="count text-[10px]">0</span>👍</button>
                                        <button onclick="react(this,'❤️')" class="reaction-btn w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 text-xl relative" data-tooltip="Love"><span class="count text-[10px]">0</span>❤️</button>
                                        <button onclick="react(this,'😂')" class="reaction-btn w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 text-xl relative" data-tooltip="Funny"><span class="count text-[10px]">0</span>😂</button>
                                        <button onclick="react(this,'😮')" class="reaction-btn w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 text-xl relative" data-tooltip="Wow"><span class="count text-[10px]">0</span>😮</button>
                                        <button onclick="react(this,'😢')" class="reaction-btn w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 text-xl relative" data-tooltip="Sad"><span class="count text-[10px]">0</span>😢</button>
                                        <button onclick="react(this,'😡')" class="reaction-btn w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 text-xl relative" data-tooltip="Angry"><span class="count text-[10px]">0</span>😡</button>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <button id="postSaveBtn" onclick="togglePostSave()" class="flex-1 flex items-center justify-center gap-2 py-3 bg-primary-fixed text-on-primary-fixed-variant rounded-xl text-sm font-bold hover:bg-primary-fixed/80 transition-all active:scale-95">
                                        <span class="material-symbols-outlined text-base" id="postSaveIcon">bookmark_add</span>
                                        <span id="postSaveText">Save</span>
                                    </button>
                                    <button onclick="copyBlogLink()" class="flex-1 flex items-center justify-center gap-2 py-3 bg-surface-container text-on-surface rounded-xl text-sm font-bold hover:bg-surface-container-high transition-colors active:scale-95">
                                        <span class="material-symbols-outlined text-base">share</span> Share
                                    </button>
                                </div>
                            </div>
                        </aside>
                        <article class="lg:col-span-8 order-1 space-y-6" id="post-body"></article>
                    </div>

                    <!-- Comments section -->
                    <div class="mt-10 md:mt-14" id="post-comments-section">
                        <div class="border-t border-outline-variant/20 pt-10">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-6 md:mb-8">
                                <h3 class="text-xl md:text-2xl font-black tracking-tight text-on-surface">Community Thoughts (<span id="commentCount">0</span>)</h3>
                                <div class="flex gap-2">
                                    <button onclick="sortComments('top')" id="sortTop" class="px-4 py-1.5 rounded-full text-xs font-bold bg-primary text-white transition-colors">Top</button>
                                    <button onclick="sortComments('new')" id="sortNew" class="px-4 py-1.5 rounded-full text-xs font-bold bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors">Newest</button>
                                </div>
                            </div>
                            <div class="flex gap-3 md:gap-4 p-4 md:p-6 rounded-2xl md:rounded-3xl bg-surface-container-low mb-6 md:mb-8">
                                <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-primary-fixed shrink-0 flex items-center justify-center text-primary font-black text-sm">
                                    <?= $user_initial ?>
                                </div>
                                <div class="flex-1 space-y-3 md:space-y-4 min-w-0">
                                    <textarea id="commentInput" class="w-full bg-transparent border-none focus:ring-0 text-on-surface p-0 placeholder:text-on-surface-variant/40 resize-none h-16 md:h-20 outline-none text-sm leading-relaxed" placeholder="Join the discussion…" oninput="updateCommentCharCount(this)"></textarea>
                                    <div class="flex justify-between items-center gap-2 flex-wrap">
                                        <div class="flex gap-2 md:gap-3 items-center"><span class="text-xs text-on-surface-variant/50" id="charCount">0 / 500</span></div>
                                        <div class="flex gap-2">
                                            <button onclick="clearCommentInput()" class="px-3 md:px-4 py-2 text-on-surface-variant text-sm font-medium hover:text-on-surface transition-colors">Clear</button>
                                            <button onclick="postComment()" class="px-4 md:px-6 py-2 bg-primary text-white font-bold rounded-xl text-sm shadow-lg shadow-primary/20 hover:bg-primary-container transition-all active:scale-95">Post Comment</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="commentsList" class="space-y-4 md:space-y-6"></div>
                            <button id="loadMoreBtn" onclick="loadMoreComments()" class="w-full mt-4 py-3 md:py-4 rounded-2xl border border-outline-variant/30 text-sm font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors hidden">Load more</button>
                        </div>
                    </div>
                </div>

                <!-- ══ SAVED POSTS ══ -->
                <div class="page" id="page-saved">
                    <div class="mb-6 md:mb-8">
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Saved Posts</h1>
                        <p class="text-on-surface-variant mt-1">Posts you've bookmarked for later reading.</p>
                    </div>
                    <div id="savedList" class="space-y-3 md:space-y-4"></div>
                </div>

                <!-- ══ CATEGORIES ══ -->
                <div class="page" id="page-categories">
                    <div class="mb-6 md:mb-8">
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Categories</h1>
                        <p class="text-on-surface-variant mt-1">Browse blogs by topic. Click a category to explore its posts.</p>
                    </div>
                    <div id="categoriesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5"></div>
                </div>

                <!-- ══ NOTIFICATIONS ══ -->
                <div class="page" id="page-notifications">
                    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Notifications</h1>
                            <p class="text-on-surface-variant mt-1">Stay updated on your activity and replies.</p>
                        </div>
                        <div class="sm:ml-auto flex gap-2">
                            <button id="mark-all-read-btn" onclick="markAllNotificationsRead()" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl shadow-lg shadow-primary/20 hover:bg-primary-container transition-all active:scale-95 flex items-center gap-1.5 <?= $unread_count > 0 ? '' : 'hidden' ?>">
                                <span class="material-symbols-outlined text-sm">done_all</span> Mark all as read
                            </button>
                        </div>
                    </div>
                    <div id="notificationsList" class="bg-surface-container-lowest rounded-3xl p-2 md:p-3 border border-outline-variant/10 divide-y divide-outline-variant/10"></div>
                </div>

                <!-- ══ FORGOT PASSWORD ══ -->
                <div class="page" id="page-forgot-password">
                    <div class="mb-6 md:mb-8 flex items-center gap-3">
                        <button onclick="showPage('change-password',null); resetForgotFlow();" class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-surface-container transition-colors shrink-0">
                            <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
                        </button>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Forgot Password</h1>
                            <p class="text-on-surface-variant mt-0.5">Reset your account password via email verification.</p>
                        </div>
                    </div>
                    <div class="max-w-md bg-surface-container-lowest rounded-3xl p-6 md:p-8 space-y-5">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-2" style="background:rgba(99,14,212,.1)">
                            <span class="material-symbols-outlined text-primary text-3xl">lock_reset</span>
                        </div>
                        
                        <!-- Step 1: Request OTP -->
                        <div id="resetStep1" class="space-y-5">
                            <p class="text-sm text-on-surface-variant">We'll send a password verification code to your registered email address.</p>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Email Address</label>
                                <input type="email" id="forgotEmailInput" placeholder="<?= $user_email ?>" class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface" />
                            </div>
                            <button id="sendResetBtn" onclick="requestResetOtp()" class="w-full py-3 stat-gradient text-white rounded-xl font-bold text-sm hover:opacity-90 transition-opacity active:scale-95 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-base">send</span> Send Verification Code
                            </button>
                        </div>
                        
                        <!-- Step 2: Verify OTP and Reset -->
                        <div id="resetStep2" class="space-y-5 hidden">
                            <p class="text-sm text-on-surface-variant">Please enter the 6-digit verification code sent to your email and choose a new password.</p>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Verification Code</label>
                                <input type="text" id="resetOtpInput" placeholder="123456" maxlength="6" class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface text-center tracking-widest font-bold" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">New Password</label>
                                <input type="password" id="resetNewPwd" placeholder="••••••••" oninput="checkStrengthReset(this.value)" class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface" />
                                <div class="h-1.5 rounded-full bg-surface-container-high overflow-hidden mt-2">
                                    <div id="sfillReset" class="h-full rounded-full transition-all duration-300" style="width:0%"></div>
                                </div>
                                <p id="stextReset" class="text-xs text-on-surface-variant"></p>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Confirm New Password</label>
                                <input type="password" id="resetConfirmPwd" placeholder="••••••••" class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface" />
                            </div>
                            <button id="verifyResetBtn" onclick="verifyResetOtp()" class="w-full py-3 stat-gradient text-white rounded-xl font-bold text-sm hover:opacity-90 transition-opacity active:scale-95 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-base">verified</span> Verify & Reset Password
                            </button>
                        </div>
                        
                        <p class="text-center text-xs text-on-surface-variant">Remember your password? <button onclick="showPage('change-password',null); resetForgotFlow();" class="text-primary font-bold hover:underline">Go back</button></p>
                    </div>
                </div>

                <!-- ══ SETTINGS ══ -->
                <div class="page" id="page-settings">
                    <div class="mb-6 md:mb-8">
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Settings</h1>
                        <p class="text-on-surface-variant mt-1">Customize your account preferences.</p>
                    </div>
                    <div class="max-w-lg space-y-4">
                        <div class="bg-surface-container-lowest rounded-2xl p-5 md:p-6 space-y-5">
                            <h3 class="font-black text-xs uppercase tracking-widest text-on-surface-variant">Appearance</h3>
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-sm text-on-surface">Dark Mode</div>
                                </div>
                                <button id="darkModeToggle" onclick="toggleDark()" class="w-11 h-6 bg-surface-container-highest rounded-full relative transition-colors" data-on="false">
                                    <div id="darkModeDot" class="w-4 h-4 bg-white rounded-full absolute top-1 left-1 transition-all shadow-sm"></div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        // ─── PHP → JS ─────────────────────────────────
        <?php
        $js_cat_list = [];
        foreach ($categories as $_c) {
            $js_cat_list[] = ['slug' => $_c['slug'], 'name' => $_c['name']];
        }
        ?>
        const PHP_USER_ID      = <?= (int)$user_id ?>;
        const PHP_USER_INITIAL = <?= json_encode($user_initial) ?>;
        const PHP_USER_NAME    = <?= json_encode($user_name) ?>;
        const PHP_SAVED_SLUGS  = <?= json_encode($saved_slugs, JSON_UNESCAPED_UNICODE) ?>;
        const PHP_CATEGORIES   = <?= json_encode($js_cat_list, JSON_UNESCAPED_UNICODE) ?>;
        const PHP_NOTIFICATIONS = <?= json_encode($notifications, JSON_UNESCAPED_UNICODE) ?>;

        // ─── STATE ────────────────────────────────────
        let BLOGS            = [];
        let SAVED            = [...PHP_SAVED_SLUGS];   // populated from DB via PHP
        let NOTIFICATIONS    = [...PHP_NOTIFICATIONS];
        let BLOG_COMMENTS    = {};
        let activeCat        = 'all';
        let activeTag        = null;
        let currentBlogSlug  = null;
        let isDark           = false;
        let commentSortMode  = 'top';
        let commentDisplayed = 3;
        let nextCommentId    = 200;

        // ─── INIT ─────────────────────────────────────
        const model = "<?= $_GET['model'] ?? '' ?>";

        window.onload = async () => {
            try {
                const res  = await fetch('../include/user_api.php');
                const json = await res.json();
                BLOGS = json.blogs || [];
            } catch(e) {
                console.error('Failed to load blogs:', e);
                BLOGS = [];
            }
            renderBlogs();
            renderSaved();
            renderCategories();
            
            // Check if page reloaded with success message for profile update
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('msg') === 'profile_updated') {
                showPage('profile', null);
            } else if (model === 'blog') {
                showPage('blogs', null);
                closeSidebarMobile();
            }
        };

        // ─── DARK MODE ────────────────────────────────
        function toggleDark() {
            isDark = !isDark;
            document.documentElement.classList.toggle('dark', isDark);
            const topbarIcon = document.getElementById('darkIconTopbar');
            if (topbarIcon) topbarIcon.textContent = isDark ? 'light_mode' : 'dark_mode';
            const toggle = document.getElementById('darkModeToggle');
            const dot    = document.getElementById('darkModeDot');
            if (toggle && dot) {
                toggle.dataset.on = isDark.toString();
                toggle.classList.toggle('bg-primary', isDark);
                toggle.classList.toggle('bg-surface-container-highest', !isDark);
                dot.style.left  = isDark ? 'auto' : '4px';
                dot.style.right = isDark ? '4px'  : 'auto';
            }
            showToast(isDark ? 'Dark mode enabled' : 'Light mode enabled');
        }

        // ─── PAGE NAVIGATION ──────────────────────────
        function showPage(id, btn) {
            document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
            document.getElementById('blog-post-view').classList.remove('active');
            const pg = document.getElementById('page-' + id);
            if (pg) pg.classList.add('active');
            if (btn) {
                document.querySelectorAll('.sidebar-pill').forEach(n => n.classList.remove('active'));
                btn.classList.add('active');
            } else {
                document.querySelectorAll('.sidebar-pill').forEach(n => {
                    const txt = n.textContent.trim().toLowerCase();
                    if (txt.includes(id.replace('-', ' '))) n.classList.add('active');
                });
            }
            window.scrollTo(0, 0);
        }

        // ─── MOBILE SIDEBAR ───────────────────────────
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const isHidden = sidebar.classList.contains('-translate-x-full');
            sidebar.classList.toggle('-translate-x-full', !isHidden);
            overlay.classList.toggle('hidden', !isHidden);
        }
        function closeSidebarMobile() {
            if (window.innerWidth < 1024) {
                document.getElementById('sidebar').classList.add('-translate-x-full');
                document.getElementById('sidebarOverlay').classList.add('hidden');
            }
        }

        // ─── BLOG RENDERING ───────────────────────────
        function renderBlogs() {
            const search  = (document.getElementById('blogSearch') || { value: '' }).value.toLowerCase();
            const grid    = document.getElementById('blogGrid');
            const noRes   = document.getElementById('noResults');
            
            const catSelect = document.querySelector('select[name="category_id"]');
            if (catSelect) catSelect.value = activeCat;
            
            // Tag strip
            const tagRow      = document.getElementById('tagFilterRow');
            const clearTagBtn = document.getElementById('clearTagBtn');
            if (tagRow) {
                const allTags = [...new Set(BLOGS.filter(b => activeCat === 'all' || b.cat === activeCat).flatMap(b => b.tags || []))];
                // Count posts per tag for badge
                const tagCounts = {};
                allTags.forEach(t => {
                    tagCounts[t] = BLOGS.filter(b => (activeCat === 'all' || b.cat === activeCat) && b.tags && b.tags.includes(t)).length;
                });
                tagRow.innerHTML = allTags.map(t => {
                    const isActive = activeTag === t;
                    return `<button onclick="filterByTag('${t}')" class="tag-chip ${isActive ? 'tag-chip-active' : ''}" data-tag="${t}">
                      <span class="tag-chip-dot"></span>
                      <span class="tag-chip-label">#${t}</span>
                      <span class="tag-chip-count">${tagCounts[t]}</span>
                    </button>`;
                }).join('');
                if (clearTagBtn) clearTagBtn.classList.toggle('hidden', !activeTag);
                // Scroll active chip into view
                const activeChip = tagRow.querySelector('.tag-chip-active');
                if (activeChip) activeChip.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                updateTagArrows();
            }

            const filtered = BLOGS.filter(b => {
                const matchCat    = activeCat === 'all' || b.cat === activeCat;
                const matchTag    = !activeTag || (b.tags && b.tags.includes(activeTag));
                const matchSearch = !search || b.title.toLowerCase().includes(search)
                    || b.author.toLowerCase().includes(search)
                    || (b.tags && b.tags.some(t => t.toLowerCase().includes(search)));
                return matchCat && matchTag && matchSearch;
            });


            if (!filtered.length) {
                grid.innerHTML = '';
                noRes.classList.remove('hidden');
                return;
            }
            noRes.classList.add('hidden');

            grid.innerHTML = filtered.map(b => {
                const isSaved = SAVED.includes(b.slug);
                return `
                <div class="blog-card bg-surface-container-lowest rounded-2xl overflow-hidden cursor-pointer flex flex-col h-full" onclick="openBlog('${b.id}')">
                  <div class="h-44 sm:h-48 relative overflow-hidden shrink-0">
                    <img src="${b.img || ''}" alt="${escapeHtml(b.title)}" class="w-full h-full object-cover opacity-85" onerror="this.parentElement.style.background='#ede5f4'" />
                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-primary/60 backdrop-blur-sm text-white text-[10px] font-black uppercase tracking-widest">${escapeHtml(b.cat)}</span>
                  </div>
                  <div class="p-4 md:p-5 flex flex-col flex-1">
                    <h3 class="font-black text-sm md:text-base leading-snug text-on-surface mb-2 line-clamp-2">${escapeHtml(b.title)}</h3>
                    <div class="flex items-center justify-between text-xs text-on-surface-variant mb-2">
                      <div class="flex items-center gap-2 truncate pr-2">
                        <img src="../${b.author_image || 'upload/profile-images/default.png'}" 
                             alt="${escapeHtml(b.author)}" 
                             class="w-6 h-6 rounded-full object-cover shrink-0" 
                             onerror="this.src='../upload/profile-images/default.png'" />
                        <span class="font-semibold truncate">${escapeHtml(b.author)}</span>
                      </div>
                      <span class="shrink-0 font-medium">${b.date}</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mb-3 mt-auto">
                      ${(() => {
                          const maxVisible = 3;
                          const tags = b.tags || [];
                          const visible = tags.slice(0, maxVisible);
                          const remaining = tags.length - maxVisible;
                          let html = visible.map(t => `<span onclick="event.stopPropagation();filterByTag('${t}')" class="tag-pill px-2 py-0.5 rounded-full text-[10px] font-bold bg-surface-container text-on-surface-variant ${activeTag === t ? 'active-tag' : ''}">#${t}</span>`).join('');
                          if (remaining > 0) {
                              html += `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-surface-container-high/60 text-on-surface-variant/80 cursor-default" title="${escapeHtml(tags.slice(maxVisible).join(', '))}" onclick="event.stopPropagation()">+${remaining}</span>`;
                          }
                          return html;
                      })()}
                    </div>
                    
                    <!-- Stats row -->
                    <div class="flex items-center gap-3.5 text-[11px] font-semibold text-on-surface-variant/70 mb-3.5 mt-1 border-t border-outline-variant/5 pt-2 shrink-0">
                      <span class="flex items-center gap-1" title="Total Reactions">
                        <span class="material-symbols-outlined text-sm text-[#9b005c]">favorite</span>
                        <span>${b.reaction_count || 0}</span>
                      </span>
                      <span class="flex items-center gap-1" title="Comments">
                        <span class="material-symbols-outlined text-sm text-[#4b41e1]">chat_bubble</span>
                        <span>${b.comment_count || 0}</span>
                      </span>
                      <span class="flex items-center gap-1" title="Shares">
                        <span class="material-symbols-outlined text-sm text-[#f0a500]">share</span>
                        <span>${b.share_count || 0}</span>
                      </span>
                      <span class="flex items-center gap-1 ml-auto" title="Views">
                        <span class="material-symbols-outlined text-sm text-primary">visibility</span>
                        <span>${b.views || 0}</span>
                      </span>
                    </div>

                    <div class="flex items-center gap-2 mt-2 pt-3 border-t border-outline-variant/10 shrink-0">
                      <button onclick="event.stopPropagation();openBlog('${b.id}')" class="flex-1 py-2 md:py-2.5 stat-gradient text-white rounded-xl text-xs font-bold text-center hover:opacity-90 transition-opacity">Read Post →</button>
                      <button onclick="event.stopPropagation();toggleSave('${b.slug}',this)" class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center rounded-xl bg-surface-container hover:bg-primary-fixed transition-colors shrink-0" title="${isSaved ? 'Remove from saved' : 'Save post'}">
                        <span class="material-symbols-outlined text-base md:text-lg ${isSaved ? 'text-primary ms-filled' : 'text-on-surface-variant'}">${isSaved ? 'bookmark' : 'bookmark'}</span>
                      </button>
                    </div>
                  </div>
                </div>`;
            }).join('');
        }

        function filterBlogs() { renderBlogs(); }

        function setCat(cat, el) {
            activeCat = cat;
            activeTag = null;
            document.querySelectorAll('button.filter-tab').forEach(t => t.classList.remove('active'));
            if (el && el.tagName === 'BUTTON') el.classList.add('active');
            renderBlogs();
        }

        function filterByTag(tag) {
            activeTag = (activeTag === tag) ? null : tag;
            showPage('blogs', document.querySelector('[onclick*="\'blogs\'"]'));
            renderBlogs();
        }

        function clearTag() { activeTag = null; renderBlogs(); }

        // ─── CATEGORIES PAGE ──────────────────────────
        function renderCategories() {
            const grid = document.getElementById('categoriesGrid');
            if (!grid) return;
            const cats = [...new Set(BLOGS.map(b => b.cat))];
            if (!cats.length) {
                // Fallback: use PHP-side categories
                grid.innerHTML = PHP_CATEGORIES.map(c => `
                <div class="cat-card bg-surface-container-lowest rounded-2xl p-6 cursor-pointer border border-transparent hover:border-outline-variant/30"
                     onclick="openCategoryBlogs('${c.slug}')">
                  <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4" style="background:rgba(99,14,212,.1)">
                    <span class="material-symbols-outlined text-2xl text-primary">article</span>
                  </div>
                  <h3 class="font-black text-lg text-on-surface mb-1">${escapeHtml(c.name)}</h3>
                  <button class="mt-4 w-full py-2 rounded-xl text-xs font-bold text-white stat-gradient hover:opacity-90">Browse ${escapeHtml(c.name)} →</button>
                </div>`).join('');
                return;
            }
            const catColors = ['#630ed4','#4b41e1','#9b005c','#f0a500','#16a34a','#0ea5e9'];
            const catIcons  = ['palette','code','style','article','star','bolt'];
            grid.innerHTML = cats.map((cat, i) => {
                const color = catColors[i % catColors.length];
                const icon  = catIcons[i % catIcons.length];
                const count = BLOGS.filter(b => b.cat === cat).length;
                const allTags = [...new Set(BLOGS.filter(b => b.cat === cat).flatMap(b => b.tags || []))].slice(0, 4);
                return `
                <div class="cat-card bg-surface-container-lowest rounded-2xl p-6 cursor-pointer border border-transparent hover:border-outline-variant/30" onclick="openCategoryBlogs('${cat}')">
                  <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4" style="background:${color}18">
                    <span class="material-symbols-outlined text-2xl" style="color:${color}">${icon}</span>
                  </div>
                  <h3 class="font-black text-lg text-on-surface mb-1">${escapeHtml(cat)}</h3>
                  <p class="text-xs text-on-surface-variant mb-3">${count} post${count !== 1 ? 's' : ''}</p>
                  <div class="flex flex-wrap gap-1.5">
                    ${allTags.map(t => `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-surface-container text-on-surface-variant">#${t}</span>`).join('')}
                  </div>
                  <button class="mt-4 w-full py-2 rounded-xl text-xs font-bold text-white hover:opacity-90 transition-opacity" style="background:${color}">Browse ${escapeHtml(cat)} →</button>
                </div>`;
            }).join('');
        }

        function openCategoryBlogs(cat) {
            activeTag = null;
            activeCat = cat;
            showPage('blogs', document.querySelector('[onclick*="\'blogs\'"]'));
            renderBlogs();
        }

        // ─── SAVE / UNSAVE ────────────────────────────
        function toggleSave(slug, btn) {
            const idx = SAVED.indexOf(slug);
            if (idx > -1) { SAVED.splice(idx, 1); showToast('Post removed from saved'); }
            else           { SAVED.push(slug);     showToast('Post saved to bookmarks! 🔖'); }
            renderBlogs();
            renderSaved();
            if (currentBlogSlug === slug) updatePostSaveBtn();
        }

        // ─── OPEN BLOG (redirect to single-post) ─────
        function openBlog(id) {
            window.location.href = "../redirect-post.php?id=" + id;
        }

        function closeBlogView() {
            document.body.classList.remove('single-blog-active');
            document.getElementById('blog-post-view').classList.remove('active');
            history.pushState({ type: 'page', page: 'blogs' }, 'Blogs', location.pathname);
            showPage('blogs', document.querySelector('[onclick*="\'blogs\'"]'));
        }

        window.onpopstate = function(e) {
            if (e.state && e.state.type === 'blog') { openBlog(e.state.id); }
            else {
                document.body.classList.remove('single-blog-active');
                document.getElementById('blog-post-view').classList.remove('active');
                showPage(e.state?.page || 'blogs', null);
            }
        };

        // ─── POST SAVE BUTTON ─────────────────────────
        function togglePostSave() {
            const idx = SAVED.indexOf(currentBlogSlug);
            if (idx > -1) { SAVED.splice(idx, 1); showToast('Post removed from saved'); }
            else           { SAVED.push(currentBlogSlug); showToast('Post saved to bookmarks! 🔖'); }
            updatePostSaveBtn();
            renderBlogs();
            renderSaved();
        }
        function updatePostSaveBtn() {
            if (!currentBlogSlug) return;
            const saved = SAVED.includes(currentBlogSlug);
            const btn  = document.getElementById('postSaveBtn');
            const icon = document.getElementById('postSaveIcon');
            const text = document.getElementById('postSaveText');
            if (!btn) return;
            icon.textContent = 'bookmark';
            icon.className   = 'material-symbols-outlined text-base' + (saved ? ' ms-filled' : '');
            text.textContent = saved ? 'Saved' : 'Save';
            btn.className    = `flex-1 flex items-center justify-center gap-2 py-3 ${saved ? 'bg-primary text-white' : 'bg-primary-fixed text-on-primary-fixed-variant'} rounded-xl text-sm font-bold hover:opacity-90 transition-all active:scale-95`;
        }
        function copyBlogLink() {
            navigator.clipboard.writeText(window.location.href).catch(() => {});
            showToast('Link copied to clipboard!');
        }

        // ─── SAVED POSTS ──────────────────────────────
        function renderSaved() {
            const container = document.getElementById('savedList');
            const saved     = BLOGS.filter(b => SAVED.includes(b.slug));
            if (!saved.length) {
                container.innerHTML = `<div class="text-center text-on-surface-variant py-16"><span class="material-symbols-outlined text-4xl mb-3 block">bookmark_border</span><p class="text-sm">No saved posts yet. Browse blogs and save posts you love!</p></div>`;
                return;
            }
            container.innerHTML = saved.map(b => `
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 md:gap-4 bg-surface-container-lowest rounded-2xl p-4 md:p-5 w-full overflow-hidden">
              <div class="flex items-center gap-3 flex-1 min-w-0">
                  <div class="w-14 h-14 md:w-16 md:h-16 rounded-xl shrink-0 overflow-hidden bg-surface-container-high">
                    <img src="${b.img || ''}" class="w-full h-full object-cover" onerror="this.style.display='none'" />
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm truncate text-on-surface">${escapeHtml(b.title)}</div>
                    <div class="text-xs text-on-surface-variant mt-1">${escapeHtml(b.author)} · ${b.date}</div>
                  </div>
              </div>
              <div class="flex gap-2 sm:shrink-0 w-full sm:w-auto mt-2 sm:mt-0">
                <button onclick="openBlog('${b.id}')" class="flex-1 sm:flex-none px-3 md:px-4 py-2 stat-gradient text-white rounded-xl text-xs font-bold hover:opacity-90 transition-opacity text-center">View</button>
                <button onclick="toggleSave('${b.slug}',this)" class="flex-1 sm:flex-none px-3 md:px-4 py-2 bg-error-container/30 text-error rounded-xl text-xs font-bold hover:bg-error-container/60 transition-colors text-center">Remove</button>
              </div>
            </div>`).join('');
        }

        // ─── TOPBAR SEARCH ────────────────────────────
        function handleTopbarSearch(q) {
            const box = document.getElementById('topbarResults');
            if (!q.trim()) { box.classList.add('hidden'); return; }
            const results = BLOGS.filter(b => b.title.toLowerCase().includes(q.toLowerCase())).slice(0, 4);
            if (!results.length) { box.classList.add('hidden'); return; }
            box.innerHTML = results.map(b => `
            <div onclick="openBlog('${b.id}');document.getElementById('topbarResults').classList.add('hidden')" class="flex items-center gap-3 px-4 py-3 hover:bg-surface-container-low cursor-pointer border-b border-outline-variant/10 last:border-0">
              <span class="material-symbols-outlined text-primary text-base">article</span>
              <div><div class="text-sm font-semibold text-on-surface">${escapeHtml(b.title)}</div><div class="text-xs text-on-surface-variant">${escapeHtml(b.author)} · ${escapeHtml(b.cat)}</div></div>
            </div>`).join('');
            box.classList.remove('hidden');
        }
        function goSearchBlogs(q) {
            document.getElementById('topbarResults').classList.add('hidden');
            if (!q) return;
            showPage('blogs', document.querySelector('[onclick*="\'blogs\'"]'));
            document.getElementById('blogSearch').value = q;
            filterBlogs();
        }
        document.addEventListener('click', e => {
            if (!e.target.closest('#topbarSearch') && !e.target.closest('#topbarResults'))
                document.getElementById('topbarResults')?.classList.add('hidden');
        });

        // ─── COMMENT SYSTEM ───────────────────────────
        function renderBlogComments() {
            const comments = BLOG_COMMENTS[currentBlogSlug] || [];
            let sorted = [...comments];
            if (commentSortMode === 'top') sorted.sort((a, b) => b.likes - a.likes);
            else sorted.sort((a, b) => b.id - a.id);
            const toShow = sorted.slice(0, commentDisplayed);
            document.getElementById('commentsList').innerHTML = toShow.map(c => renderCommentHtml(c)).join('');
            const loadBtn = document.getElementById('loadMoreBtn');
            if (loadBtn) loadBtn.classList.toggle('hidden', commentDisplayed >= comments.length);
        }

        function renderCommentHtml(c) {
            const isOwn     = c.id >= 200;
            const repliesHtml = (c.replies || []).map(r => `
            <div class="reply-comment mt-3">
              <div class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-surface-container-high shrink-0 flex items-center justify-center text-xs font-black text-on-surface-variant">${r.avatar}</div>
                <div class="flex-1 min-w-0 bg-surface-container-low rounded-2xl px-4 py-3">
                  <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                    <span class="font-bold text-sm text-on-surface">${escapeHtml(r.author)}</span>
                    <span class="text-xs text-on-surface-variant">${r.time}</span>
                  </div>
                  <p class="text-sm text-on-surface leading-relaxed">${escapeHtml(r.text)}</p>
                  <button onclick="likeComment(${r.id},true,${c.id})" class="comment-like flex items-center gap-1 text-xs font-bold text-on-surface-variant hover:text-on-surface transition-colors mt-2 ${r.liked ? 'liked' : ''}">
                    <span class="material-symbols-outlined text-[14px]">thumb_up</span> <span id="likes-${r.id}">${r.likes}</span>
                  </button>
                </div>
              </div>
            </div>`).join('');

            return `<div class="flex gap-3" id="comment-${c.id}">
              <div class="w-9 h-9 rounded-full bg-primary-fixed shrink-0 flex items-center justify-center text-primary font-black text-sm">${c.avatar}</div>
              <div class="flex-1 min-w-0">
                <div class="bg-surface-container-low rounded-2xl px-4 py-4">
                  <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <span class="font-bold text-sm text-on-surface">${escapeHtml(c.author)}</span>
                    <span class="text-xs text-on-surface-variant">${c.time}</span>
                    ${isOwn ? '<span class="text-[10px] px-2 py-0.5 rounded-full bg-primary-fixed text-primary font-bold">You</span>' : ''}
                  </div>
                  <p class="text-sm text-on-surface leading-relaxed" id="comment-text-${c.id}">${escapeHtml(c.text)}</p>
                  <div class="comment-edit-area mt-3" id="edit-area-${c.id}">
                    <textarea id="edit-input-${c.id}" class="w-full bg-surface-container border border-outline-variant/20 rounded-xl px-3 py-2 text-sm text-on-surface outline-none focus:border-primary/30 resize-none h-16">${escapeHtml(c.text)}</textarea>
                    <div class="flex justify-end gap-2 mt-2">
                      <button onclick="cancelEditComment(${c.id})" class="px-4 py-1.5 text-on-surface-variant text-sm font-medium hover:text-on-surface transition-colors">Cancel</button>
                      <button onclick="saveEditComment(${c.id})" class="px-5 py-1.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-container transition-colors active:scale-95">Save</button>
                    </div>
                  </div>
                  <div class="flex items-center gap-4 pt-2 mt-1">
                    <button onclick="startReply(${c.id})" class="text-xs font-bold text-primary hover:underline transition-colors">Reply</button>
                    <button onclick="likeComment(${c.id},false,null)" class="comment-like flex items-center gap-1 text-xs font-bold text-on-surface-variant hover:text-on-surface transition-colors ${c.liked ? 'liked' : ''}">
                      <span class="material-symbols-outlined text-[14px]">thumb_up</span> <span id="likes-${c.id}">${c.likes}</span>
                    </button>
                    ${isOwn ? `<button onclick="editComment(${c.id})" class="text-xs font-bold text-secondary hover:underline transition-colors">Edit</button>` : ''}
                    ${isOwn ? `<button onclick="deleteComment(${c.id})" class="text-xs font-bold text-error hover:underline transition-colors">Delete</button>` : ''}
                  </div>
                </div>
                ${repliesHtml}
                <div id="reply-box-${c.id}" class="hidden mt-3">
                  <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-black text-xs shrink-0">${PHP_USER_INITIAL}</div>
                    <div class="flex-1">
                      <textarea id="reply-input-${c.id}" class="w-full bg-surface-container-low border border-outline-variant/20 rounded-xl px-4 py-3 text-sm text-on-surface outline-none focus:border-primary/30 resize-none h-16" placeholder="Write a reply..."></textarea>
                      <div class="flex justify-end gap-2 mt-2">
                        <button onclick="cancelReply(${c.id})" class="px-4 py-1.5 text-on-surface-variant text-sm font-medium hover:text-on-surface transition-colors">Cancel</button>
                        <button onclick="postReply(${c.id})" class="px-5 py-1.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-container transition-colors active:scale-95">Reply</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>`;
        }

        function postComment() {
            const input = document.getElementById('commentInput');
            const text  = input.value.trim();
            if (!text) { showToast('Please write something first!'); return; }
            if (text.length > 500) { showToast('Comment too long (max 500 chars)'); return; }
            if (!BLOG_COMMENTS[currentBlogSlug]) BLOG_COMMENTS[currentBlogSlug] = [];
            BLOG_COMMENTS[currentBlogSlug].unshift({
                id: nextCommentId++, author: PHP_USER_NAME, avatar: PHP_USER_INITIAL,
                time: 'just now', text, likes: 0, liked: false, replies: []
            });
            commentDisplayed++;
            input.value = '';
            document.getElementById('charCount').textContent = '0 / 500';
            renderBlogComments();
            updateCommentCountEl();
            showToast('Comment posted! 💬');
        }

        function postReply(commentId) {
            const input = document.getElementById('reply-input-' + commentId);
            const text  = input.value.trim();
            if (!text) return;
            const comment = (BLOG_COMMENTS[currentBlogSlug] || []).find(c => c.id === commentId);
            if (!comment) return;
            comment.replies.push({ id: nextCommentId++, author: PHP_USER_NAME, avatar: PHP_USER_INITIAL, time: 'just now', text, likes: 0, liked: false });
            updateCommentCountEl(1);
            renderBlogComments();
            showToast('Reply posted!');
        }

        function startReply(id) {
            document.querySelectorAll('[id^=reply-box-]').forEach(el => el.classList.add('hidden'));
            document.getElementById('reply-box-' + id)?.classList.remove('hidden');
            document.getElementById('reply-input-' + id)?.focus();
        }
        function cancelReply(id) { document.getElementById('reply-box-' + id)?.classList.add('hidden'); }

        function likeComment(commentId, isReply, parentId) {
            const comments = BLOG_COMMENTS[currentBlogSlug] || [];
            if (isReply) {
                const parent = comments.find(c => c.id === parentId);
                const reply  = parent?.replies.find(r => r.id === commentId);
                if (!reply) return;
                reply.liked = !reply.liked;
                reply.likes += reply.liked ? 1 : -1;
            } else {
                const comment = comments.find(c => c.id === commentId);
                if (!comment) return;
                comment.liked = !comment.liked;
                comment.likes += comment.liked ? 1 : -1;
            }
            renderBlogComments();
        }

        function deleteComment(id) {
            if (!BLOG_COMMENTS[currentBlogSlug]) return;
            BLOG_COMMENTS[currentBlogSlug] = BLOG_COMMENTS[currentBlogSlug].filter(c => c.id !== id);
            if (commentDisplayed > 0) commentDisplayed--;
            updateCommentCountEl(-1);
            renderBlogComments();
            showToast('Comment deleted');
        }

        function sortComments(mode) {
            commentSortMode  = mode;
            commentDisplayed = 3;
            document.getElementById('sortTop').className = mode === 'top'
                ? 'px-4 py-1.5 rounded-full text-xs font-bold bg-primary text-white transition-colors'
                : 'px-4 py-1.5 rounded-full text-xs font-bold bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors';
            document.getElementById('sortNew').className = mode === 'new'
                ? 'px-4 py-1.5 rounded-full text-xs font-bold bg-primary text-white transition-colors'
                : 'px-4 py-1.5 rounded-full text-xs font-bold bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors';
            renderBlogComments();
        }
        function loadMoreComments()    { commentDisplayed += 5; renderBlogComments(); }
        function updateCommentCountEl(delta = 0) {
            const comments = BLOG_COMMENTS[currentBlogSlug] || [];
            let total = comments.length + comments.reduce((s, c) => s + (c.replies?.length || 0), 0) + delta;
            total = Math.max(0, total);
            const el = document.getElementById('commentCount');
            if (el) el.textContent = total;
        }
        function updateCommentCharCount(el) {
            const cc = document.getElementById('charCount');
            if (cc) cc.textContent = el.value.length + ' / 500';
        }
        function clearCommentInput() {
            const input = document.getElementById('commentInput');
            if (input) input.value = '';
            const cc = document.getElementById('charCount');
            if (cc) cc.textContent = '0 / 500';
        }
        function editComment(id) {
            document.querySelectorAll('.comment-edit-area.open').forEach(el => el.classList.remove('open'));
            const area = document.getElementById('edit-area-' + id);
            if (area) {
                area.classList.add('open');
                const input = document.getElementById('edit-input-' + id);
                if (input) { input.focus(); input.setSelectionRange(input.value.length, input.value.length); }
            }
        }
        function saveEditComment(id) {
            const input   = document.getElementById('edit-input-' + id);
            if (!input) return;
            const newText = input.value.trim();
            if (!newText) { showToast('Comment cannot be empty'); return; }
            const comment = (BLOG_COMMENTS[currentBlogSlug] || []).find(c => c.id === id);
            if (!comment) return;
            comment.text = newText;
            comment.time = comment.time + ' (edited)';
            renderBlogComments();
            showToast('Comment updated ✏️');
        }
        function cancelEditComment(id) {
            const area = document.getElementById('edit-area-' + id);
            if (area) area.classList.remove('open');
        }

        // ─── MISC ─────────────────────────────────────
        async function requestResetOtp() {
            const input = document.getElementById('forgotEmailInput');
            const email = input ? input.value.trim() : '';
            if (!email || !email.includes('@')) {
                showToast('Please enter a valid email address');
                return;
            }
            
            const btn = document.getElementById('sendResetBtn');
            btn.disabled = true;
            btn.innerHTML = 'Sending...';
            
            const fd = new FormData();
            fd.append('action', 'send_reset_otp');
            fd.append('email', email);
            
            try {
                const res = await fetch(window.location.href, { method: 'POST', body: fd });
                const json = await res.json();
                if (json.success) {
                    showToast('✉️ Verification code sent successfully!');
                    // console.log('--- DEBUG RESET OTP ---');
                    // console.log('Verification Code:', json.debug_otp);
                    // console.log('------------------------');
                    
                    document.getElementById('resetStep1').classList.add('hidden');
                    document.getElementById('resetStep2').classList.remove('hidden');
                } else {
                    showToast('❌ ' + (json.error || 'Failed to send verification code.'));
                }
            } catch (e) {
                console.error(e);
                showToast('❌ An error occurred.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined text-base">send</span> Send Verification Code';
            }
        }
        
        async function verifyResetOtp() {
            const otp = document.getElementById('resetOtpInput').value.trim();
            const newPwd = document.getElementById('resetNewPwd').value;
            const confirmPwd = document.getElementById('resetConfirmPwd').value;
            
            if (!otp || otp.length !== 6) {
                showToast('Please enter the 6-digit verification code.');
                return;
            }
            if (!newPwd || !confirmPwd) {
                showToast('Please fill out all password fields.');
                return;
            }
            if (newPwd.length < 8) {
                showToast('New password must be at least 8 characters.');
                return;
            }
            if (newPwd !== confirmPwd) {
                showToast('Passwords do not match.');
                return;
            }
            
            const btn = document.getElementById('verifyResetBtn');
            btn.disabled = true;
            btn.innerHTML = 'Verifying...';
            
            const fd = new FormData();
            fd.append('action', 'verify_reset_otp');
            fd.append('otp', otp);
            fd.append('new_password', newPwd);
            
            try {
                const res = await fetch(window.location.href, { method: 'POST', body: fd });
                const json = await res.json();
                if (json.success) {
                    showToast('Password reset successfully! ✅');
                    resetForgotFlow();
                    setTimeout(() => showPage('profile', null), 1500);
                } else {
                    showToast('❌ ' + (json.error || 'Verification failed.'));
                }
            } catch (e) {
                console.error(e);
                showToast('❌ An error occurred.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined text-base">verified</span> Verify & Reset Password';
            }
        }
        
        function resetForgotFlow() {
            document.getElementById('forgotEmailInput').value = '';
            document.getElementById('resetOtpInput').value = '';
            document.getElementById('resetNewPwd').value = '';
            document.getElementById('resetConfirmPwd').value = '';
            document.getElementById('sfillReset').style.width = '0%';
            document.getElementById('stextReset').textContent = '';
            document.getElementById('resetStep1').classList.remove('hidden');
            document.getElementById('resetStep2').classList.add('hidden');
        }
        
        function checkStrengthReset(v) {
            let s = 0;
            if (v.length >= 8) s++; if (/[A-Z]/.test(v)) s++; if (/[0-9]/.test(v)) s++; if (/[^A-Za-z0-9]/.test(v)) s++;
            const levels = [{w:'0%',c:'transparent',t:''},{w:'25%',c:'#ba1a1a',t:'Weak'},{w:'50%',c:'#f0a500',t:'Fair'},{w:'75%',c:'#4b41e1',t:'Good'},{w:'100%',c:'#16a34a',t:'Strong'}];
            document.getElementById('sfillReset').style.cssText = `width:${levels[s].w};background:${levels[s].c}`;
            document.getElementById('stextReset').textContent = levels[s].t;
        }

        function escapeHtml(str) {
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function toggleProfileMenu() { document.getElementById('profileMenu').classList.toggle('hidden'); }
        function closeProfileMenu()  { document.getElementById('profileMenu').classList.add('hidden'); }

        document.addEventListener('click', e => {
            if (!e.target.closest('#profileMenu') && !e.target.closest('[onclick*="toggleProfileMenu"]')) closeProfileMenu();
        });

        async function updatePassword() {
            const oldPwd = document.getElementById('oldPwd').value;
            const newPwd = document.getElementById('newPwd').value;
            const confirmPwd = document.getElementById('confirmPwd').value;
            
            if (!oldPwd || !newPwd || !confirmPwd) {
                showToast('Please fill out all fields.');
                return;
            }
            if (newPwd.length < 8) {
                showToast('New password must be at least 8 characters.');
                return;
            }
            if (newPwd !== confirmPwd) {
                showToast('Confirm password does not match.');
                return;
            }
            
            const fd = new FormData();
            fd.append('action', 'change_password');
            fd.append('old_password', oldPwd);
            fd.append('new_password', newPwd);
            
            try {
                const res = await fetch(window.location.href, { method: 'POST', body: fd });
                const json = await res.json();
                if (json.success) {
                    showToast('Password updated successfully! ✅');
                    document.getElementById('oldPwd').value = '';
                    document.getElementById('newPwd').value = '';
                    document.getElementById('confirmPwd').value = '';
                    document.getElementById('sfill').style.width = '0%';
                    document.getElementById('stext').textContent = '';
                    setTimeout(() => showPage('profile', null), 1500);
                } else {
                    showToast('❌ ' + (json.error || 'Failed to update password.'));
                }
            } catch (e) {
                console.error(e);
                showToast('❌ An error occurred.');
            }
        }

        function checkStrength(v) {
            let s = 0;
            if (v.length >= 8) s++; if (/[A-Z]/.test(v)) s++; if (/[0-9]/.test(v)) s++; if (/[^A-Za-z0-9]/.test(v)) s++;
            const levels = [{w:'0%',c:'transparent',t:''},{w:'25%',c:'#ba1a1a',t:'Weak'},{w:'50%',c:'#f0a500',t:'Fair'},{w:'75%',c:'#4b41e1',t:'Good'},{w:'100%',c:'#16a34a',t:'Strong'}];
            document.getElementById('sfill').style.cssText = `width:${levels[s].w};background:${levels[s].c}`;
            document.getElementById('stext').textContent = levels[s].t;
        }

        function react(btn, emoji) {
            const wasActive = btn.classList.contains('active');
            document.querySelectorAll('.reaction-btn').forEach(b => {
                b.classList.remove('active');
                const cnt = b.querySelector('.count');
                if (cnt) cnt.style.cssText = 'opacity:0;transform:scale(0)';
            });
            if (!wasActive) {
                btn.classList.add('active');
                const cnt = btn.querySelector('.count');
                cnt.textContent = '1';
                cnt.style.cssText = 'opacity:1;transform:scale(1)';
                showToast(`You reacted ${emoji}`);
            }
        }

        function logout() {
            showToast('Logging out...');
            window.location.href = '../actions/logout.php';
        }

        // ─── TAG STRIP SCROLL ─────────────────────────
        function scrollTagStrip(dir) {
            const track = document.getElementById('tagFilterRow');
            if (track) {
                track.scrollBy({ left: dir * 220, behavior: 'smooth' });
                setTimeout(updateTagArrows, 320);
            }
        }
        function updateTagArrows() {
            const track = document.getElementById('tagFilterRow');
            const left  = document.getElementById('tagArrowLeft');
            const right = document.getElementById('tagArrowRight');
            if (!track || !left || !right) return;
            left.classList.toggle('hidden-arrow',  track.scrollLeft <= 4);
            right.classList.toggle('hidden-arrow', track.scrollLeft + track.clientWidth >= track.scrollWidth - 4);
        }
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('tagFilterRow');
            if (track) track.addEventListener('scroll', updateTagArrows, { passive: true });
        });

        // ─── NOTIFICATIONS SYSTEM ─────────────────────
        function renderNotifications() {
            const list = document.getElementById('notificationsList');
            if (!list) return;
            if (!NOTIFICATIONS.length) {
                list.innerHTML = `
                <div class="text-center text-on-surface-variant py-16">
                    <span class="material-symbols-outlined text-4xl mb-3 block">notifications_off</span>
                    <p class="text-sm">You have no notifications yet.</p>
                </div>`;
                return;
            }
            list.innerHTML = NOTIFICATIONS.map(n => {
                let icon = 'notifications';
                let colorBg = 'rgba(99,14,212,.1)';
                let colorText = '#630ed4';
                if (n.type === 'comment') {
                    icon = 'chat';
                    colorBg = 'rgba(75,65,225,.1)';
                    colorText = '#4b41e1';
                } else if (n.type === 'like') {
                    icon = 'thumb_up';
                    colorBg = 'rgba(34,197,94,.1)';
                    colorText = '#16a34a';
                } else if (n.type === 'reaction') {
                    icon = 'favorite';
                    colorBg = 'rgba(155,0,92,.1)';
                    colorText = '#9b005c';
                } else if (n.type === 'post') {
                    icon = 'newspaper';
                    colorBg = 'rgba(240,165,0,.1)';
                    colorText = '#f0a500';
                }
                
                return `
                <div class="flex items-start gap-3 md:gap-4 p-4 md:p-5 hover:bg-surface-container-low transition-colors group relative ${n.is_read ? 'opacity-70' : 'bg-primary/5 rounded-2xl'}">
                    <div class="w-9 h-9 md:w-10 md:h-10 rounded-full flex items-center justify-center shrink-0" style="background:${colorBg}">
                        <span class="material-symbols-outlined text-base md:text-lg" style="color:${colorText}">${icon}</span>
                    </div>
                    <div class="flex-1 min-w-0 pr-8">
                        <div class="font-bold text-sm text-on-surface flex items-center gap-2">
                            ${n.title}
                            ${!n.is_read ? '<span class="w-2 h-2 bg-primary rounded-full inline-block shrink-0"></span>' : ''}
                        </div>
                        <p class="text-on-surface-variant text-xs mt-1 leading-relaxed">${n.message}</p>
                        <div class="text-[10px] text-on-surface-variant/60 mt-1 font-medium">${n.date}</div>
                    </div>
                    <button onclick="deleteNotification(${n.id}, this)" class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container text-on-surface-variant" title="Delete notification">
                        <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                </div>`;
            }).join('');
        }

        async function markAllNotificationsRead() {
            try {
                const fd = new FormData();
                fd.append('action', 'mark_notifications_read');
                const res = await fetch('home.php', { method: 'POST', body: fd });
                const json = await res.json();
                if (json.success) {
                    NOTIFICATIONS.forEach(n => n.is_read = 1);
                    document.getElementById('notif-badge').classList.add('hidden');
                    const dot = document.getElementById('notif-dot-topbar');
                    if (dot) dot.classList.add('hidden');
                    document.getElementById('mark-all-read-btn').classList.add('hidden');
                    renderNotifications();
                    showToast('All notifications marked as read');
                } else {
                    showToast('Failed to update notifications');
                }
            } catch(e) {
                console.error(e);
                showToast('An error occurred');
            }
        }

        async function deleteNotification(id, btn) {
            if (event) event.stopPropagation();
            try {
                const fd = new FormData();
                fd.append('action', 'delete_notification');
                fd.append('id', id);
                const res = await fetch('home.php', { method: 'POST', body: fd });
                const json = await res.json();
                if (json.success) {
                    const wasUnread = NOTIFICATIONS.find(n => n.id === id && !n.is_read);
                    NOTIFICATIONS = NOTIFICATIONS.filter(n => n.id !== id);
                    if (wasUnread) {
                        const badge = document.getElementById('notif-badge');
                        let count = parseInt(badge.textContent) - 1;
                        badge.textContent = count;
                        if (count <= 0) {
                            badge.classList.add('hidden');
                            const dot = document.getElementById('notif-dot-topbar');
                            if (dot) dot.classList.add('hidden');
                            document.getElementById('mark-all-read-btn').classList.add('hidden');
                        }
                    }
                    renderNotifications();
                    showToast('Notification deleted');
                } else {
                    showToast('Failed to delete notification');
                }
            } catch(e) {
                console.error(e);
                showToast('An error occurred');
            }
        }
    </script>
</body>
</html>
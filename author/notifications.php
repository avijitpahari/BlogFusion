<?php
include "../include/session.php";
requireAuthor();
include "../include/db.php";
include "../include/author_nav_sidebar.php";

$user_id = (int)$_SESSION['user_id'];

// ---- AJAX ACTIONS FOR NOTIFICATIONS ----
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
}

// Fetch counts
$unread_query = "SELECT COUNT(*) as total FROM notifications WHERE user_id = $user_id AND is_read = 0";
$unread_result = mysqli_query($conn, $unread_query);
$unread_row = mysqli_fetch_assoc($unread_result);
$unread_count = (int)($unread_row['total'] ?? 0);

$total_query = "SELECT COUNT(*) as total FROM notifications WHERE user_id = $user_id";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_count = (int)($total_row['total'] ?? 0);

// Fetch notifications
$notif_query = "SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 50";
$notif_result = mysqli_query($conn, $notif_query);
$notifications_db = $notif_result ? mysqli_fetch_all($notif_result, MYSQLI_ASSOC) : [];

$notifications = [];
foreach ($notifications_db as $n) {
    $notifications[] = [
        'id' => (int)$n['id'],
        'title' => htmlspecialchars($n['title']),
        'message' => htmlspecialchars($n['message']),
        'type' => htmlspecialchars($n['type']),
        'is_read' => (int)$n['is_read'],
        'date' => date('M d, Y · H:i', strtotime($n['created_at']))
    ];
}
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Notifications - Luminous Editor</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "tertiary": "#9b005c", "surface-container": "#f3ebfa",
                        "on-tertiary-fixed-variant": "#8c0053", "inverse-primary": "#d2bbff",
                        "on-surface-variant": "#4a4455", "inverse-on-surface": "#f6eefc",
                        "tertiary-fixed-dim": "#ffb0cd", "primary-container": "#7c3aed",
                        "background": "#fef7ff", "on-tertiary-container": "#ffdde7",
                        "on-primary": "#ffffff", "surface-container-lowest": "#ffffff",
                        "secondary-fixed-dim": "#c3c0ff", "tertiary-fixed": "#ffd9e4",
                        "on-error": "#ffffff", "surface-container-high": "#ede5f4",
                        "on-tertiary": "#ffffff", "primary": "#630ed4",
                        "on-tertiary-fixed": "#3e0022", "surface-container-low": "#f9f1ff",
                        "surface-variant": "#e8dfee", "primary-fixed": "#eaddff",
                        "on-primary-fixed-variant": "#5a00c6", "surface-dim": "#dfd7e6",
                        "on-error-container": "#93000a", "on-secondary": "#ffffff",
                        "tertiary-container": "#bf2076", "surface-bright": "#fef7ff",
                        "surface-tint": "#732ee4", "on-secondary-fixed-variant": "#3323cc",
                        "secondary": "#4b41e1", "error-container": "#ffdad6",
                        "on-primary-container": "#ede0ff", "primary-fixed-dim": "#d2bbff",
                        "on-surface": "#1d1a24", "inverse-surface": "#332f39",
                        "on-secondary-container": "#fffbff", "on-secondary-fixed": "#0f0069",
                        "secondary-fixed": "#e2dfff", "secondary-container": "#645efb",
                        "on-background": "#1d1a24", "error": "#ba1a1a",
                        "outline-variant": "#ccc3d8", "surface-container-highest": "#e8dfee",
                        "on-primary-fixed": "#25005a", "surface": "#fef7ff", "outline": "#7b7487"
                    },
                    "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                    "fontFamily": { "headline": ["Public Sans"], "body": ["Public Sans"], "label": ["Public Sans"] }
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Public Sans', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }

        /* Toast */
        #toast {
            transition: opacity 0.4s ease, transform 0.4s ease;
        }
        #toast.hide {
            opacity: 0;
            transform: translateY(-14px);
            pointer-events: none;
        }
    </style>
</head>

<body class="bg-background text-on-background min-h-screen">

    <!-- ── Toast ── -->
    <div id="toast" class="hide fixed top-6 left-1/2 -translate-x-1/2 z-[9999] px-5 py-3 rounded-2xl shadow-xl text-sm font-bold flex items-center gap-2 bg-on-surface text-surface">
        <span id="toast-icon" class="material-symbols-outlined text-base">check_circle</span>
        <span id="toast-msg">Done</span>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-black/40 z-40 md:hidden hidden transition-opacity" id="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <?= author_slidebar('notifications') ?>
    <?= author_navbar(); ?>

    <!-- Main Content -->
    <main class="md:pl-64 pt-20 min-h-screen">
        <div class="p-4 sm:p-6 md:p-10 max-w-5xl mx-auto">

            <!-- ── Page Header ── -->
            <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-black text-on-surface tracking-tighter -mb-1">Notifications</h2>
                    <p class="text-on-surface-variant font-medium mt-2 text-sm sm:text-base">Stay updated on your readers' actions and platform updates.</p>
                </div>
                <!-- Action button -->
                <div class="flex gap-2">
                    <button id="mark-all-read-btn" onclick="markAllNotificationsRead()" class="px-4 py-2.5 bg-primary text-white text-xs font-bold rounded-xl shadow-lg shadow-primary/20 hover:opacity-90 transition-all active:scale-95 flex items-center gap-1.5 <?= $unread_count > 0 ? '' : 'hidden' ?>">
                        <span class="material-symbols-outlined text-base">done_all</span> Mark all read
                    </button>
                </div>
            </div>

            <!-- ── Stats Bento Grid ── -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <div class="bg-surface-container-low p-5 rounded-3xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-primary">notifications</span>
                    </div>
                    <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Total Notifications</p>
                    <p id="total-notif-count" class="text-4xl font-black text-on-surface"><?= $total_count ?></p>
                </div>
                <div class="bg-surface-container-low p-5 rounded-3xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-[#ba1a1a]">mark_chat_unread</span>
                    </div>
                    <p class="text-xs font-bold text-[#ba1a1a] uppercase tracking-widest mb-1">Unread</p>
                    <p id="unread-notif-count" class="text-4xl font-black text-on-surface"><?= $unread_count ?></p>
                </div>
            </div>

            <!-- ── Notifications List ── -->
            <div id="notificationsList" class="bg-surface-container-lowest rounded-3xl p-2 md:p-3 border border-outline-variant/10 divide-y divide-outline-variant/10">
                <!-- Notifications render dynamically via JS -->
            </div>

        </div>
    </main>

    <script>
        // Server injected PHP notifications data
        const PHP_NOTIFICATIONS = <?= json_encode($notifications, JSON_UNESCAPED_UNICODE) ?>;
        let NOTIFICATIONS = [...PHP_NOTIFICATIONS];

        document.addEventListener('DOMContentLoaded', () => {
            renderNotifications();
        });

        /* ── Sidebar ── */
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

        /* ── Toast ── */
        let toastTimer;
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            const icon  = document.getElementById('toast-icon');
            const text  = document.getElementById('toast-msg');
            const map   = { success: ['check_circle', 'bg-on-surface text-surface'], error: ['error', 'bg-error text-on-error'], info: ['info', 'bg-primary text-on-primary'] };
            const [ic, cls] = map[type] || map.success;
            icon.textContent = ic;
            text.textContent = msg;
            toast.className = 'fixed top-6 left-1/2 -translate-x-1/2 z-[9999] px-5 py-3 rounded-2xl shadow-xl text-sm font-bold flex items-center gap-2 ' + cls;
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => toast.classList.add('hide'), 3200);
        }

        /* ── Render Notifications list ── */
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
                <div id="notif-item-${n.id}" class="flex items-start gap-3 md:gap-4 p-4 md:p-5 hover:bg-surface-container-low transition-all group relative rounded-2xl ${n.is_read ? 'opacity-70' : 'bg-primary/5'}">
                    <div class="w-9 h-9 md:w-10 md:h-10 rounded-full flex items-center justify-center shrink-0" style="background:${colorBg}">
                        <span class="material-symbols-outlined text-base md:text-lg" style="color:${colorText}">${icon}</span>
                    </div>
                    <div class="flex-1 min-w-0 pr-8">
                        <div class="font-bold text-sm text-on-surface flex items-center gap-2">
                            ${n.title}
                            ${!n.is_read ? '<span class="w-2 h-2 bg-primary rounded-full inline-block shrink-0" id="unread-dot-' + n.id + '"></span>' : ''}
                        </div>
                        <p class="text-on-surface-variant text-xs mt-1 leading-relaxed">${n.message}</p>
                        <div class="text-[10px] text-on-surface-variant/60 mt-1 font-medium">${n.date}</div>
                    </div>
                    <button onclick="deleteNotification(${n.id})" class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container text-on-surface-variant" title="Delete notification">
                        <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                </div>`;
            }).join('');
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        /* ── Action: Mark all notifications as read ── */
        async function markAllNotificationsRead() {
            const btn = document.getElementById('mark-all-read-btn');
            btn.disabled = true;
            try {
                const fd = new FormData();
                fd.append('action', 'mark_notifications_read');
                const res = await fetch('notifications.php', { method: 'POST', body: fd });
                const json = await res.json();
                if (json.success) {
                    NOTIFICATIONS.forEach(n => n.is_read = 1);
                    
                    // Hide header buttons/badges
                    document.getElementById('mark-all-read-btn').classList.add('hidden');
                    document.getElementById('unread-notif-count').textContent = '0';
                    
                    // Hide sidebar elements if they exist
                    const sidebarBadge = document.getElementById('notif-badge-sidebar');
                    if (sidebarBadge) sidebarBadge.classList.add('hidden');
                    
                    const dotNavbar = document.getElementById('notif-dot-navbar');
                    if (dotNavbar) dotNavbar.classList.add('hidden');
                    
                    renderNotifications();
                    showToast('All notifications marked as read');
                } else {
                    showToast('Failed to update notifications', 'error');
                }
            } catch(e) {
                console.error(e);
                showToast('An error occurred', 'error');
            } finally {
                btn.disabled = false;
            }
        }

        /* ── Action: Delete a single notification ── */
        async function deleteNotification(id) {
            const item = document.getElementById('notif-item-' + id);
            if (!item) return;
            
            try {
                const fd = new FormData();
                fd.append('action', 'delete_notification');
                fd.append('id', id);
                const res = await fetch('notifications.php', { method: 'POST', body: fd });
                const json = await res.json();
                if (json.success) {
                    const deletedNotif = NOTIFICATIONS.find(n => n.id === id);
                    const wasUnread = deletedNotif && !deletedNotif.is_read;
                    
                    NOTIFICATIONS = NOTIFICATIONS.filter(n => n.id !== id);
                    
                    // Update stats
                    const totalCountEl = document.getElementById('total-notif-count');
                    totalCountEl.textContent = parseInt(totalCountEl.textContent) - 1;
                    
                    if (wasUnread) {
                        const unreadCountEl = document.getElementById('unread-notif-count');
                        const newUnread = Math.max(0, parseInt(unreadCountEl.textContent) - 1);
                        unreadCountEl.textContent = newUnread;
                        
                        // Update navigation badges
                        const sidebarBadge = document.getElementById('notif-badge-sidebar');
                        if (sidebarBadge) {
                            if (newUnread <= 0) {
                                sidebarBadge.classList.add('hidden');
                            } else {
                                sidebarBadge.textContent = newUnread;
                            }
                        }
                        
                        const dotNavbar = document.getElementById('notif-dot-navbar');
                        if (dotNavbar && newUnread <= 0) {
                            dotNavbar.classList.add('hidden');
                        }
                        
                        if (newUnread <= 0) {
                            document.getElementById('mark-all-read-btn').classList.add('hidden');
                        }
                    }
                    
                    // Smooth transition and remove
                    item.style.transition = 'all 0.3s ease';
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        item.remove();
                        if (!NOTIFICATIONS.length) {
                            renderNotifications();
                        }
                    }, 300);
                    
                    showToast('Notification deleted');
                } else {
                    showToast('Failed to delete notification', 'error');
                }
            } catch(e) {
                console.error(e);
                showToast('An error occurred', 'error');
            }
        }
    </script>
</body>
</html>

<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAuthor();
include BASE_PATH . 'include/db.php';
include BASE_PATH . 'include/author_nav_sidebar.php';
//include BASE_PATH . 'include/data_fetch.php';

$table = 'comments';
$limit = 6;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$id = $_SESSION['user_id'];

// Search support
$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, trim($_GET['q'])) : '';
$search_clause = $search ? "AND c.comment LIKE '%$search%'" : '';

// Filter: all | replied | pending
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$filter_clause = '';
if ($filter === 'replied') {
    $filter_clause = "AND c.parent_id IS NULL AND EXISTS(SELECT 1 FROM comments r WHERE r.parent_id = c.id AND r.user_id = $id)";
} elseif ($filter === 'pending') {
    $filter_clause = "AND c.parent_id IS NULL AND NOT EXISTS(SELECT 1 FROM comments r WHERE r.parent_id = c.id AND r.user_id = $id)";
} else {
    // Only top-level comments (parent_id IS NULL) in main listing
    $filter_clause = "AND c.parent_id IS NULL";
}

$query = "SELECT c.*, p.title AS post_title, p.slug AS post_slug
          FROM $table c
          INNER JOIN posts p ON c.post_id = p.id
          WHERE p.author_id = $id $filter_clause $search_clause
          ORDER BY c.created_at DESC
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
$data = mysqli_num_rows($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$total_query = "SELECT COUNT(*) as total FROM $table c INNER JOIN posts p ON c.post_id = p.id WHERE p.author_id = $id AND c.parent_id IS NULL $search_clause";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];

// Stats
$stats_query = "SELECT
    COUNT(DISTINCT c.id) as total_comments,
    COUNT(DISTINCT CASE WHEN r.id IS NOT NULL THEN c.id END) as replied_count,
    COUNT(DISTINCT CASE WHEN r.id IS NULL THEN c.id END) as pending_count
    FROM comments c
    INNER JOIN posts p ON c.post_id = p.id
    LEFT JOIN comments r ON r.parent_id = c.id AND r.user_id = $id
    WHERE p.author_id = $id AND c.parent_id IS NULL";
$stats = mysqli_fetch_assoc(mysqli_query($conn, $stats_query));

include BASE_PATH . 'include/pagination.php';
$pagination = paginate($data, $total_records, $page, $offset, $limit);
$data24 = $pagination['data'];
$total_pages = $pagination['total_pages'];
$page = $pagination['current_page'];
$limit = $pagination['limit'];
$total_records = $pagination['total_records'];
$offset = $pagination['offset'];

// Helper: fetch replies for a comment
function getReplies($conn, $comment_id) {
    $q = "SELECT c.*, u.name, u.profile_image FROM comments c
          LEFT JOIN users u ON c.user_id = u.id
          WHERE c.parent_id = '$comment_id'
          ORDER BY c.created_at ASC";
    $r = mysqli_query($conn, $q);
    return $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];
}
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <link rel="icon" type="image/png" href="<?php echo defined('BASE_URL') ? BASE_URL : '/BlogFusion/'; ?>upload/site_image/logo2.png" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Comments - Luminous Editor</title>
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

        /* Reply panel slide animation */
        .reply-panel {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease;
        }
        .reply-panel.open {
            max-height: 800px;
        }

        /* Delete modal */
        #deleteModal.hidden { display: none; }

        /* Filter tab active */
        .filter-tab.active {
            background: #630ed4;
            color: white;
        }
    </style>
</head>

<body class="bg-background text-on-background min-h-screen">



    <!-- ── Delete Confirmation Modal ── -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-3xl shadow-2xl w-full max-w-sm p-8 text-center">
            <div class="w-16 h-16 bg-error-container rounded-full flex items-center justify-center mx-auto mb-5">
                <span class="material-symbols-outlined text-3xl text-error">delete_forever</span>
            </div>
            <h3 class="text-xl font-black text-on-surface mb-2">Delete Comment?</h3>
            <p class="text-sm text-on-surface-variant mb-8">This will permanently remove this comment and all its replies.</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 py-3 rounded-xl bg-surface-container font-bold text-on-surface-variant hover:bg-surface-container-high transition-colors text-sm">
                    Cancel
                </button>
                <button id="deleteConfirmBtn" onclick="executeDelete()" class="flex-1 py-3 rounded-xl bg-error text-on-error font-bold hover:opacity-90 transition-opacity text-sm flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-base">delete</span> Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-black/40 z-40 md:hidden hidden transition-opacity" id="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <?= author_slidebar('comments') ?>
    <?= author_navbar() ?>

    <!-- Main Content -->
    <main class="md:pl-64 pt-16 min-h-screen">
        <div class="p-4 sm:p-6 md:p-10 max-w-7xl mx-auto">

            <!-- ── Page Header ── -->
            <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-black text-on-surface tracking-tighter -mb-1">Comments</h2>
                    <p class="text-on-surface-variant font-medium mt-2 text-sm sm:text-base">Manage and respond to your audience's engagement.</p>
                </div>
                <!-- Filter tabs -->
                <div class="flex gap-2 flex-wrap">
                    <a href="?filter=all<?= $search ? '&q='.urlencode($search) : '' ?>"
                       class="filter-tab px-4 py-2 rounded-xl bg-surface-container-high text-on-surface-variant text-xs font-bold hover:bg-surface-variant transition-colors <?= $filter === 'all' ? 'active' : '' ?>">
                        All (<?= $stats['total_comments'] ?? 0 ?>)
                    </a>
                    <a href="?filter=pending<?= $search ? '&q='.urlencode($search) : '' ?>"
                       class="filter-tab px-4 py-2 rounded-xl bg-surface-container-high text-on-surface-variant text-xs font-bold hover:bg-surface-variant transition-colors <?= $filter === 'pending' ? 'active' : '' ?>">
                        Pending (<?= $stats['pending_count'] ?? 0 ?>)
                    </a>
                    <a href="?filter=replied<?= $search ? '&q='.urlencode($search) : '' ?>"
                       class="filter-tab px-4 py-2 rounded-xl bg-surface-container-high text-on-surface-variant text-xs font-bold hover:bg-surface-variant transition-colors <?= $filter === 'replied' ? 'active' : '' ?>">
                        Replied (<?= $stats['replied_count'] ?? 0 ?>)
                    </a>
                </div>
            </div>

            <!-- ── Stats ── -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-surface-container-low p-5 rounded-3xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-primary">chat_bubble</span>
                    </div>
                    <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Total Comments</p>
                    <p class="text-4xl font-black text-on-surface"><?= $stats['total_comments'] ?? 0 ?></p>
                </div>
                <div class="bg-surface-container-low p-5 rounded-3xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-green-600">mark_chat_read</span>
                    </div>
                    <p class="text-xs font-bold text-green-600 uppercase tracking-widest mb-1">Replied</p>
                    <p class="text-4xl font-black text-on-surface"><?= $stats['replied_count'] ?? 0 ?></p>
                </div>
                <div class="bg-surface-container-highest p-5 rounded-3xl relative overflow-hidden group border border-primary/5">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-tertiary">pending</span>
                    </div>
                    <p class="text-xs font-bold text-tertiary uppercase tracking-widest mb-1">Awaiting Reply</p>
                    <p class="text-4xl font-black text-on-surface"><?= $stats['pending_count'] ?? 0 ?></p>
                </div>
            </div>

            <!-- ── Search ── -->
            <form method="GET" action="" class="mb-6 relative">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">search</span>
                <input name="q" value="<?= htmlspecialchars($search) ?>"
                    class="w-full bg-surface-container-low border-none rounded-2xl py-3.5 pl-12 pr-12 text-sm focus:ring-2 focus:ring-primary/20 outline-none"
                    placeholder="Search comments..." type="text" />
                <?php if ($search): ?>
                <a href="?filter=<?= $filter ?>" class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-error">
                    <span class="material-symbols-outlined text-lg">close</span>
                </a>
                <?php endif; ?>
            </form>

            <!-- ── Comments List ── -->
            <div class="space-y-4">

                <?php if (empty($data24)): ?>
                <div class="bg-surface-container-lowest rounded-3xl py-20 flex flex-col items-center text-center px-6">
                    <div class="w-20 h-20 bg-surface-container rounded-full flex items-center justify-center mb-5">
                        <span class="material-symbols-outlined text-4xl text-on-surface-variant opacity-40">forum</span>
                    </div>
                    <h3 class="text-lg font-bold text-on-surface mb-2">
                        <?= $search ? 'No comments found for "'.htmlspecialchars($search).'"' : 'No comments yet' ?>
                    </h3>
                    <p class="text-sm text-on-surface-variant">
                        <?= $search ? 'Try a different keyword.' : 'Comments on your posts will appear here.' ?>
                    </p>
                </div>

                <?php else: ?>
                <?php foreach ($data24 as $key => $row):
                    $commenter = data_featch($conn, $row['user_id']);
                    $commenter_data = $commenter['data'];
                    $replies = getReplies($conn, $row['id']);
                    $has_author_reply = false;
                    foreach ($replies as $r) {
                        if ($r['user_id'] == $id) { $has_author_reply = true; break; }
                    }
                ?>

                <!-- ── Comment Card ── -->
                <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 overflow-hidden shadow-sm hover:shadow-md transition-shadow" id="comment-<?= $row['id'] ?>">

                    <!-- Main comment row -->
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                            <!-- Avatar + user info -->
                            <div class="flex items-center gap-3 sm:gap-0 sm:flex-col sm:items-center sm:w-16 shrink-0">
                                <img class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl object-cover shadow-sm bg-surface-container"
                                    src="<?= BASE_URL ?><?= htmlspecialchars($commenter_data['profile_image'] ?? 'upload/profile-images/default.png') ?>"
                                    onerror="this.src='https://placehold.co/48x48/e8dfee/630ed4?text=U'"
                                    alt="avatar">
                                <div class="sm:hidden">
                                    <p class="font-bold text-sm text-on-surface"><?= htmlspecialchars($commenter_data['name'] ?? 'User') ?></p>
                                    <p class="text-xs text-on-surface-variant"><?= date("M d", strtotime($row['created_at'])) ?></p>
                                </div>
                            </div>

                            <!-- Comment body -->
                            <div class="flex-1 min-w-0">
                                <!-- User + date (desktop) -->
                                <div class="hidden sm:flex items-center gap-3 mb-2 flex-wrap">
                                    <span class="font-bold text-sm text-on-surface"><?= htmlspecialchars($commenter_data['name'] ?? 'User') ?></span>
                                    <span class="text-xs text-on-surface-variant"><?= htmlspecialchars($commenter_data['email'] ?? '') ?></span>
                                    <span class="text-xs text-outline">•</span>
                                    <span class="text-xs text-on-surface-variant"><?= date("M d, Y · H:i", strtotime($row['created_at'])) ?></span>
                                    <?php if ($has_author_reply): ?>
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs" style="font-size:12px">check</span> Replied
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Comment text -->
                                <p class="text-sm text-on-surface-variant leading-relaxed"><?= htmlspecialchars($row['comment']) ?></p>

                                <!-- Post link -->
                                <div class="mt-3 inline-flex items-center gap-2 bg-surface-container px-3 py-1.5 rounded-xl text-xs font-semibold text-primary hover:bg-surface-container-high transition-colors cursor-pointer"
                                     onclick="window.open('<?= BASE_URL ?>redirect-post.php?id=<?= $row['post_id'] ?>', '_blank')">
                                    <span class="material-symbols-outlined text-xs" style="font-size:14px">article</span>
                                    <span class="truncate max-w-[180px] sm:max-w-xs"><?= htmlspecialchars($row['post_title']) ?></span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex sm:flex-col gap-2 shrink-0 justify-end sm:justify-start">
                                <!-- Reply toggle -->
                                <button onclick="toggleReplyPanel(<?= $row['id'] ?>)"
                                    id="reply-btn-<?= $row['id'] ?>"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition-all
                                           <?= $has_author_reply ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-primary/10 text-primary hover:bg-primary hover:text-white' ?>">
                                    <span class="material-symbols-outlined text-base" style="font-size:16px">reply</span>
                                    <span class="hidden sm:inline"><?= $has_author_reply ? 'Replied' : 'Reply' ?></span>
                                </button>
                                <!-- Delete -->
                                <button onclick="openDeleteModal(<?= $row['id'] ?>)"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-tertiary/10 text-tertiary hover:bg-tertiary hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-base" style="font-size:16px">delete</span>
                                    <span class="hidden sm:inline">Delete</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ── Replies Section ── -->
                    <div class="reply-panel" id="reply-panel-<?= $row['id'] ?>">
                        <div class="border-t border-outline-variant/10 bg-surface-container-low/50 px-5 sm:px-8 py-5">

                            <!-- Existing replies -->
                            <?php if (!empty($replies)): ?>
                            <div class="space-y-4 mb-5" id="replies-list-<?= $row['id'] ?>">
                                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-3">
                                    <?= count($replies) ?> <?= count($replies) === 1 ? 'Reply' : 'Replies' ?>
                                </p>
                                <?php foreach ($replies as $reply):
                                    $reply_user = data_featch($conn, $reply['user_id']);
                                    $reply_user_data = $reply_user['data'];
                                    $is_me = $reply['user_id'] == $id;
                                ?>
                                <div class="flex items-start gap-3" id="reply-item-<?= $reply['id'] ?>">
                                    <div class="shrink-0 w-1 self-stretch bg-<?= $is_me ? 'primary' : 'outline-variant' ?> rounded-full opacity-30"></div>
                                    <img class="w-8 h-8 rounded-xl object-cover bg-surface-container shrink-0"
                                        src="<?= BASE_URL ?><?= htmlspecialchars($reply_user_data['profile_image'] ?? 'upload/profile-images/default.png') ?>"
                                        onerror="this.src='https://placehold.co/32x32/e8dfee/630ed4?text=U'"
                                        alt="avatar">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                                            <span class="font-bold text-xs text-on-surface"><?= htmlspecialchars($reply_user_data['name'] ?? 'User') ?></span>
                                            <?php if ($is_me): ?>
                                            <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] font-bold rounded-full">You</span>
                                            <?php endif; ?>
                                            <span class="text-[11px] text-on-surface-variant"><?= date("M d, Y · H:i", strtotime($reply['created_at'])) ?></span>
                                        </div>
                                        <p class="text-sm text-on-surface-variant leading-relaxed"><?= htmlspecialchars($reply['comment']) ?></p>
                                    </div>
                                    <?php if ($is_me): ?>
                                    <button onclick="deleteReply(<?= $reply['id'] ?>, <?= $row['id'] ?>)"
                                        class="shrink-0 p-1.5 rounded-lg text-on-surface-variant hover:bg-error-container hover:text-error transition-all"
                                        title="Delete reply">
                                        <span class="material-symbols-outlined text-sm" style="font-size:16px">close</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div id="replies-list-<?= $row['id'] ?>" class="mb-4"></div>
                            <?php endif; ?>

                            <!-- Reply compose box -->
                            <div class="flex items-start gap-3">
                                <img class="w-8 h-8 rounded-xl object-cover bg-surface-container shrink-0 mt-1"
                                    src="<?= BASE_URL ?><?php
                                        $me = data_featch($conn, $id);
                                        echo htmlspecialchars($me['data']['profile_image'] ?? 'upload/profile-images/default.png');
                                    ?>"
                                    onerror="this.src='https://placehold.co/32x32/7c3aed/ffffff?text=Me'"
                                    alt="you">
                                <div class="flex-1 relative">
                                    <textarea
                                        id="reply-text-<?= $row['id'] ?>"
                                        placeholder="Write a reply..."
                                        rows="2"
                                        class="w-full bg-surface-container-lowest border border-outline-variant/30 rounded-2xl px-4 py-3 text-sm resize-none focus:ring-2 focus:ring-primary/20 focus:border-primary/30 outline-none transition-all"
                                        oninput="autoResize(this)"
                                    ></textarea>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-xs text-on-surface-variant opacity-60">Reply publicly as you</span>
                                        <button
                                            onclick="submitReply(<?= $row['id'] ?>)"
                                            id="send-btn-<?= $row['id'] ?>"
                                            class="flex items-center gap-1.5 px-4 py-2 bg-primary text-on-primary rounded-xl text-xs font-bold hover:opacity-90 transition-all active:scale-95 disabled:opacity-50">
                                            <span class="material-symbols-outlined text-sm" style="font-size:16px">send</span>
                                            Send Reply
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php endforeach; ?>

                <!-- Pagination -->
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/10 mt-2">
                    <?php pagination_links($total_pages, $limit, $total_records, $offset, 'comments'); ?>
                </div>
                <?php endif; ?>

            </div><!-- /space-y-4 -->
        </div>
    </main>

    <script>
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

        /* ── Reply Panel ── */
        function toggleReplyPanel(commentId) {
            const panel = document.getElementById('reply-panel-' + commentId);
            const isOpen = panel.classList.contains('open');
            // close all
            document.querySelectorAll('.reply-panel').forEach(p => p.classList.remove('open'));
            if (!isOpen) {
                panel.classList.add('open');
                // focus textarea
                setTimeout(() => {
                    const ta = document.getElementById('reply-text-' + commentId);
                    if (ta) ta.focus();
                }, 350);
            }
        }

        /* ── Auto-resize textarea ── */
        function autoResize(el) {
            el.style.height = 'auto';
            el.style.height = (el.scrollHeight) + 'px';
        }

        /* ── Submit Reply (AJAX) ── */
        function submitReply(commentId) {
            const ta    = document.getElementById('reply-text-' + commentId);
            const btn   = document.getElementById('send-btn-' + commentId);
            const text  = ta.value.trim();
            if (!text) { showToast('Please write a reply first.', 'error'); ta.focus(); return; }

            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin" style="font-size:16px">autorenew</span> Sending…';

            fetch('<?= BASE_URL ?>actions/author_comment_reply.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'parent_id=' + commentId + '&comment=' + encodeURIComponent(text)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Inject new reply into DOM
                    const list = document.getElementById('replies-list-' + commentId);
                    list.innerHTML += buildReplyHTML(data.reply);
                    ta.value = '';
                    ta.style.height = 'auto';

                    // Update reply button
                    const replyBtn = document.getElementById('reply-btn-' + commentId);
                    replyBtn.className = replyBtn.className
                        .replace('bg-primary/10 text-primary hover:bg-primary hover:text-white', 'bg-green-100 text-green-700 hover:bg-green-200');
                    replyBtn.querySelector('span.hidden').textContent = 'Replied';

                    showToast('Reply posted!', 'success');
                } else {
                    showToast(data.message || 'Failed to post reply', 'error');
                }
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined text-sm" style="font-size:16px">send</span> Send Reply';
            })
            .catch(() => {
                showToast('Network error. Please try again.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined text-sm" style="font-size:16px">send</span> Send Reply';
            });
        }

        function buildReplyHTML(reply) {
            return `
            <div class="flex items-start gap-3 mt-4" id="reply-item-${reply.id}">
                <div class="shrink-0 w-1 self-stretch bg-primary rounded-full opacity-30"></div>
                <img class="w-8 h-8 rounded-xl object-cover bg-surface-container shrink-0"
                    src="${reply.profile_image}"
                    onerror="this.src='https://placehold.co/32x32/7c3aed/ffffff?text=Me'"
                    alt="you">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <span class="font-bold text-xs text-on-surface">${reply.name}</span>
                        <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] font-bold rounded-full">You</span>
                        <span class="text-[11px] text-on-surface-variant">${reply.created_at}</span>
                    </div>
                    <p class="text-sm text-on-surface-variant leading-relaxed">${escHtml(reply.comment)}</p>
                </div>
                <button onclick="deleteReply(${reply.id}, ${reply.parent_id})"
                    class="shrink-0 p-1.5 rounded-lg text-on-surface-variant hover:bg-error-container hover:text-error transition-all"
                    title="Delete reply">
                    <span class="material-symbols-outlined text-sm" style="font-size:16px">close</span>
                </button>
            </div>`;
        }

        function escHtml(str) {
            return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        /* ── Delete Reply (AJAX) ── */
        function deleteReply(replyId, commentId) {
            if (!confirm('Delete this reply?')) return;
            fetch('<?= BASE_URL ?>actions/author.php?btn=comment&id=' + replyId, { method: 'GET' })
            .then(r => {
                // author.php uses echo <script> style — we check via status
                const el = document.getElementById('reply-item-' + replyId);
                if (el) { el.style.opacity='0'; el.style.transition='opacity 0.3s'; setTimeout(() => el.remove(), 300); }
                showToast('Reply deleted', 'info');
            });
        }

        /* ── Delete Comment Modal ── */
        let pendingDeleteId = null;
        function openDeleteModal(commentId) {
            pendingDeleteId = commentId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            pendingDeleteId = null;
        }
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });

        function executeDelete() {
            if (!pendingDeleteId) return;
            const btn = document.getElementById('deleteConfirmBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined text-base animate-spin">autorenew</span> Deleting…';

            fetch('<?= BASE_URL ?>actions/delete_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + pendingDeleteId
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById('comment-' + pendingDeleteId);
                    if (card) {
                        card.style.transition = 'all 0.4s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.97)';
                        setTimeout(() => card.remove(), 400);
                    }
                    showToast('Comment deleted', 'info');
                } else {
                    showToast(data.message || 'Failed to delete', 'error');
                }
                closeDeleteModal();
            })
            .catch(() => {
                showToast('Network error. Please try again.', 'error');
                closeDeleteModal();
            });
        }
    </script>
</body>
</html>
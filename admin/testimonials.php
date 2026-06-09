<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAdmin();
include BASE_PATH . 'include/db.php';
include_once BASE_PATH . 'include/functions.php';

global $conn;

// Handle approve / reject / delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'approve' && $id > 0) {
        $ok = $conn->query("UPDATE testimonials SET is_approved = 1 WHERE id = $id");
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }
    if ($action === 'reject' && $id > 0) {
        $ok = $conn->query("UPDATE testimonials SET is_approved = 0 WHERE id = $id");
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }
    if ($action === 'delete' && $id > 0) {
        $ok = $conn->query("DELETE FROM testimonials WHERE id = $id");
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }
    echo json_encode(['success' => false, 'error' => 'Unknown action']);
    exit;
}

// Fetch filter
$filter = $_GET['filter'] ?? 'all';
$where  = '';
if ($filter === 'approved') $where = 'WHERE is_approved = 1';
if ($filter === 'pending')  $where = 'WHERE is_approved = 0';

$result       = $conn->query("SELECT * FROM testimonials $where ORDER BY created_at DESC");
$testimonials = [];
if ($result) {
    while ($row = $result->fetch_assoc()) $testimonials[] = $row;
}

// Counts
$total_all      = (int)$conn->query("SELECT COUNT(*) c FROM testimonials")->fetch_assoc()['c'];
$total_approved = (int)$conn->query("SELECT COUNT(*) c FROM testimonials WHERE is_approved = 1")->fetch_assoc()['c'];
$total_pending  = (int)$conn->query("SELECT COUNT(*) c FROM testimonials WHERE is_approved = 0")->fetch_assoc()['c'];
?><!DOCTYPE html>
<html lang="en" class="light">
<head>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>upload/site_image/logo2.png" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Testimonials Manager — BlogFusion Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {
                colors: { primary: '#7C3AED', accent: '#EC4899', 'bg-dark': '#111827', 'card-dark': '#1F2937' },
                fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] }
            }}
        };
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .card-fade { animation: fadeIn .3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
    </style>
</head>
<body class="bg-gray-50 dark:bg-bg-dark text-gray-900 dark:text-gray-100 min-h-screen">

<?php include BASE_PATH . 'include/admin_nav_sidebar.php'; ?>

<div class="lg:pl-64">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tight">Testimonials Manager</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Review, approve, and manage reader testimonials from the landing page.</p>
            </div>
            <a href="<?php echo site_url('index.php#write-review'); ?>" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white"
                style="background:linear-gradient(135deg,#7C3AED,#4F46E5);">
                <span class="material-symbols-outlined text-sm">open_in_new</span>
                View Landing Page
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-white dark:bg-card-dark rounded-2xl p-5 border border-gray-100 dark:border-gray-700">
                <div class="text-3xl font-black text-gray-900 dark:text-white"><?php echo $total_all; ?></div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Reviews</div>
            </div>
            <div class="bg-white dark:bg-card-dark rounded-2xl p-5 border border-gray-100 dark:border-gray-700">
                <div class="text-3xl font-black text-green-600"><?php echo $total_approved; ?></div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Published</div>
            </div>
            <div class="bg-white dark:bg-card-dark rounded-2xl p-5 border border-gray-100 dark:border-gray-700">
                <div class="text-3xl font-black text-yellow-500"><?php echo $total_pending; ?></div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                    Pending
                    <?php if ($total_pending > 0): ?><span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse ml-1"></span><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Filter tabs -->
        <div class="flex gap-2 mb-6 bg-white dark:bg-card-dark rounded-2xl p-1.5 border border-gray-100 dark:border-gray-700 w-fit">
            <?php
            $tabs = [
                ['label' => 'All',      'value' => 'all',      'count' => $total_all],
                ['label' => 'Approved', 'value' => 'approved', 'count' => $total_approved],
                ['label' => 'Pending',  'value' => 'pending',  'count' => $total_pending],
            ];
            foreach ($tabs as $tab): ?>
            <a href="?filter=<?php echo $tab['value']; ?>"
                class="px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 transition-all <?php echo $filter === $tab['value'] ? 'bg-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5'; ?>">
                <?php echo $tab['label']; ?>
                <span class="text-xs font-black px-1.5 py-0.5 rounded-md <?php echo $filter === $tab['value'] ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-700'; ?>">
                    <?php echo $tab['count']; ?>
                </span>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Testimonials table -->
        <?php if (empty($testimonials)): ?>
        <div class="text-center py-16 bg-white dark:bg-card-dark rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
            <span class="material-symbols-outlined text-5xl text-gray-300 dark:text-gray-600 block mb-3">rate_review</span>
            <p class="text-gray-500 dark:text-gray-400 font-medium">No testimonials found for this filter.</p>
        </div>
        <?php else: ?>
        <div class="space-y-4" id="testi-list">
            <?php foreach ($testimonials as $t):
                $initials = '';
                foreach (explode(' ', trim($t['name'])) as $word) $initials .= mb_strtoupper(mb_substr($word, 0, 1));
                $initials = mb_substr($initials, 0, 2);
                $approved = (bool)$t['is_approved'];
            ?>
            <div class="card-fade bg-white dark:bg-card-dark rounded-2xl border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row gap-4"
                id="testi-card-<?php echo $t['id']; ?>">

                <!-- Avatar -->
                <div class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center text-white text-sm font-black"
                    style="background: linear-gradient(135deg, <?php echo escape_html($t['avatar_color'] ?? '#7C3AED'); ?>, #4F46E5);">
                    <?php echo escape_html($initials); ?>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-3 mb-1">
                        <span class="font-bold text-gray-900 dark:text-white"><?php echo escape_html($t['name']); ?></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400"><?php echo escape_html($t['role']); ?></span>
                        <!-- Stars -->
                        <span class="text-yellow-400 text-sm tracking-tight">
                            <?php echo str_repeat('★', (int)$t['rating']) . str_repeat('☆', 5 - (int)$t['rating']); ?>
                        </span>
                        <!-- Status badge -->
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold <?php echo $approved ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'; ?>">
                            <?php echo $approved ? '✓ Published' : '⏳ Pending'; ?>
                        </span>
                        <span class="text-[11px] text-gray-400 ml-auto"><?php echo date('d M Y, H:i', strtotime($t['created_at'])); ?></span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed italic">
                        "<?php echo escape_html($t['review']); ?>"
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex sm:flex-col gap-2 sm:items-end shrink-0">
                    <?php if (!$approved): ?>
                    <button onclick="testiAction('approve', <?php echo $t['id']; ?>)"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/40 transition-colors">
                        <span class="material-symbols-outlined text-sm">check_circle</span> Approve
                    </button>
                    <?php else: ?>
                    <button onclick="testiAction('reject', <?php echo $t['id']; ?>)"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-yellow-50 text-yellow-700 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/40 transition-colors">
                        <span class="material-symbols-outlined text-sm">unpublished</span> Unpublish
                    </button>
                    <?php endif; ?>
                    <button onclick="testiAction('delete', <?php echo $t['id']; ?>)"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors">
                        <span class="material-symbols-outlined text-sm">delete</span> Delete
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Toast -->
<?php inject_project_toast(); ?>

<script>
async function testiAction(action, id) {
    if (action === 'delete' && !confirm('Delete this review permanently?')) return;

    const body = new FormData();
    body.append('action', action);
    body.append('id', id);

    try {
        const res  = await fetch(window.location.pathname, { method: 'POST', body });
        const data = await res.json();

        if (data.success) {
            if (action === 'delete') {
                const card = document.getElementById('testi-card-' + id);
                if (card) {
                    card.style.transition = 'opacity .3s, transform .3s';
                    card.style.opacity = '0';
                    card.style.transform = 'translateX(20px)';
                    setTimeout(() => card.remove(), 300);
                }
            } else {
                // Reload to reflect badge changes
                setTimeout(() => location.reload(), 300);
            }
            const msgs = { approve: '✅ Review approved and published.', reject: '⚠️ Review unpublished.', delete: '🗑️ Review deleted.' };
            if (typeof window.showToast === 'function') window.showToast(msgs[action], action === 'delete' ? 'warning' : 'success');
        } else {
            if (typeof window.showToast === 'function') window.showToast('❌ Action failed. Try again.', 'error');
        }
    } catch {
        if (typeof window.showToast === 'function') window.showToast('❌ Network error.', 'error');
    }
}
</script>

</body>
</html>

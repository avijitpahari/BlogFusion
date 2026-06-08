<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/session.php';
requireAuthor();
include BASE_PATH . 'include/db.php';
include BASE_PATH . 'include/functions.php';
include BASE_PATH . 'include/author_nav_sidebar.php';
$id = $_SESSION['user_id'];
$sql = "SELECT * FROM category_requests where author_id=$id ORDER BY id DESC";
$run = mysqli_query($conn, $sql);
$all_data = mysqli_fetch_all($run, MYSQLI_ASSOC);
$sql1 = "SELECT COUNT(*) AS total_requests,
    SUM(CASE WHEN status='pending'  THEN 1 ELSE 0 END) AS pending_count,
    SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) AS approved_count,
    SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END) AS rejected_count
FROM category_requests where author_id=$id";
$result1 = mysqli_query($conn, $sql1);
$count = mysqli_fetch_assoc($result1);

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo defined('BASE_URL') ? BASE_URL : '/BlogFusion/'; ?>upload/site_image/logo2.png" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Category Requests | Luminous Editor</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary": "#630ed4", "outline-variant": "#ccc3d8",
                        "surface-variant": "#e8dfee", "surface-container-highest": "#e8dfee",
                        "on-tertiary-fixed-variant": "#8c0053", "surface-container-high": "#ede5f4",
                        "on-error-container": "#93000a", "on-primary": "#ffffff",
                        "on-tertiary": "#ffffff", "on-secondary-fixed": "#0f0069",
                        "on-background": "#1d1a24", "primary-fixed-dim": "#d2bbff",
                        "on-secondary-fixed-variant": "#3323cc", "on-secondary-container": "#fffbff",
                        "on-secondary": "#ffffff", "surface-container": "#f3ebfa",
                        "on-primary-fixed": "#25005a", "surface-container-low": "#f9f1ff",
                        "on-surface": "#1d1a24", "on-tertiary-container": "#ffdde7",
                        "secondary": "#4b41e1", "inverse-on-surface": "#f6eefc",
                        "tertiary": "#9b005c", "background": "#fef7ff",
                        "secondary-container": "#645efb", "tertiary-container": "#bf2076",
                        "tertiary-fixed": "#ffd9e4", "on-tertiary-fixed": "#3e0022",
                        "secondary-fixed": "#e2dfff", "primary-container": "#7c3aed",
                        "on-surface-variant": "#4a4455", "error-container": "#ffdad6",
                        "inverse-surface": "#332f39", "on-primary-fixed-variant": "#5a00c6",
                        "secondary-fixed-dim": "#c3c0ff", "error": "#ba1a1a",
                        "surface-bright": "#fef7ff", "outline": "#7b7487",
                        "tertiary-fixed-dim": "#ffb0cd", "surface-tint": "#732ee4",
                        "surface-dim": "#dfd7e6", "on-error": "#ffffff",
                        "surface": "#fef7ff", "primary-fixed": "#eaddff",
                        "surface-container-lowest": "#ffffff", "on-primary-container": "#ede0ff",
                        "inverse-primary": "#d2bbff"
                    },
                    "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.75rem", "xl": "1rem", "full": "9999px" },
                    "fontFamily": { "headline": ["Public Sans"], "display": ["Public Sans"], "body": ["Public Sans"], "label": ["Public Sans"] }
                }
            }
        }
    </script>
    <style>
        body { 
            font-family: 'Public Sans', sans-serif; 
            background: linear-gradient(135deg, #FAF7FE 0%, #FEF7FF 50%, #F9F4FD 100%);
        }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .glass-header { backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
    </style>
</head>

<body class="font-body text-on-surface min-h-screen">
    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-black/40 z-40 md:hidden hidden transition-opacity" id="sidebar-overlay"
        onclick="toggleSidebar()"></div>
    <!-- Sidebar -->
    <?= author_slidebar('cate-req') ?>
    <!-- Main Content Wrapper -->
    <div class="md:ml-64 flex flex-col min-h-screen">
        <!-- TopNavBar -->
        <?= author_navbar(); ?>
        <!-- Canvas Area -->
        <main class="pt-24 px-4 sm:px-8 md:px-12 pb-12 relative overflow-hidden">
            <!-- Decorative Blobs -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-secondary/5 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4 relative z-10">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-on-surface">Category Requests</h2>
                    <p class="text-on-surface-variant text-sm mt-1">Propose new editorial taxonomies or track your existing request approvals.</p>
                </div>
                <!-- Action Tabs -->
                <div class="flex items-center gap-3 shrink-0 self-start sm:self-auto">
                    <button id="tab-view" onclick="switchTab('view')"
                        class="px-6 py-2.5 border-2 border-primary text-primary bg-white hover:bg-primary/5 font-bold rounded-full text-sm transition-all shadow-sm">
                        View Category
                    </button>
                    <button id="tab-create" onclick="switchTab('create')"
                        class="px-6 py-2.5 bg-primary text-white hover:bg-primary/95 font-bold rounded-full text-sm transition-all shadow-md shadow-primary/10">
                        Add a Category
                    </button>
                </div>
            </div>

            <!-- Main Render Container -->
            <div class="relative z-10 w-full max-w-4xl">
                
                <!-- ── VIEW SECTION ── -->
                <div id="view-section" class="bg-white rounded-[2rem] p-8 sm:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.02)] border border-purple-100/50 space-y-8 transition-all duration-300">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <!-- Pending Card -->
                        <div class="bg-[#fff9e6] border border-[#fdecd0] p-6 rounded-2xl">
                            <span class="text-[11px] font-extrabold text-[#e67e22] uppercase tracking-wider block mb-1">Pending</span>
                            <span class="text-4xl font-extrabold text-gray-900"><?= (int)$count['pending_count'] ?></span>
                        </div>
                        <!-- Approved Card -->
                        <div class="bg-[#edfbf1] border border-[#daf7e3] p-6 rounded-2xl">
                            <span class="text-[11px] font-extrabold text-[#27ae60] uppercase tracking-wider block mb-1">Approved</span>
                            <span class="text-4xl font-extrabold text-gray-900"><?= (int)$count['approved_count'] ?></span>
                        </div>
                        <!-- Rejected Card -->
                        <div class="bg-[#fdf2f2] border border-[#fcdede] p-6 rounded-2xl">
                            <span class="text-[11px] font-extrabold text-[#e74c3c] uppercase tracking-wider block mb-1">Rejected</span>
                            <span class="text-4xl font-extrabold text-gray-900"><?= (int)$count['rejected_count'] ?></span>
                        </div>
                    </div>

                    <!-- Search Input Wrapper -->
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-purple-400">search</span>
                        <input class="w-full bg-[#f6f2fa] border border-[#e8dfee]/40 rounded-2xl py-3.5 pl-12 pr-4 text-sm placeholder-[#7b7487] focus:ring-2 focus:ring-primary/20 focus:border-[#e8dfee] outline-none text-gray-900"
                            placeholder="Search category..." type="text" id="searchInput" oninput="filterTable()" />
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-y-4" id="categoryTable">
                            <thead>
                                <tr class="text-left text-[#9b94a7] uppercase text-[10px] tracking-widest border-none">
                                    <th class="px-6 py-2">Category</th>
                                    <th class="px-6 py-2 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($all_data)): ?>
                                <tr>
                                    <td colspan="2" class="px-6 py-12 text-center text-[#9b94a7]">
                                        <span class="material-symbols-outlined text-4xl block mb-2 text-purple-200">category</span>
                                        No category requests submitted yet.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($all_data as $row): ?>
                                <!-- Main Row -->
                                <tr class="bg-white hover:bg-[#faf9fc] transition-colors group cursor-pointer border-b border-purple-50/50" onclick="toggleDetails(<?= $row['id'] ?>)">
                                    <td class="px-6 py-5 rounded-l-2xl border-t border-b border-l border-purple-50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 bg-primary/5 rounded-xl flex items-center justify-center text-primary">
                                                <span class="material-symbols-outlined text-lg">label</span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-[15px] text-gray-900 block"><?= htmlspecialchars($row['category_name']) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 rounded-r-2xl border-t border-b border-r border-purple-50 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <span class="inline-flex px-4 py-1.5 text-[10px] font-extrabold uppercase tracking-wider rounded-full
                                                <?php
                                                if ($row['status'] == 'pending') echo 'bg-[#ffedd5] text-[#f97316]';
                                                elseif ($row['status'] == 'approved') echo 'bg-[#dcfce7] text-[#22c55e]';
                                                else echo 'bg-[#fee2e2] text-[#ef4444]';
                                                ?>">
                                                <?= $row['status'] ?>
                                            </span>
                                            <!-- Details toggle chevron -->
                                            <button class="w-7 h-7 rounded-full bg-purple-50 text-purple-600 hover:bg-primary hover:text-white transition-all flex items-center justify-center pointer-events-none">
                                                <span class="material-symbols-outlined text-[16px] transition-transform duration-300" id="chevron-<?= $row['id'] ?>">expand_more</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Collapsible Reason Row -->
                                <tr id="details-<?= $row['id'] ?>" class="hidden">
                                    <td colspan="2" class="px-6 py-5 bg-[#faf8ff] rounded-2xl border border-dashed border-purple-200">
                                        <div class="text-left space-y-3">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-purple-100/50 pb-2">
                                                <span class="text-xs font-bold text-primary uppercase tracking-widest">Request Details</span>
                                                <span class="text-xs text-[#9b94a7] font-mono">Slug: <strong class="text-primary font-semibold"><?= htmlspecialchars($row['category_slug']) ?></strong></span>
                                            </div>
                                            <div class="space-y-1">
                                                <span class="text-[11px] font-bold text-[#7b7487] uppercase tracking-wider block">Reason for request:</span>
                                                <p class="text-sm text-gray-700 leading-relaxed font-medium italic">
                                                    "<?= htmlspecialchars($row['reason']) ?>"
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ── CREATE SECTION ── -->
                <div id="create-section" class="hidden transition-all duration-300">
                    <div class="bg-white rounded-[2rem] p-8 sm:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.02)] border border-purple-100/50">
                        <div class="mb-8 text-center max-w-md mx-auto">
                            <div class="w-14 h-14 bg-primary/5 text-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <span class="material-symbols-outlined text-2xl">post_add</span>
                            </div>
                            <h3 class="text-xl font-bold tracking-tight text-gray-900">Request New Category</h3>
                            <p class="text-[#7b7487] text-sm mt-2">Propose a specialized taxonomy. The editorial board will review and notify you upon decision.</p>
                        </div>
                        <form class="space-y-6 max-w-xl mx-auto" id="categoryForm" method="POST" action="<?= BASE_URL ?>actions/category-request-action.php">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-primary uppercase tracking-widest ml-1">Category Name</label>
                                <input name="category_name" id="categoryNameInput" required oninput="generateSlugPreview(this.value)"
                                    class="w-full bg-[#f6f2fa] border border-[#e8dfee]/40 rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-[#e8dfee] outline-none text-gray-900 placeholder-[#9b94a7]"
                                    placeholder="e.g. AI News" type="text" />
                                <div class="text-xs text-[#9b94a7] font-mono ml-1 hidden" id="slugPreviewWrapper">
                                    Slug preview: <span class="text-primary font-bold" id="slugPreview"></span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center ml-1">
                                    <label class="block text-[10px] font-bold text-primary uppercase tracking-widest">Description</label>
                                    <span class="text-[10px] text-[#9b94a7] italic" id="charCount">0 / 300 characters</span>
                                </div>
                                <textarea name="reason" id="reasonInput" required maxlength="300" oninput="updateCharCount(this.value)"
                                    class="w-full bg-[#f6f2fa] border border-[#e8dfee]/40 rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-[#e8dfee] outline-none resize-none text-gray-900 placeholder-[#9b94a7]"
                                    placeholder="Explain why this category is needed and what type of content you plan to publish..."
                                    rows="5"></textarea>
                            </div>
                            <div class="pt-4">
                                <button type="submit" name="submit" id="submitBtn"
                                    class="w-full bg-gradient-to-r from-primary to-[#7c3aed] text-white py-4 rounded-xl font-bold text-sm tracking-wide shadow-lg shadow-primary/20 hover:opacity-95 hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center gap-2">
                                    <span id="btnText">Submit Request</span>
                                    <span id="btnIcon" class="material-symbols-outlined text-lg">send</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Guidance Note -->
                <div class="mt-8 flex items-start gap-4 p-5 bg-[#fbf9fe] border border-[#eaddff] rounded-2xl">
                    <span class="material-symbols-outlined text-primary mt-0.5 shrink-0">info</span>
                    <p class="text-sm text-[#7b7487] leading-relaxed">
                        Requests are typically reviewed within 24-48 hours. You'll receive a notification once the editorial team makes a decision.<br>
                        <strong>Pro tip:</strong> Detailed reasons help us understand the audience potential better!
                    </p>
                </div>
            </div>
        </main>
    </div>

    <!-- Trigger Toast Notification if session message exists -->
    <?php if (!empty($message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof showToast === 'function') {
                showToast(<?= json_encode($message) ?>, 'info');
            } else {
                alert(<?= json_encode($message) ?>);
            }
        });
    </script>
    <?php endif; ?>

    <script>
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

        function switchTab(tab) {
            const tabView = document.getElementById('tab-view');
            const tabCreate = document.getElementById('tab-create');
            const viewSection = document.getElementById('view-section');
            const createSection = document.getElementById('create-section');

            if (tab === 'view') {
                // View Category (Active - outlined as per screenshot)
                tabView.className = "px-6 py-2.5 border-2 border-primary text-primary bg-white hover:bg-primary/5 font-bold rounded-full text-sm transition-all shadow-sm";
                // Add a Category (Inactive - solid as per screenshot)
                tabCreate.className = "px-6 py-2.5 bg-primary text-white hover:bg-primary/95 font-bold rounded-full text-sm transition-all shadow-md shadow-primary/10";
                
                viewSection.classList.remove('hidden');
                createSection.classList.add('hidden');
            } else {
                // View Category (Inactive - solid)
                tabView.className = "px-6 py-2.5 bg-primary text-white hover:bg-primary/95 font-bold rounded-full text-sm transition-all shadow-md shadow-primary/10";
                // Add a Category (Active - outlined)
                tabCreate.className = "px-6 py-2.5 border-2 border-primary text-primary bg-white hover:bg-primary/5 font-bold rounded-full text-sm transition-all shadow-sm";
                
                createSection.classList.remove('hidden');
                viewSection.classList.add('hidden');
            }
        }

        function toggleDetails(id) {
            const row = document.getElementById('details-' + id);
            const chevron = document.getElementById('chevron-' + id);
            if (row.classList.contains('hidden')) {
                row.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            } else {
                row.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            }
        }

        function generateSlugPreview(name) {
            const wrapper = document.getElementById('slugPreviewWrapper');
            const preview = document.getElementById('slugPreview');
            if (!name.trim()) {
                wrapper.classList.add('hidden');
                return;
            }
            const slug = name.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            preview.textContent = slug;
            wrapper.classList.remove('hidden');
        }

        function updateCharCount(text) {
            const count = document.getElementById('charCount');
            count.textContent = `${text.length} / 300 characters`;
            if (text.length >= 280) {
                count.className = "text-[10px] text-red-500 italic font-medium";
            } else {
                count.className = "text-[10px] text-[#9b94a7] italic";
            }
        }

        function filterTable() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#categoryTable tbody tr[class*="group"]');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const id = row.id.replace('main-row-', '');
                const detailsRow = document.getElementById('details-' + id);
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                    if (detailsRow) detailsRow.classList.add('hidden');
                }
            });
        }

        // Apply id to rows to make details toggling reliable during search
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('#categoryTable tbody tr[class*="group"]');
            rows.forEach(row => {
                // Determine id from details row
                const detailsRow = row.nextElementSibling;
                if (detailsRow && detailsRow.id && detailsRow.id.startsWith('details-')) {
                    const id = detailsRow.id.replace('details-', '');
                    row.id = 'main-row-' + id;
                }
            });
        });

        document.getElementById('categoryForm').addEventListener('submit', function (e) {
            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            btn.disabled = true;
            btnText.innerHTML = 'Processing...';
            document.getElementById('btnIcon').outerHTML = `<span class="material-symbols-outlined text-lg animate-spin">progress_activity</span>`;
        });
    </script>
</body>
</html>
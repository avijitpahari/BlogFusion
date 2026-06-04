<?php include "../include/session.php";
requireAuthor();
include "../include/db.php";
include "../include/author_nav_sidebar.php";

$table = 'comments';
$limit = 6;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$id = $_SESSION['user_id'];

$query = "SELECT c.*,p.title FROM $table c INNER JOIN posts p ON c.post_id = p.id WHERE p.author_id = $id ORDER BY c.created_at DESC LIMIT $limit OFFSET $offset;";
$result = mysqli_query($conn, $query);
$data = mysqli_num_rows($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$total_query = "SELECT COUNT(*) as total FROM $table c INNER JOIN posts p ON c.post_id = p.id WHERE p.author_id = $id";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];

$total_post = "SELECT COUNT(*) as total_posts FROM posts WHERE author_id = $id";
$total_result1 = mysqli_query($conn, $total_post);
$total_posts = mysqli_fetch_assoc($total_result1);
$total_post1 = $total_posts['total_posts'];

include "../include/pagination.php";
$pagination = paginate($data, $total_records, $page, $offset, $limit);
$data24        = $pagination['data'];
$total_pages   = $pagination['total_pages'];
$page          = $pagination['current_page'];
$limit         = $pagination['limit'];
$total_records = $pagination['total_records'];
$offset        = $pagination['offset'];
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Comments Management - Luminous</title>
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
    </style>
</head>

<body class="bg-background text-on-background min-h-screen">
    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-black/40 z-40 md:hidden hidden transition-opacity" id="sidebar-overlay"
        onclick="toggleSidebar()"></div>
    <!-- Sidebar -->
    <?= author_slidebar('comments') ?>
    <!-- TopNavBar -->
    <?= author_navbar(); ?>
    <!-- Main Content Canvas — FIXED: was pl-64 pt-16 (no responsive prefix), now md:pl-64 + pt-16 -->
    <main class="md:pl-64 pt-16 min-h-screen">
        <div class="p-4 sm:p-6 md:p-10 max-w-7xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8 md:mb-10 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-black text-on-surface tracking-tighter -mb-1">Comments</h2>
                    <p class="text-zinc-500 font-medium mt-2">Manage and respond to your audience's engagement.</p>
                </div>
                <div class="flex gap-3">
                    <button class="px-4 sm:px-5 py-2.5 rounded-xl bg-surface-container-high text-on-surface-variant text-sm font-semibold hover:bg-surface-variant transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">filter_list</span>
                        Filter
                    </button>
                </div>
            </div>
            <!-- Stats (Bento Style) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-10">
                <div class="bg-surface-container-low p-5 sm:p-6 rounded-3xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-primary">chat_bubble</span>
                    </div>
                    <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Total Comments</p>
                    <p class="text-4xl font-black text-on-surface"><?= $total_records ?></p>
                    <p class="text-xs text-zinc-500 mt-2 flex items-center gap-1">
                        <span class="text-emerald-600 font-bold">+12%</span> from last week
                    </p>
                </div>
                <div class="bg-surface-container-highest p-5 sm:p-6 rounded-3xl flex items-center justify-between border border-primary/5">
                    <div>
                        <p class="text-xs font-bold text-secondary uppercase tracking-widest mb-1">Response Rate</p>
                        <p class="text-4xl font-black text-on-surface">
                            <?= $total_post1 > 0 ? round($total_records / $total_post1, 2) : 0 ?>%
                        </p>
                        <p class="text-xs text-zinc-500 mt-2">Excellent engagement score</p>
                    </div>
                    <div class="h-16 w-24 sm:w-32 flex items-end gap-1 shrink-0">
                        <div class="w-2 bg-primary/20 rounded-full h-1/2"></div>
                        <div class="w-2 bg-primary/20 rounded-full h-3/4"></div>
                        <div class="w-2 bg-primary/40 rounded-full h-2/3"></div>
                        <div class="w-2 bg-primary/60 rounded-full h-full"></div>
                        <div class="w-2 bg-primary rounded-full h-4/5"></div>
                    </div>
                </div>
            </div>
            <!-- Comments Table -->
            <div class="bg-surface-container-lowest rounded-[2.5rem] p-3 sm:p-4 shadow-sm">
                <!-- overflow-x-auto enables horizontal scroll on small screens -->
                <div class="overflow-x-auto">
                    <table class="min-w-[640px] w-full text-left border-separate border-spacing-y-3 sm:border-spacing-y-4">
                        <thead class="text-[10px] font-black uppercase tracking-[0.15em] text-zinc-400">
                            <tr>
                                <th class="px-4 sm:px-8 pb-2">User</th>
                                <th class="px-4 pb-2">Comment</th>
                                <th class="px-4 pb-2 hidden md:table-cell">Post context</th>
                                <th class="px-4 pb-2 hidden sm:table-cell">Date</th>
                                <th class="px-4 sm:px-8 pb-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php foreach ($data24 as $key => $row):
                                $data_fetch = data_featch($conn, $row['user_id']);
                                $data01 = $data_fetch['data'];
                                $data_fetch = data($conn, 'posts', $row['post_id']);
                                $posts = $data_fetch;
                            ?>
                            <tr class="group hover:bg-surface-container-low transition-colors rounded-3xl">
                                <td class="px-4 sm:px-8 py-4 rounded-l-[2rem]">
                                    <div class="flex items-center gap-3 min-w-[140px]">
                                        <img class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl object-cover shrink-0"
                                            src="../<?= $data01['profile_image'] ?>" alt="avatar">
                                        <div class="min-w-0">
                                            <p class="font-bold text-on-surface truncate"><?= htmlspecialchars($data01['name']) ?></p>
                                            <p class="text-xs text-zinc-500 truncate max-w-[100px] sm:max-w-none"><?= htmlspecialchars($data01['email']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 max-w-[160px] sm:max-w-xs">
                                    <p class="text-on-surface-variant line-clamp-2 leading-relaxed text-xs sm:text-sm">
                                        <?= htmlspecialchars($row['comment']) ?>
                                    </p>
                                </td>
                                <td class="px-4 py-4 hidden md:table-cell">
                                    <div class="bg-surface-container px-3 py-1.5 rounded-xl inline-flex items-center gap-2 group-hover:bg-white transition-colors max-w-[180px]">
                                        <span class="material-symbols-outlined text-base text-primary shrink-0">article</span>
                                        <span class="text-xs font-semibold text-zinc-600 truncate"><?= htmlspecialchars($posts['title']) ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 hidden sm:table-cell whitespace-nowrap">
                                    <p class="text-xs font-medium text-zinc-500">
                                        <?= date("M d, Y", strtotime($row['created_at'])) ?>
                                    </p>
                                </td>
                                <td class="px-4 sm:px-8 py-4 text-right rounded-r-[2rem]">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all flex items-center justify-center" title="Reply">
                                            <span class="material-symbols-outlined text-base sm:text-lg">reply</span>
                                        </button>
                                        <button onclick="window.location.href='../actions/author.php?id=<?=$id?>&btn=comment'"
                                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-tertiary/10 text-tertiary hover:bg-tertiary hover:text-white transition-all flex items-center justify-center" title="Delete">
                                            <span class="material-symbols-outlined text-base sm:text-lg">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <?php pagination_links($total_pages, $limit, $total_records, $offset, 'comments'); ?>
            </div>
        </div>
    </main>
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
    </script>
</body>
</html>
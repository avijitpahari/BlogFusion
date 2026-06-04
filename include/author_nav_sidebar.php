<?php
include "data_fetch.php";
$data_fetch = data_featch($conn, $_SESSION['user_id']);
$data1 = $data_fetch['data'];


function author_slidebar($active)
{
    global $conn;
    $id = $_SESSION['user_id'];
    $notif_cnt_query = "SELECT COUNT(*) as total FROM notifications WHERE user_id = $id AND is_read = 0";
    $notif_cnt_result = mysqli_query($conn, $notif_cnt_query);
    $notif_cnt_row = mysqli_fetch_assoc($notif_cnt_result);
    $notif_count = (int)($notif_cnt_row['total'] ?? 0);
    ?>
    <!-- Shared sidebar CSS — injected once per page via this component -->
    <style>
        .sidebar-closed { transform: translateX(-100%); }
        .sidebar-open   { transform: translateX(0); }
        @media (min-width: 768px) {
            .sidebar-closed,
            .sidebar-open { transform: translateX(0) !important; }
        }
    </style>

    <aside
        class="h-screen w-64 fixed left-0 top-0 bg-[#f9f1ff] dark:bg-[#1d1a24] flex flex-col py-8 px-4 z-50 transition-transform duration-300 sidebar-closed md:translate-x-0"
        id="main-sidebar">
        <div class="mb-10 px-4 flex justify-between items-center">
            <h1 class="text-2xl font-black tracking-tight text-[#7C3AED] d-flex">
                <img src="../upload/site_image/logo1.png" alt="Luminous">
            </h1>
            <button
                class="md:hidden w-10 h-10 flex items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container-high transition-colors"
                onclick="toggleSidebar()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <nav class="flex-1 space-y-2">
            <a class="<?= ($active == 'dashboard') ? 'flex items-center gap-3 px-4 py-3 bg-[#eaddff] text-[#5a00c6] rounded-full font-semibold' : 'flex items-center gap-3 px-4 py-3 text-[#4a4455] dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full' ?>"
                href="index.php">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="edit-post.php"
                class="<?= ($active == 'add-post') ? 'flex items-center gap-3 px-4 py-3 bg-[#eaddff] text-[#5a00c6] rounded-full font-semibold' : 'flex items-center gap-3 px-4 py-3 text-[#4a4455] dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full' ?>">
                <span class="material-symbols-outlined">post_add</span>
                <span class="font-medium">Add Post</span>
            </a>
            <a href="my-post.php"
                class="<?= ($active == 'my-post') ? 'flex items-center gap-3 px-4 py-3 bg-[#eaddff] text-[#5a00c6] rounded-full font-semibold' : 'flex items-center gap-3 px-4 py-3 text-[#4a4455] dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full' ?>">
                <span class="material-symbols-outlined">article</span>
                <span class="font-medium">My Posts</span>
            </a>
            <a href="analytics.php"
                class="<?= ($active == 'analytics') ? 'flex items-center gap-3 px-4 py-3 bg-[#eaddff] text-[#5a00c6] rounded-full font-semibold' : 'flex items-center gap-3 px-4 py-3 text-[#4a4455] dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full' ?>">
                <span class="material-symbols-outlined">insights</span>
                <span class="font-medium">Analytics</span>
            </a>
            <a class="<?= ($active == 'cate-req') ? 'flex items-center gap-3 px-4 py-3 bg-[#eaddff] text-[#5a00c6] rounded-full font-semibold' : 'flex items-center gap-3 px-4 py-3 text-[#4a4455] dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full' ?>"
                href="request-category.php">
                <span class="material-symbols-outlined" data-icon="category">category</span>
                <span class="font-medium">Category</span>
            </a>
            <a href="comments.php"
                class="<?= ($active == 'comments') ? 'flex items-center gap-3 px-4 py-3 bg-[#eaddff] text-[#5a00c6] rounded-full font-semibold' : 'flex items-center gap-3 px-4 py-3 text-[#4a4455] dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full' ?>">
                <span class="material-symbols-outlined">forum</span>
                <span class="font-medium">Comments</span>
            </a>
            <a href="notifications.php"
                class="<?= ($active == 'notifications') ? 'flex items-center gap-3 px-4 py-3 bg-[#eaddff] text-[#5a00c6] rounded-full font-semibold relative' : 'flex items-center gap-3 px-4 py-3 text-[#4a4455] dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full relative' ?>">
                <span class="material-symbols-outlined">notifications</span>
                <span class="font-medium flex-1">Notifications</span>
                <?php if ($notif_count > 0): ?>
                <span id="notif-badge-sidebar" class="bg-primary text-white text-[11px] font-bold px-2 py-0.5 rounded-full min-w-5 text-center leading-none">
                    <?= $notif_count ?>
                </span>
                <?php endif; ?>
            </a>
            <a href="profile.php"
                class="<?= ($active == 'profile') ? 'flex items-center gap-3 px-4 py-3 bg-[#eaddff] text-[#5a00c6] rounded-full font-semibold' : 'flex items-center gap-3 px-4 py-3 text-[#4a4455] dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full' ?>">
                <span class="material-symbols-outlined">person</span>
                <span class="font-medium">Profile</span>
            </a>
        </nav>
        <div class="mt-auto border-t border-outline-variant/20 pt-6">
            <a class="flex items-center gap-3 px-4 py-3 dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full text-error"
                href="../actions/logout.php">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-medium">Logout</span>
            </a>
        </div>
    </aside>
    <?php
}


function author_navbar()
{
    global $data1, $data_fetch, $conn;
    $id = $_SESSION['user_id'];
    $notif_cnt_query = "SELECT COUNT(*) as total FROM notifications WHERE user_id = $id AND is_read = 0";
    $notif_cnt_result = mysqli_query($conn, $notif_cnt_query);
    $notif_cnt_row = mysqli_fetch_assoc($notif_cnt_result);
    $notif_count = (int)($notif_cnt_row['total'] ?? 0);
    ?>
    <header
        class="fixed top-0 right-0 w-full md:w-[calc(100%-16rem)] z-40 bg-white/70 dark:bg-[#1d1a24]/70 backdrop-blur-xl flex justify-between items-center px-4 md:px-8 h-16 shadow-sm shadow-indigo-500/5">
        <div class="flex items-center flex-1 max-w-xl gap-4">
            <button
                class="md:hidden w-10 h-10 flex items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container-high transition-colors"
                onclick="toggleSidebar()">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <form method="GET" action="" class="relative w-full">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                <input
                    id="topbarSearchInput"
                    name="q"
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                    class="w-full bg-surface-container-highest border-none rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-[#7c3aed]/20 transition-all outline-none"
                    placeholder="Search articles or activity..." type="text" />
            </form>
        </div>
        <div class="flex items-center gap-4 ml-4 md:ml-8">
            <button
                onclick="window.location.href='notifications.php'"
                class="hidden sm:flex w-10 h-10 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container-high transition-colors relative"
                title="Notifications">
                <span class="material-symbols-outlined">notifications</span>
                <?php if ($notif_count > 0): ?>
                <span id="notif-dot-navbar" class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-600 rounded-full animate-pulse"></span>
                <?php endif; ?>
            </button>
            <div class="h-10 w-10 rounded-full bg-primary-container overflow-hidden cursor-pointer"
                onclick="window.location.href='profile.php'">
                <img class="h-full w-full object-cover"
                    src="../<?= $data1['profile_image'] ?>"
                    alt="Profile" />
            </div>
        </div>
    </header>
    <?php
    if (function_exists('inject_project_toast')) {
        inject_project_toast();
    }
}
?>
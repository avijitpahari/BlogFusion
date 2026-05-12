<?php
include "data_fetch.php";
$data_fetch = data_featch($conn, $_SESSION['user_id']);
$data1 = $data_fetch['data'];




function author_slidebar($active)
{
    ?>
    <aside
        class="h-screen w-64 fixed left-0 top-0 bg-[#f9f1ff] dark:bg-[#1d1a24] flex flex-col py-8 px-4 z-50 transition-transform duration-300 sidebar-closed md:translate-x-0"
        id="main-sidebar">
        <div class="mb-10 px-4 flex justify-between items-center">
            <h1 class="text-2xl font-black tracking-tight text-[#7C3AED] d-flex"><img src="../upload/site_image/logo1.png" alt=""></h1>
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
            <a href="edit-post.php" class="<?= ($active == 'add-post') ? 'flex items-center gap-3 px-4 py-3 bg-[#eaddff] text-[#5a00c6] rounded-full font-semibold' : 'flex items-center gap-3 px-4 py-3 text-[#4a4455] dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full' ?>"
                >
                <span class="material-symbols-outlined">post_add</span>
                <span class="font-medium">Add Post</span>
            </a>
            <a href="my-post.php" class="<?= ($active == 'my-post') ? 'flex items-center gap-3 px-4 py-3 bg-[#eaddff] text-[#5a00c6] rounded-full font-semibold' : 'flex items-center gap-3 px-4 py-3 text-[#4a4455] dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full' ?>"
                >
                <span class="material-symbols-outlined">article</span>
                <span class="font-medium">My Posts</span>
            </a>
            <a href="comments.php" class="<?= ($active == 'comments') ? 'flex items-center gap-3 px-4 py-3 bg-[#eaddff] text-[#5a00c6] rounded-full font-semibold' : 'flex items-center gap-3 px-4 py-3 text-[#4a4455] dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full' ?>"
                >
                <span class="material-symbols-outlined">forum</span>
                <span class="font-medium">Comments</span>
            </a>
            <a href="profile.php" class="<?= ($active == 'profile') ? 'flex items-center gap-3 px-4 py-3 bg-[#eaddff] text-[#5a00c6] rounded-full font-semibold' : 'flex items-center gap-3 px-4 py-3 text-[#4a4455] dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full' ?>" >
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
    global $data1, $data_fetch;
    ?>
    <header
        class="fixed top-0 right-0 w-full md:w-[calc(100%-16rem)] z-40 bg-white/70 dark:bg-[#1d1a24]/70 backdrop-blur-xl flex justify-between items-center px-4 md:px-8 h-16 shadow-sm shadow-indigo-500/5">
        <div class="flex items-center flex-1 max-w-xl gap-4">
            <button
                class="md:hidden w-10 h-10 flex items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container-high transition-colors"
                onclick="toggleSidebar()">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <div class="relative w-full">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                <input
                    class="w-full bg-surface-container-highest border-none rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-[#7c3aed]/20 transition-all outline-none"
                    placeholder="Search articles or activity..." type="text" />
            </div>
        </div>
        <div class="flex items-center gap-4 ml-4 md:ml-8">
            <button
                class="hidden sm:flex w-10 h-10 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined">notifications</span>
            </button>
            <!-- <button
                class="w-10 h-10 flex items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container-high transition-colors"
                onclick="document.documentElement.classList.toggle('dark')">
                <span class="material-symbols-outlined dark:hidden">dark_mode</span>
                <span class="material-symbols-outlined hidden dark:block">light_mode</span>
            </button> -->
            <div class="h-10 w-10 rounded-full bg-primary-container overflow-hidden">
                <img class="h-full w-full object-cover" onclick="window.location.href='profile.php'"
                    data-alt="professional headshot of Alex Rivera, a young male professional with a warm smile wearing a navy blazer"
                    src="../<?= $data1['profile_image'] ?>" />
            </div>
        </div>
    </header>
    <?php
}
?>
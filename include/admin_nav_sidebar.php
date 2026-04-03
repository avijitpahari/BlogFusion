<?php
include "data_fetch.php";
$data_fetch=data_featch($conn,$_SESSION['user_id']);
$data=$data_fetch['data'];


// function slidebar($active){
//     echo '
//         <aside
//             class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-y-auto">
//             <div class="p-6 flex items-center gap-3">
//                 <div class="h-10 w-10 bg-primary rounded-xl flex items-center justify-center text-white">
//                     <span class="material-symbols-outlined">auto_awesome</span>
//                 </div>
//                 <div>
//                     <h1 class="text-xl font-bold tracking-tight text-primary">Blog Fusion</h1>
//                     <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Admin Portal</p>
//                 </div>
//             </div>
//             <nav class="mt-6 px-3 space-y-1">
//                 <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
//                     href="dashboard.php" id="dashboard">
//                     <span class="material-symbols-outlined">dashboard</span>
//                     <span>Dashboard</span>
//                 </a>
//                 <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
//                     href="users.php">
//                     <span class="material-symbols-outlined">group</span>
//                     <span>Users</span>
//                 </a>
//                 <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
//                     href="posts.php">
//                     <span class="material-symbols-outlined">article</span>
//                     <span>Posts</span>
//                 </a>
//                 <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
//                     href="categories.php">
//                     <span class="material-symbols-outlined">category</span>
//                     <span>Categories</span>
//                 </a>
//                 <a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all"
//                     href="comments.php">
//                     <span class="material-symbols-outlined">comment</span>
//                     <span>Comments</span>
//                 </a>
//             </nav>
//             <div class="mt-auto pt-10 px-3 pb-6">
//                 <a class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-lg transition-all"
//                     href="../actions/logout.php">
//                     <span class="material-symbols-outlined">logout</span>
//                     <span>Logout</span>
//                 </a>
//             </div>
//         </aside>
//         script>
//         document.getElementById("dashboard").class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all";
//     </script>
//     ';
//     
  //   <!-- <script>
//         document.getElementById('dashboard').class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all";
//     </script> -->

     
// }

function slidebar($active){
?>
<aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-y-auto">

    <div class="p-6 flex items-center gap-3">
        <div class="h-10 w-10 bg-primary rounded-xl flex items-center justify-center text-white">
            <span class="material-symbols-outlined">auto_awesome</span>
        </div>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-primary">Blog Fusion</h1>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Admin Portal</p>
        </div>
    </div>

    <nav class="mt-6 px-3 space-y-1">

        <!-- Dashboard -->
        <a href="dashboard.php"
           class="<?= ($active == 'dashboard') ? 'flex items-center gap-3 px-4 py-3 text-primary font-semibold sidebar-active rounded-lg transition-all' : 'flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all' ?>">
            <span class="material-symbols-outlined">dashboard</span>
            <span>Dashboard</span>
        </a>

        <!-- Users -->
        <a href="users.php"
           class="<?= ($active == 'users') ? 'flex items-center gap-3 px-4 py-3 text-primary font-semibold sidebar-active rounded-lg transition-all' : 'flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all' ?>">
            <span class="material-symbols-outlined">group</span>
            <span>Users</span>
        </a>

        <!-- Posts -->
        <a href="posts.php"
           class="<?= ($active == 'posts') ? 'flex items-center gap-3 px-4 py-3 text-primary font-semibold sidebar-active rounded-lg transition-all' : 'flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all' ?>">
            <span class="material-symbols-outlined">article</span>
            <span>Posts</span>
        </a>

        <!-- Categories -->
        <a href="categories.php"
           class="<?= ($active == 'categories') ? 'flex items-center gap-3 px-4 py-3 text-primary font-semibold sidebar-active rounded-lg transition-all' : 'flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all' ?>">
            <span class="material-symbols-outlined">category</span>
            <span>Categories</span>
        </a>

        <!-- Comments -->
        <a href="comments.php"
           class="<?= ($active == 'comments') ? 'flex items-center gap-3 px-4 py-3 text-primary font-semibold sidebar-active rounded-lg transition-all' : 'flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary rounded-lg transition-all' ?>">
            <span class="material-symbols-outlined">comment</span>
            <span>Comments</span>
        </a>

    </nav>

    <div class="mt-auto pt-10 px-3 pb-6">
        <a href="../actions/logout.php"
           class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-lg transition-all">
            <span class="material-symbols-outlined">logout</span>
            <span>Logout</span>
        </a>
    </div>

</aside>
<?php
}


function ad_navbar(){
    global $data, $data_fetch;
?>
    
    <header
                class="h-16 flex items-center justify-between px-8 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
                <div class="max-w-md w-full">
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input
                            class="w-full pl-10 pr-4 py-2 bg-slate-100 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary text-sm"
                            placeholder="Search analytics or posts..." type="text" />
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <!-- ======button====== -->
                    <button id="themeToggle"
                        class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full flex items-center justify-center"
                        title="Toggle Theme">
                        <span id="themeIcon" class="material-symbols-outlined">light_mode</span>
                    </button>
                    
                    <button
                        class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span
                            class="absolute top-2 right-2 h-2 w-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
                    </button>
                    <div class="h-8 w-[1px] bg-slate-200 dark:bg-slate-700 mx-2"></div>
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold"><?=  $data['name'] ?></p>
                            <p class="text-xs text-slate-500">Super Admin</p>
                        </div>
                        <img alt="Admin Avatar" class="h-10 w-10 rounded-full object-cover border-2 border-primary/20"
                            data-alt="Close up portrait of a professional male administrator"
                            src="<?php if($data_fetch['image']){
                                    echo  $data_fetch['image'];
                                }else{
                                    echo '../upload/profile-images/default.png';
                                }
                                ?>" />
                    </div>
                </div>
            </header><?php
}
?>
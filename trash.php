<div class="flex gap-2">

    <!-- Previous -->
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 bg-white border rounded">Previous</a>
    <?php endif; ?>

    <!-- Page Numbers -->
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>

        <a href="?page=<?php echo $i; ?>" class="px-3 py-1 rounded 
                                    <?php echo ($i == $page) ? 'bg-primary text-white' : 'bg-white border'; ?>">
            <?php echo $i; ?>
        </a>

    <?php endfor; ?>

    <!-- Next -->
    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="bg-white "><span
                class="material-symbols-outlined text-[18px]">chevron_right</span></a>
    <?php endif; ?>

</div>






<?php

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPages = 4; // example

// 👉 start & end calculate
$start = $currentPage - 1;
$end = $currentPage + 1;

// 👉 fix start
if ($start < 1) {
    $start = 1;
    $end = min(3, $totalPages);
}

// 👉 fix end
if ($end > $totalPages) {
    $end = $totalPages;
    $start = max(1, $totalPages - 2);
}
?>

<div class="flex gap-2">

    <!-- 🔙 PREVIOUS -->
    <a href="?page=<?= $currentPage - 1 ?>"
       class="<?= ($currentPage == 1) ? 'opacity-50 pointer-events-none' : '' ?>">
       Prev
    </a>

    <!-- 🔢 PAGE NUMBERS -->
    <?php for ($i = $start; $i <= $end; $i++) { ?>
        <a href="?page=<?= $i ?>"
           class="<?= ($i == $currentPage) ? 'bg-purple-600 text-white px-3 py-1' : 'px-3 py-1' ?>">
           <?= $i ?>
        </a>
    <?php } ?>

    <!-- 🔜 NEXT -->
    <a href="?page=<?= $currentPage + 1 ?>"
       class="<?= ($currentPage == $totalPages) ? 'opacity-50 pointer-events-none' : '' ?>">
       Next
    </a>

</div>

















// calculate start & end
    $start = ($current_page - 1) * $limit + 1;
    $end = $start + $data_count - 1;

    if ($total_records == 0) {
        $start = 0;
        $end = 0;
    }

    echo '<div class="d-flex justify-content-between align-items-center mt-3">';

    // LEFT SIDE (Showing text)
    echo '<div>
        Showing ' . $start . ' to ' . $end . ' of ' . $total_records . ' entries
    </div>';

    // RIGHT SIDE (Pagination links)
    echo '<div><ul class="pagination">';

    // Previous
    if ($current_page > 1) {
        echo '<li class="page-item">
        <a class="page-link" href="?page=' . ($current_page - 1) . '&limit=' . $limit . '">Previous</a>
        </li>';
    }

    // Page numbers
    for ($i = max(1,$current_page-1); $i <= min($total_pages,3); $i++) {
        $active = ($i == $current_page) ? 'active' : '';

        echo '<li class="page-item ' . $active . '">
        <a class="page-link" href="?page=' . $i . '&limit=' . $limit . '">' . $i . '</a>
        </li>';
    }

    // Next
    if ($current_page < $total_pages) {
        echo '<li class="page-item">
        <a class="page-link" href="?page=' . ($current_page + 1) . '&limit=' . $limit . '">Next</a>
        </li>';
    }

    echo '</ul></div>';

    echo '</div>';







    <?php
                include "../include/db.php";

                // pagination setup
                $limit = 4; // rows per page
                $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

                if ($page < 1)
                    $page = 1;

                $offset = ($page - 1) * $limit;

                // fetch users
                $query = "SELECT * FROM users LIMIT $limit OFFSET $offset";
                $result = mysqli_query($conn, $query);

                // total count
                $total_query = "SELECT COUNT(*) as total FROM users";
                $total_result = mysqli_query($conn, $total_query);
                $total_data = mysqli_fetch_assoc($total_result);
                $total_users = $total_data['total'];

                $total_pages = ceil($total_users / $limit);
                ?>







       <!-- ==============================  edit-post.php   ================================================== -->


       <!DOCTYPE html>
<?php include "../include/session.php";
requireAuthor();
include "../include/db.php";
include "../include/author_nav_sidebar.php";
include "../config.php";
$year = date("Y");
$month = date("m");
$cat_query = "SELECT * FROM categories";
$cat_result = mysqli_query($conn, $cat_query);

?>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Add New Post | Blog Fusion</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&amp;display=swap"
        rel="stylesheet" /> -->
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-surface": "#1d1a24",
                        "tertiary-fixed": "#ffd9e4",
                        "inverse-on-surface": "#f6eefc",
                        "primary-container": "#7c3aed",
                        "secondary-container": "#645efb",
                        "outline-variant": "#ccc3d8",
                        "surface-container-highest": "#e8dfee",
                        "surface-container-high": "#ede5f4",
                        "on-error-container": "#93000a",
                        "tertiary-fixed-dim": "#ffb0cd",
                        "tertiary-container": "#bf2076",
                        "on-error": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "on-secondary-fixed": "#0f0069",
                        "on-secondary": "#ffffff",
                        "surface-bright": "#fef7ff",
                        "primary-fixed": "#eaddff",
                        "surface-container": "#f3ebfa",
                        "on-primary-fixed-variant": "#5a00c6",
                        "error-container": "#ffdad6",
                        "on-primary-container": "#ede0ff",
                        "surface-tint": "#732ee4",
                        "surface": "#fef7ff",
                        "outline": "#7b7487",
                        "on-tertiary-fixed": "#3e0022",
                        "error": "#ba1a1a",
                        "on-background": "#1d1a24",
                        "surface-variant": "#e8dfee",
                        "secondary-fixed": "#e2dfff",
                        "primary-fixed-dim": "#d2bbff",
                        "on-secondary-fixed-variant": "#3323cc",
                        "on-primary": "#ffffff",
                        "surface-container-low": "#f9f1ff",
                        "secondary-fixed-dim": "#c3c0ff",
                        "on-secondary-container": "#fffbff",
                        "background": "#fef7ff",
                        "secondary": "#4b41e1",
                        "surface-dim": "#dfd7e6",
                        "on-surface-variant": "#4a4455",
                        "on-tertiary-fixed-variant": "#8c0053",
                        "on-tertiary": "#ffffff",
                        "on-primary-fixed": "#25005a",
                        "primary": "#630ed4",
                        "inverse-surface": "#332f39",
                        "on-tertiary-container": "#ffdde7",
                        "inverse-primary": "#d2bbff",
                        "tertiary": "#9b005c"
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
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e8dfee;
            border-radius: 10px;
        }
    </style>
    <script src="../vendor/tinymce/tinymce.min.js"></script>
</head>

<body class="bg-surface text-on-surface">
    <!-- SideNavBar Shell -->
    <?= author_slidebar('add-post') ?>
    <!-- Main Wrapper -->
    <div class="ml-64 flex flex-col min-h-screen">
        <!-- TopNavBar Shell -->
        <?= author_navbar(); ?>
        <!-- Canvas Content Area -->
        <main class="mt-16 p-8 bg-surface">
            <form action="../actions/author_post.php" method="POST" enctype="multipart/form-data">
                <div class="max-w-[1400px] mx-auto">
                    <!-- Page Header -->
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-extrabold tracking-tight text-on-surface">Add New Post</h2>
                            <!-- <p class="text-on-surface-variant mt-1">Craft your next masterpiece with our Luminous
                                Editor.
                            </p> -->
                        </div>
                        <!-- <div class="flex items-center space-x-3">
                            <button
                                class="px-6 py-2.5 rounded-xl text-primary font-semibold hover:bg-surface-container-high transition-all">Save
                                Draft</button>
                            <button
                                class="px-8 py-2.5 rounded-xl bg-gradient-to-br from-primary to-primary-container text-white font-bold shadow-lg shadow-primary/20 transition-all hover:scale-[1.02] active:scale-[0.98]">Publish
                                Post</button>
                        </div> -->
                    </div>
                    <div class="grid grid-cols-12 gap-8">
                        <!-- Left Column: Main Editor -->
                        <div class="col-span-12 lg:col-span-8 space-y-8">
                            <!-- Post Basic Info -->
                            <div
                                class="bg-surface-container-lowest rounded-3xl p-8 shadow-sm border border-outline-variant/10">
                                <div class="mb-6">
                                    <label
                                        class="block text-[10px] font-bold text-gray-400 mb-2 uppercase tracking-[2px]">Title</label>
                                    <input type="text" id="post-title" name="title"
                                        placeholder="Enter post title here..."
                                        class="w-full bg-purple-50/30 border border-purple-100 text-sm rounded-xl py-4 px-5 focus:outline-none focus:ring-2 focus:ring-purple-200 transition-all">

                                    <div
                                        class="flex items-center mt-2 text-[11px] text-gray-500 bg-purple-50/50 w-fit px-3 py-1.5 rounded-lg border border-purple-100">
                                        <span class="material-icons text-sm mr-1.5 text-purple-400">link</span>
                                        <span>Permalink:</span>
                                        <span
                                            class="ml-1 text-gray-400"><?= POST_URL . "/" . $year . "/" . $month . "/" ?></span>
                                        <span id="permalink-slug"
                                            class="font-bold text-purple-600 ml-0.5">your-post-title</span>
                                        <input type="hidden" name="slug" id="slug">
                                        <!-- <button type="button" id="edit-slug-btn"
                                        class="ml-2 text-gray-400 hover:text-purple-600 transition-colors">
                                        <span class="material-icons" style="font-size: 14px;">edit</span>
                                    </button> -->
                                    </div>

                                </div>

                                <div class="mb-8">
                                    <div class="flex justify-between items-end mb-2">
                                        <label
                                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-[2px]">Short
                                            Description</label>
                                        <span id="char-count" class="text-[10px] text-gray-400 font-medium italic">0 /
                                            160
                                            characters</span>
                                    </div>
                                    <textarea id="short-desc" name="short_description" rows="3" maxlength="160"
                                        placeholder="Write a brief excerpt that summarizes the post..."
                                        class="w-full p-5 bg-purple-50/30 border border-purple-100 rounded-2xl text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-200 transition-all resize-none shadow-sm"></textarea>
                                </div>

                                <div class="mb-2">
                                    <label
                                        class="block text-[10px] font-bold text-gray-400 mb-3 uppercase tracking-widest">Blog
                                        Image</label>

                                    <label for="seo_image_input"
                                        class="relative border-2 border-dashed border-purple-100 rounded-2xl p-8 text-center cursor-pointer block bg-purple-50/10 hover:bg-purple-50/40 transition-all group overflow-hidden">
                                        <input type="file" name='blog_image' id="seo_image_input" accept="image/*"
                                            hidden>

                                        <div id="seo-placeholder" class="flex flex-col items-center">
                                            <div
                                                class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-3">
                                                <span class="material-icons text-purple-500">add_photo_alternate</span>
                                            </div>
                                            <p class="text-sm text-gray-600 font-medium">Drop image here or click to
                                                upload
                                            </p>
                                            <p
                                                class="text-[10px] text-gray-400 uppercase mt-1 tracking-widest font-bold">
                                                Recommended: 1200 x 630px</p>
                                        </div>

                                        <div id="seo-preview-wrapper" class="hidden relative inline-block group">
                                            <img id="seo-preview-image" src="#"
                                                class="max-h-56 rounded-xl shadow-lg border-4 border-white mx-auto">
                                            <button type="button" id="remove-seo-img"
                                                class="absolute -top-3 -right-3 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg hover:bg-red-600 border-2 border-white transition-all">
                                                <span class="material-icons text-sm">close</span>
                                            </button>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <!-- Content Editor -->
                            <div class="bg-surface-container-lowest rounded-3xl shadow-sm border border-outline-variant/10 overflow-hidden ring-2 ring-primary/40"
                                id="rich-text-editor-container">

                                <textarea
                                    class="w-full min-h-[600px] p-10 bg-white text-lg text-on-surface-variant placeholder:text-surface-dim placeholder:italic border-none focus:ring-0 resize-none outline-none"
                                    placeholder="Start writing your story here..." id="editor" name="content">
                                </textarea>
                            </div>
                            <!-- SEO Meta Section -->
                            <!-- <div class="seo-meta-card bg-white p-8 rounded-3xl shadow-sm border border-purple-50 mt-8">
                            <div class="flex items-center gap-2 mb-6">
                                <span class="material-icons text-purple-500">search_check</span>
                                <h2 class="text-lg font-bold text-gray-700">Blog SEO Meta</h2>
                            </div>

                            <div class="mb-6">
                                <div class="flex justify-between mb-2">
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Meta
                                        Title</label>
                                    <span id="meta-title-count" class="text-[10px] text-gray-400">0 / 70
                                        characters</span>
                                </div>
                                <input type="text" id="meta-title-input" name="meta_title"
                                    placeholder="SEO Title for search engines"
                                    class="w-full bg-purple-50/30 border border-purple-100 text-sm rounded-xl py-4 px-5 focus:outline-none focus:ring-2 focus:ring-purple-200 transition-all">
                            </div>

                            <div class="mb-6">
                                <div class="flex justify-between mb-2">
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Meta
                                        Description</label>
                                    <span id="meta-desc-count" class="text-[10px] text-gray-400">0 / 160
                                        characters</span>
                                </div>
                                <textarea id="meta-desc-input" name="meta_description" rows="3"
                                    placeholder="Brief summary for search engine results..."
                                    class="w-full bg-purple-50/30 border border-purple-100 text-sm rounded-xl py-4 px-5 focus:outline-none focus:ring-2 focus:ring-purple-200 transition-all resize-none"></textarea>
                            </div>

                            <div class="mb-2">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 mb-3 uppercase tracking-widest">Meta
                                    Image (Social Sharing)</label>

                                <label for="seo_image_input"
                                    class="relative border-2 border-dashed border-purple-100 rounded-2xl p-8 text-center cursor-pointer block bg-purple-50/10 hover:bg-purple-50/40 transition-all group overflow-hidden">
                                    <input type="file" name="meta_image" id="seo_image_input" accept="image/*" hidden>

                                    <div id="seo-placeholder" class="flex flex-col items-center">
                                        <div
                                            class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-3">
                                            <span class="material-icons text-purple-500">add_photo_alternate</span>
                                        </div>
                                        <p class="text-sm text-gray-600 font-medium">Drop image here or click to upload
                                        </p>
                                        <p class="text-[10px] text-gray-400 uppercase mt-1 tracking-widest font-bold">
                                            Recommended: 1200 x 630px</p>
                                    </div>

                                    <div id="seo-preview-wrapper" class="hidden relative inline-block group">
                                        <img id="seo-preview-image" src="#"
                                            class="max-h-56 rounded-xl shadow-lg border-4 border-white mx-auto">
                                        <button type="button" id="remove-seo-img"
                                            class="absolute -top-3 -right-3 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg hover:bg-red-600 border-2 border-white transition-all">
                                            <span class="material-icons text-sm">close</span>
                                        </button>
                                    </div>
                                </label>
                            </div>
                        </div> -->
                        </div>
                        <!-- Right Column: Sidebar Panels -->
                        <div class="col-span-12 lg:col-span-4 space-y-6">
                            <!-- Publish Panel -->
                            <div
                                class="publish-status-card bg-white p-5 rounded-2xl shadow-sm border border-purple-50 mt-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Publish
                                        Status
                                    </h3>
                                    <span id="status-badge"
                                        class="bg-yellow-100 text-yellow-600 text-[10px] px-2 py-1 rounded-full font-bold uppercase">Draft</span>
                                </div>

                                <div class="space-y-3 mb-6">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <span class="material-icons text-gray-400 mr-2"
                                            style="font-size: 18px;">visibility</span>
                                        <span>Visibility: <strong class="text-purple-600 ml-1">Public</strong></span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <span class="material-icons text-gray-400 mr-2"
                                            style="font-size: 18px;">event</span>
                                        <span>Publish: <strong class="ml-1">Immediately</strong></span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <button type="submit" name="save_draft" onclick="dra()"
                                        class="py-2.5 px-4 bg-gray-50 text-gray-500 rounded-xl font-bold text-xs hover:bg-gray-100 transition-all border border-gray-100">
                                        SAVE DRAFT
                                    </button>
                                    <button type="submit" name="publish_post" onclick="pub()"
                                        class="py-2.5 px-4 bg-purple-600 text-white rounded-xl font-bold text-xs hover:bg-purple-700 shadow-md shadow-purple-100 transition-all">
                                        PUBLISH
                                    </button>
                                </div>
                                <input type="hidden" name="status" id="status">
                                <div class="mt-4 pt-4 border-t border-gray-50 flex justify-center">
                                    <button type="button" onclick="window.location.reload();"
                                        class="text-red-400 text-[10px] font-bold uppercase hover:text-red-600 flex items-center transition-all">
                                        <span class="material-icons text-sm mr-1">delete_outline</span>
                                        Move to Trash
                                    </button>
                                </div>
                            </div>
                            <!-- Featured Status -->

                            <!-- <div
                            class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm border border-outline-variant/10">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant">Featured
                                    Post</h3>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input class="sr-only peer" type="checkbox" />
                                    <div
                                        class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                    </div>
                                </label>
                            </div>
                            <p class="text-[10px] text-on-surface-variant/60 mt-2">Make this post sticky or highlight in
                                hero sections.</p>
                        </div> -->

                            <!-- Categories Panel -->
                            <div
                                class="categories-section bg-white p-5 rounded-2xl shadow-sm border border-purple-50 mt-4">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 mb-3 uppercase tracking-widest">Blog
                                    Categories</label>

                                <div class="relative mb-4">
                                    <select name="category_id"
                                        class="w-full bg-purple-50/30 border border-purple-100 text-gray-700 text-xs rounded-xl py-3 px-4 appearance-none focus:outline-none focus:ring-2 focus:ring-purple-200 transition-all cursor-pointer">
                                        <option value="" disabled selected>Select a Category</option>
                                        <?php
                                            while($cat = mysqli_fetch_assoc($cat_result)){?>
                                        <option value="<?=$cat['id']?>"><?=$cat['name']?>        <=</option>
                                        <?php }?>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-purple-400">
                                        <span class="material-icons text-sm">expand_more</span>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-gray-50">
                                    <button type="button" id="add-cat-toggle"
                                        class="flex items-center text-purple-600 hover:text-purple-700 transition-all group focus:outline-none no-underline">
                                        <div
                                            class="w-7 h-7 bg-purple-50 rounded-lg flex items-center justify-center mr-2 group-hover:bg-purple-100 transition-colors border border-purple-100">
                                            <span class="material-icons text-sm" id="toggle-icon">add</span>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase tracking-widest"
                                            id="toggle-text">Add
                                            New Category</span>
                                    </button>

                                    <div id="new-cat-input-wrapper" class="hidden mt-4 animate-fade-in">
                                        <div
                                            class="flex flex-col sm:flex-row items-stretch gap-2 p-1.5 bg-gray-50 rounded-xl border border-gray-200">
                                            <input type="text" id="new_category_name" placeholder="Name..."
                                                class="flex-1 min-w-0 bg-transparent text-xs py-2 px-3 focus:outline-none border-none">
                                            <button type="button"
                                                class="bg-purple-600 text-white px-4 py-2 rounded-lg text-[10px] font-bold hover:bg-purple-700 transition-all shadow-sm uppercase tracking-tighter shrink-0">
                                                ADD
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tags Panel -->
                            <div class="tags-section bg-white p-5 rounded-2xl shadow-sm border border-purple-50 mt-4">
                                <div class="flex justify-between items-center mb-3">
                                    <label
                                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Blog
                                        Tags</label>
                                    <button type="button" id="remove-all-tags"
                                        class="text-[9px] font-bold text-red-400 hover:text-red-600 uppercase tracking-tighter transition-all focus:outline-none">
                                        Clear All
                                    </button>
                                </div>

                                <div class="relative mb-4">
                                    <input type="text" id="tag-input"
                                        placeholder="Type or paste tags (comma separated)..."
                                        class="w-full bg-purple-50/30 border border-purple-100 text-xs rounded-xl py-3 pl-4 pr-12 focus:outline-none focus:ring-2 focus:ring-purple-200 transition-all">
                                    <button type="button" id="add-tag-btn"
                                        class="absolute right-2 top-1.5 w-8 h-8 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all shadow-md flex items-center justify-center">
                                        <span class="material-icons" style="font-size: 18px;">auto_awesome_motion</span>
                                    </button>
                                </div>


                                <div id="tags-container" class="flex flex-wrap gap-2"></div>
                                <div id="tags-hidden-inputs"></div>
                            </div>
                            <!-- Blog Image Panel -->
                            <!-- <div
                            class="featured-image-section mt-6 p-4 bg-white rounded-2xl shadow-sm border border-purple-50">
                            <div class="flex justify-between items-center mb-3">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Featured
                                    Image</label>
                                <div id="featured-actions" class="hidden flex gap-2">
                                    <span
                                        class="material-icons text-gray-400 cursor-pointer hover:text-purple-500 text-sm">sync</span>
                                </div>
                            </div>

                            <label for="featured_image_input" class="relative group cursor-pointer block">
                                <input type="file" name="featured_image" id="featured_image_input" accept="image/*"
                                    hidden>

                                <div id="featured-placeholder"
                                    class="border-2 border-dashed border-purple-100 rounded-xl p-6 text-center bg-purple-50/10 group-hover:bg-purple-50/50 transition-all">
                                    <span class="material-icons text-purple-300 text-3xl">add_a_photo</span>
                                    <p class="text-[10px] text-gray-400 mt-2 font-medium">Click to set featured image
                                    </p>
                                </div>

                                <div id="featured-preview-wrapper"
                                    class="hidden relative overflow-hidden rounded-xl shadow-md border-2 border-white">
                                    <img id="featured-preview-image" src="#" class="w-full h-40 object-cover">

                                    <button type="button" id="remove-featured-btn"
                                        class="absolute top-2 right-2 bg-white/90 text-red-500 rounded-full w-7 h-7 flex items-center justify-center shadow-md hover:bg-red-500 hover:text-white transition-all">
                                        <span class="material-icons text-xs">close</span>
                                    </button>

                                    <div
                                        class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <span
                                            class="text-white text-[10px] font-bold uppercase tracking-tighter bg-black/40 px-3 py-1 rounded-full backdrop-blur-sm">Replace
                                            Image</span>
                                    </div>
                                </div>
                            </label>
                        </div> -->
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
    <script>
        tinymce.init({
            selector: '#editor',
            license_key: 'gpl',
            plugins: 'autoresize lists link image table code preview',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright justify | bullist numlist | forecolor backcolor | link image table | code preview',
            image_title: true,
            automatic_upload: true,
            setup: function (editor) {

                editor.on('change', function () {
                    editor.save();
                });

            },
            file_picker_types: 'image',
            file_picker_callback: (cb, value, meta) => {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = function () {
                    const file = this.files[0];
                    const reader = new FileReader();
                    reader.onload = function () {
                        const id = 'blobid' + (new Date()).getTime();
                        const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                        const base64 = reader.result.split(',')[1];
                        const blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);
                        cb(blobInfo.blobUri(), { title: file.name });
                    };
                    reader.readAsDataURL(file);
                };
                input.click();
            },
            content_style: "body { font-family:Arial; font-size:14px }",
            autoresize_bottom_margin: 20,
            min_height: 300,
            max_height: 1000
        });
        function dra() {
            document.getElementById('status').value = 'draft';
        }
        function pub() {
            document.getElementById('status').value = 'published';
        }

        // const metaInput = document.getElementById('meta_image_input');
        // const placeholder = document.getElementById('upload-placeholder');
        // const previewWrapper = document.getElementById('image-preview-wrapper');
        // const previewImage = document.getElementById('meta-preview-image');
        // const removeBtn = document.getElementById('remove-img-btn');

        //Image Preview Logic
        // metaInput.addEventListener('change', function (e) {
        //     const file = this.files[0];
        //     if (file) {
        //         const reader = new FileReader();
        //         reader.onload = function (e) {
        //             previewImage.src = e.target.result;
        //             placeholder.classList.add('hidden');
        //             previewWrapper.classList.remove('hidden');
        //         }
        //         reader.readAsDataURL(file);
        //     }
        // });

        // Remove/Cross Button Logic
        // removeBtn.addEventListener('click', function (e) {
        //     e.preventDefault();
        //     e.stopPropagation();

        //     metaInput.value = "";
        //     previewImage.src = "#";

        // UI Reset
        //     placeholder.classList.remove('hidden');
        //     previewWrapper.classList.add('hidden');
        // });
        // const featuredInput = document.getElementById('featured_image_input');
        // const featuredPlaceholder = document.getElementById('featured-placeholder');
        // const featuredWrapper = document.getElementById('featured-preview-wrapper');
        // const featuredImage = document.getElementById('featured-preview-image');
        // const removeFeaturedBtn = document.getElementById('remove-featured-btn');

        //Featured Image Preview
        // featuredInput.addEventListener('change', function (e) {
        //     const file = this.files[0];
        //     if (file) {
        //         const reader = new FileReader();
        //         reader.onload = function (e) {
        //             featuredImage.src = e.target.result;
        //             featuredPlaceholder.classList.add('hidden');
        //             featuredWrapper.classList.remove('hidden');
        //         }
        //         reader.readAsDataURL(file);
        //     }
        // });

        //Remove Featured Image
        // removeFeaturedBtn.addEventListener('click', function (e) {
        //     e.preventDefault();
        //     e.stopPropagation();

        //     featuredInput.value = "";
        //     featuredImage.src = "#";

        //     featuredPlaceholder.classList.remove('hidden');
        //     featuredWrapper.classList.add('hidden');
        // });


        //<!-- Categories Panel -->
        document.getElementById('add-cat-toggle').addEventListener('click', function () {
            const wrapper = document.getElementById('new-cat-input-wrapper');
            const icon = document.getElementById('toggle-icon');
            const text = document.getElementById('toggle-text');

            wrapper.classList.toggle('hidden');

            if (!wrapper.classList.contains('hidden')) {
                icon.innerText = 'close';
                text.innerText = 'Cancel';
                this.classList.replace('text-purple-600', 'text-red-400');
            } else {
                icon.innerText = 'add';
                text.innerText = 'Add New Category';
                this.classList.replace('text-red-400', 'text-purple-600');
            }
        });


        //<!-- Tags Panel -->
        const tagInput = document.getElementById('tag-input');
        const tagsContainer = document.getElementById('tags-container');
        const addTagBtn = document.getElementById('add-tag-btn');
        const removeAllBtn = document.getElementById('remove-all-tags');
        function createTagUI(label) {
            const value = label.trim();
            if (value === "") return;

            // Duplicate check
            const existing = [...tagsContainer.querySelectorAll('span.tracking-wide')]
                .map(s => s.innerText.toLowerCase());
            if (existing.includes(value.toUpperCase())) return;

            const tag = document.createElement('div');
            tag.className = "flex items-center bg-purple-50 text-purple-600 text-[10px] font-bold px-3 py-1.5 rounded-lg border border-purple-100 animate-fade-in group";
            tag.dataset.tagValue = value; 

            tag.innerHTML = `
        <span class="tracking-wide">${value.toUpperCase()}</span>
        <button type="button" class="remove-tag ml-2 flex items-center text-purple-300 hover:text-red-500 transition-colors">
            <span class="material-icons" style="font-size: 14px;">close</span>
        </button>
    `;

            // Hidden input create
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'tags[]';      
            hiddenInput.value = value;
            hiddenInput.id = 'tag-hidden-' + value.replace(/\s+/g, '-');
            document.getElementById('tags-hidden-inputs').appendChild(hiddenInput);

            // Remove button — UI + hidden input 
            tag.querySelector('.remove-tag').onclick = (e) => {
                e.stopPropagation();
                const inp = document.getElementById('tag-hidden-' + value.replace(/\s+/g, '-'));
                if (inp) inp.remove();
                tag.remove();
            };

            tagsContainer.appendChild(tag);
        }


        function processInput() {
            const rawValue = tagInput.value;
            if (rawValue.includes(',')) {
                const parts = rawValue.split(',');
                parts.slice(0, -1).forEach(part => createTagUI(part));
                tagInput.value = parts[parts.length - 1];
            }
        }
        function forceAddTags() {
            const parts = tagInput.value.split(',');
            parts.forEach(part => createTagUI(part));
            tagInput.value = "";
        }


        tagInput.addEventListener('input', processInput);


        addTagBtn.onclick = (e) => {
            e.preventDefault();
            forceAddTags();
        };


        tagInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                forceAddTags();
            }
        });


        removeAllBtn.onclick = () => {
            if (tagsContainer.children.length > 0) {
                if (confirm("Delete all tags?")) {
                    tagsContainer.innerHTML = "";
                }
            }
        };



        //<!-- Post Basic Info -->
        const titleInput = document.getElementById('post-title');
        const slugDisplay = document.getElementById('permalink-slug');
        // const editSlugBtn = document.getElementById('edit-slug-btn');
        const shortDesc = document.getElementById('short-desc');
        const charCount = document.getElementById('char-count');

        // 1. Auto-generate Slug from Title
        titleInput.addEventListener('input', function () {
            let slug = this.value
                .toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');

            slugDisplay.innerText = slug || 'your-post-title';
            document.getElementById('slug').value = slug;
        });

        // 2. Manual Slug Edit
        // editSlugBtn.addEventListener('click', function () {
        //     const currentSlug = slugDisplay.innerText;
        //     const newSlug = prompt("Edit Permalink Slug:", currentSlug);
        //     if (newSlug) {
        //         slugDisplay.innerText = newSlug.toLowerCase().replace(/ +/g, '-');
        //     }
        // });

        // 3. Character Counter for Short Description
        shortDesc.addEventListener('input', function () {
            const length = this.value.length;
            charCount.innerText = `${length} / 160 characters`;
            if (length >= 150) {
                charCount.classList.replace('text-gray-400', 'text-red-400');
            } else {
                charCount.classList.replace('text-red-400', 'text-gray-400');
            }
        });



        // <!-- SEO Meta Section -->
        // const metaTitle = document.getElementById('meta-title-input');
        // const metaDesc = document.getElementById('meta-desc-input');
        // const titleCount = document.getElementById('meta-title-count');
        // const descCount = document.getElementById('meta-desc-count');

        const seoInput = document.getElementById('seo_image_input');
        const seoPlaceholder = document.getElementById('seo-placeholder');
        const seoPreviewWrapper = document.getElementById('seo-preview-wrapper');
        const seoPreviewImage = document.getElementById('seo-preview-image');
        const removeSeoBtn = document.getElementById('remove-seo-img');

        // 1. Meta Title Character Counter
        // metaTitle.addEventListener('input', function () {
        //     const len = this.value.length;
        //     titleCount.innerText = `${len} / 70 characters`;
        //     titleCount.style.color = len > 70 ? '#f87171' : '#9ca3af'; // Red if over limit
        // });

        // 2. Meta Description Character Counter
        // metaDesc.addEventListener('input', function () {
        //     const len = this.value.length;
        //     descCount.innerText = `${len} / 160 characters`;
        //     descCount.style.color = len > 160 ? '#f87171' : '#9ca3af';
        // });

        // 3. Social Image Preview Logic
        seoInput.addEventListener('change', function () {
            const file1 = this.files[0];
            if (file1) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    seoPreviewImage.src = e.target.result;
                    seoPlaceholder.classList.add('hidden');
                    seoPreviewWrapper.classList.remove('hidden');
                }
                reader.readAsDataURL(file1);
            }
        });

        // 4. Remove Social Image
        removeSeoBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            seoInput.value = "";
            seoPlaceholder.classList.remove('hidden');
            seoPreviewWrapper.classList.add('hidden');
        });

    </script>
</body>

</html>














pages/home.php               

<?php

include "../include/session.php";
requireUser();
include "../config.php";
include "../include/db.php";
include "../include/data_fetch.php";

global $conn;

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

$image = $data['profile_image'];

$posts = latest_posts(20);
?>




<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BlogFusion – User Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
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
        body {
            font-family: 'Public Sans', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .ms-filled {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .page {
            display: none;
            animation: fadeIn .3s ease;
        }

        .page.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .nav-item {
            transition: all .18s;
        }

        .stat-gradient {
            background: linear-gradient(135deg, #630ed4, #7c3aed);
        }

        .toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(80px);
            background: #1d1a24;
            color: #eaddff;
            padding: 12px 24px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
            z-index: 9999;
            transition: transform .3s ease;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .2);
        }

        .toast.show {
            transform: translateX(-50%) translateY(0);
        }

        .blog-view {
            display: none;
            animation: fadeIn .4s ease;
        }

        .blog-view.active {
            display: block;
        }

        .reaction-btn.active {
            background: rgba(99, 14, 212, .12) !important;
            transform: scale(1.1);
        }

        .reaction-btn {
            transition: all .2s;
        }

        .live-dot {
            width: 6px;
            height: 6px;
            background: #630ed4;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .4
            }
        }

        .filter-tab.active {
            color: #630ed4;
            font-weight: 700;
            border-bottom: 2px solid #630ed4;
        }

        .filter-tab {
            transition: all .18s;
            border-bottom: 2px solid transparent;
        }

        .blog-card {
            transition: all .22s;
        }

        .blog-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(99, 14, 212, .1);
        }

        .sidebar-pill.active {
            background: #eaddff;
            color: #5a00c6;
            font-weight: 700;
        }

        input[type=text],
        input[type=email],
        input[type=password],
        input[type=file],
        textarea {
            outline: none;
            transition: border-color .2s;
        }

        input:focus,
        textarea:focus {
            border-color: #630ed4 !important;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #ccc3d8;
            border-radius: 4px;
        }

        .comment-like.liked {
            color: #630ed4 !important;
        }

        .tag-pill {
            transition: all .15s;
            cursor: pointer;
        }

        .tag-pill:hover {
            background: #eaddff;
            color: #5a00c6;
        }

        .tag-pill.active-tag {
            background: #630ed4;
            color: #fff !important;
        }

        .cat-card {
            transition: all .22s;
        }

        .cat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(99, 14, 212, .13);
        }

        .comment-edit-area {
            display: none;
        }

        .comment-edit-area.open {
            display: block;
        }

        html.dark .tag-pill {
            background: #2a2534;
            color: #ccc3d8;
        }

        html.dark .tag-pill:hover {
            background: #3d1a7a;
            color: #d2bbff;
        }

        html.dark .tag-pill.active-tag {
            background: #630ed4;
            color: #fff !important;
        }

        .reply-comment {
            margin-left: 44px;
            border-left: 2px solid rgba(99, 14, 212, .2);
            padding-left: 14px;
        }

        /* ═══════════ DARK MODE ═══════════ */
        html.dark body {
            background: #1d1a24 !important;
            color: #ede5f4;
        }

        html.dark {
            background: #1d1a24;
        }

        html.dark .bg-surface-bright {
            background: #1d1a24 !important;
        }

        html.dark .bg-surface {
            background: #1d1a24 !important;
        }

        html.dark .bg-surface-container-low {
            background: #2a2534 !important;
        }

        html.dark .bg-surface-container-lowest {
            background: #1e1b28 !important;
        }

        html.dark .bg-surface-container-high {
            background: #332f3d !important;
        }

        html.dark .bg-surface-container-highest {
            background: #3d3849 !important;
        }

        html.dark .bg-surface-container {
            background: #241f30 !important;
        }

        html.dark .text-on-surface {
            color: #ede5f4 !important;
        }

        html.dark .text-on-surface-variant {
            color: #ccc3d8 !important;
        }

        html.dark .bg-white {
            background: #2a2534 !important;
        }

        html.dark #topbar-header {
            background: rgba(29, 26, 36, 0.92) !important;
            border-color: rgba(100, 90, 120, 0.25) !important;
        }

        html.dark .border-outline-variant\/10 {
            border-color: rgba(100, 90, 120, 0.25) !important;
        }

        html.dark .border-outline-variant\/20 {
            border-color: rgba(100, 90, 120, 0.3) !important;
        }

        html.dark input[type=text],
        html.dark input[type=email],
        html.dark input[type=password],
        html.dark textarea,
        html.dark select {
            background: rgba(50, 45, 62, 0.8) !important;
            color: #ede5f4 !important;
        }

        html.dark .bg-primary-fixed {
            background: #3d1a7a !important;
        }

        html.dark .text-on-primary-fixed-variant {
            color: #d2bbff !important;
        }

        html.dark .toast {
            background: #eaddff;
            color: #1d1a24;
        }

        html.dark .sidebar-pill.active {
            background: #3d1a7a !important;
            color: #d2bbff !important;
        }

        html.dark .hover\:bg-surface-container-high:hover {
            background: #332f3d !important;
        }

        html.dark .hover\:bg-surface-container:hover {
            background: #241f30 !important;
        }

        html.dark .hover\:bg-surface-container-low:hover {
            background: #2a2534 !important;
        }

        html.dark #profileMenu {
            background: #2a2534 !important;
        }

        html.dark #topbarResults {
            background: #2a2534 !important;
        }

        #sidebarOverlay {
            backdrop-filter: blur(2px);
        }

        @media (max-width: 1023px) {
            .reaction-btn {
                width: 2.75rem;
                height: 2.75rem;
            }
        }

        /* ══════════ SINGLE BLOG POST WIDE MODE ══════════ */
        body.single-blog-active #sidebar {
            display: none !important;
        }

        body.single-blog-active #topbar-header {
            display: none !important;
        }

        body.single-blog-active .lg\:ml-64 {
            margin-left: 0 !important;
        }
    </style>
</head>

<body class="bg-surface text-on-surface">

    <div id="sidebarOverlay" onclick="closeSidebarMobile()" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden">
    </div>

    <div class="toast" id="toast"></div>

    <div class="flex min-h-screen">

        <aside id="sidebar"
            class="w-64 bg-surface-container-low shrink-0 fixed top-0 left-0 h-screen flex flex-col z-40 transition-transform duration-300 -translate-x-full lg:translate-x-0">
            <div class="px-6 py-5 border-b border-outline-variant/20">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl stat-gradient flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-base ms-filled">edit_note</span>
                    </div>
                    <span class="text-lg font-black tracking-tight text-on-surface">Blog<span
                            class="text-primary">Fusion</span></span>
                </div>
                <div class="text-[11px] text-on-surface-variant mt-1 font-medium">User Panel</div>
            </div>

            <div class="px-4 py-4 border-b border-outline-variant/10 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full stat-gradient flex items-center justify-center text-white font-black text-sm shrink-0">
                    AV</div>
                <div>
                    <div class="font-bold text-sm text-on-surface">Avijit Roy</div>
                    <div
                        class="text-[11px] px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed-variant font-bold inline-block mt-0.5">
                        Reader</div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto no-scrollbar py-4 px-3 space-y-0.5">
                <p class="text-[10px] font-black tracking-widest uppercase text-on-surface-variant px-3 py-2">Main</p>
                <button onclick="showPage('dashboard',this);closeSidebarMobile()"
                    class="nav-item sidebar-pill active w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-lg">grid_view</span> Dashboard
                </button>
                <button onclick="showPage('profile',this);closeSidebarMobile()"
                    class="nav-item sidebar-pill w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-lg">person</span> Profile
                </button>
                <button onclick="showPage('blogs',this);closeSidebarMobile()"
                    class="nav-item sidebar-pill w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-lg">article</span> Blogs
                </button>
                <button onclick="showPage('categories',this);renderCategories();closeSidebarMobile()"
                    class="nav-item sidebar-pill w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-lg">category</span> Categories
                </button>
                <button onclick="showPage('saved',this);closeSidebarMobile()"
                    class="nav-item sidebar-pill w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-lg">bookmark</span> Saved Posts
                </button>

                <p class="text-[10px] font-black tracking-widest uppercase text-on-surface-variant px-3 py-2 mt-2">
                    Account</p>
                <button onclick="showPage('notifications',this);closeSidebarMobile()"
                    class="nav-item sidebar-pill w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-lg">notifications</span> Notifications
                    <span
                        class="ml-auto bg-tertiary text-white text-[10px] font-black px-2 py-0.5 rounded-full">3</span>
                </button>
                <button onclick="showPage('settings',this);closeSidebarMobile()"
                    class="nav-item sidebar-pill w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-on-surface-variant hover:bg-surface-container-high">
                    <span class="material-symbols-outlined text-lg">settings</span> Settings
                </button>
            </nav>

            <div class="px-3 py-4 border-t border-outline-variant/10">
                <button onclick="logout()"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-full text-sm text-error hover:bg-error-container/30 transition-colors">
                    <span class="material-symbols-outlined text-lg">logout</span> Logout
                </button>
            </div>
        </aside>

        <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">

            <header id="topbar-header"
                class="sticky top-0 z-20 bg-surface-container-lowest/90 backdrop-blur-xl border-b border-outline-variant/10 px-4 md:px-8 py-3 flex items-center gap-3">
                <button onclick="toggleSidebar()"
                    class="lg:hidden w-9 h-9 flex items-center justify-center rounded-xl hover:bg-surface-container transition-colors shrink-0">
                    <span class="material-symbols-outlined text-on-surface-variant">menu</span>
                </button>

                <span class="lg:hidden font-black tracking-tight text-on-surface text-base">Blog<span
                        class="text-primary">Fusion</span></span>

                <div class="relative flex-1 max-w-md hidden sm:block">
                    <span
                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                    <input id="topbarSearch" type="text" placeholder="Search posts, topics..."
                        class="w-full pl-10 pr-4 py-2 bg-surface-container-highest/60 rounded-xl text-sm text-on-surface placeholder-on-surface-variant border border-transparent focus:border-primary/30"
                        oninput="handleTopbarSearch(this.value)"
                        onkeydown="if(event.key==='Enter')goSearchBlogs(this.value)" />
                    <div id="topbarResults"
                        class="hidden absolute top-full mt-2 left-0 right-0 bg-white rounded-2xl shadow-2xl border border-outline-variant/20 z-50 overflow-hidden">
                    </div>
                </div>

                <div class="ml-auto flex items-center gap-1.5 md:gap-2">
                    <button
                        class="sm:hidden w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors"
                        onclick="showToast('Use the blog search below')">
                        <span class="material-symbols-outlined text-on-surface-variant text-xl">search</span>
                    </button>
                    <button onclick="toggleDark()" id="darkToggleTopbar"
                        class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors"
                        title="Toggle dark mode">
                        <span class="material-symbols-outlined text-on-surface-variant text-xl"
                            id="darkIconTopbar">dark_mode</span>
                    </button>
                    <button onclick="showPage('notifications',null)"
                        class="relative w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined text-on-surface-variant text-xl">notifications</span>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-tertiary rounded-full"></span>
                    </button>
                    <div class="relative">
                        <button onclick="toggleProfileMenu()"
                            class="flex items-center gap-1.5 pl-2 pr-3 py-1.5 rounded-full hover:bg-surface-container transition-colors">
                            <div
                                class="w-7 h-7 rounded-full stat-gradient flex items-center justify-center text-white text-xs font-black">
                                AV</div>
                            <span class="text-sm font-semibold text-on-surface hidden md:inline">Avijit</span>
                            <span class="material-symbols-outlined text-on-surface-variant text-base">expand_more</span>
                        </button>
                        <div id="profileMenu"
                            class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl shadow-2xl border border-outline-variant/20 p-2 z-50">
                            <button onclick="showPage('profile',null);closeProfileMenu()"
                                class="w-full text-left px-3 py-2 rounded-xl hover:bg-surface-container text-sm flex items-center gap-2 text-on-surface transition-colors"><span
                                    class="material-symbols-outlined text-base">person</span> Profile</button>
                            <button onclick="showPage('settings',null);closeProfileMenu()"
                                class="w-full text-left px-3 py-2 rounded-xl hover:bg-surface-container text-sm flex items-center gap-2 text-on-surface transition-colors"><span
                                    class="material-symbols-outlined text-base">settings</span> Settings</button>
                            <hr class="border-outline-variant/20 my-1" />
                            <button onclick="logout();closeProfileMenu()"
                                class="w-full text-left px-3 py-2 rounded-xl hover:bg-error-container/20 text-sm text-error flex items-center gap-2 transition-colors"><span
                                    class="material-symbols-outlined text-base">logout</span> Logout</button>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-5 md:px-8 md:py-8 bg-surface-bright">

                <div class="page active" id="page-dashboard">
                    <div class="mb-6 md:mb-8">
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Welcome back, Avijit
                            👋</h1>
                        <p class="text-on-surface-variant mt-1">Here's what's happening with your account.</p>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6 md:mb-8">
                        <div class="bg-surface-container-lowest rounded-2xl p-4 md:p-5 relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-0.5 bg-primary"></div>
                            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl mb-2 md:mb-3 flex items-center justify-center"
                                style="background:rgba(99,14,212,.1)">
                                <span class="material-symbols-outlined text-primary text-lg">bookmark</span>
                            </div>
                            <div class="text-2xl md:text-3xl font-black text-on-surface">24</div>
                            <div class="text-xs text-on-surface-variant mt-1 font-medium">Saved Posts</div>
                        </div>
                        <div class="bg-surface-container-lowest rounded-2xl p-4 md:p-5 relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-0.5 bg-secondary"></div>
                            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl mb-2 md:mb-3 flex items-center justify-center"
                                style="background:rgba(75,65,225,.1)">
                                <span class="material-symbols-outlined text-secondary text-lg">chat_bubble</span>
                            </div>
                            <div class="text-2xl md:text-3xl font-black text-on-surface">58</div>
                            <div class="text-xs text-on-surface-variant mt-1 font-medium">Total Comments</div>
                        </div>
                        <div class="bg-surface-container-lowest rounded-2xl p-4 md:p-5 relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-0.5 bg-tertiary"></div>
                            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl mb-2 md:mb-3 flex items-center justify-center"
                                style="background:rgba(155,0,92,.1)">
                                <span class="material-symbols-outlined text-tertiary text-lg">favorite</span>
                            </div>
                            <div class="text-2xl md:text-3xl font-black text-on-surface">132</div>
                            <div class="text-xs text-on-surface-variant mt-1 font-medium">Total Reactions</div>
                        </div>
                        <div class="bg-surface-container-lowest rounded-2xl p-4 md:p-5 relative overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-0.5" style="background:#f0a500"></div>
                            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl mb-2 md:mb-3 flex items-center justify-center"
                                style="background:rgba(240,165,0,.1)">
                                <span class="material-symbols-outlined text-lg"
                                    style="color:#f0a500">notifications_active</span>
                            </div>
                            <div class="text-2xl md:text-3xl font-black text-on-surface">3</div>
                            <div class="text-xs text-on-surface-variant mt-1 font-medium">Unread Notifications</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
                        <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl p-5 md:p-6">
                            <h3 class="font-black text-base mb-4 md:mb-5 text-on-surface">Recent Activity</h3>
                            <div class="space-y-3 md:space-y-4">
                                <div
                                    class="flex items-start gap-3 p-3 rounded-xl hover:bg-surface-container-low transition-colors">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
                                        style="background:rgba(99,14,212,.1)"><span
                                            class="material-symbols-outlined text-primary text-base">bookmark_added</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-semibold text-on-surface">You saved <span
                                                class="text-primary">"How to Learn PHP"</span></div>
                                        <div class="text-xs text-on-surface-variant mt-0.5">2 minutes ago</div>
                                    </div>
                                </div>
                                <div
                                    class="flex items-start gap-3 p-3 rounded-xl hover:bg-surface-container-low transition-colors">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
                                        style="background:rgba(75,65,225,.1)"><span
                                            class="material-symbols-outlined text-secondary text-base">chat</span></div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-semibold text-on-surface">You commented on <span
                                                class="text-primary">"Blog Design Tips"</span></div>
                                        <div class="text-xs text-on-surface-variant mt-0.5">1 hour ago</div>
                                    </div>
                                </div>
                                <div
                                    class="flex items-start gap-3 p-3 rounded-xl hover:bg-surface-container-low transition-colors">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
                                        style="background:rgba(155,0,92,.1)"><span
                                            class="material-symbols-outlined text-tertiary text-base">favorite</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-semibold text-on-surface">You reacted ❤️ to <span
                                                class="text-primary">"Single Post UI"</span></div>
                                        <div class="text-xs text-on-surface-variant mt-0.5">3 hours ago</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="stat-gradient rounded-2xl p-5 md:p-6 text-white">
                                <div class="font-black text-lg mb-1">Explore Blogs</div>
                                <p class="text-primary-fixed/80 text-sm mb-4">Discover the latest posts from top
                                    authors.</p>
                                <button onclick="showPage('blogs',null)"
                                    class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-sm font-bold transition-colors border border-white/20">Browse
                                    All Posts →</button>
                            </div>
                            <div class="bg-surface-container-lowest rounded-2xl p-5">
                                <h3 class="font-black text-xs mb-3 text-on-surface-variant uppercase tracking-widest">
                                    Quick Links</h3>
                                <div class="space-y-1">
                                    <button onclick="showPage('saved',null)"
                                        class="w-full text-left px-3 py-2 rounded-xl hover:bg-surface-container text-sm flex items-center gap-2 text-on-surface transition-colors"><span
                                            class="material-symbols-outlined text-base text-primary">bookmark</span> My
                                        Saved Posts</button>
                                    <button onclick="showPage('profile',null)"
                                        class="w-full text-left px-3 py-2 rounded-xl hover:bg-surface-container text-sm flex items-center gap-2 text-on-surface transition-colors"><span
                                            class="material-symbols-outlined text-base text-secondary">person</span> My
                                        Profile</button>
                                    <button onclick="showPage('notifications',null)"
                                        class="w-full text-left px-3 py-2 rounded-xl hover:bg-surface-container text-sm flex items-center gap-2 text-on-surface transition-colors"><span
                                            class="material-symbols-outlined text-base text-tertiary">notifications</span>
                                        Notifications</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page" id="page-profile">
                    <div class="mb-6 md:mb-8">
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">My Profile</h1>
                        <p class="text-on-surface-variant mt-1">Your personal information at a glance.</p>
                    </div>
                    <div
                        class="bg-surface-container-lowest rounded-3xl p-6 md:p-8 mb-6 flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-8">
                        <div class="relative shrink-0">
                            <div
                                class="w-20 h-20 md:w-24 md:h-24 rounded-full stat-gradient flex items-center justify-center text-white text-2xl md:text-3xl font-black ring-4 ring-surface-container-low">
                                AV</div>
                            <div
                                class="absolute bottom-0 right-0 w-6 h-6 md:w-7 md:h-7 bg-green-500 rounded-full border-2 border-white">
                            </div>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h2 class="text-xl md:text-2xl font-black tracking-tight text-on-surface">Avijit Roy</h2>
                            <p class="text-on-surface-variant mt-1">avijit@example.com</p>
                            <div class="flex items-center gap-2 mt-3 justify-center md:justify-start flex-wrap">
                                <span
                                    class="px-3 py-1 rounded-full bg-primary-fixed text-on-primary-fixed-variant text-xs font-bold">Reader</span>
                                <span
                                    class="px-3 py-1 rounded-full bg-surface-container text-on-surface-variant text-xs font-medium">Joined
                                    Jan 2024</span>
                                <span class="px-3 py-1 rounded-full text-xs font-medium"
                                    style="background:rgba(34,197,94,.1);color:#16a34a">● Active</span>
                            </div>
                            <div class="flex flex-wrap gap-3 mt-5 md:mt-6 justify-center md:justify-start">
                                <button onclick="showPage('edit-profile',null)"
                                    class="flex items-center gap-2 px-4 md:px-5 py-2.5 stat-gradient text-white rounded-xl text-sm font-bold hover:opacity-90 transition-opacity active:scale-95">
                                    <span class="material-symbols-outlined text-base">edit</span> Edit Profile
                                </button>
                                <button onclick="showPage('change-password',null)"
                                    class="flex items-center gap-2 px-4 md:px-5 py-2.5 bg-surface-container text-on-surface rounded-xl text-sm font-bold hover:bg-surface-container-high transition-colors active:scale-95">
                                    <span class="material-symbols-outlined text-base">lock</span> Change Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page" id="page-edit-profile">
                    <div class="mb-6 md:mb-8 flex items-center gap-3">
                        <button onclick="showPage('profile',null)"
                            class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-surface-container transition-colors shrink-0">
                            <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
                        </button>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Edit Profile</h1>
                            <p class="text-on-surface-variant mt-0.5">Update your personal information.</p>
                        </div>
                    </div>
                    <div class="max-w-2xl bg-surface-container-lowest rounded-3xl p-6 md:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">First
                                    Name</label>
                                <input type="text" value="Avijit"
                                    class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Last
                                    Name</label>
                                <input type="text" value="Roy"
                                    class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface" />
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3 mt-6 md:mt-8">
                            <button onclick="showToast('Profile updated successfully!')"
                                class="flex items-center gap-2 px-6 py-2.5 stat-gradient text-white rounded-xl text-sm font-bold hover:opacity-90 transition-opacity active:scale-95">
                                <span class="material-symbols-outlined text-base">check</span> Save Changes
                            </button>
                        </div>
                    </div>
                </div>

                <div class="page" id="page-change-password">
                    <div class="mb-6 md:mb-8 flex items-center gap-3">
                        <button onclick="showPage('profile',null)"
                            class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-surface-container transition-colors shrink-0">
                            <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
                        </button>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Change Password
                            </h1>
                        </div>
                    </div>
                    <div class="max-w-md bg-surface-container-lowest rounded-3xl p-6 md:p-8 space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">New
                                Password</label>
                            <input type="password" id="newPwd" placeholder="••••••••"
                                oninput="checkStrength(this.value)"
                                class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface" />
                            <div class="h-1.5 rounded-full bg-surface-container-high overflow-hidden mt-2">
                                <div id="sfill" class="h-full rounded-full transition-all duration-300"
                                    style="width:0%"></div>
                            </div>
                            <p id="stext" class="text-xs text-on-surface-variant"></p>
                        </div>
                        <button onclick="showToast('Password updated successfully!')"
                            class="w-full py-3 stat-gradient text-white rounded-xl font-bold text-sm hover:opacity-90 transition-opacity active:scale-95">Update
                            Password</button>
                        <p class="text-center text-xs text-on-surface-variant pt-1">Forgot your current password?
                            <button onclick="showPage('forgot-password',null)"
                                class="text-primary font-bold hover:underline">Reset via Email</button></p>
                    </div>
                </div>

                <div class="page" id="page-blogs">
                    <div class="mb-5 md:mb-6 flex flex-col md:flex-row md:items-center gap-3 md:gap-4">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Blogs</h1>
                            <p class="text-on-surface-variant mt-0.5">Browse and read all published posts.</p>
                        </div>
                        <div class="relative md:ml-auto w-full md:w-72">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                            <input id="blogSearch" type="text" placeholder="Search blogs..."
                                class="w-full pl-10 pr-4 py-2.5 bg-surface-container-highest/60 rounded-xl text-sm border border-transparent text-on-surface"
                                oninput="filterBlogs()" />
                        </div>
                    </div>

                    <div
                        class="flex gap-0.5 flex-wrap mb-3 border-b border-outline-variant/20 pb-1 overflow-x-auto no-scrollbar">
                        <button
                            class="filter-tab active px-3 md:px-4 py-2 text-sm font-semibold text-on-surface-variant whitespace-nowrap"
                            data-cat="all" onclick="setCat('all',this)">All</button>
                        <button
                            class="filter-tab px-3 md:px-4 py-2 text-sm font-semibold text-on-surface-variant whitespace-nowrap"
                            data-cat="design" onclick="setCat('design',this)">Design</button>
                        <button
                            class="filter-tab px-3 md:px-4 py-2 text-sm font-semibold text-on-surface-variant whitespace-nowrap"
                            data-cat="php" onclick="setCat('php',this)">PHP</button>
                        <button
                            class="filter-tab px-3 md:px-4 py-2 text-sm font-semibold text-on-surface-variant whitespace-nowrap"
                            data-cat="css" onclick="setCat('css',this)">CSS</button>
                    </div>

                    <!-- Tag filter row -->
                    <div class="flex flex-wrap gap-2 mb-5 items-center">
                        <span
                            class="text-[11px] font-black uppercase tracking-widest text-on-surface-variant">Tags:</span>
                        <div id="tagFilterRow" class="flex flex-wrap gap-1.5"></div>
                        <button id="clearTagBtn" onclick="clearTag()"
                            class="hidden text-xs text-primary font-bold hover:underline ml-1">✕ Clear tag</button>
                    </div>

                    <div id="blogGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5"></div>
                    <p id="noResults" class="hidden text-center text-on-surface-variant py-16 text-sm">No posts found
                        matching your search.</p>
                </div>

                <div class="blog-view" id="blog-post-view">
                    <button onclick="closeBlogView()"
                        class="flex items-center gap-2 mb-5 md:mb-6 text-sm font-bold text-primary hover:text-primary-container transition-colors">
                        <span class="material-symbols-outlined text-base">arrow_back</span> Back to Blogs
                    </button>

                    <header class="mb-6 md:mb-8 text-center max-w-3xl mx-auto px-2" id="post-header"></header>

                    <div class="w-full h-52 sm:h-64 md:h-96 rounded-2xl md:rounded-3xl overflow-hidden mb-8 md:mb-10 shadow-2xl shadow-primary/10"
                        id="post-image-wrap"></div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-10">
                        <aside class="lg:col-span-4 order-2 lg:order-2 space-y-4 md:space-y-6">
                            <div class="lg:sticky lg:top-24 space-y-4 md:space-y-6">
                                <div
                                    class="p-5 md:p-6 rounded-2xl md:rounded-3xl bg-surface-container-low border border-outline-variant/10">
                                    <h3
                                        class="text-[10px] font-black tracking-widest uppercase text-on-surface-variant mb-3 md:mb-4">
                                        Reactions</h3>
                                    <div class="flex flex-wrap gap-2 md:gap-3">
                                        <button onclick="react(this,'👍')"
                                            class="reaction-btn w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 text-xl relative"
                                            data-tooltip="Like"><span class="count text-[10px]">0</span>👍</button>
                                        <button onclick="react(this,'❤️')"
                                            class="reaction-btn w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 text-xl relative"
                                            data-tooltip="Love"><span class="count text-[10px]">0</span>❤️</button>
                                        <button onclick="react(this,'😂')"
                                            class="reaction-btn w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 text-xl relative"
                                            data-tooltip="Funny"><span class="count text-[10px]">0</span>😂</button>
                                        <button onclick="react(this,'😮')"
                                            class="reaction-btn w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 text-xl relative"
                                            data-tooltip="Wow"><span class="count text-[10px]">0</span>😮</button>
                                        <button onclick="react(this,'😢')"
                                            class="reaction-btn w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 text-xl relative"
                                            data-tooltip="Sad"><span class="count text-[10px]">0</span>😢</button>
                                        <button onclick="react(this,'😡')"
                                            class="reaction-btn w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 text-xl relative"
                                            data-tooltip="Angry"><span class="count text-[10px]">0</span>😡</button>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <button id="postSaveBtn" onclick="togglePostSave()"
                                        class="flex-1 flex items-center justify-center gap-2 py-3 bg-primary-fixed text-on-primary-fixed-variant rounded-xl text-sm font-bold hover:bg-primary-fixed/80 transition-all active:scale-95">
                                        <span class="material-symbols-outlined text-base"
                                            id="postSaveIcon">bookmark_add</span>
                                        <span id="postSaveText">Save</span>
                                    </button>
                                    <button onclick="copyBlogLink()"
                                        class="flex-1 flex items-center justify-center gap-2 py-3 bg-surface-container text-on-surface rounded-xl text-sm font-bold hover:bg-surface-container-high transition-colors active:scale-95">
                                        <span class="material-symbols-outlined text-base">share</span> Share
                                    </button>
                                </div>
                            </div>
                        </aside>

                        <article class="lg:col-span-8 order-1 space-y-6" id="post-body"></article>
                    </div>

                    <div class="mt-10 md:mt-14" id="post-comments-section">
                        <div class="border-t border-outline-variant/20 pt-10">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-6 md:mb-8">
                                <h3 class="text-xl md:text-2xl font-black tracking-tight text-on-surface">Community
                                    Thoughts (<span id="commentCount">0</span>)</h3>
                                <div class="flex gap-2">
                                    <button onclick="sortComments('top')" id="sortTop"
                                        class="px-4 py-1.5 rounded-full text-xs font-bold bg-primary text-white transition-colors">Top</button>
                                    <button onclick="sortComments('new')" id="sortNew"
                                        class="px-4 py-1.5 rounded-full text-xs font-bold bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors">Newest</button>
                                </div>
                            </div>

                            <div
                                class="flex gap-3 md:gap-4 p-4 md:p-6 rounded-2xl md:rounded-3xl bg-surface-container-low mb-6 md:mb-8">
                                <div
                                    class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-primary-fixed shrink-0 flex items-center justify-center">
                                    <span class="text-primary font-black text-sm">AV</span>
                                </div>
                                <div class="flex-1 space-y-3 md:space-y-4 min-w-0">
                                    <textarea id="commentInput"
                                        class="w-full bg-transparent border-none focus:ring-0 text-on-surface p-0 placeholder:text-on-surface-variant/40 resize-none h-16 md:h-20 outline-none text-sm leading-relaxed"
                                        placeholder="Join the discussion…"
                                        oninput="updateCommentCharCount(this)"></textarea>
                                    <div class="flex justify-between items-center gap-2 flex-wrap">
                                        <div class="flex gap-2 md:gap-3 items-center"><span
                                                class="text-xs text-on-surface-variant/50" id="charCount">0 / 500</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <button onclick="clearCommentInput()"
                                                class="px-3 md:px-4 py-2 text-on-surface-variant text-sm font-medium hover:text-on-surface transition-colors">Clear</button>
                                            <button onclick="postComment()"
                                                class="px-4 md:px-6 py-2 bg-primary text-white font-bold rounded-xl text-sm shadow-lg shadow-primary/20 hover:bg-primary-container transition-all active:scale-95">Post
                                                Comment</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="commentsList" class="space-y-4 md:space-y-6"></div>
                            <button id="loadMoreBtn" onclick="loadMoreComments()"
                                class="w-full mt-4 py-3 md:py-4 rounded-2xl border border-outline-variant/30 text-sm font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors hidden">Load
                                more</button>
                        </div>
                    </div>
                </div>

                <div class="page" id="page-saved">
                    <div class="mb-6 md:mb-8">
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Saved Posts</h1>
                        <p class="text-on-surface-variant mt-1">Posts you've bookmarked for later reading.</p>
                    </div>
                    <div id="savedList" class="space-y-3 md:space-y-4"></div>
                </div>

                <!-- ══════════ CATEGORIES PAGE ══════════ -->
                <div class="page" id="page-categories">
                    <div class="mb-6 md:mb-8">
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Categories</h1>
                        <p class="text-on-surface-variant mt-1">Browse blogs by topic. Click a category to explore its
                            posts.</p>
                    </div>
                    <div id="categoriesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
                    </div>
                </div>

                <!-- ══════════ FORGOT PASSWORD PAGE ══════════ -->
                <div class="page" id="page-forgot-password">
                    <div class="mb-6 md:mb-8 flex items-center gap-3">
                        <button onclick="showPage('change-password',null)"
                            class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-surface-container transition-colors shrink-0">
                            <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
                        </button>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Forgot Password
                            </h1>
                            <p class="text-on-surface-variant mt-0.5">Enter your email to receive a reset link.</p>
                        </div>
                    </div>
                    <div class="max-w-md bg-surface-container-lowest rounded-3xl p-6 md:p-8 space-y-5">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-2"
                            style="background:rgba(99,14,212,.1)">
                            <span class="material-symbols-outlined text-primary text-3xl">lock_reset</span>
                        </div>
                        <p class="text-sm text-on-surface-variant">We'll send a password reset link to your registered
                            email address. Check your inbox after submitting.</p>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Email
                                Address</label>
                            <input type="email" id="forgotEmailInput" placeholder="avijit@example.com"
                                class="w-full bg-surface-container-high rounded-xl px-4 py-3 text-sm border border-transparent text-on-surface" />
                        </div>
                        <button onclick="sendResetEmail()"
                            class="w-full py-3 stat-gradient text-white rounded-xl font-bold text-sm hover:opacity-90 transition-opacity active:scale-95 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-base">send</span> Send Reset Link
                        </button>
                        <p class="text-center text-xs text-on-surface-variant">Remember your password? <button
                                onclick="showPage('change-password',null)"
                                class="text-primary font-bold hover:underline">Go back</button></p>
                    </div>
                </div>

                <div class="page" id="page-notifications">
                    <div class="mb-6 md:mb-8 flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Notifications
                            </h1>
                            <p class="text-on-surface-variant mt-1">Stay updated on your activity.</p>
                        </div>
                    </div>
                    <div
                        class="bg-surface-container-lowest rounded-2xl md:rounded-3xl p-1 md:p-2 divide-y divide-outline-variant/10">
                        <div
                            class="flex items-start gap-3 md:gap-4 p-4 md:p-5 hover:bg-surface-container-low rounded-2xl transition-colors">
                            <div class="w-9 h-9 md:w-10 md:h-10 rounded-full flex items-center justify-center shrink-0"
                                style="background:rgba(75,65,225,.1)"><span
                                    class="material-symbols-outlined text-secondary text-lg">chat</span></div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-sm text-on-surface">Admin replied to your comment</div>
                                <div class="text-on-surface-variant text-xs mt-0.5">On "React vs Vue in 2025"</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page" id="page-settings">
                    <div class="mb-6 md:mb-8">
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-on-surface">Settings</h1>
                        <p class="text-on-surface-variant mt-1">Customize your account preferences.</p>
                    </div>
                    <div class="max-w-lg space-y-4">
                        <div class="bg-surface-container-lowest rounded-2xl p-5 md:p-6 space-y-5">
                            <h3 class="font-black text-xs uppercase tracking-widest text-on-surface-variant">Appearance
                            </h3>
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-sm text-on-surface">Dark Mode</div>
                                </div>
                                <button id="darkModeToggle" onclick="toggleDark()"
                                    class="w-11 h-6 bg-surface-container-highest rounded-full relative transition-colors"
                                    data-on="false">
                                    <div id="darkModeDot"
                                        class="w-4 h-4 bg-white rounded-full absolute top-1 left-1 transition-all shadow-sm">
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        // ─── JSON DATA (Backend Simulation) ─────────────────────
        const jsonData = {
            "blogs": [
                {
                    "slug": "the-luminous-editor-why-tonal-depth-beats-structural-lines",
                    "title": "The Luminous Editor: Why Tonal Depth Beats Structural Lines",
                    "cat": "design",
                    "author": "Julian Vance",
                    "date": "Oct 24, 2024",
                    "views": "1.2k",
                    "tags": ["ui", "depth", "typography", "visual"],
                    "img": "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800&q=80",
                    "content": "<p class=\"text-xl text-on-surface-variant font-medium leading-relaxed italic border-l-4 border-primary pl-6\">\"We have lived in a world of boxes for too long. The 1px solid border is the cage that restricts modern digital expression.\"</p><p>In the transition from traditional web design to the <strong>Luminous Editor</strong> philosophy, we prioritize how light interacts with surfaces. Think of your interface not as a set of flat containers, but as layers of semi-translucent paper resting atop a soft light source.</p><h2 class=\"text-2xl font-black tracking-tight pt-6\" id=\"philosophy\">The Philosophy of Depth</h2><p>When we remove borders, we force the eye to rely on tonal shifts. This reduces visual noise and cognitive load. A section placed against a lighter background provides all the structural integrity required without the clutter of lines.</p><div class=\"my-8 p-8 rounded-3xl bg-gradient-to-br from-primary to-primary-container text-white shadow-xl relative overflow-hidden\"><div class=\"absolute -right-16 -top-16 w-48 h-48 bg-white/10 rounded-full blur-3xl\"></div><h3 class=\"text-xl font-bold mb-3 relative z-10\">Design Insight</h3><p class=\"text-primary-fixed/90 relative z-10\">Luminous design isn't just about color; it's about the perceived weight of information.</p></div><h2 class=\"text-2xl font-black tracking-tight pt-6\" id=\"execution\">The \"No-Divider\" Rule</h2><p>Instead of separating list items with lines, use whitespace. Spacing-8 (2rem) is often the magic number for editorial clarity. If a hit area needs to be defined, use a background color shift on hover rather than a persistent outline.</p>"
                },
                {
                    "slug": "how-to-learn-php-in-2025-roadmap",
                    "title": "How to Learn PHP in 2025: A Complete Roadmap for Beginners",
                    "cat": "php",
                    "author": "Sakib Ahmed",
                    "date": "Nov 12, 2024",
                    "views": "3.4k",
                    "tags": ["php", "backend", "beginner", "roadmap"],
                    "img": "https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&q=80",
                    "content": "<p class=\"text-xl text-on-surface-variant font-medium leading-relaxed italic border-l-4 border-secondary pl-6\">\"PHP powers over 77% of the web. Learning it is not just practical — it's essential.\"</p><p>PHP remains one of the most in-demand server-side languages in the world. Whether you're building a simple blog or a full-featured e-commerce platform, PHP is the workhorse behind it all.</p><h2 class=\"text-2xl font-black tracking-tight pt-6\" id=\"start\">Where to Start</h2><p>Begin with the fundamentals: variables, data types, control structures, functions. Don't skip these — a solid foundation will make everything else click faster.</p><h2 class=\"text-2xl font-black tracking-tight pt-6\" id=\"practice\">Practice Projects</h2><p>Build a CRUD app, a login system, then a full blog. Each project teaches you something new and forces you to problem-solve in real scenarios.</p>"
                },
                {
                    "slug": "css-grid-complete-guide",
                    "title": "CSS Grid Complete Guide: Build Any Layout in Minutes",
                    "cat": "css",
                    "author": "Tania Hossain",
                    "date": "Dec 1, 2024",
                    "views": "2.1k",
                    "tags": ["css", "layout", "grid", "responsive"],
                    "img": "https://images.unsplash.com/photo-1507721999472-8ed4421c4af2?w=800&q=80",
                    "content": "<p class=\"text-xl text-on-surface-variant font-medium leading-relaxed italic border-l-4 border-tertiary pl-6\">\"CSS Grid is the layout engine modern web design has been waiting for since the 90s.\"</p><p>CSS Grid Layout gives you a two-dimensional grid system that lets you place items in rows and columns simultaneously. Say goodbye to float hacks and hello to clean, readable layout code.</p><h2 class=\"text-2xl font-black tracking-tight pt-6\" id=\"basics\">Grid Basics</h2><p>Start with <code class=\"bg-surface-container px-1.5 py-0.5 rounded text-primary text-sm\">display: grid</code> and define your columns with <code class=\"bg-surface-container px-1.5 py-0.5 rounded text-primary text-sm\">grid-template-columns</code>. From there, place items using grid-column and grid-row.</p><h2 class=\"text-2xl font-black tracking-tight pt-6\" id=\"advanced\">Advanced Techniques</h2><p>Named grid areas, implicit vs explicit grids, and auto-fill/auto-fit — these unlock truly responsive designs without a single media query.</p>"
                },
                {
                    "slug": "mastering-flexbox-2025",
                    "title": "Mastering Flexbox: The Complete Visual Guide",
                    "cat": "css",
                    "author": "Tania Hossain",
                    "date": "Jan 5, 2025",
                    "views": "1.8k",
                    "tags": ["css", "flexbox", "layout", "frontend"],
                    "img": "https://images.unsplash.com/photo-1547658719-da2b51169166?w=800&q=80",
                    "content": "<p class=\"text-xl text-on-surface-variant font-medium leading-relaxed italic border-l-4 border-tertiary pl-6\">\"Flexbox made the one-dimensional layout problems of the web finally feel solvable.\"</p><p>Flexbox is the backbone of modern component-level layouts. Master it and everything from navigation bars to card grids becomes effortless.</p><h2 class=\"text-2xl font-black tracking-tight pt-6\">Core Concepts</h2><p>Understand the main axis vs cross axis, justify-content vs align-items, and flex-grow vs flex-shrink. These six properties will handle 80% of your layout challenges.</p>"
                },
                {
                    "slug": "php-oop-for-beginners",
                    "title": "PHP OOP for Beginners: Classes, Objects & Inheritance",
                    "cat": "php",
                    "author": "Sakib Ahmed",
                    "date": "Jan 20, 2025",
                    "views": "2.7k",
                    "tags": ["php", "oop", "backend", "advanced"],
                    "img": "https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800&q=80",
                    "content": "<p class=\"text-xl text-on-surface-variant font-medium leading-relaxed italic border-l-4 border-secondary pl-6\">\"Object-oriented PHP transforms your messy procedural scripts into elegant, reusable systems.\"</p><p>Once you understand classes and objects, you'll wonder how you ever wrote PHP without them. OOP isn't just a style — it's a superpower.</p><h2 class=\"text-2xl font-black tracking-tight pt-6\">Classes & Objects</h2><p>A class is a blueprint. An object is an instance of that blueprint. Define properties and methods inside a class, instantiate it with <code class=\"bg-surface-container px-1.5 py-0.5 rounded text-primary text-sm\">new</code>, and you're building real software.</p><h2 class=\"text-2xl font-black tracking-tight pt-6\">Inheritance</h2><p>Extend a class to share behaviour without repeating code. Use <code class=\"bg-surface-container px-1.5 py-0.5 rounded text-primary text-sm\">parent::</code> to call parent methods and <code class=\"bg-surface-container px-1.5 py-0.5 rounded text-primary text-sm\">abstract</code> to enforce contracts.</p>"
                },
                {
                    "slug": "design-systems-that-scale",
                    "title": "Design Systems That Scale: Tokens, Components & Docs",
                    "cat": "design",
                    "author": "Julian Vance",
                    "date": "Feb 3, 2025",
                    "views": "980",
                    "tags": ["design", "ui", "tokens", "system"],
                    "img": "https://images.unsplash.com/photo-1558655146-d09347e92766?w=800&q=80",
                    "content": "<p class=\"text-xl text-on-surface-variant font-medium leading-relaxed italic border-l-4 border-primary pl-6\">\"A design system without documentation is just a collection of components with no soul.\"</p><p>Building a design system is one of the highest-leverage investments a product team can make. Done right, it enforces consistency, accelerates development, and makes onboarding seamless.</p><h2 class=\"text-2xl font-black tracking-tight pt-6\">Design Tokens</h2><p>Start with tokens — the named values for color, spacing, and typography that serve as the single source of truth across design and code. Tools like Style Dictionary can sync them between Figma and your codebase automatically.</p>"
                }
            ],
            "comments": {
                "the-luminous-editor-why-tonal-depth-beats-structural-lines": [
                    { "id": 1, "author": "Elena S.", "avatar": "E", "time": "2 hours ago", "text": "This perspective on tonal depth is exactly what I've been trying to articulate to my clients. The 'box' fatigue is real. Great read!", "likes": 12, "liked": false, "replies": [] },
                    {
                        "id": 2, "author": "Marco T.", "avatar": "M", "time": "4 hours ago", "text": "I've been using this approach for 6 months now and my clients love it.", "likes": 8, "liked": false, "replies": [
                            { "id": 21, "author": "Sarah K.", "avatar": "S", "time": "3 hours ago", "text": "Same here!", "likes": 3, "liked": false }
                        ]
                    }
                ]
            }
        };
        let jsonData1;
        (async function (){
            let loaddata= await fetch('../include/user_api.php');
            jsonData1= await loaddata.json();
            console.log(jsonData1);
        })();

        // ─── STATE VARIABLES ────────────────────────────────────
        let BLOGS = [];
        let SAVED = ['css-grid-complete-guide', 'how-to-learn-php-in-2025-roadmap'];
        let activeCat = 'all';
        let activeTag = null;
        let currentBlogSlug = null;
        let isDark = false;
        let commentSortMode = 'top';
        let commentDisplayed = 3;
        let nextCommentId = 200;

        // ─── INIT ────────────────────────────────────────────────
        window.onload = () => {
            BLOGS = jsonData1.blogs;

            renderBlogs();
            renderSaved();
            renderCategories();

            const path = window.location.pathname.replace(/^\/|\/$/g, "");

            if (path && path !== 'index.html' && !path.includes('.')) {
                if (BLOGS.find(b => b.slug === path)) {
                    openBlog(path, false);
                }
            }
        };

        // ─── DARK MODE ───────────────────────────────────────────
        function toggleDark() {
            isDark = !isDark;
            document.documentElement.classList.toggle('dark', isDark);
            const topbarIcon = document.getElementById('darkIconTopbar');
            if (topbarIcon) topbarIcon.textContent = isDark ? 'light_mode' : 'dark_mode';
            const toggle = document.getElementById('darkModeToggle');
            const dot = document.getElementById('darkModeDot');
            if (toggle && dot) {
                toggle.dataset.on = isDark.toString();
                toggle.classList.toggle('bg-primary', isDark);
                toggle.classList.toggle('bg-surface-container-highest', !isDark);
                dot.style.left = isDark ? 'auto' : '4px';
                dot.style.right = isDark ? '4px' : 'auto';
            }
            showToast(isDark ? 'Dark mode enabled' : 'Light mode enabled');
        }

        // ─── PAGE NAVIGATION ────────────────────────────────────
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

        // ─── MOBILE SIDEBAR ─────────────────────────────────────
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

        // ─── BLOG RENDERING ─────────────────────────────────────
        function renderBlogs() {
            const search = (document.getElementById('blogSearch') || { value: '' }).value.toLowerCase();
            const grid = document.getElementById('blogGrid');
            const noRes = document.getElementById('noResults');
            const filtered = BLOGS.filter(b => {
                const matchCat = activeCat === 'all' || b.cat === activeCat;
                const matchTag = !activeTag || (b.tags && b.tags.includes(activeTag));
                const matchSearch = !search || b.title.toLowerCase().includes(search)
                    || b.author.toLowerCase().includes(search)
                    || (b.tags && b.tags.some(t => t.toLowerCase().includes(search)));
                return matchCat && matchTag && matchSearch;
            });

            // Render tag pills
            const tagRow = document.getElementById('tagFilterRow');
            const clearTagBtn = document.getElementById('clearTagBtn');
            if (tagRow) {
                const allTags = [...new Set(BLOGS.filter(b => activeCat === 'all' || b.cat === activeCat).flatMap(b => b.tags || []))];
                tagRow.innerHTML = allTags.map(t => `
                  <button onclick="filterByTag('${t}')" class="tag-pill px-2.5 py-1 rounded-full text-xs font-bold bg-surface-container text-on-surface-variant ${activeTag === t ? 'active-tag' : ''}">#${t}</button>
                `).join('');
                if (clearTagBtn) clearTagBtn.classList.toggle('hidden', !activeTag);
            }

            grid.innerHTML = filtered.map(b => `
    <div class="blog-card bg-surface-container-lowest rounded-2xl overflow-hidden cursor-pointer" onclick="openBlog('${b.slug}')">
      <div class="h-44 stat-gradient relative overflow-hidden">
        <img src="${b.img}" alt="${b.title}" class="w-full h-full object-cover opacity-60"/>
        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-white/20 backdrop-blur-sm text-white text-[10px] font-black uppercase tracking-widest">${b.cat}</span>
      </div>
      <div class="p-4 md:p-5">
        <h3 class="font-black text-sm leading-snug text-on-surface mb-2 line-clamp-2">${b.title}</h3>
        <div class="flex items-center justify-between text-xs text-on-surface-variant">
          <span>${b.author}</span>
          <span>${b.date} · ${b.views} views</span>
        </div>
        <div class="flex flex-wrap gap-1.5 mt-2">
          ${(b.tags || []).map(t => `<span onclick="event.stopPropagation();filterByTag('${t}')" class="tag-pill px-2 py-0.5 rounded-full text-[10px] font-bold bg-surface-container text-on-surface-variant ${activeTag === t ? 'active-tag' : ''}">#${t}</span>`).join('')}
        </div>
        <div class="flex items-center gap-2 mt-3 md:mt-4">
          <button onclick="event.stopPropagation();openBlog('${b.slug}')" class="flex-1 py-2 stat-gradient text-white rounded-xl text-xs font-bold text-center hover:opacity-90 transition-opacity">Read Post →</button>
          <button onclick="event.stopPropagation();toggleSave('${b.slug}',this)" class="w-8 h-8 flex items-center justify-center rounded-xl bg-surface-container hover:bg-primary-fixed transition-colors" title="Save">
            <span class="material-symbols-outlined text-base ${SAVED.includes(b.slug) ? 'text-primary ms-filled' : 'text-on-surface-variant'}">${SAVED.includes(b.slug) ? 'bookmark' : 'bookmark'}</span>
          </button>
        </div>
      </div>
    </div>`).join('');
            noRes.classList.toggle('hidden', filtered.length > 0);
        }

        function filterBlogs() { renderBlogs(); }
        function setCat(cat, el) {
            activeCat = cat;
            activeTag = null;
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            renderBlogs();
        }

        function filterByTag(tag) {
            activeTag = (activeTag === tag) ? null : tag;
            showPage('blogs', document.querySelector('[onclick*="\'blogs\'"]'));
            renderBlogs();
        }

        function clearTag() {
            activeTag = null;
            renderBlogs();
        }

        // ─── CATEGORIES PAGE ─────────────────────────────────────
        function renderCategories() {
            const grid = document.getElementById('categoriesGrid');
            if (!grid) return;
            const catMeta = {
                design: { icon: 'palette', color: '#630ed4', bg: 'rgba(99,14,212,.1)', label: 'Design' },
                php: { icon: 'code', color: '#4b41e1', bg: 'rgba(75,65,225,.1)', label: 'PHP' },
                css: { icon: 'style', color: '#9b005c', bg: 'rgba(155,0,92,.1)', label: 'CSS' },
            };
            const cats = [...new Set(BLOGS.map(b => b.cat))];
            grid.innerHTML = cats.map(cat => {
                const m = catMeta[cat] || { icon: 'article', color: '#630ed4', bg: 'rgba(99,14,212,.1)', label: cat };
                const count = BLOGS.filter(b => b.cat === cat).length;
                const allTags = [...new Set(BLOGS.filter(b => b.cat === cat).flatMap(b => b.tags || []))].slice(0, 4);
                return `
                <div class="cat-card bg-surface-container-lowest rounded-2xl p-6 cursor-pointer border border-transparent hover:border-outline-variant/30"
                     onclick="openCategoryBlogs('${cat}')">
                  <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4" style="background:${m.bg}">
                    <span class="material-symbols-outlined text-2xl" style="color:${m.color}">${m.icon}</span>
                  </div>
                  <h3 class="font-black text-lg text-on-surface mb-1">${m.label}</h3>
                  <p class="text-xs text-on-surface-variant mb-3">${count} post${count !== 1 ? 's' : ''}</p>
                  <div class="flex flex-wrap gap-1.5">
                    ${allTags.map(t => `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-surface-container text-on-surface-variant">#${t}</span>`).join('')}
                  </div>
                  <button class="mt-4 w-full py-2 rounded-xl text-xs font-bold text-white hover:opacity-90 transition-opacity" style="background:${m.color}">Browse ${m.label} →</button>
                </div>`;
            }).join('');
        }

        function openCategoryBlogs(cat) {
            activeTag = null;
            activeCat = cat;
            showPage('blogs', document.querySelector('[onclick*="\'blogs\'"]'));
            document.querySelectorAll('.filter-tab').forEach(t => {
                t.classList.toggle('active', t.dataset.cat === cat);
            });
            renderBlogs();
        }

        function toggleSave(slug, btn) {
            const idx = SAVED.indexOf(slug);
            if (idx > -1) { SAVED.splice(idx, 1); showToast('Post removed from saved'); }
            else { SAVED.push(slug); showToast('Post saved to bookmarks!'); }
            renderBlogs();
            renderSaved();
            if (currentBlogSlug === slug) updatePostSaveBtn();
        }

        // ─── SINGLE BLOG POST VIEW ────────────────────────
        function openBlog(slug, pushHistory = true) {
            const blog = BLOGS.find(b => b.slug === slug);
            if (!blog) return;
            currentBlogSlug = slug;

            document.body.classList.add('single-blog-active');
            document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));

            if (pushHistory) {
                history.pushState({ type: 'blog', slug: slug }, blog.title, '/' + slug);
            }

            document.getElementById('post-header').innerHTML = `
    <span class="inline-block px-3 py-1 rounded-full bg-tertiary-fixed text-xs font-black uppercase tracking-widest mb-4" style="color:#3e0022">${blog.cat}</span>
    <h1 class="text-2xl md:text-3xl lg:text-4xl font-black tracking-tight text-on-surface leading-tight mb-5">${blog.title}</h1>
    <div class="flex items-center gap-3 justify-center">
      <div class="w-10 h-10 rounded-full stat-gradient flex items-center justify-center text-white text-sm font-black">${blog.author[0]}</div>
      <div class="text-left"><div class="font-bold text-sm text-on-surface">${blog.author}</div><div class="text-xs text-on-surface-variant">${blog.date} · <span class="text-primary font-semibold">● ${blog.views} views</span></div></div>
    </div>`;

            document.getElementById('post-image-wrap').innerHTML = `<img src="${blog.img}" alt="${blog.title}" class="w-full h-full object-cover"/>`;
            document.getElementById('post-body').innerHTML = `<div class="prose max-w-none space-y-5 text-on-surface leading-relaxed text-base">${blog.content}</div>`;

            const view = document.getElementById('blog-post-view');
            view.classList.add('active');
            window.scrollTo(0, 0);

            updatePostSaveBtn();

            commentSortMode = 'top';
            commentDisplayed = 3;
            if (!BLOG_COMMENTS[slug]) BLOG_COMMENTS[slug] = [];
            renderBlogComments();
            updateCommentCountEl();
        }

        function closeBlogView() {
            document.body.classList.remove('single-blog-active');
            document.getElementById('blog-post-view').classList.remove('active');

            history.pushState({ type: 'page', page: 'blogs' }, 'Blogs', '/');
            showPage('blogs', document.querySelector('[onclick*="\'blogs\'"]'));
        }

        window.onpopstate = function (e) {
            if (e.state && e.state.type === 'blog') {
                openBlog(e.state.slug, false);
            } else {
                document.body.classList.remove('single-blog-active');
                document.getElementById('blog-post-view').classList.remove('active');
                showPage(e.state?.page || 'blogs', null);
            }
        };

        // ─── POST SAVE BUTTON ────────────────────────────────────
        function togglePostSave() {
            const idx = SAVED.indexOf(currentBlogSlug);
            if (idx > -1) { SAVED.splice(idx, 1); showToast('Post removed from saved'); }
            else { SAVED.push(currentBlogSlug); showToast('Post saved to bookmarks! 🔖'); }
            updatePostSaveBtn();
            renderBlogs();
            renderSaved();
        }

        function updatePostSaveBtn() {
            if (!currentBlogSlug) return;
            const saved = SAVED.includes(currentBlogSlug);
            const btn = document.getElementById('postSaveBtn');
            const icon = document.getElementById('postSaveIcon');
            const text = document.getElementById('postSaveText');
            if (!btn) return;
            icon.textContent = 'bookmark';
            icon.className = 'material-symbols-outlined text-base' + (saved ? ' ms-filled' : '');
            text.textContent = saved ? 'Saved' : 'Save';
            btn.className = `flex-1 flex items-center justify-center gap-2 py-3 ${saved ? 'bg-primary text-white' : 'bg-primary-fixed text-on-primary-fixed-variant'} rounded-xl text-sm font-bold hover:opacity-90 transition-all active:scale-95`;
        }

        function copyBlogLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).catch(() => { });
            showToast('Link copied to clipboard!');
        }

        // ─── SAVED POSTS ─────────────────────────────────────────
        function renderSaved() {
            const container = document.getElementById('savedList');
            const saved = BLOGS.filter(b => SAVED.includes(b.slug));
            if (!saved.length) {
                container.innerHTML = `<div class="text-center text-on-surface-variant py-16"><span class="material-symbols-outlined text-4xl mb-3 block">bookmark_border</span><p class="text-sm">No saved posts yet. Browse blogs and save posts you love!</p></div>`;
                return;
            }
            container.innerHTML = saved.map(b => `
    <div class="flex items-center gap-3 md:gap-4 bg-surface-container-lowest rounded-2xl p-4 md:p-5">
      <div class="w-14 h-14 md:w-16 md:h-16 rounded-xl stat-gradient shrink-0 flex items-center justify-center overflow-hidden">
        <img src="${b.img}" class="w-full h-full object-cover opacity-70"/>
      </div>
      <div class="flex-1 min-w-0">
        <div class="font-bold text-sm truncate text-on-surface">${b.title}</div>
        <div class="text-xs text-on-surface-variant mt-1">${b.author} · ${b.date}</div>
      </div>
      <div class="flex gap-2 shrink-0">
        <button onclick="openBlog('${b.slug}')" class="px-3 md:px-4 py-2 stat-gradient text-white rounded-xl text-xs font-bold hover:opacity-90 transition-opacity">View</button>
        <button onclick="toggleSave('${b.slug}',this)" class="px-3 md:px-4 py-2 bg-error-container/30 text-error rounded-xl text-xs font-bold hover:bg-error-container/60 transition-colors">Remove</button>
      </div>
    </div>`).join('');
        }

        // ─── TOPBAR SEARCH ───────────────────────────────────────
        function handleTopbarSearch(q) {
            const box = document.getElementById('topbarResults');
            if (!q.trim()) { box.classList.add('hidden'); return; }
            const results = BLOGS.filter(b => b.title.toLowerCase().includes(q.toLowerCase())).slice(0, 4);
            if (!results.length) { box.classList.add('hidden'); return; }
            box.innerHTML = results.map(b => `
    <div onclick="openBlog('${b.slug}');document.getElementById('topbarResults').classList.add('hidden')" class="flex items-center gap-3 px-4 py-3 hover:bg-surface-container-low cursor-pointer border-b border-outline-variant/10 last:border-0">
      <span class="material-symbols-outlined text-primary text-base">article</span>
      <div><div class="text-sm font-semibold text-on-surface">${b.title}</div><div class="text-xs text-on-surface-variant">${b.author} · ${b.cat}</div></div>
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

        // ─── COMMENT SYSTEM ─────────────────────────────────────
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
            const isOwn = c.id >= 200;
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
          <!-- Edit area -->
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
            <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-black text-xs shrink-0">AV</div>
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
            const text = input.value.trim();
            if (!text) { showToast('Please write something first!'); return; }
            if (text.length > 500) { showToast('Comment too long (max 500 chars)'); return; }

            if (!BLOG_COMMENTS[currentBlogSlug]) BLOG_COMMENTS[currentBlogSlug] = [];
            BLOG_COMMENTS[currentBlogSlug].unshift({
                id: nextCommentId++, author: 'Avijit Roy', avatar: 'AV',
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
            const text = input.value.trim();
            if (!text) return;
            const comments = BLOG_COMMENTS[currentBlogSlug] || [];
            const comment = comments.find(c => c.id === commentId);
            if (!comment) return;
            comment.replies.push({ id: nextCommentId++, author: 'Avijit Roy', avatar: 'AV', time: 'just now', text, likes: 0, liked: false });
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
                const reply = parent?.replies.find(r => r.id === commentId);
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
            commentSortMode = mode;
            commentDisplayed = 3;
            document.getElementById('sortTop').className = mode === 'top'
                ? 'px-4 py-1.5 rounded-full text-xs font-bold bg-primary text-white transition-colors'
                : 'px-4 py-1.5 rounded-full text-xs font-bold bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors';
            document.getElementById('sortNew').className = mode === 'new'
                ? 'px-4 py-1.5 rounded-full text-xs font-bold bg-primary text-white transition-colors'
                : 'px-4 py-1.5 rounded-full text-xs font-bold bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors';
            renderBlogComments();
        }

        function loadMoreComments() {
            commentDisplayed += 5;
            renderBlogComments();
        }

        function updateCommentCountEl(delta = 0) {
            const comments = BLOG_COMMENTS[currentBlogSlug] || [];
            let total = comments.length + comments.reduce((s, c) => s + (c.replies?.length || 0), 0);
            total += delta;
            total = Math.max(0, total);
            const el = document.getElementById('commentCount');
            if (el) el.textContent = total;
        }

        function updateCommentCharCount(el) {
            const len = el.value.length;
            const cc = document.getElementById('charCount');
            if (cc) {
                cc.textContent = len + ' / 500';
            }
        }

        function clearCommentInput() {
            const input = document.getElementById('commentInput');
            if (input) { input.value = ''; }
            const cc = document.getElementById('charCount');
            if (cc) cc.textContent = '0 / 500';
        }

        function editComment(id) {
            // Close any other open edit areas
            document.querySelectorAll('.comment-edit-area.open').forEach(el => el.classList.remove('open'));
            const area = document.getElementById('edit-area-' + id);
            const textEl = document.getElementById('comment-text-' + id);
            if (area && textEl) {
                area.classList.add('open');
                const input = document.getElementById('edit-input-' + id);
                if (input) { input.focus(); input.setSelectionRange(input.value.length, input.value.length); }
            }
        }

        function saveEditComment(id) {
            const input = document.getElementById('edit-input-' + id);
            if (!input) return;
            const newText = input.value.trim();
            if (!newText) { showToast('Comment cannot be empty'); return; }
            const comments = BLOG_COMMENTS[currentBlogSlug] || [];
            const comment = comments.find(c => c.id === id);
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

        function sendResetEmail() {
            const input = document.getElementById('forgotEmailInput');
            const email = input ? input.value.trim() : '';
            if (!email || !email.includes('@')) { showToast('Please enter a valid email address'); return; }
            // Simulate sending email
            if (input) input.value = '';
            showToast('✉️ Reset link sent to ' + email);
            setTimeout(() => showPage('change-password', null), 1800);
        }

        function escapeHtml(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function showToast(msg) {
            const t = document.getElementById('toast');
            t.textContent = msg; t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 2800);
        }

        function toggleProfileMenu() { document.getElementById('profileMenu').classList.toggle('hidden'); }
        function closeProfileMenu() { document.getElementById('profileMenu').classList.add('hidden'); }

        document.addEventListener('click', e => {
            if (!e.target.closest('#profileMenu') && !e.target.closest('[onclick*="toggleProfileMenu"]')) closeProfileMenu();
        });

        function checkStrength(v) {
            let s = 0;
            if (v.length >= 8) s++; if (/[A-Z]/.test(v)) s++; if (/[0-9]/.test(v)) s++; if (/[^A-Za-z0-9]/.test(v)) s++;
            const levels = [{ w: '0%', c: 'transparent', t: '' }, { w: '25%', c: '#ba1a1a', t: 'Weak' }, { w: '50%', c: '#f0a500', t: 'Fair' }, { w: '75%', c: '#4b41e1', t: 'Good' }, { w: '100%', c: '#16a34a', t: 'Strong' }];
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
                cnt.textContent = '1'; cnt.style.cssText = 'opacity:1;transform:scale(1)';
                showToast(`You reacted ${emoji}`);
            }
        }

        function logout() {

            showToast('Logging out...');
            window.location.href ='../actions/logout.php';
        }
    </script>
</body>

</html>




author_nav_sidebar



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
            <a class="<?= ($active == 'cate-req') ? 'flex items-center gap-3 px-4 py-3 bg-[#eaddff] text-[#5a00c6] rounded-full font-semibold' : 'flex items-center gap-3 px-4 py-3 text-[#4a4455] dark:text-[#e8dfee] hover:bg-[#f3ebfa] dark:hover:bg-[#4a4455] transition-colors rounded-full' ?>"
                href="request-category.php">
                <span class="material-symbols-outlined" data-icon="category">category</span>
                <span class="font-medium">Category</span>
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




auther/index.php


<?php include "../include/session.php";
requireAuthor();
include "../include/db.php";
include "../include/author_nav_sidebar.php";
$id = $_SESSION['user_id'];

$total_query = "SELECT COUNT(*) as total FROM comments c INNER JOIN posts p ON c.post_id = p.id WHERE p.author_id = $id";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_comments = $total_row['total'];

$total_query1 = "SELECT COUNT(*) as total1 FROM reactions WHERE user_id = $id";
$total_result1 = mysqli_query($conn, $total_query1);
$total_row1 = mysqli_fetch_assoc($total_result1);
$total_reactions = $total_row1['total1'];

$total_query2 = "SELECT COUNT(*) as total2 FROM posts WHERE author_id = $id";
$total_result2 = mysqli_query($conn, $total_query2);
$total_row2 = mysqli_fetch_assoc($total_result2);
$total_posts = $total_row2['total2'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Luminous | Author Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-secondary-container": "#fffbff",
                        "surface-container-high": "#ede5f4",
                        "on-tertiary-container": "#ffdde7",
                        "on-primary": "#ffffff",
                        "surface-container-low": "#f9f1ff",
                        "surface-container": "#f3ebfa",
                        "primary-fixed-dim": "#d2bbff",
                        "primary": "#630ed4",
                        "tertiary": "#9b005c",
                        "on-secondary-fixed": "#0f0069",
                        "on-tertiary-fixed-variant": "#8c0053",
                        "on-primary-container": "#ede0ff",
                        "outline-variant": "#ccc3d8",
                        "inverse-primary": "#d2bbff",
                        "on-primary-fixed": "#25005a",
                        "background": "#fef7ff",
                        "primary-fixed": "#eaddff",
                        "inverse-surface": "#332f39",
                        "tertiary-fixed-dim": "#ffb0cd",
                        "primary-container": "#7c3aed",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-highest": "#e8dfee",
                        "on-primary-fixed-variant": "#5a00c6",
                        "inverse-on-surface": "#f6eefc",
                        "secondary-fixed": "#e2dfff",
                        "on-tertiary-fixed": "#3e0022",
                        "surface-bright": "#fef7ff",
                        "secondary": "#4b41e1",
                        "surface-tint": "#732ee4",
                        "tertiary-container": "#bf2076",
                        "on-surface-variant": "#4a4455",
                        "outline": "#7b7487",
                        "surface-dim": "#dfd7e6",
                        "secondary-fixed-dim": "#c3c0ff",
                        "on-surface": "#1d1a24",
                        "on-secondary-fixed-variant": "#3323cc",
                        "on-tertiary": "#ffffff",
                        "tertiary-fixed": "#ffd9e4",
                        "surface": "#fef7ff",
                        "on-error-container": "#93000a",
                        "surface-variant": "#e8dfee",
                        "error": "#ba1a1a",
                        "on-background": "#1d1a24",
                        "secondary-container": "#645efb",
                        "on-secondary": "#ffffff",
                        "on-error": "#ffffff",
                        "error-container": "#ffdad6"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Public Sans", "sans-serif"],
                        "body": ["Public Sans", "sans-serif"],
                        "label": ["Public Sans", "sans-serif"]
                    }
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body class="bg-background text-on-background selection:bg-primary-fixed selection:text-on-primary-fixed">
    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-black/40 z-40 md:hidden hidden transition-opacity" id="sidebar-overlay"
        onclick="toggleSidebar()"></div>
    <!-- Sidebar Navigation -->
    <?= author_slidebar('dashboard') ?>
    <!-- Top App Bar -->
    <?= author_navbar(); ?>
    <!-- Main Content Canvas -->
    <main class="md:ml-64 pt-20 min-h-screen px-4 sm:px-6 pb-12 transition-all duration-300">
        <!-- Welcome Header -->
        <header class="mb-8 flex flex-col gap-1">
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-on-surface">Good Morning, Alex</h1>
            <p class="text-on-surface-variant font-medium">Here's what's happening with your content today.</p>
        </header>
        <!-- Stats Bento Grid -->
        <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 mb-8">
            <div
                class="bg-surface-container-lowest p-5 sm:p-6 rounded-xl flex flex-col gap-4 shadow-sm border border-outline-variant/10 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10">
                    <div class="w-12 h-12 rounded-lg bg-primary-fixed/30 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-2xl">post_add</span>
                    </div>
                    <span class="text-xs font-bold text-primary px-2 py-1 bg-primary-fixed/20 rounded-full">+12% this
                        week</span>
                </div>
                <div class="z-10">
                    <h3 class="text-on-surface-variant text-sm font-semibold uppercase tracking-wider">Total Posts</h3>
                    <p class="text-4xl font-extrabold text-on-surface mt-1"><?= $total_posts ?? 0 ?></p>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-9xl"
                        style="font-variation-settings: 'FILL' 1;">post_add</span>
                </div>
            </div>
            <div
                class="bg-surface-container-lowest p-5 sm:p-6 rounded-xl flex flex-col gap-4 shadow-sm border border-outline-variant/10 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10">
                    <div
                        class="w-12 h-12 rounded-lg bg-tertiary-fixed/30 flex items-center justify-center text-tertiary">
                        <span class="material-symbols-outlined text-2xl">forum</span>
                    </div>
                    <span class="text-xs font-bold text-tertiary px-2 py-1 bg-tertiary-fixed/20 rounded-full">+8.4%
                        today</span>
                </div>
                <div class="z-10">
                    <h3 class="text-on-surface-variant text-sm font-semibold uppercase tracking-wider">Total Comments
                    </h3>
                    <p class="text-4xl font-extrabold text-on-surface mt-1"><?= $total_comments ?? 0 ?></p>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-9xl"
                        style="font-variation-settings: 'FILL' 1;">forum</span>
                </div>
            </div>
            <div
                class="bg-surface-container-lowest p-5 sm:p-6 rounded-xl flex flex-col gap-4 shadow-sm border border-outline-variant/10 relative overflow-hidden group sm:col-span-2 md:col-span-1">
                <div class="flex justify-between items-start z-10">
                    <div
                        class="w-12 h-12 rounded-lg bg-secondary-fixed/30 flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-2xl">favorite</span>
                    </div>
                    <span class="text-xs font-bold text-secondary px-2 py-1 bg-secondary-fixed/20 rounded-full">+24%
                        avg</span>
                </div>
                <div class="z-10">
                    <h3 class="text-on-surface-variant text-sm font-semibold uppercase tracking-wider">Total Reactions
                    </h3>
                    <p class="text-4xl font-extrabold text-on-surface mt-1"><?= $total_reactions ?? 0 ?></p>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-9xl"
                        style="font-variation-settings: 'FILL' 1;">favorite</span>
                </div>
            </div>
        </section>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
            <!-- Recent Posts Asymmetric Layout -->
            <section class="lg:col-span-8 flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-on-surface">Recent Posts</h2>
                    <a href="my-post.php" class="text-sm font-semibold text-primary hover:underline">View All Posts</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                    <div class="col-span-1 sm:col-span-2 group cursor-pointer">
                        <div
                            class="bg-surface-container-low rounded-xl p-4 sm:p-6 flex flex-col sm:flex-row gap-5 sm:gap-6 hover:bg-surface-container transition-colors">
                            <div
                                class="w-full sm:w-48 h-44 sm:h-48 rounded-lg overflow-hidden flex-shrink-0 bg-slate-200">
                                <img alt="Post thumbnail"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAaApYUgtdi4MDQqdVTDiiTfZv__wrx2iwKHTbOBPegYwuTGjT2Zukv2TIcOspTNT5RBvlUIAmEx4wJ7LmwDFYZoYJ0i1Yk9kioYzNmm7LU_bUWg9StGAt1IEWtrQnqrVpeNrFFcUqMRq1P29DRoLqvngl37bMkENNcHCik1BuXAwcsAnDEqWxTUOvgHHb28etUZe5S5PK7UyYCxLrwwJKX53bdHTjVorn9OKrgcK4EHf98Z-K4PF4Igozt1dXxApzwVGS8tuH3Bt8" />
                            </div>
                            <div class="flex flex-col justify-between py-1">
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span
                                            class="px-2 py-0.5 bg-tertiary-container text-on-tertiary rounded text-[10px] font-bold uppercase tracking-widest">Technology</span>
                                        <span class="text-xs text-on-surface-variant font-medium">Published 2h
                                            ago</span>
                                    </div>
                                    <h3 class="text-xl sm:text-2xl font-bold text-on-surface leading-tight">The Future
                                        of Generative UI: Designing Beyond Components</h3>
                                    <p class="text-on-surface-variant text-sm line-clamp-2">Exploring how artificial
                                        intelligence is reshaping the way we think about user interface modularity and
                                        dynamic layout generation in modern applications.</p>
                                </div>
                                <div class="flex items-center gap-4 sm:gap-6 mt-4">
                                    <span class="flex items-center gap-1 text-xs font-semibold text-on-surface-variant">
                                        <span class="material-symbols-outlined text-lg">visibility</span> 12.4k
                                    </span>
                                    <span class="flex items-center gap-1 text-xs font-semibold text-on-surface-variant">
                                        <span class="material-symbols-outlined text-lg">comment</span> 84
                                    </span>
                                    <span class="flex items-center gap-1 text-xs font-semibold text-on-surface-variant">
                                        <span class="material-symbols-outlined text-lg">share</span> 312
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="group cursor-pointer">
                        <div
                            class="bg-surface-container-low rounded-xl p-4 flex flex-col gap-4 hover:bg-surface-container transition-colors h-full">
                            <div class="w-full aspect-video rounded-lg overflow-hidden bg-slate-200">
                                <img alt="Post thumbnail"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDwsxMvun9jZ9yLLrCGUx1TKiCdF2LwspDk4OcsXT3fB530mEbCGD1BCEI8kJfpS6lBGWIGXYfCIOeRXhO53Wc4-6Wgp98HBpovNLrMntMSzf84OxT0FKiDCUn8Htyyt2dbl2dSViBKGRNoOkPYG_Bjg7-KucbU60KphLaMW85jN1mpPakNNoDycYdpzFNM8d7ruoJX__rYdCm1ER4S-IXQJinK27NQB7WBfSG_5BUbh-kVvIScQcEhPhEIvklJRpcSBO8B_Yx-v3U" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span
                                        class="px-2 py-0.5 bg-secondary-container text-on-secondary rounded text-[10px] font-bold uppercase tracking-widest">Creative</span>
                                    <span class="text-xs text-on-surface-variant font-medium">8h ago</span>
                                </div>
                                <h3 class="text-lg font-bold text-on-surface leading-tight">10 Principles of Luminous
                                    Design</h3>
                                <p class="text-on-surface-variant text-xs line-clamp-2">How to use light and tonal depth
                                    to create sophisticated editorial layouts.</p>
                            </div>
                        </div>
                    </div>
                    <div class="group cursor-pointer">
                        <div
                            class="bg-surface-container-low rounded-xl p-4 flex flex-col gap-4 hover:bg-surface-container transition-colors h-full">
                            <div class="w-full aspect-video rounded-lg overflow-hidden bg-slate-200">
                                <img alt="Post thumbnail"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDeYJuRUj3fifqQ9OVhWTGCh2tSfZXqG5XPdylVB2UFGUdwyNd_XmM6bje6zxvUpg6buSwxjb0gYJ8JuTATtYkc10_gk-ugOylkkISl8TMGmRWNV7Cdn3lvxK64aGDwPCwTjNGdUhst7Vz0bziHKVUmUMyNEHpPp9gFIfJBpXLor43MspWHl9WbruaLIi4RuiX_wWVqKjKztryTQjgwYaaMZH5N1db2srV0_3lL-1ArCjzjEd19OzQiCs67Cgnu9CJFngPNjBbeFGU" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span
                                        class="px-2 py-0.5 bg-primary-container text-on-primary rounded text-[10px] font-bold uppercase tracking-widest">Editorial</span>
                                    <span class="text-xs text-on-surface-variant font-medium">Yesterday</span>
                                </div>
                                <h3 class="text-lg font-bold text-on-surface leading-tight">Navigating the New Media
                                    Landscape</h3>
                                <p class="text-on-surface-variant text-xs line-clamp-2">Strategies for maintaining
                                    editorial integrity in the age of rapid content cycles.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Recent Comments Sidebar Feed -->
            <!-- <section class="lg:col-span-4 flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-on-surface">Activity Feed</h2>
                    <button class="p-2 hover:bg-surface-container-high rounded-full transition-colors">
                        <span class="material-symbols-outlined text-xl">more_horiz</span>
                    </button>
                </div>
                <div class="flex flex-col gap-6">
                    <div class="flex gap-4 group">
                        <div class="relative flex-shrink-0">
                            <img alt="Sarah J." class="w-10 h-10 rounded-lg bg-surface-container-high"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAU8idqd-ioxhVvU28LZWUFC1m4tcRDnlNvfvwcU1K_cr3PdWLEtKV4YWZPN578DSkGTnB7AMaIrE4iTOEgqEWZZge1pSIxnQYlRGOwKJQ0th-uKq2X_V4ZHDuKeL3NFJPuL4jfdlwdT9SNJNzamX60LaXAWOhF5MW9cFyEpsmEVOytSAfNQ8I7dX0ITbgSVkLGNXcIGEvCXq145lfbfwP2J6VQlwP_0hsHuXJZVlbWqDgt7kXdVtn5Dsl0JofB7DhJDMj05PiYVRQ" />
                            <div
                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-primary text-white rounded-full flex items-center justify-center text-[8px]">
                                <span class="material-symbols-outlined text-[10px]"
                                    style="font-variation-settings: 'FILL' 1;">mode_comment</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-on-surface">Sarah Jenkins</span>
                                <span class="text-[10px] text-on-surface-variant">15m ago</span>
                            </div>
                            <p class="text-sm text-on-surface-variant leading-relaxed">"This approach to component
                                architecture is truly game-changing. I especially loved the section on dynamic grids!"
                            </p>
                            <div class="flex items-center gap-4 mt-1">
                                <button class="text-xs font-bold text-primary">Reply</button>
                                <button class="text-xs font-medium text-on-surface-variant">Approve</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-4 group">
                        <div class="relative flex-shrink-0">
                            <img alt="Marcus T." class="w-10 h-10 rounded-lg bg-surface-container-high"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuApvk3O3Hb9b0QdFDlE4CwEKDq02GgF8Mfu12_ZoAPTXewBEojtQIQAXiBJ2Dyiq953WkVySQ7effaG_gXNESMW2Vhu-gkiFZYCWzWIcRIoR5KhWtotR_Q1QdwQMRd_FrupT2EIXyfbWbhXcV19rxuiJYjIUii6xhK1nczj-7wlrxiN9yl_9gfrD1rEPPkdvHRKZkTBiqrBQadl8t-XFx8Oq0xT96qBG4cjjHamqF-FjB8TSrzMkmtLQ-cumhd3h0JgCsffmrtx0Ok" />
                            <div
                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-tertiary text-white rounded-full flex items-center justify-center text-[8px]">
                                <span class="material-symbols-outlined text-[10px]"
                                    style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-on-surface">Marcus Thorne</span>
                                <span class="text-[10px] text-on-surface-variant">1h ago</span>
                            </div>
                            <p class="text-sm text-on-surface-variant leading-relaxed">Liked your post <span
                                    class="font-semibold italic">"The Future of Generative UI"</span> and shared it with
                                their network.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 group">
                        <div class="relative flex-shrink-0">
                            <img alt="Elena V." class="w-10 h-10 rounded-lg bg-surface-container-high"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD1BowBy75yTp9vXh8Wr069c2s_6Gej7ug7wXIkik_dik2SfeMqcStCK072pel9y9KoRSvTiREzIxWenU2QzOd5oTeP8dda3Bh5vHaxHxb5cnJxQaOLJF5YWnduzgpQDGJihp1WTNwi4iq-amWKKwmWjJFAZY3VX01VTKWQKgQM5QTXTGOQhVSSNKBlkhfx0t90OJLM2aKbc7WmSRtoOeHms0atJkyt1EL7mCD5RGSkI5uOMQ-INyGDb9o1QUylvHACj007oDum2ZQ" />
                            <div
                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-primary text-white rounded-full flex items-center justify-center text-[8px]">
                                <span class="material-symbols-outlined text-[10px]"
                                    style="font-variation-settings: 'FILL' 1;">mode_comment</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-on-surface">Elena Vance</span>
                                <span class="text-[10px] text-on-surface-variant">3h ago</span>
                            </div>
                            <p class="text-sm text-on-surface-variant leading-relaxed">"Could you elaborate more on the
                                accessibility implications of these dynamic layouts? Great read!"</p>
                            <div class="flex items-center gap-4 mt-1">
                                <button class="text-xs font-bold text-primary">Reply</button>
                                <button class="text-xs font-medium text-on-surface-variant">Approve</button>
                            </div>
                        </div>
                    </div>
                    <div class="bg-surface-container-low p-5 rounded-xl mt-4">
                        <h4 class="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-3">Today's
                            Goal</h4>
                        <div class="flex flex-col gap-3">
                            <div class="flex justify-between items-center text-sm font-bold text-on-surface">
                                <span>3 Posts Left</span>
                                <span>70%</span>
                            </div>
                            <div class="w-full h-2 bg-white rounded-full overflow-hidden">
                                <div class="w-[70%] h-full bg-gradient-to-r from-primary to-primary-container"></div>
                            </div>
                            <p class="text-[11px] text-on-surface-variant italic">Reach 10 posts this week to maintain
                                your Editor Elite status.</p>
                        </div>
                    </div>
                </div>
            </section> -->
            <section class="lg:col-span-4 flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-on-surface">Activity Feed</h2>
                    <button class="p-2 hover:bg-surface-container-high rounded-full transition-colors">
                        <span class="material-symbols-outlined text-xl">more_horiz</span>
                    </button>
                </div>

                <!-- 2-column grid on sm+ screens, single col on lg (tight sidebar), 2-col again on xl -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-4">

                    <div
                        class="flex gap-4 group bg-surface-container-lowest rounded-xl p-4 border border-outline-variant/10 hover:bg-surface-container transition-colors">
                        <div class="relative flex-shrink-0">
                            <img alt="Sarah J." class="w-10 h-10 rounded-lg bg-surface-container-high"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAU8idqd-ioxhVvU28LZWUFC1m4tcRDnlNvfvwcU1K_cr3PdWLEtKV4YWZPN578DSkGTnB7AMaIrE4iTOEgqEWZZge1pSIxnQYlRGOwKJQ0th-uKq2X_V4ZHDuKeL3NFJPuL4jfdlwdT9SNJNzamX60LaXAWOhF5MW9cFyEpsmEVOytSAfNQ8I7dX0ITbgSVkLGNXcIGEvCXq145lfbfwP2J6VQlwP_0hsHuXJZVlbWqDgt7kXdVtn5Dsl0JofB7DhJDMj05PiYVRQ" />
                            <div
                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-primary text-white rounded-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-[10px]"
                                    style="font-variation-settings: 'FILL' 1;">mode_comment</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-bold text-on-surface">Sarah Jenkins</span>
                                <span class="text-[10px] text-on-surface-variant">15m ago</span>
                            </div>
                            <p class="text-sm text-on-surface-variant leading-relaxed line-clamp-3">"This approach to
                                component architecture is truly game-changing!"</p>
                            <div class="flex items-center gap-4 mt-1">
                                <button class="text-xs font-bold text-primary">Reply</button>
                                <button class="text-xs font-medium text-on-surface-variant">Approve</button>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex gap-4 group bg-surface-container-lowest rounded-xl p-4 border border-outline-variant/10 hover:bg-surface-container transition-colors">
                        <div class="relative flex-shrink-0">
                            <img alt="Marcus T." class="w-10 h-10 rounded-lg bg-surface-container-high"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuApvk3O3Hb9b0QdFDlE4CwEKDq02GgF8Mfu12_ZoAPTXewBEojtQIQAXiBJ2Dyiq953WkVySQ7effaG_gXNESMW2Vhu-gkiFZYCWzWIcRIoR5KhWtotR_Q1QdwQMRd_FrupT2EIXyfbWbhXcV19rxuiJYjIUii6xhK1nczj-7wlrxiN9yl_9gfrD1rEPPkdvHRKZkTBiqrBQadl8t-XFx8Oq0xT96qBG4cjjHamqF-FjB8TSrzMkmtLQ-cumhd3h0JgCsffmrtx0Ok" />
                            <div
                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-tertiary text-white rounded-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-[10px]"
                                    style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-bold text-on-surface">Marcus Thorne</span>
                                <span class="text-[10px] text-on-surface-variant">1h ago</span>
                            </div>
                            <p class="text-sm text-on-surface-variant leading-relaxed line-clamp-3">Liked your post
                                <span class="font-semibold italic">"The Future of Generative UI"</span> and shared it.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex gap-4 group bg-surface-container-lowest rounded-xl p-4 border border-outline-variant/10 hover:bg-surface-container transition-colors">
                        <div class="relative flex-shrink-0">
                            <img alt="Elena V." class="w-10 h-10 rounded-lg bg-surface-container-high"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD1BowBy75yTp9vXh8Wr069c2s_6Gej7ug7wXIkik_dik2SfeMqcStCK072pel9y9KoRSvTiREzIxWenU2QzOd5oTeP8dda3Bh5vHaxHxb5cnJxQaOLJF5YWnduzgpQDGJihp1WTNwi4iq-amWKKwmWjJFAZY3VX01VTKWQKgQM5QTXTGOQhVSSNKBlkhfx0t90OJLM2aKbc7WmSRtoOeHms0atJkyt1EL7mCD5RGSkI5uOMQ-INyGDb9o1QUylvHACj007oDum2ZQ" />
                            <div
                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-primary text-white rounded-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-[10px]"
                                    style="font-variation-settings: 'FILL' 1;">mode_comment</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-bold text-on-surface">Elena Vance</span>
                                <span class="text-[10px] text-on-surface-variant">3h ago</span>
                            </div>
                            <p class="text-sm text-on-surface-variant leading-relaxed line-clamp-3">"Could you elaborate
                                more on the accessibility implications? Great read!"</p>
                            <div class="flex items-center gap-4 mt-1">
                                <button class="text-xs font-bold text-primary">Reply</button>
                                <button class="text-xs font-medium text-on-surface-variant">Approve</button>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Goal spans full width -->
                    <div class="bg-surface-container-low p-5 rounded-xl sm:col-span-2 lg:col-span-1 xl:col-span-2">
                        <h4 class="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-3">Today's
                            Goal</h4>
                        <div class="flex flex-col gap-3">
                            <div class="flex justify-between items-center text-sm font-bold text-on-surface">
                                <span>3 Posts Left</span><span>70%</span>
                            </div>
                            <div class="w-full h-2 bg-white rounded-full overflow-hidden">
                                <div class="w-[70%] h-full bg-gradient-to-r from-primary to-primary-container"></div>
                            </div>
                            <p class="text-[11px] text-on-surface-variant italic">Reach 10 posts this week to maintain
                                your Editor Elite status.</p>
                        </div>
                    </div>

                </div>
            </section>
        </div>
    </main>
    <!-- Contextual FAB - mobile only -->
    <a href="edit-post.php"
        class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-br from-primary to-primary-container text-white rounded-2xl shadow-xl flex items-center justify-center group hover:scale-105 active:scale-95 transition-all md:hidden">
        <span class="material-symbols-outlined text-2xl">add</span>
    </a>
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

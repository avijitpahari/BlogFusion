<!DOCTYPE html>
<?php include "../include/session.php";
requireAuthor();
include "../include/db.php";
include "../include/author_nav_sidebar.php";
include "../config.php";
$year = date("Y");
$month = date("m");
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
                                    <button type="button"
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
                                        <option value="1">Technology</option>
                                        <option value="2">Editorial Design</option>
                                        <option value="3">UI/UX Strategy</option>
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
            automatic_uploads: true,
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
            document.getElementById('status').value='draft';
        }
        function pub() {
            document.getElementById('status').value='published';
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

            const tag = document.createElement('div');
            tag.className = "flex items-center bg-purple-50 text-purple-600 text-[10px] font-bold px-3 py-1.5 rounded-lg border border-purple-100 animate-fade-in group";

            tag.innerHTML = `
        <span class="tracking-wide">${value.toUpperCase()}</span>
        <button type="button" class="remove-tag ml-2 flex items-center text-purple-300 hover:text-red-500 transition-colors">
            <span class="material-icons" style="font-size: 14px;">close</span>
        </button>
    `;


            tag.querySelector('.remove-tag').onclick = (e) => {
                e.stopPropagation();
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
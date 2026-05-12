<!DOCTYPE html>
<?php include "../include/session.php";
requireAuthor();
include "../include/db.php";
include "../include/author_nav_sidebar.php";
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
            <div class="max-w-[1400px] mx-auto">
                <!-- Page Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight text-on-surface">Add New Post</h2>
                        <p class="text-on-surface-variant mt-1">Craft your next masterpiece with our Luminous Editor.
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button
                            class="px-6 py-2.5 rounded-xl text-primary font-semibold hover:bg-surface-container-high transition-all">Save
                            Draft</button>
                        <button
                            class="px-8 py-2.5 rounded-xl bg-gradient-to-br from-primary to-primary-container text-white font-bold shadow-lg shadow-primary/20 transition-all hover:scale-[1.02] active:scale-[0.98]">Publish
                            Post</button>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-8">
                    <!-- Left Column: Main Editor -->
                    <div class="col-span-12 lg:col-span-8 space-y-8">
                        <!-- Post Basic Info -->
                        <div
                            class="bg-surface-container-lowest rounded-3xl p-8 shadow-sm border border-outline-variant/10">
                            <div class="space-y-6">
                                <div>
                                    <label
                                        class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Title</label>
                                    <input
                                        class="w-full text-2xl font-bold bg-transparent border-none focus:ring-0 p-0 placeholder:text-surface-dim"
                                        placeholder="Enter post title here..." type="text" />
                                </div>
                                <div
                                    class="flex items-center space-x-2 py-2 px-4 bg-surface-container-low rounded-lg text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-sm">link</span>
                                    <span class="font-medium">Permalink:</span>
                                    <span class="text-primary truncate">https://blogfusion.com/posts/2024/05/</span>
                                    <input
                                        class="bg-transparent border-none p-0 focus:ring-0 text-primary-container font-semibold min-w-[200px]"
                                        type="text" value="the-future-of-editorial-design" />
                                    <button class="material-symbols-outlined text-sm hover:text-primary">edit</button>
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Short
                                        Description</label>
                                    <textarea
                                        class="w-full bg-surface-container-low border-none rounded-2xl focus:ring-2 focus:ring-primary/10 p-4 text-on-surface-variant resize-none"
                                        placeholder="Write a brief excerpt that summarizes the post..."
                                        rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- Content Editor -->
                        <div class="bg-surface-container-lowest rounded-3xl shadow-sm border border-outline-variant/10 overflow-hidden ring-2 ring-primary/40"
                            id="rich-text-editor-container"><textarea
                                class="w-full min-h-[600px] p-10 bg-white text-lg text-on-surface-variant placeholder:text-surface-dim placeholder:italic border-none focus:ring-0 resize-none outline-none"
                                placeholder="Start writing your story here..." id="editor"></textarea></div>
                        <!-- SEO Meta Section -->
                        <div
                            class="bg-surface-container-lowest rounded-3xl p-8 shadow-sm border border-outline-variant/10">
                            <h3 class="text-xl font-bold mb-6 flex items-center">
                                <span class="material-symbols-outlined mr-2 text-secondary">search_check</span>
                                Blog SEO Meta
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label
                                        class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Meta
                                        Title</label>
                                    <input
                                        class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/10 p-4 text-on-surface"
                                        placeholder="SEO Title for search engines" type="text" />
                                    <div class="mt-2 flex justify-end"><span
                                            class="text-[10px] font-medium text-primary">60 / 70 characters</span></div>
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Meta
                                        Description</label>
                                    <textarea
                                        class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/10 p-4 text-on-surface resize-none"
                                        placeholder="Brief summary for search engine results..." rows="2"></textarea>
                                    <div class="mt-2 flex justify-end"><span
                                            class="text-[10px] font-medium text-tertiary">145 / 160 characters</span>
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Meta
                                        Image (Social Sharing)</label>
                                    <div class="relative group">
                                        <!-- <div
                                            class="w-full h-40 bg-surface-container-high rounded-2xl flex flex-col items-center justify-center border-2 border-dashed border-outline-variant/30 group-hover:border-primary/30 transition-all overflow-hidden">
                                            <div class="text-center z-10 p-6">
                                                <span
                                                    class="material-symbols-outlined text-4xl text-on-surface-variant mb-2">add_photo_alternate</span>
                                                <p class="text-sm font-medium text-on-surface-variant">Drop image here
                                                    or click to upload</p>
                                                <p
                                                    class="text-[10px] text-on-surface-variant/60 mt-1 uppercase tracking-tighter">
                                                    Recommended: 1200 x 630px</p>
                                            </div>
                                            <div class="absolute inset-0 bg-cover bg-center opacity-0 group-hover:opacity-10 transition-opacity"
                                                data-alt="abstract tech code on dark screen with vibrant purple and cyan glowing lights"
                                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBkpJByRRwkHteYofwaJYZtEA1Y6F01jGWCiaY8tJKlf0B9-8444jhcACRECkHbw4MZ_Pb3Xd_Gl92bnu95PrMMIVPMpGWro1xVsh90iuhBrIV-_SkDTi9xzycQdmZdBJDB4dys6EHO6IyvhBJgAwQPCaGFlRcP8tVuB0BFt6ip3A5WbERJRn7THICjooVoLDNv-nyN3LL0rh_TKGNfzKlmY5rp5238GFhnlmCi1n34MQMFVdCeYu8w3sm32PO_cQqGKyZzG6VCfYg')">
                                            </div>
                                        </div> -->
                                        <div class="mt-4">

                                            <label for="meta_image_input" id="meta-drop-area"
                                                class="border-2 border-dashed border-purple-100 rounded-2xl p-8 text-center cursor-pointer block bg-purple-50/20 hover:bg-purple-50/50 transition-all relative">

                                                <input type="file" name="meta_image" id="meta_image_input"
                                                    accept="image/*" hidden>

                                                <div id="upload-placeholder" class="flex flex-col items-center">
                                                    <div
                                                        class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-3">
                                                        <span
                                                            class="material-icons text-purple-500">add_photo_alternate</span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 font-medium">Drop image here or
                                                        click to upload</p>
                                                    <p class="text-[10px] text-gray-400 uppercase mt-1 tracking-wider">
                                                        Recommended: 1200 x 630px</p>
                                                </div>

                                                <div id="image-preview-wrapper"
                                                    class="hidden relative inline-block group">
                                                    <img id="meta-preview-image" src="#" alt="Meta Preview"
                                                        class="max-h-56 mx-auto rounded-xl shadow-lg border-4 border-white">

                                                    <button type="button" id="remove-img-btn"
                                                        class="absolute -top-3 -right-3 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-xl hover:bg-red-600 hover:scale-110 transition-all border-2 border-white">
                                                        <span class="material-icons text-sm">close</span>
                                                    </button>

                                                    <div class="mt-3">
                                                        <span
                                                            class="text-[10px] bg-purple-100 text-purple-600 px-3 py-1 rounded-full font-bold uppercase tracking-tighter">Click
                                                            to replace image</span>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Right Column: Sidebar Panels -->
                    <div class="col-span-12 lg:col-span-4 space-y-6">
                        <!-- Publish Panel -->
                        <div
                            class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm border border-outline-variant/10">
                            <h3 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-4">Publish
                                Status</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="flex items-center text-on-surface-variant">
                                        <span class="material-symbols-outlined text-sm mr-2">visibility</span>
                                        Visibility:
                                    </span>
                                    <span class="font-bold text-primary">Public</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="flex items-center text-on-surface-variant">
                                        <span class="material-symbols-outlined text-sm mr-2">event</span>
                                        Publish:
                                    </span>
                                    <span class="font-bold text-on-surface">Immediately</span>
                                </div>
                                <div class="pt-4 flex flex-col gap-2">
                                    <button
                                        class="w-full py-2 bg-surface-container-high rounded-lg text-on-surface font-semibold hover:bg-surface-container-highest transition-all flex items-center justify-center">
                                        <span class="material-symbols-outlined text-sm mr-2">pending</span>
                                        Pending Review
                                    </button>
                                    <div class="flex gap-2">
                                        <button
                                            class="flex-1 py-2 bg-primary-fixed rounded-lg text-on-primary-fixed-variant font-bold hover:bg-primary-fixed-dim transition-all">Update</button>
                                        <button
                                            class="flex-1 py-2 text-error font-semibold hover:bg-error-container/20 rounded-lg transition-all">Move
                                            to Trash</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Featured Status -->
                        <div
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
                        </div>
                        <!-- Categories Panel -->
                        <div
                            class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm border border-outline-variant/10">
                            <h3 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-4">Blog
                                Categories</h3>
                            <div class="max-h-48 overflow-y-auto custom-scrollbar space-y-2 pr-2">
                                <label class="flex items-center group cursor-pointer">
                                    <input
                                        class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20"
                                        type="checkbox" />
                                    <span
                                        class="ml-3 text-sm text-on-surface group-hover:text-primary transition-colors">Technology</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input checked=""
                                        class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20"
                                        type="checkbox" />
                                    <span
                                        class="ml-3 text-sm text-on-surface group-hover:text-primary transition-colors">Editorial
                                        Design</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input
                                        class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20"
                                        type="checkbox" />
                                    <span
                                        class="ml-3 text-sm text-on-surface group-hover:text-primary transition-colors">UI/UX
                                        Strategy</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input
                                        class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/20"
                                        type="checkbox" />
                                    <span
                                        class="ml-3 text-sm text-on-surface group-hover:text-primary transition-colors">Creative
                                        Process</span>
                                </label>
                            </div>
                            <button class="mt-4 text-xs font-bold text-primary flex items-center hover:underline">
                                <span class="material-symbols-outlined text-sm mr-1">add</span>
                                Add New Category
                            </button>
                        </div>
                        <!-- Tags Panel -->
                        <div
                            class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm border border-outline-variant/10">
                            <h3 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-4">Blog
                                Tags</h3>
                            <div class="relative mb-4">
                                <input
                                    class="w-full bg-surface-container-low border-none rounded-xl py-2 px-4 text-sm focus:ring-2 focus:ring-primary/10"
                                    placeholder="Add tags..." type="text" />
                                <button
                                    class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-primary hover:bg-white rounded-md transition-all">
                                    <span class="material-symbols-outlined text-sm">add_circle</span>
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    class="px-3 py-1 bg-violet-50 text-violet-700 text-[10px] font-bold rounded-full border border-violet-100 flex items-center">
                                    DESIGN <button class="material-symbols-outlined text-[12px] ml-1">close</button>
                                </span>
                                <span
                                    class="px-3 py-1 bg-violet-50 text-violet-700 text-[10px] font-bold rounded-full border border-violet-100 flex items-center">
                                    LAYOUT <button class="material-symbols-outlined text-[12px] ml-1">close</button>
                                </span>
                                <span
                                    class="px-3 py-1 bg-violet-50 text-violet-700 text-[10px] font-bold rounded-full border border-violet-100 flex items-center">
                                    CHROMA <button class="material-symbols-outlined text-[12px] ml-1">close</button>
                                </span>
                            </div>
                            <p class="text-[10px] text-on-surface-variant/50 mt-3 italic">Separate tags with commas</p>
                        </div>
                        <!-- Blog Image Panel -->
                        <div
                            class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm border border-outline-variant/10">
                            <h3 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-4">
                                Featured Image</h3>
                            <div
                                class="relative group aspect-video rounded-2xl bg-surface-container-high overflow-hidden border border-outline-variant/20 mb-4">
                                <img alt="Blog feature preview"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    data-alt="minimalist organic abstract background with smooth flowing 3d curves in shades of lavender and violet"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuD0CZyVKjPmPnx-PwsuiCrB7owPZAfEBWWtS3l6tr212sc1QK40aGRVxaq0jZQIY4D8J8QPpTw9eYaUWXmVPYtRQCVqS4S5R-Ceq5Vdx49uow5yi3wDLM9B3qy7EO8B-EQRqU5GuJm-VOBoMKAy_gmoTwJCWePrDV3UWklzVnmC_aOZJmQZK381r6AadRkcmDJsUxVhrJY6_tiQr_RyOrDGOWp8i0hmqf-Y49hVozRjLja81xsPH3YydCpSgP55s6PyyTwX36RXtWU" />
                                <div
                                    class="absolute inset-0 bg-on-surface/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                    <button
                                        class="p-3 bg-white text-on-surface rounded-full shadow-lg hover:bg-primary-container hover:text-white transition-all">
                                        <span class="material-symbols-outlined">sync</span>
                                    </button>
                                    <button
                                        class="p-3 bg-white text-error rounded-full shadow-lg hover:bg-error hover:text-white transition-all">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </div>
                            <button
                                class="w-full py-3 bg-surface-container-low text-primary font-bold rounded-xl border border-dashed border-primary/20 hover:bg-white transition-all flex items-center justify-center">
                                <span class="material-symbols-outlined mr-2">upload_file</span>
                                Replace Image
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
        function showContent() {
            const content = tinymce.get('editor').getContent();
            //console.log(content);
            document.getElementById('rich-text-editor-container').innerHTML = content;
        }
        function insertTag(tag) {
            tinymce.get('editor').insertContent(tag);
        }
        let elements = document.getElementsByClassName("tox-promotion");

        for (let i = 0; i < elements.length; i++) {
            elements[i].style.display = "none";
        }
        const metaInput = document.getElementById('meta_image_input');
        const placeholder = document.getElementById('upload-placeholder');
        const previewWrapper = document.getElementById('image-preview-wrapper');
        const previewImage = document.getElementById('meta-preview-image');
        const removeBtn = document.getElementById('remove-img-btn');

        // 1. Image Preview Logic
        metaInput.addEventListener('change', function (e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    placeholder.classList.add('hidden');
                    previewWrapper.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        // Remove/Cross Button Logic
        removeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            metaInput.value = "";
            previewImage.src = "#";

            // UI Reset
            placeholder.classList.remove('hidden');
            previewWrapper.classList.add('hidden');
        });
    </script>
</body>

</html>
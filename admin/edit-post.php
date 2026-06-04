<?php include "../include/session.php";
requireAdmin();
include "../include/db.php";
include "../include/admin_nav_sidebar.php";
include "../config.php";
include "../include/functions.php";

$is_edit = false;
$post_data = [];
if (isset($_GET['id'])) {
    $post_id = (int)$_GET['id'];
    // Admin can edit any post — no author_id restriction
    $post_res = mysqli_query($conn, "SELECT * FROM posts WHERE id = $post_id LIMIT 1");
    if ($post_res && mysqli_num_rows($post_res) > 0) {
        $is_edit = true;
        $post_data = mysqli_fetch_assoc($post_res);
    }
}

$year  = date("Y");
$month = date("m");
$cat_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= $is_edit ? 'Edit Post' : 'Add New Post' ?> | Blog Fusion Admin</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#7C3AED",
                        "primary-hover": "#6D28D9",
                        "background-light": "#f8f7ff",
                        "background-dark": "#0f172a",
                        "on-surface": "#1d1a24",
                        "surface": "#fef7ff",
                        "on-surface-variant": "#4a4455",
                        "surface-container-lowest": "#ffffff",
                        "surface-container": "#f3ebfa",
                        "outline-variant": "#ccc3d8",
                        "surface-dim": "#dfd7e6",
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"],
                        "body": ["Public Sans", "sans-serif"],
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="../vendor/tinymce/tinymce.min.js"></script>
    <style>
        body { font-family: 'Public Sans', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
    <div class="flex h-screen overflow-hidden">
        <!-- SideNavBar -->
        <?= slidebar('posts'); ?>
        <div id="overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <?= ad_navbar(); ?>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-6 md:p-8">
                <form action="<?= $is_edit ? '../actions/admin_post_update.php' : '../actions/admin_post.php' ?>" method="POST" enctype="multipart/form-data">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?= (int)$post_data['id'] ?>" />
                        <input type="hidden" name="old_image" value="<?= htmlspecialchars($post_data['image'] ?? '') ?>" />
                        <input type="hidden" name="delete_image" id="delete_image" value="0" />
                    <?php endif; ?>

                    <div class="max-w-[1400px] mx-auto">
                        <!-- Page Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 sm:mb-8 gap-4">
                            <div>
                                <h2 class="text-2xl sm:text-3xl font-black tracking-tight"><?= $is_edit ? 'Edit Post' : 'Add New Post' ?></h2>
                                <p class="text-slate-500 mt-1 text-sm"><?= $is_edit ? 'Modify and update the existing post' : 'Create a new blog post and publish it' ?></p>
                            </div>
                            <a href="posts.php" class="inline-flex items-center gap-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2.5 rounded-xl font-bold text-sm transition-colors">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                Back to Posts
                            </a>
                        </div>

                        <!-- Two-column grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
                            <!-- Left Column: Main Editor -->
                            <div class="lg:col-span-8 space-y-6">

                                <!-- Post Basic Info -->
                                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200 dark:border-slate-800">
                                    <!-- Title -->
                                    <div class="mb-6">
                                        <label class="block text-[10px] font-bold text-slate-400 mb-2 uppercase tracking-[2px]">Post Title</label>
                                        <input type="text" id="post-title" name="title"
                                            placeholder="Enter post title here..."
                                            value="<?= htmlspecialchars($post_data['title'] ?? '') ?>"
                                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm rounded-xl py-3 sm:py-4 px-5 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all text-slate-900 dark:text-slate-100">
                                        <div class="flex items-center flex-wrap mt-2 text-[11px] text-slate-500 bg-slate-50 dark:bg-slate-800 w-fit px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 gap-1">
                                            <span class="material-symbols-outlined text-sm text-primary">link</span>
                                            <span>Permalink:</span>
                                            <span class="text-slate-400"><?= POST_URL . $year . "/" . $month . "/" ?></span>
                                            <span id="permalink-slug" class="font-bold text-primary"><?= htmlspecialchars($post_data['slug'] ?? 'your-post-title') ?></span>
                                            <input type="hidden" name="slug" id="slug" value="<?= htmlspecialchars($post_data['slug'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <!-- Short Description -->
                                    <div class="mb-6">
                                        <div class="flex justify-between items-end mb-2">
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-[2px]">Short Description</label>
                                            <span id="char-count" class="text-[10px] text-slate-400 font-medium italic">0 / 160 characters</span>
                                        </div>
                                        <textarea id="short-desc" name="short_description" rows="3" maxlength="160"
                                            placeholder="Write a brief excerpt that summarizes the post..."
                                            class="w-full p-4 sm:p-5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all resize-none shadow-sm"><?= htmlspecialchars($post_data['description'] ?? '') ?></textarea>
                                    </div>

                                    <!-- Blog Image Upload -->
                                    <div class="mb-2">
                                        <label class="block text-[10px] font-bold text-slate-400 mb-3 uppercase tracking-widest">Featured Image</label>
                                        <label for="seo_image_input"
                                            class="relative border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-6 sm:p-8 text-center cursor-pointer block bg-slate-50/50 dark:bg-slate-800/50 hover:bg-slate-100/60 dark:hover:bg-slate-800 transition-all group overflow-hidden">
                                            <input type="file" name="blog_image" id="seo_image_input" accept="image/*" hidden>
                                            <div id="seo-placeholder" class="flex flex-col items-center">
                                                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-3">
                                                    <span class="material-symbols-outlined text-primary">add_photo_alternate</span>
                                                </div>
                                                <p class="text-sm text-slate-600 dark:text-slate-300 font-medium">Drop image here or click to upload</p>
                                                <p class="text-[10px] text-slate-400 uppercase mt-1 tracking-widest font-bold">Recommended: 1200 x 630px</p>
                                            </div>
                                            <div id="seo-preview-wrapper" class="hidden relative inline-block group">
                                                <img id="seo-preview-image" src="#"
                                                    class="max-h-48 sm:max-h-56 rounded-xl shadow-lg border-4 border-white dark:border-slate-700 mx-auto">
                                                <button type="button" id="remove-seo-img"
                                                    class="absolute -top-3 -right-3 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg hover:bg-red-600 border-2 border-white transition-all">
                                                    <span class="material-symbols-outlined text-sm">close</span>
                                                </button>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Content Editor -->
                                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden ring-2 ring-primary/30" id="rich-text-editor-container">
                                    <textarea
                                        class="w-full min-h-[400px] sm:min-h-[600px] p-6 sm:p-10 bg-white text-base sm:text-lg text-slate-700 placeholder:text-slate-400 placeholder:italic border-none focus:ring-0 resize-none outline-none"
                                        placeholder="Start writing your story here..." id="editor" name="content"><?php if ($is_edit): ?><?= htmlspecialchars($post_data['content'] ?? '') ?><?php endif; ?></textarea>
                                </div>

                            </div>

                            <!-- Right Column: Sidebar Panels -->
                            <div class="lg:col-span-4 space-y-4 sm:space-y-6">

                                <!-- Publish Panel -->
                                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Publish Status</h3>
                                        <span id="status-badge"
                                            class="<?= (isset($post_data['status']) && $post_data['status'] === 'published') ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' ?> text-[10px] px-2 py-1 rounded-full font-bold uppercase">
                                            <?= htmlspecialchars(ucfirst($post_data['status'] ?? 'Draft')) ?>
                                        </span>
                                    </div>
                                    <div class="space-y-3 mb-6">
                                        <div class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                                            <span class="material-symbols-outlined text-slate-400 mr-2" style="font-size:18px;">visibility</span>
                                            <span>Visibility: <strong class="text-primary ml-1">Public</strong></span>
                                        </div>
                                        <div class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                                            <span class="material-symbols-outlined text-slate-400 mr-2" style="font-size:18px;">event</span>
                                            <span>Publish: <strong class="ml-1">Immediately</strong></span>
                                        </div>
                                        <div class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                                            <span class="material-symbols-outlined text-slate-400 mr-2" style="font-size:18px;">shield</span>
                                            <span>Role: <strong class="text-primary ml-1">Admin</strong></span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="submit" name="save_draft" onclick="dra()"
                                            class="py-2.5 px-4 bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-300 rounded-xl font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-700 transition-all border border-slate-200 dark:border-slate-700">
                                            SAVE DRAFT
                                        </button>
                                        <button type="submit" name="publish_post" onclick="pub()"
                                            class="py-2.5 px-4 bg-primary text-white rounded-xl font-bold text-xs hover:bg-primary-hover shadow-md shadow-primary/20 transition-all">
                                            PUBLISH
                                        </button>
                                    </div>
                                    <input type="hidden" name="status" id="status" value="<?= htmlspecialchars($post_data['status'] ?? 'draft') ?>">
                                    <?php if ($is_edit): ?>
                                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-center">
                                        <a href="posts.php" class="text-slate-400 text-[10px] font-bold uppercase hover:text-slate-600 flex items-center transition-all">
                                            <span class="material-symbols-outlined text-sm mr-1">arrow_back</span>
                                            Cancel & Back
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Categories Panel -->
                                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
                                    <label class="block text-[10px] font-bold text-slate-400 mb-3 uppercase tracking-widest">Blog Category</label>
                                    <div class="relative mb-4">
                                        <select name="category_id"
                                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs rounded-xl py-3 px-4 appearance-none focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all cursor-pointer" required>
                                            <option value="" disabled <?= !$is_edit ? 'selected' : '' ?>>Select a Category</option>
                                            <?php while ($cat = mysqli_fetch_assoc($cat_result)): ?>
                                                <option value="<?= $cat['id'] ?>" <?= (isset($post_data['category_id']) && (int)$post_data['category_id'] === (int)$cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-primary">
                                            <span class="material-symbols-outlined text-sm">expand_more</span>
                                        </div>
                                    </div>
                                    <!-- Add New Category -->
                                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                                        <button type="button" id="add-cat-toggle"
                                            class="flex items-center text-primary hover:text-primary-hover transition-all group focus:outline-none">
                                            <div class="w-7 h-7 bg-primary/10 rounded-lg flex items-center justify-center mr-2 group-hover:bg-primary/20 transition-colors border border-primary/20">
                                                <span class="material-symbols-outlined text-sm" id="toggle-icon">add</span>
                                            </div>
                                            <span class="text-[10px] font-bold uppercase tracking-widest" id="toggle-text">Add New Category</span>
                                        </button>
                                        <div id="new-cat-input-wrapper" class="hidden mt-4">
                                            <div class="flex flex-col sm:flex-row items-stretch gap-2 p-1.5 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                                                <input type="text" id="new_category_name" placeholder="Category name..."
                                                    class="flex-1 min-w-0 bg-transparent text-xs py-2 px-3 focus:outline-none border-none text-slate-700 dark:text-slate-200">
                                                <button type="button" id="add-cat-btn"
                                                    class="bg-primary text-white px-4 py-2 rounded-lg text-[10px] font-bold hover:bg-primary-hover transition-all shadow-sm uppercase tracking-tighter shrink-0">
                                                    ADD
                                                </button>
                                            </div>
                                            <p id="cat-add-msg" class="text-[10px] mt-1 hidden"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tags Panel -->
                                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
                                    <div class="flex justify-between items-center mb-3">
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Blog Tags</label>
                                        <button type="button" id="remove-all-tags"
                                            class="text-[9px] font-bold text-red-400 hover:text-red-600 uppercase tracking-tighter transition-all focus:outline-none">
                                            Clear All
                                        </button>
                                    </div>
                                    <div class="relative mb-4">
                                        <input type="text" id="tag-input"
                                            placeholder="Type tags, separate with comma..."
                                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-xl py-3 pl-4 pr-12 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all text-slate-700 dark:text-slate-200">
                                        <button type="button" id="add-tag-btn"
                                            class="absolute right-2 top-1.5 w-8 h-8 bg-primary text-white rounded-lg hover:bg-primary-hover transition-all shadow-md flex items-center justify-center">
                                            <span class="material-symbols-outlined" style="font-size:18px;">add</span>
                                        </button>
                                    </div>
                                    <div id="tags-container" class="flex flex-wrap gap-2"></div>
                                    <div id="tags-hidden-inputs"></div>
                                </div>

                            </div><!-- end right col -->
                        </div><!-- end grid -->
                    </div><!-- end max-w -->
                </form>
            </div><!-- end scrollable -->
        </main>
    </div>

    <script src="../assets/js/admin.js"></script>
    <script>
        // ── TinyMCE Init ──────────────────────────────────────────────────────────
        tinymce.init({
            selector: '#editor',
            license_key: 'gpl',
            plugins: 'autoresize lists link image table code preview',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | alignleft aligncenter alignright justify | bullist numlist | forecolor backcolor | link image table | code preview',
            image_title: true,
            automatic_upload: true,
            images_upload_url: '../actions/upload_image.php',
            images_upload_base_path: '',
            relative_urls: false,
            remove_script_host: false,
            images_reuse_filename: true,
            setup: function (editor) {
                editor.on('change', function () { editor.save(); });
            },
            content_style: "body { font-family: 'Public Sans', Arial; font-size:14px }",
            autoresize_bottom_margin: 20,
            min_height: 300,
            max_height: 1000
        });

        // ── Status buttons ────────────────────────────────────────────────────────
        function dra() { document.getElementById('status').value = 'draft'; }
        function pub() { document.getElementById('status').value = 'published'; }

        // ── Char counter ──────────────────────────────────────────────────────────
        const shortDesc = document.getElementById('short-desc');
        const charCount = document.getElementById('char-count');
        function updateCharCount() {
            const length = shortDesc.value.length;
            charCount.innerText = `${length} / 160 characters`;
            charCount.classList.toggle('text-red-400', length >= 150);
            charCount.classList.toggle('text-slate-400', length < 150);
        }
        shortDesc.addEventListener('input', updateCharCount);
        updateCharCount(); // init on load

        // ── Slug generator ────────────────────────────────────────────────────────
        const titleInput   = document.getElementById('post-title');
        const slugDisplay  = document.getElementById('permalink-slug');
        const slugInput    = document.getElementById('slug');
        titleInput.addEventListener('input', function () {
            let slug = this.value.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
            slugDisplay.innerText = slug || 'your-post-title';
            slugInput.value = slug;
        });

        // ── Featured Image Preview ────────────────────────────────────────────────
        const seoInput          = document.getElementById('seo_image_input');
        const seoPlaceholder    = document.getElementById('seo-placeholder');
        const seoPreviewWrapper = document.getElementById('seo-preview-wrapper');
        const seoPreviewImage   = document.getElementById('seo-preview-image');
        const removeSeoBtn      = document.getElementById('remove-seo-img');

        // Pre-fill existing image when editing
        <?php if ($is_edit && !empty($post_data['image'])): ?>
        (function () {
            const existingImg = '../<?= htmlspecialchars($post_data['image']) ?>';
            seoPreviewImage.src = existingImg;
            seoPlaceholder.classList.add('hidden');
            seoPreviewWrapper.classList.remove('hidden');
        })();
        <?php endif; ?>

        seoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    seoPreviewImage.src = e.target.result;
                    seoPlaceholder.classList.add('hidden');
                    seoPreviewWrapper.classList.remove('hidden');
                    const delInput = document.getElementById('delete_image');
                    if (delInput) delInput.value = "0";
                };
                reader.readAsDataURL(file);
            }
        });

        removeSeoBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            seoInput.value = "";
            seoPreviewImage.src = "#";
            seoPlaceholder.classList.remove('hidden');
            seoPreviewWrapper.classList.add('hidden');
            const delInput = document.getElementById('delete_image');
            if (delInput) delInput.value = "1";
        });

        // ── Categories toggle ─────────────────────────────────────────────────────
        document.getElementById('add-cat-toggle').addEventListener('click', function () {
            const wrapper = document.getElementById('new-cat-input-wrapper');
            const icon    = document.getElementById('toggle-icon');
            const text    = document.getElementById('toggle-text');
            wrapper.classList.toggle('hidden');
            const isOpen = !wrapper.classList.contains('hidden');
            icon.innerText = isOpen ? 'close' : 'add';
            text.innerText = isOpen ? 'Cancel' : 'Add New Category';
            this.classList.toggle('text-primary', !isOpen);
            this.classList.toggle('text-red-400', isOpen);
        });

        // ── Add category via AJAX ─────────────────────────────────────────────────
        document.getElementById('add-cat-btn').addEventListener('click', function () {
            const nameInput = document.getElementById('new_category_name');
            const msg       = document.getElementById('cat-add-msg');
            const name      = nameInput.value.trim();
            if (!name) return;

            fetch('../actions/admin_add_category.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'name=' + encodeURIComponent(name)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const select = document.querySelector('select[name="category_id"]');
                    const opt    = document.createElement('option');
                    opt.value    = data.id;
                    opt.text     = data.name;
                    opt.selected = true;
                    select.appendChild(opt);
                    nameInput.value = '';
                    msg.textContent = '✓ Category added!';
                    msg.className   = 'text-[10px] mt-1 text-green-600';
                    msg.classList.remove('hidden');
                    setTimeout(() => msg.classList.add('hidden'), 3000);
                } else {
                    msg.textContent = data.message || 'Failed to add category.';
                    msg.className   = 'text-[10px] mt-1 text-red-500';
                    msg.classList.remove('hidden');
                }
            })
            .catch(() => {
                msg.textContent = 'Network error.';
                msg.className   = 'text-[10px] mt-1 text-red-500';
                msg.classList.remove('hidden');
            });
        });

        // ── Tags ──────────────────────────────────────────────────────────────────
        const tagInput        = document.getElementById('tag-input');
        const tagsContainer   = document.getElementById('tags-container');
        const addTagBtn       = document.getElementById('add-tag-btn');
        const removeAllBtn    = document.getElementById('remove-all-tags');

        function createTagUI(label) {
            const value = label.trim();
            if (!value) return;
            const existing = [...tagsContainer.querySelectorAll('span.tracking-wide')].map(s => s.innerText.toLowerCase());
            if (existing.includes(value.toLowerCase())) return;

            const tag = document.createElement('div');
            tag.className = "flex items-center bg-primary/10 text-primary text-[10px] font-bold px-3 py-1.5 rounded-lg border border-primary/20 group";
            tag.dataset.tagValue = value;
            tag.innerHTML = `
                <span class="tracking-wide">${value.toUpperCase()}</span>
                <button type="button" class="remove-tag ml-2 flex items-center text-primary/40 hover:text-red-500 transition-colors">
                    <span class="material-symbols-outlined" style="font-size:14px;">close</span>
                </button>`;

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'tags[]';
            hiddenInput.value = value;
            hiddenInput.id = 'tag-hidden-' + value.replace(/\s+/g, '-');
            document.getElementById('tags-hidden-inputs').appendChild(hiddenInput);

            tag.querySelector('.remove-tag').onclick = (e) => {
                e.stopPropagation();
                const inp = document.getElementById('tag-hidden-' + value.replace(/\s+/g, '-'));
                if (inp) inp.remove();
                tag.remove();
            };
            tagsContainer.appendChild(tag);
        }

        function forceAddTags() {
            tagInput.value.split(',').forEach(p => createTagUI(p));
            tagInput.value = "";
        }

        tagInput.addEventListener('input', function () {
            if (this.value.includes(',')) {
                const parts = this.value.split(',');
                parts.slice(0, -1).forEach(p => createTagUI(p));
                this.value = parts[parts.length - 1];
            }
        });

        addTagBtn.onclick = (e) => { e.preventDefault(); forceAddTags(); };
        tagInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); forceAddTags(); } });
        removeAllBtn.onclick = () => {
            if (tagsContainer.children.length > 0) {
                if (confirm("Delete all tags?")) {
                    tagsContainer.innerHTML = "";
                    document.getElementById('tags-hidden-inputs').innerHTML = "";
                }
            }
        };

        // Pre-fill existing tags when editing
        <?php if ($is_edit && !empty($post_data['tag'])): ?>
        (function () {
            const existingTags = <?= json_encode(json_decode(trim($post_data['tag']), true) ?? []) ?>;
            existingTags.forEach(tag => createTagUI(tag));
        })();
        <?php endif; ?>
    </script>
</body>

</html>

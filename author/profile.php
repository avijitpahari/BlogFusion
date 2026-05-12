<?php include "../include/session.php";
requireAuthor();
include "../include/db.php";
include "../include/author_nav_sidebar.php";
global $data1;
$sql = "SELECT SUM(CASE WHEN status='published' THEN 1 ELSE 0 END) AS published_posts FROM posts WHERE author_id = {$data1['id']}; ";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>User Profile | Luminous Editor</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "tertiary-container": "#bf2076",
                        "secondary-fixed-dim": "#c3c0ff",
                        "inverse-surface": "#332f39",
                        "on-tertiary-fixed": "#3e0022",
                        "surface-container-low": "#f9f1ff",
                        "primary-container": "#7c3aed",
                        "surface-container-lowest": "#ffffff",
                        "secondary-container": "#645efb",
                        "on-primary-container": "#ede0ff",
                        "secondary": "#4b41e1",
                        "outline": "#7b7487",
                        "inverse-primary": "#d2bbff",
                        "on-error-container": "#93000a",
                        "on-background": "#1d1a24",
                        "primary": "#630ed4",
                        "primary-fixed-dim": "#d2bbff",
                        "inverse-on-surface": "#f6eefc",
                        "surface-container-highest": "#e8dfee",
                        "on-secondary-fixed-variant": "#3323cc",
                        "surface-tint": "#732ee4",
                        "on-tertiary-fixed-variant": "#8c0053",
                        "on-error": "#ffffff",
                        "background": "#fef7ff",
                        "on-primary-fixed-variant": "#5a00c6",
                        "on-primary": "#ffffff",
                        "tertiary-fixed": "#ffd9e4",
                        "on-secondary-container": "#fffbff",
                        "on-primary-fixed": "#25005a",
                        "tertiary-fixed-dim": "#ffb0cd",
                        "surface": "#fef7ff",
                        "surface-container": "#f3ebfa",
                        "surface-bright": "#fef7ff",
                        "secondary-fixed": "#e2dfff",
                        "on-tertiary-container": "#ffdde7",
                        "on-tertiary": "#ffffff",
                        "on-secondary-fixed": "#0f0069",
                        "surface-container-high": "#ede5f4",
                        "primary-fixed": "#eaddff",
                        "surface-variant": "#e8dfee",
                        "tertiary": "#9b005c",
                        "error": "#ba1a1a",
                        "on-surface-variant": "#4a4455",
                        "outline-variant": "#ccc3d8",
                        "surface-dim": "#dfd7e6",
                        "on-secondary": "#ffffff",
                        "on-surface": "#1d1a24",
                        "error-container": "#ffdad6"
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

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
        }

        .modal-backdrop {
            background: rgba(29, 26, 36, 0.4);
            backdrop-filter: blur(8px);
        }

        @media (max-width: 768px) {
            .sidebar-closed {
                transform: translateX(-100%);
            }

            .sidebar-open {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="bg-surface text-on-surface antialiased">
    <!-- Modal Component -->
    <div class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4" id="edit-profile-modal">
        <div class="absolute inset-0 modal-backdrop" onclick="closeModal()"></div>
        <div
            class="relative bg-surface-container-lowest w-full max-w-lg rounded-[2.5rem] shadow-2xl shadow-primary/20 overflow-hidden border border-outline-variant/20">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-on-surface">Edit Profile</h3>
                    <button
                        class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container-high transition-colors"
                        onclick="closeModal()">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form class="space-y-5" enctype="multipart/form-data" action="../actions/author.php" method="POST">
                    <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1" for="name">Name</label>
                        <input
                            class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none"
                            id="edit_name" type="text" value="Alex Rivera" name="name" />
                    </div>
                    <input type="hidden" id="id" name="id">
                    <!-- <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1" for="bio">Bio</label>
                        <textarea
                            class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none resize-none"
                            id="bio"
                            rows="3">Editor-in-Chief at Blog Fusion. Digital storyteller and content strategist.</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1"
                                for="location">Location</label>
                            <input
                                class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none"
                                id="location" type="text" value="San Francisco, CA" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-on-surface-variant mb-2 ml-1"
                                for="website">Website</label>
                            <input
                                class="w-full bg-surface-container border-none rounded-2xl px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary/20 transition-all outline-none"
                                id="website" placeholder="https://" type="text" />
                        </div>
                    </div> -->


                    <!-- === image === -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Profile Picture</label>
                        <div
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-accent transition duration-200">
                            <div class="space-y-1 text-center w-full relative group">
                                <!-- Placeholder UI -->
                                <div class="block" id="upload-placeholder">
                                    <svg aria-hidden="true" class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                        stroke="currentColor" viewbox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-indigo-500"
                                            for="file-upload">
                                            <span>Upload a file</span>
                                            <input accept="image/*" class="sr-only" id="file-upload" name="new_image"
                                                onchange="handleImagePreview(event)" type="file" />
                                            <input type="hidden" name="old_image" id="old-image" value="">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG up to 10MB</p>
                                </div>
                                <!-- Image Preview UI -->
                                <div class="hidden relative inline-block mx-auto" id="preview-container">
                                    <img alt="Profile Preview"
                                        class="h-32 w-32 object-cover rounded-full border-4 border-white shadow-md mx-auto"
                                        id="image-preview" src="" />
                                    <button
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 transition-colors"
                                        onclick="removeImage()" type="button">
                                        <span class="material-symbols-outlined text-sm">close</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

            </div>
            <div class="flex gap-3 p-8 pt-0">
                <button
                    class="flex-1 py-4 rounded-2xl font-bold bg-surface-container-high text-on-surface hover:bg-surface-container-highest transition-all active:scale-[0.98]"
                    onclick="closeModal()">
                    Cancel
                </button>
                <button type="submit" name="profile_update"
                    class="flex-1 py-4 rounded-2xl font-bold bg-primary text-white shadow-lg shadow-primary/20 hover:bg-primary-container transition-all active:scale-[0.98]">
                    Save Changes
                </button>
            </div>
            </form>
        </div>
    </div>
    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-black/40 z-40 md:hidden hidden transition-opacity" id="sidebar-overlay"
        onclick="toggleSidebar()"></div>
    <!-- SideNavBar Component -->
    <?= author_slidebar('profile') ?>
    <!-- TopNavBar Component -->
    <?= author_navbar(); ?>
    <!-- Main Content Area -->
    <main class="md:ml-64 pt-24 px-4 md:px-12 pb-12 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <!-- Section 1: Profile Overview -->
            <section class="bg-surface-container-low rounded-[2rem] p-6 md:p-10 relative overflow-hidden mb-8">
                <!-- Abstract Background Ornament -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
                <div class="flex flex-col md:flex-row items-center md:items-start gap-8 md:gap-10 relative z-10">
                    <div class="relative">
                        <div
                            class="w-40 h-40 md:w-48 md:h-48 rounded-full border-4 border-surface-container-lowest shadow-xl shadow-primary/10 overflow-hidden">
                            <img class="h-full w-full object-cover"
                                data-alt="close-up of Alex Rivera smiling professionally against a clean studio background with soft natural lighting"
                                src="../<?= $data1['profile_image'] ?>" />
                        </div>
                        <div
                            class="absolute bottom-2 right-2 bg-primary text-white w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center border-4 border-surface-container-low">
                            <span class="material-symbols-outlined text-xs md:text-sm">verified</span>
                        </div>
                    </div>
                    <div class="flex-1 text-center md:text-left mt-4 md:mt-0">
                        <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4">
                            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight text-on-surface">
                                <?= $data1['name'] ?>
                            </h2>
                            <!-- <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full bg-secondary-container text-on-secondary-container text-xs font-bold tracking-widest uppercase self-center md:self-auto">
                                EDITOR-IN-CHIEF
                            </span> -->
                        </div>
                        <div class="space-y-3 mb-8">
                            <div
                                class="flex items-center justify-center md:justify-start gap-2 text-on-surface-variant">
                                <span class="material-symbols-outlined text-lg">mail</span>
                                <span class="text-sm md:text-body-md"><?= $data1['email'] ?></span>
                            </div>
                            <div
                                class="flex items-center justify-center md:justify-start gap-2 text-on-surface-variant">
                                <span class="material-symbols-outlined text-lg">calendar_today</span>
                                <span class="text-sm md:text-body-md">Joined
                                    <?php echo date("M d, Y", strtotime($data1['created_at'])); ?></span>
                            </div>
                            <!-- <div
                                class="flex items-center justify-center md:justify-start gap-2 text-on-surface-variant">
                                <span class="material-symbols-outlined text-lg">location_on</span>
                                <span class="text-sm md:text-body-md">San Francisco, CA</span>
                            </div> -->
                        </div>
                        <!-- Stats Strip - Asymmetrical layout -->
                        <div class="grid grid-cols-3 gap-4 md:gap-8 border-t border-outline-variant/30 pt-8">
                            <div>
                                <p
                                    class="text-[10px] md:text-label-sm uppercase tracking-widest text-on-surface-variant font-bold mb-1">
                                    Published Posts</p>
                                <p class="text-2xl md:text-3xl font-extrabold text-primary">
                                    <?= $row['published_posts'] ?>
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-[10px] md:text-label-sm uppercase tracking-widest text-on-surface-variant font-bold mb-1">
                                    Reach</p>
                                <p class="text-2xl md:text-3xl font-extrabold text-secondary">42.5k</p>
                            </div>
                            <div>
                                <p
                                    class="text-[10px] md:text-label-sm uppercase tracking-widest text-on-surface-variant font-bold mb-1">
                                    Impact</p>
                                <p class="text-2xl md:text-3xl font-extrabold text-tertiary">98%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Section 2: Profile Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Edit Profile CTA -->
                <button
                    class="group flex items-center justify-between p-6 bg-gradient-to-br from-primary to-primary-container text-white rounded-3xl shadow-lg shadow-primary/20 transition-all hover:scale-[1.02] active:scale-95"
                    onclick="editauthor()">
                    <div class="flex items-center gap-4 text-left">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                            <span class="material-symbols-outlined">edit</span>
                        </div>
                        <div>
                            <p class="font-bold text-lg">Edit Profile</p>
                            <p class="text-white/70 text-sm">Update personal information</p>
                        </div>
                    </div>
                    <span
                        class="material-symbols-outlined opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
                </button>
                <!-- Change Password CTA -->
                <button
                    class="group flex items-center justify-between p-6 bg-surface-container text-on-surface rounded-3xl transition-all hover:bg-surface-container-high hover:scale-[1.02] active:scale-95">
                    <div class="flex items-center gap-4 text-left">
                        <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined">lock</span>
                        </div>
                        <div>
                            <p class="font-bold text-lg text-on-surface">Change Password</p>
                            <p class="text-on-surface-variant text-sm">Secure your account</p>
                        </div>
                    </div>
                    <span
                        class="material-symbols-outlined opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward</span>
                </button>
            </div>
            <!-- Bento Section: Account Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
                <div
                    class="md:col-span-2 bg-surface-container-lowest p-8 rounded-3xl shadow-sm border border-outline-variant/10">
                    <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">analytics</span>
                        Content Analytics
                    </h3>
                    <div
                        class="aspect-video w-full bg-surface-container-low rounded-2xl flex items-center justify-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/10 to-transparent"></div>
                        <span class="text-on-surface-variant font-medium relative z-10 text-center px-4">Monthly
                            Performance Visualization</span>
                        <!-- Visual representation of a chart -->
                        <div class="absolute bottom-0 left-0 w-full h-24 flex items-end px-4 gap-2 opacity-30">
                            <div class="flex-1 bg-primary h-1/2 rounded-t-sm"></div>
                            <div class="flex-1 bg-primary h-3/4 rounded-t-sm"></div>
                            <div class="flex-1 bg-primary h-2/3 rounded-t-sm"></div>
                            <div class="flex-1 bg-primary h-full rounded-t-sm"></div>
                            <div class="flex-1 bg-primary h-4/5 rounded-t-sm"></div>
                        </div>
                    </div>
                </div>
                <!-- <div class="bg-surface-container p-8 rounded-3xl space-y-6">
                    <h3 class="text-xl font-bold mb-2">Connected</h3>
                    <div class="flex items-center justify-between p-4 bg-surface-container-lowest rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-[#1877F2] flex items-center justify-center text-white">
                                <span class="material-symbols-outlined text-sm">share</span>
                            </div>
                            <span class="text-sm font-semibold">LinkedIn</span>
                        </div>
                        <span class="text-xs text-secondary font-bold">ACTIVE</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-surface-container-lowest rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-black flex items-center justify-center text-white">
                                <span class="material-symbols-outlined text-sm">terminal</span>
                            </div>
                            <span class="text-sm font-semibold">GitHub</span>
                        </div>
                        <span class="text-xs text-on-surface-variant font-bold">LINK</span>
                    </div>
                    <div class="pt-4">
                        <p class="text-xs text-on-surface-variant font-medium leading-relaxed">
                            Luminous handles your social integrations with high-fidelity encryption.
                        </p>
                    </div>
                </div> -->
            </div>
        </div>
    </main>
    <script>
        function openModal() {
            document.getElementById('edit-profile-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            document.getElementById('edit-profile-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
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
        function handleImagePreview(event) {
            const input = event.target;
            const placeholder = document.getElementById('upload-placeholder');
            const previewContainer = document.getElementById('preview-container');
            const previewImg = document.getElementById('image-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    placeholder.classList.add('hidden');
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
            document.getElementById('image-preview').value = 1;
        }
        function editauthor() {
            let edit_name = document.getElementById("edit_name");
            let id = document.getElementById("id");
            let image_preview = document.getElementById("image-preview");
            let placeholder = document.getElementById('upload-placeholder');
            let previewContainer = document.getElementById('preview-container');
            let image = "<?= $data1['profile_image'] ?>";
            edit_name.value = "<?= $data1['name'] ?>";
            id.value = <?= $data1['id'] ?>;
            image_preview.src = '../' + image;
            document.getElementById('old-image').value = image;
            placeholder.classList.add('hidden');
            previewContainer.classList.remove('hidden');

            openModal();
        }
        function removeImage() {
            const input = document.getElementById('file-upload');
            const placeholder = document.getElementById('upload-placeholder');
            const previewContainer = document.getElementById('preview-container');
            const previewImg = document.getElementById('image-preview');

            input.value = '';
            previewImg.src = '';
            previewContainer.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    </script>
</body>

</html>
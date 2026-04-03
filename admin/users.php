<?php include "../include/session.php";
requireAdmin();
include "../include/db.php";
include "../include/pagination.php";
include "../include/admin_nav_sidebar.php";


// call function
$pagination = paginate('users');
$data24 = $pagination['data'];
$total_pages = $pagination['total_pages'];
$page = $pagination['current_page'];
$limit = $pagination['limit'];
$total_records = $pagination['total_records'];
$offset = $pagination['offset'];
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Users Management - Blog Fusion</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#7C3AED", // Violet-600 (Primary from request)
                        "primary-hover": "#6D28D9",
                        "background-light": "#f8f7ff",
                        "background-dark": "#0f172a",
                        "indigo-accent": "#4F46E5",
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"]
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
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?=slidebar('users');?>
        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Navbar -->
            <?=ad_navbar();?>
            <div class="flex-1 overflow-y-auto p-8">
                <!-- Page Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-black tracking-tight">Users</h2>
                        <p class="text-slate-500">Manage platform contributors and account access levels.</p>
                    </div>
                    <button
                        class="bg-primary hover:bg-primary-hover text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-primary/20 transition-all">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span>Add User</span>
                    </button>
                </div>
                <!-- Filters -->
                <div
                    class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm mb-8 flex flex-wrap gap-4 items-center">
                    <div class="flex-1 min-w-[300px] relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">filter_list</span>
                        <input
                            class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-xl py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary"
                            placeholder="Filter users by name, email or role..." type="text" />
                    </div>
                    <div class="flex gap-2">
                        <button
                            class="flex items-center gap-2 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                            <span>Date Joined</span>
                        </button>
                        <button
                            class="flex items-center gap-2 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            <span>Export</span>
                        </button>
                    </div>
                </div>
                <!-- Table Container -->

                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-8">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="text-slate-500 text-xs uppercase tracking-wider bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                                    <th class="px-6 py-4 font-semibold">User Name</th>
                                    <th class="px-6 py-4 font-semibold">Email Address</th>
                                    <th class="px-6 py-4 font-semibold">Role</th>
                                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                                <?php foreach ($data24 as $key => $row) { ?>

                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">

                                        <!-- USER NAME + IMAGE -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">

                                                <div class="size-10 rounded-full bg-primary/10 bg-cover bg-center border border-primary/20"
                                                    style="background-image: url('../<?php echo $row['profile_image']; ?>')">
                                                </div>

                                                <div>
                                                    <p class="font-bold text-sm"><?php echo $row['name']; ?></p>
                                                    <p class="text-xs text-slate-500">
                                                        Joined <?php echo date("M d, Y", strtotime($row['created_at'])); ?>
                                                    </p>
                                                </div>

                                            </div>
                                        </td>

                                        <!-- EMAIL -->
                                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                            <?php echo $row['email']; ?>
                                        </td>

                                        <!-- ROLE -->
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full 
                                                <?php
                                                if ($row['role'] == "admin")
                                                    echo "bg-purple-100 text-purple-700";
                                                elseif ($row['role'] == "author")
                                                    echo "bg-blue-100 text-blue-700";
                                                else
                                                    echo "bg-slate-100 text-slate-600";
                                                ?>">
                                                <?php echo ucfirst($row['role']); ?>
                                            </span>
                                        </td>

                                        <!-- ACTIONS -->
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button class="p-2 hover:text-primary">
                                                    <span class="material-symbols-outlined"
                                                        onclick='loopdata(<?php echo json_encode($row); ?>);'>edit_note</span>
                                                </button>
                                                <button class="p-2 hover:text-red-500">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </div>
                                        </td>

                                    </tr>

                                <?php } ?>

                            </tbody>
                        </table>
                    </div>
                    <?php
                    $page=pagination_links(
                        $total_pages,
                        $limit,
                        $total_records,
                        $offset,
                        count($data24)
                    );
                    ?>
                </div>

            </div>
        </main>
    </div>
    <!-- Edit User Modal -->
    <div aria-labelledby="modal-title" aria-modal="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden" id="edit-user-modal" role="dialog">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-all"
            onclick="toggleModal('edit-user-modal', false)">
        </div>
        <!-- Modal Content -->
        <div
            class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all border border-slate-200 dark:border-slate-800">
            <!-- Modal Header -->
            <div
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="modal-title">Edit User Profile</h3>
                <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                    onclick="toggleModal('edit-user-modal', false)">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <!-- Modal Body -->
            <form class="p-6 space-y-5" enctype="multipart/form-data" action="../actions/admin.php?page=<?php echo $_GET['page']??$page ;?>" method="POST">
                <input type="hidden" id="user-id" name="user_id" value="">
                <!-- User Name -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
                        for="edit-name">User Name</label>
                    <input
                        class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                        id="edit-name" type="text" value="User Name" name="name" />
                </div>
                <!-- Email Address -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
                        for="edit-email">Email Address</label>
                    <input
                        class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                        id="edit-email" type="email" value="default@gmail.com" name="email"/>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Role Dropdown -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5"
                            for="edit-role">Role</label>
                        <select
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none appearance-none"
                            id="edit-role" name="role">
                            <option selected="" value="admin">Admin</option>
                            <option value="author">Author</option>
                            <option value="user">User</option>
                        </select>
                    </div>

                </div>
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

                <!-- Modal Footer -->
                <div
                    class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <button
                        class="px-5 py-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors"
                        onclick="toggleModal('edit-user-modal', false)">
                        Cancel
                    </button>
                    <button
                        class="px-5 py-2 text-sm font-bold bg-primary hover:bg-primary-hover text-white rounded-xl shadow-lg shadow-primary/20 transition-all"
                        onclick="toggleModal('edit-user-modal', false)" type="submit" name="user-update">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="../assets/js/admin.js"></script>
    <script>
        function toggleModal(modalId, show) {
            const modal = document.getElementById(modalId);
            if (show) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }
        function loopdata(row) {
            let edit_name = document.getElementById("edit-name");
            let edit_email = document.getElementById("edit-email");
            let edit_role = document.getElementById("edit-role");
            let image_preview = document.getElementById("image-preview");
            let placeholder = document.getElementById('upload-placeholder');
            let previewContainer = document.getElementById('preview-container');
            let id=document.getElementById('user-id');
            let image = row.profile_image
            edit_name.value = row.name;
            edit_email.value = row.email;
            edit_role.value = row.role;
            id.value=row.id;
            image_preview.src = '../' + image;
            document.getElementById('old-image').value=image;
            placeholder.classList.add('hidden');
            previewContainer.classList.remove('hidden');
            toggleModal('edit-user-modal', true);
        }
        // Handle file upload preview or name display
        const fileInput = document.getElementById('file-upload');
        if (fileInput) {
            fileInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    console.log('File selected:', this.files[0].name);
                }
            });
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
            document.getElementById('image-preview').value=1;
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
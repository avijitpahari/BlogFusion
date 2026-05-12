<?php include "../include/session.php";
requireAuthor();
include "../include/db.php";
include "../include/author_nav_sidebar.php";
include "../include/pagination.php";


// logic data
$table = 'posts';
$limit = 4;
// current page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;

$offset = ($page - 1) * $limit;

// fetch data
$query = "SELECT * FROM $table WHERE author_id={$_SESSION['user_id']} LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
$data = mysqli_num_rows($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

// total count
$total_query = "SELECT COUNT(*) as total FROM $table WHERE author_id={$_SESSION['user_id']}";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];


// call function
$pagination = paginate($data, $total_records, $page, $offset);
$data24 = $pagination['data'];
$total_pages = $pagination['total_pages'];
$page = $pagination['current_page'];
$limit = $pagination['limit'];
$total_records = $pagination['total_records'];
$offset = $pagination['offset'];

function shortText($text, $limit = 40)
{
    if (strlen($text) <= $limit) {
        return $text;
    }

    $text = substr($text, 0, $limit);
    $text = substr($text, 0, strrpos($text, ' '));

    return $text . "...";
}
$sql258="SELECT COUNT(*) as total_posts,SUM(status = 'published') as total_published,SUM(status = 'draft') as total_draft FROM posts WHERE author_id={$_SESSION['user_id']};";
$deta=mysqli_fetch_assoc(mysqli_query($conn,$sql258));
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>My Posts - Luminous Editor</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-tint": "#732ee4",
                        "secondary-fixed": "#e2dfff",
                        "on-primary-fixed": "#25005a",
                        "on-background": "#1d1a24",
                        "surface": "#fef7ff",
                        "on-primary": "#ffffff",
                        "tertiary-fixed": "#ffd9e4",
                        "tertiary-container": "#bf2076",
                        "on-tertiary-fixed-variant": "#8c0053",
                        "on-tertiary-fixed": "#3e0022",
                        "outline-variant": "#ccc3d8",
                        "surface-variant": "#e8dfee",
                        "surface-container-highest": "#e8dfee",
                        "on-error": "#ffffff",
                        "on-surface": "#1d1a24",
                        "error-container": "#ffdad6",
                        "primary-fixed": "#eaddff",
                        "tertiary-fixed-dim": "#ffb0cd",
                        "secondary": "#4b41e1",
                        "primary-fixed-dim": "#d2bbff",
                        "inverse-on-surface": "#f6eefc",
                        "surface-container": "#f3ebfa",
                        "primary": "#630ed4",
                        "on-secondary": "#ffffff",
                        "on-error-container": "#93000a",
                        "on-tertiary": "#ffffff",
                        "secondary-container": "#645efb",
                        "on-secondary-fixed-variant": "#3323cc",
                        "primary-container": "#7c3aed",
                        "on-secondary-fixed": "#0f0069",
                        "on-tertiary-container": "#ffdde7",
                        "on-secondary-container": "#fffbff",
                        "outline": "#7b7487",
                        "surface-dim": "#dfd7e6",
                        "surface-container-high": "#ede5f4",
                        "surface-container-lowest": "#ffffff",
                        "surface-bright": "#fef7ff",
                        "background": "#fef7ff",
                        "error": "#ba1a1a",
                        "on-primary-fixed-variant": "#5a00c6",
                        "inverse-surface": "#332f39",
                        "tertiary": "#9b005c",
                        "on-primary-container": "#ede0ff",
                        "inverse-primary": "#d2bbff",
                        "secondary-fixed-dim": "#c3c0ff",
                        "surface-container-low": "#f9f1ff",
                        "on-surface-variant": "#4a4455"
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

        /* Custom scrollbar for an editorial feel */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #e8dfee;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-surface text-on-surface min-h-screen">
    <!-- Sidebar Navigation -->
    <?= author_slidebar('my-post') ?>
    <!-- Main Content Wrapper -->
    <div class="ml-72 min-h-screen flex flex-col">
        <!-- Top Navigation Bar -->
        <?= author_navbar(); ?>

        <!-- Page Content -->
        <main class="p-8 flex-1 mt-10">
            <!-- Header Section -->
            <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h2 class="text-4xl font-bold tracking-tight text-on-surface mb-2">My Posts</h2>
                    <p class="text-on-surface-variant max-w-md">Manage your creative editorial portfolio. Edit,
                        organize, and monitor the performance of your published content.</p>
                </div>
                <button
                    class="flex items-center gap-2 bg-gradient-to-br from-primary to-primary-container text-on-primary px-6 py-3 rounded-xl font-bold shadow-lg shadow-primary/20 transition-transform active:scale-95">
                    <span class="material-symbols-outlined text-lg" data-icon="add">add</span>
                    Create New Post
                </button>
            </div>
            <!-- Dashboard Filtering & Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="md:col-span-2 relative">
                    <span
                        class="material-symbols-outlined absolute left-4 top-7 -translate-y-1/2 text-on-surface-variant"
                        data-icon="filter_list">filter_list</span>
                    <input
                        class="w-full bg-surface-container-low border-none rounded-xl py-4 pl-12 text-sm focus:ring-2 focus:ring-primary-container/20 transition-all"
                        placeholder="Filter posts by title, tag, or keyword..." type="text" />
                </div>
                <div
                    class="bg-surface-container-low rounded-xl p-4 flex items-center justify-between border border-outline-variant/10">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant opacity-60">
                            Published</p>
                        <p class="text-2xl font-bold text-on-surface"><?=$deta['total_published']?></p>
                    </div>
                    <div class="p-2 bg-secondary-container/10 rounded-lg text-secondary">
                        <span class="material-symbols-outlined" data-icon="check_circle">check_circle</span>
                    </div>
                </div>
                <div
                    class="bg-surface-container-low rounded-xl p-4 flex items-center justify-between border border-outline-variant/10">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant opacity-60">Drafts
                        </p>
                        <p class="text-2xl font-bold text-on-surface"><?=$deta['total_draft']?></p>
                    </div>
                    <div class="p-2 bg-tertiary-container/10 rounded-lg text-tertiary">
                        <span class="material-symbols-outlined" data-icon="edit_note">edit_note</span>
                    </div>
                </div>
            </div>
            <!-- Content Canvas (Bento-style List) -->
            <div
                class="bg-surface-container-lowest rounded-3xl shadow-sm overflow-hidden border border-outline-variant/5">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/50">
                            <th
                                class="px-6 py-5 text-xs font-bold uppercase tracking-widest text-on-surface-variant opacity-70">
                                Post Overview</th>
                            <th
                                class="px-6 py-5 text-xs font-bold uppercase tracking-widest text-on-surface-variant opacity-70">
                                Category</th>
                            <th
                                class="px-6 py-5 text-xs font-bold uppercase tracking-widest text-on-surface-variant opacity-70">
                                Published Date</th>
                            <th
                                class="px-6 py-5 text-xs font-bold uppercase tracking-widest text-on-surface-variant opacity-70">
                                Status</th>
                            <th
                                class="px-6 py-5 text-xs font-bold uppercase tracking-widest text-on-surface-variant opacity-70 text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container-low">
                        <!-- Row 1 -->
                        <?php foreach ($data24 as $key => $row) {
                            $data_fetch1 = data($conn, 'categories', $row['category_id']);
                            $data01 = $data_fetch1;
                            


                            ?>
                            <tr class="hover:bg-surface-container-low transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <img class="w-14 h-14 rounded-lg object-cover shadow-sm"
                                            data-alt="minimal workspace with high-end laptop and coding environment in soft morning light"
                                            src="../<?= $row['image'] ?>" />
                                        <div>
                                            <p class="font-bold text-[18px] text-on-surface group-hover:text-primary transition-colors">
                                                <?= $row['title'] ?>
                                            </p>

                                            <p class="text-[10px] text-on-surface-variant font-semibold"><?= shortText($row['content']) ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span
                                        class="bg-secondary-fixed text-on-secondary-fixed-variant px-3 py-1 rounded-full text-xs font-semibold">
                                        <?= $data01['name'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-sm text-on-surface font-medium">
                                        <?php echo date("M d, Y", strtotime($row['created_at'])); ?>
                                    </p>
                                    <!-- <p class="text-[10px] text-on-surface-variant">Last edited 2h ago</p> -->
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-1.5  
                                        <?php
                                        if ($row['status'] == 'published') {
                                            echo 'text-green-500';
                                        } else {
                                            echo 'text-tertiary
                                            ';
                                        }
                                        ?>
                                    ">
                                        <span class="w-2 h-2 rounded-full  
                                            <?php
                                            if ($row['status'] == 'published') {
                                                echo 'bg-green-500';
                                            } else {
                                                echo 'bg-tertiary';
                                            }
                                            ?>
                                        "></span>
                                        <span class="text-xs font-bold"><?= ucfirst($row['status']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            class="p-2 rounded-lg text-on-surface-variant hover:bg-primary-fixed hover:text-primary transition-all">
                                            <span class="material-symbols-outlined text-xl" data-icon="edit">edit</span>
                                        </button>
                                        <button
                                            class="p-2 rounded-lg text-on-surface-variant hover:bg-error-container hover:text-error transition-all">
                                            <span class="material-symbols-outlined text-xl" data-icon="delete">delete</span>
                                        </button>
                                        <button
                                            class="p-2 rounded-lg text-on-surface-variant hover:bg-surface-container-highest transition-all">
                                            <span class="material-symbols-outlined text-xl"
                                                data-icon="visibility">visibility</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <!-- Pagination/Footer of Table -->
                <?php
                $page = pagination_links(
                    $total_pages,
                    $limit,
                    $total_records,
                    $offset
                );
                ?>
            </div>
        </main>
        <!-- Footer -->
        <footer class="p-8 text-center">
            <p class="text-xs text-on-surface-variant opacity-40 uppercase tracking-[0.2em]">Luminous Editor © 2024 •
                Powered by fusion chroma design system</p>
        </footer>
    </div>
</body>

</html>
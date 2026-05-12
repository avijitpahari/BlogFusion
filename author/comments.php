<?php include "../include/session.php";
requireAuthor();
include "../include/db.php";
include "../include/author_nav_sidebar.php";

// logic data
$table = 'comments';
$limit = 4;
// current page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;

$offset = ($page - 1) * $limit;
$id = $_SESSION['user_id'];

// fetch data
$query = "SELECT c.*,p.title FROM $table c INNER JOIN posts p ON c.post_id = p.id WHERE p.author_id = $id ORDER BY c.created_at DESC LIMIT $limit OFFSET $offset;";
$result = mysqli_query($conn, $query);
$data = mysqli_num_rows($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

// total count
$total_query = "SELECT COUNT(*) as total FROM $table c INNER JOIN posts p ON c.post_id = p.id WHERE p.author_id = $id";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];

// total post
$total_post = "SELECT COUNT(*) as total_posts FROM posts WHERE author_id = $id";
$total_result1 = mysqli_query($conn, $total_post);
$total_posts = mysqli_fetch_assoc($total_result1);
$total_post1 = $total_posts['total_posts'];

// call function
include "../include/pagination.php";
$pagination = paginate($data, $total_records, $page, $offset);
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
    <title>Comments Management - Fusion Chroma</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&amp;display=swap"
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
                        "tertiary": "#9b005c",
                        "surface-container": "#f3ebfa",
                        "on-tertiary-fixed-variant": "#8c0053",
                        "inverse-primary": "#d2bbff",
                        "on-surface-variant": "#4a4455",
                        "inverse-on-surface": "#f6eefc",
                        "tertiary-fixed-dim": "#ffb0cd",
                        "primary-container": "#7c3aed",
                        "background": "#fef7ff",
                        "on-tertiary-container": "#ffdde7",
                        "on-primary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "secondary-fixed-dim": "#c3c0ff",
                        "tertiary-fixed": "#ffd9e4",
                        "on-error": "#ffffff",
                        "surface-container-high": "#ede5f4",
                        "on-tertiary": "#ffffff",
                        "primary": "#630ed4",
                        "on-tertiary-fixed": "#3e0022",
                        "surface-container-low": "#f9f1ff",
                        "surface-variant": "#e8dfee",
                        "primary-fixed": "#eaddff",
                        "on-primary-fixed-variant": "#5a00c6",
                        "surface-dim": "#dfd7e6",
                        "on-error-container": "#93000a",
                        "on-secondary": "#ffffff",
                        "tertiary-container": "#bf2076",
                        "surface-bright": "#fef7ff",
                        "surface-tint": "#732ee4",
                        "on-secondary-fixed-variant": "#3323cc",
                        "secondary": "#4b41e1",
                        "error-container": "#ffdad6",
                        "on-primary-container": "#ede0ff",
                        "primary-fixed-dim": "#d2bbff",
                        "on-surface": "#1d1a24",
                        "inverse-surface": "#332f39",
                        "on-secondary-container": "#fffbff",
                        "on-secondary-fixed": "#0f0069",
                        "secondary-fixed": "#e2dfff",
                        "secondary-container": "#645efb",
                        "on-background": "#1d1a24",
                        "error": "#ba1a1a",
                        "outline-variant": "#ccc3d8",
                        "surface-container-highest": "#e8dfee",
                        "on-primary-fixed": "#25005a",
                        "surface": "#fef7ff",
                        "outline": "#7b7487"
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
    </style>
</head>

<body class="bg-background text-on-background min-h-screen">
    <!-- SideNavBar Shell -->
    <?= author_slidebar('comments') ?>
    <!-- TopNavBar Shell -->
    <?= author_navbar(); ?>
    <!-- Main Content Canvas -->
    <main class="pl-64 pt-16 min-h-screen">
        <div class="p-10 max-w-7xl mx-auto">
            <!-- Page Header -->
            <div class="mb-10 flex justify-between items-end">
                <div>
                    <h2 class="text-4xl font-black text-on-surface tracking-tighter -mb-1">Comments</h2>
                    <p class="text-zinc-500 font-medium mt-2">Manage and respond to your audience's engagement.</p>
                </div>
                <div class="flex gap-3">
                    <button
                        class="px-5 py-2.5 rounded-xl bg-surface-container-high text-on-surface-variant text-sm font-semibold hover:bg-surface-variant transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg" data-icon="filter_list">filter_list</span>
                        Filter
                    </button>
                    <!-- <button
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-br from-primary to-primary-container text-white text-sm font-semibold shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg" data-icon="check_circle">check_circle</span>
                        Approve All
                    </button> -->
                </div>
            </div>
            <!-- Dashboard Stats Summary (Bento Style) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-surface-container-low p-6 rounded-3xl md:col-span-2 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-primary"
                            data-icon="chat_bubble">chat_bubble</span>
                    </div>
                    <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Total Comments</p>
                    <p class="text-4xl font-black text-on-surface"><?= $total_records ?></p>
                    <p class="text-xs text-zinc-500 mt-2 flex items-center gap-1">
                        <span class="text-emerald-600 font-bold">+12%</span> from last week
                    </p>
                </div>
                <!-- <div class="bg-surface-container p-6 rounded-3xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-6xl text-tertiary"
                            data-icon="pending">pending</span>
                    </div>
                    <p class="text-xs font-bold text-tertiary uppercase tracking-widest mb-1">Pending</p>
                    <p class="text-4xl font-black text-on-surface">42</p>
                    <p class="text-xs text-zinc-500 mt-2">Needs manual review</p>
                </div> -->
                <div
                    class="bg-surface-container-highest p-6 rounded-3xl md:col-span-2 flex items-center justify-between border border-primary/5">
                    <div>
                        <p class="text-xs font-bold text-secondary uppercase tracking-widest mb-1">Response Rate</p>
                        <p class="text-4xl font-black text-on-surface">
                            <?= $total_post1 > 0 ? round($total_records / $total_post1, 2) : 0 ?>%</p>
                        <p class="text-xs text-zinc-500 mt-2">Excellent engagement score</p>
                    </div>
                    <div class="h-16 w-32 flex items-end gap-1">
                        <div class="w-2 bg-primary/20 rounded-full h-1/2"></div>
                        <div class="w-2 bg-primary/20 rounded-full h-3/4"></div>
                        <div class="w-2 bg-primary/40 rounded-full h-2/3"></div>
                        <div class="w-2 bg-primary/60 rounded-full h-full"></div>
                        <div class="w-2 bg-primary rounded-full h-4/5"></div>
                    </div>
                </div>
            </div>
            <!-- Comments List Container -->
            <div class="bg-surface-container-lowest rounded-[2.5rem] p-4 shadow-sm">
                <div class="overflow-hidden">
                    <table class="w-full text-left border-separate border-spacing-y-4">
                        <thead class="text-[10px] font-black uppercase tracking-[0.15em] text-zinc-400">
                            <tr>
                                <th class="px-8 pb-2">User</th>
                                <th class="px-4 pb-2">Comment</th>
                                <th class="px-4 pb-2">Post context</th>
                                <th class="px-4 pb-2">Date</th>
                                <th class="px-8 pb-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php foreach ($data24 as $key => $row) {
                                $data_fetch = data_featch($conn, $row['user_id']);
                                $data01 = $data_fetch['data'];
                                $data_fetch = data($conn, 'posts', $row['post_id']);
                                $posts = $data_fetch;
                                ?>

                                <tr class="group hover:bg-surface-container-low transition-colors rounded-3xl">
                                    <td class="px-8 py-5 rounded-l-[2rem]">
                                        <div class="flex items-center gap-3">
                                            <!-- <div
                                                class="w-10 h-10 rounded-2xl bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed font-bold">
                                                JS
                                            </div> -->
                                            <div class="flex items-center gap-3">
                                                <img class="w-10 h-10 rounded-2xl object-cover"
                                                    src="../<?= $data01['profile_image'] ?>">
                                            </div>
                                            <div>
                                                <p class="font-bold text-on-surface"><?= $data01['name'] ?></p>
                                                <p class="text-xs text-zinc-500"><?= $data01['email'] ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 max-w-xs">
                                        <p class="text-on-surface-variant line-clamp-2 leading-relaxed">
                                            <?= $row['comment'] ?>
                                        </p>
                                    </td>
                                    <td class="px-4 py-5">
                                        <div
                                            class="bg-surface-container px-3 py-1.5 rounded-xl inline-flex items-center gap-2 group-hover:bg-white transition-colors">
                                            <span class="material-symbols-outlined text-base text-primary"
                                                data-icon="article">article</span>
                                            <span class="text-xs font-semibold text-zinc-600"><?= $posts['title'] ?></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5">
                                        <p class="text-xs font-medium text-zinc-500">
                                            <?php echo date("M d, Y", strtotime($row['created_at'])); ?>
                                        </p>
                                        <!-- <p class="text-[10px] text-zinc-400">14:20 PM</p> -->
                                    </td>
                                    <td class="px-8 py-5 text-right rounded-r-[2rem]">
                                        <div class="flex items-center justify-end gap-2  transition-opacity">
                                            <button
                                                class="w-9 h-9 rounded-xl bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all flex items-center justify-center">
                                                <span class="material-symbols-outlined text-lg"
                                                    data-icon="reply">reply</span>

                                            </button>
                                            <button onclick="window.location.href='../actions/author.php?id=<?=$id?>&btn=comment'"
                                                class="w-9 h-9 rounded-xl bg-tertiary/10 text-tertiary hover:bg-tertiary hover:text-white transition-all flex items-center justify-center">
                                                <span class="material-symbols-outlined text-lg"
                                                    data-icon="delete">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Shell -->
                <?php
                $page = pagination_links(
                    $total_pages,
                    $limit,
                    $total_records,
                    $offset
                );
                ?>
            </div>

        </div>
    </main>
</body>

</html>
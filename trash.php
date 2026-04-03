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
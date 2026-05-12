<?php
// ==== Use for Pagination data ======



function paginate($data,$total_records,$page,$offset, $limit = 4){

    // current page
    // $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    // if ($page < 1)
    //     $page = 1;

    // $offset = ($page - 1) * $limit;
    $total_pages = ceil($total_records / $limit);

    return [
        'data' => $data,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'limit' => $limit,
        'total_records' => $total_records,
        'offset' => $offset
    ];
}



// ==== Use For Pagination Links ======


function pagination_links($totalPages, $limit, $total_records, $offset)
{

    $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;


    //  start & end calculate
    $start = $currentPage - 1;
    $end = $currentPage + 1;

    //  fix start
    if ($start < 1) {
        $start = 1;
        $end = min(3, $totalPages);
    }

    //  fix end
    if ($end > $totalPages) {
        $end = $totalPages;
        $start = max(1, $totalPages - 2);
    }
    echo '<div class="bg-slate-50/50 dark:bg-slate-800/50 px-6 py-4 flex items-center justify-between border-t border-slate-100 dark:border-slate-800">';
    echo '<p class="text-sm text-slate-500">Showing <span
                                class="font-bold text-slate-900 dark:text-slate-100">';
    echo ($offset + 1);
    echo '</span>
                            to <span
                                class="font-bold text-slate-900 dark:text-slate-100">';
    echo min($offset + $limit, $total_records), '</span>
                            of <span
                                class="font-bold text-slate-900 dark:text-slate-100">';
    echo $total_records, '</span>
                            users</p>';
    echo '<div class="px-6 py-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/30 flex items-center justify-between">';
    echo '<div class="flex items-center gap-2">';

    //  PREVIOUS BUTTON
    if ($currentPage == 1) {
        echo '<button class="p-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 rounded-lg opacity-50" disabled>
            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
          </button>';
    } else {
        echo '<a href="?page=' . ($currentPage - 1) . '">
            <button class="p-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            </button>
          </a>';
    }

    //  PAGE NUMBERS
    for ($i = $start; $i <= $end; $i++) {

        //  Active Page
        if ($i == $currentPage) {
            echo '<button class="h-9 w-9 flex items-center justify-center bg-primary text-white rounded-lg font-bold text-sm shadow-sm">'
                . $i .
                '</button>';
        } else {
            echo '<a href="?page=' . $i . '">
                <button class="h-9 w-9 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg font-medium text-sm transition-colors">'
                . $i .
                '</button>
              </a>';
        }
    }

    //  NEXT BUTTON
    if ($currentPage == $totalPages) {
        echo '<button class="p-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 rounded-lg opacity-50" disabled>
            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
          </button>';
    } else {
        echo '<a href="?page=' . ($currentPage + 1) . '">
            <button class="p-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </button>
          </a>';
    }

    echo '</div>';
    echo '</div>';
    return $currentPage;
}
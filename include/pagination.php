<?php
// ==== Use for Pagination data ======
function paginate($data, $total_records, $page, $offset, $limit = 6) {
    $total_pages = ceil($total_records / $limit);
    if ($total_pages < 1) $total_pages = 1;
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
function pagination_links($totalPages, $limit, $total_records, $offset, $label = 'entries', $pageParam = 'page')
{
    $currentPage = isset($_GET[$pageParam]) ? (int) $_GET[$pageParam] : 1;
    if ($currentPage < 1) $currentPage = 1;
    if ($currentPage > $totalPages) $currentPage = $totalPages;

    // Get other query parameters to preserve them
    $queryParams = $_GET;
    unset($queryParams[$pageParam]);
    $queryString = http_build_query($queryParams);
    $extra = $queryString ? '&' . $queryString : '';
    $extra = htmlspecialchars($extra, ENT_QUOTES, 'UTF-8');

    // Elegant and adaptive Tailwind design for both admin and author templates
    echo '<div class="px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-900/40 rounded-b-2xl transition-all duration-300">';
    echo '<p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Showing <span class="font-bold text-slate-900 dark:text-slate-100">';
    echo ($total_records > 0 ? $offset + 1 : 0);
    echo '</span> to <span class="font-bold text-slate-900 dark:text-slate-100">';
    echo min($offset + $limit, $total_records);
    echo '</span> of <span class="font-bold text-slate-900 dark:text-slate-100">';
    echo $total_records;
    echo '</span> ' . htmlspecialchars($label) . '</p>';
    
    echo '<div class="flex items-center gap-1.5" role="navigation" aria-label="Pagination Navigation">';

    // PREVIOUS BUTTON
    if ($currentPage <= 1) {
        echo '<button class="p-2 border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800 text-slate-300 dark:text-slate-600 rounded-xl cursor-not-allowed opacity-50 flex items-center justify-center h-9 w-9" disabled aria-label="Previous page">
            <span class="material-symbols-outlined text-lg leading-none">chevron_left</span>
          </button>';
    } else {
        echo '<a href="?' . htmlspecialchars($pageParam) . '=' . ($currentPage - 1) . $extra . '" class="group" aria-label="Previous page">';
        echo '<button class="p-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-750 hover:border-primary dark:hover:border-primary hover:text-primary dark:hover:text-primary rounded-xl transition-all duration-200 flex items-center justify-center active:scale-95 group-hover:shadow-sm h-9 w-9">';
        echo '<span class="material-symbols-outlined text-lg leading-none transition-transform group-hover:-translate-x-0.5">chevron_left</span>';
        echo '</button>';
        echo '</a>';
    }

    // Dynamic pagination range calculation with ellipsis
    $range = 1; // number of pages to show on either side of current page
    
    // Page 1 is always shown
    if ($currentPage == 1) {
        echo '<button class="h-9 w-9 flex items-center justify-center bg-primary text-white rounded-xl font-bold text-xs shadow-md shadow-primary/20 hover:bg-primary-hover active:scale-95 transition-all duration-200">1</button>';
    } else {
        echo '<a href="?' . htmlspecialchars($pageParam) . '=1' . $extra . '">';
        echo '<button class="h-9 w-9 flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:border-primary dark:hover:border-primary hover:text-primary dark:hover:text-primary rounded-xl font-bold text-xs active:scale-95 transition-all duration-200">1</button>';
        echo '</a>';
    }

    // Show ellipsis if there is a gap between 1 and start of current page range
    $startPage = max(2, $currentPage - $range);
    if ($startPage > 2) {
        echo '<span class="h-9 w-9 flex items-center justify-center text-slate-400 font-bold text-xs select-none">...</span>';
    }

    // Show middle pages
    $endPage = min($totalPages - 1, $currentPage + $range);
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            echo '<button class="h-9 w-9 flex items-center justify-center bg-primary text-white rounded-xl font-bold text-xs shadow-md shadow-primary/20 hover:bg-primary-hover active:scale-95 transition-all duration-200">' . $i . '</button>';
        } else {
            echo '<a href="?' . htmlspecialchars($pageParam) . '=' . $i . $extra . '">';
            echo '<button class="h-9 w-9 flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:border-primary dark:hover:border-primary hover:text-primary dark:hover:text-primary rounded-xl font-bold text-xs active:scale-95 transition-all duration-200">' . $i . '</button>';
            echo '</a>';
        }
    }

    // Show ellipsis if there is a gap between end of current page range and totalPages
    if ($endPage < $totalPages - 1) {
        echo '<span class="h-9 w-9 flex items-center justify-center text-slate-400 font-bold text-xs select-none">...</span>';
    }

    // Show last page if totalPages > 1
    if ($totalPages > 1) {
        if ($currentPage == $totalPages) {
            echo '<button class="h-9 w-9 flex items-center justify-center bg-primary text-white rounded-xl font-bold text-xs shadow-md shadow-primary/20 hover:bg-primary-hover active:scale-95 transition-all duration-200">' . $totalPages . '</button>';
        } else {
            echo '<a href="?' . htmlspecialchars($pageParam) . '=' . $totalPages . $extra . '">';
            echo '<button class="h-9 w-9 flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:border-primary dark:hover:border-primary hover:text-primary dark:hover:text-primary rounded-xl font-bold text-xs active:scale-95 transition-all duration-200">' . $totalPages . '</button>';
            echo '</a>';
        }
    }

    // NEXT BUTTON
    if ($currentPage >= $totalPages) {
        echo '<button class="p-2 border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800 text-slate-300 dark:text-slate-600 rounded-xl cursor-not-allowed opacity-50 flex items-center justify-center h-9 w-9" disabled aria-label="Next page">
            <span class="material-symbols-outlined text-lg leading-none">chevron_right</span>
          </button>';
    } else {
        echo '<a href="?' . htmlspecialchars($pageParam) . '=' . ($currentPage + 1) . $extra . '" class="group" aria-label="Next page">';
        echo '<button class="p-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-750 hover:border-primary dark:hover:border-primary rounded-xl transition-all duration-200 flex items-center justify-center active:scale-95 group-hover:shadow-sm h-9 w-9">';
        echo '<span class="material-symbols-outlined text-lg leading-none transition-transform group-hover:translate-x-0.5">chevron_right</span>';
        echo '</button>';
        echo '</a>';
    }

    echo '</div>';
    echo '</div>';
}
?>
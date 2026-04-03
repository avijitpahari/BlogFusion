<?php
include 'include/db.php';
include 'test2.php';

// call function
$pagination = paginate('users');

$data = $pagination['data'];
$total_pages = $pagination['total_pages'];
$page = $pagination['current_page'];
$limit = $pagination['limit'];
$total_records = $pagination['total_records'];
$offset = $pagination['offset'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Job List</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
</head>

<body>

    <div class="container mt-5">
        <form method="GET" class="mb-3">

            <!-- <select name="limit" onchange="this.form.submit()" class="form-select w-auto">

                <option value="5" <?php //if ($limit == 5)
                    //echo 'selected'; ?>>5</option>
                <option value="10" <?php //if ($limit == 10)
                    //echo 'selected'; ?>>10</option>
                <option value="20" <?php //if ($limit == 20)
                    //echo 'selected'; ?>>20</option>
                <option value="50" <?php //if ($limit == 50)
                    //echo 'selected'; ?>>50</option>

            </select> -->

        </form>
        <h2 class="mb-4">Job List</h2>

        <table class="table table-bordered table-striped">

            <thead class="table-dark">
                <tr>
                    <th>SL</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($data)) { ?>

                    <?php foreach ($data as $key => $row) { ?>
                        <tr>
                            <td><?php echo ($offset + $key + 1); ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                        </tr>
                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="3" class="text-center">No Data Found</td>
                    </tr>

                <?php } ?>

            </tbody>

        </table>

        <!-- Pagination -->
        <!-- <div class="d-flex justify-content-center"> -->
        <?php
        pagination_links(
            $total_pages,
            $page,
            $limit,
            $total_records,
            $offset,
            count($data)
        );
        ?>
        <!-- </div> -->

    </div>
    
</body>

</html>
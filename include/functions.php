<?php 


function escape_html($text){
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

function site_url($path = ''){
    if (defined('BASE_URL')) {
        $base = BASE_URL;
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $script_name = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = 'BlogFusion';
        if (preg_match('/^\/([^\/]+)\//', $script_name, $matches)) {
            $dir = $matches[1];
        }
        $base = $protocol . $host . '/' . $dir . '/';
    }
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function normalize_image($image){
    if(empty($image)){
        return site_url('assets/images/default.jpg');
    }

    return site_url($image);
}

function excerpt_text($text, $limit = 120){

    $text = strip_tags($text);

    if(strlen($text) > $limit){
        return substr($text,0,$limit).'...';
    }

    return $text;
}

function reading_time($content){

    $words = str_word_count(strip_tags($content));

    $minutes = ceil($words / 200);

    return max(1,$minutes);
}

function fetch_latest_posts(
    $conn,
    $limit = 6,
    $search = null,
    $category = null
){
    $limit = (int)$limit;
    $sql = "SELECT posts.*, categories.name as category_name, users.name as author_name 
            FROM posts 
            LEFT JOIN categories ON posts.category_id = categories.id
            LEFT JOIN users ON posts.author_id = users.id
            WHERE posts.status='published'";
    
    if (!empty($search)) {
        $search = mysqli_real_escape_string($conn, $search);
        $sql .= " AND (posts.title LIKE '%$search%' OR posts.content LIKE '%$search%')";
    }
    
    if (!empty($category)) {
        $category = mysqli_real_escape_string($conn, $category);
        $sql .= " AND categories.slug = '$category'";
    }
    
    $sql .= " ORDER BY posts.created_at DESC LIMIT $limit";
    
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function fetch_top_posts($conn,$limit=5){
    $limit = (int)$limit;

    $sql = "SELECT *
            FROM posts
            WHERE status='published'
            ORDER BY views DESC
            LIMIT $limit";

    $result = mysqli_query($conn,$sql);
    if (!$result) {
        return [];
    }

    return mysqli_fetch_all($result,MYSQLI_ASSOC);
}

function fetch_category_stats($conn){

    $sql = "
        SELECT categories.name, categories.slug,
        COUNT(posts.id) AS total_posts
        FROM categories
        LEFT JOIN posts
        ON categories.id = posts.category_id
        GROUP BY categories.id
    ";

    $result = mysqli_query($conn,$sql);
    if (!$result) {
        return [];
    }

    return mysqli_fetch_all($result,MYSQLI_ASSOC);
}

function fetch_dashboard_stats($conn){
    $posts_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM posts WHERE status='published'");
    $published_posts = $posts_q ? (int)mysqli_fetch_assoc($posts_q)['total'] : 0;

    $cats_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM categories");
    $total_categories = $cats_q ? (int)mysqli_fetch_assoc($cats_q)['total'] : 0;

    $reacts_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM reactions");
    $total_reactions = $reacts_q ? (int)mysqli_fetch_assoc($reacts_q)['total'] : 0;

    $comments_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM comments");
    $total_comments = $comments_q ? (int)mysqli_fetch_assoc($comments_q)['total'] : 0;

    return [
        'published_posts' => $published_posts,
        'total_categories' => $total_categories,
        'total_reactions' => $total_reactions,
        'total_comments' => $total_comments
    ];
}

function fetch_user_by_id($conn,$id){

    $id = (int)$id;

    $sql = "SELECT * FROM users WHERE id=$id";

    $result = mysqli_query($conn,$sql);

    return mysqli_fetch_assoc($result);
}

function fetch_site_settings(){

    return [
        // 'site_name' => 'Blog Fusion',
        // 'logo' => 'assets/images/logo.png'
    ];
}

function inject_project_toast() {
    ?>
    <!-- Unified Toast Notification System -->
    <style>
        .project-toast-container {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            opacity: 0;
            pointer-events: none;
            z-index: 10000;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.3s ease;
        }
        .project-toast-container.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
            pointer-events: auto;
        }
    </style>
    <div id="project-toast" class="project-toast-container flex items-center gap-3 bg-slate-900 text-white dark:bg-white dark:text-slate-900 px-6 py-3.5 rounded-2xl shadow-2xl border border-slate-700/50 dark:border-slate-200/50 max-w-md text-sm font-semibold tracking-wide">
        <span id="project-toast-icon" class="text-lg"></span>
        <span id="project-toast-message"></span>
    </div>
    <script>
        window.showToast = function(message, type = 'info') {
            const toast = document.getElementById('project-toast');
            const toastIcon = document.getElementById('project-toast-icon');
            const toastMsg = document.getElementById('project-toast-message');
            if (!toast) return;

            // Map types to icons
            let icon = 'ℹ️';
            if (type === 'success') {
                icon = '✅';
            } else if (type === 'error') {
                icon = '❌';
            } else if (type === 'warning') {
                icon = '⚠️';
            }

            toastIcon.textContent = icon;
            toastMsg.textContent = message;

            // Show toast
            toast.classList.add('show');

            clearTimeout(window.projectToastTimeout);
            window.projectToastTimeout = setTimeout(() => {
                toast.classList.remove('show');
            }, 3200);
        };

        // Auto-detect msg and error parameters in URL
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            let msg = urlParams.get('msg');
            let error = urlParams.get('error');

            if (msg) {
                // Map common codes to user-friendly messages
                let displayMsg = msg;
                let type = 'success';
                
                // Determine if it is actually an error/warning or success message
                const errorCodes = ['p_not_match', 'u_not_find', 'inactive', 'exists', 'verification_failed', 'post_fail', 'unauthorized', 'update_fail', 'user_add_fail', 'delete_self_error', 'user_delete_fail', 'category_delete_fail', 'post_delete_fail', 'comment_delete_fail'];
                const successCodes = ['registered', 'profile_updated', 'user_add', 'posted', 'login_success', 'logout', 'user_deleted', 'category_deleted', 'post_deleted', 'comment_deleted', 'reset_success'];
                
                if (errorCodes.includes(msg)) {
                    type = 'error';
                }
                
                if (msg === 'registered') displayMsg = 'Registration successful! Please login.';
                else if (msg === 'profile_updated') displayMsg = 'Profile updated successfully!';
                else if (msg === 'reset_success') displayMsg = 'Password reset successfully! Please log in with your new password.';
                else if (msg === 'login_success') displayMsg = 'Welcome back! Logged in successfully.';
                else if (msg === 'logout') displayMsg = 'Logged out successfully.';
                else if (msg === 'exists') displayMsg = 'Email already exists. Choose another one.';
                else if (msg === 'p_not_match') displayMsg = 'Password does not match. Please enter again.';
                else if (msg === 'u_not_find') displayMsg = 'Email does not match. Please enter again.';
                else if (msg === 'inactive') displayMsg = 'Your account has been deactivated. Please contact support.';
                else if (msg === 'verification_failed') displayMsg = 'Email OTP verification failed.';
                else if (msg === 'email') { displayMsg = 'Email is already in use by another account.'; type = 'error'; }
                else if (msg === 'user_add') displayMsg = 'User added successfully!';
                else if (msg === 'posted') displayMsg = 'Saved successfully!';
                else if (msg === 'post_fail') displayMsg = 'Failed to save.';
                else if (msg === 'unauthorized') displayMsg = 'Unauthorized action.';
                else if (msg === 'update_fail') displayMsg = 'Failed to update.';
                else if (msg === 'user_add_fail') displayMsg = 'Failed to insert user.';
                else if (msg === 'delete_self_error') displayMsg = 'You cannot delete your own account.';
                else if (msg === 'user_deleted') displayMsg = 'User deleted successfully.';
                else if (msg === 'user_delete_fail') displayMsg = 'Failed to delete user.';
                else if (msg === 'category_deleted') displayMsg = 'Category deleted successfully.';
                else if (msg === 'category_delete_fail') displayMsg = 'Failed to delete category.';
                else if (msg === 'post_deleted') displayMsg = 'Post deleted successfully.';
                else if (msg === 'post_delete_fail') displayMsg = 'Failed to delete post.';
                else if (msg === 'comment_deleted') displayMsg = 'Comment deleted successfully.';
                else if (msg === 'comment_delete_fail') displayMsg = 'Failed to delete comment.';
                
                window.showToast(displayMsg, type);
                
                // Remove parameter from URL without page refresh
                const url = new URL(window.location);
                url.searchParams.delete('msg');
                window.history.replaceState({}, document.title, url.pathname + url.search);
            }
            if (error) {
                window.showToast(error, 'error');
                const url = new URL(window.location);
                url.searchParams.delete('error');
                window.history.replaceState({}, document.title, url.pathname + url.search);
            }
        });
    </script>
    <?php
}

function get_unique_slug($conn, $title, $current_slug = '', $exclude_id = null) {
    $slug = trim($current_slug);
    if (empty($slug)) {
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($title));
        $slug = trim($slug, '-');
    }
    
    $base_slug = $slug;
    $counter = 1;
    
    while (true) {
        $check_sql = "SELECT id FROM posts WHERE slug = '" . mysqli_real_escape_string($conn, $slug) . "'";
        if ($exclude_id !== null) {
            $check_sql .= " AND id != " . (int)$exclude_id;
        }
        $check_sql .= " LIMIT 1";
        
        $res = mysqli_query($conn, $check_sql);
        if ($res && mysqli_num_rows($res) > 0) {
            $slug = $base_slug . '-' . $counter;
            $counter++;
        } else {
            break;
        }
    }
    
    return $slug;
}
?>
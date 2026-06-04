<?php
session_start();
include "../include/db.php";
include "../config.php";
include_once "../include/functions.php";

$slug    = isset($_GET['slug']) ? mysqli_real_escape_string($conn, $_GET['slug']) : '';
$user_id = $_SESSION['user_id'] ?? null;

// Ensure guest comment tracking array exists
if (!isset($_SESSION['guest_comments'])) {
    $_SESSION['guest_comments'] = [];
}

// ==========================================
// AJAX ACTION HANDLER
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action  = $_POST['action'];
    $post_id = (int)$_POST['post_id'];

    // ---- ADD COMMENT (logged-in or ghost) ----
    if ($action === 'add_comment') {
        $text      = mysqli_real_escape_string($conn, trim($_POST['text']));
        $parent_id = (isset($_POST['parent_id']) && $_POST['parent_id'] !== '') ? (int)$_POST['parent_id'] : 'NULL';
        $uid       = $user_id ? $user_id : 'NULL';

        $query  = "INSERT INTO comments (post_id, user_id, comment, parent_id) VALUES ($post_id, $uid, '$text', $parent_id)";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $new_id = mysqli_insert_id($conn);
            if (!$user_id) {
                $_SESSION['guest_comments'][] = $new_id;
            }
            
            // Notify post author
            $author_query = "SELECT author_id, title FROM posts WHERE id = $post_id";
            $author_result = mysqli_query($conn, $author_query);
            $author_row = mysqli_fetch_assoc($author_result);
            if ($author_row && $author_row['author_id']) {
                $post_author_id = (int)$author_row['author_id'];
                if ($post_author_id != $user_id) {
                    $post_title = $author_row['title'];
                    $msg_excerpt = mb_strimwidth(strip_tags($text), 0, 60, "...");
                    add_notification($conn, $post_author_id, "New comment on your post", "Someone commented on your post '$post_title': \"$msg_excerpt\"", "comment");
                }
            }

            // Notify parent comment owner if it is a reply
            if ($parent_id !== 'NULL') {
                $p_query = "SELECT user_id, comment FROM comments WHERE id = $parent_id";
                $p_result = mysqli_query($conn, $p_query);
                $p_row = mysqli_fetch_assoc($p_result);
                if ($p_row && $p_row['user_id']) {
                    $parent_owner_id = (int)$p_row['user_id'];
                    if ($parent_owner_id != $user_id) {
                        $post_title = $author_row['title'] ?? 'your post';
                        $msg_excerpt = mb_strimwidth(strip_tags($text), 0, 60, "...");
                        add_notification($conn, $parent_owner_id, "New reply to your comment", "Someone replied to your comment on '$post_title': \"$msg_excerpt\"", "comment");
                    }
                }
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        }
        exit;
    }

    // ---- EDIT COMMENT (owner only, within 2 min) ----
    if ($action === 'edit_comment') {
        $comment_id = (int)$_POST['comment_id'];
        $text       = mysqli_real_escape_string($conn, trim($_POST['text']));

        $c_query  = "SELECT * FROM comments WHERE id = $comment_id";
        $c_result = mysqli_query($conn, $c_query);
        $c_row    = mysqli_fetch_assoc($c_result);

        if ($c_row) {
            $is_owner  = ($user_id && $user_id == $c_row['user_id']) ||
                         (!$user_id && in_array($comment_id, $_SESSION['guest_comments']));
            $time_diff = time() - strtotime($c_row['created_at']);

            if ($is_owner && $time_diff <= 120) {
                $u_query = "UPDATE comments SET comment = '$text' WHERE id = $comment_id";
                mysqli_query($conn, $u_query);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Time limit exceeded or unauthorized.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Comment not found.']);
        }
        exit;
    }

    // ---- DELETE COMMENT ----
    if ($action === 'delete_comment') {
        $comment_id = (int)$_POST['comment_id'];

        $c_query  = "SELECT * FROM comments WHERE id = $comment_id";
        $c_result = mysqli_query($conn, $c_query);
        $c_row    = mysqli_fetch_assoc($c_result);

        if ($c_row) {
            $is_owner = ($user_id && $user_id == $c_row['user_id']) ||
                        (!$user_id && in_array($comment_id, $_SESSION['guest_comments']));
            if ($is_owner) {
                $d_query = "DELETE FROM comments WHERE id = $comment_id OR parent_id = $comment_id";
                mysqli_query($conn, $d_query);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Comment not found.']);
        }
        exit;
    }

    // ---- LIKE COMMENT ----
    if ($action === 'like_comment') {
        $comment_id = (int)$_POST['comment_id'];

        $lk_query  = "UPDATE comments SET `like` = `like` + 1 WHERE id = $comment_id";
        $lk_result = mysqli_query($conn, $lk_query);

        // Fetch comment details to notify comment owner
        $c_query = "SELECT user_id, comment, post_id FROM comments WHERE id = $comment_id";
        $c_result = mysqli_query($conn, $c_query);
        $c_row = mysqli_fetch_assoc($c_result);
        if ($c_row && $c_row['user_id']) {
            $notify_uid = (int)$c_row['user_id'];
            if ($notify_uid != $user_id) {
                $post_id = (int)$c_row['post_id'];
                $post_title_query = "SELECT title FROM posts WHERE id = $post_id";
                $post_title_res = mysqli_query($conn, $post_title_query);
                $post_title_row = mysqli_fetch_assoc($post_title_res);
                $post_title = $post_title_row['title'] ?? 'a post';
                
                $comment_excerpt = mb_strimwidth(strip_tags($c_row['comment']), 0, 60, "...");
                add_notification($conn, $notify_uid, "Someone liked your comment", "Your comment \"$comment_excerpt\" on '$post_title' received a like!", "like");
            }
        }

        // Return new count
        $cnt_query  = "SELECT `like` FROM comments WHERE id = $comment_id";
        $cnt_result = mysqli_query($conn, $cnt_query);
        $cnt_row    = mysqli_fetch_assoc($cnt_result);

        echo json_encode(['success' => true, 'likes' => (int)$cnt_row['like']]);
        exit;
    }

    // ---- REACT (emoji) ----
    if ($action === 'react') {
        if (!$user_id) {
            echo json_encode(['success' => false, 'error' => 'Login required to react']);
            exit;
        }
        $emoji = mysqli_real_escape_string($conn, $_POST['emoji']);

        $chk_query  = "SELECT * FROM reactions WHERE user_id = $user_id AND post_id = $post_id";
        $chk_result = mysqli_query($conn, $chk_query);
        $chk_row    = mysqli_fetch_assoc($chk_result);

        // Fetch author & post info upfront for notification updates
        $author_query = "SELECT author_id, title FROM posts WHERE id = $post_id";
        $author_result = mysqli_query($conn, $author_query);
        $author_row = mysqli_fetch_assoc($author_result);
        $post_author_id = $author_row ? (int)$author_row['author_id'] : 0;
        $raw_post_title = $author_row ? $author_row['title'] : '';

        // Fetch user's name for notifications
        $user_name = 'Someone';
        $user_name_query = "SELECT name FROM users WHERE id = $user_id";
        $user_name_result = mysqli_query($conn, $user_name_query);
        if ($user_name_result && $user_name_row = mysqli_fetch_assoc($user_name_result)) {
            $user_name = $user_name_row['name'];
        }
        $user_name_escaped = mysqli_real_escape_string($conn, $user_name);

        if ($chk_row) {
            $old_emoji = mysqli_real_escape_string($conn, $chk_row['emoji']);
            if ($chk_row['emoji'] === $emoji) {
                // Delete from DB
                $del_query = "DELETE FROM reactions WHERE id = " . (int)$chk_row['id'];
                mysqli_query($conn, $del_query);
                $status = 'removed';

                 // Sync notifications: remove the reaction notification safely
                if ($post_author_id && $post_author_id != $user_id) {
                    $del_notif_query = "DELETE FROM notifications WHERE user_id = $post_author_id AND type = 'reaction' AND (message LIKE '{$user_name_escaped} reacted with $old_emoji to your post%' COLLATE utf8mb4_bin OR message LIKE '%reacted with $old_emoji to your post%' COLLATE utf8mb4_bin)";
                    mysqli_query($conn, $del_notif_query);
                }
            } else {
                // Update in DB
                $upd_query = "UPDATE reactions SET emoji = '$emoji' WHERE id = " . (int)$chk_row['id'];
                mysqli_query($conn, $upd_query);
                $status = 'updated';

                // Sync notifications: update the reaction notification message with the new emoji safely
                if ($post_author_id && $post_author_id != $user_id) {
                    $new_emoji = mysqli_real_escape_string($conn, $emoji);
                    $new_message = mysqli_real_escape_string($conn, "{$user_name} reacted with {$emoji} to your post '{$raw_post_title}'");
                    $upd_notif_query = "UPDATE notifications SET message = '{$new_message}' WHERE user_id = $post_author_id AND type = 'reaction' AND (message LIKE '{$user_name_escaped} reacted with $old_emoji to your post%' COLLATE utf8mb4_bin OR message LIKE '%reacted with $old_emoji to your post%' COLLATE utf8mb4_bin)";
                    mysqli_query($conn, $upd_notif_query);
                }
            }
        } else {
            // Insert in DB
            $ins_query = "INSERT INTO reactions (user_id, post_id, emoji) VALUES ($user_id, $post_id, '$emoji')";
            mysqli_query($conn, $ins_query);
            $status = 'added';
            
            // Notify author of post
            if ($post_author_id && $post_author_id != $user_id) {
                add_notification($conn, $post_author_id, "New reaction on your post", "{$user_name} reacted with $emoji to your post '{$raw_post_title}'", "reaction");
            }
        }

        // Return fresh counts for all emojis
        $rc_query  = "SELECT emoji, COUNT(*) as total FROM reactions WHERE post_id = $post_id GROUP BY emoji";
        $rc_result = mysqli_query($conn, $rc_query);
        $counts    = [];
        while ($rc_row = mysqli_fetch_assoc($rc_result)) {
            $counts[$rc_row['emoji']] = (int)$rc_row['total'];
        }
        echo json_encode(['success' => true, 'status' => $status, 'counts' => $counts]);
        exit;
    }

    // ---- SAVE / UNSAVE POST ----
    if ($action === 'save_post') {
        if (!$user_id) {
            echo json_encode(['success' => false, 'error' => 'Login required to save posts']);
            exit;
        }
        $sv_chk_query  = "SELECT * FROM saved_posts WHERE user_id = $user_id AND post_id = $post_id";
        $sv_chk_result = mysqli_query($conn, $sv_chk_query);

        if (mysqli_num_rows($sv_chk_result) > 0) {
            $sv_del_query = "DELETE FROM saved_posts WHERE user_id = $user_id AND post_id = $post_id";
            mysqli_query($conn, $sv_del_query);
            echo json_encode(['success' => true, 'status' => 'removed']);
        } else {
            $sv_ins_query = "INSERT INTO saved_posts (user_id, post_id) VALUES ($user_id, $post_id)";
            mysqli_query($conn, $sv_ins_query);
            echo json_encode(['success' => true, 'status' => 'saved']);
        }
        exit;
    }

    // ---- SHARE POST ----
    if ($action === 'share_post') {
        if ($user_id) {
            $sh_query = "INSERT INTO share (user_id, post_id, share) VALUES ($user_id, $post_id, 1)";
            mysqli_query($conn, $sh_query);
        }
        // Return updated share count
        $sh_cnt_query  = "SELECT COUNT(*) as total FROM share WHERE post_id = $post_id";
        $sh_cnt_result = mysqli_query($conn, $sh_cnt_query);
        $sh_cnt_row    = mysqli_fetch_assoc($sh_cnt_result);
        echo json_encode(['success' => true, 'shares' => (int)$sh_cnt_row['total']]);
        exit;
    }
}

// ==========================================
// DATA FETCHING
// ==========================================

// Fetch Main Post
$post_query  = "SELECT p.*, u.name as author_name, u.profile_image as author_image, u.role as author_role,
                c.name as category_name, c.slug as category_slug
                FROM posts p
                LEFT JOIN users u ON p.author_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = '$slug' AND p.status = 'published'";
$post_result = mysqli_query($conn, $post_query);
$post        = mysqli_fetch_assoc($post_result);

if (!$post) {
    die("<div style='display:flex;align-items:center;justify-content:center;height:100vh;font-family:sans-serif;'><h2>Post not found.</h2></div>");
}

$post_id   = (int)$post['id'];
$author_id = (int)$post['author_id'];

// Increment Views
$view_query = "UPDATE posts SET views = views + 1 WHERE id = $post_id";
mysqli_query($conn, $view_query);
$post['views'] += 1;

// Read-time estimate
$word_count    = str_word_count(strip_tags($post['content']));
$read_time_min = max(1, (int)ceil($word_count / 200));

// Fetch Author Stats
$author_query  = "SELECT COUNT(*) as total FROM posts WHERE author_id = $author_id AND status = 'published'";
$author_result = mysqli_query($conn, $author_query);
$author_stats  = mysqli_fetch_assoc($author_result);
$author_posts_count = (int)$author_stats['total'];

// Fetch Reactions (grouped counts)
$reactions_query  = "SELECT emoji, COUNT(*) as total FROM reactions WHERE post_id = $post_id GROUP BY emoji";
$reactions_result = mysqli_query($conn, $reactions_query);
$reactions_counts = [];
while ($rr = mysqli_fetch_assoc($reactions_result)) {
    $reactions_counts[$rr['emoji']] = (int)$rr['total'];
}

// Total reaction count
$total_reactions = array_sum($reactions_counts);

// Current user's reaction
$user_reaction = null;
if ($user_id) {
    $ur_query  = "SELECT emoji FROM reactions WHERE post_id = $post_id AND user_id = $user_id";
    $ur_result = mysqli_query($conn, $ur_query);
    $ur_row    = mysqli_fetch_assoc($ur_result);
    if ($ur_row) {
        $user_reaction = $ur_row['emoji'];
    }
}

// Saved status
$is_saved = false;
if ($user_id) {
    $sv_query  = "SELECT id FROM saved_posts WHERE post_id = $post_id AND user_id = $user_id";
    $sv_result = mysqli_query($conn, $sv_query);
    $is_saved  = mysqli_num_rows($sv_result) > 0;
}

// Share count
$share_query  = "SELECT COUNT(*) as total FROM share WHERE post_id = $post_id";
$share_result = mysqli_query($conn, $share_query);
$share_row    = mysqli_fetch_assoc($share_result);
$share_count  = (int)($share_row['total'] ?? 0);

// Fetch Comments (with user info)
$comment_query  = "SELECT c.*, u.name as user_name, u.profile_image
                   FROM comments c
                   LEFT JOIN users u ON c.user_id = u.id
                   WHERE c.post_id = $post_id
                   ORDER BY c.created_at ASC";
$comment_result = mysqli_query($conn, $comment_query);
$comments       = [];
$total_comments = 0;

while ($crow = mysqli_fetch_assoc($comment_result)) {
    $total_comments++;
    if ($crow['parent_id'] == null) {
        $comments[$crow['id']]           = $crow;
        $comments[$crow['id']]['replies'] = [];
    } else {
        if (isset($comments[$crow['parent_id']])) {
            $comments[$crow['parent_id']]['replies'][] = $crow;
        }
    }
}

// Most Read Today (top 4 by views, excluding current post)
$popular_query  = "SELECT p.*, c.name as category_name, c.slug as cat_slug
                   FROM posts p
                   LEFT JOIN categories c ON p.category_id = c.id
                   WHERE p.status = 'published' AND p.id != $post_id
                   ORDER BY p.views DESC LIMIT 4";
$popular_result = mysqli_query($conn, $popular_query);

// More from Blog Fusion (same category, different post)
$related_query  = "SELECT p.*, u.name as author_name, c.name as category_name
                   FROM posts p
                   LEFT JOIN users u ON p.author_id = u.id
                   LEFT JOIN categories c ON p.category_id = c.id
                   WHERE p.category_id = {$post['category_id']} AND p.id != $post_id AND p.status = 'published'
                   ORDER BY p.created_at DESC LIMIT 3";
$related_result = mysqli_query($conn, $related_query);

// If not enough from same category, fallback to latest posts
$related_rows = [];
while ($rrow = mysqli_fetch_assoc($related_result)) {
    $related_rows[] = $rrow;
}
if (count($related_rows) < 3) {
    $existing_ids = array_merge([$post_id], array_column($related_rows, 'id'));
    $ids_clause   = implode(',', $existing_ids);
    $fallback_query  = "SELECT p.*, u.name as author_name, c.name as category_name
                        FROM posts p
                        LEFT JOIN users u ON p.author_id = u.id
                        LEFT JOIN categories c ON p.category_id = c.id
                        WHERE p.status = 'published' AND p.id NOT IN ($ids_clause)
                        ORDER BY p.created_at DESC LIMIT " . (3 - count($related_rows));
    $fallback_result = mysqli_query($conn, $fallback_query);
    while ($frow = mysqli_fetch_assoc($fallback_result)) {
        $related_rows[] = $frow;
    }
}

function fmtViews($n) {
    if ($n >= 1000000000) {
        $val = round($n / 1000000000, 1);
        return ($val == (int)$val ? (int)$val : $val) . 'B';
    }
    if ($n >= 1000000) {
        $val = round($n / 1000000, 1);
        return ($val == (int)$val ? (int)$val : $val) . 'M';
    }
    if ($n >= 1000) {
        $val = round($n / 1000, 1);
        return ($val == (int)$val ? (int)$val : $val) . 'k';
    }
    return $n;
}

// Helper: time ago
function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)   return 'just now';
    if ($diff < 3600) return (int)($diff/60) . 'm ago';
    if ($diff < 86400)return (int)($diff/3600) . 'h ago';
    return date('M j, Y', strtotime($datetime));
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title><?= htmlspecialchars($post['title']) ?> — Blog Fusion</title>
  <meta name="description" content="<?= htmlspecialchars($post['description'] ?? substr(strip_tags($post['content']), 0, 155)) ?>">
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            'primary':                    '#7C3AED',
            'primary-container':          '#EDE9FE',
            'on-primary':                 '#FFFFFF',
            'secondary':                  '#9F67E4',
            'tertiary':                   '#A855F7',
            'on-tertiary-fixed-variant':  '#581C87',
            'tertiary-fixed':             '#F3E8FF',
            'surface':                    '#FAFAFA',
            'surface-bright':             '#F8F6FF',
            'surface-container-low':      '#F3F0FB',
            'surface-container':          '#EDE9F9',
            'surface-container-high':     '#E5E0F6',
            'surface-container-highest':  '#DDD8F3',
            'on-surface':                 '#1C1B1F',
            'on-surface-variant':         '#49454F',
            'on-background':              '#1C1B1F',
            'outline-variant':            '#CAC4D0',
            'error':                      '#B91C1C',
          },
          fontFamily: { sans: ['Public Sans', 'sans-serif'] },
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Public Sans', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }

    /* Reaction defaults */

    /* Reaction buttons */
    .reaction-btn { position:relative; transition:transform 0.2s, background 0.2s; }
    .reaction-btn .count { position:absolute; top:-6px; right:-6px; background:#7C3AED; color:#fff; font-size:10px; font-weight:700; border-radius:999px; min-width:18px; height:18px; display:flex; align-items:center; justify-content:center; padding:0 4px; opacity:0; transform:scale(0); transition:all 0.2s; pointer-events:none; }
    .reaction-btn.has-count .count { opacity:1; transform:scale(1); }
    .reaction-btn.active { background:rgba(124,58,237,0.12) !important; transform:scale(1.12); box-shadow:0 0 0 2px #7C3AED; }

    /* Tooltip */
    [data-tooltip] { position:relative; }
    [data-tooltip]::after { content:attr(data-tooltip); position:absolute; bottom:calc(100% + 6px); left:50%; transform:translateX(-50%); background:#1c1b1f; color:#fff; font-size:11px; font-weight:600; padding:3px 8px; border-radius:6px; white-space:nowrap; opacity:0; pointer-events:none; transition:opacity 0.15s; z-index:50; }
    [data-tooltip]:hover::after { opacity:1; }

    /* Live dot */
    .live-dot { width:6px; height:6px; background:#7C3AED; border-radius:50%; display:inline-block; animation:pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

    /* Reply indent */
    .reply-block { margin-left:52px; border-left:2px solid rgba(124,58,237,.18); padding-left:16px; }

    /* "You" badge */
    .you-badge { display:inline-flex; align-items:center; padding:1px 8px; border-radius:999px; background:#EDE9FE; color:#7C3AED; font-size:10px; font-weight:700; letter-spacing:0.05em; }

    /* Comment like btn active */
    .like-btn.liked { color:#7C3AED; }

    /* Prose styles */
    .post-content h1,.post-content h2,.post-content h3 { font-weight:800; line-height:1.3; margin:1.5rem 0 0.75rem; }
    .post-content h1 { font-size:2rem; }
    .post-content h2 { font-size:1.6rem; }
    .post-content h3 { font-size:1.3rem; }
    .post-content p  { margin-bottom:1.25rem; color:#49454F; line-height:1.85; }
    .post-content a  { color:#7C3AED; text-decoration:underline; }
    .post-content ul,.post-content ol { padding-left:1.5rem; margin-bottom:1rem; }
    .post-content li { margin-bottom:.4rem; }
    .post-content blockquote { border-left:4px solid #7C3AED; padding:0.5rem 1rem; background:#F3F0FB; border-radius:0 8px 8px 0; font-style:italic; color:#49454F; }

    /* Skeleton shimmer */
    @keyframes shimmer { 0%{background-position:-400px 0} 100%{background-position:400px 0} }
    .shimmer { background:linear-gradient(90deg,#f0eef9 25%,#e0dcf3 37%,#f0eef9 63%); background-size:800px 100%; animation:shimmer 1.4s infinite; border-radius:6px; }
  </style>
</head>
<body class="bg-surface-bright text-on-surface">

<?php inject_project_toast(); ?>

<!-- ============================================================
     BACK BUTTON + HEADER
============================================================ -->
<header class="pt-20 pb-12 px-6 max-w-7xl mx-auto">
  <button onclick="window.history.back()"
    class="flex items-center gap-2 mb-5 md:mb-7 text-sm font-bold text-primary hover:text-purple-800 transition-colors group">
    <span class="material-symbols-outlined text-base group-hover:-translate-x-1 transition-transform">arrow_back</span>
    Back
  </button>

  <div class="flex flex-col items-center text-center space-y-5">
    <!-- Category pill -->
    <a href="?category=<?= urlencode($post['category_slug'] ?? '') ?>"
       class="px-4 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed-variant text-xs font-bold tracking-widest uppercase hover:bg-purple-200 transition-colors">
      <?= htmlspecialchars($post['category_name'] ?? 'General') ?>
    </a>

    <!-- Title -->
    <h1 class="text-3xl md:text-5xl lg:text-6xl font-black tracking-tighter text-on-surface max-w-4xl leading-[1.1]">
      <?= htmlspecialchars($post['title']) ?>
    </h1>

    <!-- Meta -->
    <div class="flex items-center gap-4 pt-2 flex-wrap justify-center">
      <div class="w-12 h-12 rounded-full bg-surface-container-highest overflow-hidden ring-4 ring-surface-container-low shrink-0">
        <img alt="<?= htmlspecialchars($post['author_name']) ?>"
             class="w-full h-full object-cover"
             src="<?= site_url($post['author_image'] ?? 'upload/profile-images/default.png') ?>"
             onerror="this.src='<?= site_url('upload/profile-images/default.png') ?>'" />
      </div>
      <div class="text-left">
        <div class="font-bold text-on-surface"><?= htmlspecialchars($post['author_name']) ?></div>
        <div class="text-on-surface-variant text-sm flex items-center gap-2 flex-wrap">
          <span><?= date('F j, Y', strtotime($post['created_at'])) ?></span>
          <span>·</span>
          <span><?= $read_time_min ?> min read</span>
          <span>·</span>
          <span class="flex items-center gap-1.5">
            <span class="live-dot"></span>
            <span class="text-primary font-semibold" id="viewCounter"><?= fmtViews($post['views']) ?> views</span>
          </span>
          <?php if ($total_reactions > 0): ?>
          <span>·</span>
          <span class="text-on-surface-variant"><?= $total_reactions ?> reactions</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- ============================================================
     FEATURED IMAGE
============================================================ -->
<?php if (!empty($post['image'])): ?>
<section class="max-w-7xl mx-auto px-0 md:px-6 mb-16">
  <div class="w-full h-[480px] md:h-[614px] md:rounded-3xl overflow-hidden shadow-2xl shadow-primary/10">
    <img alt="<?= htmlspecialchars($post['title']) ?>"
         class="w-full h-full object-cover"
         src="<?= site_url($post['image']) ?>"
         onerror="this.src='<?= site_url('upload/profile-images/default.png') ?>'" />
  </div>
</section>
<?php endif; ?>

<!-- ============================================================
     MAIN GRID
============================================================ -->
<main class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 relative">

  <!-- ===================== SIDEBAR ===================== -->
  <aside class="lg:col-span-4 order-2 lg:order-2 space-y-8">
    <div class="sticky top-24 space-y-8">

      <!-- About the Author -->
      <div class="p-7 rounded-3xl bg-surface-container-highest/40 backdrop-blur-sm border border-outline-variant/10">
        <h3 class="text-xs font-black tracking-widest uppercase mb-5 text-on-surface-variant">About the Author</h3>
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 rounded-full overflow-hidden bg-surface-container-highest shrink-0">
            <img alt="<?= htmlspecialchars($post['author_name']) ?>"
                 class="w-full h-full object-cover"
                 src="<?= site_url($post['author_image'] ?? 'upload/profile-images/default.png') ?>"
                 onerror="this.src='<?= site_url('upload/profile-images/default.png') ?>'" />
          </div>
          <div>
            <p class="font-bold text-sm text-on-surface"><?= htmlspecialchars($post['author_name']) ?></p>
            <p class="text-xs text-on-surface-variant capitalize"><?= htmlspecialchars($post['author_role'] ?? 'Author') ?></p>
          </div>
        </div>
        <p class="text-on-surface-variant text-sm leading-relaxed mb-4">
          Expert creator focused on delivering high-quality insights and content.
        </p>
        <div class="text-xs text-on-surface-variant font-bold pt-3 border-t border-outline-variant/20">
          <?= $author_posts_count ?> articles published
        </div>
      </div>

      <!-- Most Read Today -->
      <div class="space-y-5">
        <div class="flex justify-between items-center px-1">
          <h3 class="text-xs font-black tracking-widest uppercase text-on-surface-variant">Most Read Today</h3>
          <a href="<?= site_url('pages/home.php') ?>" class="text-[11px] font-bold text-primary hover:underline">See all</a>
        </div>
        <div class="space-y-3">
          <?php
          $pop_num = 0;
          while ($pop = mysqli_fetch_assoc($popular_result)):
              $pop_num++;
              $pop_read = max(1, (int)ceil(str_word_count(strip_tags($pop['content'])) / 200));
              $pop_year = date("Y", strtotime($pop['created_at']));
              $pop_month = date("m", strtotime($pop['created_at']));
          ?>
          <div class="group flex gap-4 items-center p-3 rounded-2xl hover:bg-surface-container transition-colors cursor-pointer"
               onclick="window.location.href='<?= site_url("redirect-post.php?id=" . $pop['id']) ?>'">
            <div class="w-16 h-16 shrink-0 rounded-xl overflow-hidden bg-surface-container-high">
              <img alt="<?= htmlspecialchars($pop['title']) ?>"
                   class="w-full h-full object-cover"
                   src="<?= site_url($pop['image'] ?? 'upload/profile-images/default.png') ?>"
                   onerror="this.src='<?= site_url('upload/profile-images/default.png') ?>'" />
            </div>
            <div class="min-w-0">
              <h4 class="font-bold text-sm text-on-surface group-hover:text-primary transition-colors leading-tight line-clamp-2">
                <?= htmlspecialchars($pop['title']) ?>
              </h4>
              <span class="text-[10px] text-tertiary font-bold uppercase tracking-widest block mt-0.5">
                <?= htmlspecialchars($pop['category_name'] ?? '') ?>
              </span>
              <p class="text-xs text-on-surface-variant mt-0.5">
                <?= fmtViews($pop['views']) ?> views · <?= $pop_read ?> min
              </p>
            </div>
          </div>
          <?php endwhile; ?>
          <?php if ($pop_num === 0): ?>
          <p class="text-xs text-on-surface-variant px-2">No popular posts yet.</p>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </aside>

  <!-- ===================== ARTICLE ===================== -->
  <article class="lg:col-span-8 order-1 lg:order-1">

    <!-- Post content -->
    <div class="post-content max-w-none space-y-6 text-on-surface leading-[1.85] text-lg">
      <?php 
        $formatted_content = $post['content'];
        $formatted_content = str_replace('../upload/', BASE_URL . 'upload/', $formatted_content);
        $formatted_content = preg_replace('/(src|href)="(?!\.\.\/|https?:\/\/|ftp:\/\/|\/\/)(upload\/)/i', '$1="' . BASE_URL . '$2', $formatted_content);
        $formatted_content = preg_replace('/(src|href)="https?:\/\/[^\/]+(?:\/[^\/]+)*\/upload\//i', '$1="' . BASE_URL . 'upload/', $formatted_content);
        echo $formatted_content;
      ?>
    </div>

    <!-- Tags -->
    <?php if (!empty($post['tag']) && $post['tag'] !== 'Array'): ?>
    <div class="mt-10 flex flex-wrap gap-2">
      <?php
      $raw_tags = $post['tag'];
      $decoded = json_decode($raw_tags, true);
      if (is_array($decoded)) {
          $tags = $decoded;
      } else {
          $tags = explode(',', $raw_tags);
      }
      foreach ($tags as $tag):
          $tag = trim($tag, " \t\n\r\0\x0B\"'[]");
          if ($tag && $tag !== 'Array'):
      ?>
      <span class="px-3 py-1 rounded-full bg-surface-container text-xs font-semibold text-on-surface-variant hover:bg-primary-container hover:text-primary transition-colors cursor-pointer">
        #<?= htmlspecialchars($tag) ?>
      </span>
      <?php endif; endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- ===================== REACTIONS + SHARE + SAVE ===================== -->
    <section class="mt-14 py-8 border-y border-outline-variant/20 flex flex-wrap items-center justify-between gap-6">
      <!-- Reactions -->
      <div class="flex items-center gap-3 flex-wrap">
        <span class="text-xs font-black text-on-surface-variant uppercase tracking-widest">React</span>
        <div class="flex flex-wrap gap-2 md:gap-3" id="reactionsRow">
          <?php
          $emoji_meta = [
              '👍' => 'Like',
              '❤️' => 'Love',
              '😂' => 'Funny',
              '😮' => 'Wow',
              '😢' => 'Sad',
              '😡' => 'Angry',
          ];
          foreach ($emoji_meta as $em => $label):
              $count       = $reactions_counts[$em] ?? 0;
              $activeClass = ($user_reaction === $em) ? 'active' : '';
              $hasCountClass = ($count > 0) ? 'has-count' : '';
              $data_emoji  = htmlspecialchars($em, ENT_QUOTES);
          ?>
          <button onclick="react(this,'<?= $data_emoji ?>')"
                  class="reaction-btn <?= $activeClass ?> <?= $hasCountClass ?> w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 text-xl relative"
                  data-tooltip="<?= $label ?>"
                  data-emoji="<?= $data_emoji ?>">
            <span class="count text-[10px]"><?= $count ?></span><?= $em ?>
          </button>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Share + Save -->
      <div class="flex items-center gap-3 flex-wrap">
        <button onclick="sharePost()" id="shareBtn"
                class="flex items-center gap-2 px-5 py-2.5 rounded-full border border-outline-variant text-sm font-bold text-on-surface hover:bg-surface-container transition-colors">
          <span class="material-symbols-outlined text-lg">share</span>
          Share
          <span id="shareCount" class="text-xs text-on-surface-variant font-normal ml-1"><?= $share_count > 0 ? '· '.$share_count : '' ?></span>
        </button>
        <button id="saveBtn" onclick="toggleSave()"
                class="flex items-center gap-2 px-5 py-2.5 rounded-full <?= $is_saved ? 'bg-primary text-white' : 'bg-on-surface text-white' ?> text-sm font-bold shadow-lg hover:scale-105 transition-transform">
          <span class="material-symbols-outlined text-lg" id="saveIcon"><?= $is_saved ? 'bookmark_added' : 'bookmark' ?></span>
          <span id="saveText"><?= $is_saved ? 'Saved' : 'Save' ?></span>
        </button>
      </div>
    </section>

    <!-- ===================== COMMENTS ===================== -->
    <section class="mt-14 space-y-8" id="commentsSection">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-black tracking-tight">
          Community Thoughts
          <span class="text-on-surface-variant font-normal text-xl ml-1">(<span id="commentCount"><?= $total_comments ?></span>)</span>
        </h2>
      </div>

      <!-- Comment Input -->
      <div class="flex gap-4 p-6 rounded-3xl bg-surface-container-low">
        <div class="w-10 h-10 rounded-full bg-primary-fixed shrink-0 flex items-center justify-center text-primary font-black text-sm overflow-hidden">
          <?php if ($user_id && !empty($_SESSION['profile_image'])): ?>
          <img src="<?= site_url($_SESSION['profile_image']) ?>" class="w-full h-full object-cover" />
          <?php else: ?>
          <?= $user_id ? strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)) : 'G' ?>
          <?php endif; ?>
        </div>
        <div class="flex-1 space-y-3">
          <?php if (!$user_id): ?>
          <p class="text-xs text-on-surface-variant bg-tertiary-fixed px-3 py-2 rounded-xl">
            💬 You're commenting as <strong>Guest Reader</strong>.
            <a href="<?= site_url('pages/login.php') ?>" class="text-primary font-bold hover:underline ml-1">Login</a> or
            <a href="<?= site_url('pages/register.php') ?>" class="text-primary font-bold hover:underline">Sign Up</a> for a personalised experience.
          </p>
          <?php endif; ?>
          <textarea id="commentInput"
                    class="w-full bg-transparent border-none focus:ring-0 text-on-surface p-0 placeholder:text-on-surface-variant/50 resize-none h-20 outline-none text-sm leading-relaxed"
                    placeholder="Join the discussion..."></textarea>
          <div class="flex justify-end">
            <button onclick="submitComment()"
                    class="px-6 py-2 bg-primary text-white font-bold rounded-xl text-sm shadow-lg hover:bg-purple-700 transition-all active:scale-95">
              Post Comment
            </button>
          </div>
        </div>
      </div>

      <!-- Comments List -->
      <div id="commentsList" class="space-y-6">
        <?php if (empty($comments)): ?>
        <div class="text-center py-12 text-on-surface-variant">
          <span class="material-symbols-outlined text-4xl block mb-2">chat_bubble_outline</span>
          <p class="text-sm">No comments yet. Be the first to comment!</p>
        </div>
        <?php endif; ?>

        <?php foreach ($comments as $c):
            $is_owner  = ($user_id && $user_id == $c['user_id']) ||
                         (!$user_id && in_array($c['id'], $_SESSION['guest_comments']));
            $can_edit  = $is_owner && ((time() - strtotime($c['created_at'])) <= 120);
            $c_name    = !empty($c['user_name']) ? $c['user_name'] : 'Guest Reader';
            $c_avatar  = !empty($c['profile_image']) ? site_url($c['profile_image']) : null;
            $c_initial = strtoupper(substr($c_name, 0, 1));
        ?>
        <div class="flex gap-4 p-3 rounded-2xl hover:bg-surface-container-low/50 transition-colors" id="comment-<?= $c['id'] ?>">
          <!-- Avatar -->
          <div class="w-10 h-10 rounded-full shrink-0 flex items-center justify-center text-sm font-black bg-primary-fixed text-primary overflow-hidden">
            <?= $c_avatar
                ? "<img src='{$c_avatar}' class='w-full h-full object-cover' onerror=\"this.style.display='none'\" />"
                : $c_initial ?>
          </div>

          <!-- Body -->
          <div class="flex-1 space-y-2 min-w-0">
            <!-- Name + time -->
            <div class="flex items-center gap-2 flex-wrap">
              <span class="font-bold text-on-surface text-sm"><?= htmlspecialchars($c_name) ?></span>
              <span class="text-xs text-on-surface-variant"><?= timeAgo($c['created_at']) ?></span>
              <?php if ($is_owner): ?>
              <span class="you-badge">You</span>
              <?php endif; ?>
            </div>

            <!-- Text / Edit box -->
            <div id="comment-text-<?= $c['id'] ?>">
              <p class="text-on-surface-variant text-sm leading-relaxed"><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
            </div>
            <div id="edit-box-<?= $c['id'] ?>" class="hidden space-y-2">
              <textarea id="edit-input-<?= $c['id'] ?>"
                        class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl p-3 text-sm text-on-surface resize-none focus:outline-none focus:ring-2 focus:ring-primary/30"
                        rows="3"><?= htmlspecialchars($c['comment']) ?></textarea>
              <div class="flex gap-2">
                <button onclick="saveEdit(<?= $c['id'] ?>)"
                        class="text-xs bg-primary text-white px-4 py-1.5 rounded-lg font-bold hover:bg-purple-700 transition-colors">Save</button>
                <button onclick="toggleEdit(<?= $c['id'] ?>)"
                        class="text-xs text-on-surface-variant px-3 py-1.5 hover:text-on-surface transition-colors">Cancel</button>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4 pt-1 flex-wrap">
              <button onclick="toggleReplyBox(<?= $c['id'] ?>)"
                      class="text-xs font-bold text-primary hover:underline transition-colors">
                Reply
              </button>
              <button onclick="likeComment(this, <?= $c['id'] ?>)"
                      class="like-btn flex items-center gap-1 text-xs font-bold text-on-surface-variant hover:text-on-surface transition-colors"
                      id="like-btn-<?= $c['id'] ?>">
                <span class="material-symbols-outlined text-[14px]">thumb_up</span>
                <span id="like-count-<?= $c['id'] ?>"><?= $c['like'] ?></span>
              </button>
              <?php if ($can_edit): ?>
              <button onclick="toggleEdit(<?= $c['id'] ?>)"
                      class="text-xs font-bold text-secondary hover:underline transition-colors"
                      id="edit-btn-<?= $c['id'] ?>">
                Edit <span class="opacity-60 font-normal">(2m)</span>
              </button>
              <?php endif; ?>
              <?php if ($is_owner): ?>
              <button onclick="deleteComment(<?= $c['id'] ?>)"
                      class="text-xs font-bold text-error hover:underline transition-colors">Delete</button>
              <?php endif; ?>
            </div>

            <!-- Replies -->
            <?php foreach ($c['replies'] as $r):
                $r_is_owner = ($user_id && $user_id == $r['user_id']) ||
                              (!$user_id && in_array($r['id'], $_SESSION['guest_comments']));
                $r_can_edit = $r_is_owner && ((time() - strtotime($r['created_at'])) <= 120);
                $r_name     = !empty($r['user_name']) ? $r['user_name'] : 'Guest Reader';
                $r_initial  = strtoupper(substr($r_name, 0, 1));
                $r_avatar   = !empty($r['profile_image']) ? site_url($r['profile_image']) : null;
            ?>
            <div class="reply-block mt-4 flex gap-3" id="comment-<?= $r['id'] ?>">
              <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center text-xs font-black bg-primary-fixed text-primary overflow-hidden">
                <?= $r_avatar
                    ? "<img src='{$r_avatar}' class='w-full h-full object-cover' onerror=\"this.style.display='none'\" />"
                    : $r_initial ?>
              </div>
              <div class="flex-1 space-y-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="font-bold text-on-surface text-sm"><?= htmlspecialchars($r_name) ?></span>
                  <span class="text-xs text-on-surface-variant"><?= timeAgo($r['created_at']) ?></span>
                  <?php if ($r_is_owner): ?>
                  <span class="you-badge">You</span>
                  <?php endif; ?>
                </div>

                <div id="comment-text-<?= $r['id'] ?>">
                  <p class="text-on-surface-variant text-sm leading-relaxed"><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
                </div>
                <div id="edit-box-<?= $r['id'] ?>" class="hidden space-y-2 mt-2">
                  <textarea id="edit-input-<?= $r['id'] ?>"
                            class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl p-2 text-sm text-on-surface resize-none focus:outline-none focus:ring-2 focus:ring-primary/30"
                            rows="2"><?= htmlspecialchars($r['comment']) ?></textarea>
                  <div class="flex gap-2">
                    <button onclick="saveEdit(<?= $r['id'] ?>)"
                            class="text-xs bg-primary text-white px-3 py-1 rounded-lg font-bold">Save</button>
                    <button onclick="toggleEdit(<?= $r['id'] ?>)"
                            class="text-xs text-on-surface-variant px-2 py-1">Cancel</button>
                  </div>
                </div>

                <div class="flex items-center gap-4 pt-1 flex-wrap">
                  <button onclick="likeComment(this, <?= $r['id'] ?>)"
                          class="like-btn flex items-center gap-1 text-xs font-bold text-on-surface-variant hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined text-[14px]">thumb_up</span>
                    <span id="like-count-<?= $r['id'] ?>"><?= $r['like'] ?></span>
                  </button>
                  <?php if ($r_can_edit): ?>
                  <button onclick="toggleEdit(<?= $r['id'] ?>)"
                          class="text-xs font-bold text-secondary hover:underline">Edit <span class="opacity-60 font-normal">(2m)</span></button>
                  <?php endif; ?>
                  <?php if ($r_is_owner): ?>
                  <button onclick="deleteComment(<?= $r['id'] ?>)"
                          class="text-xs font-bold text-error hover:underline">Delete</button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>

            <!-- Reply input box -->
            <div id="reply-box-<?= $c['id'] ?>" class="hidden mt-4">
              <div class="flex gap-3">
                <textarea id="reply-input-<?= $c['id'] ?>"
                          class="flex-1 bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-3 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary/30"
                          rows="2"
                          placeholder="Write a reply..."></textarea>
                <button onclick="submitReply(<?= $c['id'] ?>)"
                        class="px-5 py-2 bg-primary text-white text-sm font-bold rounded-xl self-end hover:bg-purple-700 transition-colors">
                  Reply
                </button>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Not logged in — bottom nudge -->
      <?php if (!$user_id): ?>
      <div class="mt-6 p-5 rounded-2xl bg-tertiary-fixed border border-purple-200 text-center">
        <p class="text-sm font-semibold text-on-tertiary-fixed-variant mb-3">
          Want a personalised experience? Your comments will be linked to your account.
        </p>
        <div class="flex justify-center gap-3">
          <a href="<?= site_url('pages/login.php') ?>"
             class="px-6 py-2 bg-primary text-white font-bold rounded-xl text-sm hover:bg-purple-700 transition-colors">
             Login
          </a>
          <a href="<?= site_url('pages/register.php') ?>"
             class="px-6 py-2 border border-primary text-primary font-bold rounded-xl text-sm hover:bg-primary-container transition-colors">
             Sign Up
          </a>
        </div>
      </div>
      <?php endif; ?>
    </section>

  </article>
</main>

<!-- ============================================================
     MORE FROM BLOG FUSION
============================================================ -->
<section class="max-w-7xl mx-auto px-6 py-24">
  <div class="flex justify-between items-center mb-10">
    <h3 class="text-xs font-black tracking-widest uppercase text-on-surface-variant">More from Blog Fusion</h3>
    <a href="<?= site_url('pages/home.php') ?>" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
      View all <span class="material-symbols-outlined text-sm">arrow_forward</span>
    </a>
  </div>

  <?php if (empty($related_rows)): ?>
  <p class="text-on-surface-variant text-sm text-center py-8">No related posts found.</p>
  <?php else: ?>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <?php foreach ($related_rows as $rel):
        $rel_read = max(1, (int)ceil(str_word_count(strip_tags($rel['content'])) / 200));
    ?>
    <div class="group cursor-pointer" onclick="window.location.href='<?= site_url('redirect-post.php?id=' . urlencode($rel['id'])) ?>'">
      <div class="aspect-video rounded-3xl overflow-hidden bg-surface-container-high mb-5 relative">
        <img alt="<?= htmlspecialchars($rel['title']) ?>"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
             src="<?= site_url($rel['image'] ?? 'upload/profile-images/default.png') ?>"
             onerror="this.src='<?= site_url('upload/profile-images/default.png') ?>'" />
      </div>
      <span class="text-[10px] text-tertiary font-bold uppercase tracking-widest"><?= htmlspecialchars($rel['category_name'] ?? '') ?></span>
      <h4 class="text-xl font-bold text-on-surface group-hover:text-primary transition-colors leading-tight mt-1 mb-2 line-clamp-2">
        <?= htmlspecialchars($rel['title']) ?>
      </h4>
      <div class="text-sm text-on-surface-variant flex items-center gap-2">
        <span>By <?= htmlspecialchars($rel['author_name'] ?? 'Unknown') ?></span>
        <span>·</span>
        <span><?= $rel_read ?> min read</span>
        <span>·</span>
        <span><?= fmtViews($rel['views']) ?> views</span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<!-- ============================================================
     JAVASCRIPT
============================================================ -->
<script>
  const POST_ID        = <?= $post_id ?>;
  const IS_LOGGED_IN   = <?= $user_id ? 'true' : 'false' ?>;
  const USER_REACTION  = <?= $user_reaction ? json_encode($user_reaction) : 'null' ?>;

  // ---- GENERIC POST ----
  async function apiPost(data) {
    const fd = new FormData();
    fd.append('post_id', POST_ID);
    for (const [k, v] of Object.entries(data)) fd.append(k, v);
    const res = await fetch(window.location.href, { method: 'POST', body: fd });
    return res.json();
  }

  // ---- SUBMIT COMMENT ----
  async function submitComment() {
    const input = document.getElementById('commentInput');
    const text  = input.value.trim();
    if (!text) { showToast('Please write something first ✍️'); return; }

    const data = await apiPost({ action: 'add_comment', text });
    if (data.success) {
      showToast('Comment posted! 💬');
      input.value = '';
      location.reload();
    } else {
      showToast(data.error || 'Failed to post comment.');
    }
  }

  // ---- SUBMIT REPLY ----
  async function submitReply(parentId) {
    const input = document.getElementById(`reply-input-${parentId}`);
    const text  = input.value.trim();
    if (!text) { showToast('Please write something first ✍️'); return; }

    const data = await apiPost({ action: 'add_comment', text, parent_id: parentId });
    if (data.success) {
      showToast('Reply posted! 💬');
      input.value = '';
      location.reload();
    } else {
      showToast(data.error || 'Failed to post reply.');
    }
  }

  // ---- TOGGLE EDIT ----
  function toggleEdit(id) {
    document.getElementById(`comment-text-${id}`).classList.toggle('hidden');
    document.getElementById(`edit-box-${id}`).classList.toggle('hidden');
  }

  // ---- SAVE EDIT ----
  async function saveEdit(id) {
    const text = document.getElementById(`edit-input-${id}`).value.trim();
    if (!text) { showToast('Comment cannot be empty.'); return; }

    const data = await apiPost({ action: 'edit_comment', comment_id: id, text });
    if (data.success) {
      showToast('Comment updated ✅');
      document.querySelector(`#comment-text-${id} p`).textContent = text;
      toggleEdit(id);
    } else {
      showToast(data.error || 'Update failed.');
    }
  }

  // ---- DELETE COMMENT ----
  async function deleteComment(id) {
    if (!confirm('Delete this comment?')) return;
    const data = await apiPost({ action: 'delete_comment', comment_id: id });
    if (data.success) {
      const el = document.getElementById(`comment-${id}`);
      if (el) el.remove();
      const cc = document.getElementById('commentCount');
      if (cc) cc.textContent = Math.max(0, parseInt(cc.textContent) - 1);
      showToast('Comment deleted.');
    } else {
      showToast(data.error || 'Delete failed.');
    }
  }

  // ---- LIKE COMMENT ----
  async function likeComment(btn, id) {
    const data = await apiPost({ action: 'like_comment', comment_id: id });
    if (data.success) {
      const countEl = document.getElementById(`like-count-${id}`);
      if (countEl) countEl.textContent = data.likes;
      btn.classList.add('liked');
    }
  }

  // ---- REACT ----
  async function react(btn, emoji) {
    if (!IS_LOGGED_IN) {
      showToast('🔒 Login required to react');
      return;
    }

    const data = await apiPost({ action: 'react', emoji });
    if (data.success) {
      // Update all reaction buttons with fresh counts and classes
      document.querySelectorAll('.reaction-btn').forEach(b => {
        const em       = b.dataset.emoji;
        const countEl  = b.querySelector('.count');
        const newCount = data.counts[em] || 0;
        countEl.textContent = newCount;

        // Toggle count badge visibility
        if (newCount > 0) {
          b.classList.add('has-count');
        } else {
          b.classList.remove('has-count');
        }

        // Toggle active reaction highlighting (only one emoji reaction is active at a time)
        if (em === emoji) {
          if (data.status === 'added' || data.status === 'updated') {
            b.classList.add('active');
          } else {
            b.classList.remove('active');
          }
        } else {
          b.classList.remove('active');
        }
      });
    } else {
      showToast(data.error || 'Reaction failed.');
    }
  }

  // ---- SAVE ----
  async function toggleSave() {
    if (!IS_LOGGED_IN) {
      showToast('🔒 Login required to save posts');
      return;
    }
    const data = await apiPost({ action: 'save_post' });
    if (data.success) {
      const btn  = document.getElementById('saveBtn');
      const icon = document.getElementById('saveIcon');
      const text = document.getElementById('saveText');

      if (data.status === 'saved') {
        btn.classList.replace('bg-on-surface', 'bg-primary');
        icon.textContent = 'bookmark_added';
        text.textContent = 'Saved';
        showToast('📌 Post saved!');
      } else {
        btn.classList.replace('bg-primary', 'bg-on-surface');
        icon.textContent = 'bookmark';
        text.textContent = 'Save';
        showToast('Removed from saved.');
      }
    } else {
      showToast(data.error || 'Save failed.');
    }
  }

  // ---- SHARE ----
  async function sharePost() {
    try {
      await navigator.clipboard.writeText(window.location.href);
    } catch(e) {}
    showToast('🔗 Link copied to clipboard!');

    const data = await apiPost({ action: 'share_post' });
    if (data.success && data.shares > 0) {
      const el = document.getElementById('shareCount');
      if (el) el.textContent = '· ' + data.shares;
    }
  }

  // ---- TOGGLE REPLY BOX ----
  function toggleReplyBox(id) {
    const box = document.getElementById(`reply-box-${id}`);
    box.classList.toggle('hidden');
    if (!box.classList.contains('hidden')) {
      box.querySelector('textarea').focus();
    }
  }

  // ---- EDIT BUTTON AUTO-HIDE ----
  // Hide edit button after 2 minutes client-side
  document.querySelectorAll('[id^="edit-btn-"]').forEach(btn => {
    setTimeout(() => { btn.remove(); }, 120000);
  });
</script>
</body>
</html>
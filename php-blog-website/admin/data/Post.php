<?php 

// Get All 
function getAll($conn){
   $sql = "SELECT post.*, users.username AS author_name 
           FROM post 
           LEFT JOIN users ON post.author_id = users.id AND users.type = 'author'
           WHERE post.publish = 1 
           ORDER BY post.post_id DESC";

   $stmt = $conn->prepare($sql);
   $stmt->execute();

   if($stmt->rowCount() >= 1){
   	   $data = $stmt->fetchAll();
   	   return $data;
   }else {
   	 return 0;
   }
}

 // getAllDeep admin
function getAllDeep($conn){
   $sql = "SELECT * FROM post";
   $stmt = $conn->prepare($sql);
   $stmt->execute();

   if($stmt->rowCount() >= 1){
         $data = $stmt->fetchAll();
         return $data;
   }else {
       return 0;
   }
}

function getPostsByUser($conn, $user_id) {
    $sql = "SELECT * FROM post WHERE author_id = ? ORDER BY post_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}
function getUserPostStats($conn, $user_id) {
    $stats = [
        'total_posts' => 0,
        'total_likes' => 0,
        'total_comments' => 0,
        'top_post' => 'N/A'
    ];

    // Total posts
    $stmt = $conn->prepare("SELECT COUNT(*) FROM post WHERE author_id = ?");
    $stmt->execute([$user_id]);
    $stats['total_posts'] = $stmt->fetchColumn();

    // Total likes
$stmt = $conn->prepare("
    SELECT pl.post_id, COUNT(pl.like_id) AS like_count
    FROM post_like pl
    JOIN post p ON pl.post_id = p.post_id
    WHERE p.author_id = ?
    GROUP BY pl.post_id
    ORDER BY like_count DESC
    LIMIT 1
");
$stmt->execute([$user_id]);
$topPost = $stmt->fetch(PDO::FETCH_ASSOC);

// Set the stat value
if ($topPost && isset($topPost['post_id'])) {
    $stmt = $conn->prepare("SELECT post_title FROM post WHERE post_id = ?");
    $stmt->execute([$topPost['post_id']]);
    $topTitle = $stmt->fetchColumn();
    $stats['top_post'] = $topTitle ?: 'Untitled';
} else {
    $stats['top_post'] = 'N/A';
}

    // Total comments
    $stmt = $conn->prepare("SELECT COUNT(*) FROM comment WHERE post_id IN (SELECT post_id FROM post WHERE author_id = ?)");
    $stmt->execute([$user_id]);
    $stats['total_comments'] = $stmt->fetchColumn();

    // Most liked post title
   $stmt = $conn->prepare("
      SELECT pl.post_id, COUNT(pl.liked_by) AS like_count
      FROM post_like pl
      JOIN post p ON pl.post_id = p.post_id
      WHERE p.author_id = ?
      GROUP BY pl.post_id
      ORDER BY like_count DESC
      LIMIT 1
   ");
   $stmt->execute([$user_id]);
   $topPostId = $stmt->fetchColumn();

   // Optional: get the title of the top post
   if ($topPostId) {
      $stmt = $conn->prepare("SELECT post_title FROM post WHERE post_id = ?");
      $stmt->execute([$topPostId]);
      $topPostTitle = $stmt->fetchColumn();
      $stats['top_post'] = $topPostTitle ?: 'Untitled';
   } else {
      $stats['top_post'] = 'N/A';
   }

    return $stats;
}
function getRecentCommentsByUserPosts($conn, $author_id, $limit = 5) {
    // Make sure $limit is cast to an integer to prevent injection
    $limit = (int)$limit;

    $sql = "SELECT c.*, p.post_title 
            FROM comment c
            JOIN post p ON c.post_id = p.post_id
            WHERE p.author_id = ?
            ORDER BY c.created_at DESC
            LIMIT $limit";  // Injected safely

    $stmt = $conn->prepare($sql);
    $stmt->execute([$author_id]);
    return $stmt->fetchAll();
}

function getActivityLogs($conn, $user_id) {
    $logs = [];

    // Created posts
    $stmt = $conn->prepare("SELECT post_title, crated_at FROM post WHERE author_id = ? ORDER BY crated_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $posts = $stmt->fetchAll();
    foreach ($posts as $p) {
        $logs[] = [
            'message' => "You published <b>" . htmlspecialchars($p['post_title']) . "</b>",
            'timestamp' => $p['crated_at']
        ];
    }

    // You can later add logs for edits, deletions, etc.

    return $logs;
}
function getPostCountByMonth($conn, $user_id) {
    $sql = "SELECT DATE_FORMAT(crated_at, '%Y-%m') AS month, COUNT(*) AS count
            FROM post
            WHERE author_id = ?
            GROUP BY month
            ORDER BY month";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $data = [];
    foreach ($stmt->fetchAll() as $row) {
        $data[$row['month']] = $row['count'];
    }
    return $data;
}

// getAllPostsByCategory
function getAllPostsByCategory($conn, $category_id){
   $sql = "SELECT * FROM post  WHERE category=? AND publish=1";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$category_id]);

   if($stmt->rowCount() >= 1){
         $data = $stmt->fetchAll();
         return $data;
   }else {
       return 0;
   }
}
// getById
function getById($conn, $id){
   $sql = "SELECT post.*, users.username AS author_name 
           FROM post 
           LEFT JOIN users ON post.author_id = users.id AND users.type = 'author'
           WHERE post.post_id = ? AND post.publish = 1";

   $stmt = $conn->prepare($sql);
   $stmt->execute([$id]);

   if($stmt->rowCount() >= 1){
         $data = $stmt->fetch();
         return $data;
   }else {
       return 0;
   }
}


// serach
function serach($conn, $key){
   # creating simple search temple :)  
   $key = "%{$key}%";

   $sql = "SELECT * FROM post 
           WHERE publish=1 AND (post_title LIKE ? 
           OR post_text LIKE ?)";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$key, $key]);

   if($stmt->rowCount() >= 1){
         $data = $stmt->fetchAll();
         return $data;
   }else {
       return 0;
   }
}
// getCategoryById
function getCategoryById($conn, $id){
   $sql = "SELECT * FROM category WHERE id=?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$id]);

   if($stmt->rowCount() >= 1){
         $data = $stmt->fetch();
         return $data;
   }else {
       return 0;
   }
}

//get 5 Categoies 

function get5Categoies($conn){
   $sql = "SELECT * FROM category LIMIT 5";
   $stmt = $conn->prepare($sql);
   $stmt->execute();

   if($stmt->rowCount() >= 1){
         $data = $stmt->fetchAll();
         return $data;
   }else {
       return 0;
   }
}



function getUserByID($conn, $id){
   $sql = "SELECT id, fname, username FROM users WHERE id=?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$id]);

   if($stmt->rowCount() >= 1){
         $data = $stmt->fetch();
         return $data;
   }else {
       return 0;
   }
}

// getAllCategories
function getAllCategories($conn){
   $sql = "SELECT * FROM category ORDER BY category";
   $stmt = $conn->prepare($sql);
   $stmt->execute();

   if($stmt->rowCount() >= 1){
         $data = $stmt->fetchAll();
         return $data;
   }else {
       return 0;
   }
}

// Delete By ID
function deleteById($conn, $id){
   $sql = "DELETE FROM post WHERE post_id=?";
   $stmt = $conn->prepare($sql);
   $res = $stmt->execute([$id]);

   if($res){
   	   return 1;
   }else {
   	 return 0;
   }
}
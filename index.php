<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloga Ieraksti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .post {
            border-bottom: 1px solid #ddd;
            padding: 20px 0;
        }
        h2 {
            color: #2c3e50;
        }
        .author, .created_at {
            color: #7f8c8d;
        }
        .content {
            margin-top: 10px;
            font-size: 1.1em;
            line-height: 1.5;
            color: #34495e;
        }
        .comments {
            margin-top: 20px;
        }
        .comments ul {
            list-style-type: none;
            padding: 0;
        }
        .comments li {
            background-color: #ecf0f1;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .comments li strong {
            color: #2980b9;
        }
        .no-comments {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bloga Ieraksti</h1>

<?php
// Konfigurācijas dati datubāzes savienojumam
$servername = "localhost";
$username = "bebra";  // mainiet, ja nepieciešams
$password = "password";      // mainiet, ja nepieciešams
$dbname = "blog_12032025";

try {
    // Izveidot savienojumu, izmantojot PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Iestatīt PDO, lai izsistītu izņēmumus pie kļūdām
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL vaicājums, lai iegūtu visus ierakstus no posts tabulas ar pievienotiem komentāriem
    $sql = "SELECT posts.post_id, posts.title, posts.author, posts.content, posts.created_at,
                   comments.comment_id, comments.author AS comment_author, comments.comment_text
            FROM posts
            LEFT JOIN comments ON posts.post_id = comments.post_id
            ORDER BY posts.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Pārbaudīt, vai ir kādi ieraksti
    if ($stmt->rowCount() > 0) {
        $posts = [];
        
        // Iegūstam visus ierakstus un apkopojam komentārus
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $post_id = $row['post_id'];
            if (!isset($posts[$post_id])) {
                $posts[$post_id] = [
                    'title' => $row['title'],
                    'author' => $row['author'],
                    'content' => $row['content'],
                    'created_at' => $row['created_at'],
                    'comments' => []
                ];
            }

            if ($row['comment_id']) {
                $posts[$post_id]['comments'][] = [
                    'author' => $row['comment_author'],
                    'comment_text' => $row['comment_text']
                ];
            }
        }

        // Attēlojam visus ierakstus
        foreach ($posts as $post) {
            echo '<div class="post">';
            echo "<h2>" . htmlspecialchars($post["title"]) . "</h2>";
            echo "<p class='author'><strong>Autors:</strong> " . htmlspecialchars($post["author"]) . "</p>";
            echo "<p class='created_at'><strong>Izveidots:</strong> " . $post["created_at"] . "</p>";
            echo "<div class='content'>" . nl2br(htmlspecialchars($post["content"])) . "</div>";

            // Pievienot komentārus šim ierakstam
            echo "<div class='comments'>";
            echo "<h3>Komentāri:</h3>";

            if (count($post['comments']) > 0) {
                echo "<ul>";
                foreach ($post['comments'] as $comment) {
                    echo "<li><strong>" . htmlspecialchars($comment['author']) . ":</strong> " . htmlspecialchars($comment['comment_text']) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p class='no-comments'>Nav komentāru.</p>";
            }

            echo "</div>"; // Beidzam komentāru daļu
            echo "</div>"; // Beidzam raksta daļu
        }
    } else {
        echo "<p>Nav ierakstu.</p>";
    }
}
catch(PDOException $e) {
    echo "Kļūda: " . $e->getMessage();
}

// Aizvērt savienojumu
$conn = null;
?>

    </div>
</body>
</html>

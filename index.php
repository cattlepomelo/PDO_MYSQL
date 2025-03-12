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
}
catch(PDOException $e) {
    echo "Kļūda: " . $e->getMessage();
    die(); // Ja savienojums neizdodas, izbeigt izpildi
}

try {
    // SQL vaicājums, lai iegūtu visus ierakstus no posts tabulas
    $sql = "SELECT * FROM posts";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Pārbaudīt, vai ir kādi ieraksti
    if ($stmt->rowCount() > 0) {
        // Iegūstam visus ierakstus un izvadām tos
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="post">';
            echo "<h2>" . htmlspecialchars($row["title"]) . "</h2>";
            echo "<p class='author'><strong>Autors:</strong> " . htmlspecialchars($row["author"]) . "</p>";
            echo "<p class='created_at'><strong>Izveidots:</strong> " . $row["created_at"] . "</p>";
            echo "<div class='content'>" . nl2br(htmlspecialchars($row["content"])) . "</div>";

            // Pievienot komentārus šim ierakstam
            echo "<div class='comments'>";
            echo "<h3>Komentāri:</h3>";

            // SQL vaicājums, lai iegūtu komentārus attiecībā uz konkrēto post_id
            $post_id = $row["post_id"];
            $comment_sql = "SELECT * FROM comments WHERE post_id = :post_id";
            $comment_stmt = $conn->prepare($comment_sql);
            $comment_stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $comment_stmt->execute();

            if ($comment_stmt->rowCount() > 0) {
                // Ja ir komentāri, attēlojam tos kā ul->li sarakstu
                echo "<ul>";
                while ($comment = $comment_stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<li><strong>" . htmlspecialchars($comment["author"]) . ":</strong> " . htmlspecialchars($comment["comment_text"]) . "</li>";
                }
                echo "</ul>";
            } else {
                // Ja nav komentāru, izvadām ziņu
                echo "<p class='no-comments'>Nav komentāru.</p>";
            }
            echo "</div>"; // Beidzam komentāru daļu
            echo "</div>"; // Beidzam raksta daļu
        }
    } else {
        echo "<p>Nav ierakstu.</p>";
    }
} catch (PDOException $e) {
    echo "Kļūda: " . $e->getMessage();
}

// Aizvērt savienojumu
$conn = null;
?>

    </div>
</body>
</html>

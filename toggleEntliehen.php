<?php

include 'dbconfig.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $conn = new mysqli(SERVER_NAME, USER_NAME, USER_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        die('{"status":"ERROR", "message":"Connection failed: ' . $conn->connect_error . '"}');
    }
    $conn->set_charset("utf8mb4");
    $stmt = $conn->prepare("UPDATE buch SET Entliehen = !Entliehen WHERE ID=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt = $conn->prepare("SELECT Entliehen FROM buch WHERE ID=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt->bind_result($rentliehen);
    $stmt->fetch();

    echo '{"status":"OK", "entliehen":"' . $rentliehen . '"}';
    $conn->close();
} else {
    echo '{"status":"ERROR", "message":"No id given"}';
}
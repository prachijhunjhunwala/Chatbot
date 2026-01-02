<?php
include "db_connect.php";

$result = $conn->query("SELECT * FROM chat_history ORDER BY created_at DESC LIMIT 10");

$chats = [];
while ($row = $result->fetch_assoc()) {
    $chats[] = $row;
}

echo json_encode($chats);

$conn->close();
?>

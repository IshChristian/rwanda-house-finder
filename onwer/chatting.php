<?php
session_start();
require '../config/connection.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$user_id = $_SESSION['user_id'];

// Get recent conversations
$sql = "SELECT u.id, u.name, u.email, MAX(m.message) AS last_message, MAX(m.created_at) AS last_time, 
               SUM(CASE WHEN m.receiver_id = ? AND m.is_read = 0 THEN 1 ELSE 0 END) AS unread_count
        FROM messages m
        JOIN users u ON (m.sender_id = u.id OR m.receiver_id = u.id) AND u.id != ?
        WHERE m.sender_id = ? OR m.receiver_id = ?
        GROUP BY u.id, u.name, u.email
        ORDER BY last_time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Messages</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-6">Chat Messages</h1>
        <div class="bg-white shadow-md rounded-lg p-4">
            <?php if (empty($messages)): ?>
                <p class="text-gray-500">No messages yet. Start a conversation!</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($messages as $chat): ?>
                        <li class="border-b py-3 flex justify-between items-center">
                            <a href="message.php?receiver_id=<?= $chat['id'] ?>" class="flex items-center w-full">
                                <div class="flex-1">
                                    <h2 class="text-lg font-semibold"><?= htmlspecialchars($chat['name']) ?></h2>
                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($chat['last_message']) ?></p>
                                </div>
                                <?php if ($chat['unread_count'] > 0): ?>
                                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                        <?= $chat['unread_count'] ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

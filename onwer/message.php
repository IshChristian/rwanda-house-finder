<?php
session_start();
require '../config/connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location='../login.php'</script>";
}

$user_id = $_SESSION['user_id'];
$receiver_id = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;

if (!$receiver_id) {
    die("Invalid user.");
}

// Fetch receiver info
$userQuery = "SELECT name FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$userResult = $stmt->get_result();
$receiver = $userResult->fetch_assoc();

if (!$receiver) {
    die("User not found.");
}

// Fetch messages
$messageQuery = "SELECT * FROM messages 
                 WHERE (sender_id = ? AND receiver_id = ?) 
                    OR (sender_id = ? AND receiver_id = ?) 
                 ORDER BY created_at ASC";
$stmt = $conn->prepare($messageQuery);
$stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);
$stmt->execute();
$messageResult = $stmt->get_result();
$messages = $messageResult->fetch_all(MYSQLI_ASSOC);

// Mark messages as read
$updateQuery = "UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param("ii", $receiver_id, $user_id);
$stmt->execute();

// Handle sending a new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $insertQuery = "INSERT INTO messages (sender_id, receiver_id, message, is_read) VALUES (?, ?, ?, 0)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("iis", $user_id, $receiver_id, $message);
        $stmt->execute();
        header("Location: message.php?receiver_id=$receiver_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?= htmlspecialchars($receiver['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <?php include './include/navbar.php' ?>
    <div class="container p-4">
        <div class=" bg-white rounded-lg shadow-md p-6">
            <h1 class="text-xl font-bold mb-4">Chat with <?= htmlspecialchars($receiver['name']) ?></h1>
            <div id="chat-window" class="h-96 overflow-y-auto border border-gray-200 p-4 rounded-lg">
                <?php if (empty($messages)): ?>
                    <p class="text-gray-500 text-center">No messages yet. Start the conversation!</p>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="mb-2 text-sm p-2 rounded-lg <?= $msg['sender_id'] == $user_id ? 'bg-blue-100 ml-auto' : 'bg-gray-200 mr-auto' ?>" style="max-width: 75%;">
                            <?= htmlspecialchars($msg['message']) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <form id="chat-form" method="POST" class="mt-4 flex">
                <input type="text" name="message" id="message-input" class="flex-1 p-2 border border-gray-300 rounded-l-lg" placeholder="Type a message..." required>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-lg">Send</button>
            </form>
        </div>
    </div>
    <?php include './include/footer.php' ?>

    <script>
        // Real-time message fetching
        const chatWindow = document.getElementById('chat-window');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');

        // Function to fetch new messages
        const fetchMessages = async () => {
            const response = await fetch(`message.php?receiver_id=<?= $receiver_id ?>`);
            const data = await response.json();
            if (data.error) {
                alert(data.error);
                return;
            }
            chatWindow.innerHTML = data.messages.map(msg => `
                <div class="mb-2 text-sm p-2 rounded-lg ${msg.sender_id == <?= $user_id ?> ? 'bg-blue-100 ml-auto' : 'bg-gray-200 mr-auto'}" style="max-width: 75%;">
                    ${msg.message}
                </div>
            `).join('');
            chatWindow.scrollTop = chatWindow.scrollHeight; // Auto-scroll to latest message
        };

        // Send message via AJAX
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(chatForm);
            const response = await fetch('message.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                messageInput.value = ''; // Clear input
                fetchMessages(); // Refresh messages
            }
        });

        // Poll for new messages every 2 seconds
        setInterval(fetchMessages, 2000);
    </script>
</body>
</html>

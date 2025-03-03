<?php
include '../config/connection.php'; // Include your database connection file

// Fetch user's name from the database
$username = "Guest"; // Default name
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT name FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
}
// Handle Change Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Fetch current password from the database
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    // Verify current password
    if (password_verify($current_password, $hashed_password)) {
        // Update password
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $new_hashed_password, $user_id);
        $update_stmt->execute();

        $password_change_message = "Password updated successfully!";
    } else {
        $password_change_message = "Current password is incorrect!";
    }
}

// Handle Delete Account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    // Delete user account
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Destroy session and redirect
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LiveAtRwanda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <!-- Header -->
    <div class="w-full bg-white shadow-md fixed top-0 left-0 right-0 z-10">
        <div class="navbar px-4 lg:px-10 flex justify-between items-center h-16">
            <!-- Left: Project Name -->
            <div class="text-2xl font-bold text-gray-800 antialiased">
                LiveAtRwanda
            </div>

            <!-- Right: Hello, Username and Settings Dropdown -->
            <div class="flex items-center space-x-4">
                <!-- Hello, Username -->
                <div class="text-gray-600">
                    Hello, <?php echo htmlspecialchars($username); ?>
                </div>

                <!-- Settings Dropdown -->
                <div class="relative group">
                    <button class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-cog text-lg"></i> <!-- Settings Icon -->
                    </button>
                    <!-- Dropdown Content -->
                    <ul class="absolute hidden group-hover:block bg-white shadow-lg rounded-md w-48 right-0 mt-2">
                        <li>
                            <a href="#" class="block px-4 py-2 hover:bg-gray-200" data-modal-target="change-password-modal">
                                Change Password
                            </a>
                        </li>
                        <li>
                            <a href="#" class="block px-4 py-2 hover:bg-gray-200 text-red-600" data-modal-target="delete-account-modal">
                                Delete Account
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="change-password-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-xl font-bold mb-4">Change Password</h2>
            <?php if (isset($password_change_message)): ?>
                <div class="mb-4 text-sm text-green-600">
                    <?php echo $password_change_message; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <div class="mb-4">
                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md mr-2" data-modal-close="change-password-modal">Cancel</button>
                    <button type="submit" name="change_password" class="bg-blue-600 text-white px-4 py-2 rounded-md">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div id="delete-account-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-xl font-bold mb-4">Delete Account</h2>
            <p class="mb-4">Are you sure you want to delete your account? This action cannot be undone.</p>
            <form method="POST" action="">
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md mr-2" data-modal-close="delete-account-modal">Cancel</button>
                    <button type="submit" name="delete_account" class="bg-red-600 text-white px-4 py-2 rounded-md">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Open modal
            document.querySelectorAll('[data-modal-target]').forEach(button => {
                button.addEventListener('click', () => {
                    const target = button.getAttribute('data-modal-target');
                    document.getElementById(target).classList.remove('hidden');
                });
            });

            // Close modal
            document.querySelectorAll('[data-modal-close]').forEach(button => {
                button.addEventListener('click', () => {
                    const target = button.getAttribute('data-modal-close');
                    document.getElementById(target).classList.add('hidden');
                });
            });
        });
    </script>
</body>
</html>
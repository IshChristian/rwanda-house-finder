<?php
session_start();
require '../config/connection.php'; // Include database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$user_id = $_SESSION['user_id'];

// Fetch bookings data where user is an owner
$query = "SELECT bookings.id, bookings.check_in_date, bookings.check_out_date, bookings.status, 
                 properties.title, users.name AS renter_name
          FROM bookings
          JOIN properties ON bookings.property_id = properties.id
          JOIN users ON bookings.user_id = users.id
          WHERE properties.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</head>
<body class="bg-white">
    <!-- Navbar -->
    <header class="bg-white shadow-md p-4 mb-6">
        <?php include './include/navbar.php'; ?>
    </header>
    <div class="container mx-auto flex">
        <!-- Sidebar -->
        <aside class="w-1/4  p-6">
            <?php include './include/sidebar.php'; ?>
        </aside>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Booking Management</h1>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Property</th>
                        <th class="px-4 py-2">Check-in</th>
                        <th class="px-4 py-2">Check-out</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Renter</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"> <?= htmlspecialchars($booking['title']) ?> </td>
                        <td class="px-4 py-2"> <?= htmlspecialchars($booking['start_date']) ?> </td>
                        <td class="px-4 py-2"> <?= htmlspecialchars($booking['end_date']) ?> </td>
                        <td class="px-4 py-2"> <?= htmlspecialchars($booking['status']) ?> </td>
                        <td class="px-4 py-2"> <?= htmlspecialchars($booking['renter_name']) ?> </td>
                        <td class="px-4 py-2">
                            <button class="px-2 py-1 bg-blue-500 text-white rounded-md" onclick="viewDetails(<?= $booking['id'] ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Modal for Viewing Booking Details -->
        <div id="modal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
                <h2 class="text-xl font-bold mb-4">Booking Details</h2>
                <div id="modal-content" class="mb-4"></div>
                <div class="flex justify-end space-x-2">
                    <button onclick="updateStatus('accepted')" class="px-4 py-2 bg-green-500 text-white rounded-md">Accept</button>
                    <button onclick="updateStatus('canceled')" class="px-4 py-2 bg-red-500 text-white rounded-md">Cancel</button>
                    <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewDetails(bookingId) {
            fetch(`booking_details.php?id=${bookingId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('modal-content').innerHTML = data;
                    document.getElementById('modal').classList.remove('hidden');
                });
        }
        
        function updateStatus(status) {
            const bookingId = document.getElementById('modal-content').dataset.bookingId;
            fetch(`update_booking.php?id=${bookingId}&status=${status}`, { method: 'POST' })
                .then(() => location.reload());
        }
        
        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }
    </script>
</body>
</html>

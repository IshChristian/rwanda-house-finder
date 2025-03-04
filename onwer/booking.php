<?php
session_start();
include '../config/connection.php'; // Database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$user_id = $_SESSION['user_id'];
$selected_booking = null;
$show_delete_modal = false;

// Fetch bookings where user is the property owner
$query = "SELECT bookings.id, bookings.check_in_date, bookings.check_out_date, bookings.status, 
                 properties.title, properties.id AS property_id, users.name AS renter_name, 
                 users.email AS renter_email, users.phone AS renter_phone
          FROM bookings
          JOIN properties ON bookings.property_id = properties.id
          JOIN users ON bookings.user_id = users.id
          WHERE properties.user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $property_id = intval($_POST['property_id']);
    $new_status = ($_POST['current_status'] === "accepted") ? "canceled" : "accepted";

    // Update booking status
    $update_query = "UPDATE bookings SET status = '$new_status' WHERE id = '$booking_id'";
    mysqli_query($conn, $update_query);
    
    // Update property status
    if ($new_status === "accepted") {
        // If booking is accepted, update property status to "booked"
        $property_update_query = "UPDATE properties SET status = 'booked' WHERE id = '$property_id'";
    } else {
        // If booking is canceled, update property status to "available"
        $property_update_query = "UPDATE properties SET status = 'available' WHERE id = '$property_id'";
    }
    mysqli_query($conn, $property_update_query);
    
    header("Location: booking.php"); // Refresh the page to reflect changes
    exit();
}

// Handle delete booking
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_booking'])) {
    $booking_id = intval($_POST['booking_id']);
    $property_id = intval($_POST['property_id']);
    
    // Delete the booking
    $delete_query = "DELETE FROM bookings WHERE id = '$booking_id'";
    mysqli_query($conn, $delete_query);
    
    // Update property status to available
    $property_update_query = "UPDATE properties SET status = 'available' WHERE id = '$property_id'";
    mysqli_query($conn, $property_update_query);
    
    header("Location: booking.php"); // Refresh the page to reflect changes
    exit();
}

// Load booking details if ID is selected
if (isset($_GET['view'])) {
    $booking_id = intval($_GET['view']);
    $details_query = "SELECT bookings.*, properties.title, properties.id AS property_id, users.name AS renter_name, 
                             users.email AS renter_email, users.phone AS renter_phone
                      FROM bookings
                      JOIN properties ON bookings.property_id = properties.id
                      JOIN users ON bookings.user_id = users.id
                      WHERE bookings.id = '$booking_id'";
    $details_result = mysqli_query($conn, $details_query);
    $selected_booking = mysqli_fetch_assoc($details_result);
}

// Show delete confirmation modal
if (isset($_GET['delete'])) {
    $booking_id = intval($_GET['delete']);
    $details_query = "SELECT bookings.*, properties.title, properties.id AS property_id
                      FROM bookings
                      JOIN properties ON bookings.property_id = properties.id
                      WHERE bookings.id = '$booking_id'";
    $details_result = mysqli_query($conn, $details_query);
    $selected_booking = mysqli_fetch_assoc($details_result);
    $show_delete_modal = true;
}
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
        <aside class="w-1/4 p-6">
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
                            <td class="px-4 py-2"> <?= htmlspecialchars($booking['check_in_date']) ?> </td>
                            <td class="px-4 py-2"> <?= htmlspecialchars($booking['check_out_date']) ?> </td>
                            <td class="px-4 py-2"> <?= htmlspecialchars($booking['status']) ?> </td>
                            <td class="px-4 py-2"> <?= htmlspecialchars($booking['renter_name']) ?> </td>
                            <td class="px-4 py-2 flex space-x-2">
                                <a href="?view=<?= $booking['id'] ?>" class="px-2 py-1 bg-blue-500 text-white rounded-md">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="?delete=<?= $booking['id'] ?>" class="px-2 py-1 bg-red-500 text-white rounded-md">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Booking Details Modal -->
            <?php if ($selected_booking && !$show_delete_modal): ?>
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
                    <h2 class="text-xl font-bold mb-4">Booking Details</h2>
                    <p><strong>Property:</strong> <?= htmlspecialchars($selected_booking['title']) ?></p>
                    <p><strong>Check-in:</strong> <?= htmlspecialchars($selected_booking['check_in_date']) ?></p>
                    <p><strong>Check-out:</strong> <?= htmlspecialchars($selected_booking['check_out_date']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($selected_booking['status']) ?></p>
                    <p><strong>Renter Name:</strong> <?= htmlspecialchars($selected_booking['renter_name']) ?></p>
                    <p><strong>Renter Email:</strong> <?= htmlspecialchars($selected_booking['renter_email']) ?></p>
                    <p><strong>Renter Phone:</strong> <?= htmlspecialchars($selected_booking['renter_phone']) ?></p>
                    
                    <div class="flex justify-between mt-6">
                        <!-- Status Update Form -->
                        <form method="POST" class="mt-4">
                            <input type="hidden" name="booking_id" value="<?= $selected_booking['id'] ?>">
                            <input type="hidden" name="property_id" value="<?= $selected_booking['property_id'] ?>">
                            <input type="hidden" name="current_status" value="<?= $selected_booking['status'] ?>">
                            <?php if ($selected_booking['status'] === "accepted"): ?>
                                <button type="submit" name="update_status" class="px-4 py-2 bg-red-500 text-white rounded-md">Cancel Booking</button>
                            <?php else: ?>
                                <button type="submit" name="update_status" class="px-4 py-2 bg-green-500 text-white rounded-md">Accept Booking</button>
                            <?php endif; ?>
                        </form>
                        
                        <!-- Delete Button -->
                        <a href="?delete=<?= $selected_booking['id'] ?>" class="px-4 py-2 bg-red-500 text-white rounded-md mt-4">
                            <i class="fas fa-trash"></i> Delete Booking
                        </a>
                    </div>
                    
                    <!-- Close Button -->
                    <a href="booking.php" class="block mt-4 text-center px-4 py-2 border border-gray-300 rounded-md">Close</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Delete Confirmation Modal -->
            <?php if ($show_delete_modal): ?>
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                    <h2 class="text-xl font-bold mb-4 text-red-600">Confirm Deletion</h2>
                    <p>Are you sure you want to delete the booking for:</p>
                    <p class="font-bold mt-2"><?= htmlspecialchars($selected_booking['title']) ?></p>
                    <p>Check-in: <?= htmlspecialchars($selected_booking['check_in_date']) ?></p>
                    <p>Check-out: <?= htmlspecialchars($selected_booking['check_out_date']) ?></p>
                    <p class="text-red-500 mt-4">This action cannot be undone.</p>
                    
                    <div class="flex justify-between mt-6">
                        <!-- Delete Form -->
                        <form method="POST">
                            <input type="hidden" name="booking_id" value="<?= $selected_booking['id'] ?>">
                            <input type="hidden" name="property_id" value="<?= $selected_booking['property_id'] ?>">
                            <button type="submit" name="delete_booking" class="px-4 py-2 bg-red-500 text-white rounded-md">
                                Yes, Delete Booking
                            </button>
                        </form>
                        
                        <!-- Cancel Button -->
                        <a href="booking.php" class="px-4 py-2 bg-gray-500 text-white rounded-md">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
session_start();
include '../config/connection.php'; // Include database connection

$_SESSION['user_id'] = '1'; // You can set the user_id based on the actual session data

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get user ID from session

// Set default date range to the current month
$start_date = date('Y-m-01');
$end_date = date('Y-m-t');

// Check for date range filter
if (isset($_POST['filter'])) {
    // Custom date range filter
    if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
    }
}

// Fetch data based on the selected date range
$bookings_query = "SELECT * FROM bookings WHERE user_id = $user_id AND created_at BETWEEN '$start_date' AND '$end_date'";
$properties_query = "SELECT * FROM properties WHERE user_id = $user_id";
$reviews_query = "SELECT * FROM reviews WHERE user_id = $user_id AND created_at BETWEEN '$start_date' AND '$end_date'";
$payments_query = "SELECT * FROM payments WHERE user_id = $user_id AND createAt BETWEEN '$start_date' AND '$end_date'";

// Execute queries and fetch data into arrays
$bookings = [];
$properties = [];
$reviews = [];
$payments = [];

// Fetch bookings
$bookings_result = $conn->query($bookings_query);
if ($bookings_result && $bookings_result->num_rows > 0) {
    while ($row = $bookings_result->fetch_assoc()) {
        $bookings[] = $row;
    }
    $bookings_result->free(); // Free the result set
}

// Fetch properties
$properties_result = $conn->query($properties_query);
if ($properties_result && $properties_result->num_rows > 0) {
    while ($row = $properties_result->fetch_assoc()) {
        $properties[] = $row;
    }
    $properties_result->free(); // Free the result set
}

// Fetch reviews
$reviews_result = $conn->query($reviews_query);
if ($reviews_result && $reviews_result->num_rows > 0) {
    while ($row = $reviews_result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $reviews_result->free(); // Free the result set
}

// Fetch payments
$payments_result = $conn->query($payments_query);
if ($payments_result && $payments_result->num_rows > 0) {
    while ($row = $payments_result->fetch_assoc()) {
        $payments[] = $row;
    }
    $payments_result->free(); // Free the result set
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyse Data</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <header class="bg-white shadow-md p-4 mb-6">
        <?php include './include/navbar.php'; ?>
    </header>
    <div class="container mx-auto flex">
        <!-- Sidebar -->
        <aside class="w-1/4 p-6">
            <?php include './include/sidebar.php'; ?>
        </aside>
        <div class="container mx-auto p-5">
            <h1 class="text-3xl font-semibold text-center">Data Analysis</h1>

            <!-- Date Filter Form -->
            <form method="POST" action='analyse.php' class="my-5">
                <div class="flex justify-center space-x-4">
                    <div class="flex space-x-2">
                        <input type="date" name="start_date" class="px-4 py-2 border border-gray-300 rounded-md" value="<?php echo isset($start_date) ? $start_date : ''; ?>" required>
                        <input type="date" name="end_date" class="px-4 py-2 border border-gray-300 rounded-md" value="<?php echo isset($end_date) ? $end_date : ''; ?>" required>
                    </div>
                    <button type="submit" name="filter" class="px-4 py-2 bg-blue-500 text-white rounded-md">Filter</button>
                </div>
            </form>

            <!-- Bookings Table -->
            <h2 class="text-2xl font-semibold">Bookings</h2>
            <table class="min-w-full bg-white border border-gray-300 mt-4">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border">Booking ID</th>
                        <th class="px-4 py-2 border">Property</th>
                        <th class="px-4 py-2 border">Booking Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?php echo $booking['id']; ?></td>
                            <td class="px-4 py-2 border"><?php echo $booking['property_id']; ?></td>
                            <td class="px-4 py-2 border"><?php echo $booking['booking_date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Properties Table -->
            <h2 class="text-2xl font-semibold mt-8">Properties</h2>
            <table class="min-w-full bg-white border border-gray-300 mt-4">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border">Property ID</th>
                        <th class="px-4 py-2 border">Property Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $property): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?php echo $property['id']; ?></td>
                            <td class="px-4 py-2 border"><?php echo $property['title']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Reviews Table -->
            <h2 class="text-2xl font-semibold mt-8">Reviews</h2>
            <table class="min-w-full bg-white border border-gray-300 mt-4">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border">Review ID</th>
                        <th class="px-4 py-2 border">Review Date</th>
                        <th class="px-4 py-2 border">Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?php echo $review['id']; ?></td>
                            <td class="px-4 py-2 border"><?php echo $review['review_date']; ?></td>
                            <td class="px-4 py-2 border"><?php echo $review['rating']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Payments Table -->
            <h2 class="text-2xl font-semibold mt-8">Payments</h2>
            <table class="min-w-full bg-white border border-gray-300 mt-4">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border">Payment ID</th>
                        <th class="px-4 py-2 border">Amount</th>
                        <th class="px-4 py-2 border">Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?php echo $payment['id']; ?></td>
                            <td class="px-4 py-2 border"><?php echo $payment['amount']; ?></td>
                            <td class="px-4 py-2 border"><?php echo $payment['payment_date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
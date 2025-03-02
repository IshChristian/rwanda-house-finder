<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Database connection
include '../config/connection.php';

// Get all bookings for the current user
$bookings = [];
$sql = "SELECT b.id as _id, b.property_id as property, p.title as propertyName, 
               p.address, p.sector, p.village, p.price, 
               b.check_in_date as startDate, b.check_out_date as endDate, b.status
        FROM bookings b
        JOIN properties p ON b.property_id = p.id
        WHERE b.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Format the data to match the React component structure
    $booking = [
        '_id' => $row['_id'],
        'property' => $row['property'],
        'propertyName' => $row['propertyName'],
        'location' => [
            'address' => $row['address'],
            'city' => $row['sector'],
            'state' => $row['village']
        ],
        'price' => $row['price'],
        'startDate' => $row['startDate'],
        'endDate' => $row['endDate'],
        'status' => $row['status']
    ];
    $bookings[] = $booking;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <?php include './include/navbar.php' ?>
    <div class="container mx-auto p-4 space-y-4">
        <h1 class="text-2xl font-bold mb-6">My Bookings</h1>
        <div class="space-y-4">
            <?php foreach ($bookings as $booking): ?>
            <div 
                class="w-full rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-lg transition-shadow cursor-pointer"
                onclick="window.location.href='payment.php?booking_id=<?php echo $booking['_id']; ?>&property_id=<?php echo $booking['property']; ?>'"
            >
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($booking['propertyName']); ?></h3>
                            <p class="flex items-center mt-1 text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt h-4 w-4 mr-1"></i>
                                <?php 
                                if (isset($booking['location'])) {
                                    echo htmlspecialchars($booking['location']['address'] . ', ' . 
                                                        $booking['location']['city'] . ', ' . 
                                                        $booking['location']['state']);
                                } else {
                                    echo "Location not available";
                                }
                                ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-gray-900">
                                $<?php echo isset($booking['price']) ? number_format($booking['price'], 2) : 'N/A'; ?>
                            </p>
                            <p class="text-sm text-gray-500">per night</p>
                        </div>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <div class="space-y-1">
                            <p class="text-sm text-gray-500">Check-in</p>
                            <p class="font-medium text-gray-900">
                                <?php echo date('m/d/Y', strtotime($booking['startDate'])); ?>
                            </p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-500">Check-out</p>
                            <p class="font-medium text-gray-900">
                                <?php echo date('m/d/Y', strtotime($booking['endDate'])); ?>
                            </p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-500">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <?php echo htmlspecialchars($booking['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php include './include/footer.php' ?>
</body>
</html>
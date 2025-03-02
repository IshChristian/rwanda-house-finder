<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if booking_id is provided
if (!isset($_GET['booking_id'])) {
    header("Location: booking.php");
    exit();
}

$booking_id = $_GET['booking_id'];
$user_id = $_SESSION['user_id'];

// Database connection
include '../config/connection.php';

// Get booking details - ensure it belongs to the logged-in user for security
$sql = "SELECT b.id as booking_id, b.property_id, b.user_id, p.title as property_name, 
               p.address, p.sector, p.village, p.price, 
               b.check_in_date, b.check_out_date, b.status,
               DATEDIFF(b.check_in_date, b.check_out_date) as nights,
               u.phone
        FROM bookings b
        JOIN properties p ON b.property_id = p.id
        JOIN users u ON b.user_id = u.id
        WHERE b.id = ? AND b.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Booking not found or doesn't belong to this user
    header("Location: booking.php");
    exit();
}

$booking = $result->fetch_assoc();
$total_price = $booking['price'] * $booking['nights'];
$service_fee = $total_price * 0.1;
$cleaning_fee = 50.00;
$final_total = $total_price + $service_fee + $cleaning_fee;

// Check if payment already exists for this booking
$payment_sql = "SELECT * FROM payments WHERE user_id = ? AND booking_id = ? AND status != 'failed'";
$payment_stmt = $conn->prepare($payment_sql);
$payment_stmt->bind_param("ii", $user_id, $booking['booking_id']);
$payment_stmt->execute();
$payment_result = $payment_stmt->get_result();
$existing_payment = $payment_result->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - <?php echo htmlspecialchars($booking['property_name']); ?></title>
    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto p-4">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6">Payment Details</h1>
            
            <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($booking['property_name']); ?></h2>
                <p class="flex items-center text-gray-600 mb-2">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <?php echo htmlspecialchars($booking['address'] . ', ' . $booking['sector'] . ', ' . $booking['village']); ?>
                </p>
                
                <div class="flex justify-between border-t border-gray-200 pt-4 mt-4">
                    <div>
                        <p class="text-sm text-gray-500">Check-in</p>
                        <p class="font-medium"><?php echo date('m/d/Y', strtotime($booking['check_in_date'])); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Check-out</p>
                        <p class="font-medium"><?php echo date('m/d/Y', strtotime($booking['check_out_date'])); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nights</p>
                        <p class="font-medium"><?php echo $booking['nights']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-3">Price Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>$<?php echo number_format($booking['price'], 2); ?> × <?php echo $booking['nights']; ?> nights</span>
                        <span>$<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Cleaning fee</span>
                        <span>$<?php echo number_format($cleaning_fee, 2); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Service fee</span>
                        <span>$<?php echo number_format($service_fee, 2); ?></span>
                    </div>
                    <div class="flex justify-between font-bold pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span>$<?php echo number_format($final_total, 2); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if ($existing_payment): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <p>Payment has already been processed for this booking.</p>
                    </div>
                </div>
            <?php else: ?>
                <form action="payment.php?booking_id=<?php echo $booking_id ?>" method="post" class="space-y-4">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="property_id" value="<?php echo $booking['property_id']; ?>">
                    <input type="hidden" name="amount" value="<?php echo $final_total; ?>">
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <div class="relative">
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($booking['phone'] ?? ''); ?>" placeholder="(123) 456-7890" class="w-full p-2 pl-10 border border-gray-300 rounded-md">
                            <span class="absolute left-3 top-2.5 text-gray-400">
                                <i class="fas fa-phone"></i>
                            </span>
                        </div>
                    </div>
                    
                    
                    
                    <button type="submit" name="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 font-medium">
                        <i class="fas fa-lock mr-2"></i> Confirm and Pay
                    </button>
                </form>
            <?php endif; ?>
            
            <div class="mt-4 text-center text-sm text-gray-500">
                <p><i class="fas fa-shield-alt mr-1"></i> Your payment information is secure and encrypted</p>
            </div>
        </div>
    </div>
</body>
</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float) $_POST['amount'];
    $phone = $_POST['phone'];
    
    if ($user_id != $_SESSION['user_id']) {
        $_SESSION['payment_error'] = "Invalid user information";
        header("Location: booking.php");
        exit();
    }
    
    // Insert payment data
    $insert_sql = "INSERT INTO payments (user_id, booking_id, amount, phone, status) VALUES ($user_id, $booking_id, $amount, '$phone', 'completed')";
    $insert_result = mysqli_query($conn, $insert_sql);
    
    // if ($insert_result) {
    //     $update_phone_sql = "UPDATE users SET phone = '$phone' WHERE id = $user_id";
    //     mysql_query($update_phone_sql, $conn);
    //     $_SESSION['payment_success'] = "Payment processed successfully!";
    // } else {
    //     $_SESSION['payment_error'] = "Error processing payment. Please try again.";
    // }
    
    echo "<script>window.location='booking.php'</script>";
    exit();
}
?>

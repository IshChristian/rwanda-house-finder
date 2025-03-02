<?php
// Start session
session_start();

include '../config/connection.php';

// Get property ID from URL
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($property_id <= 0) {
    header("Location: properties.php");
    exit();
}

// Check if user is logged in
$user_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$user_id = $user_logged_in ? $_SESSION['user_id'] : 0;
// Check if user is logged in
// $user_logged_in = isset($_SESSION['user_id']);
// $user_id = $user_logged_in ? $_SESSION['user_id'] : null;
// $property_id = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch property details - CORRECTED to fetch all data in one query
$stmt = $conn->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: properties.php");
    exit();
}

$property = $result->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $property['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: properties.php");
    exit();
}

$onwer = $result->fetch_assoc();
$stmt->close();


// Parse JSON arrays for features and images - CORRECTED
$features = json_decode($property['features'], true) ?? [];
$images = json_decode($property['images'], true) ?? [];

// Fallback if no images
if (count($images) == 0) {
    $images[] = "https://via.placeholder.com/400x300?text=No+Image";
}

if(isset($_POST['messagebtn'])){
    header("Location: message.php?receiver_id=".$property['user_id']);
    exit();
}

// Fetch property reviews
$stmt = $conn->prepare("SELECT r.*, u.name, u.image 
                       FROM reviews r 
                       LEFT JOIN users u ON r.user_id = u.id 
                       WHERE r.property_id = ? 
                       ORDER BY r.created_at DESC");
$stmt->bind_param("i", $property_id);
$stmt->execute();
$reviews_result = $stmt->get_result();
$reviews = [];
while ($row = $reviews_result->fetch_assoc()) {
    $reviews[] = $row;
}
$stmt->close();

// Check if user has liked this property


// Initialize favorite status
$is_favorite = false;

if ($user_logged_in && $property_id) {
    // Check if a review record exists for this user and property
    $stmt = $conn->prepare("SELECT id, favorite FROM reviews WHERE user_id = ? AND property_id = ?");
    $stmt->bind_param("ii", $user_id, $property_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Record exists, get current favorite status
        $review = $result->fetch_assoc();
        $review_id = $review['id'];
        $is_favorite = (bool)$review['favorite'];
    }
    $stmt->close();
}

// Handle favorite toggle action
if (isset($_POST['toggle_favorite']) && $user_logged_in && $property_id) {
    if ($result->num_rows > 0) {
        // Record exists, update favorite status (toggle it)
        $new_favorite_status = $is_favorite ? 0 : 1;
        $stmt = $conn->prepare("UPDATE reviews SET favorite = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_favorite_status, $review_id);
        $stmt->execute();
        $stmt->close();
    } else {
        // No record exists, insert a new one with favorite set to true
        $favorite_status = 1;
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, property_id, favorite) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $property_id, $favorite_status);
        $stmt->execute();
        $stmt->close();
    }
    
    // Redirect to prevent form resubmission
    header("Location: propertyDetails.php?id=" . $property_id);
    exit();
}

// Handle review submission
if (isset($_POST['submit_review']) && $user_logged_in) {
    $rating = intval($_POST['rating']);
    $comment = $_POST['review'];
    
    if ($rating >= 1 && $rating <= 5) {
        // Check if user already submitted a review
        $stmt = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND property_id = ?");
        $stmt->bind_param("ii", $user_id, $property_id);
        $stmt->execute();
        $existing_review = $stmt->get_result();
        $stmt->close();
        
        if ($existing_review->num_rows > 0) {
            // Update existing review
            $review_id = $existing_review->fetch_assoc()['id'];
            $stmt = $conn->prepare("UPDATE reviews SET rating = ?, comment = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("isi", $rating, $comment, $review_id);
        } else {
            // Insert new review
            $stmt = $conn->prepare("INSERT INTO reviews (property_id, user_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("iiis", $property_id, $user_id, $rating, $comment);
        }
        
        $stmt->execute();
        $stmt->close();
        
        // Redirect to prevent form resubmission
        header("Location: propertyDetails.php?id=" . $property_id);
        exit();
    }
}

// Handle booking submission
if (isset($_POST['submit_booking']) && $user_logged_in) {
    $start_date = $_POST['start_date'];
    $is_monthly = isset($_POST['is_monthly']) ? 1 : 0;
    
    // Calculate end date - if monthly, add 30 days to start date
    if ($is_monthly) {
        $end_date = date('Y-m-d', strtotime($start_date . ' + 30 days'));
    } else {
        $end_date = $_POST['end_date'];
    }
    
    $rent_amount = $property['price'];
    
    // CORRECTED bind_param parameters to match query
    $stmt = $conn->prepare("INSERT INTO bookings (property_id, user_id, check_in_date, check_out_date, price, status, created_at) 
                           VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("iissd", $property_id, $user_id, $start_date, $end_date, $rent_amount);
    $stmt->execute();
    $booking_id = $stmt->insert_id;
    $stmt->close();
    
    // Redirect to booking confirmation page
    header("Location: booking.php?id=" . $booking_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }
        
        .modal-content {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 28rem;
            max-width: 90%;
            position: relative;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <?php include './include/navbar.php' ?>
    <div class="container mx-auto px-4 py-8">
        <!-- Collage Image Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-8/12">
                    <img
                        src="<?php echo htmlspecialchars($images[0]); ?>"
                        alt="Main Property Image"
                        class="rounded-lg object-cover w-full h-[400px] hover:opacity-90 transition-opacity duration-300"
                    />
                </div>
                <div class="w-full md:w-4/12 flex flex-row md:flex-col gap-4">
                    <?php for ($i = 1; $i < min(3, count($images)); $i++): ?>
                    <img
                        src="<?php echo htmlspecialchars($images[$i]); ?>"
                        alt="Property Image <?php echo $i + 1; ?>"
                        class="rounded-lg object-cover w-full h-[192px] hover:opacity-90 transition-opacity duration-300"
                    />
                    <?php endfor; ?>
                    
                    <?php if (count($images) == 1): ?>
                    <img
                        src="https://via.placeholder.com/400x300?text=No+Additional+Images"
                        alt="No additional images"
                        class="rounded-lg object-cover w-full h-[192px]"
                    />
                    <img
                        src="https://via.placeholder.com/400x300?text=No+Additional+Images"
                        alt="No additional images"
                        class="rounded-lg object-cover w-full h-[192px]"
                    />
                    <?php elseif (count($images) == 2): ?>
                    <img
                        src="https://via.placeholder.com/400x300?text=No+Additional+Images"
                        alt="No additional images"
                        class="rounded-lg object-cover w-full h-[192px]"
                    />
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- REVISED: Changed to two-column layout with property info left and booking right -->
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Side (Property Information) -->
            <div class="w-full lg:w-2/3 space-y-8">
                <div class="bg-white rounded-lg shadow-md p-6 relative">
                    <h1 class="text-3xl font-bold mb-2 flex items-center justify-between">
                        <?php echo htmlspecialchars($property['title']); ?>
                        
                        <!-- Like (Heart) Icon -->
                        <form method="post" class="inline">
                            <input type="hidden" name="toggle_like" value="1">
                            <button type="submit" class="text-3xl <?php echo $is_liked ? 'text-red-600' : 'text-gray-500'; ?>">
                                <i class="fas<?php echo $is_liked ? 's' : 'r'; ?> fa-heart"></i>
                            </button>
                        </form>
                    </h1>
                    
                    <p class="text-2xl text-indigo-600 font-semibold mb-2">
                        $<?php echo htmlspecialchars($property['price']); ?> / <?php echo htmlspecialchars($property['period']); ?>
                    </p>
                    
                    <p class="text-gray-600 mb-4">
                        <?php echo htmlspecialchars($property['address'] ?? 'Address not available'); ?>,
                        <?php echo htmlspecialchars($property['sector'] ?? 'Sector not available'); ?>,
                        <?php echo htmlspecialchars($property['village'] ?? 'Village not available'); ?>, 
                        Zipcode: <?php echo htmlspecialchars($property['zipcode'] ?? 'N/A'); ?>
                    </p>

                    <!-- Property Features -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-home mr-2 text-indigo-600"></i>
                            <span><?php echo htmlspecialchars($property['area'] ?? 'N/A'); ?> sqft</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-bed mr-2 text-indigo-600"></i>
                            <span><?php echo htmlspecialchars($property['bedrooms'] ?? '0'); ?> Bedrooms</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-bath mr-2 text-indigo-600"></i>
                            <span><?php echo htmlspecialchars($property['bathrooms'] ?? '0'); ?> Bathrooms</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-car mr-2 text-indigo-600"></i>
                            <span><?php echo htmlspecialchars($property['parking'] ?? '0'); ?> Parking</span>
                        </div>
                    </div>

                    <h2 class="text-xl font-semibold mb-2">Features</h2>
                    <div class="flex flex-wrap gap-2">
                        <?php if (count($features) > 0): ?>
                            <?php foreach ($features as $feature): ?>
                                <span class="bg-gray-200 px-3 py-1 rounded-full text-sm">
                                    <?php echo htmlspecialchars($feature); ?>
                                </span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-gray-500">No features listed.</span>
                        <?php endif; ?>
                    </div>
                    
                    <h2 class="text-xl font-semibold mt-6 mb-2">Description</h2>
                    <p class="text-gray-600">
                        <?php echo nl2br(htmlspecialchars($property['description'] ?? 'No description available.')); ?>
                    </p>
                    
                    <!-- Review and Rate Section -->
                    <div class="mt-8 text-center">
                        <h2 class="text-xl font-semibold mb-4">Rate this Property</h2>
                        <div class="flex justify-center space-x-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa<?php echo $i <= 3 ? 's' : 'r'; ?> fa-star text-yellow-400 cursor-pointer text-2xl" 
                                   data-rating="<?php echo $i; ?>" 
                                   onclick="setRating(<?php echo $i; ?>)"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <!-- Property Reviews -->
                    <div class="mt-8">
                        <h2 class="text-xl font-semibold mb-4">Property Reviews</h2>
                        
                        <?php if (count($reviews) > 0): ?>
                            <div class="space-y-6">
                                <?php foreach ($reviews as $review): ?>
                                    <div class="border-b pb-4 last:border-b-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center">
                                                <span class="font-medium mr-2">
                                                    <?php echo htmlspecialchars($review['name'] ?? 'Anonymous'); ?>
                                                </span>
                                                <div class="flex">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fa<?php echo $i <= $review['rating'] ? 's' : 'r'; ?> fa-star text-yellow-400 text-sm"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                            <span class="text-sm text-gray-500">
                                                <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                            </span>
                                        </div>
                                        <p class="text-gray-700"><?php echo htmlspecialchars($review['comment']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-gray-500 py-4">
                                <i class="far fa-comment-alt mx-auto text-3xl mb-2"></i>
                                <p>No reviews yet. Be the first to leave a review!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
                
            <!-- Right Side (Booking Panel) - MOVED OUTSIDE OF PROPERTY INFO -->
            <div class="w-full lg:w-1/3 space-y-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <form action="propertyDetails.php?id=<?php echo $property_id ?>" method="post">
                        <button onclick="openBookingModal()" class="w-full bg-indigo-600 text-white py-3 rounded-lg mb-4 hover:bg-indigo-700" name="messagebtn">
                            <i class="far fa-envelope mr-2"></i>
                            <a href="message.php?receiver_id=<?php echo $property['user_id'] ?>">Message Owner</a>
                        </button>
                    </form>
                    <button class="w-full bg-green-500 text-white py-3 rounded-lg hover:bg-green-600" 
                        id="bookNowBtn" onclick="openBookingModal()">
                        <i class="fas fa-book mr-2"></i>
                        Book Now
                    </button>
                </div>
                
                <!-- Property Agent Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Property Owner</h3>
                    <div class="flex items-center mb-4">
                        <div>
                            <p class="font-medium"> <?php echo htmlspecialchars($onwer['name']); ?></p>
                            <p class="text-sm text-gray-600">Property Owner</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <p class="flex items-center text-gray-700">
                            <i class="fas fa-phone mr-2 text-indigo-600"></i>
                            <?php echo htmlspecialchars($onwer['phone']); ?>
                        </p>
                        <p class="flex items-center text-gray-700">
                            <i class="fas fa-envelope mr-2 text-indigo-600"></i>
                            <?php echo htmlspecialchars($onwer['email']); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Review Modal -->
    <div id="reviewModal" class="modal-backdrop">
        <div class="modal-content">
            <h2 class="text-2xl font-bold mb-4">Submit Your Review</h2>
            <p class="text-gray-600 mb-4" id="ratingText">You rated this property <span id="selectedRating">3</span> stars.</p>
            
            <form method="post" action="propertyDetails.php?id=<?php echo $property_id; ?>">
                <input type="hidden" name="rating" id="ratingInput" value="3">
                
                <label for="review" class="block text-sm font-medium text-gray-700 mb-2">
                    Your Review
                </label>
                <textarea
                    id="review"
                    name="review"
                    class="w-full border border-gray-300 rounded-lg p-2 mb-4"
                    rows="4"
                    placeholder="What do you think about this property?"
                    required
                ></textarea>
                <button
                    type="submit"
                    name="submit_review"
                    class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600"
                >
                    Submit Review
                </button>
            </form>
            
            <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl" onclick="closeReviewModal()">
                &times;
            </button>
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="modal-backdrop">
        <div class="modal-content">
            <h2 class="text-2xl font-bold mb-4">Book Now</h2>
            <form method="post" action="propertyDetails.php?id=<?php echo $property_id; ?>">
                <!-- Start Date -->
                <div class="mb-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">
                        Check-in Date
                    </label>
                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                        required
                        min="<?php echo date('Y-m-d'); ?>"
                    />
                </div>

                <!-- Monthly Option -->
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="is_monthly" name="is_monthly" class="rounded border-gray-300 text-indigo-600" 
                               onchange="toggleEndDateField()">
                        <span class="ml-2">Monthly Rental (30 days)</span>
                    </label>
                </div>

                <!-- End Date -->
                <div class="mb-4" id="end_date_container">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">
                        Check-out Date
                    </label>
                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                        required
                    />
                </div>
                
                <!-- Rent Amount (Read-Only) -->
                <div class="mb-4">
                    <label for="rent_amount" class="block text-sm font-medium text-gray-700">
                        Rent Amount
                    </label>
                    <input
                        type="text"
                        id="rent_amount"
                        name="rent_amount"
                        value="$<?php echo htmlspecialchars($property['price']); ?>"
                        readonly
                        class="mt-1 block w-full border border-gray-300 rounded-md p-2 bg-gray-100"
                    />
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    name="submit_booking"
                    class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600"
                >
                    Confirm Booking
                </button>
            </form>

            <!-- Close Modal Button -->
            <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl" onclick="closeBookingModal()">
                &times;
            </button>
        </div>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="modal-backdrop">
        <div class="modal-content">
            <h2 class="text-2xl font-bold mb-4">Please Log In or Create an Account</h2>
            <p class="mb-4">
                You must be logged in to make a booking or leave a review. Would you like to log
                in or create a new account?
            </p>
            <div class="flex gap-4">
                <a href="login.php" class="bg-blue-500 text-white py-2 px-4 rounded-lg text-center flex-1">
                    Log In
                </a>
                <a href="register.php" class="bg-green-500 text-white py-2 px-4 rounded-lg text-center flex-1">
                    Create account
                </a>
            </div>
            <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl" onclick="closeLoginModal()">
                &times;
            </button>
        </div>
    </div>
    
    <!-- CORRECTED: Full width footer -->
    <div class="w-full">
        <?php include './include/footer.php' ?>
    </div>

    <script>
        // Current rating for review
        let currentRating = 3;
        
        // Set rating when star is clicked
        function setRating(rating) {
            currentRating = rating;
            document.getElementById('selectedRating').textContent = rating;
            document.getElementById('ratingInput').value = rating;
            
            // Update stars UI
            const stars = document.querySelectorAll('[data-rating]');
            stars.forEach(star => {
                const starValue = parseInt(star.getAttribute('data-rating'));
                if (starValue <= rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            });
            
            <?php if ($user_logged_in): ?>
            openReviewModal();
            <?php else: ?>
            openLoginModal();
            <?php endif; ?>
        }
        
        // Calculate end date based on start date (for monthly rentals)
        function calculateEndDate() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const isMonthly = document.getElementById('is_monthly').checked;
            
            if (startDateInput.value && isMonthly) {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + 30);
                
                // Format date as YYYY-MM-DD
                const year = endDate.getFullYear();
                const month = String(endDate.getMonth() + 1).padStart(2, '0');
                const day = String(endDate.getDate()).padStart(2, '0');
                
                endDateInput.value = `${year}-${month}-${day}`;
            }
        }
        
        // Toggle end date field visibility based on monthly checkbox
        function toggleEndDateField() {
            const isMonthly = document.getElementById('is_monthly').checked;
            const endDateContainer = document.getElementById('end_date_container');
            
            if (isMonthly) {
                endDateContainer.style.display = 'none';
                calculateEndDate();
            } else {
                endDateContainer.style.display = 'block';
                document.getElementById('end_date').value = '';
            }
        }
        
        // Modal functions
        function openReviewModal() {
            document.getElementById('reviewModal').style.display = 'flex';
        }
        
        function closeReviewModal() {
            document.getElementById('reviewModal').style.display = 'none';
        }
        
        function openBookingModal() {
            <?php if ($user_logged_in): ?>
            document.getElementById('bookingModal').style.display = 'flex';
            <?php else: ?>
            openLoginModal();
            <?php endif; ?>
        }
        
        function closeBookingModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }
        
        function openLoginModal() {
            document.getElementById('loginModal').style.display = 'flex';
        }
        
        function closeLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
        }
        
        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Set min date for end date input based on start date
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            
            startDateInput.addEventListener('change', function() {
                endDateInput.min = this.value;
                if (document.getElementById('is_monthly').checked) {
                    calculateEndDate();
                }
            });
            
            // Initialize date inputs with current date
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            
            startDateInput.value = `${year}-${month}-${day}`;
            endDateInput.min = startDateInput.value;
        });
    </script>
</body>
</html>
<?php
require '../config/connection.php'; // Include your database connection
session_start(); // Start session for user authentication

// Get the property ID from the URL
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if delete form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_property'])) {
    // Delete the property
    $delete_query = "DELETE FROM properties WHERE id = '$property_id'";
    $delete_result = mysqli_query($conn, $delete_query);
    
    if ($delete_result) {
        // Also delete related records (reviews, bookings, etc.)
        $delete_reviews = "DELETE FROM reviews WHERE property_id = '$property_id'";
        mysqli_query($conn, $delete_reviews);
        
        $delete_bookings = "DELETE FROM bookings WHERE property_id = '$property_id'";
        mysqli_query($conn, $delete_bookings);
        
        // Redirect to properties list
        header("Location: properties.php?deleted=success");
        exit;
    } else {
        $error_message = "Failed to delete property.";
    }
}

// Fetch property details from the database
$query = "SELECT * FROM properties WHERE id = '$property_id'";
$result = mysqli_query($conn, $query);
$property = mysqli_fetch_assoc($result);

// Check if property exists
if (!$property) {
    header("Location: properties.php?error=notfound");
    exit;
}

// Fetch property reviews
$review_query = "SELECT * FROM reviews WHERE property_id = '$property_id'";
$reviews_result = mysqli_query($conn, $review_query);

// Check if delete confirmation was requested
$show_delete_modal = isset($_GET['confirm_delete']) && $_GET['confirm_delete'] == 'true';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <header class="bg-white shadow-md p-4 mb-6">
        <?php include './include/navbar.php'; ?>
    </header>
    <div class="container mx-auto flex">
        <!-- Sidebar -->
        <aside class="w-1/4 bg-white p-6 shadow-lg rounded-lg">
            <?php include './include/sidebar.php'; ?>
        </aside>
        <!-- Main Content -->
        <main class="w-3/4 p-6 grid grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 bg-white p-8 shadow-lg rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold"><?= htmlspecialchars($property['title']) ?></h1>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <a href="edit_property.php?id=<?= $property_id ?>" class="px-4 py-2 bg-blue-500 text-white rounded-md">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <a href="?id=<?= $property_id ?>&confirm_delete=true" class="px-4 py-2 bg-red-500 text-white rounded-md">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </a>
                    </div>
                </div>
                
                <img src="<?= htmlspecialchars($property['images']) ?>" alt="Property Image" class="w-full h-80 object-cover rounded-md">
                <p class="text-gray-700 mt-6"><?= htmlspecialchars($property['description']) ?></p>
                <ul class="mt-6 space-y-3">
                    <li><strong>Price:</strong> $<?= htmlspecialchars($property['price']) ?></li>
                    <li><strong>Period:</strong> <?= htmlspecialchars($property['period']) ?></li>
                    <li><strong>Purpose:</strong> <?= htmlspecialchars($property['purpose']) ?></li>
                    <li><strong>Address:</strong> <?= htmlspecialchars($property['address']) ?>, <?= htmlspecialchars($property['zipcode']) ?></li>
                    <li><strong>Sector:</strong> <?= htmlspecialchars($property['sector']) ?></li>
                    <li><strong>Village:</strong> <?= htmlspecialchars($property['village']) ?></li>
                    <li><strong>Bedrooms:</strong> <?= htmlspecialchars($property['bedrooms']) ?></li>
                    <li><strong>Bathrooms:</strong> <?= htmlspecialchars($property['bathrooms']) ?></li>
                    <li><strong>Area:</strong> <?= htmlspecialchars($property['area']) ?> sq ft</li>
                    <li><strong>Type:</strong> <?= htmlspecialchars($property['property_type']) ?></li>
                    <li><strong>Status:</strong> <?= htmlspecialchars($property['status']) ?></li>
                    <li><strong>Features:</strong> <?= htmlspecialchars(implode(', ', json_decode($property['features'], true))) ?></li>
                </ul>
            </div>
            
            <!-- Right Column - Reviews -->
            <div class="bg-white p-8 shadow-lg rounded-lg">
                <h2 class="text-2xl font-bold mb-6">Tenant Reviews</h2>
                <?php if (mysqli_num_rows($reviews_result) > 0): ?>
                    <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
                        <div class="border-b pb-6 mb-6">
                            <h3 class="font-semibold"><?= htmlspecialchars($review['title']) ?></h3>
                            <div class="flex items-center mt-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="mt-3 text-gray-600"><?= htmlspecialchars($review['comment']) ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-gray-500 italic">No reviews yet for this property.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <?php if ($show_delete_modal): ?>
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <h2 class="text-xl font-bold mb-4 text-red-600">Confirm Property Deletion</h2>
            <p class="mb-4">Are you sure you want to delete the property:</p>
            <p class="font-bold mb-4"><?= htmlspecialchars($property['title']) ?></p>
            <p class="text-red-500 mb-6">This action cannot be undone. All property data, reviews, and booking information will be permanently removed.</p>
            
            <div class="flex justify-between">
                <form method="POST">
                    <input type="hidden" name="delete_property" value="1">
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md">
                        Yes, Delete Property
                    </button>
                </form>
                
                <a href="?id=<?= $property_id ?>" class="px-4 py-2 bg-gray-500 text-white rounded-md">
                    Cancel
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>
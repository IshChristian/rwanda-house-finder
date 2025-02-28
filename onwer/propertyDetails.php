<?php
require '../config/connection.php'; // Include your database connection

// Get the property ID from the URL
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch property details from the database
$query = "SELECT * FROM properties WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();

// Fetch property reviews
$review_query = "SELECT * FROM reviews WHERE property_id = ?";
$review_stmt = $conn->prepare($review_query);
$review_stmt->bind_param("i", $property_id);
$review_stmt->execute();
$reviews = $review_stmt->get_result();
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
                <h1 class="text-3xl font-bold mb-6"> <?= $property['title'] ?> </h1>
                <img src="<?= $property['images'] ?>" alt="Property Image" class="w-full h-80 object-cover rounded-md">
                <p class="text-gray-700 mt-6"> <?= $property['description'] ?> </p>
                <ul class="mt-6 space-y-3">
                    <li><strong>Price:</strong> $<?= $property['price'] ?></li>
                    <li><strong>Period:</strong> <?= $property['period'] ?></li>
                    <li><strong>Purpose:</strong> <?= $property['purpose'] ?></li>
                    <li><strong>Address:</strong> <?= $property['address'] ?>, <?= $property['zipcode'] ?></li>
                    <li><strong>Sector:</strong> <?= $property['sector'] ?></li>
                    <li><strong>Village:</strong> <?= $property['village'] ?></li>
                    <li><strong>Bedrooms:</strong> <?= $property['bedrooms'] ?></li>
                    <li><strong>Bathrooms:</strong> <?= $property['bathrooms'] ?></li>
                    <li><strong>Area:</strong> <?= $property['area'] ?> sq ft</li>
                    <li><strong>Type:</strong> <?= $property['property_type'] ?></li>
                    <li><strong>Status:</strong> <?= $property['status'] ?></li>
                    <li><strong>Features:</strong> <?= implode(', ', json_decode($property['features'], true)) ?></li>
                </ul>
            </div>
            
            <!-- Right Column - Reviews -->
            <div class="bg-white p-8 shadow-lg rounded-lg">
                <h2 class="text-2xl font-bold mb-6">Tenant Reviews</h2>
                <?php while ($review = $reviews->fetch_assoc()): ?>
                    <div class="border-b pb-6 mb-6">
                        <h3 class="font-semibold"> <?= $review['title'] ?> </h3>
                        <div class="flex items-center mt-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="mt-3 text-gray-600"> <?= $review['comment'] ?> </p>
                    </div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>
</body>
</html>

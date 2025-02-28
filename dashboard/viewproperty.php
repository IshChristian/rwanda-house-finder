<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Owner Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50" x-data="{ showDeleteModal: false, showImageModal: false, currentImage: '' }">
    <div class="min-h-screen flex">
        <!-- Sidebar Navigation -->
        <?php include "./include/sidebar.php"; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <?php include "./include/header.php"; ?>

            <!-- Backend Logic to Fetch Property Details -->
            <?php
            // Database connection
            $conn = new mysqli("localhost", "root", "", "liveatrwanda");

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Get property ID from URL
            $property_id = 1;
            // $property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

            // Fetch property details
            $sql = "SELECT * FROM properties WHERE property_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $property_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                // Redirect to viewproperty.php if no property is found
                header("Location: viewproperty.php");
                exit();
            }

            $property = $result->fetch_assoc();
            ?>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-100 p-4">
                <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <!-- Header with Actions -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($property['title']); ?></h2>
                            <p class="text-gray-600">Property ID: PROP<?php echo htmlspecialchars($property['property_id']); ?></p>
                        </div>
                        <div class="flex space-x-4">
                            <a href="viewproperty.php" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                                Back to Listings
                            </a>
                            <a href="editproperty.php?id=<?php echo $property['property_id']; ?>" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700">
                                Edit Property
                            </a>
                            <button @click="showDeleteModal = true" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700">
                                Delete Property
                            </button>
                        </div>
                    </div>

                    <!-- Property Details Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left Column -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Image Gallery Card -->
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Property Images</h3>
                                    <div class="grid grid-cols-3 gap-4">
                                        <?php
                                        // Fetch property images
                                        $image_sql = "SELECT * FROM property_images WHERE property_id = ?";
                                        $image_stmt = $conn->prepare($image_sql);
                                        $image_stmt->bind_param("i", $property_id);
                                        $image_stmt->execute();
                                        $image_result = $image_stmt->get_result();

                                        while ($image = $image_result->fetch_assoc()) {
                                            echo '<div class="aspect-w-4 aspect-h-3 cursor-pointer" @click="showImageModal = true; currentImage = \'' . htmlspecialchars($image['url']) . '\'">
                                                    <img src="../uploads/' . htmlspecialchars($image['url']) . '" alt="Property Image" class="object-cover rounded-lg hover:opacity-75 transition">
                                                </div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Property Details Card -->
                            <div class="bg-white rounded-lg shadow-sm">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Property Details</h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Property Type</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($property['property_type']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Price</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($property['price']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Size</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($property['size']); ?> sq ft</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Bedrooms</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($property['bedrooms']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Bathrooms</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($property['bathrooms']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Parkings</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($property['parking_spots']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Available From</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($property['available_from']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Condition</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($property['conditions']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Status</p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Available
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-6">
                                        <p class="text-sm font-medium text-gray-500">Description</p>
                                        <p class="mt-1 text-sm text-gray-900"><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Features & Amenities Card -->
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Features & Amenities</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <?php
            // Define the features and their corresponding database column names
            $feature_list = [
                'Air Conditioning' => 'air_conditioning',
                'Heating' => 'heating',
                'Internet' => 'internet',
                'Security System' => 'security_system'
            ];

            // Loop through each feature and display if the value is 1
            foreach ($feature_list as $feature_name => $column) {
                if ($property[$column] == 1) {
                    echo '<div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="text-sm text-gray-600">' . htmlspecialchars($feature_name) . '</span>
                          </div>';
                }
            }
            ?>
        </div>
    </div>
</div>

                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Owner Information Card -->
                            <div class="bg-white rounded-lg shadow-sm">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Owner Information</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Owner Name</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($property['owner_name']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Email</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($property['email']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Phone</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($property['phone_number']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Analytics Card -->
                            <div class="bg-white rounded-lg shadow-sm">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Analytics</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Total Views</p>
                                            <p class="mt-1 text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($property['views']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Inquiries</p>
                                            <p class="mt-1 text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($property['inquiries']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Saved by Users</p>
                                            <p class="mt-1 text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($property['saves']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </main>

            <!-- Delete Confirmation Modal -->
            <div x-show="showDeleteModal" class="fixed inset-0 z-10 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                    <!-- Modal panel -->
                    <div class="bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                        <div>
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.93 2a9.998 9.998 0 1113.86 0A10 10 0 015 16z"/>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Delete Property</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Are you sure you want to delete this property? This action cannot be undone.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button @click="showDeleteModal = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                                Delete
                            </button>
                            <button @click="showDeleteModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Modal -->
            <div x-show="showImageModal" class="fixed inset-0 z-10 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                    <!-- Modal panel -->
                    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553 2.276A1 1 0 0120 13.236V17a2 2 0 01-2 2h-4v-2h4v-3.236L15 13.236V11a1 1 0 00-.553-.894l-9-4.5A1 1 0 004 6v11a1 1 0 001 1h9v2H5a3 3 0 01-3-3V6a3 3 0 011.5-2.598l9-4.5a3 3 0 012.996.013l9 4.5A3 3 0 0122 6v7a3 3 0 01-1.5 2.598l-9 4.5A3 3 0 0111 19H8v2h3a5 5 0 003.535-1.464L20 16.236V17a4 4 0 01-4 4h-3a1 1 0 010-2h3a2 2 0 002-2v-2h-3a1 1 0 010-2h3a1 1 0 011 1v2h2.764L15 10.764V10z"/>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Property Image</h3>
                                    <div class="mt-2">
                                        <img :src="currentImage" alt="Property Image" class="w-full h-full object-cover rounded-lg">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button @click="showImageModal = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <?php include "./include/footer.php"; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>

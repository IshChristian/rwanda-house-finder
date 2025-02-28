<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-800">liveat<span class="text-blue-700">rwanda</span> | Add New Property</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        < Back
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Add New Property</h2>
                <p class="text-gray-600">Review and approve property listing details</p>
            </div>

            <form class="space-y-8" method="POST" enctype="multipart/form-data">
    <!-- Property Information Section -->
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-gray-900 pb-2 border-b">Property Information</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Property Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Property Type</label>
                <select name="property_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option>Commercial</option>
                    <option>Residential</option>
                    <option>Industrial</option>
                    <option>Land</option>
                </select>
            </div>

            <!-- Property Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Property Title</label>
                <input type="text" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Price (Monthly Rent)</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="text" name="price" class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Size -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Size (sq ft)</label>
                <input type="number" name="size" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Available From -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Available From</label>
                <input type="date" name="available_from" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Property Condition -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Property Condition</label>
                <select name="condition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option>New</option>
                    <option>Well-maintained</option>
                    <option>Needs Repair</option>
                    <option>Under Renovation</option>
                </select>
            </div>
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>

        <!-- Location -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Location</label>
            <input type="text" name="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <!-- Images Upload -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Property Images</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600">
                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                            <span>Upload files</span>
                            <input id="file-upload" name="images[]" type="file" class="sr-only" multiple onchange="previewImages(event)">
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                </div>
            </div>
        </div>

        <!-- Image Preview -->
        <div id="image-preview" class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4"></div>
    </div>

    <!-- Features Section -->
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-gray-900 pb-2 border-b">Features & Amenities</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Bedrooms</label>
                <input type="number" name="bedrooms" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Bathrooms</label>
                <input type="number" name="bathrooms" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Parking Spots</label>
                <input type="number" name="parking_spots" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="air_conditioning" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Air Conditioning</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="heating" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Heating</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="internet" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Internet</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="security_system" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="text-sm text-gray-700">Security System</span>
            </label>
        </div>
    </div>

    <!-- Owner Information -->
    <div class="space-y-6">
        <h3 class="text-xl font-semibold text-gray-900 pb-2 border-b">Owner Information</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Owner Name</label>
                <input type="text" name="owner_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="phone_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <!-- Submit Section -->
    <div class="mt-8">
        <button type="submit" class="w-full py-3 px-6 bg-blue-600 text-white font-semibold rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Submit Property
        </button>
    </div>
</form>
        </div>
    </main>

    <script>
        // Preview uploaded images
        function previewImages(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('image-preview');
            previewContainer.innerHTML = '';  // Clear previous previews

            Array.from(files).forEach(file => {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const imgCard = document.createElement('div');
                    imgCard.classList.add('bg-white', 'border', 'rounded-lg', 'p-2', 'shadow-sm');

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Uploaded Image';
                    img.classList.add('w-full', 'h-32', 'object-cover', 'rounded-md');

                    imgCard.appendChild(img);
                    previewContainer.appendChild(imgCard);
                };

                reader.readAsDataURL(file);
            });
        }
    </script>
</body>
</html>

<?php
// Database connection (Adjust with your own credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "liveatrwanda";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $property_type = $_POST['property_type'];
    $userID = 1;
    $title = $_POST['title'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $available_from = $_POST['available_from'];
    $condition = $_POST['condition'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $parking_spots = $_POST['parking_spots'];
    $air_conditioning = isset($_POST['air_conditioning']) ? 1 : 0;
    $heating = isset($_POST['heating']) ? 1 : 0;
    $internet = isset($_POST['internet']) ? 1 : 0;
    $security_system = isset($_POST['security_system']) ? 1 : 0;
    $owner_name = $_POST['owner_name'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];

    // Insert property data into the properties table
    $sql = "INSERT INTO properties (property_type, title, price, size, available_from, conditions, description, location, 
            bedrooms, bathrooms, parking_spots, air_conditioning, heating, internet, security_system, owner_name, phone_number, email, user_id)
            VALUES ('$property_type', '$title', '$price', '$size', '$available_from', '$condition', '$description', '$location', 
            '$bedrooms', '$bathrooms', '$parking_spots', '$air_conditioning', '$heating', '$internet', '$security_system', '$owner_name', '$phone_number', '$email','$userID')";

    if ($conn->query($sql) === TRUE) {
        // Get the last inserted property ID
        $property_id = $conn->insert_id;

        // Handle image upload
        if (isset($_FILES['images'])) {
            foreach ($_FILES['images']['tmp_name'] as $index => $tmp_name) {
                $image_name = $_FILES['images']['name'][$index];
                $image_path = '../uploads/' . basename($image_name);
                
                if (move_uploaded_file($tmp_name, $image_path)) {
                    $sql_img = "INSERT INTO property_images (property_id, image_url) VALUES ('$property_id', '$image_path')";
                    $conn->query($sql_img);
                }
            }
        }

        // Close connection
        $conn->close();

        // Redirect to a success page or show an alert
        echo "<script>
                alert('Property added successfully!');
                window.location.href = 'property.php'; // Redirect to a success page
              </script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

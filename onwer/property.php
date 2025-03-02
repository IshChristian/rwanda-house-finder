<?php
session_start();

// Check if user is logged in and user_id is available in session
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to add a property.");
}

$user_id = $_SESSION['user_id'];  // Get user_id from session

include '../config/connection.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Form fields
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $period = mysqli_real_escape_string($conn, $_POST['period']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $zipCode = mysqli_real_escape_string($conn, $_POST['zipCode']);
    $area = (int)$_POST['area'];
    $bedroom = (int)$_POST['bedroom'];
    $bathroom = (int)$_POST['bathroom'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Features (store as an array)
    $features = isset($_POST['features']) ? json_encode($_POST['features']) : json_encode([]);

    // Prepare images for upload
    $image_urls = [];
    if (isset($_FILES['images'])) {
        $target_dir = "../uploads/";
        $files = $_FILES['images'];
        $file_count = count($files['name']);

        // Ensure exactly 3 images are uploaded
        if ($file_count == 3) {
            for ($i = 0; $i < $file_count; $i++) {
                $target_file = $target_dir . basename($files["name"][$i]);
                if (move_uploaded_file($files["tmp_name"][$i], $target_file)) {
                    $image_urls[] = $target_file;
                }
            }
        } else {
            echo "You must upload exactly 3 images.";
            exit();
        }
    }

    // Store image URLs as a JSON array
    $images = json_encode($image_urls);

    // Insert data into properties table using regular mysqli
    $query = "INSERT INTO properties (user_id, title, property_type, purpose, status, price, period, address, sector, village, zipcode, area, bedrooms, bathrooms, description, features, images) 
              VALUES ('$user_id', '$title', '$type', '$purpose', '$status', '$price', '$period', '$address', '$city', '$state', '$zipCode', $area, $bedroom, $bathroom, '$description', '$features', '$images')";
    
    if(mysqli_query($conn, $query)) {
        echo "Property added successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Management Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        /* Ensure the sidebar is fixed and has a fixed width */
        .sidebar {
            width: 250px; /* Set your sidebar width */
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background-color: #fff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Adjust the main content to take the remaining space */
        .main-content {
            margin-left: 250px; /* Ensure the content is aligned next to the sidebar */
            padding: 20px;
        }

        /* Ensure the content doesn't overflow and has proper padding */
        .main-content-inner {
            padding-left: 20px;
            padding-right: 20px;
            margin-top: 60px; /* Adjust based on the height of your navbar */
        }

        /* Make it responsive */
        @media (max-width: 1024px) {
            .sidebar {
                position: relative;
                width: 100%;
            }
            .main-content {
                margin-left: 0;
            }
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 50;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        /* Feature checkbox styling */
        .feature-label {
            transition: all 0.3s ease;
        }
        .feature-label:hover {
            background-color: rgb(37, 99, 235);
        }
        .feature-label:hover .feature-text,
        .feature-label:hover i {
            color: white;
        }
        .feature-checkbox:checked + .feature-text {
            color: white;
        }
        .feature-checkbox:checked + .feature-text i {
            color: white;
        }
        
        /* Image upload preview */
        .image-preview {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            margin-right: 10px;
            display: inline-block;
        }
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-preview .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        /* Property card styling */
        .property-card {
            transition: all 0.3s ease;
        }
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <?php include './include/navbar.php' ?>

    <!-- Header -->
    <?php include './include/sidebar.php' ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="main-content-inner">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">My Properties</h1>
                    <p class="text-gray-600">Manage your property listings</p>
                </div>
                <button id="openModalBtn" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out flex items-center shadow-sm">
                    <i class="fas fa-plus mr-2"></i> Add New Property
                </button>
            </div>

            <!-- Property Listings -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                // Fetch properties from database using mysqli_query instead of prepared statements
                $user_id = $_SESSION['user_id'];
                $sql = "SELECT * FROM properties WHERE user_id = '$user_id'";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $property_id = $row['id'];
                        $title = $row['title'];
                        $location = $row['address'] . ", " . $row['sector'] . ", " . $row['village'] . ", " . $row['zipcode'];
                        $description = $row['description'];
                        $status = $row['status'];
                        $price = $row['price'];
                        $period = $row['period'];
                        $bedroom = $row['bedrooms'];
                        $bathroom = $row['bathrooms'];
                        $area = $row['area'];

                        // Truncate description to 150 characters
                        $description_preview = mb_strlen($description) > 150 ? mb_substr($description, 0, 150) . '...' : $description;

                        // Get image URL (First image from the JSON field)
                        $images = json_decode($row['images'], true);
                        $image_url = !empty($images) ? $images[0] : 'default_image.jpg'; // Fallback to a default image
                        ?>
                        
                        <div class="property-card bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                            <div class="relative">
                                <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($title); ?>" class="w-full h-48 object-cover">
                                <div class="absolute top-0 right-0 mt-2 mr-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $status === 'available' ? 'Available' : 'Sold'; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-1">
                                    <a href="propertyDetails.php?property_id=<?php echo $property_id; ?>" class="hover:text-blue-600">
                                        <?php echo htmlspecialchars($title); ?>
                                    </a>
                                </h3>
                                
                                <div class="flex items-center text-sm text-gray-500 mb-2">
                                    <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                                    <span><?php echo htmlspecialchars($location); ?></span>
                                </div>
                                
                                <p class="text-sm text-gray-600 line-clamp-3 mb-3">
                                    <?php echo htmlspecialchars($description_preview); ?>
                                </p>
                                
                                <div class="flex justify-between items-center">
                                    <div class="flex space-x-3 text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <i class="fas fa-bed mr-1"></i>
                                            <?php echo $bedroom; ?>
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-bath mr-1"></i>
                                            <?php echo $bathroom; ?>
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-ruler-combined mr-1"></i>
                                            <?php echo $area; ?> sqft
                                        </span>
                                    </div>
                                    
                                    <a href="propertyDetails.php?property_id=<?php echo $property_id; ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View Details
                                    </a>
                                </div>
                            </div>
                            
                            <div class="py-2 px-4 border-t border-gray-100 <?php echo $status === 'available' ? 'bg-green-50' : 'bg-red-50'; ?>">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium">
                                        <?php echo $row['purpose'] === 'rent' ? 'For Rent' : 'For Sale'; ?>
                                    </span>
                                    <span class="font-bold text-gray-900">
                                        $<?php echo number_format($price); ?><?php echo $period === 'monthly' ? '/mo' : ''; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <?php
                    }
                } else {
                    ?>
                    <div class="col-span-full flex flex-col items-center justify-center p-10 bg-white rounded-xl shadow-sm border border-gray-200">
                        <i class="fas fa-home text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">No Properties Found</h3>
                        <p class="text-gray-500 text-center mb-4">You haven't added any properties yet.</p>
                        <button id="emptyStateAddBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300 ease-in-out">
                            <i class="fas fa-plus mr-2"></i> Add Your First Property
                        </button>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Add Property Modal -->
    <div id="newPropertyModal" class="modal">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-start mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Add New Property</h2>
                <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700 transition duration-300 ease-in-out">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <form method="POST" action="" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Title
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    />
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                        Type
                    </label>
                    <select
                        id="type"
                        name="type"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="residential">Residential</option>
                        <option value="commercial">Commercial</option>
                    </select>
                </div>
                
                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">
                        Purpose
                    </label>
                    <select
                        id="purpose"
                        name="purpose"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="rent">Rent</option>
                        <option value="sale">Sale</option>
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="available">Available</option>
                        <option value="sold">Sold</option>
                    </select>
                </div>
                
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                        Price
                    </label>
                    <input
                        type="number"
                        id="price"
                        name="price"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    />
                </div>
                
                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700 mb-1">
                        Period
                    </label>
                    <select
                        id="period"
                        name="period"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                        Address
                    </label>
                    <input
                        type="text"
                        id="address"
                        name="address"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    />
                </div>
                
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                        City
                    </label>
                    <input
                        type="text"
                        id="city"
                        name="city"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    />
                </div>
                
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                        State
                    </label>
                    <input
                        type="text"
                        id="state"
                        name="state"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    />
                </div>
                
                <div>
                    <label for="zipCode" class="block text-sm font-medium text-gray-700 mb-1">
                        Zip Code
                    </label>
                    <input
                        type="text"
                        id="zipCode"
                        name="zipCode"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    />
                </div>
                
                <div>
                    <label for="area" class="block text-sm font-medium text-gray-700 mb-1">
                        Area (sqft)
                    </label>
                    <input
                        type="number"
                        id="area"
                        name="area"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    />
                </div>
                
                <div>
                    <label for="bedroom" class="block text-sm font-medium text-gray-700 mb-1">
                        Bedrooms
                    </label>
                    <input
                        type="number"
                        id="bedroom"
                        name="bedroom"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    />
                </div>
                
                <div>
                    <label for="bathroom" class="block text-sm font-medium text-gray-700 mb-1">
                        Bathrooms
                    </label>
                    <input
                        type="number"
                        id="bathroom"
                        name="bathroom"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    />
                </div>
                
                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    ></textarea>
                </div>
                
                <!-- Features -->
                <div id="featureSection" class="md:col-span-2">
                    <label for="features" class="block text-sm font-medium text-gray-700 mb-2">Features</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label class="feature-label group relative flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-blue-500 cursor-pointer transition-all">
                            <input type="checkbox" name="features[]" value="wifi" class="feature-checkbox absolute opacity-0" />
                            <span class="feature-text flex items-center text-sm text-gray-700 group-hover:text-white">
                                <i class="fas fa-wifi mr-2 text-blue-500 group-hover:text-white"></i>
                                Wifi
                            </span>
                        </label>
                        
                        <label class="feature-label group relative flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-blue-500 cursor-pointer transition-all">
                            <input type="checkbox" name="features[]" value="ac" class="feature-checkbox absolute opacity-0" />
                            <span class="feature-text flex items-center text-sm text-gray-700 group-hover:text-white">
                                <i class="fas fa-snowflake mr-2 text-blue-500 group-hover:text-white"></i>
                                Air Conditioning
                            </span>
                        </label>
                        
                        <label class="feature-label group relative flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-blue-500 cursor-pointer transition-all">
                            <input type="checkbox" name="features[]" value="parking" class="feature-checkbox absolute opacity-0" />
                            <span class="feature-text flex items-center text-sm text-gray-700 group-hover:text-white">
                                <i class="fas fa-car mr-2 text-blue-500 group-hover:text-white"></i>
                                Parking
                            </span>
                        </label>
                        
                        <label class="feature-label group relative flex items-center p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-blue-500 cursor-pointer transition-all">
                            <input type="checkbox" name="features[]" value="gym" class="feature-checkbox absolute opacity-0" />
                            <span class="feature-text flex items-center text-sm text-gray-700 group-hover:text-white">
                                <i class="fas fa-dumbbell mr-2 text-blue-500 group-hover:text-white"></i>
                                Gym
                            </span>
                        </label>
                    </div>
                </div>
                
                <!-- Images -->
                <div class="md:col-span-2">
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-1">
                        Images (Upload 3 images)
                    </label>
                    <div class="flex flex-wrap gap-2 mb-2" id="imagePreviewContainer">
                        <!-- Image previews will be added here by JavaScript -->
                    </div>
                    <div class="flex items-center justify-center w-full">
                        <label for="imageUpload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-gray-500">PNG, JPG or JPEG (MAX. 3 images)</p>
                            </div>
                            <input id="imageUpload" name="images[]" type="file" class="hidden" multiple accept="image/*" />
                        </label>
                    </div>
                </div>
                
                <div class="md:col-span-2 mt-4">
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 transition duration-300 ease-in-out flex items-center justify-center"
                    >
                        <i class="fas fa-check mr-2"></i>
                        Add Property
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include './include/footer.php' ?>

    <script>
        // Modal functionality
        const openModalBtn = document.getElementById('openModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const newPropertyModal = document.getElementById('newPropertyModal');
        
        // Open modal only when button is clicked
        openModalBtn.addEventListener('click', function() {
            newPropertyModal.style.display = 'flex';
        });
        
        // Add event listener for the empty state button if it exists
        const emptyStateAddBtn = document.getElementById('emptyStateAddBtn');
        if (emptyStateAddBtn) {
            emptyStateAddBtn.addEventListener('click', function() {
                newPropertyModal.style.display = 'flex';
            });
        }
        
        // Close modal when close button is clicked
        closeModalBtn.addEventListener('click', function() {
            newPropertyModal.style.display = 'none';
        });
        
        // Close modal when clicking outside of the modal content
        newPropertyModal.addEventListener('click', function(event) {
            if (event.target === newPropertyModal) {
                newPropertyModal.style.display = 'none';
            }
        });
        
        // Feature checkbox styling
        const featureCheckboxes = document.querySelectorAll('.feature-checkbox');
        featureCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const label = this.closest('.feature-label');
                if (this.checked) {
                    label.classList.add('bg-blue-600');
                    label.classList.remove('bg-white');
                    label.querySelectorAll('.text-gray-700, .text-blue-500').forEach(el => {
                        el.classList.add('text-white');
                        el.classList.remove('text-gray-700', 'text-blue-500');
                    });
                } else {
                    label.classList.remove('bg-blue-600');
                    label.classList.add('bg-white');
                    const text = label.querySelector('.feature-text');
                    text.classList.remove('text-white');
                    text.classList.add('text-gray-700');
                    const icon = label.querySelector('i');
                    icon.classList.remove('text-white');
                    icon.classList.add('text-blue-500');
                }
            });
        });
        
        // Image upload preview
        const imageUpload = document.getElementById('imageUpload');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        
        imageUpload.addEventListener('change', function() {
            // Clear previous previews if needed
            imagePreviewContainer.innerHTML = '';
            
            // Limit to 3 images
            const files = Array.from(this.files).slice(0, 3);
            
            files.forEach((file, index) => {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'image-preview';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = `Preview ${index + 1}`;
                    
                    const removeBtn = document.createElement('div');
                    removeBtn.className = 'remove-btn';
                    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                    removeBtn.onclick = function() {
                        preview.remove();
                        // Note: This doesn't actually remove the file from the input
                        // In a real implementation, you'd need to handle this properly
                    };
                    
                    preview.appendChild(img);
                    preview.appendChild(removeBtn);
                    imagePreviewContainer.appendChild(preview);
                };
                
                reader.readAsDataURL(file);
            });
        });
    </script>
</body>
</html>
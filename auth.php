<?php
// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include './config/connection.php';

// Get email from URL parameter 
$email = isset($_GET['email']) ? $_GET['email'] : null;

if (!$email) {
    echo json_encode(['error' => 'No email provided']);
    exit;
}

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get selected user type
    $business_type = isset($_POST['business_type']) ? $_POST['business_type'] : null;
    
    if (!$business_type || !in_array($business_type, ['renter', 'owner'])) {
        echo json_encode(['error' => 'Please select a valid type']);
        exit;
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = 'uploads/profile_pictures/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $profileImage = null;
    $uploadSuccess = false;
    $uploadError = null;
    
    // Handle file upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $tempFile = $_FILES['profile_image']['tmp_name'];
        $fileExtension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        
        // Check if the file is an image
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $validExtensions)) {
            // Generate unique file name
            $fileName = uniqid('profile_') . '.' . $fileExtension;
            $targetFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($tempFile, $targetFile)) {
                $profileImage = $fileName;
                $uploadSuccess = true;
            } else {
                $uploadError = "Failed to upload image";
            }
        } else {
            $uploadError = "Invalid file type. Please upload an image (jpg, jpeg, png, gif)";
        }
    }
    
    // Update database
    $updateFields = [];
    $updateParams = [];
    $paramTypes = '';
    
    if ($profileImage) {
        $updateFields[] = "image = ?";
        $updateParams[] = $profileImage;
        $paramTypes .= 's';
    }
    
    if ($business_type) {
        $updateFields[] = "business_type = ?";
        $updateParams[] = $business_type;
        $paramTypes .= 's';
    }
    
    // Add email to parameters
    $updateParams[] = $email;
    $paramTypes .= 's';
    
    $query = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE email = ?";
    $stmt = $conn->prepare($query);
    
    // Dynamically bind parameters
    $bindParams = array_merge([$paramTypes], $updateParams);
    $stmt->bind_param(...$bindParams);
    
    $result = $stmt->execute();
    
    if ($result) {
        if ($business_type === 'renter') {
            header("Location: /renter/index.php?email=" . urlencode($email));
        } else {
            header("Location: /owner/index.php?email=" . urlencode($email));
        }
        exit;
    } else {
        $error = "Failed to update profile: " . $conn->error;
    }
    
    $stmt->close();
    $conn->close();
}

// Fetch user data to display current profile (if any)
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

$current_image = ($user && $user['image']) ? 'uploads/profile_pictures/' . $user['profile_image'] : '';
$current_type = ($user && $user['business_type']) ? $user['business_type'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #3B82F6;
        }
        .profile-pic-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #E5E7EB;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid #3B82F6;
        }
        .camera-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: #3B82F6;
            padding: 8px;
            border-radius: 50%;
            cursor: pointer;
        }
        .card-input:checked + .card {
            border-color: #3B82F6;
            background-color: #EFF6FF;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 max-w-md">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-2xl font-bold text-center mb-6">Complete Your Profile</h1>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($uploadError)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $uploadError; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" action="?email=<?php echo urlencode($email); ?>">
                <!-- Profile Picture Upload -->
                <div class="flex flex-col items-center mb-8">
                    <div class="relative mb-3">
                        <?php if ($current_image): ?>
                            <img src="<?php echo htmlspecialchars($current_image); ?>" alt="Profile" class="profile-pic" id="profile-preview">
                        <?php else: ?>
                            <div class="profile-pic-placeholder" id="profile-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <img src="" alt="Profile" class="profile-pic hidden" id="profile-preview">
                        <?php endif; ?>
                        <label class="camera-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <input type="file" name="profile_image" id="profile-image-input" class="hidden" accept="image/*">
                        </label>
                    </div>
                    <p class="text-sm text-gray-500">Upload a profile picture</p>
                </div>
                
                <!-- User Type Selection -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-3">I am a:</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <label>
                            <input type="radio" name="business_type" value="renter" class="card-input hidden" <?php echo $current_type === 'renter' ? 'checked' : ''; ?>>
                            <div class="card border-2 rounded-lg p-4 text-center cursor-pointer hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <div class="font-medium">Renter</div>
                            </div>
                        </label>
                        <label>
                            <input type="radio" name="business_type" value="owner" class="card-input hidden" <?php echo $current_type === 'owner' ? 'checked' : ''; ?>>
                            <div class="card border-2 rounded-lg p-4 text-center cursor-pointer hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <div class="font-medium">Owner</div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg focus:outline-none transition duration-150">
                    Submit and Continue
                </button>
            </form>
        </div>
    </div>
    
    <script>
        // Display image preview when a file is selected
        document.getElementById('profile-image-input').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const placeholder = document.getElementById('profile-placeholder');
                    const preview = document.getElementById('profile-preview');
                    
                    if (placeholder) {
                        placeholder.classList.add('hidden');
                    }
                    
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Style the selected card
        document.querySelectorAll('.card-input').forEach(input => {
            input.addEventListener('change', function() {
                document.querySelectorAll('.card').forEach(card => {
                    card.classList.remove('bg-blue-50', 'border-blue-500');
                    card.classList.add('border-gray-200');
                });
                
                if (this.checked) {
                    this.nextElementSibling.classList.add('bg-blue-50', 'border-blue-500');
                    this.nextElementSibling.classList.remove('border-gray-200');
                }
            });
            
            // Initialize selected card style
            if (input.checked) {
                input.nextElementSibling.classList.add('bg-blue-50', 'border-blue-500');
                input.nextElementSibling.classList.remove('border-gray-200');
            }
        });
    </script>
</body>
</html>
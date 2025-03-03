<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Database connection
include "../config/connection.php";

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Update profile information
if(isset($_POST['update_profile'])) {
    // Get form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    // Handle password update
    $password_update = false;
    if(!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
        $old_password = mysqli_real_escape_string($conn, $_POST['old_password']);
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
        
        // Verify old password
        $check_password = "SELECT password FROM users WHERE id = '$user_id'";
        $password_result = mysqli_query($conn, $check_password);
        $current_password = mysqli_fetch_assoc($password_result)['password'];
        
        if(md5($old_password) == $current_password) {
            $hashed_password = md5($new_password);
            $update_password = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
            if(mysqli_query($conn, $update_password)) {
                $password_update = true;
            } else {
                $password_error = "Error updating password: " . mysqli_error($conn);
            }
        } else {
            $password_error = "Current password is incorrect";
        }
    }
    
    // Handle profile picture upload
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['profile_picture']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($ext), $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = '../uploads/profile_pictures/' . $new_filename;
            
            if(move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                $profile_picture = $new_filename;
                $update_picture = "UPDATE users SET image = '$profile_picture' WHERE id = '$user_id'";
                mysqli_query($conn, $update_picture);
            }
        }
    }
    
    // Update user information
    $update_sql = "UPDATE users SET name = '$name', email = '$email', phone = '$phone' WHERE id = '$user_id'";
    if(!mysqli_query($conn, $update_sql)) {
        $update_error = "Error updating profile: " . mysqli_error($conn);
    }
    
    // Redirect to login if password was updated
    if($password_update) {
        session_destroy(); // Destroy the current session
        echo "<script>
            alert('Password updated successfully. Please login again with your new password.');
            window.location.href = '../login.php';
        </script>";
        exit;
    } else if(!isset($password_error) && !isset($update_error)) {
        // Refresh the page to show updated info
        header("Location: account.php?updated=1");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - LiveAtRwanda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include './include/navbar.php' ?>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-blue-800">My Profile</h1>
            
            <?php if(isset($_GET['updated'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                Profile updated successfully!
            </div>
            <?php endif; ?>
            
            <?php if(isset($password_error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $password_error; ?>
            </div>
            <?php endif; ?>
            
            <?php if(isset($update_error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $update_error; ?>
            </div>
            <?php endif; ?>
            
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/3 mb-6 md:mb-0 flex flex-col items-center">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-blue-200 mb-4">
                        <img src="../uploads/profile_pictures/<?php echo $user['image'] ? $user['image'] : 'default.jpg'; ?>" 
                             alt="Profile Picture" class="w-full h-full object-cover">
                    </div>
                </div>
                
                <div class="md:w-2/3">
                    <form action="account.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 font-bold mb-2">Name</label>
                            <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="phone" class="block text-gray-700 font-bold mb-2">Phone</label>
                            <input type="text" id="phone" name="phone" value="<?php echo $user['phone']; ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="profile_picture" class="block text-gray-700 font-bold mb-2">Profile Picture</label>
                            <input type="file" id="profile_picture" name="profile_picture" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="text-lg font-bold mb-2">Change Password</h3>
                            <div class="mb-2">
                                <label for="old_password" class="block text-gray-700 font-bold mb-2">Current Password</label>
                                <input type="password" id="old_password" name="old_password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="mb-2">
                                <label for="new_password" class="block text-gray-700 font-bold mb-2">New Password</label>
                                <input type="password" id="new_password" name="new_password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors duration-300">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php include './include/footer.php' ?>
</body>
</html>
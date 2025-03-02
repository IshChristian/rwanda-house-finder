<?php
// Start the session to enable session variables
session_start();
include './config/connection.php';

// Initialize variables
$name = "";
$email = "";
$phone = "";
$password = "";
$remember_me = false;
$error = null;

// Function to set cookies
function setCookie_custom($name, $value, $days) {
    $date = time() + ($days * 24 * 60 * 60);
    setcookie($name, $value, $date, "/");
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $phone = isset($_POST["phone"]) ? $_POST["phone"] : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $remember_me = isset($_POST["remember_me"]) ? true : false;
    
    // Basic validation
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = "Please fill in all fields!";
    } else {
        
        // Check if email already exists
        $check_sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "Email already registered. Please use a different email.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Current timestamp
            $created_at = date("Y-m-d H:i:s");
            
            // Prepare SQL statement to prevent SQL injection
            $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, phone, password, created_at) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $phone, $hashed_password, $created_at);
            
            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // Store email in cookies or session
                if ($remember_me) {
                    setCookie_custom("userEmail", $email, 7); // Cookie expires in 7 days
                } else {
                    $_SESSION["userEmail"] = $email;
                }
                
                // Redirect to authentication page
                header("Location: auth.php?email=" . urlencode($email));
                exit;
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
        
        // Close connection
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - LiveAt Rwanda</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.1.0/dist/full.css" rel="stylesheet" type="text/css" />

</head>
<body>
    <div class="hero min-h-screen bg-gradient-to-r from-blue-50 via-white to-blue-50">
        <div class="hero-content flex-col lg:flex-row gap-8">
            <!-- Text Section -->
            <div class="text-center lg:text-left max-w-lg">
                <h1 class="text-5xl font-bold text-primary">Sign Up Now!</h1>
                <p class="py-6 text-gray-600">
                    Join LiveAt Rwanda to explore amazing properties and manage your
                    listings with ease. Get started by creating your account today!
                </p>
            </div>

            <!-- Sign Up Card -->
            <div class="card w-full max-w-sm bg-white shadow-2xl rounded-lg">
                <div class="flex justify-center items-center mt-5">
                    <h3 class="text-primary text-xl font-black">
                        LiveAt <span class="text-info">Rwanda</span>
                    </h3>
                </div>

                <form class="card-body" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Full Name</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            value="<?php echo htmlspecialchars($name); ?>"
                            placeholder="Enter your full name"
                            class="input input-bordered focus:input-primary"
                            required
                        />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Email</span>
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="<?php echo htmlspecialchars($email); ?>"
                            placeholder="Enter your email"
                            class="input input-bordered focus:input-primary"
                            required
                        />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Phone</span>
                        </label>
                        <input
                            type="text"
                            name="phone"
                            value="<?php echo htmlspecialchars($phone); ?>"
                            placeholder="Enter your phone number"
                            class="input input-bordered focus:input-primary"
                            required
                        />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Password</span>
                        </label>
                        <input
                            type="password"
                            name="password"
                            placeholder="Create a password"
                            class="input input-bordered focus:input-primary"
                            required
                        />
                    </div>

                    <!-- Remember Me -->
                    <div class="form-control flex-row items-center mt-2">
                        <input
                            type="checkbox"
                            name="remember_me"
                            <?php if($remember_me) echo "checked"; ?>
                            class="checkbox checkbox-primary"
                        />
                        <label class="label">
                            <span class="label-text font-medium ml-2">Remember Me</span>
                        </label>
                    </div>

                    <label class="label">
                            <a href="login.php" class="label-text-alt link link-hover text-sm">
                                I already create account? login
                            </a>
                        </label>

                    <?php if ($error): ?>
                        <p class="text-sm text-red-500 mt-2">
                            <?php echo htmlspecialchars($error); ?>
                        </p>
                    <?php endif; ?>

                    <div class="form-control mt-4">
                        <button
                            type="submit"
                            class="btn btn-primary hover:btn-info"
                        >
                            Sign Up
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php
// Start the session to store user data
session_start();

// Database connection details
include './config/connection.php';

// Initialize error variable
$error = null;

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get email and password from form
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    
    try {
        
        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, password, business_type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password (assuming password is hashed with password_hash())
            if (password_verify($password, $user["password"])) {
                // Set session variables
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["business_type"] = $user["business_type"];
                
                // Set cookies (30 days expiration)
                $expiryDate = new DateTime();
                $expiryDate->modify("+30 days");
                setcookie("userID", $user["user_id"], $expiryDate->getTimestamp(), "/", "", true, true);
                setcookie("name", $user["name"], $expiryDate->getTimestamp(), "/", "", true, true);
                
                // Debug: Log cookies
                error_log("Cookies after login: " . print_r($_COOKIE, true));
                
                // Redirect based on business_type
                if (strtolower($user["business_type"]) === "owner") {
                    header("Location: onwer/index.php");
                } else {
                    header("Location: renter/index.php");
                }
                exit;
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
        
        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LiveAt Rwanda</title>
    <!-- Include DaisyUI or Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.1.0/dist/full.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="hero min-h-screen bg-gradient-to-r from-blue-50 via-white to-blue-50">
        <div class="hero-content flex-col lg:flex-row-reverse gap-8">
            <!-- Text Section -->
            <div class="text-center lg:text-left max-w-lg">
                <h1 class="text-5xl font-bold text-primary">Login Now!</h1>
                <p class="py-6 text-gray-600">
                    Access your account and explore amazing features tailored just for
                    you. Manage your listings, interact with others, and much more.
                </p>
            </div>

            <!-- Login Card -->
            <div class="card w-full max-w-sm bg-white shadow-2xl rounded-lg">
                <div class="flex justify-center items-center mt-5">
                    <h3 class="text-primary text-xl font-black">
                        LiveAt <span class="text-info">Rwanda</span>
                    </h3>
                </div>

                <form class="card-body" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <!-- Email Field -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Email</span>
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            placeholder="Enter your email"
                            class="input input-bordered focus:input-primary"
                            required
                        />
                    </div>

                    <!-- Password Field -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Password</span>
                        </label>
                        <input
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            class="input input-bordered focus:input-primary"
                            required
                        />
                        <label class="label">
                            <a href="#" class="label-text-alt link link-hover text-sm">
                                Forgot password?
                            </a>
                        </label>
                    </div>

                    <!-- Error Message -->
                    <?php if ($error): ?>
                        <p class="text-red-500 text-sm text-center mt-2"><?php echo htmlspecialchars($error); ?></p>
                    <?php endif; ?>

                    <!-- Login Button -->
                    <div class="form-control mt-4">
                        <button type="submit" class="btn btn-primary hover:btn-info">
                            Login
                        </button>
                    </div>

                    <!-- Sign in with Google -->
                    <div class="form-control mt-2">
                        <button
                            type="button"
                            class="btn btn-outline btn-info hover:btn-primary"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                class="w-5 h-5 mr-2"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2zm1.751 10.002v-.503h4.735c.212 1.11.212 2.398-.03 3.461-1.014 4.448-6.09 5.782-9.494 3.02l2.036-1.61c1.466 1.05 3.898 1.303 5.076-.467 1.02-1.574.883-4.009.117-5.11l-3.154 2.209zM7.36 8.451a4.76 4.76 0 0 1 2.8-.956h.001l.074-.003 2.036 1.61C11.094 9.528 8.722 9.28 7.365 7.5l-1.84 1.453a4.763 4.763 0 0 1 1.836 1.103zm-.33 5.284L5.556 12.282l2.037-1.61c1.676 2.207 4.142 1.84 5.477.248 1.195-1.47 1.432-3.81.263-5.126L10.3 5.105c1.042-1.297 3.054-1.785 4.418-.845 1.606 1.105 2.517 3.117 2.585 5.47l-1.925 1.915h-4.662zm-2.247 1.777c1.005.991 3.398.847 5.065.402 1.93-.503 3.174-2.364 3.036-4.477H8.875l-.002.503c-.12 1.724-1.607 3.005-2.905 3.573z"
                                />
                            </svg>
                           <a href="register.php">Create an account</a>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
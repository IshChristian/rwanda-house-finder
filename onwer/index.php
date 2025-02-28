<?php
include '../config/connection.php';
// Start session to access user_id
session_start();
$_SESSION['user_id'] = '1';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Function to get statistics
function getStats($conn, $user_id) {
    $stats = [
        'likes' => 0,
        'likes_growth' => 0,
        'user_requests' => 0,
        'requests_growth' => 0,
        'booked_requests' => 0,
        'team_members' => 0,
        'total_income' => 0,
        'income_growth' => 0
    ];
    
    // Get total likes
    $sql = "SELECT COUNT(*) as total FROM reviews WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $stats['likes'] = $row['total'];
    }
    
    // Get user requests
    $sql = "SELECT COUNT(*) as total FROM bookings WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $stats['user_requests'] = $row['total'];
    }
    
    // Get booked requests
    $sql = "SELECT COUNT(*) as total FROM bookings WHERE user_id = ? AND status = 'Confirmed'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $stats['booked_requests'] = $row['total'];
    }
    
    // Get team members
    $sql = "SELECT COUNT(*) as total FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $stats['team_members'] = $row['total'];
    }
    
    // Get total income
    $sql = "SELECT SUM(amount) as total FROM payments WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $stats['total_income'] = $row['total'] ? $row['total'] : 0;
    }
    
    return $stats;
}

// Function to get properties
function getProperties($conn, $user_id) {
    $properties = [];
    
    $sql = "SELECT id, title, status, images FROM properties WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    
    return $properties;
}

// Function to get bookings
function getBookings($conn, $user_id) {
    $bookings = [];
    
    $sql = "SELECT b.id, c.name as customerName, p.title as propertyName, b.booking_date as bookingDate, b.status 
           FROM bookings b 
           JOIN users c ON b.user_id = c.id 
           JOIN properties p ON b.property_id = p.id 
           WHERE p.user_id = ? 
           ORDER BY b.booking_date DESC 
           LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    
    return $bookings;
}

// Get data from database
$stats = getStats($conn, $user_id);
$properties = getProperties($conn, $user_id);
$bookings = getBookings($conn, $user_id);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- <link src='../style.css'></link> -->
    <!-- Lucide Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.min.js"></script>
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

        .stats {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
        @media (min-width: 640px) {
            .stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1024px) {
            .stats {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }
        }
        .stat {
            background-color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .stat-figure {
            margin-bottom: 0.5rem;
        }
        .stat-title {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .stat-value {
            font-size: 1.875rem;
            font-weight: 600;
            color: #111827;
        }
        .stat-desc {
            font-size: 0.75rem;
            color: #6b7280;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <?php include './include/navbar.php' ?>

    <!-- Header -->
    <?php include './include/sidebar.php' ?>

     <!-- Main Content -->
     <div class="main-content">
        <div class="main-content-inner">
            <main class="flex-1 bg-gray-100">
                <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <!-- Stats Section -->
                    <div class="stats shadow grid gap-4 mb-8">
                        <!-- Total Likes -->
                        <div class="stat place-items-center">
                            <div class="stat-figure text-primary">
                                <i data-lucide="heart" class="h-8 w-8 text-red-500"></i>
                            </div>
                            <div class="stat-title">Total Likes</div>
                            <div class="stat-value"><?php echo number_format($stats['likes']); ?></div>
                            <div class="stat-desc">↗︎ <?php echo number_format($stats['likes_growth']); ?> this month</div>
                        </div>

                        <!-- Total User Requests -->
                        <div class="stat place-items-center">
                            <div class="stat-figure text-secondary">
                                <i data-lucide="users" class="h-8 w-8 text-blue-500"></i>
                            </div>
                            <div class="stat-title">User Requests</div>
                            <div class="stat-value text-secondary"><?php echo number_format($stats['user_requests']); ?></div>
                            <div class="stat-desc text-secondary">
                                <?php
                                if ($stats['user_requests'] > 0) {
                                    echo "↗︎ Growing steadily";
                                } else {
                                    echo "No requests yet";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Total Booked Requests -->
                        <div class="stat place-items-center">
                            <div class="stat-figure text-accent">
                                <i data-lucide="check-circle" class="h-8 w-8 text-green-500"></i>
                            </div>
                            <div class="stat-title">Booked Requests</div>
                            <div class="stat-value"><?php echo number_format($stats['booked_requests']); ?></div>
                            <div class="stat-desc">
                                <?php
                                if ($stats['booked_requests'] > 0) {
                                    echo "Steady bookings";
                                } else {
                                    echo "No bookings yet";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Total Team -->
                        <div class="stat place-items-center">
                            <div class="stat-figure text-info">
                                <i data-lucide="user-plus" class="h-8 w-8 text-purple-500"></i>
                            </div>
                            <div class="stat-title">Team Members</div>
                            <div class="stat-value"><?php echo number_format($stats['team_members']); ?></div>
                            <div class="stat-desc">
                                <?php
                                if ($stats['team_members'] > 0) {
                                    echo "All active";
                                } else {
                                    echo "No team members yet";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Total Income Balance -->
                        <div class="stat place-items-center">
                            <div class="stat-figure text-success">
                                <i data-lucide="dollar-sign" class="h-8 w-8 text-emerald-500"></i>
                            </div>
                            <div class="stat-title">Total Income</div>
                            <div class="stat-value">$<?php echo number_format($stats['total_income']); ?></div>
                            <div class="stat-desc">↗︎ $<?php echo number_format($stats['income_growth']); ?> last month</div>
                        </div>
                    </div>

                    <!-- Properties Section -->
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Your Properties</h2>
                        
                        <?php if (empty($properties)): ?>
                        <div class="bg-white rounded-lg shadow-md p-6 text-center">
                            <p class="text-gray-500">You haven't added any properties yet.</p>
                            <button class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                                Add Your First Property
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                            <?php foreach ($properties as $property): ?>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:scale-105">
                                <img src="<?php echo !empty($property['image']) ? $property['image'] : 'images/placeholder.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($property['title']); ?>" 
                                     class="w-full h-40 object-cover">
                                <div class="p-4">
                                    <h3 class="font-semibold text-lg mb-2">
                                        <?php echo htmlspecialchars($property['title']); ?>
                                    </h3>
                                    <div class="flex justify-between items-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                                            <?php echo $property['status'] === 'Available' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                                            <?php echo htmlspecialchars($property['status']); ?>
                                        </span>
                                        <button class="text-blue-600 hover:text-blue-800 transition-colors duration-300">
                                            <?php if ($property['status'] === 'Available'): ?>
                                                <i data-lucide="edit" class="h-5 w-5"></i>
                                            <?php else: ?>
                                                <i data-lucide="eye" class="h-5 w-5"></i>
                                            <?php endif; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </section>

                    <!-- Bookings Section -->
                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Booking Requests</h2>
                        
                        <?php if (empty($bookings)): ?>
                        <div class="bg-white shadow-md rounded-lg p-6 text-center">
                            <p class="text-gray-500">No booking requests yet.</p>
                        </div>
                        <?php else: ?>
                        <div class="bg-white shadow-md rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Customer
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Property
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($bookings as $booking): ?>
                                    <tr class="hover:bg-gray-50 transition-colors duration-300">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($booking['customerName']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($booking['propertyName']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($booking['bookingDate']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                <?php 
                                                if ($booking['status'] === 'Confirmed') {
                                                    echo 'bg-green-100 text-green-800';
                                                } elseif ($booking['status'] === 'Pending') {
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                } else {
                                                    echo 'bg-red-100 text-red-800';
                                                }
                                                ?>">
                                                <?php echo htmlspecialchars($booking['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </section>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <?php include './include/footer.php' ?>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
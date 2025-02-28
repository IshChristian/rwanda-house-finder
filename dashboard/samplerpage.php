<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Owner Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar Navigation -->
        <?php
        include "./include/sidebar.php";
        ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <?php
            include "./include/header.php";
            ?>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-100 p-4">
                <!-- Property Overview & Stats -->
                
            </main>
        <?php
        include "./include/footer.php";
        ?>
        </div>
    </div>
</body>
</html>
<?php
// Active menu item (set dynamically if needed)
$activeMenuItem = "Properties";

// Menu items
$menuItems = [
    ["icon" => "fa-home", "label" => "Properties", "path" => "../onwer/"],
    ["icon" => "fa-calendar-check", "label" => "Bookings", "path" => "../onwer/booking.php"],
    ["icon" => "fa-building", "label" => "Property", "path" => "../onwer/property.php"],
    ["icon" => "fa-chart-bar", "label" => "Analytics", "path" => "../onwer/analyse.php"],
    ["icon" => "fa-sign-out-alt", "label" => "Logout", "path" => "../logout.php"]
];
?>

<!-- Sidebar -->
<div class="sidebar fixed left-0 top-0 bottom-0 bg-white shadow-lg p-4 w-64 flex flex-col justify-between">
    <div>
        <div class="h-16 flex items-center justify-center mb-6">
            <h2 class="text-xl font-semibold">Rental Dashboard</h2>
        </div>

        <div class="mt-4">
            <?php foreach ($menuItems as $item) : ?>
                <a href="<?= $item['path']; ?>"
                    class="flex items-center px-6 py-3 rounded-lg transition-colors duration-300 
                        <?= ($activeMenuItem === $item['label']) ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white'; ?>">
                    <i class="fas <?= $item['icon']; ?> h-5 w-5 mr-3"></i>
                    <?= $item['label']; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Optional Footer or other content in the sidebar -->
    <div class="mt-8 text-center">
        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Help</a>
    </div>
</div>

<?php
// Include database connection
include '../config/connection.php';

// Fetch properties based on address and sector filter
$address = isset($_GET['address']) ? $_GET['address'] : '';
$sector = isset($_GET['sector']) ? $_GET['sector'] : '';
$features = isset($_GET['features']) ? $_GET['features'] : [];
$status = isset($_GET['status']) ? $_GET['status'] : '';
$property_type = isset($_GET['property_type']) ? $_GET['property_type'] : '';

// Create SQL query with filters
$sql = "SELECT p.*, COUNT(v.property_id) as view_count 
        FROM properties p 
        LEFT JOIN reviews v ON p.id = v.property_id 
        WHERE 1=1";

if ($address) {
    $sql .= " AND p.address LIKE '%" . $conn->real_escape_string($address) . "%'";
}
if ($sector) {
    $sql .= " AND p.sector LIKE '%" . $conn->real_escape_string($sector) . "%'";
}
if ($property_type) {
    $sql .= " AND p.property_type = '" . $conn->real_escape_string($property_type) . "'";
}
if (!empty($features)) {
    foreach ($features as $feature) {
        $sql .= " AND p.features LIKE '%" . $conn->real_escape_string($feature) . "%'";
    }
}
if ($status) {
    $sql .= " AND p.status = '" . $conn->real_escape_string($status) . "'";
}

$sql .= " GROUP BY p.id";

// If no filters, get popular properties first
if (empty($address) && empty($sector) && empty($features) && empty($status) && empty($property_type)) {
    $popularSql = $sql . " ORDER BY id DESC LIMIT 5";
    $popularResult = $conn->query($popularSql);
    $popularProperties = [];
    
    if ($popularResult && $popularResult->num_rows > 0) {
        while ($row = $popularResult->fetch_assoc()) {
            $popularProperties[] = $row;
        }
    }
    
    // Get all properties sorted by creation date
    $sql .= " ORDER BY p.created_at DESC";
} else {
    $popularProperties = [];
}

// Execute query for all/filtered properties
$result = $conn->query($sql);
$properties = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Property Finder</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
  <style>
    .swiper {
      width: 100%;
      height: 100%;
    }
    .checkbox-container {
      display: flex;
      align-items: center;
      margin-bottom: 8px;
    }
    .checkbox-container input[type="checkbox"] {
      margin-right: 8px;
    }
  </style>
</head>
<body class="bg-gray-100">

  <!-- Navbar -->
  <nav class="top-0 left-0 right-0 z-50 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col">
        <!-- Top Section -->
        <div class="flex items-center justify-between h-16">
          <div class="flex-1 flex items-center">
            <a href="./" class="text-2xl font-medium text-blue-800 hover:text-zinc-600 transition-colors duration-200">
              LiveAtRwanda
            </a>
          </div>
          <div class="flex items-center gap-6">
          <!-- Booking Icon -->
          <a href="booking.php" class="text-blue-800 hover:text-zinc-600 transition-colors duration-200">
            <i class="fas fa-calendar-alt"></i>
          </a>
          <!-- Logout Icon -->
          <a href="../logout.php" class="text-blue-800 hover:text-zinc-600 transition-colors duration-200">
            <i class="fas fa-sign-out-alt"></i>
          </a>
        </div>
        </div>

        <!-- Bottom Section -->
        <div class="flex justify-between items-center py-2 border-t">
          <!-- Left side - Address and Sector Inputs -->
          <div class="flex items-center gap-6">
            <form action="index.php" method="GET" class="flex items-center gap-4">
              <!-- Address Input -->
              <div class="flex items-center gap-2">
                <i class="fas fa-map-pin text-zinc-600"></i>
                <input type="text" name="address" value="<?= htmlspecialchars($address) ?>" placeholder="Address"
                  class="border border-gray-300 rounded-md p-2" />
              </div>
              <!-- Sector Input -->
              <div class="flex items-center gap-2">
                <i class="fas fa-map text-zinc-600"></i>
                <input type="text" name="sector" value="<?= htmlspecialchars($sector) ?>" placeholder="Sector"
                  class="border border-gray-300 rounded-md p-2" />
              </div>
              <?php if ($property_type): ?>
  <input type="hidden" name="property_type" value="<?= htmlspecialchars($property_type) ?>">
  <?php endif; ?>
  <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Search</button>
</form>
          </div>

          <!-- Right side - Accommodation Types -->
<div class="flex items-center gap-6">
  <a href="index.php?property_type=house<?= !empty($address) ? '&address='.urlencode($address) : '' ?><?= !empty($sector) ? '&sector='.urlencode($sector) : '' ?><?= !empty($status) ? '&status='.urlencode($status) : '' ?>" 
     class="flex flex-col items-center gap-1 <?= $property_type == 'house' ? 'text-blue-600 font-semibold' : 'text-zinc-600' ?> hover:text-zinc-900 transition-colors">
    <i class="fas fa-home"></i>
    <span class="text-xs">House</span>
  </a>
  <a href="index.php?property_type=apartment<?= !empty($address) ? '&address='.urlencode($address) : '' ?><?= !empty($sector) ? '&sector='.urlencode($sector) : '' ?><?= !empty($status) ? '&status='.urlencode($status) : '' ?>" 
     class="flex flex-col items-center gap-1 <?= $property_type == 'apartment' ? 'text-blue-600 font-semibold' : 'text-zinc-600' ?> hover:text-zinc-900 transition-colors">
    <i class="fas fa-building"></i>
    <span class="text-xs">Apartment</span>
  </a>
  <a href="index.php?property_type=guest_house<?= !empty($address) ? '&address='.urlencode($address) : '' ?><?= !empty($sector) ? '&sector='.urlencode($sector) : '' ?><?= !empty($status) ? '&status='.urlencode($status) : '' ?>" 
     class="flex flex-col items-center gap-1 <?= $property_type == 'guest_house' ? 'text-blue-600 font-semibold' : 'text-zinc-600' ?> hover:text-zinc-900 transition-colors">
    <i class="fas fa-hotel"></i>
    <span class="text-xs">Guest House</span>
  </a>
  <a href="index.php?property_type=hotel<?= !empty($address) ? '&address='.urlencode($address) : '' ?><?= !empty($sector) ? '&sector='.urlencode($sector) : '' ?><?= !empty($status) ? '&status='.urlencode($status) : '' ?>" 
     class="flex flex-col items-center gap-1 <?= $property_type == 'hotel' ? 'text-blue-600 font-semibold' : 'text-zinc-600' ?> hover:text-zinc-900 transition-colors">
    <i class="fas fa-concierge-bell"></i>
    <span class="text-xs">Hotel</span>
  </a>
</div>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="flex flex-row gap-6 p-6">

    <!-- Left Column: Filters Section -->
    <div class="w-1/4 space-y-6">
      <div class="bg-white shadow-md rounded-lg p-4">
        <h2 class="font-semibold text-lg mb-4">Filter</h2>
        <form action="index.php" method="GET">
          <!-- Hidden inputs to preserve other filter values -->
          <input type="hidden" name="address" value="<?= htmlspecialchars($address) ?>">
          <input type="hidden" name="sector" value="<?= htmlspecialchars($sector) ?>">
          
          <!-- Price Range -->
          <div class="mb-4">
            <label class="block text-sm">Price Range</label>
            <input type="range" name="price_range" min="500" max="5000" step="100" class="w-full mt-2">
            <div class="flex justify-between text-xs px-2">
              <span>$500</span>
              <span>$5000</span>
            </div>
          </div>

          <!-- Bedrooms -->
          <div class="mb-4">
            <label class="block text-sm">Bedrooms</label>
            <select name="bedrooms" class="w-full mt-2 p-2 border border-gray-300 rounded-md">
              <option value="">Any</option>
              <option value="1">1 Bedroom</option>
              <option value="2">2 Bedrooms</option>
              <option value="3">3 Bedrooms</option>
              <option value="4+">4+ Bedrooms</option>
            </select>
          </div>

          <!-- Bathrooms -->
          <div class="mb-4">
            <label class="block text-sm">Bathrooms</label>
            <select name="bathrooms" class="w-full mt-2 p-2 border border-gray-300 rounded-md">
              <option value="">Any</option>
              <option value="1">1 Bathroom</option>
              <option value="2">2 Bathrooms</option>
              <option value="3+">3+ Bathrooms</option>
            </select>
          </div>
          
          <!-- Features Section -->
          <div class="mb-4">
            <label class="block text-sm mb-2">Features</label>
            <div class="space-y-2">
              <div class="checkbox-container">
                <input type="checkbox" id="feature-parking" name="features[]" value="parking" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="feature-parking" class="text-sm text-gray-700">Parking</label>
              </div>
              <div class="checkbox-container">
                <input type="checkbox" id="feature-pool" name="features[]" value="pool" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="feature-pool" class="text-sm text-gray-700">Swimming Pool</label>
              </div>
              <div class="checkbox-container">
                <input type="checkbox" id="feature-garden" name="features[]" value="garden" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="feature-garden" class="text-sm text-gray-700">Garden</label>
              </div>
              <div class="checkbox-container">
                <input type="checkbox" id="feature-security" name="features[]" value="security" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="feature-security" class="text-sm text-gray-700">Security</label>
              </div>
              <div class="checkbox-container">
                <input type="checkbox" id="feature-wifi" name="features[]" value="wifi" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="feature-wifi" class="text-sm text-gray-700">WiFi</label>
              </div>
            </div>
          </div>
          
          <!-- Status Section -->
          <div class="mb-4">
            <label class="block text-sm mb-2">Status</label>
            <div class="space-y-2">
              <div class="checkbox-container">
                <input type="radio" id="status-available" name="status" value="available" class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="status-available" class="text-sm text-gray-700">Available</label>
              </div>
              <div class="checkbox-container">
                <input type="radio" id="status-booked" name="status" value="booked" class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="status-booked" class="text-sm text-gray-700">Booked</label>
              </div>
            </div>
          </div>

          <!-- Submit & Clear Filters Buttons -->
          <div class="flex gap-2">
            <button type="submit" class="w-1/2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
              Apply Filters
            </button>
            <a href="index.php" class="w-1/2 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-center">
              Clear Filters
            </a>
          </div>
        </form>
      </div>
    </div>

    <!-- Right Column: Property Listings -->
    <div class="w-3/4 space-y-6">
      <?php if (!empty($popularProperties)): ?>
      <!-- Popular Properties Section -->
      <div class="bg-white shadow-md rounded-lg p-4">
        <h2 class="font-semibold text-lg mb-4">Most Popular Properties</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <?php foreach ($popularProperties as $property): ?>
            <?php 
              // Parse images from JSON array
              $images = json_decode($property['images'], true) ?: [$property['images']]; 
            ?>
            <div class="rounded-lg overflow-hidden shadow-md bg-white">
              <!-- Property Image Slider -->
              <div class="h-56 relative">
                <div class="swiper propertySwiper h-full">
                  <div class="swiper-wrapper">
                    <?php foreach ($images as $image): ?>
                      <div class="swiper-slide">
                        <img src="<?= htmlspecialchars($image) ?>" alt="Property Image" class="h-full w-full object-cover">
                      </div>
                    <?php endforeach; ?>
                  </div>
                  <div class="swiper-pagination"></div>
                </div>
              </div>
              <!-- Property Details -->
              <div class="p-4">
                <h3 class="font-medium"><?= htmlspecialchars($property['title']) ?></h3>
                <p class="text-sm text-gray-500"><?= htmlspecialchars($property['address']) ?>, <?= htmlspecialchars($property['sector']) ?></p>
                <div class="flex mt-4 gap-2 flex-wrap">
                  <div class="flex items-center gap-1 text-xs text-indigo-700">
                    <i class="fas fa-ruler-combined"></i>
                    <span><?= htmlspecialchars($property['area']) ?> sqft</span>
                  </div>
                  <div class="flex items-center gap-1 text-xs text-indigo-700">
                    <i class="fas fa-bath"></i>
                    <span><?= htmlspecialchars($property['bathrooms']) ?> baths</span>
                  </div>
                  <div class="flex items-center gap-1 text-xs text-indigo-700">
                    <i class="fas fa-bed"></i>
                    <span><?= htmlspecialchars($property['bedrooms']) ?> beds</span>
                  </div>
                </div>
                <div class="flex justify-between mt-4 items-center">
                  <p class="font-semibold text-indigo-700">$<?= htmlspecialchars($property['price']) ?></p>
                  <?php if ($property['status'] == 'available'): ?>
                    <a href="propertyDetails.php?id=<?= $property['id'] ?>" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-md text-sm">
                      Book Now
                    </a>
                  <?php else: ?>
                    <span class="bg-gray-500 text-white px-3 py-1 rounded-md text-sm">
                      Booked
                    </span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- All Properties Section -->
      <div class="bg-white shadow-md rounded-lg p-4">
        <h2 class="font-semibold text-lg mb-4">
          <?= (!empty($address) || !empty($sector)) ? 'Search Results' : 'All Properties' ?>
        </h2>

        <?php if (empty($properties)): ?>
          <p class="text-center py-8">No properties found based on your search.</p>
        <?php else: ?>
          <div class="space-y-4">
            <?php foreach ($properties as $property): ?>
              <?php 
                // Parse images from JSON array
                $images = json_decode($property['images'], true) ?: [$property['images']]; 
              ?>
              <div class="flex bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Property Image Slider -->
                <div class="w-1/3 h-48 relative">
                  <div class="swiper mySwiper h-full">
                    <div class="swiper-wrapper">
                      <?php foreach ($images as $image): ?>
                        <div class="swiper-slide">
                          <img src="<?= htmlspecialchars($image) ?>" alt="Property Image" class="h-full w-full object-cover">
                        </div>
                      <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                  </div>
                </div>
                <!-- Property Details -->
                <div class="w-2/3 p-4">
                  <h3 class="font-medium text-lg"><?= htmlspecialchars($property['title']) ?></h3>
                  <p class="text-gray-500"><?= htmlspecialchars($property['address']) ?>, <?= htmlspecialchars($property['sector']) ?></p>
                  <div class="flex mt-2 gap-4">
                    <div class="flex items-center gap-1 text-sm text-indigo-700">
                      <i class="fas fa-ruler-combined"></i>
                      <span><?= htmlspecialchars($property['area']) ?> sqft</span>
                    </div>
                    <div class="flex items-center gap-1 text-sm text-indigo-700">
                      <i class="fas fa-bath"></i>
                      <span><?= htmlspecialchars($property['bathrooms']) ?> baths</span>
                    </div>
                    <div class="flex items-center gap-1 text-sm text-indigo-700">
                      <i class="fas fa-bed"></i>
                      <span><?= htmlspecialchars($property['bedrooms']) ?> beds</span>
                    </div>
                    <div class="flex items-center gap-1 text-sm text-indigo-700">
                      <i class="fas fa-eye"></i>
                      <span><?= htmlspecialchars($property['view_count']) ?> views</span>
                    </div>
                  </div>
                  <div class="flex justify-between items-center mt-4">
                    <div>
                      <p class="font-semibold text-xl text-indigo-700">$<?= htmlspecialchars($property['price']) ?></p>
                      <p class="text-sm text-gray-500">
                        <?= $property['status'] == 'available' ? 
                            '<span class="text-green-600">Available</span>' : 
                            '<span class="text-red-600">Booked</span>' ?>
                      </p>
                    </div>
                    <?php if ($property['status'] == 'available'): ?>
                      <a href="propertyDetails.php?id=<?= $property['id'] ?>" 
                         class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                        Book Now
                      </a>
                    <?php else: ?>
                      <span class="bg-gray-500 text-white px-4 py-2 rounded-md">
                        Booked
                      </span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <?php include './include/footer.php' ?>

  <!-- Initialize Swiper -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize all swipers
      const swipers = document.querySelectorAll('.swiper');
      swipers.forEach(function(swiperElement) {
        new Swiper(swiperElement, {
          pagination: {
            el: ".swiper-pagination",
            dynamicBullets: true,
          },
          loop: true,
          autoplay: {
            delay: 3000,
          },
        });
      });
    });
  </script>
</body>
</html>
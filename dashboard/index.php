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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-75 text-white mr-4">
                                <i class="fas fa-home text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-700">12</h3>
                                <p class="text-gray-600">Active Listings</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-75 text-white mr-4">
                                <i class="fas fa-key text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-700">8</h3>
                                <p class="text-gray-600">Rented Properties</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-500 bg-opacity-75 text-white mr-4">
                                <i class="fas fa-clock text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-700">3</h3>
                                <p class="text-gray-600">Pending Listings</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-500 bg-opacity-75 text-white mr-4">
                                <i class="fas fa-dollar-sign text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-700">$24,500</h3>
                                <p class="text-gray-600">Total Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Properties List -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-700">Your Properties</h3>
                        <button class="bg-blue-500 text-white px-4 py-2 rounded-md text-sm">Add New Property</button>
                    </div>
                    <div class="overflow-x-auto">
                    <div class="grid grid-cols-4">
                        <div class="prop">
                            <a href="#" class="block rounded-lg p-4 shadow-sm shadow-indigo-100">
                                <img
                                  alt=""
                                  src="https://images.unsplash.com/photo-1613545325278-f24b0cae1224?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80"
                                  class="h-56 w-full rounded-md object-cover"
                                />
                              
                                <div class="mt-2">
                                  <dl>
                                    <div>
                                      <dt class="sr-only">Price</dt>
                              
                                      <dd class="text-sm text-gray-500">$240,000</dd>
                                    </div>
                              
                                    <div>
                                      <dt class="sr-only">Address</dt>
                              
                                      <dd class="font-medium">123 Wallaby Avenue, Park Road</dd>
                                    </div>
                                  </dl>
                              
                                  <div class="mt-6 flex items-center gap-8 text-xs">
                                    <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
                                      <svg
                                        class="size-4 text-indigo-700"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                      >
                                        <path
                                          stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"
                                        />
                                      </svg>
                              
                                      <div class="mt-1.5 sm:mt-0">
                                        <p class="text-gray-500">Parking</p>
                              
                                        <p class="font-medium">2 spaces</p>
                                      </div>
                                    </div>
                              
                                    <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
                                      <svg
                                        class="size-4 text-indigo-700"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                      >
                                        <path
                                          stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"
                                        />
                                      </svg>
                              
                                      <div class="mt-1.5 sm:mt-0">
                                        <p class="text-gray-500">Bathroom</p>
                              
                                        <p class="font-medium">2 rooms</p>
                                      </div>
                                    </div>
                              
                                    <div class="sm:inline-flex sm:shrink-0 sm:items-center sm:gap-2">
                                      <svg
                                        class="size-4 text-indigo-700"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                      >
                                        <path
                                          stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                                        />
                                      </svg>
                              
                                      <div class="mt-1.5 sm:mt-0">
                                        <p class="text-gray-500">Bedroom</p>
                              
                                        <p class="font-medium">4 rooms</p>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </a>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- Calendar & Messages -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Calendar -->
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Upcoming Appointments</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-day text-blue-500 mr-3"></i>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Property Viewing</h4>
                                        <p class="text-sm text-gray-600">123 Main Street</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-800">Today</div>
                                    <div class="text-sm text-gray-600">2:00 PM</div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-user-friends text-green-500 mr-3"></i>
                                    <div>
                                        <h4 class="font-medium text-gray-800">Client Meeting</h4>
                                        <p class="text-sm text-gray-600">456 Oak Avenue</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-800">Tomorrow</div>
                                    <div class="text-sm text-gray-600">10:00 AM</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent Messages</h3>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <img src="https://via.placeholder.com/40" alt="User" class="w-10 h-10 rounded-full">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-medium text-gray-800">Sarah Johnson</h4>
                                        <span class="text-sm text-gray-500">1h ago</span>
                                    </div>
                                    <p class="text-sm text-gray-600">Interested in viewing the property at 123 Main Street...</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <img src="https://via.placeholder.com/40" alt="User" class="w-10 h-10 rounded-full">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-medium text-gray-800">Mike Smith</h4>
                                        <span class="text-sm text-gray-500">3h ago</span>
                                    </div>
                                    <p class="text-sm text-gray-600">When would be a good time to discuss the lease terms?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        <?php
        include "./include/footer.php";
        ?>
        </div>
    </div>
</body>
</html>
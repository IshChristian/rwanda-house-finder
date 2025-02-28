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
                <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Property - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js for interactivity -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50" x-data="{ showDeleteModal: false, showImageModal: false, currentImage: '' }">

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header with Actions -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Luxury Downtown Apartment</h2>
                <p class="text-gray-600">Property ID: PRO123456</p>
            </div>
            <div class="flex space-x-4">
                <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                    Back to Listings
                </button>
                <button class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700">
                    Edit Property
                </button>
                <button @click="showDeleteModal = true" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700">
                    Delete Property
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Image Gallery Card -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Property Images</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="aspect-w-4 aspect-h-3 cursor-pointer" @click="showImageModal = true; currentImage = 'https://via.placeholder.com/800x600'">
                                <img src="https://via.placeholder.com/800x600" alt="Property 1" class="object-cover rounded-lg hover:opacity-75 transition">
                            </div>
                            <div class="aspect-w-4 aspect-h-3 cursor-pointer" @click="showImageModal = true; currentImage = 'https://via.placeholder.com/800x600'">
                                <img src="https://via.placeholder.com/800x600" alt="Property 2" class="object-cover rounded-lg hover:opacity-75 transition">
                            </div>
                            <div class="aspect-w-4 aspect-h-3 cursor-pointer" @click="showImageModal = true; currentImage = 'https://via.placeholder.com/800x600'">
                                <img src="https://via.placeholder.com/800x600" alt="Property 3" class="object-cover rounded-lg hover:opacity-75 transition">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Property Details Card -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Property Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Property Type</p>
                                <p class="mt-1 text-sm text-gray-900">Residential</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Price</p>
                                <p class="mt-1 text-sm text-gray-900">$2,500/month</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Size</p>
                                <p class="mt-1 text-sm text-gray-900">1,200 sq ft</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Available From</p>
                                <p class="mt-1 text-sm text-gray-900">January 1, 2024</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Condition</p>
                                <p class="mt-1 text-sm text-gray-900">Well-maintained</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Available
                                </span>
                            </div>
                        </div>
                        <div class="mt-6">
                            <p class="text-sm font-medium text-gray-500">Description</p>
                            <p class="mt-1 text-sm text-gray-900">
                                Luxurious downtown apartment featuring modern amenities and stunning city views. Recently renovated with high-end finishes throughout. Perfect for professionals or small families looking for an urban lifestyle.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Features & Amenities Card -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Features & Amenities</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="text-sm text-gray-600">2 Bedrooms</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="text-sm text-gray-600">2 Bathrooms</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="text-sm text-gray-600">1 Parking Spot</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span class="text-sm text-gray-600">Air Conditioning</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span class="text-sm text-gray-600">Heating</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span class="text-sm text-gray-600">Internet</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Owner Information Card -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Owner Information</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Owner Name</p>
                                <p class="mt-1 text-sm text-gray-900">John Doe</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email</p>
                                <p class="mt-1 text-sm text-gray-900">john.doe@example.com</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Phone</p>
                                <p class="mt-1 text-sm text-gray-900">+1 (555) 123-4567</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Preferred Contact</p>
                                <p class="mt-1 text-sm text-gray-900">Email</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Card -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Analytics</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Views</p>
                                <p class="mt-1 text-xl font-semibold text-gray-900">1,234</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Inquiries</p>
                                <p class="mt-1 text-xl font-semibold text-gray-900">56</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Saved by Users</p>
                                <p class="mt-1 text-xl font-semibold text-gray-900">89</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-10 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteModal" @click="showDeleteModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Delete Property
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to delete this property? This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                    <button type="button" @click="showDeleteModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div x-show="showImageModal" class="fixed inset-0 z-10 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showImageModal" @click="showImageModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button type="button" @click="showImageModal = false" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="sm:flex sm:items-start">
                    <img :src="currentImage" alt="Property" class="w-full h-auto">
                </div>
            </div>
        </div>
    </div>
</
            </main>
        <?php
        include "./include/footer.php";
        ?>
        </div>
    </div>
</body>
</html>
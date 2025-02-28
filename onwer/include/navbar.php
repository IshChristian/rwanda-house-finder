<!-- Header -->
<div class="w-full bg-white shadow-md fixed top-0 left-0 right-0 z-10">
    <div class="navbar px-4 lg:px-10 flex justify-between items-center h-16">
        
        <!-- Left: Project Name -->
        <div class="text-2xl font-bold text-gray-800 antialiased">
            LiveAtRwanda
        </div>

        <!-- Center: Menu -->
        <div class="flex space-x-4">
            <a class="text-gray-600 hover:text-gray-800" href="#">Home</a>
            <a class="text-gray-600 hover:text-gray-800" href="#">About</a>
            
            <!-- Dropdown Menu -->
            <div class="relative group">
                <button class="text-gray-600 hover:text-gray-800 flex items-center">
                    Services <i class="fas fa-chevron-down ml-1"></i>
                </button>
                
                <!-- Dropdown Content -->
                <ul class="absolute hidden group-hover:block bg-white shadow-lg rounded-md w-40 mt-2">
                    <li><a class="block px-4 py-2 hover:bg-gray-200" href="#">Service 1</a></li>
                    <li><a class="block px-4 py-2 hover:bg-gray-200" href="#">Service 2</a></li>
                    <li><a class="block px-4 py-2 hover:bg-gray-200" href="#">Service 3</a></li>
                </ul>
            </div>
        </div>

        <!-- Right: Notification and Profile -->
        <div class="flex items-center space-x-4">
            <!-- Notification Bell -->
            <button class="relative">
                <i class="fas fa-bell text-gray-600 hover:text-gray-800 text-lg"></i>
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                    3
                </span>
            </button>
        </div>
    </div>
</div>

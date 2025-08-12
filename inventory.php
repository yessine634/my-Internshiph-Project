



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile & Inventory</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles/project_styles.css">
</head>
<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg rounded-r-lg flex flex-col overflow-y-auto sidebar">
        <div class="p-4 border-b border-gray-200">
            <button class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold shadow-md hover:bg-blue-600 transition duration-300">
                <img src="logo/artibedded_logo.webp" alt="">
            </button>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="homepage.php" id="home-link" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition duration-200 ">
                <i class="fas fa-home mr-3 text-lg"></i>
                <span class="font-medium">Home</span>
            </a>

            <!-- Inventory Link (no longer a dropdown toggle) -->
            <a href="#" id="inventory-link" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition duration-200 active">
                <i class="fas fa-boxes mr-3 text-lg"></i>
                <span class="font-medium">Inventory</span>
            </a>

            <a href="cart.php" id="cart-link" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition duration-200">
                <i class="fas fa-shopping-cart mr-3 text-lg"></i>
                <span class="font-medium">Cart</span>
            </a>
            <a href="logout.php" id="logout-link" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-red-100 hover:text-red-700 transition duration-200">
                <i class="fas fa-sign-out-alt mr-3 text-lg"></i>
                <span class="font-medium">Logout</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col p-6 overflow-y-auto">
        <!-- Top Bar -->
        <header class="bg-blue-500 text-white p-4 rounded-lg shadow-md mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold" id="main-header-title">Inventory</h1>
            <!-- Placeholder for potential right-side elements like user avatar/settings -->
            <div></div>
        </header>
        <section id="inventory-menu-content" class="bg-white p-8 rounded-lg shadow-md flex-1">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Inventory</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="categories-grid">
                        </div>
                    </section>
        </main>

    <script src="scripts/inventory.js"></script>
</body>
</html>

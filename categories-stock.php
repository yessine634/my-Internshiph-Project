<?php
session_start();

$success = $_SESSION['success'] ?? null;  // Get the message if set
unset($_SESSION['success']);

require 'vendor/autoload.php'; // MongoDB composer package

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$client = new Client("mongodb://localhost:27017");
$db = $client->elect_lab;
$categories = $db->categories;
$items = $db->items;

$category = null;
$category_items = [];

if (isset($_GET['id'])) {
    try {
        $category_id = new ObjectId($_GET['id']); // convert string to ObjectId

        // Fetch the category
        $category = $categories->findOne(['_id' => $category_id]);

        // Fetch all items with matching category_id
        $cursor = $items->find(['category_id' => $category_id]);
        $category_items = iterator_to_array($cursor);

    } catch (Exception $e) {
        echo "Invalid ID format.";
    }
}

$search = $_GET['search'] ?? null;

if ($search) {
    $search = strtolower($search);
    $category_items = array_filter($category_items, function($item) use ($search) {
        return 
            strpos(strtolower($item['name']), $search) !== false ||
            strpos(strtolower($item['description']), $search) !== false;
    });
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Components</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles/categories-stock.css">
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
            <a href="homepage.php" id="home-link" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition duration-200">
                <i class="fas fa-home mr-3 text-lg"></i>
                <span class="font-medium">Home</span>
            </a>

            <a href="inventory.php" id="inventory-link" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition duration-200 active">
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
            <div></div>
        </header>
        <?php if ($success): ?>
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded shadow">
            <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
               
        <!-- Category Components Content -->
        <section class="bg-white p-8 rounded-lg shadow-md flex-1">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6"><?php echo $category ? htmlspecialchars($category['name']) : "Unknown Category"; ?>
             </h2>

            <!-- Tabs for All, Stock, Worth -->
            <?php if (empty($_GET['search'])): ?>
                <div class="flex border-b border-gray-200 mb-6">
                    <button class="tab-button px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-700 focus:outline-none active-tab" data-tab="all">All</button>
                </div>
            <?php endif; ?>

            <!-- Search Input -->
            <form method="GET" action="" class="flex items-center justify-end mb-6">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
                <div class="relative flex items-center w-full max-w-xs">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="search"
                        value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <i class="fas fa-search absolute left-3 text-gray-400"></i>
                </div>
            </form>

            <!-- Inventory Table -->
            <div class="overflow-x-auto rounded-lg shadow-md">
                 <table class="w-full table-auto border border-gray-300">
                    <thead>
                        <tr class="bg-blue-100 text-left">
                            <th class="border px-4 py-2">Buy</th>
                            <th class="border px-4 py-2">Name</th>
                            <th class="border px-4 py-2">Stock</th>
                            <th class="border px-4 py-2">Price</th>
                            <th class="border px-4 py-2">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($category_items as $item): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border px-4 py-2">
                                <form method="POST" action="cart.php" class="flex items-center">
                                    <input type="hidden" name="item_id" value="<?php echo $item['_id']; ?>">
                                    <input type="number" name="buy_qty" min="1" max="<?php echo $item['quantity']; ?>" class="w-16 border rounded px-2 py-1 text-sm" placeholder="0">
                                    <button type="submit" class="ml-2 px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($item['name']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars(number_format($item['price'], 2)); ?> TND</td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($item['description']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
      </table>
            </div>
        </section>
    </main>

   
</body>
</html>

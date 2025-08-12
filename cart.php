<?php
session_start();
require 'vendor/autoload.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;
$client = new Client("mongodb://localhost:27017");
$db = $client->elect_lab;
$items = $db->items;



if ($_SERVER['REQUEST_METHOD']==='POST'){
    $_item_id= $_POST['item_id'] ?? null;
    $buy_qty = intval($_POST['buy_qty']);
    if ($_item_id && $buy_qty > 0){
        $item = $items->findOne(['_id' => new ObjectId($_item_id)]);
        if ($item){
            if ($buy_qty > $item['quantity']) {
                $_SESSION['success'] = "Only {$item['quantity']} left in stock for {$item['name']}.";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
            $cart_item = [
                'id'=> (string) $item['_id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $buy_qty,
            ];
        // if cart array doesn't create yet create it
        // with session (Super Global variable) we can access cart array through all pages
            if (!isset($_SESSION['cart'])){
                $_SESSION['cart'] = [];
            }
        $is_exist = false;
        foreach ($_SESSION['cart'] as &$search_item ){
            if ($search_item['id'] === $cart_item['id']){
                $search_item['quantity'] += $cart_item['quantity'];
                $is_exist = true;
               

                break;
            }
        }

        // if item not exist in cart array, add it
        // otherwise increase quantity of existing item ( like we did above)
        if (!$is_exist) {
            $_SESSION['cart'][] = $cart_item;
            
        }
        $_SESSION['success'] = "{$buy_qty} × {$item['name']} added to cart.";
         $items->updateOne(
                    ['_id' => new ObjectId($_item_id)],
                    ['$inc' => ['quantity' => -$buy_qty]]
                    );

        }
    }


}
// quantity update and delete
if (isset($_POST['update_index']) && isset($_POST['new_qty'])) {
    $index = intval($_POST['update_index']);
    $new_qty = intval($_POST['new_qty']);
    if (isset($_SESSION['cart'][$index])) {
        $item = $_SESSION['cart'][$index];
        $old_qty = $item['quantity'];
        $diff = $new_qty - $old_qty;

        if ($diff !== 0) {
            $items->updateOne(
                ['_id' => new ObjectId($item['id'])],
                ['$inc' => ['quantity' => -$diff]]
            );
        }

        if ($new_qty <= 0) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
            $_SESSION['success'] = "{$item['name']} removed from cart.";
        } else {
            $_SESSION['cart'][$index]['quantity'] = $new_qty;
            $_SESSION['success'] = "Updated {$item['name']} to quantity {$new_qty}.";
        }
    }

    
}

if (isset($_POST['delete_index'])){
    $index = intval($_POST['delete_index']);

    if (isset($_SESSION['cart'][$index])) {
        $deleted = $_SESSION['cart'][$index];

        // Restore stock in MongoDB
        $items->updateOne(
            ['_id' => new ObjectId($deleted['id'])],
            ['$inc' => ['quantity' => $deleted['quantity']]]
        );

        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex array
        $_SESSION['success'] = "{$deleted['name']} removed from cart.";
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;

}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

?>



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
            <a href="inventory.php" id="inventory-link" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition duration-200">
                <i class="fas fa-boxes mr-3 text-lg"></i>
                <span class="font-medium">Inventory</span>
            </a>

            <a href="#" id="cart-link" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition duration-200 active">
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
        <header class="bg-blue-500 text-white p-4 rounded-lg shadow-md mb-6 flex items-center justify-between ">
            <h1 class="text-2xl font-bold" id="main-header-title">Cart</h1>
            <!-- Placeholder for potential right-side elements like user avatar/settings -->
            <div></div>
        </header>
        <?php
        $cart = $_SESSION['cart'] ?? [];
        $total = 0;

        
        ?>

    <?php if (count($cart) > 0): ?>
        <section class="bg-white p-6 rounded-lg shadow-md">
            <table class="w-full table-auto border border-gray-300">
                <thead>
                    <tr class="bg-blue-100 text-left">
                        <th class="border px-4 py-2">Name</th>
                        <th class="border px-4 py-2">Quantity</th>
                        <th class="border px-4 py-2">Price (TND)</th>
                        <th class="border px-4 py-2">Subtotal</th>
                        <th class="border px-4 py-2">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $index => $item): 
                        $subtotal = $item['quantity'] * $item['price'];
                        $total += $subtotal;
                    ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($item['name']); ?></td>
                            <td class="border px-4 py-2">
                                <form method="POST" action="cart.php" class="flex items-center space-x-2">
                                    <input type="hidden" name="update_index" value="<?php echo $index; ?>">
                                    <input type="number" name="new_qty" min="0" class="w-16 border rounded px-2 py-1 text-sm" value="<?php echo $item['quantity']; ?>">
                                    <button type="submit" class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm">
                                        Update
                                    </button>
                                </form>
                            </td>
                            <td class="border px-4 py-2"><?php echo number_format($item['price'], 2); ?></td>
                            <td class="border px-4 py-2"><?php echo number_format($subtotal, 2); ?></td>
                            <td class="border px-4 py-2">
                            
                            <form method="POST" action="cart.php" onsubmit="return confirm('Remove this item from cart?');">
                                <input type="hidden" name="delete_index" value="<?php echo $index; ?>">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-semibold bg-gray-100">
                        <td colspan="3" class="border px-4 py-2 text-right">Total:</td>
                        <td class="border px-4 py-2"><?php echo number_format($total, 2); ?> TND</td>
                    </tr>
                </tfoot>
            </table>
        </section>
    <?php else: ?>
        <section class="bg-white p-8 rounded-lg shadow-md">
            <p class="text-gray-700">Your cart is currently empty.</p>
            <p class="mt-4 text-gray-600">Add items to your cart from the inventory.</p>
        </section>
    <?php endif; ?>

    

       
    </main>

    <script src="scripts/project_scripts.js"></script>
</body>
</html>

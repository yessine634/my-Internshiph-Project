<?php
// import MongoDB connection
include 'database.php';

// Set response type to JSON
header('Content-Type: application/json');

// Fetch categories from MongoDB
$cursor = $collection_cat->find([], ['projection' => ['name' => 1, 'description' => 1]]);

$categories = [];
foreach ($cursor as $document) {
    $categories[] = [
        'id' => (string)$document['_id'], // Convert ObjectId to string
        'name' => $document['name'],
        'description' => $document['description']
    ];
}

// Return JSON-encoded categories
echo json_encode($categories);
?>

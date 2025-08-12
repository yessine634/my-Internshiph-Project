<?php
require 'vendor/autoload.php'; 

$client = new MongoDB\Client("mongodb://localhost:27017");


$db = $client->elect_lab;
$collection = $db->users;
$collection_cat = $db->categories;
?>

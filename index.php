<?php
require 'menu.php';

$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text        = $_POST["text"];

// Instantiate the BlogUSSD class and handle user input
$blogUSSD = new BlogUSSD();
$response = $blogUSSD->handleUserInput($text, $phoneNumber);

// Output the response to the user
header('Content-type: text/plain');
echo $response;
?>

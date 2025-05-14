<?php
require_once 'util.php';
require_once 'config.php';
require_once 'Menu.php';

$text = $_POST['text'] ?? '';
$sessionId = $_POST['sessionId'] ?? '';
$phoneNumber = $_POST['phoneNumber'] ?? '';

$menu = new Menu($text, $sessionId, $phoneNumber, $conn);
$input = explode("*", $menu->middleWare($text));

if ($text == "") {
    $menu->mainMenu();
} else {
    switch ($input[0]) {
        case "1":
            $menu->menuSubscribe($input);
            break;
        case "2":
            $menu->menuUnsubscribe($input);
            break;
        case "3":
            $menu->menuRateBlog($input);
            break;
        case "4":
            $menu->menuSuggestTopic($input);
            break;
        case "5":
            $menu->menuFeedback($input);
            break;
        default:
            echo "END Invalid option.";
            break;
    }
}
?>

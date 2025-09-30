<?php
session_start();

$siteId = $_GET['site_id'] ?? 0;

if (isset($_SESSION['cart'][$siteId])) {
    unset($_SESSION['cart'][$siteId]);
}

// Go back to cart
header("Location: /linkbuildings/cart/cart.php");
exit;

<?php
// cart_action.php
require_once 'config/database.php';
require_once 'includes/cart.php';

header('Content-Type: application/json');

$cart = new Cart();
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$response = ['success' => false];

switch ($action) {
    case 'add':
        $productId = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if ($productId && $cart->add($productId, $quantity)) {
            $response['success'] = true;
            $response['message'] = 'Produit ajouté';
        }
        break;

    case 'update':
        $productId = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if ($productId && $cart->update($productId, $quantity)) {
            $response['success'] = true;
        }
        break;

    case 'remove':
        $productId = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
        if ($productId && $cart->remove($productId)) {
            $response['success'] = true;
        }
        break;

    case 'count':
        $response['success'] = true;
        $response['count'] = $cart->getCount();
        break;
}

echo json_encode($response);
?>

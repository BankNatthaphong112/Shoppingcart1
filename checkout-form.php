<?php
session_start();
include 'config.php';

$now = date('Y-m-d H:i:s');
$query = mysqli_query($conn, "INSERT INTO orders (order_date, fullname, email, tel, grand_total) VALUES ('{$now}', '{$_POST['fullname']}','{$_POST['email']}', '{$_POST['tel']}', '{$_POST['grand_total']}')") or die ('query failed'); 

if($query) {
    $last_id = mysqli_insert_id($conn);
    foreach($_SESSION['cart'] as $productId => $productQty) {
        $product_name = $_POST['product'][$productId]['name'];
        $price = $_POST['product'][$productId]['price'];
        $total = $price * $productQty;

        mysqli_query($conn, "INSERT INTO order_details (order_id, product_id, product_name, price, quantity, total) 
        VALUES ('{$last_id}', '{$productId}','{$product_name}', '{$price}', '{$productQty}', '{$total}')") or die ('query failed'); 
    }
    unset($_SESSION['cart']);
    $_SESSION['message'] = 'Checkout order success';
    header('location:' . $base_url . '/checkout-success.php');
}else{
        $_SESSION['message'] = 'Checkout not complete!!!';
        header('location: ' . $base_url . '/checkout-success.php');
}

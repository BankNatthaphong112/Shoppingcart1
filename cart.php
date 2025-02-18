<?php
    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    session_start();
    include 'config.php';

    // Check if the session cart exists and is not empty
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        $_SESSION['message'] = 'Your cart is empty!';
        header('Location: index.php');
        exit;
    }

    // Collect product IDs from the session cart
    $productIds = [];
    foreach ($_SESSION['cart'] as $cardId => $cardQty) {
        $productIds[] = (int)$cardId; // Ensure product IDs are integers
    }

    // If there are no product IDs in the cart, show an error message
    if (count($productIds) == 0) {
        $_SESSION['message'] = 'Your cart is empty!';
        header('Location: index.php');
        exit;
    }

    // Create a comma-separated list of product IDs for the query
    $ids = implode(',', $productIds);

    // Query to fetch product details from the database
    $query = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");

    // Check if the query was successful
    if (!$query) {
        $_SESSION['message'] = 'Failed to fetch product details: ' . mysqli_error($conn);
        header('Location: index.php');
        exit;
    }

    $rows = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    
    <!-- Bootstrap CSS -->
    <link href="<?php echo $base_url; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CSS -->
    <link href="<?php echo $base_url; ?>/assets/fontawesome/css/fontawesome.min.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>/assets/fontawesome/css/brands.min.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>/assets/fontawesome/css/solid.min.css" rel="stylesheet">
</head>
   <body class="bg-primary-subtle">
    <?php include 'include/menu.php'; ?>
    <div class="container" style="margin-top: 30px;">
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-warning alert-dismissable fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <h4>Cart</h4>
        <div class="row">
            <div class="col-12">
                <form action="<?php echo $base_url; ?>/cart-update.php" method="post">
                    <table class="table table-bordered border-info">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Image</th>
                                <th>Product Name</th>
                                <th style="width: 200px;">Price</th>
                                <th style="width: 100px;">Quantity</th>
                                <th style="width: 200px;">Total</th>
                                <th style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($rows > 0): ?>
                                <?php while ($product = mysqli_fetch_assoc($query)): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($product['profile_image'])): ?>
                                                <img src="<?php echo $base_url; ?>/upload_image/<?php echo $product['profile_image']; ?>" width="100" alt="Product Image">
                                            <?php else: ?>
                                                <img src="<?php echo $base_url; ?>assets/images/no-image.png" width="100" alt="Product Image">
                                            <?php endif; ?> 
                                        </td>
                                        <td>
                                            <?php echo $product['product_name']; ?>
                                            <div>
                                                <small class="text-muted"><?php echo nl2br($product['detail']); ?></small>
                                            </div>
                                        </td>
                                        <td><?php echo number_format($product['price'], 2); ?></td>
                                        <td><input type="number" name="product[<?php echo $product['id']; ?>][quantity]" value="<?php echo $_SESSION['cart'][$product['id']]; ?>" class="form-control"></td>
                                        <td><?php echo number_format($product['price'] * $_SESSION['cart'][$product['id']], 2); ?></td>
                                        <td>
                                            <a onclick="return confirm('Are you sure you want to delete?');" role="button" href="<?php echo $base_url; ?>/cat-delete.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-danger"><i class="fa-solid fa-trash me-1"></i>Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <tr>
                                    <td colspan="6" class="text-end">
                                        <button type="submit" class="btn btn-lg btn-success">Update Cart</button>
                                        <a href="<?php echo $base_url;?>/checkout.php" class="btn btn-lg btn-primary">Checkout Order</a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">
                                        <h4 class="text-center text-danger">No items in your cart</h4>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="<?php echo $base_url; ?>/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

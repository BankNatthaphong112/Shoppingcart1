<?php
session_start();
include 'config.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['message'] = 'Your cart is empty!';
    header('Location: cart.php');
    exit;
}

$productIds = array_keys($_SESSION['cart']);
$productIds = array_map('intval', $productIds);
$ids = implode(', ', $productIds);

$query = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");

if (!$query) {
    die('Query failed: ' . mysqli_error($conn));
}

$rows = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="<?php echo $base_url; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
</head>
   <body class="bg-primary-subtle">
    <?php include 'include/menu.php'; ?>
    
    <div class="container" style="margin-top: 30px;">
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <h4 class="mb-3">Checkout</h4>
        <form action="<?php echo $base_url; ?>/checkout-form.php" method="post">
            <div class="row g-5">
                <div class="col-md-6 col-lg-7">
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="fullname" class="form-control" required>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Tel</label>
                            <input type="text" name="tel" class="form-control" required>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="text-end">
                        <a href="<?php echo $base_url; ?>/product-list.php" class="btn btn-secondary btn-lg">Back to Product</a>
                        <button class="btn btn-primary btn-lg" type="submit">Continue to Checkout</button>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5 order-md-last">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-primary">Your cart</span>
                        <span class="badge bg-primary rounded-pill"><?php echo $rows; ?></span>
                    </h4>

                    <?php if ($rows > 0): ?>
                        <ul class="list-group mb-3">
                            <?php $grand_total = 0; ?>
                            <?php while ($product = mysqli_fetch_assoc($query)): ?>
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">
                                            <?php echo htmlspecialchars($product['product_name']); ?>
                                            (<?php echo intval($_SESSION['cart'][$product['id']]); ?>)
                                        </h6>
                                        <small class="text-body-secondary"><?php echo nl2br($product['detail']); ?></small>
                                        <input type="hidden" name="product[<?php echo $product['id']; ?>][price]" value="<?php echo floatval($product['price']); ?>">
                                        <input type="hidden" name="product[<?php echo $product['id']; ?>][name]" value="<?php echo htmlspecialchars($product['product_name']); ?>">
                                    </div>
                                    <span class="text-body-secondary">
                                        ฿<?php echo number_format($_SESSION['cart'][$product['id']] * $product['price'], 2); ?>
                                    </span>
                                </li>
                                <?php $grand_total += $_SESSION['cart'][$product['id']] * $product['price']; ?>
                            <?php endwhile; ?>
                            <li class="list-group-item d-flex justify-content-between bg-body-tertiary">
                                <div class="text-success">
                                    <h6 class="my-0">Grand Total</h6>
                                    <small>amount</small>
                                </div>
                                <span class="text-success"><strong>฿<?php echo number_format($grand_total, 2); ?></strong></span>
                            </li>
                        </ul>
                        <input type="hidden" name="grand_total" value="<?php echo $grand_total; ?>">
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <script src="<?php echo $base_url; ?>/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
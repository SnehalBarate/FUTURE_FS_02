<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Clear the cart after a successful order simulation
unset($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Success | Mini Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container text-center">
        <div class="card shadow border-0 p-5 mx-auto" style="max-width: 500px; border-radius: 20px;">
            <div class="mb-4">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
            </div>
            <h2 class="fw-bold">Order Placed!</h2>
            <p class="text-muted">Thank you, <strong><?php echo $_SESSION['user']; ?></strong>. Your order has been simulated successfully.</p>
            <hr>
            <p class="small text-secondary">Order ID: #<?php echo rand(10000, 99999); ?></p>
            <a href="index.php" class="btn btn-primary px-5 py-2 mt-3 fw-bold shadow-sm">Back to Store</a>
        </div>
    </div>
</body>
</html>
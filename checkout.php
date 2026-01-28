<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 550px;">
        <div class="card shadow border-0 p-4">
            <h3 class="text-center mb-4 text-primary fw-bold">Checkout</h3>
            <form onsubmit="alert('Success! Your order has been placed simulation.'); return true;" action="index.php">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" required placeholder="Enter your full name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" required placeholder="name@example.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping Address</label>
                    <textarea class="form-control" required rows="3" placeholder="123 Street, City, Country"></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label">Payment Method</label>
                    <select class="form-select" required>
                        <option value="">Select a method...</option>
                        <option>Credit/Debit Card</option>
                        <option>Cash on Delivery</option>
                        <option>PayPal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Place Order</button>
                <a href="cart.php" class="btn btn-link w-100 mt-2 text-decoration-none text-muted">Return to Cart</a>
            </form>
        </div>
    </div>
</body>
</html>
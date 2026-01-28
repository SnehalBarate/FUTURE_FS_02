<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$u_email = $_SESSION['user'];

// 1. Remove from Wishlist Logic
if (isset($_GET['remove'])) {
    $wid = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM wishlist WHERE id = '$wid' AND user_email = '$u_email'");
    header("location:wishlist.php?removed=1");
    exit();
}

// 2. Get Counts for Navbar
$wish_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM wishlist WHERE user_email='$u_email'");
$wish_count = mysqli_fetch_assoc($wish_res)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist | Mini Store Elite</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root { --primary: #0066cc; --light-gray: #f5f5f7; --dark: #1d1d1f; }
        body { background-color: var(--light-gray); font-family: 'SF Pro Display', sans-serif; }

        /* Navbar Style */
        .navbar { background: rgba(255, 255, 255, 0.8) !important; backdrop-filter: blur(15px); border-bottom: 1px solid rgba(0,0,0,0.05); }
        
        /* Wishlist Card UI */
        .wish-card { background: #fff; border-radius: 24px; border: none; padding: 20px; transition: 0.3s; margin-bottom: 20px; }
        .wish-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }
        .product-thumb { width: 100px; height: 100px; object-fit: contain; }

        .btn-cart { background: var(--dark); color: white; border-radius: 12px; font-weight: 600; border: none; padding: 10px 20px; font-size: 0.9rem; }
        .btn-remove { background: #fff0f0; color: #dc3545; border-radius: 12px; font-weight: 600; border: none; padding: 10px 15px; font-size: 0.9rem; }
        .btn-remove:hover { background: #dc3545; color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top px-lg-5">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-cpu-fill text-primary me-2"></i>MINI STORE</a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="index.php" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">Continue Shopping</a>
                <a href="cart.php" class="text-dark position-relative me-2">
                    <i class="bi bi-cart-fill fs-4 text-primary"></i>
                    <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle">
                        <?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
                    </span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold mb-0">My Wishlist <span class="text-muted fs-5">(<?php echo $wish_count; ?>)</span></h2>
                </div>

                <?php
                // Fetch Wishlisted Products
                $query = "SELECT wishlist.id as wid, products.* FROM wishlist 
                          JOIN products ON wishlist.product_id = products.id 
                          WHERE wishlist.user_email = '$u_email' ORDER BY wishlist.id DESC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) { ?>
                        <div class="wish-card shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-4">
                                <img src="images/<?php echo $row['image']; ?>" class="product-thumb">
                                <div>
                                    <h5 class="fw-bold mb-1"><?php echo $row['name']; ?></h5>
                                    <h5 class="text-primary fw-bold mb-0">₹<?php echo number_format($row['price'], 2); ?></h5>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <form method="POST" action="index.php">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                    <button name="add_to_cart" type="submit" class="btn-cart">
                                        <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                    </button>
                                </form>
                                <a href="wishlist.php?remove=<?php echo $row['wid']; ?>" class="btn-remove">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php }
                } else { ?>
                    <div class="text-center py-5 bg-white rounded-5 shadow-sm mt-4">
                        <i class="bi bi-heart text-muted opacity-25" style="font-size: 5rem;"></i>
                        <h4 class="fw-bold text-muted mt-3">Your wishlist is empty</h4>
                        <p class="text-muted">Save items you want to see them here.</p>
                        <a href="index.php" class="btn btn-primary rounded-pill px-5 py-2 mt-2 fw-bold">Explore Gadgets</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script>
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('removed')) { Toast.fire({ icon: 'success', title: 'Removed from Wishlist' }); }
    </script>
</body>
</html>
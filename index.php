<?php
/**
 * Mini Store - Elite Tech Edition
 * Internship Project: E-commerce Website with PHP/MySQL
 */
session_start();

// Suppress notices and warnings for a cleaner production look
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

// Initialize session arrays to prevent 'undefined index' notices
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['recently_viewed'])) {
    $_SESSION['recently_viewed'] = [];
}

// Redirect to login if user session is not active
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// --- FEATURE 1: Add to Cart Logic ---
if(isset($_POST['add_to_cart'])) {
    $id = $_POST['product_id'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    header("location:index.php?added=1"); // Redirect with success parameter
    exit();
}

// --- FEATURE 2: Wishlist Toggle Logic (Add/Remove) ---
if(isset($_POST['add_to_wishlist'])) {
    $p_id = $_POST['product_id'];
    $u_email = $_SESSION['user'];
    
    // Check if item exists in database wishlist
    $check = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_email='$u_email' AND product_id='$p_id'");
    
    if(mysqli_num_rows($check) == 0) {
        // Add to wishlist
        mysqli_query($conn, "INSERT INTO wishlist (user_email, product_id) VALUES ('$u_email', '$p_id')");
        header("location:index.php?wish_added=1");
    } else {
        // Remove from wishlist (Toggle)
        mysqli_query($conn, "DELETE FROM wishlist WHERE user_email='$u_email' AND product_id='$p_id'");
        header("location:index.php?wish_removed=1");
    }
    exit();
}

// --- FEATURE 3: Recently Viewed Tracking (Tracks last 8 items) ---
if (isset($_GET['view_id'])) {
    $v_id = $_GET['view_id'];
    if (!isset($_SESSION['recently_viewed'])) { $_SESSION['recently_viewed'] = []; }
    
    // Remove if already exists (to avoid duplicates) and push to front
    if (($key = array_search($v_id, $_SESSION['recently_viewed'])) !== false) {
        unset($_SESSION['recently_viewed'][$key]);
    }
    array_unshift($_SESSION['recently_viewed'], $v_id);
    
    // Maintain a limit of 8 items for the user profile history
    $_SESSION['recently_viewed'] = array_slice($_SESSION['recently_viewed'], 0, 8);
    exit(); // Handled via background Fetch API
}

// Fetch Wishlist count for Navbar Badge
$u_email = $_SESSION['user'];
$wish_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM wishlist WHERE user_email='$u_email'");
$wish_count = mysqli_fetch_assoc($wish_res)['total'] ?? 0;

// Fetch current user's wishlist IDs to keep heart icons red
$user_wishlist = [];
$wish_list_query = mysqli_query($conn, "SELECT product_id FROM wishlist WHERE user_email='$u_email'");
while($w = mysqli_fetch_assoc($wish_list_query)) {
    $user_wishlist[] = $w['product_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Store | Elite Tech Edition</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root { --primary: #0066cc; --light-gray: #f5f5f7; --dark: #1d1d1f; }
        body { background-color: var(--light-gray); font-family: 'SF Pro Display', sans-serif; scroll-behavior: smooth; }

        /* Navbar Glassmorphism Effect */
        .navbar { background: rgba(255, 255, 255, 0.8) !important; backdrop-filter: blur(15px); border-bottom: 1px solid rgba(0,0,0,0.05); transition: 0.3s; }
        .nav-link { font-weight: 500; color: #515154 !important; margin: 0 10px; position: relative; }
        .nav-link.active { color: var(--primary) !important; }
        .nav-link::after { content: ''; position: absolute; width: 0; height: 2px; bottom: 0; left: 50%; background: var(--primary); transition: 0.3s; transform: translateX(-50%); }
        .nav-link:hover::after { width: 100%; }

        /* Premium Product Cards */
        .product-card { background: #fff; border-radius: 24px; border: none; transition: 0.4s; padding: 25px; position: relative; }
        .product-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.06) !important; }
        .product-img { height: 140px; object-fit: contain; cursor: pointer; transition: 0.3s; }

        /* Floating Wishlist Button */
        .wish-btn { position: absolute; top: 15px; right: 15px; border: none; background: white; border-radius: 50%; width: 35px; height: 35px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); z-index: 5; transition: 0.3s; }
        .wish-btn:hover { transform: scale(1.1); background: #fff0f0; }
        .wish-btn.active i { color: #ff0000 !important; }

        .btn-detail { background: rgba(0, 102, 204, 0.08); color: var(--primary); border: none; border-radius: 50px; font-size: 0.75rem; font-weight: 600; padding: 5px 15px; }
        .btn-detail:hover { background: var(--primary); color: white; }

        #contact-us { padding: 100px 0; }
        .glass-contact { background: white; border-radius: 35px; padding: 60px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .input-pill { border-radius: 50px !important; padding: 12px 20px !important; border: 1px solid #eee !important; background: #fbfbfb !important; }
        
        .modal-content { border-radius: 30px; border: none; padding: 20px; }
        .social-link { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: #f5f5f7; color: #515154; text-decoration: none; transition: 0.3s; font-size: 1.2rem; }
        .social-link:hover { transform: translateY(-5px); color: white; }
        .instagram:hover { background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285aeb 90%); }
        .twitter:hover { background: #000; }
        .youtube:hover { background: #ff0000; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top px-lg-5" id="mainNav">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-cpu-fill text-primary me-2"></i>MINI STORE</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php"><i class="bi bi-house-door me-1"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact-us"><i class="bi bi-envelope me-1"></i> Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person me-1"></i> My Profile</a></li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="wishlist.php" class="text-dark position-relative me-2">
                    <i class="bi bi-heart-fill fs-4 text-danger"></i>
                    <span class="badge rounded-pill bg-dark position-absolute top-0 start-100 translate-middle"><?php echo $wish_count; ?></span>
                </a>
                <a href="cart.php" class="text-dark position-relative me-2">
                    <i class="bi bi-cart-fill fs-4 text-primary"></i>
                    <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle">
                        <?php echo array_sum($_SESSION['cart']); ?>
                    </span>
                </a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center mb-4">
            <div class="col-md-8 text-center">
                <button onclick="filterCategory('all')" class="btn btn-sm btn-light rounded-pill px-3 mx-1 shadow-sm">All</button>
                <button onclick="filterCategory('Phone')" class="btn btn-sm btn-light rounded-pill px-3 mx-1 shadow-sm">Phones</button>
                <button onclick="filterCategory('Laptop')" class="btn btn-sm btn-light rounded-pill px-3 mx-1 shadow-sm">Laptops</button>
                <button onclick="filterCategory('Watch')" class="btn btn-sm btn-light rounded-pill px-3 mx-1 shadow-sm">Watches</button>
            </div>
        </div>

        <div class="row justify-content-center mb-5">
            <div class="col-md-5">
                <div class="d-flex bg-white rounded-pill p-1 shadow-sm border">
                    <span class="d-flex align-items-center ps-3 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="liveSearch" class="form-control border-0 ps-3 rounded-pill shadow-none" placeholder="Search gadgets...">
                </div>
            </div>
        </div>

        <div class="row g-4" id="productGrid">
            <?php
            $result = mysqli_query($conn, "SELECT * FROM products");
            while($row = mysqli_fetch_assoc($result)) { 
                $inWish = in_array($row['id'], $user_wishlist);
            ?>
                <div class="col-6 col-md-4 col-lg-3 product-item" data-cat="<?php echo $row['category']; ?>">
                    <div class="card product-card h-100 shadow-sm text-center">
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button name="add_to_wishlist" class="wish-btn <?php echo $inWish ? 'active' : ''; ?>">
                                <i class="bi <?php echo $inWish ? 'bi-heart-fill' : 'bi-heart'; ?> text-danger"></i>
                            </button>
                        </form>

                        <img src="images/<?php echo $row['image']; ?>" class="product-img mb-3" 
                             onclick="viewProduct('<?php echo addslashes($row['name']); ?>', 'images/<?php echo $row['image']; ?>', '₹<?php echo number_format($row['price'], 2); ?>', '<?php echo $row['id']; ?>')">
                        <h6 class="fw-bold mb-1 product-name"><?php echo $row['name']; ?></h6>
                        <p class="text-primary fw-bold mb-3">₹<?php echo number_format($row['price'], 2); ?></p>
                        
                        <div class="d-flex flex-column gap-2">
                            <button class="btn-detail" onclick="viewProduct('<?php echo addslashes($row['name']); ?>', 'images/<?php echo $row['image']; ?>', '₹<?php echo number_format($row['price'], 2); ?>', '<?php echo $row['id']; ?>')">Quick View</button>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <button name="add_to_cart" type="submit" class="btn btn-dark w-100 rounded-pill py-2 small" style="background:var(--dark); border:none; color:white;">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 text-center" style="border-radius:30px;">
                <div class="modal-header border-0"><button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <img id="modalImg" src="" class="img-fluid mb-4" style="max-height: 250px; object-fit: contain;">
                    <h3 id="modalName" class="fw-bold"></h3>
                    <h4 id="modalPrice" class="text-primary fw-bold mb-4"></h4>
                    <p class="text-muted small px-3">Elite tech designed for the modern lifestyle. Fast, durable, and sleek.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <section id="contact-us">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="glass-contact shadow-sm text-center">
                        <h2 class="fw-bold mb-4">How can we help?</h2>
                        <form id="contactForm" class="text-start">
                            <div class="mb-3">
                                <label class="small fw-bold ms-2 mb-1">Full Name</label>
                                <input type="text" id="contactName" class="form-control input-pill" placeholder="Enter your name" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold ms-2 mb-1">Email</label>
                                <input type="email" class="form-control input-pill" value="<?php echo $_SESSION['user']; ?>" readonly>
                            </div>
                            <div class="mb-4">
                                <label class="small fw-bold ms-2 mb-1">Message</label>
                                <textarea id="contactMsg" class="form-control" rows="4" style="border-radius: 20px; background: #fbfbfb;" placeholder="Describe your issue..." required></textarea>
                            </div>
                            <button type="button" onclick="handleContactSubmit()" class="btn btn-primary w-100 rounded-pill py-3 fw-bold">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-white border-top py-5">
        <div class="container text-center">
            <h5 class="fw-bold mb-1">MINI STORE</h5>
            <p class="small text-muted">&copy; 2026 Mini Store. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /**
         * Product Modal Display & Tracking
         */
        function viewProduct(name, img, price, id) {
            document.getElementById('modalName').innerText = name;
            document.getElementById('modalImg').src = img;
            document.getElementById('modalPrice').innerText = price;
            new bootstrap.Modal(document.getElementById('productModal')).show();
            
            // Send background request to track this product in "Recently Viewed"
            fetch(`index.php?view_id=${id}`); 
        }

        /**
         * Category Filtering Logic
         */
        function filterCategory(category) {
            document.querySelectorAll('.product-item').forEach(item => {
                let itemCat = item.getAttribute('data-cat');
                if(category === 'all' || itemCat === category) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            });
        }

        /**
         * Live Search Logic
         */
        document.getElementById('liveSearch').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                let name = item.querySelector('.product-name').innerText.toLowerCase();
                item.style.display = name.includes(filter) ? "" : "none";
            });
        });

        // --- TOAST NOTIFICATIONS (NEW) ---
        const urlParams = new URLSearchParams(window.location.search);
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });

        // Success messages based on URL parameters
        if (urlParams.has('added')) Toast.fire({ icon: 'success', title: 'Added to Cart' });
        if (urlParams.has('wish_added')) Toast.fire({ icon: 'success', title: 'Saved to Wishlist' });
        if (urlParams.has('wish_removed')) Toast.fire({ icon: 'info', title: 'Removed from Wishlist' });

        /**
         * Contact Form Submission Handler
         */
        function handleContactSubmit() {
            const name = document.getElementById('contactName').value;
            if(name === "") {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Please enter your name!' });
                return;
            }
            Swal.fire({ title: 'Success!', text: 'Thank you ' + name + ', we will contact you soon.', icon: 'success' });
            document.getElementById('contactForm').reset();
        }
    </script>
</body>
</html>
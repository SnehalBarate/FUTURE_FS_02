<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support | Mini Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --primary-color: #0066cc; --bg-light: #f5f5f7; --text-dark: #1d1d1f; }
        body { background-color: var(--bg-light); font-family: 'SF Pro Display', sans-serif; }
        
        /* Glassmorphism Navbar */
        .navbar { 
            background: rgba(255, 255, 255, 0.8) !important; 
            backdrop-filter: blur(20px); 
            border-bottom: 1px solid rgba(0,0,0,0.05); 
        }
        
        .nav-link { 
            color: #515154 !important; 
            font-weight: 500; 
            font-size: 0.9rem; 
            margin: 0 12px; 
            position: relative; 
        }
        
        /* Active State */
        .nav-link.active::after { 
            content: ''; 
            position: absolute; 
            width: 100%; 
            height: 2px; 
            bottom: -5px; 
            left: 0; 
            background: var(--primary-color); 
        }

        .support-box { background: #fff; border-radius: 25px; padding: 50px; border: none; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top mb-5 px-lg-5">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-cpu-fill text-primary me-2"></i>MINI STORE
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="support.php"><i class="bi bi-headset me-1"></i> Support</a></li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="cart.php" class="text-dark position-relative me-2"><i class="bi bi-bag-fill fs-4"></i></a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="support-box shadow-sm text-center">
                    <i class="bi bi-chat-dots text-primary display-5 mb-3"></i>
                    <h2 class="fw-bold mb-2">Help Center</h2>
                    <p class="text-muted mb-5">How can our tech experts assist you today?</p>
                    
                    <form>
                        <div class="mb-4 text-start">
                            <label class="form-label small fw-bold">Your Registered Email</label>
                            <input type="email" class="form-control rounded-pill px-3 bg-light" value="<?php echo $_SESSION['user']; ?>" readonly>
                        </div>
                        <div class="mb-4 text-start">
                            <label class="form-label small fw-bold">Describe the Issue</label>
                            <textarea class="form-control rounded-4" rows="4" placeholder="What's on your mind?"></textarea>
                        </div>
                        <button type="button" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">Submit Ticket</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
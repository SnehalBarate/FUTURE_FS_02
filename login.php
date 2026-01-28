<?php
include 'db.php';
session_start();
if (isset($_SESSION['user'])) { header("location:index.php"); exit(); }

$error = "";
if (isset($_POST['login'])) {
    // We still use the 'username' column from your DB, but treat it as the email input
    $user = mysqli_real_escape_string($conn, $_POST['email']); 
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    
    $query = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        session_regenerate_id(true); 
        $_SESSION['user'] = $user;
        header("location:index.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Premium Tech Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* Keep your original High-End Gadget background */
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), 
                        url('https://images.unsplash.com/photo-1531297484001-80022131f5a1?auto=format&fit=crop&w=1500&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
            border: none;
        }
        .btn-primary { background-color: #007bff; border: none; }
        .btn-primary:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center">
        <div class="card login-card p-5 shadow-lg border-0" style="width: 420px;">
            <h2 class="text-center fw-bold mb-2">Login</h2>
            <p class="text-center text-muted mb-4 small">Welcome back to the Tech Store</p>
            
            <?php if($error) echo "<div class='alert alert-danger py-2 small text-center'>$error</div>"; ?>
            
            <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control form-control-lg fs-6" autocomplete="off" placeholder="example@mail.com" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg fs-6" autocomplete="new-password" placeholder="Enter password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 fw-bold py-3 shadow">SIGN IN</button>
            </form>
            <p class="text-center mt-4 small">New member? <a href="register.php" class="text-decoration-none fw-bold">Create an Account</a></p>
        </div>
    </div>
</body>
</html>
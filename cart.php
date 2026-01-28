<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
include 'db.php';

// Cart logics
if(isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("location:cart.php"); exit();
}
if(isset($_POST['update_cart'])) {
    foreach($_POST['qty'] as $id => $qty) {
        if($qty <= 0) unset($_SESSION['cart'][$id]);
        else $_SESSION['cart'][$id] = $qty;
    }
    header("location:cart.php"); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Mini Store Elite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    
    <style>
        :root { --primary: #0066cc; --light-gray: #f5f5f7; }
        body { background-color: var(--light-gray); font-family: 'SF Pro Display', sans-serif; }
        .navbar { background: rgba(255, 255, 255, 0.8) !important; backdrop-filter: blur(15px); border-bottom: 1px solid #eee; }
        .cart-card, .summary-box { background: white; border-radius: 24px; padding: 30px; border: none; }
        .item-row { border-bottom: 1px solid #eee; padding: 15px 0; }
        .qty-input { width: 60px; border-radius: 50px; border: 1px solid #ddd; text-align: center; font-weight: 600; }
        .btn-checkout { background: var(--primary); color: white; border-radius: 50px; padding: 14px; font-weight: 700; width: 100%; border: none; transition: 0.3s; }
        .btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0, 102, 204, 0.2); }
        .qr-img { width: 150px; height: 150px; border: 1px solid #eee; padding: 5px; border-radius: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top px-lg-5">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-cpu-fill text-primary"></i> MINI STORE</a>
        <a href="index.php" class="btn btn-light rounded-pill px-4 btn-sm fw-bold">Continue Shopping</a>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="fw-bold mb-4">Complete Your Order.</h2>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="cart-card shadow-sm">
                <?php if(empty($_SESSION['cart'])): ?>
                    <div class="text-center py-5"><h4 class="text-muted">Your cart is empty.</h4></div>
                <?php else: ?>
                    <form method="POST" id="cartForm">
                    <?php 
                    $total = 0; $items_data = [];
                    foreach($_SESSION['cart'] as $id => $qty): 
                        $res = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
                        $product = mysqli_fetch_assoc($res);
                        $subtotal = $product['price'] * $qty;
                        $total += $subtotal;
                        $items_data[] = [$product['name'], $qty, "INR ".number_format($product['price'], 2), "INR ".number_format($subtotal, 2)];
                    ?>
                        <div class="row item-row align-items-center">
                            <div class="col-2"><img src="images/<?php echo $product['image']; ?>" class="img-fluid rounded" onerror="this.src='https://placehold.co/100';"></div>
                            <div class="col-5"><h6 class="fw-bold mb-0"><?php echo $product['name']; ?></h6></div>
                            <div class="col-3"><input type="number" name="qty[<?php echo $id; ?>]" value="<?php echo $qty; ?>" class="qty-input"></div>
                            <div class="col-2 text-end"><b class="text-primary">₹<?php echo number_format($subtotal, 2); ?></b></div>
                        </div>
                    <?php endforeach; ?>
                    <button name="update_cart" class="btn btn-sm btn-outline-dark rounded-pill mt-3 px-3">Update Quantities</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <?php if(!empty($_SESSION['cart'])): ?>
        <div class="col-lg-4">
            <div class="summary-box shadow-sm border">
                <h5 class="fw-bold mb-4">Order Summary</h5>
                <div class="mb-4">
                    <label class="small fw-bold text-muted mb-2">Select Payment Method</label>
                    <select id="pm" class="form-select rounded-pill shadow-none" onchange="togglePaymentUI()">
                        <option value="UPI">UPI / PhonePe / GPay</option>
                        <option value="Card">Credit or Debit Card</option>
                        <option value="COD">Cash on Delivery</option>
                    </select>
                </div>

                <div id="upi-section" class="text-center mb-4">
                    <p class="small text-muted mb-2">Scan to Pay securely</p>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=upi://pay?pa=store@upi&pn=MiniStore&am=<?php echo $total; ?>" class="qr-img">
                </div>

                <div id="card-section" class="mb-4" style="display:none;">
                    <div class="bg-light p-3 rounded-4">
                        <div class="mb-2">
                            <input type="password" class="form-control form-control-sm rounded-pill px-3" placeholder="Enter Card Number (Hidden)">
                        </div>
                        <div class="row g-2">
                            <div class="col-6"><input type="text" class="form-control form-control-sm rounded-pill px-3" placeholder="MM/YY"></div>
                            <div class="col-6"><input type="password" class="form-control form-control-sm rounded-pill px-3" placeholder="CVV"></div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span>₹<?php echo number_format($total, 2); ?></span></div>
                <div class="d-flex justify-content-between mb-4"><h5 class="fw-bold">Total</h5><h5 class="text-primary fw-bold">₹<?php echo number_format($total, 2); ?></h5></div>
                
                <button onclick="handleCheckout()" class="btn btn-checkout">Confirm & Pay</button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function togglePaymentUI() {
        const pm = document.getElementById('pm').value;
        document.getElementById('upi-section').style.display = (pm === 'UPI') ? 'block' : 'none';
        document.getElementById('card-section').style.display = (pm === 'Card') ? 'block' : 'none';
    }

    function handleCheckout() {
        const method = document.getElementById('pm').value;
        let statusText = "Verifying Transaction Details...";
        if(method === "COD") statusText = "Processing Your Order...";

        Swal.fire({
            title: 'Encrypting Payment',
            html: `<b>${statusText}</b>`,
            timer: 3000,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading() }
        }).then(() => {
            Swal.fire({
                icon: 'success', 
                title: 'Order Successful!',
                text: 'Your order details have been sent to your registered email.',
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-file-earmark-pdf"></i> Save Tax Invoice',
                cancelButtonText: 'Home',
                confirmButtonColor: '#0066cc'
            }).then((res) => {
                if (res.isConfirmed) {
                    generateProfessionalPDF(method);
                } else {
                    window.location.href = 'index.php?clear_cart=1';
                }
            });
        });
    }

    function generateProfessionalPDF(m) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const orderID = "MS-" + Math.floor(100000 + Math.random() * 900000);

        doc.setFillColor(0, 102, 204);
        doc.rect(0, 0, 210, 40, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(24);
        doc.text("MINI STORE ELITE", 20, 25);
        doc.setFontSize(10);
        doc.text("Official Tax Invoice", 20, 32);

        doc.setTextColor(0, 0, 0);
        doc.text("BILLED TO:", 20, 55);
        doc.setFont(undefined, 'bold');
        doc.text("<?php echo $_SESSION['user']; ?>", 20, 62);
        
        doc.setFont(undefined, 'normal');
        doc.text("INVOICE NO: " + orderID, 140, 55);
        doc.text("DATE: " + new Date().toLocaleDateString(), 140, 62);
        doc.text("PAYMENT: " + m, 140, 69);

        const tableData = <?php echo json_encode($items_data); ?>;
        doc.autoTable({
            startY: 80,
            head: [['Product Name', 'Qty', 'Unit Price', 'Total']],
            body: tableData,
            theme: 'grid',
            headStyles: { fillColor: [0, 102, 204] }
        });

        const finalY = doc.lastAutoTable.finalY + 10;
        doc.setFont(undefined, 'bold');
        doc.text("GRAND TOTAL: INR <?php echo number_format($total, 2); ?>", 140, finalY);
        
        doc.save("Invoice_" + orderID + ".pdf");
        setTimeout(() => { window.location.href = 'index.php?clear_cart=1'; }, 1000);
    }
</script>
</body>
</html>
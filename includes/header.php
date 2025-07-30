<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../includes/config.php";

// Kiểm tra số lượng sản phẩm trong giỏ hàng
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $cart_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch (PDOException $e) {
        // Không xử lý lỗi ở đây để tránh làm gián đoạn giao diện
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <title>Header</title>
</head>
<body>
  <header>

    <div class="header-container1">
      <ul class="list-header1">
        <li class="logo-header"><a href="../public/index.php">
          <img src="../public/assets/images/logo-header.png" alt="">
        </a></li>
        <li id="search-header">
          <form action="../includes/search-product.php" method="GET" class="search-container">
            <input type="text" name="query" class="search" placeholder="Search" required>
            <i class="bi bi-search"></i>
            <button type="submit" class="search-button">Search</button>
          </form>
        </li>
        <li class="cart-wrapper">
          <a href="../includes/cart.php"><i class="bi bi-cart3"></i></a>
          <?php if ($cart_count > 0): ?>
            <span class="cart-badge"><?php echo $cart_count; ?></span>
          <?php endif; ?>
        </li>
        <li>
            <?php if (isset($_SESSION['username'])): ?>
                <a href="../includes/logout.php">Logout</a>
            <?php else: ?>
                <a href="../includes/sign-in.php">Sign in</a>
            <?php endif; ?>
        </li>
        <?php if (!isset($_SESSION['username'])): ?>
            <li><a href="../includes/sign-up.php">Sign up</a></li>
        <?php endif; ?>
        <li class="header-empty"></li>
      </ul>
    </div>
    <div class="header-container2">
      <ul class="menu">
        <li class="brand">
          <a href="../includes/apple.php">Apple</a>
          <ul class="submenu">
            <a href="../includes/apple-phone.php"><li>Iphone</li></a>
            <a href="../includes/apple-tablet.php"><li>Tablet</li></a>
            <a href="../includes/apple-laptop.php"><li>Laptop</li></a>
            <a href="../includes/apple-headphone.php"><li>Earphone/ Headphone</li></a>
            <a href="../includes/apple-others.php"><li>Others</li></a>
          </ul>
        </li>
        <li class="brand">
          <a href="../includes/samsung.php">Samsung</a>
          <ul class="submenu">
            <a href="../includes/samsung-phone.php"><li>Phone</li></a>
            <a href="../includes/samsung-tablet.php"><li>Tablet</li></a>
            <a href="../includes/samsung-laptop.php"><li>Laptop</li></a>
            <a href="../includes/samsung-headphone.php"><li>Earphone/ Headphone</li></a>
            <a href="../includes/samsung-others.php"><li>Others</li></a>
          </ul>
        </li>
        <li class="brand">
          <a href="../includes/xiaomi.php">Xiaomi</a>
          <ul class="submenu">
            <a href="../includes/xiaomi-phone.php"><li>Phone</li></a>
            <a href="../includes/xiaomi-headphone.php"><li>Earphone/ Headphone</li></a>
            <a href="../includes/xiaomi-others.php"><li>Others</li></a>
          </ul>
        </li>
        <li class="brand">
          <a href="../includes/huawei.php">Huawei</a>
          <ul class="submenu">
            <a href="../includes/huawei-phone.php"><li>Phone</li></a>
            <a href="../includes/huawei-headphone.php"><li>Earphone/ Headphone</li></a>
            <a href="../includes/huawei-others.php"><li>Others</li></a>
          </ul>
        </li>
        <li class="brand">
          <a href="../includes/nokia.php">Nokia</a>
          <ul class="submenu">
            <a href="../includes/nokia-phone.php"><li>Phone</li></a>
            <a href="../includes/nokia-others.php"><li>Others</li></a>
          </ul>
        </li>
        <li class="brand">
          <a href="../includes/other.php">Other Device</a>
          <ul class="submenu">
            <a href="../includes/other-tablet.php"><li>Tablet</li></a>
            <a href="../includes/other-laptop.php"><li>Laptop</li></a>
            <a href="../includes/other-headphone.php"><li>Earphone/ Headphone</li></a>
            <a href="../includes/other-camera.php"><li>Camera</li></a>
            <a href="../includes/other-microphone.php"><li>Microphone</li></a>
            <a href="../includes/other-others.php"><li>Others</li></a>
          </ul>
        </li>
      </ul>
    </div>
  </header>
  <?php ob_end_flush(); ?>
</body>
</html>
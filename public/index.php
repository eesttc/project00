<?php
// Đảm bảo không có khoảng trắng hoặc ký tự trước thẻ <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wireless Device Store</title>
</head>
<body>
  <?php include("../public/assets/css/styles.php") ?>
  <?php include('../includes/header.php') ?>

  <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include("../includes/config.php");
  ?>

  <div class="container-index">
    <div class="hot">
      <h1>Apple</h1>
      <div class="hot-images-wrapper">
        <a href=""><img src="../public/assets/images/hot-apple1.jpg" alt=""></a>
        <a href=""><img src="./assets/images/hot-apple2.jpg" alt=""></a>
      </div>
    </div>
    <div class="hot">
      <h1>Samsung</h1>
      <div class="hot-images-wrapper">
        <a href=""><img src="./assets/images/hot-samsung1.png" alt=""></a>
        <a href=""><img src="./assets/images/hot-samsung2.jpg" alt=""></a>
      </div>
    </div>
    <div class="hot">
      <h1>Xiaomi</h1>
      <div class="hot-images-wrapper">
        <a href=""><img src="../public/assets/images/hot-xiaomi.jpg" alt=""></a>
        <a href=""><img src="../public/assets/images/hot-xiaomi2.jpg" alt=""></a>
      </div>
    </div>
    <div class="hot">
      <h1>Huawei</h1>
      <div class="hot-images-wrapper">
        <a href=""><img src="../public/assets/images/hot-huawei.jpg" alt=""></a>
        <a href=""><img src="../public/assets/images/hot-huawei2.jpg" alt=""></a>
      </div>
    </div>
    <div class="hot">
      <h1>Nokia</h1>
      <div class="hot-images-wrapper">
        <a href=""><img src="../public/assets/images/hot-nokia.jpg" alt=""></a>
        <a href=""><img src="../public/assets/images/hot-nokia2.jpg" alt=""></a>
      </div>
    </div>
  </div>

  <?php include('../includes/footer.php') ?>
</body>
</html>
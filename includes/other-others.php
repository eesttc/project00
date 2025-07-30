<?php
define('BASE_URL', 'http://localhost:8080/project00/'); // Thay 'project' bằng tên thư mục dự án của bạn
?>
<?php include("../includes/check.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Other Device</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
  <?php include("../public/assets/css/styles.php"); ?>
  <?php include('../includes/header.php'); ?>
  <?php include("../includes/config.php"); ?>

  <?php 
    try {
      $sql = "SELECT id, name, price, image_url, features FROM products WHERE brand_id = 5 AND type LIKE 'others' ORDER BY id";
      $stmt = $conn->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "Lỗi truy vấn: " . $e->getMessage();
    }
  ?>

  <div class="container-apple">
    <h2 class="text-center my-4">Other Device</h2>
    <div class="container-products-apple">
      <?php if (!empty($result)): ?>
        <?php foreach ($result as $row): ?>
          <div class="product-apple card">
            <a href="<?php echo BASE_URL; ?>includes/product-detail.php?id=<?php echo $row['id']; ?>">
              <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                <p class="card-text price">$<?php echo number_format($row['price'], 2); ?></p>
                <p class="card-text features"><?php echo htmlspecialchars($row['features']); ?></p>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-center">Không có sản phẩm nào.</p>
      <?php endif; ?>
    </div>
  </div>

  <?php
  // Đóng kết nối
  $conn = null;
  ?>

  <?php include('../includes/footer.php'); ?>
</body>
</html>
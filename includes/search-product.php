<?php
// Đảm bảo không có khoảng trắng hoặc ký tự trước thẻ <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
define('BASE_URL', 'http://localhost:8080/project00/');
include("../includes/config.php");

try {
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    $sql = "SELECT p.id, p.name, p.price, p.image_url, p.features, b.name as brand_name
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.name LIKE :query
               OR p.type LIKE :query
               OR b.name LIKE :query
            ORDER BY p.name";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['query' => "%$query%"]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Lỗi truy vấn: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php include("../public/assets/css/styles.php"); ?>
    <?php include('../includes/header.php'); ?>

    <div class="container-apple">
        <h2 class="text-center my-4">Kết quả tìm kiếm cho "<?php echo htmlspecialchars($query); ?>"</h2>
        <div class="container-products-apple">
            <?php if (!empty($result)): ?>
                <?php foreach ($result as $row): ?>
                    <div class="product-apple card">
                        <a href="<?php echo BASE_URL; ?>includes/product-detail.php?id=<?php echo $row['id']; ?>">
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?> (<?php echo htmlspecialchars($row['brand_name']); ?>)</h5>
                                <p class="card-text price">$<?php echo number_format($row['price'], 2); ?></p>
                                <p class="card-text features"><?php echo htmlspecialchars($row['features']); ?></p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Không tìm thấy sản phẩm nào phù hợp với "<?php echo htmlspecialchars($query); ?>"</p>
            <?php endif; ?>
        </div>
    </div>

    <?php
    $conn = null;
    ?>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
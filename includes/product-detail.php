<?php
ob_start();
include("../includes/config.php");
session_start();
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Truy vấn thông tin sản phẩm và thương hiệu
try {
    $stmt = $conn->prepare("
        SELECT p.id, p.name, p.price, p.image_url, p.features, b.name AS brand_name, b.logo_url
        FROM products p
        JOIN brands b ON p.brand_id = b.id
        WHERE p.id = ?
    ");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Product does not exist.");
    }
} catch (PDOException $e) {
    die("Oops! " . $e->getMessage());
}

// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../includes/sign-in.php");
        exit;
    }

    $user_id = (int)$_SESSION['user_id'];
    $quantity = 1; // Mặc định số lượng là 1

    try {
        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart_item) {
            // Cập nhật số lượng nếu sản phẩm đã có trong giỏ hàng
            $new_quantity = $cart_item['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->execute([$new_quantity, $cart_item['id']]);
        } else {
            // Thêm sản phẩm mới vào giỏ hàng
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $product_id, $quantity]);
        }
        // Lưu thông báo vào session
        $_SESSION['cart_message'] = 'Added to cart successfully!';
        // Chuyển hướng về chính trang với product_id
        header("Location: ../includes/product-detail.php?id=$product_id");
        exit;
    } catch (PDOException $e) {
        $_SESSION['cart_message'] = 'Cannot add to cart! ' . $e->getMessage();
        header("Location: ../includes/product-detail.php?id=$product_id");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../public/assets/css/style.css">
    <style>
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            display: none;
        }
        .toast.success {
            background-color: #28a745;
            color: white;
        }
        .toast.error {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../public/assets/css/styles.php'); ?>

    <div class="container-detail">
        <div class="product-detail">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-info">
                <div class="brand-logo">
                    <img src="<?php echo htmlspecialchars($product['logo_url']); ?>" alt="<?php echo htmlspecialchars($product['brand_name']); ?>">
                </div>
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                <p class="description"><?php echo htmlspecialchars($product['features']); ?></p>
                <div class="specifications">
                    <h3>Specifications</h3>
                    <ul>
                        <?php
                        $features = explode(',', $product['features']);
                        foreach ($features as $feature) {
                            echo '<li>' . htmlspecialchars(trim($feature)) . '</li>';
                        }
                        ?>
                    </ul>
                </div>
                <form method="post">
                    <div class="input-group mb-3">
                        <button type="submit" name="add_to_cart" class="buy-button">Add to cart</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Thông báo toast -->
    <?php if (isset($_SESSION['cart_message'])): ?>
        <div id="toast" class="toast <?php echo strpos($_SESSION['cart_message'], 'Oops!') === false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($_SESSION['cart_message']); ?>
        </div>
        <?php unset($_SESSION['cart_message']); // Xóa session sau khi hiển thị ?>
        <script>
            // Hiển thị và ẩn toast sau 2 giây
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.getElementById('toast');
                if (toast) {
                    toast.style.display = 'block';
                    setTimeout(() => {
                        toast.style.display = 'none';
                    }, 2000);
                }
            });
        </script>
    <?php endif; ?>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
<?php ob_end_flush(); ?>
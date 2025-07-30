<?php
ob_start();
include("../includes/config.php");
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../includes/sign-in.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Xử lý xóa sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $cart_id = (int)$_POST['cart_id'];
    
    try {
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $user_id]);
        header("Location: ../includes/cart.php");
        exit;
    } catch (PDOException $e) {
        echo "Lỗi khi xóa sản phẩm: " . $e->getMessage();
        exit;
    }
}

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    try {
        // Bắt đầu giao dịch
        $conn->beginTransaction();

        // Tạo bản ghi trong bảng order
        $stmt = $conn->prepare("INSERT INTO `order` (userid, dateoforder) VALUES (?, NOW())");
        $stmt->execute([$user_id]);
        $order_id = $conn->lastInsertId();

        // Lấy dữ liệu từ cart
        $stmt = $conn->prepare("SELECT product_id, quantity FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Thêm vào orderdetail
        foreach ($cart_items as $item) {
            $stmt = $conn->prepare("INSERT INTO orderdetail (orderid, productid, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$order_id, $item['product_id'], $item['quantity']]);
        }

        // Xóa giỏ hàng sau khi đặt hàng
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Hoàn tất giao dịch
        $conn->commit();

        // Chuyển hướng đến order-received.php
        header("Location: ../includes/order-received.php?order_id=$order_id");
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Lỗi khi đặt hàng: " . $e->getMessage();
        exit;
    }
}

// Truy vấn danh sách sản phẩm trong giỏ hàng
try {
    $stmt = $conn->prepare("
        SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.image_url, b.name AS brand_name
        FROM cart c
        JOIN products p ON c.product_id = p.id
        JOIN brands b ON p.brand_id = b.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Lỗi truy vấn: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../public/assets/css/styles.php'); ?>

    <div class="container-index">
        <h1>Giỏ hàng của bạn</h1>
        <?php if (empty($cart_items)): ?>
            <p>Giỏ hàng của bạn đang trống.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Thương hiệu</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Tổng</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($cart_items as $item):
                        $item_total = $item['price'] * $item['quantity'];
                        $total += $item_total;
                    ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"></td>
                            <td><a href="../includes/product-detail.php?id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a></td>
                            <td><?php echo htmlspecialchars($item['brand_name']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item_total, 2); ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                    <button type="submit" name="remove_item" class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-end"><strong>Tổng cộng:</strong></td>
                        <td>$<?php echo number_format($total, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="7" class="text-end">
                            <form method="post">
                                <button type="submit" name="place_order" class="btn btn-success btn-sm">Đặt hàng</button>
                            </form>
                        </td>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
<?php ob_end_flush(); ?>
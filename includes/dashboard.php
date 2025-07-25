<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Quản Lý</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #e5e7eb;
            color: #374151;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tr:hover {
            background-color: #f1f5f9;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            width: 100%;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php
    define('BASE_URL', 'http://localhost:8080/project00/');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'public/index.php');
        exit;
    }
    require_once '../includes/config.php';

    $message = '';
    try {
        // Xử lý tạo hóa đơn
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_bill'])) {
            $order_id = (int)$_POST['order_id'];
            $stmt_total = $conn->prepare("
                SELECT SUM(od.quantity * p.price) AS total_price
                FROM orderdetail od
                JOIN products p ON od.productid = p.id
                WHERE od.orderid = ?
            ");
            $stmt_total->execute([$order_id]);
            $total_price = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_price'];

            if ($total_price !== null) {
                $stmt_bill = $conn->prepare("INSERT INTO bill (orderid, totalprice) VALUES (?, ?)");
                $stmt_bill->execute([$order_id, $total_price]);
                $message = '<p class="success">Tạo hóa đơn thành công!</p>';
            } else {
                $message = '<p class="error">Không thể tính tổng giá cho đơn hàng!</p>';
            }
        }

        // Xử lý cập nhật trạng thái đơn hàng (Hoàn thành)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_order'])) {
            $order_id = (int)$_POST['order_id'];
            $stmt_update = $conn->prepare("UPDATE `order` SET status = 'done' WHERE id = ?");
            $stmt_update->execute([$order_id]);
            $message = '<p class="success">Cập nhật trạng thái đơn hàng thành công!</p>';
        }

        // Xử lý hủy đơn hàng
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
            $order_id = (int)$_POST['order_id'];
            $stmt_update = $conn->prepare("UPDATE `order` SET status = 'canceled' WHERE id = ?");
            $stmt_update->execute([$order_id]);
            $message = '<p class="success">Hủy đơn hàng thành công!</p>';
        }

        // Xử lý xóa tài khoản người dùng
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
            $user_id = (int)$_POST['user_id'];
            $stmt_delete = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt_delete->execute([$user_id]);
            $message = '<p class="success">Xóa tài khoản thành công!</p>';
        }

        // Xử lý thêm tài khoản người dùng
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
            $username = trim($_POST['username']);
            $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $role = in_array($_POST['role'], ['user', 'admin']) ? $_POST['role'] : 'user';

            $email = empty($email) ? null : $email;

            if (empty($username) || empty($password) || empty($phone)) {
                $message = '<p class="error">Vui lòng điền đầy đủ thông tin bắt buộc!</p>';
            } else {
                $stmt_check_username = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
                $stmt_check_username->execute([$username]);
                $username_exists = $stmt_check_username->fetchColumn();

                $stmt_check_phone = $conn->prepare("SELECT COUNT(*) FROM users WHERE phone = ?");
                $stmt_check_phone->execute([$phone]);
                $phone_exists = $stmt_check_phone->fetchColumn();

                $email_exists = false;
                if ($email !== null) {
                    $stmt_check_email = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
                    $stmt_check_email->execute([$email]);
                    $email_exists = $stmt_check_email->fetchColumn();
                }

                if ($username_exists) {
                    $message = '<p class="error">Tên đăng nhập đã tồn tại!</p>';
                } elseif ($phone_exists) {
                    $message = '<p class="error">Số điện thoại đã tồn tại!</p>';
                } elseif ($email_exists) {
                    $message = '<p class="error">Email đã tồn tại!</p>';
                } else {
                    try {
                        $stmt_add_user = $conn->prepare("
                            INSERT INTO users (username, password, email, phone, is_registered, role)
                            VALUES (?, ?, ?, ?, 1, ?)
                        ");
                        $stmt_add_user->execute([$username, $password, $email, $phone, $role]);
                        $message = '<p class="success">Thêm tài khoản thành công!</p>';
                    } catch (PDOException $e) {
                        $message = '<p class="error">Lỗi khi thêm tài khoản: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    }
                }
            }
        }

        // Lấy số liệu thống kê
        $stmt_products = $conn->query("SELECT COUNT(*) AS products FROM products");
        $products_count = $stmt_products->fetch(PDO::FETCH_ASSOC)['products'];

        $stmt_orders = $conn->query("SELECT COUNT(*) AS orders FROM `order`");
        $orders_count = $stmt_orders->fetch(PDO::FETCH_ASSOC)['orders'];

        // Debug: Kiểm tra dữ liệu đơn hàng
        $stmt_check_orders = $conn->query("SELECT id, userid, dateoforder, status FROM `order`");
        $debug_orders = $stmt_check_orders->fetchAll(PDO::FETCH_ASSOC);
        if (empty($debug_orders)) {
            $message = '<p class="error">Không tìm thấy đơn hàng trong database!</p>';
        }

        // Lấy danh sách đơn hàng gần đây (chưa hoàn thành)
        $stmt_orders_recent = $conn->prepare("
            SELECT o.id, o.userid, o.dateoforder, o.status, b.totalprice
            FROM `order` o
            LEFT JOIN bill b ON o.id = b.orderid
            WHERE o.status = 'pending'
            ORDER BY o.dateoforder DESC
            LIMIT 5
        ");
        $stmt_orders_recent->execute();
        $recent_orders = $stmt_orders_recent->fetchAll(PDO::FETCH_ASSOC);

        // Lấy toàn bộ đơn hàng
        $stmt_all_orders = $conn->prepare("
            SELECT o.id, o.userid, o.dateoforder, o.status, b.totalprice
            FROM `order` o
            LEFT JOIN bill b ON o.id = b.orderid
            ORDER BY o.dateoforder DESC
        ");
        $stmt_all_orders->execute();
        $all_orders = $stmt_all_orders->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách người dùng
        $stmt_users = $conn->query("SELECT id, username, email, phone, role FROM users");
        $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

        // Lấy chi tiết đơn hàng nếu có yêu cầu
        $order_details = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_details'])) {
            $order_id = (int)$_POST['order_id'];
            $stmt_details = $conn->prepare("
                SELECT p.name, od.quantity, p.price
                FROM orderdetail od
                JOIN products p ON od.productid = p.id
                WHERE od.orderid = ?
            ");
            $stmt_details->execute([$order_id]);
            $order_details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch(PDOException $e) {
        echo "<div class='container text-red-600'>Lỗi truy vấn: " . htmlspecialchars($e->getMessage()) . "</div>";
        exit;
    }
    ?>

    <!-- Header -->
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <h1 class="text-2xl font-bold">Dashboard Quản Lý</h1>
    </header>

    <!-- Main Content -->
    <div class="container">
        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-700">Sản Phẩm</h2>
                <p class="text-3xl font-bold text-blue-600"><?php echo $products_count; ?></p>
            </div>
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-700">Đơn Hàng</h2>
                <p class="text-3xl font-bold text-blue-600"><?php echo $orders_count; ?></p>
            </div>
        </div>

        <!-- Product Management Link -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Quản Lý Sản Phẩm</h2>
            <a href="../includes/dashboard-product.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Đi đến Quản Lý Sản Phẩm</a>
        </div>

        <!-- Recent Orders Table -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Đơn Hàng Gần Đây (Chưa Hoàn Thành)</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Mã Đơn Hàng</th>
                            <th>ID Người Dùng</th>
                            <th>Ngày Đặt</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr><td colspan="6">Không có đơn hàng chưa hoàn thành.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['userid']); ?></td>
                                    <td><?php echo htmlspecialchars($order['dateoforder']); ?></td>
                                    <td><?php echo $order['totalprice'] ? number_format($order['totalprice'], 2) : 'Chưa có hóa đơn'; ?> VNĐ</td>
                                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                                    <td>
                                        <?php if (!$order['totalprice']): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="show_details" value="1">
                                                <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Tạo hóa đơn</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="complete_order" value="1">
                                                <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Hoàn thành</button>
                                            </form>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="cancel_order" value="1">
                                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hủy</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Order Details Modal (hiển thị nếu có show_details) -->
        <?php if (!empty($order_details)): ?>
            <div class="modal" style="display: flex;">
                <div class="modal-content">
                    <h2 class="text-lg font-semibold mb-4">Chi Tiết Hóa Đơn</h2>
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th>Sản Phẩm</th>
                                <th>Số Lượng</th>
                                <th>Giá</th>
                                <th>Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            foreach ($order_details as $item):
                                $subtotal = $item['quantity'] * $item['price'];
                                $total += $subtotal;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td><?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo number_format($subtotal, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right font-bold">Tổng cộng:</td>
                                <td><?php echo number_format($total, 2); ?> VNĐ</td>
                            </tr>
                        </tfoot>
                    </table>
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $_POST['order_id']; ?>">
                        <input type="hidden" name="create_bill" value="1">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-4">Xác nhận tạo hóa đơn</button>
                        <a href="dashboard.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mt-4 ml-2">Đóng</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- All Orders Table -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Tất Cả Đơn Hàng</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Mã Đơn Hàng</th>
                            <th>ID Người Dùng</th>
                            <th>Ngày Đặt</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($all_orders)): ?>
                            <tr><td colspan="6">Không có đơn hàng nào.</td></tr>
                        <?php else: ?>
                            <?php foreach ($all_orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['userid']); ?></td>
                                    <td><?php echo htmlspecialchars($order['dateoforder']); ?></td>
                                    <td><?php echo $order['totalprice'] ? number_format($order['totalprice'], 2) : 'Chưa có hóa đơn'; ?> VNĐ</td>
                                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                                    <td>
                                        <?php if (!$order['totalprice']): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="show_details" value="1">
                                                <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Tạo hóa đơn</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="complete_order" value="1">
                                                <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Hoàn thành</button>
                                            </form>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="cancel_order" value="1">
                                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hủy</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Người Dùng</h2>
            <div class="mb-4">
                <h3 class="text-md font-semibold mb-2">Thêm Người Dùng</h3>
                <form method="POST">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input type="text" name="username" placeholder="Tên đăng nhập" class="border p-2 rounded" required>
                        <input type="password" name="password" placeholder="Mật khẩu" class="border p-2 rounded" required>
                        <input type="email" name="email" placeholder="Email (tùy chọn)" class="border p-2 rounded">
                        <input type="text" name="phone" placeholder="Số điện thoại" class="border p-2 rounded" required>
                        <select name="role" class="border p-2 rounded" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="add_user" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-4">Thêm</button>
                </form>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Đăng Nhập</th>
                            <th>Email</th>
                            <th>Số Điện Thoại</th>
                            <th>Vai Trò</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email'] ?: 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <input type="hidden" name="delete_user" value="1">
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
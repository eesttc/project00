<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm</title>
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
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
        .brand-section {
            margin-bottom: 20px;
        }
        .brand-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #374151;
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
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

    try {
        // Xử lý thêm thương hiệu mới
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_brand'])) {
            $brand_name = trim($_POST['brand_name']);
            if (empty($brand_name)) {
                $message = '<p class="error">Vui lòng nhập tên thương hiệu!</p>';
            } else {
                try {
                    $stmt_add_brand = $conn->prepare("INSERT INTO brands (name) VALUES (?)");
                    $stmt_add_brand->execute([$brand_name]);
                    $message = '<p class="success">Thêm thương hiệu thành công!</p>';
                } catch (PDOException $e) {
                    $message = '<p class="error">Lỗi: Thương hiệu đã tồn tại hoặc thông tin không hợp lệ! (' . htmlspecialchars($e->getMessage()) . ')</p>';
                }
            }
        }

        // Xử lý thêm sản phẩm mới
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
            $product_name = trim($_POST['product_name']);
            $price = (float)$_POST['price'];
            $brand_id = (int)$_POST['brand_id'];
            $image_url = trim($_POST['image_url']);
            $type = trim($_POST['type']);

            // Validate đầu vào
            if (empty($product_name)) {
                $message = '<p class="error">Tên sản phẩm không được để trống!</p>';
            } elseif ($price <= 0) {
                $message = '<p class="error">Giá phải lớn hơn 0!</p>';
            } elseif ($brand_id <= 0) {
                $message = '<p class="error">Vui lòng chọn thương hiệu hợp lệ!</p>';
            } elseif (empty($type)) {
                $message = '<p class="error">Loại sản phẩm không được để trống!</p>';
            } else {
                try {
                    // Kiểm tra xem brand_id có tồn tại
                    $stmt_check_brand = $conn->prepare("SELECT COUNT(*) FROM brands WHERE id = ?");
                    $stmt_check_brand->execute([$brand_id]);
                    if ($stmt_check_brand->fetchColumn() == 0) {
                        $message = '<p class="error">Thương hiệu không tồn tại!</p>';
                    } else {
                        $stmt_add_product = $conn->prepare("
                            INSERT INTO products (name, price, brand_id, image_url, type)
                            VALUES (?, ?, ?, ?, ?)
                        ");
                        $stmt_add_product->execute([$product_name, $price, $brand_id, $image_url, $type]);
                        $message = '<p class="success">Thêm sản phẩm thành công!</p>';
                    }
                } catch (PDOException $e) {
                    $message = '<p class="error">Lỗi khi thêm sản phẩm: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    error_log("Add product error: name=$product_name, price=$price, brand_id=$brand_id, type=$type, error=" . $e->getMessage());
                }
            }
        }

        // Xử lý xóa sản phẩm
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
            $product_id = (int)$_POST['product_id'];
            try {
                // Kiểm tra ràng buộc khóa ngoại
                $stmt_check_order = $conn->prepare("SELECT COUNT(*) FROM orderdetail WHERE productid = ?");
                $stmt_check_order->execute([$product_id]);
                if ($stmt_check_order->fetchColumn() > 0) {
                    $message = '<p class="error">Không thể xóa sản phẩm vì đã có trong đơn hàng!</p>';
                } else {
                    $stmt_delete_product = $conn->prepare("DELETE FROM products WHERE id = ?");
                    $stmt_delete_product->execute([$product_id]);
                    $message = '<p class="success">Xóa sản phẩm thành công!</p>';
                }
            } catch (PDOException $e) {
                $message = '<p class="error">Lỗi khi xóa sản phẩm: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
        }

        // Lấy danh sách thương hiệu
        $stmt_brands = $conn->query("SELECT id, name FROM brands ORDER BY name");
        $brands = $stmt_brands->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách sản phẩm theo thương hiệu
        $products_by_brand = [];
        foreach ($brands as $brand) {
            $query = "
                SELECT p.id, p.name, p.price, p.type
                FROM products p
                WHERE p.brand_id = :brand_id
            ";
            $params = ['brand_id' => $brand['id']];
            if ($search_query) {
                $query .= " AND (p.name LIKE :search OR p.type LIKE :search)";
                $params['search'] = '%' . $search_query . '%';
            }
            $query .= " ORDER BY p.name";
            
            $stmt_products = $conn->prepare($query);
            $stmt_products->execute($params);
            $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($products)) {
                $products_by_brand[$brand['name']] = $products;
            }
        }
    } catch(PDOException $e) {
        echo "<div class='container text-red-600'>Lỗi truy vấn: " . htmlspecialchars($e->getMessage()) . "</div>";
        exit;
    }
    ?>

    <!-- Header -->
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <h1 class="text-2xl font-bold">Quản Lý Sản Phẩm</h1>
    </header>

    <!-- Main Content -->
    <div class="container">
        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <!-- Back to Dashboard -->
        <div class="mb-4">
            <a href="../includes/dashboard.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Quay lại Dashboard</a>
        </div>

        <!-- Add Brand -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Thêm Thương Hiệu</h2>
            <form method="POST">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input type="text" name="brand_name" placeholder="Tên thương hiệu" class="border p-2 rounded" required>
                </div>
                <button type="submit" name="add_brand" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-4">Thêm Thương Hiệu</button>
            </form>
        </div>

        <!-- Add Product -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Thêm Sản Phẩm</h2>
            <form method="POST">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input type="text" name="product_name" placeholder="Tên sản phẩm" class="border p-2 rounded" required>
                    <input type="number" name="price" placeholder="Giá (VNĐ)" step="0.01" class="border p-2 rounded" required>
                    <select name="brand_id" class="border p-2 rounded" required>
                        <option value="">Chọn thương hiệu</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?php echo htmlspecialchars($brand['id']); ?>"><?php echo htmlspecialchars($brand['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="image_url" placeholder="URL hình ảnh (tùy chọn)" class="border p-2 rounded">
                    <input type="text" name="type" placeholder="Loại sản phẩm (VD: Phone, Laptop)" class="border p-2 rounded" required>
                </div>
                <button type="submit" name="add_product" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-4">Thêm Sản Phẩm</button>
            </form>
        </div>

        <!-- Search Products -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Tìm Kiếm Sản Phẩm</h2>
            <form method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input type="text" name="search" placeholder="Nhập tên hoặc loại sản phẩm" value="<?php echo htmlspecialchars($search_query); ?>" class="border p-2 rounded">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-4">Tìm Kiếm</button>
            </form>
        </div>

        <!-- Products by Brand -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Danh Sách Sản Phẩm</h2>
            <?php if (empty($products_by_brand)): ?>
                <p>Không tìm thấy sản phẩm nào.</p>
            <?php else: ?>
                <?php foreach ($products_by_brand as $brand_name => $products): ?>
                    <div class="brand-section">
                        <h3 class="brand-title"><?php echo htmlspecialchars($brand_name); ?></h3>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên Sản Phẩm</th>
                                        <th>Giá (VNĐ)</th>
                                        <th>Loại</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td><?php echo number_format($product['price'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($product['type']); ?></td>
                                            <td>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <input type="hidden" name="delete_product" value="1">
                                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Xóa</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
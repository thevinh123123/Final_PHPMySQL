<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db.php';

$id = $_GET['id'];
$product = null; // Khởi tạo biến $product để tránh lỗi undefined variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = (int)$_POST['price']; // Ép kiểu giá trị thành số nguyên
    $category = $_POST['category'];
    $description = $_POST['description'];
    $imageUpdated = false;

    // Kiểm tra trùng lặp tên sản phẩm
    $sql = "SELECT * FROM products WHERE name = '$name' AND id != $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $error = "Tên sản phẩm đã tồn tại.";
    } else {
        if (!empty($_FILES["image"]["name"])) {
            // Xử lý upload hình ảnh
            $target_dir = "images/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Kiểm tra trùng lặp hình ảnh
            $sql = "SELECT * FROM products WHERE image = '$target_file' AND id != $id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $error = "Hình ảnh đã được sử dụng cho một sản phẩm khác.";
                $imageUpdated = false;
            } else {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $imageUpdated = true;
                } else {
                    $error = "Lỗi khi tải lên hình ảnh.";
                    $imageUpdated = false;
                }
            }
        }

        if (empty($error)) {
            if ($imageUpdated) {
                $sql = "UPDATE products SET name = '$name', price = '$price', category = '$category', image = '$target_file', description = '$description' WHERE id = $id";
            } else {
                $sql = "UPDATE products SET name = '$name', price = '$price', category = '$category', description = '$description' WHERE id = $id";
            }

            if ($conn->query($sql) === TRUE) {
                header("Location: products.php");
                exit();
            } else {
                $error = "Lỗi: " . $sql . "<br>" . $conn->error;
            }
        }
    }
} else {
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        $error = "Không tìm thấy sản phẩm.";
    }
}
?>

<?php include 'includes/header.php'; ?>
<h2>Sửa Sản phẩm</h2>
<?php if ($product): ?>
<form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Tên Sản phẩm:</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
    </div>
    <div class="form-group">
        <label for="price">Giá:</label>
        <input type="number" step="1" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
    </div>
    <div class="form-group">
        <label for="category">Danh mục:</label>
        <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Mô tả:</label>
        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="image">Hình ảnh:</label>
        <input type="file" class="form-control-file" id="image" name="image">
        <?php if ($product['image']): ?>
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="100">
        <?php endif; ?>
    </div>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">Cập nhật Sản phẩm</button>
</form>
<?php else: ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>

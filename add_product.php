<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    
    // Xử lý upload hình ảnh
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kiểm tra trùng lặp tên sản phẩm
    $sql = "SELECT * FROM products WHERE name = '$name'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $error = "Tên sản phẩm đã tồn tại.";
    } else {
        // Kiểm tra trùng lặp hình ảnh
        $sql = "SELECT * FROM products WHERE image = '$target_file'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $error = "Hình ảnh đã được sử dụng cho một sản phẩm khác.";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $sql = "INSERT INTO products (name, price, category, image, description) VALUES ('$name', '$price', '$category', '$target_file', '$description')";
                if ($conn->query($sql) === TRUE) {
                    header("Location: products.php");
                    exit();
                } else {
                    $error = "Lỗi: " . $sql . "<br>" . $conn->error;
                }
            } else {
                $error = "Lỗi khi tải lên hình ảnh.";
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<h2>Thêm Sản phẩm</h2>
<form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Tên Sản phẩm:</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="price">Giá:</label>
        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
    </div>
    <div class="form-group">
        <label for="category">Danh mục:</label>
        <input type="text" class="form-control" id="category" name="category" required>
    </div>
    <div class="form-group">
        <label for="description">Mô tả:</label>
        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>
    <div class="form-group">
        <label for="image">Hình ảnh:</label>
        <input type="file" class="form-control-file" id="image" name="image" required>
    </div>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">Thêm Sản phẩm</button>
</form>
<?php include 'includes/footer.php'; ?>

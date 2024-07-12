<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db.php';

$sanPhamMoiTrang = 5; // Số sản phẩm mỗi trang
$trang = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$batDau = ($trang - 1) * $sanPhamMoiTrang;

// Biến tìm kiếm và lọc danh mục
$tuKhoaTimKiem = isset($_GET['search']) ? $_GET['search'] : '';
$locDanhMuc = isset($_GET['category']) ? $_GET['category'] : '';

// Lấy tổng số sản phẩm để phân trang
$tongSanPhamSql = "SELECT COUNT(*) as total FROM products WHERE name LIKE ? AND (category = ? OR ? = '')";
$stmt = $conn->prepare($tongSanPhamSql);
$tuKhoaTimKiemLike = "%" . $tuKhoaTimKiem . "%";
$stmt->bind_param("sss", $tuKhoaTimKiemLike, $locDanhMuc, $locDanhMuc);
$stmt->execute();
$tongSanPhamResult = $stmt->get_result();
$tongSanPhamRow = $tongSanPhamResult->fetch_assoc();
$tongSanPham = $tongSanPhamRow['total'];
$tongSoTrang = ceil($tongSanPham / $sanPhamMoiTrang);

$sql = "SELECT * FROM products WHERE name LIKE ? AND (category = ? OR ? = '') LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssii", $tuKhoaTimKiemLike, $locDanhMuc, $locDanhMuc, $batDau, $sanPhamMoiTrang);
$stmt->execute();
$result = $stmt->get_result();

// Lấy danh sách danh mục
$category_sql = "SELECT DISTINCT category FROM products";
$category_result = $conn->query($category_sql);
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Danh sách Sản phẩm</h2>
        <a href="index.php" class="btn btn-secondary"><i class="fas fa-home"></i> Quay trở lại trang chủ</a>
    </div>
    <form method="GET" action="" class="form-inline mb-4">
        <div class="form-group mx-sm-3 mb-2">
            <input type="text" class="form-control" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?php echo htmlspecialchars($tuKhoaTimKiem); ?>">
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <select class="form-control" name="category">
                <option value="">Tất cả danh mục</option>
                <?php while($cat_row = $category_result->fetch_assoc()): ?>
                    <option value="<?php echo $cat_row['category']; ?>" <?php echo $locDanhMuc == $cat_row['category'] ? 'selected' : ''; ?>><?php echo $cat_row['category']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-filter"></i> Lọc</button>
        <a href="add_product.php" class="btn btn-success mb-2 ml-2"><i class="fas fa-plus"></i> Thêm Sản phẩm</a>
    </form>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Giá</th>
                <th>Danh mục</th>
                <th>Hình ảnh</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><?php echo $row['category']; ?></td>
                <td><img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" width="50"></td>
                <td>
                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                    <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Xóa</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <!-- Phân trang -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if($trang > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $trang - 1; ?>&search=<?php echo $tuKhoaTimKiem; ?>&category=<?php echo $locDanhMuc; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $tongSoTrang; $i++): ?>
                <li class="page-item <?php if ($i == $trang) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $tuKhoaTimKiem; ?>&category=<?php echo $locDanhMuc; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <?php if($trang < $tongSoTrang): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $trang + 1; ?>&search=<?php echo $tuKhoaTimKiem; ?>&category=<?php echo $locDanhMuc; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<?php include 'includes/footer.php'; ?>

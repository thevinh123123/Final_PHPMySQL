<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Lỗi: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $error = "Tên đăng nhập đã tồn tại.";
    }
}
?>

<?php include 'includes/header.php'; ?>
<h2>Đăng ký</h2>
<form method="POST" action="">
    <div class="form-group">
        <label for="username">Tên đăng nhập:</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Mật khẩu:</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">Đăng ký</button>
</form>
<?php include 'includes/footer.php'; ?>

<?php
session_start(); // เริ่มต้น session เพื่อจัดการการล็อกอิน

// เชื่อมต่อกับฐานข้อมูล
include('db.php');

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $name = $_POST['name'];
    $password = $_POST['password'];

    // คำสั่ง SQL เพื่อค้นหาผู้ใช้จากชื่อ
    $sql = "SELECT * FROM users WHERE name = '$name'"; 
    $result = $conn->query($sql);

    // ตรวจสอบว่ามีผู้ใช้ในฐานข้อมูลหรือไม่
    if ($result->num_rows > 0) {
        // ผู้ใช้พบ, ดึงข้อมูลผู้ใช้
        $user = $result->fetch_assoc();

        // ตรวจสอบรหัสผ่าน
        if (password_verify($password, $user['password'])) {
            // หากรหัสผ่านถูกต้อง
            $_SESSION['loggedin'] = true;
            $_SESSION['user_name'] = $name;  // เก็บชื่อผู้ใช้ใน session
            $_SESSION['user_id'] = $user['user_id'];  // เก็บ user_id
            $_SESSION['role'] = $user['role'];  // เก็บ role เพื่อใช้งานในการ redirect

            // Redirect ไปหน้าที่เหมาะสมตาม role
            if ($user['role'] == 'manager') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            echo "<script>alert('รหัสผ่านไม่ถูกต้อง');</script>";
        }
    } else {
        echo "<script>alert('ชื่อผู้ใช้ไม่ถูกต้อง');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login - KaeKai Slider Beauty Salon</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- Login Form Start -->
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4 shadow-lg" style="width: 350px;">
            <div class="logo-container-center">
                <img src="img/logo/14-2.png" alt="Beauty Salon Logo" class="logo"> 
            </div>
            <br>
            <h3 class="text-center mb-4">Login</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="text-center mt-3">
                <a href="register.php">Sign Up</a>
            </div>
        </div>
    </div>
    <!-- Login Form End -->

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>

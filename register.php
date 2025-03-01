<?php
// เชื่อมต่อกับฐานข้อมูล
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ตรวจสอบว่ามีผู้ใช้ที่มีอีเมลนี้อยู่ในระบบหรือยัง
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists. Please try another one.');</script>";
    } else {
        // แฮชรหัสผ่านก่อนเก็บ
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // คำสั่ง SQL เพื่อเพิ่มข้อมูลผู้ใช้ใหม่
        $sql = "INSERT INTO users (name, email, password, role) 
                VALUES ('$name', '$email', '$hashed_password', 'customer')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful! You can now log in.');</script>";
            header('Location: login.php');
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Register - KaeKai Slider Beauty Salon</title>
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
    <!-- Register Form Start -->
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4 shadow-lg" style="width: 350px;">
            <div class="logo-container-center">
                <img src="img/logo/14-2.png" alt="Beauty Salon Logo" class="logo"> 
            </div>
            <br>
            <h3 class="text-center mb-4">Register</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php">Already have an account? Login here</a>
            </div>
        </div>
    </div>
    <!-- Register Form End -->

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>

<?php
session_start();
include('db.php');

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลผู้ใช้
$customer_id = $_SESSION['user_id'];
$user_query = $conn->query("SELECT name, email, phone_number, profile_picture FROM users WHERE user_id = '$customer_id'");
$user = $user_query->fetch_assoc();
$customer_name = $user['name'] ?? 'ไม่ระบุ';
$customer_email = $user['email'] ?? 'ไม่ระบุ';
$customer_phone = $user['phone_number'] ?? 'ไม่ระบุ';
$profile_picture = $user['profile_picture'] ?? 'default.png';

// จัดการการอัพเดทข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = isset($_POST['name']) && !empty($_POST['name']) ? $_POST['name'] : $customer_name;
    $new_email = isset($_POST['email']) && !empty($_POST['email']) ? $_POST['email'] : $customer_email;
    $new_phone = isset($_POST['phone_number']) && !empty($_POST['phone_number']) ? $_POST['phone_number'] : $customer_phone;

    // อัพโหลดรูปโปรไฟล์ถ้ามี
    $new_profile_picture = $profile_picture;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['name']) {
        $profile_picture_name = $_FILES['profile_picture']['name'];
        $target = "uploads/profiles/" . basename($profile_picture_name);
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target)) {
            $new_profile_picture = $profile_picture_name;
        } else {
            $error = "ไม่สามารถอัพโหลดรูปโปรไฟล์ได้";
        }
    }

    // อัพเดทข้อมูลในฐานข้อมูล
    $sql = "UPDATE users SET name = '$new_name', email = '$new_email', phone_number = '$new_phone', profile_picture = '$new_profile_picture' WHERE user_id = '$customer_id'";
    if ($conn->query($sql) === TRUE) {
        // อัพเดทข้อมูลใน session
        $_SESSION['user_name'] = $new_name;
        $_SESSION['user_profile_pic'] = $new_profile_picture;
        $success = "บันทึกข้อมูลสำเร็จ";
        $customer_name = $new_name;
        $customer_email = $new_email;
        $customer_phone = $new_phone;
        $profile_picture = $new_profile_picture;
    } else {
        $error = "เกิดข้อผิดพลาดในการบันทึก: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตั้งค่าบัญชี - เก๋ไก๋ สไลเดอร์</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container py-5">
        <div class="text-center mx-auto pb-5" style="max-width: 800px;">
           
            <h1 class="mb-0">ตั้งค่าบัญชี</h1>
        </div>

        <?php if (isset($success)) echo "<div class='alert alert-success text-center'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger text-center'>$error</div>"; ?>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form method="POST" enctype="multipart/form-data">
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h4 class="mb-4">ข้อมูลบัญชี</h4>
                            <div class="mb-3 text-center">
                                <label class="form-label">รูปโปรไฟล์</label>
                                <div>
                                    <img src="uploads/profiles/<?php echo $profile_picture; ?>" class="img-fluid rounded-circle mb-3" style="max-width: 150px;" alt="Profile Picture">
                                </div>
                                <input type="file" name="profile_picture" class="form-control" accept="image/*">
                                <small class="form-text text-muted">อัพโหลดรูปใหม่เพื่อเปลี่ยนรูปโปรไฟล์</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ชื่อ</label>
                                <input type="text" name="name" class="form-control" value="<?php echo $customer_name; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">อีเมล</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $customer_email; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">เบอร์โทรศัพท์</label>
                                <input type="text" name="phone_number" class="form-control" value="<?php echo $customer_phone; ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-hover-bg text-white py-2 px-4">บันทึกการเปลี่ยนแปลง</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
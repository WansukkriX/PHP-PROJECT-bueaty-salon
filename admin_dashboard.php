<?php
session_start();
include('db.php');

// ตรวจสอบว่าเป็นผู้จัดการหรือไม่
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

// Pagination และตัวกรองวันที่สำหรับการจองคิว (appointments)
$appointments_per_page = 7;
$appointments_page = isset($_GET['appointments_page']) ? (int)$_GET['appointments_page'] : 1;
$appointments_offset = ($appointments_page - 1) * $appointments_per_page;
$date_filter = isset($_GET['date_filter']) ? $conn->real_escape_string($_GET['date_filter']) : '';
$appointments_where = $date_filter ? "WHERE a.appointment_date = '$date_filter'" : "";
$total_appointments = $conn->query("SELECT COUNT(*) FROM appointments a $appointments_where")->fetch_row()[0];
$total_appointments_pages = ceil($total_appointments / $appointments_per_page);

// ดึงข้อมูลภาพรวม
$customer_count = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetch_row()[0];
$appointment_count = $conn->query("SELECT COUNT(*) FROM appointments")->fetch_row()[0];
$pending_count = $conn->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetch_row()[0];

// Pagination สำหรับการจองคิว (appointments)
$appointments_per_page = 7;
$appointments_page = isset($_GET['appointments_page']) ? (int)$_GET['appointments_page'] : 1;
$appointments_offset = ($appointments_page - 1) * $appointments_per_page;
$total_appointments = $conn->query("SELECT COUNT(*) FROM appointments")->fetch_row()[0];
$total_appointments_pages = ceil($total_appointments / $appointments_per_page);

// Pagination สำหรับจัดการบริการ (services)
$services_per_page = 7;
$services_page = isset($_GET['services_page']) ? (int)$_GET['services_page'] : 1;
$services_offset = ($services_page - 1) * $services_per_page;
$total_services = $conn->query("SELECT COUNT(*) FROM services")->fetch_row()[0];
$total_services_pages = ceil($total_services / $services_per_page);

//Pagination สำหรับจัดีcustomer
$customers_per_page = 7;
$customers_page = isset($_GET['customers_page']) ? (int)$_GET['customers_page'] : 1;
$customers_offset = ($customers_page - 1) * $customers_per_page;
$total_customers = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetch_row()[0];
$total_customers_pages = ceil($total_customers / $customers_per_page);



// ดึงข้อมูลการจองคิวรายวันและรายเดือน
$daily_bookings = $conn->query("SELECT DATE(appointment_date) AS date, COUNT(*) AS count FROM appointments WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(appointment_date)");
$monthly_bookings = $conn->query("SELECT MONTH(appointment_date) AS month, COUNT(*) AS count FROM appointments WHERE YEAR(appointment_date) = YEAR(CURDATE()) GROUP BY MONTH(appointment_date)");

// จัดการการจองและบริการ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // แก้ไขส่วนการยืนยันการจอง
    if (isset($_POST['verify_appointment'])) {
        $appointment_id = intval($_POST['appointment_id']);
        
        // ตรวจสอบว่ามีข้อมูลการชำระเงินหรือไม่
        $payment_check = $conn->query("SELECT * FROM payments WHERE appointment_id = $appointment_id");
        if ($payment_check->num_rows > 0) {
            // ใช้ค่า user_id จาก session
            $manager_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
            
            // อัพเดตสถานะการชำระเงิน
            $payment_update = $conn->query("UPDATE payments SET payment_status = 'verified', verified_by = $manager_id, verified_at = NOW() WHERE appointment_id = $appointment_id");
            
            // อัพเดตสถานะการจอง
            $appointment_update = $conn->query("UPDATE appointments SET status = 'confirmed' WHERE appointment_id = $appointment_id");
            
            if ($payment_update && $appointment_update) {
                $success = "ยืนยันการจองเรียบร้อยแล้ว";
            } else {
                $error = "เกิดข้อผิดพลาดในการยืนยันการจอง: " . $conn->error;
            }
        } else {
            $error = "ไม่พบข้อมูลการชำระเงินสำหรับการจองนี้";
        }
    }
    
    // แก้ไขส่วนการเลื่อนการจอง
    if (isset($_POST['reschedule_appointment'])) {
        $appointment_id = intval($_POST['appointment_id']);
        $new_date = $conn->real_escape_string($_POST['new_date']);
        $new_time = $conn->real_escape_string($_POST['new_time']);
        
        $reschedule = $conn->query("UPDATE appointments SET appointment_date = '$new_date', appointment_time = '$new_time' WHERE appointment_id = $appointment_id");
        
        if ($reschedule) {
            $success = "เลื่อนการจองเรียบร้อยแล้ว";
        } else {
            $error = "เกิดข้อผิดพลาดในการเลื่อนการจอง: " . $conn->error;
        }
    }
    
    // แก้ไขส่วนการยกเลิกการจอง
    if (isset($_POST['cancel_appointment'])) {
        $appointment_id = intval($_POST['appointment_id']);
        
        $cancel = $conn->query("UPDATE appointments SET status = 'cancelled' WHERE appointment_id = $appointment_id");
        
        if ($cancel) {
            $success = "ยกเลิกการจองเรียบร้อยแล้ว";
        } else {
            $error = "เกิดข้อผิดพลาดในการยกเลิกการจอง: " . $conn->error;
        }
    }
    
    // ส่วนเพิ่มบริการ
    if (isset($_POST['add_service']) && isset($_FILES['image'])) {
        $name = $conn->real_escape_string($_POST['service_name']);
        $description = $conn->real_escape_string($_POST['description']);
        $price = floatval($_POST['price']);
        $deposit = floatval($_POST['deposit']);
        $category_id = intval($_POST['category_id']);
        $image = $conn->real_escape_string($_FILES['image']['name']);
        $target = "uploads/services/" . basename($image);
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $add_service = $conn->query("INSERT INTO services (name, description, price, deposit, category_id, image) VALUES ('$name', '$description', $price, $deposit, $category_id, '$image')");
            
            if ($add_service) {
                $success = "เพิ่มบริการเรียบร้อยแล้ว";
            } else {
                $error = "เกิดข้อผิดพลาดในการเพิ่มบริการ: " . $conn->error;
            }
        } else {
            $error = "เกิดข้อผิดพลาดในการอัพโหลดรูปภาพ";
        }
    }
    
    // ส่วนแก้ไขบริการ
    if (isset($_POST['edit_service'])) {
        $service_id = intval($_POST['service_id']);
        $name = $conn->real_escape_string($_POST['service_name']);
        $description = $conn->real_escape_string($_POST['description']);
        $price = floatval($_POST['price']);
        $deposit = floatval($_POST['deposit']);
        $category_id = intval($_POST['category_id']);
        $image_sql = "";
        
        if (isset($_FILES['image']) && $_FILES['image']['name']) {
            $image = $conn->real_escape_string($_FILES['image']['name']);
            $target = "uploads/services/" . basename($image);
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_sql = ", image = '$image'";
            } else {
                $error = "เกิดข้อผิดพลาดในการอัพโหลดรูปภาพ";
            }
        }
        
        $edit_service = $conn->query("UPDATE services SET name = '$name', description = '$description', price = $price, deposit = $deposit, category_id = $category_id $image_sql WHERE service_id = $service_id");
        
        if ($edit_service) {
            $success = "แก้ไขบริการเรียบร้อยแล้ว";
        } else {
            $error = "เกิดข้อผิดพลาดในการแก้ไขบริการ: " . $conn->error;
        }
    }
    
    // ส่วนการลบบริการ
    if (isset($_POST['delete_service'])) {
        $service_id = intval($_POST['service_id']);
        
        $delete_service = $conn->query("DELETE FROM services WHERE service_id = $service_id");
        
        if ($delete_service) {
            $success = "ลบบริการเรียบร้อยแล้ว";
        } else {
            $error = "เกิดข้อผิดพลาดในการลบบริการ: " . $conn->error;
        }
    }
    
    // ส่วนลบการจอง
    if (isset($_POST['delete_appointment'])) {
        $appointment_id = intval($_POST['appointment_id']);
        
        // ลบข้อมูลการชำระเงินที่เกี่ยวข้องก่อน (ถ้ามี)
        $conn->query("DELETE FROM payments WHERE appointment_id = $appointment_id");
        
        // ลบข้อมูลการจอง
        $delete_appointment = $conn->query("DELETE FROM appointments WHERE appointment_id = $appointment_id");
        
        if ($delete_appointment) {
            $success = "ลบการจองเรียบร้อยแล้ว";
        } else {
            $error = "เกิดข้อผิดพลาดในการลบการจอง: " . $conn->error;
        }
    }
// edit customer
    if (isset($_POST['edit_customer'])) {
        $user_id = intval($_POST['user_id']);
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone_number = $conn->real_escape_string($_POST['phone_number']);
        $profile_picture_sql = "";
        
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['name']) {
            $profile_picture = $conn->real_escape_string($_FILES['profile_picture']['name']);
            $target = "uploads/profiles/" . basename($profile_picture);
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target)) {
                $profile_picture_sql = ", profile_picture = '$profile_picture'";
            } else {
                $error = "เกิดข้อผิดพลาดในการอัพโหลดรูปโปรไฟล์";
            }
        }
        
        $edit_customer = $conn->query("UPDATE users SET name = '$name', email = '$email', phone_number = '$phone_number' $profile_picture_sql WHERE user_id = $user_id AND role = 'customer'");
        if ($edit_customer) {
            $success = "แก้ไขข้อมูลลูกค้าเรียบร้อยแล้ว";
        } else {
            $error = "เกิดข้อผิดพลาดในการแก้ไขลูกค้า: " . $conn->error;
        }
    }
    // การลบลูกค้า
    if (isset($_POST['delete_customer'])) {
        $user_id = intval($_POST['user_id']);
        $conn->query("DELETE FROM payments WHERE appointment_id IN (SELECT appointment_id FROM appointments WHERE customer_id = $user_id)");
        $conn->query("DELETE FROM appointments WHERE customer_id = $user_id");
        $delete_customer = $conn->query("DELETE FROM users WHERE user_id = $user_id AND role = 'customer'");
        if ($delete_customer) {
            $success = "ลบลูกค้าเรียบร้อยแล้ว";
        } else {
            $error = "เกิดข้อผิดพลาดในการลบลูกค้า: " . $conn->error;
        }
    }

    
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>แดชบอร์ดผู้จัดการ - เก๋ไก๋ สไลเดอร์</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>

    .admin-navbar {
            background: #34495e;
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            
        }
        .admin-navbar .navbar-brand {
            font-size: 1.5rem;
            transition: color 0.3s ease;
            
        }
        .admin-navbar .navbar-brand:hover {
            color: #6ab0f0;
        }
        .admin-navbar .nav-link {
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }
        .admin-navbar .nav-link:hover {
            color: #6ab0f0;
        }
        .admin-navbar .btn-danger {
            padding: 0.5rem 1.5rem;
            font-size: 1rem;
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
                body {
                    background: #f5f7fa;
                    font-family: 'Segoe UI', sans-serif;
                    color: #2c3e50;
                }

                .container {
                    max-width: 1400px;
                }

                h1 {
                    font-weight: 700;
                    color: #34495e;
                    text-align: center;
                    margin-bottom: 2rem;
                }

                .card {
                    border: none;
                    border-radius: 20px;
                    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                    background: #fff;
                }

                .card-header {
                    background: #eef2f7;
                    color: #34495e;
                    font-weight: 600;
                    border-radius: 20px 20px 0 0;
                    padding: 1rem 1.5rem;
                }

                .btn-modern {
                    border-radius: 50px;
                    padding: 0.5rem 1.5rem;
                    font-weight: 500;
                    transition: all 0.3s ease;
                }

                .btn-primary {
                    background: #6ab0f0;
                    border: none;
                }

                .btn-primary:hover {
                    background: #4a90e2;
                }

                .btn-success {
                    background: #6edaa6;
                    border: none;
                }

                .btn-success:hover {
                    background: #4db88e;
                }

                .btn-warning {
                    background: #feca57;
                    border: none;
                    color: #333;
                }

                .btn-warning:hover {
                    background: #e6b04e;
                }

                .btn-danger {
                    background: #ff7675;
                    border: none;
                }

                .btn-danger:hover {
                    background: #e65b5a;
                }

                .modal-content {
                    border-radius: 20px;
                    background: #fff;
                }

                .table {
                    border-radius: 10px;
                    overflow: hidden;
                }

                .table thead th {
                    background: #eef2f7;
                    color: #34495e;
                }

                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 1.5rem;
                }

                canvas {
                    max-height: 300px;
                    border-radius: 10px;
                    background: #fff;
                    padding: 1rem;
                }
    </style>
</head>

<body>
    <div class="container py-5">
        <!-- Navbar สำหรับผู้จัดการ -->
        <nav class="navbar navbar-expand-lg admin-navbar fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand text-white fw-semibold" href="admin_dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i> แดชบอร์ด
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                        <li class="nav-item">
                            <a class="nav-link text-white px-3" href="#customers">
                                <i class="fas fa-users me-1"></i> ลูกค้า
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white px-3" href="#appointments">
                                <i class="fas fa-calendar-alt me-1"></i> การจอง
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white px-3" href="#services">
                                <i class="fas fa-cut me-1"></i> บริการ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="btn btn-danger btn-modern ms-3">
                                <i class="fas fa-sign-out-alt me-1"></i> ออกจากระบบ
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- ภาพรวมสถานะ -->
        <br><br>
        <div class="stats-grid mb-5 mt-5">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title"><i class="fas fa-users me-2"></i> จำนวนลูกค้า</h5>
                    <h2><?php echo $customer_count; ?></h2>
                    <a href="#customers" class="btn btn-primary btn-modern mt-2">ดูรายละเอียด</a>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title"><i class="fas fa-calendar-check me-2"></i> การจองทั้งหมด</h5>
                    <h2><?php echo $appointment_count; ?></h2>
                    <a href="#appointments" class="btn btn-primary btn-modern mt-2">ดูรายละเอียด</a>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title"><i class="fas fa-hourglass-half me-2"></i> รอการยืนยัน</h5>
                    <h2><?php echo $pending_count; ?></h2>
                    <a href="#appointments" class="btn btn-primary btn-modern mt-2">ดูรายละเอียด</a>
                </div>
            </div>
        </div>

        <!-- การจองคิวรายวันและรายเดือน -->
        <div class="card mb-5">
            <div class="card-header"><i class="fas fa-chart-line me-2"></i> สถิติการจองคิว</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h5 class="text-center">รายวัน (7 วันล่าสุด)</h5>
                        <canvas id="dailyChart"></canvas>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h5 class="text-center">รายเดือน (ปีนี้)</h5>
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

<!-- ข้อมูลลูกค้า -->
<div class="card mb-5" id="customers">
    <div class="card-header"><i class="fas fa-user-friends me-2"></i> ข้อมูลลูกค้า</div>
    <div class="card-body">
        <?php if (isset($success)) echo "<div class='alert alert-success text-center'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger text-center'>$error</div>"; ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>รูปโปรไฟล์</th>
                    <th>ชื่อ</th>
                    <th>อีเมล</th>
                    <th>เบอร์โทร</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Pagination สำหรับข้อมูลลูกค้า
                $customers_per_page = 7;
                $customers_page = isset($_GET['customers_page']) ? (int)$_GET['customers_page'] : 1;
                $customers_offset = ($customers_page - 1) * $customers_per_page;

                $total_customers = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetch_row()[0];
                $total_customers_pages = ceil($total_customers / $customers_per_page);

                $result = $conn->query("
                    SELECT * FROM users 
                    WHERE role = 'customer' 
                    LIMIT $customers_offset, $customers_per_page
                ");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['user_id']}</td>";
                    echo "<td>" . ($row['profile_picture'] ? "<img src='uploads/profiles/{$row['profile_picture']}' style='width: 40px; height: 40px; object-fit: cover; border-radius: 50%;' alt='Profile'>" : "ไม่มีรูป") . "</td>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td>{$row['phone_number']}</td>";
                    echo "<td>";
                    echo "<button class='btn btn-warning btn-modern btn-sm me-2' data-bs-toggle='modal' data-bs-target='#editCustomerModal{$row['user_id']}'><i class='fas fa-edit'></i> แก้ไข</button>";
                    echo "<form method='POST' class='d-inline' onsubmit=\"return confirm('คุณแน่ใจหรือไม่ที่จะลบลูกค้านี้? การดำเนินการนี้จะลบการจองทั้งหมดของลูกค้าด้วย');\">";
                    echo "<input type='hidden' name='user_id' value='{$row['user_id']}'>";
                    echo "<button type='submit' name='delete_customer' class='btn btn-danger btn-modern btn-sm'><i class='fas fa-trash'></i> ลบ</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";

                    // Modal สำหรับแก้ไขลูกค้า
                    echo "<div class='modal fade' id='editCustomerModal{$row['user_id']}' tabindex='-1'>
                            <div class='modal-dialog modal-lg'>
                                <div class='modal-content'>
                                    <div class='modal-header' style='background: #eef2f7; color: #34495e;'>
                                        <h5 class='modal-title'>แก้ไขลูกค้า #{$row['user_id']}</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                    </div>
                                    <form method='POST' enctype='multipart/form-data'>
                                        <div class='modal-body p-4'>
                                            <input type='hidden' name='user_id' value='{$row['user_id']}'>
                                            <div class='row g-3'>
                                                <div class='col-md-6'>
                                                    <label class='form-label fw-semibold'>ชื่อ</label>
                                                    <input type='text' name='name' class='form-control' value='{$row['name']}' required>
                                                </div>
                                                <div class='col-md-6'>
                                                    <label class='form-label fw-semibold'>อีเมล</label>
                                                    <input type='email' name='email' class='form-control' value='{$row['email']}' required>
                                                </div>
                                                <div class='col-md-6'>
                                                    <label class='form-label fw-semibold'>เบอร์โทร</label>
                                                    <input type='text' name='phone_number' class='form-control' value='{$row['phone_number']}' required>
                                                </div>
                                                <div class='col-md-6'>
                                                    <label class='form-label fw-semibold'>รูปโปรไฟล์ (ถ้ามีการเปลี่ยน)</label>
                                                    <input type='file' name='profile_picture' class='form-control' accept='image/*'>
                                                    <small class='form-text text-muted'>รูปปัจจุบัน: " . ($row['profile_picture'] ? $row['profile_picture'] : 'ไม่มี') . "</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary btn-modern' data-bs-dismiss='modal'>ปิด</button>
                                            <button type='submit' name='edit_customer' class='btn btn-primary btn-modern'>บันทึก</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                          </div>";
                }
                ?>
            </tbody>
        </table>
        <!-- Pagination สำหรับข้อมูลลูกค้า -->
        <nav aria-label="Customers Pagination">
            <ul class="pagination justify-content-center">
                <?php if ($customers_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?customers_page=<?= $customers_page - 1 ?>&appointments_page=<?= $appointments_page ?>&services_page=<?= $services_page ?>&date_filter=<?= urlencode($date_filter) ?>" aria-label="Previous">
                            <span aria-hidden="true">«</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_customers_pages; $i++): ?>
                    <li class="page-item <?= $i == $customers_page ? 'active' : '' ?>">
                        <a class="page-link" href="?customers_page=<?= $i ?>&appointments_page=<?= $appointments_page ?>&services_page=<?= $services_page ?>&date_filter=<?= urlencode($date_filter) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($customers_page < $total_customers_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?customers_page=<?= $customers_page + 1 ?>&appointments_page=<?= $appointments_page ?>&services_page=<?= $services_page ?>&date_filter=<?= urlencode($date_filter) ?>" aria-label="Next">
                            <span aria-hidden="true">»</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>
        <!-- การจองคิว -->
        <div class="card mb-5" id="appointments">
            <div class="card-header"><i class="fas fa-calendar-alt me-2"></i> การจองคิว</div>
            <div class="card-body">
                <?php if (isset($success)) echo "<div class='alert alert-success text-center'>$success</div>"; ?>
                <?php if (isset($error)) echo "<div class='alert alert-danger text-center'>$error</div>"; ?>
                <!-- ฟอร์มตัวกรองวันที่ -->
                <form method="GET" class="mb-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label for="date_filter" class="col-form-label">กรองตามวันที่:</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" id="date_filter" name="date_filter" class="form-control" value="<?= htmlspecialchars($date_filter) ?>">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-modern">กรอง</button>
                            <a href="admin_dashboard.php" class="btn btn-secondary btn-modern">แสดงทั้งหมด</a>
                        </div>
                    </div>
                </form>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ลูกค้า</th>
                            <th>บริการ</th>
                            <th>วันที่</th>
                            <th>เวลา</th>
                            <th>สถานะ</th>
                            <th>หลักฐาน</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("
                            SELECT a.*, u.name AS customer_name, s.name AS service_name, p.payment_slip, p.payment_status 
                            FROM appointments a
                            LEFT JOIN users u ON a.customer_id = u.user_id
                            LEFT JOIN services s ON a.service_id = s.service_id
                            LEFT JOIN payments p ON a.appointment_id = p.appointment_id
                            $appointments_where
                            ORDER BY a.appointment_date ASC
                            LIMIT $appointments_offset, $appointments_per_page
                        ");
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['appointment_id']}</td>";
                            echo "<td>{$row['customer_name']}</td>";
                            echo "<td>{$row['service_name']}</td>";
                            echo "<td>{$row['appointment_date']}</td>";
                            echo "<td>{$row['appointment_time']}</td>";
                            echo "<td>{$row['status']}</td>";
                            echo "<td>" . ($row['payment_slip'] ? "<a href='uploads/slips/{$row['payment_slip']}' target='_blank' class='btn btn-primary btn-modern btn-sm'>ดู</a>" : "ยังไม่อัพโหลด") . "</td>";
                            echo "<td>";
                            if ($row['payment_status'] == 'pending' && $row['payment_slip']) {
                                echo "<form method='POST' class='d-inline'>
                                        <input type='hidden' name='appointment_id' value='{$row['appointment_id']}'>
                                        <button type='submit' name='verify_appointment' class='btn btn-success btn-modern btn-sm'><i class='fas fa-check'></i> ยืนยัน</button>
                                      </form> ";
                            }
                            echo "<button class='btn btn-primary btn-modern btn-sm' data-bs-toggle='modal' data-bs-target='#rescheduleModal{$row['appointment_id']}'><i class='fas fa-calendar-alt'></i> เลื่อน</button> ";
                            echo "<form method='POST' class='d-inline'>
                                    <input type='hidden' name='appointment_id' value='{$row['appointment_id']}'>
                                    <button type='submit' name='cancel_appointment' class='btn btn-warning btn-modern btn-sm'><i class='fas fa-times'></i> ยกเลิก</button>
                                  </form> ";
                            echo "<form method='POST' class='d-inline' onsubmit=\"return confirm('คุณแน่ใจหรือไม่ที่จะลบการจองนี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้');\">
                                    <input type='hidden' name='appointment_id' value='{$row['appointment_id']}'>
                                    <button type='submit' name='delete_appointment' class='btn btn-danger btn-modern btn-sm'><i class='fas fa-trash'></i> ลบ</button>
                                  </form>";
                            echo "</td>";
                            echo "</tr>";

                            // Modal สำหรับเลื่อนการจอง
                            echo "<div class='modal fade' id='rescheduleModal{$row['appointment_id']}' tabindex='-1'>
                                    <div class='modal-dialog'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title'>เลื่อนการจอง #{$row['appointment_id']}</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                            </div>
                                            <form method='POST'>
                                                <div class='modal-body'>
                                                    <input type='hidden' name='appointment_id' value='{$row['appointment_id']}'>
                                                    <div class='mb-3'>
                                                        <label>วันที่ใหม่</label>
                                                        <input type='date' name='new_date' class='form-control' required>
                                                    </div>
                                                    <div class='mb-3'>
                                                        <label>เวลาใหม่</label>
                                                        <input type='time' name='new_time' class='form-control' required>
                                                    </div>
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>ปิด</button>
                                                    <button type='submit' name='reschedule_appointment' class='btn btn-primary btn-modern'>บันทึก</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                  </div>";
                        }
                        ?>
                    </tbody>
                </table>
                <!-- Pagination สำหรับการจองคิว -->
                <nav aria-label="Appointments Pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($appointments_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?appointments_page=<?= $appointments_page - 1 ?>&services_page=<?= $services_page ?>&date_filter=<?= urlencode($date_filter) ?>" aria-label="Previous">
                                    <span aria-hidden="true">«</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_appointments_pages; $i++): ?>
                            <li class="page-item <?= $i == $appointments_page ? 'active' : '' ?>">
                                <a class="page-link" href="?appointments_page=<?= $i ?>&services_page=<?= $services_page ?>&date_filter=<?= urlencode($date_filter) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($appointments_page < $total_appointments_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?appointments_page=<?= $appointments_page + 1 ?>&services_page=<?= $services_page ?>&date_filter=<?= urlencode($date_filter) ?>" aria-label="Next">
                                    <span aria-hidden="true">»</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- จัดการบริการ -->
        <button type="button" class="btn btn-success btn-modern mb-4" data-bs-toggle="modal" data-bs-target="#addServiceModal">
            <i class="fas fa-plus ms-2"></i> เพิ่มบริการใหม่
        </button>

        <!-- Modal สำหรับเพิ่มบริการ -->
        <div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background: #eef2f7; color: #34495e; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title" id="addServiceModalLabel"><i class="fas fa-cut me-2"></i> เพิ่มบริการใหม่</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">ชื่อบริการ</label>
                                    <input type="text" name="service_name" class="form-control" placeholder="เช่น ตัดผมพื้นฐาน" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">คำอธิบาย</label>
                                    <input type="text" name="description" class="form-control" placeholder="เช่น การตัดผมมาตรฐาน" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">ราคา (บาท)</label>
                                    <input type="number" name="price" class="form-control" placeholder="เช่น 300.00" step="0.01" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">ค่ามัดจำ (บาท)</label>
                                    <input type="number" name="deposit" class="form-control" placeholder="เช่น 100.00" step="0.01" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">หมวดหมู่</label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="" disabled selected>เลือกหมวดหมู่</option>
                                        <?php
                                        $categories = $conn->query("SELECT * FROM categories");
                                        while ($cat = $categories->fetch_assoc()) {
                                            echo "<option value='{$cat['category_id']}'>{$cat['category_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">รูปภาพ</label>
                                    <input type="file" name="image" class="form-control" accept="image/*" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">ปิด</button>
                            <button type="submit" name="add_service" class="btn btn-success btn-modern"><i class="fas fa-plus me-2"></i> เพิ่มบริการ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ตารางบริการ -->
        <div class="card mb-5" id="services">
            <div class="card-header" style="background: #eef2f7; color: #34495e;"><i class="fas fa-cut me-2"></i> จัดการบริการ</div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead style="background: #eef2f7; color: #34495e;">
                        <tr>
                            <th>ID</th>
                            <th>ชื่อบริการ</th>
                            <th>คำอธิบาย</th>
                            <th>ราคา</th>
                            <th>ค่ามัดจำ</th>
                            <th>หมวดหมู่</th>
                            <th>รูปภาพ</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("
                            SELECT s.*, c.category_name 
                            FROM services s
                            LEFT JOIN categories c ON s.category_id = c.category_id
                            LIMIT $services_offset, $services_per_page
                        ");
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['service_id']}</td>";
                            echo "<td>{$row['name']}</td>";
                            echo "<td>{$row['description']}</td>";
                            echo "<td>฿" . number_format($row['price'], 2) . "</td>";
                            echo "<td>฿" . number_format($row['deposit'] ?? 0, 2) . "</td>";
                            echo "<td>{$row['category_name']}</td>";
                            echo "<td>" . ($row['image'] ? "<img src='uploads/services/{$row['image']}' style='width: 60px; height: 60px; object-fit: cover; border-radius: 8px;' alt='Service'>" : "ไม่มี") . "</td>";
                            echo "<td>";
                            echo "<button class='btn btn-warning btn-modern btn-sm me-2' data-bs-toggle='modal' data-bs-target='#editServiceModal{$row['service_id']}'><i class='fas fa-edit'></i> แก้ไข</button>";
                            echo "<form method='POST' class='d-inline'>
                                    <input type='hidden' name='service_id' value='{$row['service_id']}'>
                                    <button type='submit' name='delete_service' class='btn btn-danger btn-modern btn-sm'><i class='fas fa-trash'></i> ลบ</button>
                                  </form>";
                            echo "</td>";
                            echo "</tr>";

                            // Modal สำหรับแก้ไขบริการ
                            echo "<div class='modal fade' id='editServiceModal{$row['service_id']}' tabindex='-1'>
                                    <div class='modal-dialog modal-lg'>
                                        <div class='modal-content'>
                                            <div class='modal-header' style='background: #eef2f7; color: #34495e;'>
                                                <h5 class='modal-title'>แก้ไขบริการ #{$row['service_id']}</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                            </div>
                                            <form method='POST' enctype='multipart/form-data'>
                                                <div class='modal-body p-4'>
                                                    <input type='hidden' name='service_id' value='{$row['service_id']}'>
                                                    <div class='row g-3'>
                                                        <div class='col-md-6'>
                                                            <label class='form-label fw-semibold'>ชื่อบริการ</label>
                                                            <input type='text' name='service_name' class='form-control' value='{$row['name']}' required>
                                                        </div>
                                                        <div class='col-md-6'>
                                                            <label class='form-label fw-semibold'>คำอธิบาย</label>
                                                            <input type='text' name='description' class='form-control' value='{$row['description']}' required>
                                                        </div>
                                                        <div class='col-md-4'>
                                                            <label class='form-label fw-semibold'>ราคา (บาท)</label>
                                                            <input type='number' name='price' class='form-control' value='{$row['price']}' step='0.01' required>
                                                        </div>
                                                        <div class='col-md-4'>
                                                            <label class='form-label fw-semibold'>ค่ามัดจำ (บาท)</label>
                                                            <input type='number' name='deposit' class='form-control' value='{$row['deposit']}' step='0.01' required>
                                                        </div>
                                                        <div class='col-md-4'>
                                                            <label class='form-label fw-semibold'>หมวดหมู่</label>
                                                            <select name='category_id' class='form-control' required>";
                            $categories = $conn->query("SELECT * FROM categories");
                            while ($cat = $categories->fetch_assoc()) {
                                $selected = $cat['category_id'] == $row['category_id'] ? 'selected' : '';
                                echo "<option value='{$cat['category_id']}' $selected>{$cat['category_name']}</option>";
                            }
                            echo "                  </select>
                                                        </div>
                                                        <div class='col-md-12'>
                                                            <label class='form-label fw-semibold'>รูปภาพ (ถ้ามีการเปลี่ยน)</label>
                                                            <input type='file' name='image' class='form-control' accept='image/*'>
                                                            <small class='form-text text-muted'>รูปปัจจุบัน: " . ($row['image'] ? $row['image'] : 'ไม่มี') . "</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='button' class='btn btn-secondary btn-modern' data-bs-dismiss='modal'>ปิด</button>
                                                    <button type='submit' name='edit_service' class='btn btn-primary btn-modern'>บันทึก</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                  </div>";
                        }
                        ?>
                    </tbody>
                </table>
                <!-- Pagination สำหรับจัดการบริการ -->
                <nav aria-label="Services Pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($services_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?appointments_page=<?= $appointments_page ?>&services_page=<?= $services_page - 1 ?>&date_filter=<?= urlencode($date_filter) ?>" aria-label="Previous">
                                    <span aria-hidden="true">«</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_services_pages; $i++): ?>
                            <li class="page-item <?= $i == $services_page ? 'active' : '' ?>">
                                <a class="page-link" href="?appointments_page=<?= $appointments_page ?>&services_page=<?= $i ?>&date_filter=<?= urlencode($date_filter) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($services_page < $total_services_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?appointments_page=<?= $appointments_page ?>&services_page=<?= $services_page + 1 ?>&date_filter=<?= urlencode($date_filter) ?>" aria-label="Next">
                                    <span aria-hidden="true">»</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
        <!-- สินสุดการจัดการบริการ ====================================================================-->
   

    <!-- JavaScript Libraries -->
<!-- เพิ่มส่วน JavaScript สำหรับกราฟ Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // แสดงกราฟรายวัน
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    const dailyChart = new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: [
                <?php
                $dates = array();
                while ($row = $daily_bookings->fetch_assoc()) {
                    $dates[] = "'{$row['date']}'";
                }
                echo implode(", ", $dates);
                ?>
            ],
            datasets: [{
                label: 'จำนวนการจอง',
                data: [
                    <?php
                    $daily_bookings->data_seek(0);
                    $counts = array();
                    while ($row = $daily_bookings->fetch_assoc()) {
                        $counts[] = $row['count'];
                    }
                    echo implode(", ", $counts);
                    ?>
                ],
                borderColor: '#4a90e2',
                backgroundColor: 'rgba(74, 144, 226, 0.1)',
                borderWidth: 3,
                tension: 0.2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // แสดงกราฟรายเดือน
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: [
                <?php
                $months = array("ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
                $monthly_data = array_fill(0, 12, 0);
                
                while ($row = $monthly_bookings->fetch_assoc()) {
                    $month_index = intval($row['month']) - 1;
                    $monthly_data[$month_index] = $row['count'];
                }
                
                echo "'" . implode("', '", $months) . "'";
                ?>
            ],
            datasets: [{
                label: 'จำนวนการจอง',
                data: [<?php echo implode(", ", $monthly_data); ?>],
                backgroundColor: '#6edaa6',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});
</script>
</body>
</html>
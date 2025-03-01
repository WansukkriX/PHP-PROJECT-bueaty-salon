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
$user_query = $conn->query("SELECT name, email, phone_number FROM users WHERE user_id = '$customer_id'");
$user = $user_query->fetch_assoc();
$customer_name = $user['name'] ?? 'ไม่ระบุ';
$customer_email = $user['email'] ?? 'ไม่ระบุ';
$customer_phone = $user['phone_number'] ?? 'ไม่ระบุ';

// รับ service_id จาก URL
$selected_service_id = isset($_GET['service_id']) ? $_GET['service_id'] : '';

// ดึงข้อมูลบริการ (รวม deposit)
$services_query = $conn->query("SELECT * FROM services");
$services = [];
while ($row = $services_query->fetch_assoc()) {
    $services[] = $row;
}

// ตรวจสอบจำนวนการจองในแต่ละช่วงเวลา
function check_booking_limit($conn, $service_id, $appointment_date, $appointment_time) {
    $time_slots = [
        '09:00-13:30' => ['09:00:00', '13:30:00'],
        '13:30-17:45' => ['13:30:00', '17:45:00'],
        '17:45-21:45' => ['17:45:00', '21:45:00']
    ];

    foreach ($time_slots as $slot => $range) {
        if ($appointment_time >= $range[0] && $appointment_time <= $range[1]) {
            $sql = "SELECT COUNT(*) as count 
                    FROM appointments 
                    WHERE service_id = '$service_id' 
                    AND appointment_date = '$appointment_date' 
                    AND appointment_time BETWEEN '$range[0]' AND '$range[1]'";
            $result = $conn->query($sql);
            $count = $result->fetch_assoc()['count'];
            return $count < 2; // คืนค่า true ถ้ายังจองได้ (น้อยกว่า 2)
        }
    }
    return false; // ถ้าอยู่นอกช่วงเวลา
}

// จัดการการจอง
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ดึงข้อมูลจากฟอร์ม
    $service_id = isset($_POST['service_id']) && !empty($_POST['service_id']) ? $_POST['service_id'] : '';
    $appointment_date = isset($_POST['appointment_date']) && !empty($_POST['appointment_date']) ? $_POST['appointment_date'] : '';
    $appointment_time = isset($_POST['appointment_time']) && !empty($_POST['appointment_time']) ? $_POST['appointment_time'] : '';
    $phone_number = isset($_POST['phone_number']) && !empty($_POST['phone_number']) ? $_POST['phone_number'] : $customer_phone;

    // ตรวจสอบว่าข้อมูลครบถ้วนหรือไม่ (ลบ stylist_id ออก)
    if ($service_id && $appointment_date && $appointment_time && $phone_number) {
        $today = date('Y-m-d');
        $min_date = date('Y-m-d', strtotime($today . ' +1 day'));
        if ($appointment_date < $min_date) {
            $error = "กรุณาจองล่วงหน้าอย่างน้อย 1 วัน";
        } else if (check_booking_limit($conn, $service_id, $appointment_date, $appointment_time)) {
            // ลบ stylist_id ออกจากการ INSERT
            $sql = "INSERT INTO appointments (customer_id, service_id, appointment_date, appointment_time, status) 
                    VALUES ('$customer_id', '$service_id', '$appointment_date', '$appointment_time', 'pending')";
            if ($conn->query($sql) === TRUE) {
                $appointment_id = $conn->insert_id;

                $conn->query("UPDATE users SET phone_number = '$phone_number' WHERE user_id = '$customer_id'");

                if (isset($_FILES['payment_slip']) && $_FILES['payment_slip']['name']) {
                    $payment_slip = $_FILES['payment_slip']['name'];
                    $target = "uploads/slips/" . basename($payment_slip);
                    if (move_uploaded_file($_FILES['payment_slip']['tmp_name'], $target)) {
                        $service = $conn->query("SELECT price FROM services WHERE service_id = '$service_id'")->fetch_assoc();
                        $amount = $service['price'] ?? 0;
                        $conn->query("INSERT INTO payments (appointment_id, amount, payment_method, payment_slip, payment_status, uploaded_at) 
                                      VALUES ('$appointment_id', '$amount', 'transfer', '$payment_slip', 'pending', NOW())");
                    } else {
                        $error = "ไม่สามารถอัพโหลดสลิปได้";
                    }
                }

                // เปลี่ยนไปหน้า booking_history.php
                header("Location: booking_history.php?booking=success");
                exit();
            } else {
                $error = "เกิดข้อผิดพลาดในการจอง: " . $conn->error;
            }
        } else {
            $error = "ช่วงเวลานี้มีการจองครบ 2 คนแล้ว กรุณาเลือกช่วงเวลาอื่น";
        }
    } else {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
        // Debug: แสดงว่าข้อมูลไหนขาดหาย
        if (!$service_id) $error .= " (ขาด: บริการ)";
        if (!$appointment_date) $error .= " (ขาด: วันที่)";
        if (!$appointment_time) $error .= " (ขาด: เวลา)";
        if (!$phone_number) $error .= " (ขาด: เบอร์โทร)";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จองคิว - เก๋ไก๋ สไลเดอร์</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="text-center mx-auto pb-5" style="max-width: 800px;">
            <!-- <h5 class="text-uppercase text-primary">จองคิวบริการ</h5> -->
            <h1 class="mb-0">จองคิวบริการ</h1>
        </div>

        <?php if (isset($error)) echo "<div class='alert alert-danger text-center'>$error</div>"; ?>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form method="POST" enctype="multipart/form-data">
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h4 class="mb-4">ข้อมูลลูกค้า</h4>
                            <div class="mb-3">
                                <label class="form-label">ชื่อ</label>
                                <input type="text" class="form-control" value="<?php echo $customer_name; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">อีเมล</label>
                                <input type="email" class="form-control" value="<?php echo $customer_email; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">เบอร์โทรศัพท์</label>
                                <input type="text" name="phone_number" class="form-control" value="<?php echo $customer_phone; ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h4 class="mb-4">ข้อมูลการจอง</h4>
                            <div class="mb-3">
                                <label class="form-label">เลือกบริการ</label>
                                <select name="service_id" class="form-control" required>
                                    <option value="" disabled <?php echo empty($selected_service_id) ? 'selected' : ''; ?>>เลือกบริการ</option>
                                    <?php foreach ($services as $service) { ?>
                                        <option value="<?php echo $service['service_id']; ?>" <?php echo $selected_service_id == $service['service_id'] ? 'selected' : ''; ?>>
                                            <?php echo $service['name'] . " - ฿" . number_format($service['price'], 2) . " (มัดจำ ฿" . number_format($service['deposit'], 2) . ")"; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">วันที่จอง</label>
                                <input type="date" name="appointment_date" class="form-control" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                <small class="form-text text-muted">กรุณาจองล่วงหน้าอย่างน้อย 1 วัน (เช่น ถ้าวันนี้วันที่ <?php echo date('d/m/Y'); ?> จองได้ตั้งแต่วันที่ <?php echo date('d/m/Y', strtotime('+1 day')); ?> เป็นต้นไป)</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">เวลาจอง</label>
                                <select name="appointment_time" class="form-control" required>
                                    <option value="" disabled selected>เลือกเวลา</option>
                                    <option value="09:00:00">09:00</option>
                                    <option value="11:15:00">11:15</option>
                                    <option value="13:30:00">13:30</option>
                                    <option value="15:45:00">15:45</option>
                                    <option value="17:45:00">17:45</option>
                                    <option value="20:00:00">20:00</option>
                                </select>
                                <small class="form-text text-muted">ช่วงเวลา: 09:00-13:30, 13:30-17:45, 17:45-21:45 (จำกัด 2 คนต่อช่วง)</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">หลักฐานการโอนเงิน</label>
                                <input type="file" name="payment_slip" class="form-control" accept="image/*" required>
                                <small class="form-text text-muted">กรุณาอัพโหลดสลิปการโอนเงินตาม QR Code ด้านล่าง</small>
                            </div>
                            <div class="row justify-content-center mb-4">
                                <div class="col-lg-6 text-center">
                                    <h5 class="mb-3">QR Code สำหรับการโอนเงิน</h5>
                                    <img src="img/qr1.png" class="img-fluid" style="max-width: 250px;" alt="QR Code">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-hover-bg text-white py-2 px-4 me-2">ยืนยันการจอง</button>
                        <a href="index.php" class="btn btn-secondary btn-hover-bg py-2 px-4"><i class="fas fa-arrow-left me-2"></i>ย้อนกลับ</a>
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
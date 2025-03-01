<?php
session_start();
include('db.php');

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

// ดึงประวัติการจอง
$sql = "SELECT a.*, s.name AS service_name, s.price, p.payment_slip 
        FROM appointments a 
        LEFT JOIN services s ON a.service_id = s.service_id 
        LEFT JOIN payments p ON a.appointment_id = p.appointment_id 
        WHERE a.customer_id = '$customer_id' 
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประวัติการจอง - เก๋ไก๋ สไลเดอร์</title>
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
            <h5 class="text-uppercase text-primary">ประวัติการจอง</h5>
            <h1 class="mb-0">KaeKai Slider Beauty Salon</h1>
        </div>

        <?php if (isset($_GET['booking']) && $_GET['booking'] == 'success') echo "<div class='alert alert-success text-center'>จองสำเร็จ!</div>"; ?>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>วันที่จอง</th>
                            <th>เวลาจอง</th>
                            <th>บริการ</th>
                            <th>ราคา</th>
                            <th>สถานะ</th>
                            <th>หลักฐานการโอน</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($row['appointment_date'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($row['appointment_time'])); ?></td>
                                    <td><?php echo $row['service_name']; ?></td>
                                    <td>฿<?php echo number_format($row['price'], 2); ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                    <td>
                                        <?php if ($row['payment_slip']) { ?>
                                            <a href="uploads/slips/<?php echo $row['payment_slip']; ?>" target="_blank">ดูสลิป</a>
                                        <?php } else { ?>
                                            ไม่มี
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>ไม่มีประวัติการจอง</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
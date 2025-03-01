<?php
// ไม่ต้อง session_start() ที่นี่ เพราะไฟล์หลัก (เช่น index.php, book_appointment.php) มีแล้ว
include('db.php');

// ถ้า role = manager ให้ redirect ไป admin_dashboard.php
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['role'] == 'manager' && basename($_SERVER['PHP_SELF']) != 'admin_dashboard.php') {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto&display=swap" rel="stylesheet">
    

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar start -->
    <div class="container-fluid fixed-top px-0">
    <div class="container px-0">
        <nav class="navbar navbar-light bg-light navbar-expand-xl">
            <a href="index.php" class="navbar-brand ms-3">
                <img src="img/logo/14-1.png" alt="KAEKAI Logo" class="logo" />
            </a>
            <button class="navbar-toggler py-2 px-3 me-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars text-primary"></span>
            </button>
            <div class="collapse navbar-collapse bg-light" id="navbarCollapse">
                <div class="navbar-nav ms-auto">
                    <a href="index.php" class="nav-item nav-link">Home</a>

                    <div class="nav-item dropdown">
                        <a href="#Our Services" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Our Services</a>
                        <div class="dropdown-menu m-0 bg-secondary rounded-0">
                            <?php
                            // ดึงข้อมูลหมวดหมู่จากตาราง categories
                            $categories_query = $conn->query("SELECT * FROM categories");
                            while ($category = $categories_query->fetch_assoc()) {
                                echo "<a href='service_category.php?category_id={$category['category_id']}' class='dropdown-item'>" . htmlspecialchars($category['category_name']) . "</a>";
                            }
                            ?>
                        </div>
                    </div>
                    <a href="book_appointment.php" class="nav-item nav-link">Book Now</a>
                    <a href="contact.php" class="nav-item nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">Contact</a>
                </div>
                <div class="d-flex align-items-center flex-nowrap pt-xl-0" style="margin-left: 15px;">
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
                        <!-- If user is logged in, show profile info with dropdown -->
                        <div class="nav-item dropdown">
                            <a href="#" class="btn btn-secondary py-2 px-4 me-3 dropdown-toggle" data-bs-toggle="dropdown">
                                <?php
                                // ตรวจสอบว่า user_profile_pic มีค่าหรือไม่
                                $profile_pic = isset($_SESSION['user_profile_pic']) && !empty($_SESSION['user_profile_pic']) ? $_SESSION['user_profile_pic'] : 'default.png';
                                ?>
                                <!-- แสดงรูปโปรไฟล์ -->
                                <img src="uploads/profiles/<?php echo $profile_pic; ?>" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px;">
                                <?php echo $_SESSION['user_name'] ?? 'Guest'; ?>
                            </a>
                            <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                <a href="booking_history.php" class="dropdown-item">ประวัติการจอง</a>
                                <a href="account_settings.php" class="dropdown-item">ตั้งค่าบัญชี</a>
                                <a href="logout.php" class="dropdown-item text-danger">ออกจากระบบ</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- If user is not logged in, show Log In button -->
                        <a href="login.php" class="btn-hover-bg btn btn-primary text-white py-2 px-4 me-3">Log In</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </div>
</div>
    <!-- Navbar End -->

    <!-- Include Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
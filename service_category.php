<?php
session_start();
include('db.php');

// ตรวจสอบว่า category_id ถูกส่งมาหรือไม่
if (!isset($_GET['category_id']) || empty($_GET['category_id'])) {
    header("Location: index.php");
    exit();
}

$category_id = $_GET['category_id'];

// ดึงข้อมูลหมวดหมู่
$category_query = $conn->query("SELECT category_name FROM categories WHERE category_id = '$category_id'");
$category = $category_query->fetch_assoc();
$category_name = $category['category_name'] ?? 'หมวดหมู่ไม่ระบุ';

// ดึงข้อมูลบริการในหมวดหมู่
$sql = "SELECT * FROM services WHERE category_id = '$category_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $category_name; ?> - KaeKai Slider Beauty Salon</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

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
    <!-- Spinner Start -->
    <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <?php include('navbar.php'); ?>
    <!-- Navbar End -->

    <!-- Events Start -->
    <section id="Our Services" class="Our Services">
        <div class="container-fluid event py-5">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5" style="max-width: 800px;">
                    <h5 class="text-uppercase text-primary">Promotions</h5>
                    <h1 class="mb-0">March more Promotions this Year 2025!</h1>
                </div>
                <div class="event-carousel owl-carousel">
                    <div class="event-item">
                        <img src="img/pro/02.png" class="img-fluid w-100" alt="Image">
                    </div>
                    <div class="event-item">
                        <img src="img/pro/0.png" class="img-fluid w-100" alt="Image">
                    </div>
                    <div class="event-item">
                        <img src="img/pro/5.png" class="img-fluid w-100" alt="Image">
                    </div>
                    <div class="event-item">
                        <img src="img/pro/01.png" class="img-fluid w-100" alt="Image">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Events End -->

    <!-- Blog Start -->
    <div class="container-fluid blog py-5 mb-5" id="show_services">
        <div class="container py-5">
            <div class="text-center mx-auto pb-5" style="max-width: 800px;">
                <h5 class="text-uppercase text-primary">Hair Services</h5>
                <h1 class="mb-0"><?php echo $category_name; ?></h1>
            </div>
            <div class="row g-4">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <div class="col-lg-6 col-xl-3">
                            <div class="blog-item">
                                <div class="blog-img">
                                    <img src="uploads/services/<?php echo $row['image']; ?>" class="img-fluid w-100" alt="Service Image">
                                    <div class="blog-info">
                                        <span><i class=""></i>฿<?php echo number_format($row['price'], 2); ?></span>
                                        <div class="d-flex">
                                            <span class="me-3"> 10 <i class="fa fa-heart"></i></span>
                                            <a href="#" class="text-white">0 <i class="fa fa-comment"></i></a>
                                        </div>
                                    </div>
                                    <div class="search-icon">
                                        <a href="uploads/services/<?php echo $row['image']; ?>" data-lightbox="<?php echo $row['service_id']; ?>" class="my-auto"><i class="fas fa-search-plus btn-primary text-white p-3"></i></a>
                                    </div>
                                </div>
                                <div class="text-dark border p-4">
                                    <h4 class="mb-4"><?php echo $row['name']; ?></h4>
                                    <p class="mb-4"><?php echo $row['description']; ?></p>
                                    <a class="btn-hover-bg btn btn-primary text-white py-2 px-4" href="book_appointment.php?service_id=<?php echo $row['service_id']; ?>">จองเลย</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>ไม่พบข้อมูลบริการในหมวดหมู่ $category_name</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <!-- Blog End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright py-4">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-4 text-center text-md-start mb-md-0">
                    <a href="index.php" class="navbar-brand ms-3">
                        <img src="img/logo/14.png" alt="KAEKAI Logo" class="logo" />
                    </a>
                </div>
                <div class="col-md-4 text-center text-md-start mb-md-0">
                    <span class="text-body"><a href="#"><i class="fas fa-copyright text-light me-2"></i>KaeKai Slider Beauty Salon</a>, All right reserved.</span>
                </div>
                <div class="col-md-4 text-center text-md-end text-body">
                    <div class="d-flex align-items-center justify-content-center">
                        <a href="https://www.facebook.com/share/18Sd6yCibN/" class="btn-hover-color btn-square text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.tiktok.com/@kaekai.slider.salon?_t=ZS-8u2W3j4N75R&_r=1" class="btn-hover-color btn-square text-white me-2"><i class="fab fa-tiktok"></i></a>
                        <a href="" class="btn-hover-color btn-square text-white me-2"><i class="fab fa-phone-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-primary-outline-0 btn-md-square back-to-top"><i class="fa fa-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>
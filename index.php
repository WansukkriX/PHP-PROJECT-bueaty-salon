<?php 
session_start();
include('db.php'); // รวมการเชื่อมต่อจาก db.php
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>KaeKai Slider Beauty Salon</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">
    </head>
    <body>
        <!-- Spinner Start -->
        <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" role="status"></div>
        </div>
        <!-- Spinner End -->

        <!-- Navbar Start -->
        <?php include('navbar.php'); ?>
        <!-- Navbar End -->

        <!-- Carousel Start -->
        <div class="container-fluid carousel-header vh-100 px-0">
            <div id="carouselId" class="carousel slide" data-bs-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-bs-target="#carouselId" data-bs-slide-to="0" class="active"></li>
                    <li data-bs-target="#carouselId" data-bs-slide-to="1"></li>
                    <li data-bs-target="#carouselId" data-bs-slide-to="2"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                    <div class="carousel-item active">
                        <img src="img/bg/4-1.jpg" class="img-fluid" alt="Image">
                        <div class="carousel-caption">
                            <div class="p-3" style="max-width: 900px;">
                                <h4 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">welcome for beauty salon</h4>
                                <h3 class="display-1 text-capitalize text-white mb-4">ควย ควย ควย ควย</h3>
                                <!-- <h3 class="display-1 text-capitalize text-white mb-4">KaeKai Slider</h3> -->
                                <p class="mb-5 fs-5">KaeKai Slider Beauty Salon, one stop beauty salon service since 2010. We seek to deliver valuable experiences with the best & premium qualified products. 
                                </p>
                                <div class="d-flex align-items-center justify-content-center">
                                    <a class="btn-hover-bg btn btn-primary text-white py-3 px-5" href="#show_services">Book Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="img/bg/3-1.jpg" class="img-fluid" alt="Image">
                        <div class="carousel-caption">
                            <div class="p-3" style="max-width: 900px;">
                                <h4 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">welcome for beauty salon</h4>
                                <h3 class="display-1 text-capitalize text-white mb-4">KaeKai Slider</h3>
                                <p class="mb-5 fs-5">KaeKai Slider Beauty Salon, one stop beauty salon service since 2010. We seek to deliver valuable experiences with the best & premium qualified products. 
                                </p>
                                <div class="d-flex align-items-center justify-content-center">
                                    <a class="btn-hover-bg btn btn-primary text-white py-3 px-5" href="login.php">Book Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="img/bg/2-1.jpg" class="img-fluid" alt="Image">
                        <div class="carousel-caption">
                            <div class="p-3" style="max-width: 900px;">
                                <h4 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">welcome for beauty salon</h4>
                                <h3 class="display-1 text-capitalize text-white mb-4">KaeKai Slider</h3>
                                <p class="mb-5 fs-5">KaeKai Slider Beauty Salon, one stop beauty salon service since 2010. We seek to deliver valuable experiences with the best & premium qualified products. 
                                </p>
                                <div class="d-flex align-items-center justify-content-center">
                                    <a class="btn-hover-bg btn btn-primary text-white py-3 px-5" href="login.php">Book Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        <!-- Carousel End -->

        <!-- About Start -->
        <div class="container-fluid about py-5">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-xl-5">
                        <div class="h-100">
                            <img src="img/bg/8.png" class="img-fluid w-100 h-100" alt="Image">
                        </div>
                    </div>
                    <div class="col-xl-7">
                        <h5 class="text-uppercase text-primary">About Us</h5>
                        <h1 class="mb-4">KAEKAI SLIDER BEAUTY SALON</h1>
                        <!-- <h1 class="mb-4">KAEKAI SLIDER BEAUTY SALON</h1> -->
                        <p class="fs-5 mb-4">เราเชื่อว่าทรงผมไม่ใช่แค่การตัดแต่ง แต่เป็นศิลปะที่สะท้อนตัวตน และสไตล์ของแต่ละคน นี่คือจุดเริ่มต้นของร้านเก๋ไก๋สไลเดอร์ บิวตี้ซาลอน ร้านเสริมสวยที่ไม่ได้เป็นเพียงแค่ร้านตัดผม แต่คือพื้นที่สำหรับคนที่ต้องการเปลี่ยนลุค เสริมความมั่นใจ และสร้างเอกลักษณ์ให้ตัวเอง
ก่อตั้งขึ้นในปี 2010 ด้วยความหลงใหลในศิลปะแห่งทรงผม เราไม่เพียงแค่ให้บริการตัดผม ซอย สระ และทำสี แต่ยังใส่ใจในทุกรายละเอียด เพื่อให้ลูกค้าได้รับประสบการณ์ที่ดีที่สุด ไม่ว่าจะเป็นสไตล์คลาสสิก หรือเทรนด์ใหม่ล่าสุด ทีมงานของเราพร้อมให้คำแนะนำและสร้างสรรค์ลุคที่เหมาะกับคุณที่สุด
เราเชื่อว่า "ทรงผมที่ดี เปลี่ยนชีวิตคุณได้" และเราพร้อมเป็นส่วนหนึ่งในการเปลี่ยนแปลงครั้งนี้                        
</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->

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
            <h1 class="mb-0">KaeKai Slider Beauty Salon</h1>
        </div>
        <div class="row g-4">
            <?php
            // ดึงข้อมูลจากตาราง services และ categories โดยดึงตัวแทนจากแต่ละหมวดหมู่
            $sql = "SELECT s.*, c.category_name 
                    FROM services s 
                    LEFT JOIN categories c ON s.category_id = c.category_id 
                    GROUP BY s.category_id 
                    LIMIT 4";
            $result = $conn->query($sql);

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
                                <h4 class="mb-4"><?php echo $row['category_name']; ?></h4>
                                <p class="mb-4"><?php echo $row['description']; ?></p>
                                <a class="btn-hover-bg btn btn-primary text-white py-2 px-4" href="service_category.php?category_id=<?php echo $row['category_id']; ?>">ดูบริการทั้งหมด</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>ไม่พบข้อมูลบริการ</p>";
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
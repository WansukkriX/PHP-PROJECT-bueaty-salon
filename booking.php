<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Booking - Environs</title>
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
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4 shadow-lg" style="width: 400px;">
            <h3 class="text-center mb-4">Booking</h3>
            <h5 class="text">Book an Appointment for: <?php echo htmlspecialchars($service['name']); ?></h5>
            <p>Service Details: <?php echo htmlspecialchars($service['description']); ?></p>
            <p class="price">Price: <?php echo number_format($service['price']); ?> ฿</p>

            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($service['id']); ?>">
                
                <div class="mb-3">
                    <label for="customer_name" class="form-label">Customer Name:</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="stylist_id" class="form-label">Select a Stylist:</label>
                    <select class="form-control" id="stylist_id" name="stylist_id" required>
                        <option value="">-- Select a Stylist --</option>
                        <?php
                        if ($stylists_result->num_rows > 0) {
                            while($stylist = $stylists_result->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($stylist['id']) . "'>" . 
                                     htmlspecialchars($stylist['name']) . "</option>";
                            }
                        } else {
                            echo "<option value=''>No stylists available</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="booking_date" class="form-label">Booking Date:</label>
                    <input type="date" class="form-control" id="booking_date" name="booking_date" required>
                </div>

                <div class="mb-3">
                    <label for="booking_time" class="form-label">Booking Time:</label>
                    <input type="time" class="form-control" id="booking_time" name="booking_time" required>
                </div>

                <div class="mb-3">
                    <label for="deposit" class="form-label">Deposit Amount (฿):</label>
                    <input type="number" class="form-control" id="deposit" name="deposit" required>
                </div>

                <div class="mb-3 text-center">
                    <h5>Payment Method</h5>
                    <img src="img/ser/pay.jpg" alt="QR Code for Payment" class="img-fluid">
                    <p>Please scan the QR Code to make a deposit payment.</p>
                </div>

                <div class="mb-3">
                    <label for="transfer_proof" class="form-label">Upload Payment Proof:</label>
                    <input type="file" class="form-control" id="transfer_proof" name="transfer_proof" accept="image/*" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Confirm Booking</button>
            </form>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
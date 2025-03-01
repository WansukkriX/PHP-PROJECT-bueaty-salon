<div class="container mt-5">
        <h2 class="text-center">บริการทั้งหมดในหมวดหมู่: <?php echo htmlspecialchars($category['category_name']); ?></h2>
        <div class="row mt-4">
            <?php if ($services_result->num_rows > 0) : ?>
                <?php while ($row = $services_result->fetch_assoc()) : ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card">
                            <img src="uploads/services/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                                <p class="text-primary">ราคา: ฿<?php echo number_format($row['price'], 2); ?></p>
                                <a href="book_service.php?service_id=<?php echo $row['service_id']; ?>" class="btn btn-primary">จองบริการนี้</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p class="text-center">ไม่มีบริการในหมวดหมู่นี้</p>
            <?php endif; ?>
        </div>
    </div>
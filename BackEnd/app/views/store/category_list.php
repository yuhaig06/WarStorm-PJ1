<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh mục sản phẩm | Store</title>
    <link rel="stylesheet" href="/PJ1/FrontEnd/Store/css/store.css">
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
</head>
<body>
    <div class="category-list-container">
        <h1 class="category-list-title">Tất cả danh mục sản phẩm</h1>
        <div class="category-list-grid">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <a class="category-list-item" href="<?php echo URLROOT; ?>/store/category/<?php echo $category->id; ?>">
                        <div class="category-list-item-name"><?php echo htmlspecialchars($category->name); ?></div>
                        <div class="category-list-item-desc"><?php echo htmlspecialchars($category->description); ?></div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="category-list-empty">Không có danh mục nào.</div>
            <?php endif; ?>
        </div>
        <div style="text-align:center;margin-top:24px;">
            <a href="<?php echo URLROOT; ?>/store" class="category-btn">Về trang sản phẩm</a>
        </div>
    </div>
</body>
</html>

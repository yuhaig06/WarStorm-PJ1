<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm mới</title>
    <link rel="stylesheet" href="/PJ1/FrontEnd/Store/css/store.css">
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h2>Thêm sản phẩm mới</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/store/add" method="POST" enctype="multipart/form-data" class="add-product-form">
                        <div class="form-group">
                            <label for="name">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo isset($data['name']) ? htmlspecialchars($data['name']) : ''; ?>">
                            <div class="error"><?php echo isset($data['name_err']) ? $data['name_err'] : ''; ?></div>
                        </div>

                        <div class="form-group">
                            <label for="category_id">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-control">
                                <option value="">Chọn danh mục</option>
                                <?php if(isset($data['categories'])) foreach($data['categories'] as $category): ?>
                                    <option value="<?php echo $category->id; ?>" <?php echo (isset($data['category_id']) && $data['category_id'] == $category->id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="error"><?php echo isset($data['category_id_err']) ? $data['category_id_err'] : ''; ?></div>
                        </div>

                        <div class="form-group">
                            <label for="price">Giá <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" min="0" value="<?php echo isset($data['price']) ? htmlspecialchars($data['price']) : ''; ?>">
                            <div class="error"><?php echo isset($data['price_err']) ? $data['price_err'] : ''; ?></div>
                        </div>

                        <div class="form-group">
                            <label for="stock">Số lượng tồn kho <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control" min="0" value="<?php echo isset($data['stock']) ? htmlspecialchars($data['stock']) : ''; ?>">
                            <div class="error"><?php echo isset($data['stock_err']) ? $data['stock_err'] : ''; ?></div>
                        </div>

                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea name="description" class="form-control"><?php echo isset($data['description']) ? htmlspecialchars($data['description']) : ''; ?></textarea>
                            <div class="error"><?php echo isset($data['description_err']) ? $data['description_err'] : ''; ?></div>
                        </div>

                        <div class="form-group">
                            <label for="image">Hình ảnh</label>
                            <input type="file" name="image" class="form-control-file" accept="image/*">
                            <div class="error"><?php echo isset($data['image_err']) ? $data['image_err'] : ''; ?></div>
                            <small class="form-text text-muted">Chấp nhận: JPG, PNG, GIF. Kích thước tối đa: 2MB</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
                            <a href="<?php echo URLROOT; ?>/store/manage" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php require APPROOT . '/app/views/error/header.php'; ?>

<div class="error-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="error-box">
                    <h1 class="error-code" style="color:#1ea7fd">403</h1>
                    <h2 class="error-title">Truy cập bị từ chối</h2>
                    <p class="error-message">Bạn không có quyền truy cập vào trang này.<br>Vui lòng liên hệ admin nếu bạn nghĩ đây là lỗi.</p>
                    <div class="error-actions">
                        <a href="<?php echo URLROOT; ?>" class="btn-home">Về trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .error-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #181c24;
        padding: 20px;
    }
    .error-box {
        background: #23272f;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .error-code {
        font-size: 120px;
        font-weight: 700;
        background: linear-gradient(90deg,#1ea7fd 60%,#4f8cff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0;
        line-height: 1;
    }
    .error-title {
        font-size: 32px;
        color: #e0e2e7;
        margin: 20px 0;
    }
    .error-message {
        font-size: 18px;
        color: #b0b3b8;
        margin-bottom: 30px;
    }
    .error-actions {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 20px;
    }
    .btn-home {
        display: inline-block;
        padding: 12px 34px;
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 999px;
        background: linear-gradient(90deg,#1ea7fd 60%,#4f8cff 100%);
        color: #fff;
        text-decoration: none;
        box-shadow: 0 2px 14px rgba(30,167,253,0.13);
        transition: all 0.2s ease;
    }
    .btn-home:hover {
        background: #ff9800;
        color: #fff;
        transform: translateY(-2px) scale(1.04);
        box-shadow: 0 4px 20px rgba(255,152,0,0.13);
    }
    @media (max-width: 768px) {
        .error-code {
            font-size: 80px;
        }
        .error-title {
            font-size: 24px;
        }
        .error-message {
            font-size: 16px;
        }
        .error-actions {
            flex-direction: column;
            gap: 15px;
        }
        .btn-home {
            width: 100%;
            text-align: center;
        }
    }
</style>

<?php require APPROOT . '/app/views/error/footer.php'; ?> 
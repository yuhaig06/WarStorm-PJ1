<?php
// Hiển thị danh sách game theo category (slug)
// Biến $games được truyền từ controller
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= !empty($overview) ? htmlspecialchars($overview['title']) : 'Không tìm thấy game!' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
    <style>
        body {
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
            background: #181c24;
            color: #e0e2e7;
            margin: 0;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 36px auto 20px auto;
            background: #23272f;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(24,28,50,0.15);
            padding: 36px 22px 24px 22px;
        }
        .game-content h1, .game-content h2, .game-content h3 {
            color: #fff;
            font-weight: 800;
            margin-bottom: 18px;
            text-align: left;
        }
        .game-content h2 {
            color: #1ea7fd;
            font-size: 2rem;
        }
        .game-content h3 {
            color: #ff9800;
        }
        .game-content p, .game-content ul, .game-content ol {
            color: #e0e2e7;
            font-size: 1.08rem;
            line-height: 1.7;
        }
        .game-content ul, .game-content ol {
            margin-left: 24px;
            margin-bottom: 16px;
        }
        .game-content li {
            margin-bottom: 8px;
        }
        .game-content img {
            max-width: 100%;
            border-radius: 12px;
            margin: 18px 0;
            box-shadow: 0 2px 14px rgba(24,28,50,0.17);
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .game-content a {
            color: #1ea7fd;
            text-decoration: underline;
            transition: color 0.2s;
            word-break: break-all;
        }
        .game-content a:hover {
            color: #ff9800;
            text-decoration: none;
        }
        .game-content blockquote {
            border-left: 4px solid #1ea7fd;
            background: #232c3b;
            color: #b0c4de;
            margin: 18px 0;
            padding: 12px 24px;
            font-style: italic;
            border-radius: 6px;
        }
        .game-content table {
            width: 100%;
            border-collapse: collapse;
            background: #232c3b;
            margin: 18px 0;
            border-radius: 8px;
            overflow: hidden;
        }
        .game-content th, .game-content td {
            border: 1px solid #1ea7fd22;
            padding: 10px 14px;
            color: #e0e2e7;
            text-align: left;
        }
        .game-content th {
            background: #1ea7fd33;
            color: #fff;
        }
        .game-content code, .game-content pre {
            background: #181c24;
            color: #1ea7fd;
            border-radius: 6px;
            padding: 2px 7px;
            font-size: 1em;
            font-family: 'Fira Mono', 'Consolas', 'Menlo', monospace;
        }
        .game-content pre {
            display: block;
            padding: 12px 14px;
            overflow-x: auto;
            margin: 18px 0;
        }
        .game-content .btn, .game-content button, .game-content input[type="button"], .game-content input[type="submit"] {
            background: #1ea7fd;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 22px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            margin: 8px 0;
            box-shadow: 0 2px 8px rgba(24,28,50,0.10);
            transition: background 0.2s, color 0.2s;
        }
        .game-content .btn:hover, .game-content button:hover, .game-content input[type="button"]:hover, .game-content input[type="submit"]:hover {
            background: #ff9800;
            color: #fff;
        }
        .game-overview {
            padding: 0;
        }
        .game-overview h2 {
            color: #1ea7fd;
            margin-bottom: 18px;
        }
        .game-overview img {
            margin-bottom: 24px;
        }
        /* Responsive */
        @media (max-width: 600px) {
            .container {
                padding: 12px 2vw 12px 2vw;
            }
            .game-content h1, .game-content h2, .game-content h3 {
                font-size: 1.15em;
            }
            .download-btn-group {
                flex-direction: column;
                gap: 12px;
            }
        }
    .download-btn-group {
        margin-top: 30px;
        display: flex;
        flex-wrap: wrap;
        gap: 18px;
        justify-content: center;
        align-items: center;
        position: relative;
        min-height: 110px;
    }
    /* Tam giác layout cho 3 nút */
    .download-btn-group.triangle {
        flex-direction: column;
        align-items: center;
        min-height: 110px;
        position: relative;
    }
    .download-btn-group.triangle .btn-download.pc {
        position: relative;
        left: 0;
        top: 0;
        margin-bottom: 18px;
        z-index: 2;
    }
    .download-btn-group.triangle .btn-download.gp {
        position: absolute;
        left: 0;
        bottom: 0;
        z-index: 1;
    }
    .download-btn-group.triangle .btn-download.ios {
        position: absolute;
        right: 0;
        bottom: 0;
        z-index: 1;
    }
    @media (max-width: 600px) {
        .download-btn-group.triangle {
            min-height: 150px;
        }
        .download-btn-group.triangle .btn-download.pc {
            margin-bottom: 38px;
        }
        .download-btn-group.triangle .btn-download.gp {
            left: 5%;
            bottom: 0;
        }
        .download-btn-group.triangle .btn-download.ios {
            right: 5%;
            bottom: 0;
        }
    }
    .btn-home {
        display: inline-block;
        margin-bottom: 28px;
        padding: 12px 34px;
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 999px;
        background: linear-gradient(90deg,#1ea7fd 60%,#4f8cff 100%);
        color: #fff;
        text-decoration: none !important;
        box-shadow: 0 2px 14px rgba(30,167,253,0.13);
        transition: background 0.2s, color 0.2s, transform 0.15s;
        border: none;
        outline: none;
    }
    .btn-home:hover {
        background: #ff9800;
        color: #fff;
        transform: translateY(-2px) scale(1.04);
        box-shadow: 0 4px 20px rgba(255,152,0,0.13);
    }
    .btn-download {
        display: inline-block;
        padding: 12px 28px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 700;
        background: linear-gradient(90deg,#1ea7fd 60%,#4f8cff 100%);
        color: #fff;
        text-decoration: none !important;
        box-shadow: 0 2px 12px rgba(30,167,253,0.16);
        transition: background 0.2s, color 0.2s, transform 0.15s;
        margin-bottom: 8px;
    }
    .btn-download.ios {
        background: linear-gradient(90deg,#23272f 60%,#1ea7fd 100%);
    }
    .btn-download:hover {
        background: #ff9800;
        color: #fff;
        transform: translateY(-2px) scale(1.04);
        box-shadow: 0 4px 20px rgba(255,152,0,0.13);
    }
    </style>
</head>
<body>
<div class="container">
    <a href="/PJ1/public/home" class="btn-home">Về trang chủ</a>
<?php
if (!empty($overview)) {
    ?>
    <div class="game-overview game-content">
        <h2><?= htmlspecialchars($overview['title']) ?></h2>
        <div>
        <?php
        // Loại bỏ các dòng header, menu, link đầu trang không cần thiết
        $content = $overview['content'];
        $content = preg_replace('/(<a[^>]*>.*?(Quay Lại Trang Chủ|Trang chủ|Tin tức|ESPORTS|Store|Liên hệ).*?<\/a>)/ius', '', $content);
        $content = preg_replace('/☰/u', '', $content);
        // Xóa các block <nav>...</nav> hoặc <ul>...</ul> đầu trang nếu chứa các từ khóa menu
        $content = preg_replace('/<nav[^>]*>.*?(Quay Lại Trang Chủ|Trang chủ|Tin tức|ESPORTS|Store|Liên hệ).*?<\/nav>/ius', '', $content);
        $content = preg_replace('/<ul[^>]*>.*?(Quay Lại Trang Chủ|Trang chủ|Tin tức|ESPORTS|Store|Liên hệ).*?<\/ul>/ius', '', $content);
        // Xóa dòng đơn lẻ nếu còn sót
        $content = preg_replace('/^.*?(Quay Lại Trang Chủ|Trang chủ|Tin tức|ESPORTS|Store|Liên hệ).*?$/ium', '', $content);
        // Xóa toàn bộ đoạn HTML hoặc text đầu content nếu chứa 'Liên Quân Mobile' mà không phải tiêu đề chính
        $content = preg_replace('/^(\s*<(p|div|span)[^>]*>\s*)?Liên Quân Mobile(\s*<\/\2>)?/iu', '', $content, 1);
        // Xóa mọi dòng chỉ chứa ảnh hoặc chữ 'Liên Quân Mobile' ở đầu content
        $content = preg_replace('/^(<p>\s*)?<img[^>]*>(\s*<\/p>)?/iu', '', $content, 1);
        $content = preg_replace('/^\s*Liên Quân Mobile\s*$/ium', '', $content);
        
        // Xóa mọi nút tải xuống có sẵn trong content
        $content = preg_replace('/<div[^>]*class="download-btn-group[^"]*".*?<\/div>/is', '', $content);
        $content = preg_replace('/<a[^>]*class="btn-download[^"]*".*?<\/a>/is', '', $content);
        $content = preg_replace('/<a[^>]*>\s*(PC & PlayStation|PC Emulator|Tải Google Play|Tải App Store)\s*<\/a>/is', '', $content);
        
        echo $content;
        ?>
        </div>
        <?php 
        // Hiển thị đúng 3 nút: PC, Google Play, App Store (PC chỉ cho genshin/pubg)
        $cat = $overview['category'] ?? null;
        $pcLinks = [
            'genshin' => [
                'url' => 'https://genshin.hoyoverse.com/vi/',
                'label' => 'PC & PlayStation'
            ],
            'pubg' => [
                'url' => 'https://syzs.qq.com/',
                'label' => 'PC Emulator'
            ],
        ];
        $downloadLinks = [
            'lienquan' => [
                'google_play' => 'https://play.google.com/store/apps/details?id=com.garena.game.kgvn',
                'app_store' => 'https://apps.apple.com/vn/app/arena-of-valor/id1150318642',
            ],
            'pubg' => [
                'google_play' => 'https://play.google.com/store/apps/details?id=com.tencent.ig',
                'app_store' => 'https://apps.apple.com/vn/app/pubg-mobile/id1330123889',
            ],
            'genshin' => [
                'google_play' => 'https://play.google.com/store/apps/details?id=com.miHoYo.GenshinImpact',
                'app_store' => 'https://apps.apple.com/vn/app/genshin-impact/id1517783697',
            ],
        ];
        $links = $cat && isset($downloadLinks[$cat]) ? $downloadLinks[$cat] : null;
        ?>
        <div class="download-btn-group triangle">
            <?php if (isset($pcLinks[$cat])): ?>
                <a href="<?= htmlspecialchars($pcLinks[$cat]['url']) ?>" class="btn-download pc" target="_blank">
                    <?= $pcLinks[$cat]['label'] ?>
                </a>
            <?php endif; ?>
            <?php if ($links): ?>
                <a href="<?= htmlspecialchars($links['google_play']) ?>" class="btn-download gp" target="_blank">Tải Google Play</a>
                <a href="<?= htmlspecialchars($links['app_store']) ?>" class="btn-download ios" target="_blank">Tải App Store</a>
            <?php endif; ?>
        </div>
    </div>
    <?php
} else {
    echo "<p style='color:#fff'>Không tìm thấy game!</p>";
}
?>
</div>
</body>
</html>

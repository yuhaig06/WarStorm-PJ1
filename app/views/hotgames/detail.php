<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết game</title>
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/PJ1/FrontEnd/Home/favicon/web-app-manifest-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/PJ1/FrontEnd/Home/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/PJ1/FrontEnd/Home/favicon/site.webmanifest">
    <style>
        body {
            background: #121212;
            color: white;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 20px;
            padding-top: 60px;
        }
        h1 {
            font-size: 24px;
            text-align: center;
        }
        p {
            text-align: justify;
            line-height: 1.6;
        }
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #121212;
            padding: 10px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            margin-right: 10px;
            transition: color 0.3s ease;
        }
        nav a:hover {
            color: #FFD700;
        }
        .main-content {
            max-width: 1200px;
            margin: 80px auto 20px;
            padding: 20px;
            background: #1e1e1e;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(255, 255, 255, 0.1);
        }
        a {
            line-height: 1.6;
            text-decoration: none;
            color: #ff9800;
        }
        img {
            width: 100%;
            max-width: 900px;
            margin: 20px auto;
            display: block;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        .time-display {
            position: absolute;
            top: 70px;
            left: 20px;
            font-size: 14px;
            color: #B0B0B0;
            background: #1e1e1e;
            padding: 5px 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }
        h2 {
            color: #ff9800;
            font-size: 2rem;
            font-weight: 800;
            margin-top: 0;
            text-align: center;
        }
        h3, h4, h5 {
            color: #ff9800;
            margin: 20px 0;
        }
        .meta {
            color: #bdbdbd;
            font-size: 1rem;
            margin: 10px 0 20px;
            font-style: italic;
            text-align: center;
        }
        .back-link {
            display: block;
            margin-top: 30px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: 600;
            color: #ff9800;
            text-decoration: none;
            padding: 10px;
            border: 2px solid #ff9800;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            background: #ff9800;
            color: #121212;
        }
        .game-detail-original {
            margin-top: 20px;
        }
        .game-detail-original img {
            margin: 30px auto;
        }
        .game-detail-original p {
            margin: 20px 0;
            font-size: 16px;
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <nav class="back-navigation">
        <a href="/PJ1/public/hotgames" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="back-icon">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Quay Lại Tin Games</span>
        </a>
    </nav>
    <style>
        .back-navigation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(18, 18, 18, 0.95);
            padding: 10px 0;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            padding: 10px 20px;
            border-radius: 50px;
            background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%);
            box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            margin: 0 auto;
        }
        
        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 210, 255, 0.4);
            background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
        }
        
        .back-button:active {
            transform: translateY(1px);
            box-shadow: 0 2px 10px rgba(0, 210, 255, 0.3);
        }
        
        .back-icon {
            transition: transform 0.3s ease;
        }
        
        .back-button:hover .back-icon {
            transform: translateX(-3px);
        }
    </style>
    <div class="time-display" id="time" style="padding-top: 10px;"></div>
    <?php
    // Lấy dữ liệu động từ controller truyền vào (biến $id)
    $pdo = new PDO('mysql:host=localhost;dbname=warstorm_db;charset=utf8', 'root', '');
    $game = null;
    if (isset($id)) {
        $stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->execute([$id]);
        $game = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    ?>
    <?php if (isset($game) && $game): ?>
        <article class="main-content">
            <h2><?= htmlspecialchars($game['title'] ?? '') ?></h2>
            <p class="meta">
                Thể loại: <?= htmlspecialchars($game['category'] ?? 'N/A') ?>
            </p>
            <?php
            $imgName = htmlspecialchars($game['image'] ?? '');
            $imgPath = $_SERVER['DOCUMENT_ROOT'] . "/PJ1/FrontEnd/Hotgames/img/" . $imgName;
            $imgInContent = strpos((string)($game['content'] ?? ''), $imgName) !== false;
            if ($imgName && file_exists($imgPath) && !$imgInContent) {
                $imgUrl = "/PJ1/FrontEnd/Hotgames/img/$imgName";
                echo "<img src=\"$imgUrl\" alt=\"" . htmlspecialchars($game['title'] ?? '') . "\" class=\"detail-img\">";
            }
            ?>
            <div class="game-detail-original">
                <?= $game['content'] ?? '<p style="font-size:18px;">(Bạn có thể thêm nội dung chi tiết cho từng game vào trường content trong DB nếu muốn hiển thị động hơn nữa)</p>' ?>
            </div>
        </article>
    <?php else: ?>
        <div class="main-content" style="text-align: center; padding: 40px 20px;">
            <h2 style="color: #ff9800; margin-bottom: 20px;">Không tìm thấy game!</h2>
            <p style="color: #bdbdbd; font-size: 16px;">Game bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.</p>
            <a class="back-link" href="/PJ1/public/hotgames">Quay lại danh sách game</a>
        </div>
    <?php endif; ?>
    <script>
        function updateTime() {
            const timeElement = document.getElementById("time");
            const now = new Date();
            const formattedTime = now.toLocaleString("vi-VN", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
                hour12: true
            }).replace(',', '');
            timeElement.textContent = "⌚" + formattedTime;
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>
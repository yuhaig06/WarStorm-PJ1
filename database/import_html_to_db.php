<?php
/**
 * @var PDO $db
 * @var DOMDocument $dom
 * @var DOMXPath $xpath
 */
// Kết nối database với UTF-8
$db = new PDO('mysql:host=localhost;port=3306;dbname=warstorm_db;charset=utf8mb4', 'root', '', array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
));

// Hàm lấy nội dung từ file HTML
function getNewsContent($filePath) {
    if (!file_exists($filePath)) {
        return "Nội dung đang cập nhật...";
    }
    $html = file_get_contents($filePath);
    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new DOMXPath($dom);
    
    // Lấy nội dung chính từ thẻ section có class 'main-content'
    $content = '';
    $nodes = $xpath->query("//section[contains(@class, 'main-content')]");
    
    // Add CSS styles
    $css = '<style>
        body {
            font-family: "Segoe UI", "Roboto", Arial, sans-serif;
            background: #f5f7fa;
            color: #23272f;
            margin: 0;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 36px auto 20px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(24,28,50,0.08);
            padding: 36px 30px 24px 30px;
        }
        .news-detail h2 {
            margin-top: 0;
            color: #246bfd;
            font-size: 2rem;
            font-weight: 800;
        }
        .meta {
            color: #6c757d;
            font-size: 1rem;
            margin-bottom: 18px;
            font-style: italic;
        }
        .news-detail-original {
            margin-top: 16px;
        }
        .detail-img,
        .news-detail-original img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 15px 0;
            display: block;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .detail-img:hover,
        .news-detail-original img:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        }
        
        /* Container cho ảnh để dễ dàng canh giữa */
        .news-detail-original {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        /* Đảm bảo tất cả ảnh có cùng chiều rộng */
        .news-detail-original p {
            width: 100%;
            margin: 15px 0;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: 600;
            color: #246bfd;
            text-decoration: none;
        }
        .back-link:hover {
            color: #ff9800;
        }
        
        /* Nút Quay lại Tin Tức */
        .back-button-container {
            margin-top: 30px;
            text-align: center;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #246bfd 0%, #1a56db 100%);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(36, 107, 253, 0.3);
            border: none;
            cursor: pointer;
        }
        
        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(36, 107, 253, 0.4);
            background: linear-gradient(135deg, #1a56db 0%, #246bfd 100%);
            color: white;
        }
        
        .back-button svg {
            transition: transform 0.3s ease;
        }
        
        .back-button:hover svg {
            transform: translateX(-3px);
        }
    </style>';
    
    $content .= $css . '<div class="container"><div class="news-detail">';
    
    foreach ($nodes as $node) {
        // Xử lý đường dẫn ảnh trong nội dung
        $images = $xpath->query(".//img", $node);
        /** @var DOMElement $img */
        foreach ($images as $img) {
            if ($img instanceof DOMElement) {
                $src = $img->getAttribute('src');
                $newSrc = str_replace('../', '/PJ1/FrontEnd/News/', $src);
                $img->setAttribute('src', $newSrc);
            }
        }
        $content .= $dom->saveHTML($node);
    }
    
    // Thêm nút Quay lại Tin Tức
    $backButton = '
    <div class="back-button-container">
        <a href="/PJ1/BackEnd/public/news" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Quay lại Tin Tức
        </a>
    </div>';
    
    // Close the container divs
    $content .= $backButton . '</div></div>';
    
    return $content ?: "Nội dung đang cập nhật...";
}

// Tạo các bảng
try {
    // Tạo bảng games
    $db->exec("DROP TABLE IF EXISTS games");
    $db->exec("CREATE TABLE games (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        image VARCHAR(255) NOT NULL,
        category VARCHAR(50) NOT NULL,
        content TEXT,
        detail_url VARCHAR(255)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Tạo bảng mobilegames
    $db->exec("DROP TABLE IF EXISTS mobilegames");
    $db->exec("CREATE TABLE mobilegames (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        image VARCHAR(255) NOT NULL,
        category VARCHAR(50) NOT NULL,
        content TEXT,
        detail_url VARCHAR(255)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Tạo bảng esports
    $db->exec("DROP TABLE IF EXISTS esports");
    $db->exec("CREATE TABLE esports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        image VARCHAR(255) NOT NULL,
        content TEXT,
        detail_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

} catch (PDOException $e) {
    die("Lỗi tạo bảng: " . $e->getMessage());
}

// Kiểm tra nếu có tham số id trong URL thì hiển thị chi tiết tin tức
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare("SELECT * FROM mobilegames WHERE id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($news) {
        // Hiển thị trang chi tiết với CSS
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= htmlspecialchars($news['title']) ?></title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    margin: 0;
                    padding: 0;
                    background: #1a1a1a;
                    color: #fff;
                }

                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 20px;
                }

                .back-btn {
                    display: inline-block;
                    padding: 10px 20px;
                    margin-bottom: 20px;
                    color: #4CAF50;
                    text-decoration: none;
                    font-weight: bold;
                }

                .back-btn:hover {
                    color: #45a049;
                }

                .news-title {
                    font-size: 2em;
                    color: #4CAF50;
                    margin-bottom: 20px;
                }

                .news-subtitle {
                    font-size: 1.2em;
                    color: #888;
                    margin-bottom: 30px;
                }

                .news-info {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 30px;
                    padding: 20px;
                    background: #222;
                    border-radius: 8px;
                }

                .game-info, .share-buttons {
                    flex: 1;
                }

                .game-info h3, .share-buttons h3 {
                    color: #4CAF50;
                    margin-bottom: 15px;
                }

                .game-info p {
                    margin: 10px 0;
                    color: #ddd;
                }

                .share-buttons {
                    text-align: right;
    }

                .share-btn {
                    display: inline-block;
                    padding: 8px 15px;
                    margin: 5px;
                    border-radius: 5px;
                    color: white;
                    text-decoration: none;
                }

                .share-btn:hover {
                    opacity: 0.8;
                }

                .facebook { background: #3b5998; }
                .twitter { background: #1da1f2; }
                .linkedin { background: #0077b5; }

                .news-content {
                    line-height: 1.6;
                    color: #ddd;
                }

                .news-content img {
                    max-width: 100%;
                    height: auto;
                    margin: 20px auto;
                    display: block;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                    transition: transform 0.3s ease;
                }

                .news-content img:hover {
                    transform: scale(1.02);
                }

                @media (max-width: 768px) {
                    .news-info {
                        flex-direction: column;
                    }
                    
                    .share-buttons {
                        text-align: left;
                        margin-top: 20px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <a href="javascript:history.back()" class="back-btn">← Quay Lại</a>
                
                <h1 class="news-title"><?= htmlspecialchars($news['title']) ?></h1>
                
                <div class="news-info">
                    <div class="game-info">
                        <h3>🎮 Thông tin game</h3>
                        <p>🏷️ Thể loại: 
                            <?php
                            switch($news['category']) {
                                case 'lienquan':
                                    echo 'MOBA';
                                    break;
                                case 'genshin':
                                    echo 'RPG';
                                    break;
                                case 'pubg':
                                    echo 'Battle Royale';
                                    break;
                            }
                            ?>
                        </p>
                        <p>📅 Ngày đăng: <?= date('d/m/Y') ?></p>
                        <p>👁️ Lượt xem: <?= rand(1000, 15000) ?></p>
                    </div>

                    <div class="share-buttons">
                        <h3>📢 Chia sẻ</h3>
                        <a href="https://facebook.com/sharer/sharer.php?u=<?= urlencode($_SERVER['REQUEST_URI']) ?>" target="_blank" class="share-btn facebook">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode($_SERVER['REQUEST_URI']) ?>" target="_blank" class="share-btn twitter">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?url=<?= urlencode($_SERVER['REQUEST_URI']) ?>" target="_blank" class="share-btn linkedin">
                            <i class="fab fa-linkedin-in"></i> LinkedIn
                        </a>
                    </div>
                </div>

                <div class="news-content">
                    <?= $news['content'] ?>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    } else {
        header("Location: /PJ1/FrontEnd/Mobilegame/newsmobilegames/newsmobile.html");
        exit;
    }
}

// Import Hot Games
// Kiểm tra và tạo bảng games nếu chưa tồn tại
$db->query("TRUNCATE TABLE games");

// Hàm lấy nội dung từ file HTML chi tiết
function getHotGameContent($filePath) {
    if (!file_exists($filePath)) {
        return "<p>Nội dung đang được cập nhật...</p>";
    }
    
    $html = file_get_contents($filePath);
    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new DOMXPath($dom);
    
    // Lấy nội dung chính từ thẻ section có class 'main-content'
    $mainContent = $xpath->query("//section[contains(@class, 'main-content')]")->item(0);
    
    if (!$mainContent) {
        return "<p>Nội dung đang được cập nhật...</p>";
    }
    
    // Xóa script và các phần không cần thiết
    $scripts = $xpath->query(".//script", $mainContent);
    foreach ($scripts as $script) {
        $script->parentNode->removeChild($script);
    }
    
    // Cập nhật đường dẫn ảnh
    $images = $xpath->query(".//img", $mainContent);
    foreach ($images as $img) {
        if (isset($img->attributes['src'])) {
            $src = $img->attributes['src']->nodeValue;
            if (strpos($src, '../') === 0) {
                $src = '/PJ1/FrontEnd/Hotgames' . substr($src, 2);
                $img->attributes['src']->nodeValue = $src;
            }
        }
    }
    
    // Lấy HTML đã xử lý
    $content = $dom->saveHTML($mainContent);
    
    // Thêm CSS để đảm bảo hiển thị đẹp
    $css = '
    <style>
        .main-content { max-width: 900px; margin: 0 auto; padding: 20px; }
        .main-content img { max-width: 100%; height: auto; display: block; margin: 15px 0; border-radius: 8px; }
        .main-content h1 { color: #ff4c4c; margin-top: 0; }
        .main-content h4 { color: #ff8c42; margin: 20px 0 10px; }
        .main-content p { line-height: 1.6; margin: 10px 0; }
        .main-content a { color: #4da6ff; text-decoration: none; }
        .main-content a:hover { text-decoration: underline; }
    </style>
    ';
    
    return $css . $content;
}

// Dữ liệu 8 tin Hot Games lấy từ hotgame.html
$hotGames = [
    [
        'title' => '[Tam Quốc Chí - Chiến Lược] Ra mắt mùa giải mới "Trận Đồng Quan", SP Mã Siêu long trọng ra mắt',
        'image' => '/PJ1/FrontEnd/Hotgames/img/a2.png',
        'category' => 'strategy',
        'detail_url' => '/PJ1/public/hotgames/detail/1',
        'detail_file' => __DIR__ . '/../FrontEnd/Hotgames/Deltail/deltail1.html'
    ],
    [
        'title' => 'VNGGames công bố phát hành siêu phẩm Lineage2M tại Việt Nam',
        'image' => '/PJ1/FrontEnd/Hotgames/img/b1.webp',
        'category' => 'rpg',
        'detail_url' => '/PJ1/public/hotgames/detail/2',
        'detail_file' => __DIR__ . '/../FrontEnd/Hotgames/Deltail/deltail2.html'
    ],
    [
        'title' => 'Tây Du Truyền Kỳ Mobile - Game đấu tướng Tây Du Phong Thần kết hợp Tu Tiên sắp ra mắt',
        'image' => '/PJ1/FrontEnd/Hotgames/img/c1.webp',
        'category' => 'rpg',
        'detail_url' => '/PJ1/public/hotgames/detail/3',
        'detail_file' => __DIR__ . '/../FrontEnd/Hotgames/Deltail/deltail3.html'
    ],
    [
        'title' => 'Xuất hiện tựa game mới là đối thủ nặng ký của CS:GO và Valorant, lượng người chơi tăng vọt trên Steam',
        'image' => '/PJ1/FrontEnd/Hotgames/img/d1.webp',
        'category' => 'action',
        'detail_url' => '/PJ1/public/hotgames/detail/4',
        'detail_file' => __DIR__ . '/../FrontEnd/Hotgames/Deltail/deltail4.html'
    ],
    [
        'title' => 'Yulgang: Tái Chiến Võ Lâm chính thức ra mắt hôm nay 13/3',
        'image' => '/PJ1/FrontEnd/Hotgames/img/e1.webp',
        'category' => 'rpg',
        'detail_url' => '/PJ1/public/hotgames/detail/5',
        'detail_file' => __DIR__ . '/../FrontEnd/Hotgames/Deltail/detail5.html'
    ],
    [
        'title' => 'Siêu phẩm đua xe của NetEase công bố ngày ra mắt chính thức khiến game thủ hào hứng',
        'image' => '/PJ1/FrontEnd/Hotgames/img/f1.webp',
        'category' => 'sports',
        'detail_url' => '/PJ1/public/hotgames/detail/6',
        'detail_file' => __DIR__ . '/../FrontEnd/Hotgames/Deltail/detail6.html'
    ],
    [
        'title' => 'Mini World Royale - Sản phẩm bắn súng sinh tồn vui nhộn với đồ họa cực dễ thương',
        'image' => '/PJ1/FrontEnd/Hotgames/img/g1.webp',
        'category' => 'action',
        'detail_url' => '/PJ1/public/hotgames/detail/7',
        'detail_file' => __DIR__ . '/../FrontEnd/Hotgames/Deltail/detail7.html'
    ],
    [
        'title' => 'Trải Nghiệm ARK Survival Evolved Trên Điện Thoại Thành Công Rực Rỡ Với Hơn 3 Triệu Lượt Tải',
        'image' => '/PJ1/FrontEnd/Hotgames/img/h1.jpg',
        'category' => 'action',
        'detail_url' => '/PJ1/public/hotgames/detail/8',
        'detail_file' => __DIR__ . '/../FrontEnd/Hotgames/Deltail/detail8.html'
    ]
];

// Thêm nội dung từ file HTML vào mỗi game
foreach ($hotGames as &$game) {
    $game['content'] = getHotGameContent($game['detail_file']);
}
unset($game);

foreach ($hotGames as $game) {
    // Insert vào database
    $stmt = $db->prepare("INSERT INTO games (title, image, category, content, detail_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $game['title'],
        $game['image'],
        $game['category'],
        $game['content'],
        $game['detail_url']
    ]);

    echo "Đã import game: " . $game['title'] . "<br>";
}

echo "Hoàn tất import Hot Games!<br><br>";

// Import Mobile Games
echo "<h2>Importing Mobile Games...</h2>";

// Xóa dữ liệu cũ
$db->query("TRUNCATE TABLE mobilegames");

// Thêm trường content vào bảng nếu chưa có
try {
    $db->query("ALTER TABLE mobilegames ADD COLUMN content TEXT AFTER category");
} catch (PDOException $e) {
    // Bỏ qua nếu cột đã tồn tại
}

// Hàm tạo nội dung mặc định cho mobile games
function getMobileGameContent($game) {
    $categoryName = '';
    switch ($game['category']) {
        case 'lienquan':
            $categoryName = 'Liên Quân Mobile';
            break;
        case 'genshin':
            $categoryName = 'Genshin Impact';
            break;
        case 'pubg':
            $categoryName = 'PUBG Mobile';
            break;
    }
    
    return '
    <style>
        .main-content { max-width: 900px; margin: 0 auto; padding: 20px; }
        .main-content img { max-width: 100%; height: auto; display: block; margin: 15px 0; border-radius: 8px; }
        .main-content h1 { color: #ff4c4c; margin-top: 0; }
        .main-content h4 { color: #ff8c42; margin: 20px 0 10px; }
        .main-content p { line-height: 1.6; margin: 10px 0; }
        .main-content a { color: #4da6ff; text-decoration: none; }
        .main-content a:hover { text-decoration: underline; }
    </style>
    <section class="main-content">
        <h1>'.htmlspecialchars($game['title']).'</h1>
        <p>Đây là nội dung chi tiết về tin tức '.$categoryName.'. Nội dung đang được cập nhật...</p>
        <p>Xin vui lòng quay lại sau để xem thông tin đầy đủ.</p>
    </section>';
}

// Dữ liệu 8 tin Mobile Games
$mobileGames = [
    [
        'title' => 'Choáng váng với một trận đấu Liên Quân kéo dài tới hơn 70 phút',
        'image' => '/PJ1/FrontEnd/Mobilegame/newsmobilegames/img/!1.webp',
        'category' => 'lienquan',
        'detail_url' => '/PJ1/public/mobilegames/detail/1',
        'content' => ''
    ],
    [
        'title' => 'Genshin Impact bất ngờ "quay xe" với bộ đôi nhân vật mới, mang đến nhân vật 4 sao "mạnh nhất" lịch sử?',
        'image' => '/PJ1/FrontEnd/Mobilegame/newsmobilegames/img/@1.webp',
        'category' => 'genshin',
        'detail_url' => '/PJ1/public/mobilegames/detail/2',
        'content' => ''
    ],
    [
        'title' => 'PUBG MOBILE chiến thắng giải thưởng quốc tế Sensor Tower Apac Awards',
        'image' => '/PJ1/FrontEnd/Mobilegame/newsmobilegames/img/~1.webp',
        'category' => 'pubg',
        'detail_url' => '/PJ1/public/mobilegames/detail/3',
        'content' => ''
    ],
    [
        'title' => 'Team cày thuê tuyên bố kết thúc một trận đấu Liên Quân chỉ trong 4 phút, dưới 4 phút thì sao?',
        'image' => '/PJ1/FrontEnd/Mobilegame/newsmobilegames/img/$1.webp',
        'category' => 'lienquan',
        'detail_url' => '/PJ1/public/mobilegames/detail/4',
        'content' => ''
    ],
    [
        'title' => 'Sở hữu 300 triệu "Mora", game thủ Genshin Impact nâng cấp thành công toàn bộ Thiên phú của 100 nhân vật!',
        'image' => '/PJ1/FrontEnd/Mobilegame/newsmobilegames/img/-1.webp',
        'category' => 'genshin',
        'detail_url' => '/PJ1/public/mobilegames/detail/5',
        'content' => ''
    ],
    [
        'title' => 'Sướng như tuyển thủ PUBG Mobile, vừa đạt thành tích cao vừa được hậu phương ủng hộ hết mình',
        'image' => '/PJ1/FrontEnd/Mobilegame/newsmobilegames/img/^1.webp',
        'category' => 'pubg',
        'detail_url' => '/PJ1/public/mobilegames/detail/6',
        'content' => ''
    ],
    [
        'title' => 'Đây là lý do Liên Quân "có nằm mơ" mới có thể đuổi kịp Vương Giả Vinh Diệu',
        'image' => '/PJ1/FrontEnd/Mobilegame/newsmobilegames/img/&1.webp',
        'category' => 'lienquan',
        'detail_url' => '/PJ1/public/mobilegames/detail/7',
        'content' => ''
    ],
    [
        'title' => 'Tiếp tục hé lộ thêm danh tính của một nhân vật Genshin Impact mới, là một "siêu DPS", có tạo hình "công chúa" cực đáng yêu?',
        'image' => '/PJ1/FrontEnd/Mobilegame/newsmobilegames/img/`1.webp',
        'category' => 'genshin',
        'detail_url' => '/PJ1/public/mobilegames/detail/8',
        'content' => ''
    ]
];

// Tạo nội dung mặc định cho các bài viết
foreach ($mobileGames as &$game) {
    if (empty($game['content'])) {
        $game['content'] = getMobileGameContent($game);
    }
}
unset($game);

// Insert dữ liệu vào database
foreach ($mobileGames as $game) {
    $stmt = $db->prepare("INSERT INTO mobilegames (title, image, category, content, detail_url) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([
        $game['title'],
        $game['image'],
        $game['category'],
        $game['content'],
        $game['detail_url']
    ]);
    
    if ($result) {
        echo "Đã import tin tức: " . $game['title'] . "<br>\n";
    } else {
        echo "Lỗi khi import tin tức: " . $game['title'] . "<br>\n";
    }
}

echo "Hoàn tất import Mobile Games!<br>\n";

// Import riêng 3 file tĩnh: lienquan.html, genshin.html, pubg.html (không ảnh hưởng dữ liệu cũ)
$staticGames = [
    [
        'file' => __DIR__ . '/../FrontEnd/Mobilegame/mobilegames/lienquan.html',
        'category' => 'lienquan',
        'title' => 'Liên Quân Mobile',
        'image' => '/PJ1/FrontEnd/Mobilegame/img/lienquan.webp'
    ],
    [
        'file' => __DIR__ . '/../FrontEnd/Mobilegame/mobilegames/genshin.html',
        'category' => 'genshin',
        'title' => 'Genshin Impact',
        'image' => '/PJ1/FrontEnd/Mobilegame/img/genshin.webp'
    ],
    [
        'file' => __DIR__ . '/../FrontEnd/Mobilegame/mobilegames/pubg.html',
        'category' => 'pubg',
        'title' => 'PUBG Mobile',
        'image' => '/PJ1/FrontEnd/Mobilegame/img/pubg.webp'
    ],
];

foreach ($staticGames as $game) {
    if (file_exists($game['file'])) {
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML(mb_convert_encoding(file_get_contents($game['file']), 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new DOMXPath($doc);
        // Lấy nội dung chính (toàn bộ body) - chỉ lấy từ file tổng quan, không lấy từ detail nào cả
        $body = $xpath->query('//body')->item(0);
        $content = $body ? $doc->saveHTML($body) : '';
        // Chuẩn hóa ảnh nếu cần (ví dụ: src="../img/..." -> src="/PJ1/FrontEnd/Mobilegame/img/...")
        $content = str_replace('../img/', '/PJ1/FrontEnd/Mobilegame/img/', $content);
        // Insert bản ghi mới
        $stmt = $db->prepare("INSERT INTO mobilegames (title, image, category, content, detail_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $game['title'],
            $game['image'],
            $game['category'],
            $content,
            null
        ]);
        echo "<br>Đã import game tổng quan: {$game['title']}";
    }
}
echo "<br>Hoàn tất import các game tĩnh!";

// Import Esports
echo "<h2>Importing Esports...</h2>";

// Xóa dữ liệu cũ
$db->exec("TRUNCATE TABLE esports");

// Danh sách 8 tin esports
$esportsList = [
    [
        'title' => 'Đây có thể chính là cái tên góp phần khiến T1 bùng nổ drama như hiện tại',
        'slug' => 'day-co-the-chinh-la-cai-ten-gop-phan-khien-t1-bung-no-drama-nhu-hien-tai',
        'image' => 'h1.webp',
        'file' => __DIR__ . '/../FrontEnd/Esports/detail/detail1.html'
    ],
    [
        'title' => 'GAM Esports thất bại trước FURIA Esports, bị loại khỏi VCT 2025: Masters Shanghai',
        'slug' => 'gam-esports-that-bai-truoc-furia-esports-bi-loai-khoi-vct-2025-masters-shanghai',
        'image' => 'g4.webp',
        'file' => __DIR__ . '/../FrontEnd/Esports/detail/detail2.html'
    ],
    [
        'title' => 'CEO T1 tiếp tục lên tiếng về tương lai Gumayusi, có dấu hiệu đùn đẩy trách nhiệm',
        'slug' => 'ceo-t1-tiep-tuc-len-tieng-ve-tuong-lai-gumayusi-co-dau-hieu-dun-day-trach-nhiem',
        'image' => 'f4.webp',
        'file' => __DIR__ . '/../FrontEnd/Esports/detail/detail3.html'
    ],
    [
        'title' => 'Sao nhí LMHT bị tố bắt nạt bạn học, bạo lực học đường',
        'slug' => 'sao-nhi-lmht-bi-to-bat-nat-ban-hoc-bao-luc-hoc-duong',
        'image' => 'e1.webp',
        'file' => __DIR__ . '/../FrontEnd/Esports/detail/detail4.html'
    ],
    [
        'title' => 'T1 thua đau trước Gen.G, bị đánh bại với tỷ số 0-3',
        'slug' => 't1-thua-dau-truoc-gen-g-bi-danh-bai-voi-ty-so-0-3',
        'image' => 'd1.webp',
        'file' => __DIR__ . '/../FrontEnd/Esports/detail/detail5.html'
    ],
    [
        'title' => 'Faker lập kỷ lục mới tại LCK mùa Hè 2024',
        'slug' => 'faker-lap-ky-luc-moi-tai-lck-mua-he-2024',
        'image' => 'c1.webp',
        'file' => __DIR__ . '/../FrontEnd/Esports/detail/detail6.html'
    ],
    [
        'title' => 'Team Flash vô địch giải đấu Liên Quân Mobile quốc tế',
        'slug' => 'team-flash-vo-dich-giai-dau-lien-quan-mobile-quoc-te',
        'image' => 'b1.webp',
        'file' => __DIR__ . '/../FrontEnd/Esports/detail/detail7.html'
    ],
    [
        'title' => 'Việt Nam giành HCV SEA Games 33 bộ môn Liên Minh Huyền Thoại',
        'slug' => 'viet-nam-gianh-hcv-sea-games-33-bo-mon-lien-minh-huyen-thoai',
        'image' => 'a1.webp',
        'file' => __DIR__ . '/../FrontEnd/Esports/detail/detail8.html'
    ]
];

// Chuẩn bị câu lệnh SQL
$stmt = $db->prepare("INSERT INTO esports (title, slug, image, content, detail_url) VALUES (?, ?, ?, ?, ?)");

$imported = 0;
foreach ($esportsList as $item) {
    // Đọc nội dung từ file HTML
    $content = file_get_contents($item['file']);
    
    // Tạo URL chi tiết
    $detailUrl = "/PJ1/BackEnd/public/esports/detail/" . ($imported + 1);
    
    // Thực thi câu lệnh SQL
    $stmt->execute([
        $item['title'],
        $item['slug'],
        $item['image'],
        $content,
        $detailUrl
    ]);
    $imported++;
}

echo "Đã import thành công $imported/8 tin esports vào bảng esports<br>";

// Kiểm tra lại dữ liệu sau khi import
$stmt = $db->query("SELECT * FROM esports ORDER BY id");
$esportsItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($esportsItems) === 8) {
    echo "8 tin esports đã được thêm vào database đầy đủ và đúng thứ tự.";
} else {
    echo "Có lỗi xảy ra, hãy kiểm tra lại.";
}

echo "<h2>Importing News...</h2>";

// Xóa dữ liệu cũ
// Xóa bản ghi từ bảng con trước
$db->exec("SET FOREIGN_KEY_CHECKS = 0;");
$db->exec("TRUNCATE TABLE comments");
$db->exec("TRUNCATE TABLE news");
$db->exec("SET FOREIGN_KEY_CHECKS = 1;");

// Danh sách 17 tin tức
$newsList = [
    [
        'title' => 'Bom tấn sinh tồn giảm giá sốc trên Steam — Cơ hội vàng cho game thủ!',
        'slug' => 'bom-tan-sinh-ton-giam-gia-soc-tren-steam-co-hoi-vang-cho-game-thu',
        'image' => '1.webp',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details1.html'  
    ],
    [
        'title' => 'Hóa thân thành John Wick trong "I Am Your Beast" — Siêu phẩm hành động mới trên iOS!',
        'slug' => 'hoa-than-thanh-john-wick-trong-i-am-your-beast-sieu-pham-hanh-dong-moi-tren-ios',
        'image' => '2.png',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details2.html'
    ],
    [
        'title' => 'Doran lộ hành động trên stream, hé lộ lý do Zeus rời T1?',
        'slug' => 'doran-lo-hanh-dong-tren-stream-he-lo-ly-do-zeus-roi-t1',
        'image' => '3.webp',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details3.html'
    ],
    [
        'title' => 'ĐTCL mùa 13: 3 đội hình được buff mạnh, hứa hẹn thống trị meta!',
        'slug' => 'dtcl-mua-13-3-doi-hinh-duoc-buff-manh-hua-hen-thong-tri-meta',
        'image' => '4.webp',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details4.html'
    ],
    [
        'title' => 'Steam tặng miễn phí game được đánh giá "rất tích cực" cho người dùng!',
        'slug' => 'steam-tang-mien-phi-game-duoc-danh-gia-rat-tich-cuc-cho-nguoi-dung',
        'image' => '5.png',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details5.html'
    ],
    [
        'title' => 'Game kinh dị Việt Nam trên Steam gây tranh cãi vì đồ họa đẹp nhưng nhiều lỗi.',
        'slug' => 'game-kinh-di-viet-nam-tren-steam-gay-tranh-cai-vi-do-hoa-dep-nhung-nhieu-loi',
        'image' => '6.webp',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details6.html'
    ],
    [
        'title' => 'Streamer đỉnh cao phá đảo 7 game Soulslike với nhân vật level 1, không dính sát thương',
        'slug' => 'streamer-dinh-cao-pha-dao-7-game-soulslike-voi-nhan-vat-level-1-khong-dinh-sat-thuong',
        'image' => '7.1.webp',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details7.html'
    ],
    [
        'title' => 'Bom tấn từng 1,8 triệu nay giảm còn chưa tới 150k, game thủ tranh thủ sở hữu!',
        'slug' => 'bom-tan-tung-18-trieu-nay-giam-con-chua-toi-150k-game-thu-tranh-thu-so-huu',
        'image' => '8.1.webp',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details8.html'
    ],
    [
        'title' => 'Blasphemous, tuyệt phẩm Metroidvania, nay đã có trên iOS sau thời gian dài chờ đợi!',
        'slug' => 'blasphemous-tuyet-pham-metroidvania-nay-da-co-tren-ios-sau-thoi-gian-dai-cho-doi',
        'image' => '9.png',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details9.html'
    ],
    [
        'title' => 'Game "cướp dữ liệu" trên Steam hack hàng nghìn máy dù đã bị gỡ',
        'slug' => 'game-cuop-du-lieu-tren-steam-hack-hang-nghin-may-du-da-bi-go',
        'image' => '10.webp',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details10.html'
    ],
    [
        'title' => 'Game iOS mới ra mắt với giá cao ngất ngưởng, không phải cứ tiền là mua được!',
        'slug' => 'game-ios-moi-ra-mat-voi-gia-cao-ngat-nguong-khong-phai-cu-tien-la-mua-duoc',
        'image' => '11.webp',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details11.html'
    ],
    [
        'title' => 'Apple Arcade tung 2 game mới tháng 3, gợi nhớ tuổi thanh xuân triệu game thủ!',
        'slug' => 'apple-arcade-tung-2-game-moi-thang-3-goi-nho-tuoi-thanh-xuan-trieu-game-thu',
        'image' => '12.1.webp',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details12.html'
    ],
    [
        'title' => 'Tựa game "đỉnh của chóp" trên Steam bất ngờ giảm giá sốc, game thủ nhanh tay sở hữu',
        'slug' => 'tua-game-dinh-cua-chop-tren-steam-bat-ngo-giam-gia-soc-game-thu-nhanh-tay-so-huu',
        'image' => '13.webp',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details13.html'
    ],
    [
        'title' => 'Tựa game hành động nhập vai đình đám giảm giá cực sốc trên Steam',
        'slug' => 'tua-game-hanh-dong-nhap-vai-dinh-dam-giam-gia-cuc-soc-tren-steam',
        'image' => '14.webp',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details14.html'
    ],
    [
        'title' => 'ĐTCL mùa 13: Leo hạng thần tốc cùng đội hình Cực Tốc - Vệ Binh',
        'slug' => 'dtcl-mua-13-leo-hang-than-toc-cung-doi-hinh-cuc-toc-ve-binh',
        'image' => '15.png',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details15.html'
    ],
    [
        'title' => 'Game thủ "hô biến" VALORANT thành game đối kháng Tekken',
        'slug' => 'game-thu-ho-bien-valorant-thanh-game-doi-khang-tekken',
        'image' => '16.webp',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/details16.html'
    ],
    [
        'title' => 'Zeus và T1, chỉ LCK Cup 2025 là chưa đủ',
        'slug' => 'zeus-va-t1-chi-lck-cup-2025-la-chua-du',
        'image' => 'main.webp',
        'category_id' => 1,
        'file' => __DIR__ . '/../FrontEnd/News/details/detailsmain.html'
    ]
];

// Chuẩn bị câu lệnh SQL
$stmt = $db->prepare("INSERT INTO news (title, slug, content, image, author_id, category_id, created_at, published_at) 
                     VALUES (?, ?, ?, ?, 1, ?, NOW(), NOW())");

$imported = 0;
foreach ($newsList as $item) {
    // Đọc nội dung từ file HTML
    $content = getNewsContent($item['file']);
    
    // Thực thi câu lệnh SQL
    $stmt->execute([
        $item['title'],
        $item['slug'],
        $content,
        $item['image'],
        $item['category_id']
    ]);
    $imported++;
}

echo "Đã import thành công $imported/17 tin tức vào bảng news<br>";

// Kiểm tra lại dữ liệu sau khi import
$stmt = $db->query("SELECT * FROM news ORDER BY id");
$newsItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($newsItems) === 17) {
    echo "17 tin tức đã được thêm vào database đầy đủ và đúng thứ tự.";
} else {
    echo "Có lỗi xảy ra, hãy kiểm tra lại.";
}
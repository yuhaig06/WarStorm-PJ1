<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <a href="javascript:history.back()" class="back-btn">← Quay Lại</a>
    
    <h1 class="mobile-title">📱 Tin Tức Game Mobile</h1>

    <?php if (isset($news) && $news): ?>
        <div class="news-detail">
            <div class="news-meta">
                <time datetime="<?= date('Y-m-d H:i', strtotime($news['created_at'])) ?>">
                    ⏰ <?= date('H:i d/m/Y', strtotime($news['created_at'])) ?>
                </time>
            </div>

            <h2 class="news-title"><?= htmlspecialchars($news['title']) ?></h2>
            
            <div class="news-subtitle">
                <?php if ($news['category'] === 'lienquan'): ?>
                    <p>Sẽ thế nào nếu các vấn đấu của Liên Quân Mobile có thời gian "khủng" đến vậy?</p>
                <?php elseif ($news['category'] === 'genshin'): ?>
                    <p>Cùng tìm hiểu những thông tin mới nhất về Genshin Impact!</p>
                <?php elseif ($news['category'] === 'pubg'): ?>
                    <p>Những tin tức mới nhất từ PUBG Mobile!</p>
                <?php endif; ?>
            </div>

            <div class="news-info">
                <div class="game-info">
                    <h3>🎮 Thông tin game</h3>
                    <p>
                        <span>🏷️ Thể loại:</span>
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
                    <p>📅 Ngày đăng: <?= date('d/m/Y', strtotime($news['created_at'])) ?></p>
                    <p>👁️ Lượt xem: <?= rand(1000, 15000) ?></p>
                </div>

                <div class="share-buttons">
                    <h3>📢 Chia sẻ</h3>
                    <a href="https://www.facebook.com/share_channel/" target="_blank" class="share-btn facebook">
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
    <?php else: ?>
        <div class="error-message">
            <h2>Không tìm thấy tin tức</h2>
            <p>Tin tức này không tồn tại hoặc đã bị xóa.</p>
            <a href="/PJ1/public/mobilegames" class="btn-back">Quay lại trang tin tức</a>
        </div>
    <?php endif; ?>
</div>

<style>
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
    transition: color 0.3s;
}

.back-btn:hover {
    color: #45a049;
}

.mobile-title {
    text-align: center;
    color: #4CAF50;
    margin-bottom: 30px;
}

.news-detail {
    background: #1a1a1a;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.news-meta {
    margin-bottom: 20px;
    color: #888;
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
    transition: opacity 0.3s;
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
    margin: 20px 0;
    border-radius: 8px;
}

.error-message {
    text-align: center;
    padding: 50px 20px;
}

.error-message h2 {
    color: #ff4444;
    margin-bottom: 20px;
}

.btn-back {
    display: inline-block;
    padding: 10px 20px;
    background: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin-top: 20px;
    transition: background 0.3s;
}

.btn-back:hover {
    background: #45a049;
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

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 
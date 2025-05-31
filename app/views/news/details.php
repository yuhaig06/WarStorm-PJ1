<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết tin tức</title>
    <link rel="icon" type="image/png" sizes="96x96" href="/PJ1/FrontEnd/Home/favicon/favicon-96x96.png">
    <style>
        body {
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
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
    </style>
</head>
<body>
    <main class="container">
        <?php if (isset($post)): ?>
            <article class="news-detail">
                <?php
                function to_array($obj) {
                    if (is_array($obj)) return $obj;
                    if (is_object($obj)) return get_object_vars($obj);
                    return [];
                }
                $postArr = to_array($post);
                ?>
                <h2><?php echo htmlspecialchars($postArr['title'] ?? ''); ?></h2>
                <p class="meta">
                    Đăng ngày: <?php echo isset($postArr['published_at']) ? htmlspecialchars($postArr['published_at']) : 'N/A'; ?> |
                    Tác giả: <?php echo isset($postArr['author_name']) ? htmlspecialchars($postArr['author_name']) : 'N/A'; ?>
                </p>
                <?php
                $imgName = htmlspecialchars($postArr['image'] ?? $postArr['thumbnail'] ?? '');
                $imgPath = $_SERVER['DOCUMENT_ROOT'] . "/PJ1/FrontEnd/News/img/" . $imgName;
                $imgInContent = strpos((string)($postArr['content'] ?? ''), $imgName) !== false;
                if ($imgName && file_exists($imgPath) && !$imgInContent) {
                    $imgUrl = "/PJ1/FrontEnd/News/img/$imgName";
                    echo "<img src=\"$imgUrl\" alt=\"" . htmlspecialchars($postArr['title'] ?? '') . "\" class=\"detail-img\">";
                }
                ?>
                <div class="news-detail-original">
                    <?php
                    $content = isset($postArr['content']) ? $postArr['content'] : '';
                    // Loại bỏ thẻ nav có chứa liên kết Quay Lại Tin Tức
                    $content = preg_replace('#<nav[^>]*>\s*<a[^>]*>Quay Lại Tin Tức<\/a>\s*<\/nav>#i', '', $content);
                    // Xử lý đường dẫn ảnh
                    $content = preg_replace(
                        '#src=[\'\"]\.\./img/([^\'\"]+)[\'\"]#i',
                        'src="/PJ1/FrontEnd/News/img/$1"',
                        $content
                    );
                    echo $content;
                    ?>
                    <div style="margin-top: 40px; text-align: center;">
                        
                        <style>
                            .back-button:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 6px 20px rgba(36, 107, 253, 0.4);
                                background: linear-gradient(135deg, #1a5bd9 0%, #246bfd 100%);
                            }
                            .back-button:active {
                                transform: translateY(1px);
                                box-shadow: 0 2px 10px rgba(36, 107, 253, 0.3);
                            }
                            .back-button:hover svg {
                                transform: translateX(-3px);
                            }
                        </style>
                    </div>
                </div>
            </article>
        <?php else: ?>
            <div class="alert alert-danger">Không tìm thấy bài viết.</div>
        <?php endif; ?>
    </main>
</body>
</html>

<?php
// 设置页面结构化数据
$structuredData = [
    "@context" => "https://schema.org",
    "@type" => "Article",
    "headline" => $article['title'],
    "description" => $pageDescription,
    "datePublished" => $article['published_at'],
    "dateModified" => $article['updated_at'] ?? $article['published_at'],
    "author" => [
        "@type" => "Person",
        "name" => $article['author_id'] ? '用户' . $article['author_id'] : '系统管理员'
    ],
    "publisher" => [
        "@type" => "Organization",
        "name" => SITE_NAME,
        "url" => SITE_URL
    ]
];

if (!empty($article['cover_image'])) {
    $structuredData["image"] = $article['cover_image'];
}
?>

<!-- 结构化数据 -->
<script type="application/ld+json">
<?php echo json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- 面包屑导航 -->
<nav aria-label="面包屑导航">
    <ol class="breadcrumb">
        <li><a href="/" class="nes-text is-primary">首页</a></li>
        <li><a href="/category/<?php echo $category['category_slug']; ?>/" class="nes-text is-primary"><?php echo $category['category_name']; ?></a></li>
        <li class="nes-text is-disabled" aria-current="page">文章详情</li>
    </ol>
</nav>

<div class="row">
    <!-- 文章内容 -->
    <div class="col-8 col-md-12">
        <?php if (!empty($isPreview)): ?>
        <div class="nes-container is-dark" style="background:#fff3cd;border:2px solid #ffc107;margin-bottom:16px;">
            <p class="title" style="color:#856404;margin-bottom:4px;">
                <i class="fas fa-eye me-2"></i>预览模式
            </p>
            <p style="color:#856404;margin:0;">
                此文章尚未发布，当前状态：<strong><?php echo $article['status']; ?></strong>。
                此页面仅供管理员预览，访客无法看到。
            </p>
        </div>
        <?php endif; ?>
        <article class="nes-container with-title">
            <h1 class="title article-detail-title"><?php echo $article['title']; ?></h1>

            <div class="article-info article-detail-meta">
                <span>
                    <i data-icon="calendar" aria-hidden="true"></i> <?php echo format_time($article['published_at'], 'Y年m月d日 H:i'); ?>
                </span>
                <span>
                    <i data-icon="user" aria-hidden="true"></i> <?php echo $article['author_id'] ? '用户' . $article['author_id'] : '系统管理员'; ?>
                </span>
                <span>
                    <i data-icon="folder" aria-hidden="true"></i> <a href="/category/<?php echo $category['category_slug']; ?>/" class="nes-text is-primary"><?php echo $category['category_name']; ?></a>
                </span>
                <span>
                    <i data-icon="eye" aria-hidden="true"></i> <?php echo $article['read_count'] + 1; ?> 阅读
                </span>
                <span>
                    <i data-icon="heart" aria-hidden="true"></i> <span id="like-count"><?php echo $article['like_count']; ?></span> 点赞
                </span>
            </div>

            <!-- 封面图 -->
            <?php if (!empty($article['cover_image'])): ?>
            <div class="article-cover-wrapper">
                <img src="<?php echo $article['cover_image']; ?>" alt="<?php echo $article['title']; ?>"
                     class="pixelated article-cover-image">
            </div>
            <?php endif; ?>

            <!-- 文章内容 -->
            <div class="article-content article-detail-content">
                <?php echo $article['content']; ?>
            </div>

            <!-- 文章操作 -->
            <div class="article-actions">
                <button class="nes-btn is-error" onclick="likeArticle(<?php echo $article['id']; ?>)" aria-label="点赞文章">
                    <i data-icon="heart" aria-hidden="true"></i> 点赞 (<span id="like-btn-count"><?php echo $article['like_count']; ?></span>)
                </button>

                <div class="article-share-actions">
                    <button class="nes-btn is-primary" onclick="shareToWeibo()" aria-label="分享到微博">
                        <i data-icon="twitter" aria-hidden="true"></i> 微博
                    </button>
                    <button class="nes-btn is-success" onclick="shareToQQ()" aria-label="分享到QQ">
                        <i data-icon="chat" aria-hidden="true"></i> QQ
                    </button>
                    <button class="nes-btn is-warning" onclick="copyArticleLink()" aria-label="复制文章链接">
                        <i data-icon="link" aria-hidden="true"></i> 复制链接
                    </button>
                </div>
            </div>
        </article>

        <!-- 相关文章 -->
        <?php if (!empty($relatedArticles)): ?>
        <div class="nes-container with-title related-articles-container">
            <h3 class="title">
                <i data-icon="link" aria-hidden="true"></i> 相关文章
            </h3>
            <ul class="article-list related-article-list">
                <?php foreach ($relatedArticles as $related): ?>
                <li class="article-list-item">
                    <a href="/article/<?php echo $related['slug']; ?>" class="nes-text related-article-link">
                        <?php echo truncate_string($related['title'], 50); ?>
                    </a>
                    <span class="nes-text is-disabled related-article-date">
                        <?php echo format_time($related['published_at'], 'm-d'); ?>
                    </span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <!-- 侧边栏 -->
    <aside class="col-4 col-md-12">
        <!-- 栏目信息 -->
        <div class="sidebar-section">
            <div class="sidebar-title">
                <i data-icon="folder" aria-hidden="true"></i> <?php echo $category['category_name']; ?>
            </div>
            <div class="sidebar-content">
                <p class="category-description"><?php echo $category['category_description']; ?></p>
                <a href="/category/<?php echo $category['category_slug']; ?>/" class="nes-btn is-primary w-full">
                    查看更多
                </a>
            </div>
        </div>

        <!-- 热门文章 -->
        <div class="sidebar-section">
            <div class="sidebar-title">
                <i data-icon="trophy" aria-hidden="true"></i> 热门文章
            </div>
            <div class="sidebar-content">
                <?php
                $rawHotArticles = (new ArticleModel())->getHotArticles(8);
                $hotArticles = array_map(function($raw) {
                    return [
                        'id' => $raw['id'],
                        'title' => $raw['article_title'],
                        'slug' => $raw['article_slug'],
                        'read_count' => $raw['article_read_count'] ?? 0
                    ];
                }, $rawHotArticles);

                if (!empty($hotArticles)):
                ?>
                <ul class="article-list sidebar-article-list">
                    <?php foreach ($hotArticles as $hot): ?>
                    <li class="article-list-item sidebar-article-item">
                        <a href="/article/<?php echo $hot['slug']; ?>" class="nes-text hot-article-link">
                            <?php echo truncate_string($hot['title'], 30); ?>
                        </a>
                        <span class="nes-badge hot-article-count">
                            <span class="is-primary"><?php echo $hot['read_count']; ?></span>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="empty-text">暂无热门文章</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 生态入口 -->
        <div class="sidebar-section">
            <div class="sidebar-title">
                <i data-icon="link" aria-hidden="true"></i> 生态入口
            </div>
            <div class="sidebar-content">
                <div class="link-list">
                    <a href="https://bgjq.top" class="nes-btn is-primary" target="_blank" rel="noopener noreferrer">主站</a>
                    <a href="https://8w.bgjq.top" class="nes-btn is-success" target="_blank" rel="noopener noreferrer">8w社区</a>
                    <a href="https://wiki.bgjq.top" class="nes-btn is-warning" target="_blank" rel="noopener noreferrer">Wiki百科</a>
                    <a href="https://countries.bgjq.top" class="nes-btn is-error" target="_blank" rel="noopener noreferrer">邦国主页</a>
                </div>
            </div>
        </div>
    </aside>
</div>

<script>
// 点赞文章
function likeArticle(articleId) {
    if (!confirm('您确定要点赞这篇文章吗？')) {
        return;
    }

    fetch('/api/article/like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            article_id: articleId,
            csrf_token: '<?php echo generate_csrf_token(); ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('like-count').textContent = data.like_count;
            document.getElementById('like-btn-count').textContent = data.like_count;
            alert('点赞成功！');
        } else {
            alert(data.message || '点赞失败');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('网络错误，请重试');
    });
}

// 分享到微博
function shareToWeibo() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('<?php echo $article['title']; ?>');
    window.open(`http://service.weibo.com/share/share.php?url=${url}&title=${title}`, '_blank');
}

// 分享到QQ
function shareToQQ() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('<?php echo $article['title']; ?>');
    window.open(`http://connect.qq.com/widget/shareqq/index.html?url=${url}&title=${title}`, '_blank');
}

// 复制文章链接
function copyArticleLink() {
    const url = window.location.href;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(() => {
            alert('文章链接已复制到剪贴板');
        }).catch(err => {
            fallbackCopy(url);
        });
    } else {
        fallbackCopy(url);
    }
}

// 备用复制方法
function fallbackCopy(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        document.execCommand('copy');
        alert('文章链接已复制到剪贴板');
    } catch (err) {
        alert('复制失败，请手动复制');
    }

    document.body.removeChild(textArea);
}

// 页面加载完成后执行
document.addEventListener('DOMContentLoaded', function() {
    // 图片懒加载
    const images = document.querySelectorAll('.article-content img');
    images.forEach(img => {
        img.addEventListener('load', function() {
            this.classList.add('loaded');
        });

        img.addEventListener('error', function() {
            this.src = '/assets/images/placeholder.jpg';
            this.alt = '图片加载失败';
        });
    });
});
</script>

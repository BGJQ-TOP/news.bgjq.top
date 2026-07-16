<?php
// 设置页面 SEO 信息
$pageTitle = '邦国新闻 - 最新资讯、热门推荐、精选文章阅读平台';
$pageDescription = '邦国新闻是一个专业的中文新闻资讯平台，致力于为用户提供最新、最全面的新闻内容。我们每日更新时事新闻、热点资讯、深度报道，涵盖国内新闻、国际新闻、财经资讯、科技动态、娱乐八卦、体育赛事等多个领域。';
$pageKeywords = '新闻，资讯，阅读，简洁，平台，热门，推荐，时事新闻，热点资讯，深度报道';
?>

<!-- 主内容区 -->
<div class="container">
    <div class="row">
        <!-- 左侧主内容 -->
        <div class="col-8 col-lg-12">
            <!-- 热门文章区块 -->
            <?php if (!empty($topArticles)): ?>
            <div class="pixel-container">
                <h3 class="pixel-title">
                    <i data-icon="fire" aria-hidden="true"></i> 热门推荐
                </h3>
                <div class="article-grid">
                    <?php foreach (array_slice($topArticles, 0, 6) as $index => $article): ?>
                    <div class="article-card">
                        <?php if (!empty($article['cover_image'])): ?>
                        <img src="<?php echo $article['cover_image']; ?>"
                             alt="<?php echo $article['title']; ?>"
                             class="article-image">
                        <?php else: ?>
                        <div class="article-image-placeholder">
                            <i data-icon="newspaper" aria-hidden="true"></i>
                        </div>
                        <?php endif; ?>
                        <div class="article-content">
                            <h4 class="article-title">
                                <a href="/article/<?php echo $article['slug']; ?>">
                                    <?php echo truncate_string($article['title'], 40); ?>
                                </a>
                            </h4>
                            <p class="article-excerpt">
                                <?php echo truncate_string(strip_tags($article['content']), 100); ?>
                            </p>
                            <div class="article-meta">
                                <span class="article-meta-item">
                                    <i data-icon="eye" aria-hidden="true"></i> <?php echo $article['read_count'] ?? 0; ?>
                                </span>
                                <span class="article-meta-item">
                                    <i data-icon="heart" aria-hidden="true"></i> <?php echo $article['like_count'] ?? 0; ?>
                                </span>
                                <span class="article-meta-item">
                                    <i data-icon="calendar" aria-hidden="true"></i> <?php echo format_time($article['published_at'] ?? 'now', 'm-d'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- 最新资讯区块 -->
            <div class="pixel-container">
                <div class="section-header">
                    <h3 class="pixel-title">
                        <i data-icon="clock" aria-hidden="true"></i> 最新资讯
                    </h3>
                    <div class="view-switcher" role="group" aria-label="文章视图切换">
                        <button class="view-btn active" onclick="switchView('grid')" id="gridViewBtn" aria-pressed="true">
                            网格视图
                        </button>
                        <button class="view-btn" onclick="switchView('list')" id="listViewBtn" aria-pressed="false">
                            列表视图
                        </button>
                    </div>
                </div>

                <?php if (!empty($latestArticles['data'])): ?>
                <div id="articlesContainer" class="article-grid">
                    <?php foreach ($latestArticles['data'] as $index => $article): ?>
                    <div class="article-card">
                        <?php if (!empty($article['cover_image'])): ?>
                        <img src="<?php echo $article['cover_image']; ?>"
                             alt="<?php echo $article['title']; ?>"
                             class="article-image">
                        <?php else: ?>
                        <div class="article-image-placeholder">
                            <i data-icon="newspaper" aria-hidden="true"></i>
                        </div>
                        <?php endif; ?>
                        <div class="article-content">
                            <h4 class="article-title">
                                <a href="/article/<?php echo $article['slug']; ?>">
                                    <?php echo $article['title']; ?>
                                </a>
                            </h4>
                            <p class="article-excerpt">
                                <?php echo truncate_string(strip_tags($article['content']), 120); ?>
                            </p>
                            <div class="article-meta">
                                <span class="article-meta-item">
                                    <i data-icon="calendar" aria-hidden="true"></i> <?php echo format_time($article['published_at'], 'm-d H:i'); ?>
                                </span>
                                <span class="article-meta-item">
                                    <i data-icon="eye" aria-hidden="true"></i> <?php echo $article['read_count']; ?>
                                </span>
                                <span class="article-meta-item">
                                    <i data-icon="heart" aria-hidden="true"></i> <?php echo $article['like_count']; ?>
                                </span>
                                <?php if (!empty($article['author_name'])): ?>
                                <span class="article-meta-item">
                                    <i data-icon="user" aria-hidden="true"></i> <?php echo htmlspecialchars($article['author_name']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- 分页 -->
                <?php if ($latestArticles['totalPages'] > 1): ?>
                <nav class="pagination" aria-label="文章分页">
                    <?php if ($latestArticles['page'] > 1): ?>
                    <a href="/?page=<?php echo $latestArticles['page'] - 1; ?>" class="pagination-btn" aria-label="上一页">
                        <i data-icon="caret-left" aria-hidden="true"></i> 上一页
                    </a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $latestArticles['page'] - 2); $i <= min($latestArticles['totalPages'], $latestArticles['page'] + 2); $i++): ?>
                    <a href="/?page=<?php echo $i; ?>" class="pagination-btn <?php echo $i == $latestArticles['page'] ? 'active' : ''; ?>" <?php echo $i == $latestArticles['page'] ? 'aria-current="page"' : ''; ?>>
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>

                    <?php if ($latestArticles['page'] < $latestArticles['totalPages']): ?>
                    <a href="/?page=<?php echo $latestArticles['page'] + 1; ?>" class="pagination-btn" aria-label="下一页">
                        下一页 <i data-icon="caret-right" aria-hidden="true"></i>
                    </a>
                    <?php endif; ?>
                </nav>
                <?php endif; ?>

                <?php else: ?>
                <div class="empty-state">
                    <i data-icon="close" aria-hidden="true"></i>
                    <p class="empty-state-title">暂无最新资讯</p>
                    <p class="empty-state-subtitle">稍后再来看看吧~</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 右侧侧边栏 -->
        <aside class="col-4 col-lg-12">
            <!-- 热门排行区块 -->
            <div class="sidebar-section">
                <div class="sidebar-title">
                    <i data-icon="trophy" aria-hidden="true"></i> 热门排行
                </div>
                <div class="sidebar-content">
                    <?php if (!empty($hotArticles)): ?>
                    <ol class="rank-list">
                        <?php foreach (array_slice($hotArticles, 0, 10) as $index => $article): ?>
                        <li class="rank-item">
                            <span class="rank-badge <?php echo $index < 3 ? 'rank-' . ($index + 1) : ''; ?>">
                                <?php echo $index + 1; ?>
                            </span>
                            <a href="/article/<?php echo isset($article['slug']) ? $article['slug'] : ''; ?>" class="rank-link">
                                <?php echo isset($article['title']) ? truncate_string($article['title'], 30) : ''; ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                    <?php else: ?>
                    <p class="empty-text">暂无热门文章</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 生态入口区块 -->
            <div class="sidebar-section">
                <div class="sidebar-title">
                    <i data-icon="link" aria-hidden="true"></i> 生态入口
                </div>
                <div class="sidebar-content">
                    <div class="link-list">
                        <a href="https://bgjq.top" class="nes-btn is-primary" target="_blank" rel="noopener noreferrer">
                            <i data-icon="star" aria-hidden="true"></i> 主站
                        </a>
                        <a href="https://8w.bgjq.top" class="nes-btn is-success" target="_blank" rel="noopener noreferrer">
                            <i data-icon="chat" aria-hidden="true"></i> 8w社区
                        </a>
                        <a href="https://wiki.bgjq.top" class="nes-btn is-warning" target="_blank" rel="noopener noreferrer">
                            <i data-icon="book" aria-hidden="true"></i> Wiki百科
                        </a>
                        <a href="https://countries.bgjq.top" class="nes-btn is-error" target="_blank" rel="noopener noreferrer">
                            <i data-icon="crown" aria-hidden="true"></i> 邦国主页
                        </a>
                    </div>
                </div>
            </div>

            <!-- 热门标签区块 -->
            <div class="sidebar-section">
                <div class="sidebar-title">
                    <i data-icon="tag" aria-hidden="true"></i> 热门标签
                </div>
                <div class="sidebar-content">
                    <div class="tag-list">
                        <a href="/search?q=MC" class="tag">MC</a>
                        <a href="/search?q=国战" class="tag">国战</a>
                        <a href="/search?q=服务器" class="tag">服务器</a>
                        <a href="/search?q=邦国" class="tag">邦国</a>
                        <a href="/search?q=更新" class="tag">更新</a>
                        <a href="/search?q=活动" class="tag">活动</a>
                        <a href="/search?q=攻略" class="tag">攻略</a>
                        <a href="/search?q=新闻" class="tag">新闻</a>
                    </div>
                </div>
            </div>

            <!-- 关于我们区块 -->
            <div class="sidebar-section">
                <div class="sidebar-title">
                    <i data-icon="heart" aria-hidden="true"></i> 关于我们
                </div>
                <div class="sidebar-content">
                    <p class="about-text">
                        邦国新闻是邦国崛起MC国战服务器的官方新闻平台，为玩家提供最新的游戏资讯、攻略和活动信息。
                    </p>
                    <div class="about-info">
                        <p><strong>联系邮箱：</strong>admin@bgjq.top</p>
                        <p><strong>QQ群：</strong>123456789</p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
// 视图切换功能
function switchView(viewType) {
    const container = document.getElementById('articlesContainer');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');

    // 移除所有视图类
    container.classList.remove('article-grid', 'article-list');

    // 更新按钮状态
    gridBtn.classList.remove('active');
    listBtn.classList.remove('active');
    gridBtn.setAttribute('aria-pressed', 'false');
    listBtn.setAttribute('aria-pressed', 'false');

    if (viewType === 'grid') {
        container.classList.add('article-grid');
        gridBtn.classList.add('active');
        gridBtn.setAttribute('aria-pressed', 'true');
        localStorage.setItem('articleView', 'grid');
    } else {
        container.classList.add('article-list');
        listBtn.classList.add('active');
        listBtn.setAttribute('aria-pressed', 'true');
        localStorage.setItem('articleView', 'list');
    }
}

// 初始化视图设置
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('articleView') || 'grid';
    switchView(savedView);
});
</script>

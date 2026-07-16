<!-- 排行榜单页面 -->
<!-- 面包屑导航 -->
<nav aria-label="面包屑导航">
    <ul class="breadcrumb">
        <li><a href="/" class="nes-text is-primary">首页</a></li>
        <li class="nes-text is-disabled" aria-current="page">排行榜</li>
    </ul>
</nav>

<!-- 主内容区 -->
<div class="row">
    <!-- 左侧主要内容 -->
    <div class="col-8 col-lg-12">
        <div class="nes-container with-title rankings-container">
            <h1 class="title">
                <i data-icon="trophy" data-size="sm" aria-hidden="true"></i> 排行榜单
            </h1>

            <!-- 标签切换 -->
            <div class="tab-bar" role="tablist" aria-label="文章排行切换">
                <button class="tab-btn is-active" id="hot-tab-btn" onclick="switchTab('hot')" role="tab" aria-selected="true" aria-controls="hot-content">
                    <i data-icon="fire" data-size="sm" aria-hidden="true"></i> 热门文章
                </button>
                <button class="tab-btn" id="latest-tab-btn" onclick="switchTab('latest')" role="tab" aria-selected="false" aria-controls="latest-content">
                    <i data-icon="clock" data-size="sm" aria-hidden="true"></i> 最新文章
                </button>
            </div>

            <!-- 热门文章 -->
            <div id="hot-content" class="tab-content" role="tabpanel" aria-labelledby="hot-tab-btn">
                <?php if (!empty($hotArticles)): ?>
                <ul class="article-list">
                    <?php foreach ($hotArticles as $index => $article): ?>
                    <li class="article-list-item">
                        <div class="d-flex gap-1 items-start">
                            <span class="rank-badge rank-badge-lg <?php echo $index < 3 ? 'rank-' . ($index + 1) : ''; ?>" aria-label="排名第 <?php echo $index + 1; ?>">
                                <?php echo $index + 1; ?>
                            </span>
                            <div class="article-list-content">
                                <h4 class="article-title">
                                    <a href="/article/<?php echo htmlspecialchars($article['article_slug'] ?? $article['slug'] ?? ''); ?>">
                                        <?php echo htmlspecialchars($article['article_title'] ?? $article['title'] ?? ''); ?>
                                    </a>
                                </h4>
                                <p class="article-excerpt">
                                    <?php
                                    $content = strip_tags($article['article_content'] ?? $article['content'] ?? '');
                                    echo mb_substr($content, 0, 100, 'UTF-8') . '...';
                                    ?>
                                </p>
                                <div class="article-meta">
                                    <span><i data-icon="eye" data-size="sm" aria-hidden="true"></i> <?php echo $article['article_read_count'] ?? $article['read_count'] ?? 0; ?></span>
                                    <span><i data-icon="heart" data-size="sm" aria-hidden="true"></i> <?php echo $article['article_like_count'] ?? $article['like_count'] ?? 0; ?></span>
                                    <a href="/article/<?php echo htmlspecialchars($article['article_slug'] ?? $article['slug'] ?? ''); ?>" class="nes-btn is-primary">
                                        阅读
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="nes-container is-rounded empty-state-box">
                    <i data-icon="close" data-size="lg" aria-hidden="true"></i>
                    <p class="nes-text empty-state-title">暂无热门文章数据</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- 最新文章 -->
            <div id="latest-content" class="tab-content hidden" role="tabpanel" aria-labelledby="latest-tab-btn">
                <?php if (!empty($latestArticles)): ?>
                <ul class="article-list">
                    <?php foreach ($latestArticles as $article): ?>
                    <li class="article-list-item">
                        <div class="row">
                            <?php if (!empty($article['cover_image'])): ?>
                            <div class="col-3 col-sm-12 article-cover-wrap">
                                <img src="<?php echo htmlspecialchars($article['cover_image']); ?>"
                                     alt="<?php echo htmlspecialchars($article['title'] ?? '文章封面图'); ?>"
                                     class="pixelated article-cover-img"
                                     loading="lazy">
                            </div>
                            <div class="col-9 col-sm-12">
                            <?php else: ?>
                            <div class="col-12">
                            <?php endif; ?>
                                <h4 class="article-title">
                                    <a href="/article/<?php echo htmlspecialchars($article['slug']); ?>">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                    </a>
                                </h4>
                                <p class="article-excerpt">
                                    <?php
                                    $content = strip_tags($article['content']);
                                    echo mb_substr($content, 0, 120, 'UTF-8') . '...';
                                    ?>
                                </p>
                                <div class="article-meta">
                                    <span><i data-icon="eye" data-size="sm" aria-hidden="true"></i> <?php echo $article['read_count']; ?></span>
                                    <span><i data-icon="heart" data-size="sm" aria-hidden="true"></i> <?php echo $article['like_count']; ?></span>
                                    <span><i data-icon="calendar" data-size="sm" aria-hidden="true"></i> <?php echo date('Y-m-d', strtotime($article['published_at'])); ?></span>
                                </div>
                                <div class="read-more-wrap">
                                    <a href="/article/<?php echo htmlspecialchars($article['slug']); ?>" class="nes-btn is-primary">
                                        <i data-icon="caret-right" data-size="sm" aria-hidden="true"></i> 阅读全文
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="nes-container is-rounded empty-state-box">
                    <i data-icon="close" data-size="lg" aria-hidden="true"></i>
                    <p class="nes-text empty-state-title">暂无最新文章</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 右侧侧边栏 -->
    <aside class="col-4 col-lg-12">
        <!-- 热门排行区块 -->
        <div class="sidebar-section">
            <div class="sidebar-title"><i data-icon="trophy" data-size="sm" aria-hidden="true"></i> 热门排行</div>
            <div class="sidebar-content">
                <?php if (!empty($hotArticles)): ?>
                <ul class="rank-list">
                    <?php foreach (array_slice($hotArticles, 0, 10) as $index => $article): ?>
                    <li class="rank-item">
                        <span class="rank-badge <?php echo $index < 3 ? 'rank-' . ($index + 1) : ''; ?>" aria-label="排名第 <?php echo $index + 1; ?>">
                            <?php echo $index + 1; ?>
                        </span>
                        <a href="/article/<?php echo isset($article['slug']) ? $article['slug'] : ''; ?>" class="rank-link">
                            <?php echo isset($article['title']) ? truncate_string($article['title'], 30) : ''; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="empty-text">暂无热门文章</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 生态入口区块 -->
        <div class="sidebar-section">
            <div class="sidebar-title"><i data-icon="link" data-size="sm" aria-hidden="true"></i> 生态入口</div>
            <div class="sidebar-content">
                <div class="link-list">
                    <a href="https://bgjq.top" class="nes-btn is-primary" target="_blank" rel="noopener">
                        <i data-icon="star" data-size="sm" aria-hidden="true"></i> 主站
                    </a>
                    <a href="https://8w.bgjq.top" class="nes-btn is-success" target="_blank" rel="noopener">
                        <i data-icon="chat" data-size="sm" aria-hidden="true"></i> 8w社区
                    </a>
                    <a href="https://wiki.bgjq.top" class="nes-btn is-warning" target="_blank" rel="noopener">
                        <i data-icon="book" data-size="sm" aria-hidden="true"></i> Wiki百科
                    </a>
                    <a href="https://countries.bgjq.top" class="nes-btn is-error" target="_blank" rel="noopener">
                        <i data-icon="crown" data-size="sm" aria-hidden="true"></i> 邦国主页
                    </a>
                </div>
            </div>
        </div>

        <!-- 热门标签区块 -->
        <div class="sidebar-section">
            <div class="sidebar-title"><i data-icon="tag" data-size="sm" aria-hidden="true"></i> 热门标签</div>
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
            <div class="sidebar-title"><i data-icon="heart" data-size="sm" aria-hidden="true"></i> 关于我们</div>
            <div class="sidebar-content">
                <p class="about-text">
                    邦国新闻是邦国崛起MC国战服务器的官方新闻平台，为玩家提供最新的游戏资讯、攻略和活动信息。
                </p>
                <div class="about-info">
                    <p><i data-icon="github" data-size="sm" aria-hidden="true"></i> 服务器IP: bgjq.simpfun.cn</p>
                    <p><i data-icon="heart" data-size="sm" aria-hidden="true"></i> 官方QQ群: 1081785684</p>
                </div>
            </div>
        </div>
    </aside>
</div>

<script>
function switchTab(tab) {
    const hotContent = document.getElementById('hot-content');
    const latestContent = document.getElementById('latest-content');
    const hotBtn = document.getElementById('hot-tab-btn');
    const latestBtn = document.getElementById('latest-tab-btn');

    if (tab === 'hot') {
        hotContent.classList.remove('hidden');
        latestContent.classList.add('hidden');
        hotBtn.classList.add('is-active');
        hotBtn.setAttribute('aria-selected', 'true');
        latestBtn.classList.remove('is-active');
        latestBtn.setAttribute('aria-selected', 'false');
    } else {
        hotContent.classList.add('hidden');
        latestContent.classList.remove('hidden');
        hotBtn.classList.remove('is-active');
        hotBtn.setAttribute('aria-selected', 'false');
        latestBtn.classList.add('is-active');
        latestBtn.setAttribute('aria-selected', 'true');
    }
}
</script>

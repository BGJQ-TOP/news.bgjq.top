<!-- 栏目文章列表页面 -->
<!-- 面包屑导航 -->
<nav aria-label="面包屑导航">
    <ul class="breadcrumb">
        <li><a href="/" class="nes-text is-primary">首页</a></li>
        <li class="nes-text is-disabled" aria-current="page"><?php echo htmlspecialchars($category['category_name'] ?? $category['name'] ?? '未知栏目'); ?></li>
    </ul>
</nav>

<!-- 主内容区 -->
<div class="row">
    <!-- 左侧主要内容 -->
    <div class="col-8 col-lg-12">
        <!-- 栏目信息 -->
        <div class="nes-container with-title mb-4">
            <h1 class="title">
                <i data-icon="folder" aria-hidden="true"></i>
                <?php echo htmlspecialchars($category['category_name'] ?? $category['name'] ?? '未知栏目'); ?>
            </h1>
            <?php if (!empty($category['category_description'] ?? $category['description'] ?? '')): ?>
            <p class="category-description">
                <?php echo htmlspecialchars($category['category_description'] ?? $category['description'] ?? ''); ?>
            </p>
            <?php endif; ?>
            <div class="category-meta">
                <span class="nes-badge is-primary">
                    <span class="is-primary">共 <?php echo count($articles); ?> 篇文章</span>
                </span>
            </div>
        </div>

        <!-- 文章列表 -->
        <div class="nes-container with-title">
            <h3 class="title"><i data-icon="list" aria-hidden="true"></i> 文章列表</h3>

            <?php if (!empty($articles)): ?>
            <ul class="article-list">
                <?php foreach ($articles as $article): ?>
                <li class="article-list-item">
                    <div class="row">
                        <?php if (!empty($article['article_cover_image'] ?? $article['cover_image'] ?? '')): ?>
                        <div class="col-3 col-sm-12 article-cover-wrap">
                            <img src="<?php echo htmlspecialchars($article['article_cover_image'] ?? $article['cover_image'] ?? ''); ?>"
                                 alt="<?php echo htmlspecialchars($article['article_title'] ?? $article['title'] ?? '文章封面图'); ?>"
                                 class="pixelated article-cover-img"
                                 loading="lazy">
                        </div>
                        <div class="col-9 col-sm-12">
                        <?php else: ?>
                        <div class="col-12">
                        <?php endif; ?>
                            <h4 class="article-title">
                                <a href="/article/<?php echo htmlspecialchars($article['article_slug'] ?? $article['slug'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($article['article_title'] ?? $article['title'] ?? '无标题'); ?>
                                </a>
                            </h4>
                            <p class="article-excerpt">
                                <?php
                                $content = strip_tags($article['article_content'] ?? $article['content'] ?? '');
                                echo mb_substr($content, 0, 120, 'UTF-8') . '...';
                                ?>
                            </p>
                            <div class="article-meta">
                                <span><i data-icon="eye" aria-hidden="true"></i> <?php echo $article['article_read_count'] ?? $article['read_count'] ?? 0; ?></span>
                                <span><i data-icon="heart" aria-hidden="true"></i> <?php echo $article['article_like_count'] ?? $article['like_count'] ?? 0; ?></span>
                                <span><i data-icon="calendar" aria-hidden="true"></i> <?php echo date('Y-m-d', strtotime($article['article_published_at'] ?? $article['published_at'] ?? 'now')); ?></span>
                                <?php if (!empty($article['author_name'])): ?>
                                <span><i data-icon="user" aria-hidden="true"></i> <?php echo htmlspecialchars($article['author_name']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="read-more-wrap">
                                <a href="/article/<?php echo htmlspecialchars($article['article_slug'] ?? $article['slug'] ?? ''); ?>" class="nes-btn is-primary" aria-label="阅读文章全文">
                                    <i data-icon="caret-right" aria-hidden="true"></i> 阅读全文
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <div class="empty-state">
                <i data-icon="close" aria-hidden="true"></i>
                <p class="empty-state-title">该栏目暂无文章</p>
                <p class="empty-state-subtitle">敬请期待更多精彩内容</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 右侧侧边栏 -->
    <aside class="col-4 col-lg-12" aria-label="侧边栏">
        <!-- 热门排行区块 -->
        <div class="sidebar-section">
            <div class="sidebar-title"><i data-icon="trophy" aria-hidden="true"></i> 热门排行</div>
            <div class="sidebar-content">
                <?php if (!empty($hotArticles)): ?>
                <ul class="rank-list">
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
                </ul>
                <?php else: ?>
                <p class="empty-text">暂无热门文章</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 生态入口区块 -->
        <div class="sidebar-section">
            <div class="sidebar-title"><i data-icon="link" aria-hidden="true"></i> 生态入口</div>
            <div class="sidebar-content">
                <div class="link-list">
                    <a href="https://bgjq.top" class="nes-btn is-primary" target="_blank" rel="noopener noreferrer" aria-label="访问主站（新窗口打开）">
                        <i data-icon="star" aria-hidden="true"></i> 主站
                    </a>
                    <a href="https://8w.bgjq.top" class="nes-btn is-success" target="_blank" rel="noopener noreferrer" aria-label="访问8w社区（新窗口打开）">
                        <i data-icon="chat" aria-hidden="true"></i> 8w社区
                    </a>
                    <a href="https://wiki.bgjq.top" class="nes-btn is-warning" target="_blank" rel="noopener noreferrer" aria-label="访问Wiki百科（新窗口打开）">
                        <i data-icon="book" aria-hidden="true"></i> Wiki百科
                    </a>
                    <a href="https://countries.bgjq.top" class="nes-btn is-error" target="_blank" rel="noopener noreferrer" aria-label="访问邦国主页（新窗口打开）">
                        <i data-icon="crown" aria-hidden="true"></i> 邦国主页
                    </a>
                </div>
            </div>
        </div>

        <!-- 热门标签区块 -->
        <div class="sidebar-section">
            <div class="sidebar-title"><i data-icon="tag" aria-hidden="true"></i> 热门标签</div>
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
            <div class="sidebar-title"><i data-icon="heart" aria-hidden="true"></i> 关于我们</div>
            <div class="sidebar-content">
                <p class="about-text">
                    邦国新闻是邦国崛起MC国战服务器的官方新闻平台，为玩家提供最新的游戏资讯、攻略和活动信息。
                </p>
                <div class="about-info">
                    <p><i data-icon="github" aria-hidden="true"></i> 服务器IP: bgjq.simpfun.cn</p>
                    <p><i data-icon="heart" aria-hidden="true"></i> 官方QQ群: 1081785684</p>
                </div>
            </div>
        </div>
    </aside>
</div>

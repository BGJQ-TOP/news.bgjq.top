<!-- 栏目列表页面 -->
<!-- 面包屑导航 -->
<nav>
    <ul class="breadcrumb">
        <li><a href="/" class="nes-text is-primary">首页</a></li>
        <li class="nes-text is-disabled">栏目大全</li>
    </ul>
</nav>

<!-- 主内容区 -->
<div class="row">
    <!-- 左侧主要内容 -->
    <div class="col-8 col-lg-12">
        <div class="nes-container with-title">
            <h1 class="title">
                <i class="nes-icon folder is-small"></i> 栏目大全
            </h1>
            
            <div class="row">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                    <div class="col-md-6 col-sm-12 mb-4">
                        <a href="/category/<?php echo htmlspecialchars($category['category_slug']); ?>/" class="text-decoration-none">
                            <div class="card h-100 category-card">
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?php echo htmlspecialchars($category['category_name']); ?></h5>
                                    <?php if (!empty($category['category_description'])): ?>
                                    <p class="card-text text-muted small"><?php echo htmlspecialchars($category['category_description']); ?></p>
                                    <?php endif; ?>
                                    <span class="badge bg-primary"><?php echo isset($category['article_count']) ? $category['article_count'] : 0; ?> 篇文章</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            暂无栏目信息
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 右侧侧边栏 -->
    <aside class="col-4 col-lg-12">
        <!-- 热门排行区块 -->
        <div class="sidebar-section">
            <div class="sidebar-title"><i class="nes-icon trophy is-small"></i> 热门排行</div>
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
            <div class="sidebar-title"><i class="nes-icon link is-small"></i> 生态入口</div>
            <div class="sidebar-content">
                <div class="link-list">
                    <a href="https://bgjq.top" class="nes-btn is-primary" target="_blank" rel="noopener">
                        <i class="nes-icon star is-small"></i> 主站
                    </a>
                    <a href="https://8w.bgjq.top" class="nes-btn is-success" target="_blank" rel="noopener">
                        <i class="nes-icon chat is-small"></i> 8w社区
                    </a>
                    <a href="https://wiki.bgjq.top" class="nes-btn is-warning" target="_blank" rel="noopener">
                        <i class="nes-icon book is-small"></i> Wiki百科
                    </a>
                    <a href="https://countries.bgjq.top" class="nes-btn is-error" target="_blank" rel="noopener">
                        <i class="nes-icon crown is-small"></i> 邦国主页
                    </a>
                </div>
            </div>
        </div>

        <!-- 热门标签区块 -->
        <div class="sidebar-section">
            <div class="sidebar-title"><i class="nes-icon tag is-small"></i> 热门标签</div>
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
            <div class="sidebar-title"><i class="nes-icon heart is-small"></i> 关于我们</div>
            <div class="sidebar-content">
                <p class="about-text">
                    邦国新闻是邦国崛起MC国战服务器的官方新闻平台，为玩家提供最新的游戏资讯、攻略和活动信息。
                </p>
                <div class="about-info">
                    <p><i class="nes-icon github is-small"></i> 服务器IP: bgjq.simpfun.cn</p>
                    <p><i class="nes-icon heart is-small"></i> 官方QQ群: 1081785684</p>
                </div>
            </div>
        </div>
    </aside>
</div>

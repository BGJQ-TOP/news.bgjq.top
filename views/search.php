<!-- 搜索页面 -->
<!-- 面包屑导航 -->
<nav aria-label="面包屑导航">
    <ul class="breadcrumb">
        <li><a href="/" class="nes-text is-primary">首页</a></li>
        <li class="nes-text is-disabled" aria-current="page">搜索</li>
    </ul>
</nav>

<!-- 主内容区 -->
<div class="row">
    <!-- 左侧主要内容 -->
    <div class="col-8 col-lg-12">
        <!-- 搜索框 -->
        <div class="nes-container with-title mb-4">
            <h3 class="title"><i data-icon="search" aria-hidden="true"></i> 搜索文章</h3>
            <form method="GET" action="/search" role="search" aria-label="站内搜索">
                <div class="search-form-row">
                    <div class="search-field">
                        <input type="text" class="nes-input search-input" name="q"
                               placeholder="请输入搜索关键词..."
                               value="<?php echo htmlspecialchars($keyword); ?>"
                               required
                               aria-label="搜索关键词">
                    </div>
                    <button type="submit" class="nes-btn is-primary" aria-label="执行搜索">
                        <i data-icon="search" aria-hidden="true"></i> 搜索
                    </button>
                </div>
            </form>
        </div>

        <!-- 搜索结果 -->
        <?php if (!empty($keyword)): ?>
            <div class="nes-container with-title mb-4">
                <h3 class="title">
                    <?php if ($results && $results['total'] > 0): ?>
                        <i data-icon="check" aria-hidden="true"></i> 搜索结果
                    <?php else: ?>
                        <i data-icon="close" aria-hidden="true"></i> 搜索结果
                    <?php endif; ?>
                </h3>

                <p class="search-result-count">
                    <?php if ($results && $results['total'] > 0): ?>
                        找到 <span class="nes-text is-primary"><?php echo $results['total']; ?></span>
                        篇关于 "<?php echo htmlspecialchars($keyword); ?>" 的文章
                    <?php else: ?>
                        未找到关于 "<?php echo htmlspecialchars($keyword); ?>" 的相关结果
                    <?php endif; ?>
                </p>

                <?php if ($results && !empty($results['data'])): ?>
                    <ul class="article-list">
                        <?php foreach ($results['data'] as $article): ?>
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
                                            <?php
                                            // 高亮关键词
                                            $title = htmlspecialchars($article['article_title'] ?? $article['title'] ?? '');
                                            echo preg_replace('/(' . preg_quote($keyword, '/') . ')/i',
                                                 '<span class="search-highlight">$1</span>', $title);
                                            ?>
                                        </a>
                                    </h4>
                                    <p class="article-excerpt">
                                        <?php
                                        $content = strip_tags($article['article_content'] ?? $article['content'] ?? '');
                                        $excerpt = mb_substr($content, 0, 150, 'UTF-8') . '...';
                                        // 高亮关键词
                                        echo preg_replace('/(' . preg_quote($keyword, '/') . ')/i',
                                             '<span class="search-highlight">$1</span>', htmlspecialchars($excerpt));
                                        ?>
                                    </p>
                                    <div class="article-meta">
                                        <span><i data-icon="calendar" aria-hidden="true"></i> <?php echo date('Y-m-d', strtotime($article['article_published_at'] ?? $article['published_at'] ?? 'now')); ?></span>
                                        <span><i data-icon="eye" aria-hidden="true"></i> <?php echo $article['article_read_count'] ?? $article['read_count'] ?? 0; ?></span>
                                        <span><i data-icon="heart" aria-hidden="true"></i> <?php echo $article['article_like_count'] ?? $article['like_count'] ?? 0; ?></span>
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

                    <!-- 分页 -->
                    <?php if ($results['totalPages'] > 1): ?>
                    <nav class="pagination" aria-label="搜索结果分页">
                        <a href="/search?q=<?php echo urlencode($keyword); ?>&page=<?php echo $results['page'] - 1; ?>"
                           class="nes-btn <?php echo $results['page'] <= 1 ? 'is-disabled' : 'is-primary'; ?>"
                           <?php echo $results['page'] <= 1 ? 'aria-disabled="true" tabindex="-1"' : ''; ?>
                           aria-label="上一页">
                            <i data-icon="caret-left" aria-hidden="true"></i> 上一页
                        </a>

                        <?php for ($i = max(1, $results['page'] - 2); $i <= min($results['totalPages'], $results['page'] + 2); $i++): ?>
                        <a href="/search?q=<?php echo urlencode($keyword); ?>&page=<?php echo $i; ?>"
                           class="nes-btn <?php echo $i === $results['page'] ? 'is-success' : ''; ?>"
                           <?php echo $i === $results['page'] ? 'aria-current="page"' : ''; ?>
                           aria-label="第 <?php echo $i; ?> 页">
                            <?php echo $i; ?>
                        </a>
                        <?php endfor; ?>

                        <a href="/search?q=<?php echo urlencode($keyword); ?>&page=<?php echo $results['page'] + 1; ?>"
                           class="nes-btn <?php echo $results['page'] >= $results['totalPages'] ? 'is-disabled' : 'is-primary'; ?>"
                           <?php echo $results['page'] >= $results['totalPages'] ? 'aria-disabled="true" tabindex="-1"' : ''; ?>
                           aria-label="下一页">
                            下一页 <i data-icon="caret-right" aria-hidden="true"></i>
                        </a>
                    </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="empty-state">
                        <i data-icon="close" aria-hidden="true"></i>
                        <p class="empty-state-title">
                            没有找到与 "<?php echo htmlspecialchars($keyword); ?>" 相关的内容，请尝试其他关键词。
                        </p>
                    </div>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- 热门搜索建议 -->
            <div class="nes-container with-title">
                <h3 class="title"><i data-icon="fire" aria-hidden="true"></i> 热门搜索</h3>
                <p class="search-suggestion-text">您可以尝试搜索以下内容：</p>
                <div class="tag-cloud">
                    <a href="/search?q=诗词" class="tag">诗词</a>
                    <a href="/search?q=科技" class="nes-btn is-primary">科技</a>
                    <a href="/search?q=生活" class="nes-btn is-success">生活</a>
                    <a href="/search?q=随笔" class="nes-btn is-warning">随笔</a>
                    <a href="/search?q=新闻" class="nes-btn is-error">新闻</a>
                    <a href="/search?q=娱乐" class="nes-btn">娱乐</a>
                </div>
            </div>
        <?php endif; ?>
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

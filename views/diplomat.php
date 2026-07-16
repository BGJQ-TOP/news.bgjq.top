<!-- 外交公告发布页面 -->
<!-- 面包屑导航 -->
<nav>
    <ul class="breadcrumb">
        <li><a href="/" class="nes-text is-primary">首页</a></li>
        <li><a href="/contribute" class="nes-text is-primary">投稿中心</a></li>
        <li class="nes-text is-disabled">外交公告</li>
    </ul>
</nav>

<!-- 主内容区 -->
<div class="row">
    <!-- 左侧主要内容 -->
    <div class="col-8 col-lg-12">
        <div class="nes-container with-title">
            <h1 class="title">
                <i class="nes-icon star is-small"></i> 发布外交公告
            </h1>

            <div class="nes-container is-rounded" style="margin-bottom: 24px; background: rgba(255, 204, 0, 0.1);">
                <p class="nes-text is-warning">
                    <i class="nes-icon star is-small"></i>
                    <strong>外交官专属</strong> - 您可以每日直接发布一篇外交公告，无需审核
                </p>
            </div>

            <div style="margin-bottom: 20px; padding: 12px; background: rgba(0,0,0,0.05); border-radius: 4px;">
                <p><strong>当前登录用户：</strong><?php echo htmlspecialchars($currentUser['username']); ?></p>
                <?php if (!empty($userCountry)): ?>
                <p><strong>所属邦国：</strong><?php echo htmlspecialchars($userCountry['name']); ?></p>
                <?php endif; ?>
            </div>

            <form id="diplomatForm">
                <!-- 公告标题 -->
                <div class="form-group">
                    <label for="title" class="form-label">
                        <i class="nes-icon star is-small"></i> 公告标题 <span class="nes-text is-error">*</span>
                    </label>
                    <input type="text" class="form-input" id="title" name="title"
                           placeholder="请输入外交公告标题" required maxlength="200">
                </div>

                <!-- 公告内容 -->
                <div class="form-group">
                    <label for="content" class="form-label">
                        <i class="nes-icon list is-small"></i> 公告内容 <span class="nes-text is-error">*</span>
                    </label>
                    <textarea class="form-textarea" id="content" name="content" rows="12"
                              placeholder="请输入外交公告内容..." required></textarea>
                </div>

                <!-- 提示信息 -->
                <div class="nes-container is-rounded" style="margin-bottom: 24px; background: rgba(239, 68, 68, 0.1);">
                    <p class="nes-text is-error">
                        <i class="nes-icon close is-small"></i>
                        <strong>注意：</strong>外交公告将直接发布到网站，每日仅可发布一篇，请谨慎操作
                    </p>
                </div>

                <!-- 提交按钮 -->
                <div style="text-align: center;">
                    <button type="submit" class="nes-btn is-warning">
                        <i class="nes-icon star is-small"></i> 直接发布
                    </button>
                    <a href="/contribute" class="nes-btn">返回投稿</a>
                </div>
            </form>
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

<script>
document.getElementById('diplomatForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const title = document.getElementById('title').value.trim();
    const content = document.getElementById('content').value.trim();

    if (!title || !content) {
        alert('请填写标题和内容');
        return;
    }

    if (!confirm('确定要发布这篇外交公告吗？发布后将直接显示在网站上。')) {
        return;
    }

    const formData = new FormData();
    formData.append('title', title);
    formData.append('content', content);

    fetch('/api/diplomat-publish', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '/';
        } else {
            alert(data.message || '发布失败');
        }
    })
    .catch(error => {
        alert('网络错误，请稍后重试');
    });
});
</script>

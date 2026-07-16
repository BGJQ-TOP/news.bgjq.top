<!-- 投稿页面 -->
<!-- 面包屑导航 -->
<nav aria-label="面包屑导航">
    <ul class="breadcrumb">
        <li><a href="/" class="nes-text is-primary">首页</a></li>
        <li class="nes-text is-disabled" aria-current="page">投稿</li>
    </ul>
</nav>

<!-- 主内容区 -->
<div class="row">
    <!-- 左侧主要内容 -->
    <div class="col-8 col-lg-12">
        <div class="nes-container with-title">
            <h1 class="title">
                <i data-icon="envelope" aria-hidden="true"></i> 投稿中心
            </h1>

            <!-- 用户信息 -->
            <?php if (!empty($currentUser)): ?>
            <div class="nes-container is-rounded user-info-box">
                <p class="nes-text is-success">
                    <i data-icon="star" aria-hidden="true"></i>
                    欢迎，<strong><?php echo htmlspecialchars($currentUser['username']); ?></strong>！
                    <?php if (!empty($userCountry)): ?>
                    （<?php echo htmlspecialchars($userCountry['name']); ?>）
                    <?php endif; ?>
                </p>
            </div>
            <?php endif; ?>

            <!-- 外交官快捷入口 -->
            <?php if ($isLoggedIn && $isDiplomat): ?>
            <div class="nes-container is-rounded diplomat-info-box">
                <p class="nes-text is-warning">
                    <i data-icon="star" aria-hidden="true"></i>
                    <strong>外交官特权：</strong>
                    <?php if ($canPublishDiplomat): ?>
                    您可以<a href="/diplomat" class="diplomat-link">直接发布外交公告</a>，无需审核，每日可发布一篇
                    <?php else: ?>
                    您今日已发布外交公告，请明日再来
                    <?php endif; ?>
                </p>
            </div>
            <?php endif; ?>

            <div class="nes-container is-rounded welcome-info-box">
                <p class="nes-text is-success category-description">
                    <i data-icon="star" aria-hidden="true"></i>
                    <strong>欢迎投稿！</strong> 请将您的原创内容发送给我们，审核后将在网站发布。
                </p>
            </div>

            <form id="contributeForm" novalidate>
                <!-- 文章标题 -->
                <div class="form-group">
                    <label for="title" class="form-label">
                        <i data-icon="star" aria-hidden="true"></i> 文章标题 <span class="nes-text is-error" aria-label="必填">*</span>
                    </label>
                    <input type="text" class="form-input" id="title" name="title"
                           placeholder="请输入文章标题" required maxlength="200" autocomplete="off"
                           aria-required="true" aria-describedby="title-error">
                    <span id="title-error" class="form-error" role="alert" aria-live="polite"></span>
                </div>

                <!-- 栏目选择 -->
                <div class="form-group">
                    <label for="category" class="form-label">
                        <i data-icon="folder" aria-hidden="true"></i> 选择栏目 <span class="nes-text is-error" aria-label="必填">*</span>
                    </label>
                    <div class="nes-select">
                        <select id="category" name="category" required class="form-select"
                                aria-required="true" aria-describedby="category-error">
                            <option value="">请选择栏目</option>
                            <?php
                            $categoryModel = new CategoryModel();
                            $categories = $categoryModel->getActiveCategories();
                            foreach ($categories as $category):
                            ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo $category['category_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <span id="category-error" class="form-error" role="alert" aria-live="polite"></span>
                </div>

                <!-- 文章内容 -->
                <div class="form-group">
                    <label for="content" class="form-label">
                        <i data-icon="list" aria-hidden="true"></i> 文章内容 <span class="nes-text is-error" aria-label="必填">*</span>
                    </label>
                    <textarea class="form-textarea" id="content" name="content" rows="12"
                              placeholder="请输入文章内容..." required autocomplete="off"
                              aria-required="true" aria-describedby="content-error"></textarea>
                    <span id="content-error" class="form-error" role="alert" aria-live="polite"></span>
                </div>

                <!-- 封面图片 -->
                <div class="form-group">
                    <label for="cover_image" class="form-label">
                        <i data-icon="image" aria-hidden="true"></i> 封面图片 URL（可选）
                    </label>
                    <input type="url" class="form-input" id="cover_image" name="cover_image"
                           placeholder="请输入图片 URL 地址" autocomplete="off"
                           aria-describedby="cover-image-hint">
                    <span id="cover-image-hint" class="form-hint">支持常见图片格式链接</span>
                </div>

                <!-- 同意协议 -->
                <div class="form-agreement">
                    <label class="agreement-label">
                        <input type="checkbox" class="nes-checkbox" id="agree" name="agree" required
                               aria-required="true" aria-describedby="agree-error">
                        <span>我已阅读并同意投稿协议，保证内容为原创</span>
                    </label>
                    <span id="agree-error" class="form-error" role="alert" aria-live="polite"></span>
                </div>

                <!-- 提交按钮 -->
                <div class="form-actions">
                    <button type="submit" class="nes-btn is-primary">
                        <i data-icon="check" aria-hidden="true"></i> 提交投稿
                    </button>
                </div>
            </form>
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
                    <a href="https://bgjq.top" class="nes-btn is-primary" target="_blank" rel="noopener">
                        <i data-icon="star" aria-hidden="true"></i> 主站
                    </a>
                    <a href="https://8w.bgjq.top" class="nes-btn is-success" target="_blank" rel="noopener">
                        <i data-icon="chat" aria-hidden="true"></i> 8w社区
                    </a>
                    <a href="https://wiki.bgjq.top" class="nes-btn is-warning" target="_blank" rel="noopener">
                        <i data-icon="book" aria-hidden="true"></i> Wiki百科
                    </a>
                    <a href="https://countries.bgjq.top" class="nes-btn is-error" target="_blank" rel="noopener">
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

<script>
document.getElementById('contributeForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const title = document.getElementById('title');
    const category = document.getElementById('category');
    const content = document.getElementById('content');
    const coverImage = document.getElementById('cover_image');
    const agree = document.getElementById('agree');

    let isValid = true;

    // 清除之前的错误提示
    document.querySelectorAll('.form-error').forEach(function(el) {
        el.textContent = '';
    });
    document.querySelectorAll('.form-input, .form-textarea, .form-select').forEach(function(el) {
        el.classList.remove('is-error');
    });

    // 验证标题
    if (!title.value.trim()) {
        showError(title, 'title-error', '请输入文章标题');
        isValid = false;
    } else if (title.value.trim().length < 2) {
        showError(title, 'title-error', '标题至少需要2个字符');
        isValid = false;
    }

    // 验证栏目
    if (!category.value) {
        showError(category, 'category-error', '请选择栏目');
        isValid = false;
    }

    // 验证内容
    if (!content.value.trim()) {
        showError(content, 'content-error', '请输入文章内容');
        isValid = false;
    } else if (content.value.trim().length < 10) {
        showError(content, 'content-error', '内容至少需要10个字符');
        isValid = false;
    }

    // 验证协议
    if (!agree.checked) {
        showError(agree, 'agree-error', '请阅读并同意投稿协议');
        isValid = false;
    }

    if (!isValid) {
        return;
    }

    const formData = new FormData();
    formData.append('title', title.value.trim());
    formData.append('category', category.value);
    formData.append('content', content.value.trim());
    if (coverImage.value.trim()) {
        formData.append('cover_image', coverImage.value.trim());
    }

    fetch('/api/contribute', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            document.getElementById('contributeForm').reset();
        } else {
            alert(data.message || '投稿失败');
        }
    })
    .catch(error => {
        alert('网络错误，请稍后重试');
    });
});

function showError(input, errorId, message) {
    const errorEl = document.getElementById(errorId);
    if (errorEl) {
        errorEl.textContent = message;
    }
    if (input && input.classList) {
        input.classList.add('is-error');
    }
}
</script>
<!-- 登录页面 -->
<!-- 面包屑导航 -->
<nav aria-label="面包屑导航">
    <ul class="breadcrumb">
        <li><a href="/" class="nes-text is-primary">首页</a></li>
        <li class="nes-text is-disabled" aria-current="page">登录</li>
    </ul>
</nav>

<!-- 主内容区 -->
<div class="row">
    <!-- 左侧主要内容 -->
    <div class="col-8 col-lg-12">
        <div class="nes-container with-title">
            <h1 class="title">
                <i data-icon="user" data-size="sm" aria-hidden="true"></i> 用户登录
            </h1>

            <div class="nes-container is-rounded login-notice">
                <p class="nes-text is-success category-description">
                    <i data-icon="star" data-size="sm" aria-hidden="true"></i>
                    <strong>8W社区账号登录</strong> - 请使用您在8W社区注册的用户名和密码登录
                </p>
            </div>

            <form id="loginForm" autocomplete="on">
                <!-- 用户名 -->
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i data-icon="user" data-size="sm" aria-hidden="true"></i> 用户名 <span class="nes-text is-error" aria-label="必填">*</span>
                    </label>
                    <input type="text" class="form-input" id="username" name="username"
                           placeholder="请输入8W社区用户名" required autocomplete="username"
                           aria-required="true">
                </div>

                <!-- 密码 -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i data-icon="lock" data-size="sm" aria-hidden="true"></i> 密码 <span class="nes-text is-error" aria-label="必填">*</span>
                    </label>
                    <input type="password" class="form-input" id="password" name="password"
                           placeholder="请输入密码" required autocomplete="current-password"
                           aria-required="true">
                </div>

                <!-- 错误提示 -->
                <div id="errorMessage" class="login-error" role="alert" aria-live="assertive">
                    <p class="login-error-text" id="errorText"></p>
                </div>

                <!-- 登录按钮 -->
                <div class="login-submit-wrap">
                    <button type="submit" class="nes-btn is-primary">
                        <i data-icon="login" data-size="sm" aria-hidden="true"></i> 登录
                    </button>
                </div>
            </form>

            <!-- 提示信息 -->
            <div class="nes-container is-rounded login-tips">
                <p><strong>温馨提示：</strong></p>
                <ul class="login-tips-list">
                    <li>登录后可进行投稿</li>
                    <li>外交官(diplomat)角色可每日直接发布一篇外交公告</li>
                    <li>如忘记密码，请前往8W社区重置</li>
                </ul>
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
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');

    console.log('username input value:', usernameInput.value);
    console.log('password input value:', passwordInput.value);
    console.log('username input value length:', usernameInput.value.length);
    console.log('password input value length:', passwordInput.value.length);

    const username = usernameInput.value.trim();
    const password = passwordInput.value;
    const redirect = '<?php echo htmlspecialchars($redirect ?? "/contribute"); ?>';

    if (!username || !password) {
        showError('请输入用户名和密码 (username长度:' + username.length + ', password长度:' + password.length + ')');
        return;
    }

    const formData = new FormData();
    formData.append('username', username);
    formData.append('password', password);
    formData.append('redirect', redirect);

    console.log('Sending login request...');

    fetch('/api/login', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Response text:', text);
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            showError('服务器返回了错误: ' + text.substring(0, 200));
            return;
        }
        console.log('Response data:', data);
        if (data.success) {
            window.location.href = data.redirect || '/contribute';
        } else {
            showError(data.message || '登录失败，请检查用户名和密码');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showError('网络错误，请稍后重试');
    });
});

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    errorText.textContent = message;
    errorDiv.classList.add('is-visible');
}
</script>

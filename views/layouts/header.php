<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    // 使用智能SEO生成功能
    $seoMeta = generate_seo_meta($pageTitle ?? null, $pageDescription ?? null);
    $finalTitle = $seoMeta['title'];
    $finalDescription = $seoMeta['description'];
    ?>
    <title><?php echo $finalTitle . SEO_TITLE_SUFFIX; ?></title>
    <meta name="description" content="<?php echo $finalDescription; ?>">
    <meta name="keywords" content="<?php echo isset($pageKeywords) ? $pageKeywords : SITE_KEYWORDS; ?>">

    <!-- 重要的SEO Meta标签 -->
    <meta name="robots" content="index, follow">
    <meta name="author" content="邦国新闻">
    <meta name="language" content="zh-CN">
    <meta name="revisit-after" content="1 days">

    <!-- 定义全局 ASSET_URL 变量 -->
    <script>window.ASSET_URL = '<?php echo ASSET_URL; ?>';</script>
    <meta name="distribution" content="global">
    <meta name="rating" content="general">

    <!-- Open Graph Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo SITE_NAME; ?>">
    <meta property="og:title" content="<?php echo $finalTitle . SEO_TITLE_SUFFIX; ?>">
    <meta property="og:description" content="<?php echo $finalDescription; ?>">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    <meta property="og:image" content="<?php echo ASSET_URL; ?>/images/BGJQ.png">
    <meta property="og:locale" content="zh_CN">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@邦国新闻">
    <meta name="twitter:title" content="<?php echo $finalTitle . SEO_TITLE_SUFFIX; ?>">
    <meta name="twitter:description" content="<?php echo $finalDescription; ?>">
    <meta name="twitter:image" content="<?php echo ASSET_URL; ?>/images/BGJQ.png">

    <!-- Theme Color -->
    <meta name="theme-color" content="#209cee" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">

    <!-- 修复后的样式文件 -->
    <link rel="stylesheet" href="<?php echo ASSET_URL; ?>/css/style.css">

    <!-- 图标 -->
    <link rel="icon" href="<?php echo ASSET_URL; ?>/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo ASSET_URL; ?>/images/BGJQ.png">

    <!-- 结构化数据 -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?php echo SITE_NAME; ?>",
        "url": "<?php echo SITE_URL; ?>",
        "description": "<?php echo SITE_DESCRIPTION; ?>",
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo SITE_NAME; ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo ASSET_URL; ?>/images/BGJQ.png"
            }
        },
        "potentialAction": {
            "@type": "SearchAction",
            "target": "<?php echo SITE_URL; ?>/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <!-- 站点统计脚本 -->
    <script>
    (function(){
    var el = document.createElement("script");
    el.src = "https://lf1-cdn-tos.bytegoofy.com/goofy/ttzz/push.js?247b0901ca59d78b6a391f46d8d989ac43b9c191ea6648f9a76288961b8e76a33d72cd14f8a76432df3935ab77ec54f830517b3cb210f7fd334f50ccb772134a";
    el.id = "ttzz";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(el, s);
    })(window)
    </script>

    <!-- Clarity tracking code -->
    <script>
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i+"?ref=bwt";
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "w6fgpqhmut");
    </script>
</head>
<body data-theme="light">
    <!-- 顶部导航 -->
    <header class="site-header">
        <div class="container">
            <div class="top-nav">
                <!-- 站点品牌 -->
                <a href="/" class="site-brand">
                    <img src="<?php echo ASSET_URL; ?>/images/BGJQ.png" alt="邦国新闻" class="site-logo pixelated">
                    <div class="site-brand-text">
                        <h1 class="site-title"><?php echo SITE_NAME; ?></h1>
                        <p class="site-subtitle">BANGGUO NEWS</p>
                    </div>
                </a>

                <!-- 桌面端搜索框 -->
                <div class="search-box desktop-only">
                    <input type="search" class="search-input" placeholder="搜索文章..." id="searchInput" aria-label="搜索文章">
                    <button class="nes-btn is-primary" onclick="doSearch()" title="搜索" aria-label="搜索">
                        <span class="search-btn-inner">
                            <i data-icon="search" data-size="sm" aria-hidden="true"></i>
                            <span>搜索</span>
                        </span>
                    </button>
                </div>

                <!-- 桌面端右侧操作区 -->
                <div class="header-actions desktop-only">
                    <!-- 主题切换下拉菜单 -->
                    <div class="theme-dropdown">
                        <button id="theme-toggle" class="nes-btn" title="主题设置" onclick="toggleThemeDropdown()" aria-label="切换主题" aria-haspopup="true" aria-expanded="false">
                            <i data-icon="sun" data-size="sm" id="theme-icon" aria-hidden="true"></i>
                        </button>
                        <div id="theme-dropdown-content" class="dropdown-menu" role="menu">
                            <a href="#" class="dropdown-item" onclick="setThemeMode('light'); return false;" role="menuitem">
                                <i data-icon="sun" data-size="sm" aria-hidden="true"></i> 浅色
                            </a>
                            <a href="#" class="dropdown-item" onclick="setThemeMode('dark'); return false;" role="menuitem">
                                <i data-icon="moon" data-size="sm" aria-hidden="true"></i> 深色
                            </a>
                            <a href="#" class="dropdown-item" onclick="setThemeMode('system'); return false;" role="menuitem">
                                <i data-icon="monitor" data-size="sm" aria-hidden="true"></i> 跟随系统
                            </a>
                        </div>
                    </div>
                    <?php if (isset($_SESSION['bgjq_user_id'])): ?>
                    <div class="user-info-dropdown">
                        <button class="nes-btn is-success" onclick="toggleUserMenu()" aria-haspopup="true" aria-expanded="false" aria-label="用户菜单：<?php echo htmlspecialchars($_SESSION['bgjq_username']); ?>">
                            <i data-icon="user" data-size="sm" aria-hidden="true"></i> <?php echo htmlspecialchars($_SESSION['bgjq_username']); ?>
                        </button>
                        <div id="user-dropdown-content" class="dropdown-menu" role="menu">
                            <?php if (isset($_SESSION['bgjq_role']) && $_SESSION['bgjq_role'] === 'diplomat'): ?>
                            <a href="/diplomat" class="dropdown-item" role="menuitem">
                                <i data-icon="star" data-size="sm" aria-hidden="true"></i> 发布外交公告
                            </a>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['bgjq_role']) && in_array($_SESSION['bgjq_role'], ['secretary_general', 'permanent_member', 'diplomat'])): ?>
                            <a href="/admin/dashboard" target="_blank" class="dropdown-item" role="menuitem">
                                <i data-icon="setting" data-size="sm" aria-hidden="true"></i> 后台管理
                            </a>
                            <?php endif; ?>
                            <a href="/profile" class="dropdown-item" role="menuitem">
                                <i data-icon="user" data-size="sm" aria-hidden="true"></i> 个人中心
                            </a>
                            <a href="/logout" class="dropdown-item" role="menuitem">
                                <i data-icon="close" data-size="sm" aria-hidden="true"></i> 退出登录
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <a href="/login" class="nes-btn is-primary" title="登录">
                        <i data-icon="user" data-size="sm" aria-hidden="true"></i> 登录
                    </a>
                    <?php endif; ?>
                </div>

                <!-- 移动端菜单按钮 -->
                <button class="hamburger-btn mobile-only" id="hamburgerBtn" aria-label="打开导航菜单" aria-expanded="false" aria-controls="mobileSidebar">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </div>

            <!-- 桌面端导航菜单 -->
            <nav class="main-nav desktop-only" id="mainNav" aria-label="主导航">
                <ul class="nav-menu" id="navMenu">
                    <li><a href="/" class="nes-btn is-primary">首页</a></li>
                    <?php
                    $categories = (new CategoryModel())->getActiveCategories();
                    foreach ($categories as $category):
                    ?>
                    <li>
                        <a href="/category/<?php echo $category['category_slug']; ?>/" class="nes-btn" title="<?php echo isset($category['category_description']) ? $category['category_description'] : ''; ?>">
                            <?php echo $category['category_name']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <li><a href="/rankings" class="nes-btn is-success">排行</a></li>
                    <li><a href="/contribute" class="nes-btn is-warning">投稿</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- 移动端侧边栏遮罩层 -->
    <div class="mobile-sidebar-overlay" id="mobileSidebarOverlay"></div>

    <!-- 移动端侧边栏 -->
    <aside class="mobile-sidebar" id="mobileSidebar" aria-label="移动端导航">
        <div class="mobile-sidebar-header">
            <span class="mobile-sidebar-title">菜单</span>
            <button class="mobile-sidebar-close" id="mobileSidebarClose" aria-label="关闭菜单">&times;</button>
        </div>

        <!-- 移动端搜索框 -->
        <div class="mobile-search-box">
            <input type="search" class="search-input" placeholder="搜索文章..." id="mobileSearchInput" aria-label="搜索文章">
            <button class="nes-btn is-primary" onclick="doMobileSearch()" title="搜索" aria-label="搜索">
                <i data-icon="search" data-size="sm" aria-hidden="true"></i>
            </button>
        </div>

        <!-- 移动端导航菜单 -->
        <nav class="mobile-sidebar-nav" aria-label="移动端主导航">
            <ul class="mobile-nav-list">
                <li><a href="/" class="nes-btn is-primary">首页</a></li>
                <?php
                $categories = (new CategoryModel())->getActiveCategories();
                foreach ($categories as $category):
                ?>
                <li>
                    <a href="/category/<?php echo $category['category_slug']; ?>/" class="nes-btn">
                        <?php echo $category['category_name']; ?>
                    </a>
                </li>
                <?php endforeach; ?>
                <li><a href="/rankings" class="nes-btn is-success">排行</a></li>
                <li><a href="/contribute" class="nes-btn is-warning">投稿</a></li>
            </ul>
        </nav>

        <!-- 移动端操作区 -->
        <div class="mobile-sidebar-actions">
            <!-- 主题切换 -->
            <div class="mobile-theme-switcher">
                <span class="mobile-action-label">主题</span>
                <div class="mobile-theme-buttons">
                    <button class="nes-btn" onclick="setThemeMode('light')" title="浅色">
                        <i data-icon="sun" data-size="sm" aria-hidden="true"></i> 浅色
                    </button>
                    <button class="nes-btn" onclick="setThemeMode('dark')" title="深色">
                        <i data-icon="moon" data-size="sm" aria-hidden="true"></i> 深色
                    </button>
                    <button class="nes-btn" onclick="setThemeMode('system')" title="跟随系统">
                        <i data-icon="monitor" data-size="sm" aria-hidden="true"></i> 系统
                    </button>
                </div>
            </div>

            <!-- 登录/用户 -->
            <div class="mobile-user-area">
                <?php if (isset($_SESSION['bgjq_user_id'])): ?>
                <span class="mobile-action-label">用户</span>
                <div class="mobile-user-links">
                    <span class="mobile-username"><i data-icon="user" data-size="sm" aria-hidden="true"></i> <?php echo htmlspecialchars($_SESSION['bgjq_username']); ?></span>
                    <?php if (isset($_SESSION['bgjq_role']) && $_SESSION['bgjq_role'] === 'diplomat'): ?>
                    <a href="/diplomat" class="nes-btn"><i data-icon="star" data-size="sm" aria-hidden="true"></i> 发布外交公告</a>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['bgjq_role']) && in_array($_SESSION['bgjq_role'], ['secretary_general', 'permanent_member', 'diplomat'])): ?>
                    <a href="/admin/dashboard" target="_blank" class="nes-btn"><i data-icon="setting" data-size="sm" aria-hidden="true"></i> 后台管理</a>
                    <?php endif; ?>
                    <a href="/profile" class="nes-btn"><i data-icon="user" data-size="sm" aria-hidden="true"></i> 个人中心</a>
                    <a href="/logout" class="nes-btn is-error"><i data-icon="close" data-size="sm" aria-hidden="true"></i> 退出登录</a>
                </div>
                <?php else: ?>
                <a href="/login" class="nes-btn is-primary w-100">
                    <i data-icon="user" data-size="sm" aria-hidden="true"></i> 登录
                </a>
                <?php endif; ?>
            </div>
        </div>
    </aside>

    <!-- 主要内容区域 -->
    <main class="container">

    <script src="<?php echo ASSET_URL; ?>/js/icons.js"></script>
    <script>
    // 搜索功能
    function doSearch() {
        const keyword = document.getElementById('searchInput').value;
        if (keyword.trim()) {
            window.location.href = '/search?q=' + encodeURIComponent(keyword);
        }
    }

    // 搜索框键盘导航
    document.getElementById('searchInput')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            doSearch();
        } else if (e.key === 'Escape') {
            this.value = '';
            this.focus();
        }
    });

    // 主题切换功能
    function setThemeMode(mode) {
        const body = document.body;
        const themeIcon = document.getElementById('theme-icon');

        if (mode === 'system') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            mode = prefersDark ? 'dark' : 'light';
            localStorage.removeItem('theme');
        } else {
            localStorage.setItem('theme', mode);
        }

        body.setAttribute('data-theme', mode);

        // 更新图标
        if (themeIcon && window.BGJQ && window.BGJQ.icons) {
            const iconName = mode === 'light' ? 'sun' : 'moon';
            const newIcon = window.BGJQ.icons.create(iconName, { size: 'sm', ariaHidden: true });
            newIcon.setAttribute('id', 'theme-icon');
            themeIcon.parentNode.replaceChild(newIcon, themeIcon);
        }

        // 更新 theme-color meta
        updateThemeColor(mode);

        // 隐藏下拉菜单
        const dropdown = document.getElementById('theme-dropdown-content');
        if (dropdown) {
            dropdown.classList.remove('is-open');
        }
        const toggleBtn = document.getElementById('theme-toggle');
        if (toggleBtn) {
            toggleBtn.setAttribute('aria-expanded', 'false');
        }
    }

    // 更新 theme-color
    function updateThemeColor(mode) {
        const metaThemeColor = document.querySelector('meta[name="theme-color"]:not([media])');
        if (metaThemeColor) {
            metaThemeColor.setAttribute('content', mode === 'dark' ? '#000000' : '#209cee');
        }
    }

    // 切换主题下拉菜单
    function toggleThemeDropdown() {
        const dropdown = document.getElementById('theme-dropdown-content');
        const toggleBtn = document.getElementById('theme-toggle');
        const isOpen = dropdown.classList.contains('is-open');
        dropdown.classList.toggle('is-open');
        toggleBtn.setAttribute('aria-expanded', String(!isOpen));
    }

    // 切换用户菜单
    function toggleUserMenu() {
        const dropdown = document.getElementById('user-dropdown-content');
        const isOpen = dropdown.classList.contains('is-open');
        dropdown.classList.toggle('is-open');
        // 找到触发按钮并更新 aria-expanded
        const btn = dropdown.parentElement.querySelector('button');
        if (btn) btn.setAttribute('aria-expanded', String(!isOpen));
    }

    // 移动端侧边栏控制
    function initMobileSidebar() {
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');
        const mobileSidebarClose = document.getElementById('mobileSidebarClose');

        if (!hamburgerBtn || !mobileSidebar) return;

        function openSidebar() {
            mobileSidebar.classList.add('is-open');
            if (mobileSidebarOverlay) mobileSidebarOverlay.classList.add('is-open');
            hamburgerBtn.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            mobileSidebar.classList.remove('is-open');
            if (mobileSidebarOverlay) mobileSidebarOverlay.classList.remove('is-open');
            hamburgerBtn.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }

        hamburgerBtn.addEventListener('click', function() {
            if (mobileSidebar.classList.contains('is-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        if (mobileSidebarClose) mobileSidebarClose.addEventListener('click', closeSidebar);
        if (mobileSidebarOverlay) mobileSidebarOverlay.addEventListener('click', closeSidebar);

        // 点击侧边栏内的链接后自动关闭
        const links = mobileSidebar.querySelectorAll('a');
        links.forEach(function(link) {
            link.addEventListener('click', closeSidebar);
        });

        // ESC 键关闭侧边栏
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileSidebar.classList.contains('is-open')) {
                closeSidebar();
            }
        });
    }



    // 初始化主题
    function initTheme() {
        const savedTheme = localStorage.getItem('theme');
        let mode;

        if (savedTheme) {
            mode = savedTheme;
            document.body.setAttribute('data-theme', savedTheme);
        } else {
            // 检测系统主题
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            mode = prefersDark ? 'dark' : 'light';
            document.body.setAttribute('data-theme', mode);
        }

        // 延迟更新图标，确保 icons.js 已加载
        setTimeout(function() {
            const themeIcon = document.getElementById('theme-icon');
            if (themeIcon && window.BGJQ && window.BGJQ.icons) {
                const iconName = mode === 'light' ? 'sun' : 'moon';
                const newIcon = window.BGJQ.icons.create(iconName, { size: 'sm', ariaHidden: true });
                newIcon.setAttribute('id', 'theme-icon');
                themeIcon.parentNode.replaceChild(newIcon, themeIcon);
            }
        }, 0);
    }

    // 点击外部关闭下拉菜单
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.theme-dropdown')) {
            const dropdown = document.getElementById('theme-dropdown-content');
            if (dropdown) dropdown.classList.remove('is-open');
            const toggleBtn = document.getElementById('theme-toggle');
            if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
        }
        if (!e.target.closest('.user-info-dropdown')) {
            const userDropdown = document.getElementById('user-dropdown-content');
            if (userDropdown) userDropdown.classList.remove('is-open');
            const btn = document.querySelector('.user-info-dropdown button');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        }
    });

    // 页面加载时初始化主题和移动端侧边栏
    document.addEventListener('DOMContentLoaded', function() {
        initTheme();
        initMobileSidebar();
    });
    </script>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>后台管理系统 - <?php echo SITE_NAME; ?></title>

    <!-- NES.css 样式 -->
    <link href="<?php echo ASSET_URL; ?>/css/nes.min.css" rel="stylesheet">
    <!-- 自定义样式 -->
    <link href="<?php echo ASSET_URL; ?>/css/style.css?v=2" rel="stylesheet">
    <!-- Font Awesome 图标 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- 图标 -->
    <link rel="icon" href="<?php echo ASSET_URL; ?>/images/favicon.ico" type="image/x-icon">

    <!-- Admin 专用样式 -->
    <style>
        /* Admin 布局 */
        .admin-wrapper {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: calc(100vh - 80px);
        }

        .admin-sidebar {
            border-right: 4px solid;
            padding: 16px;
        }

        body.dark-mode .admin-sidebar {
            background-color: var(--nes-dark-gray);
            border-right-color: var(--nes-white);
        }

        body.light-mode .admin-sidebar {
            background-color: var(--nes-white);
            border-right-color: var(--nes-black);
        }

        .admin-sidebar .nav-link {
            display: block;
            padding: 12px;
            margin-bottom: 8px;
            text-decoration: none;
            border: 4px solid transparent;
            font-family: var(--font-pixel);
            font-size: 16px;
            text-align: left;
        }

        body.dark-mode .admin-sidebar .nav-link {
            color: var(--nes-white);
        }

        body.dark-mode .admin-sidebar .nav-link:hover,
        body.dark-mode .admin-sidebar .nav-link.active {
            background-color: var(--nes-green);
            color: var(--nes-black);
            border-color: var(--nes-black);
        }

        body.light-mode .admin-sidebar .nav-link {
            color: var(--nes-black);
        }

        body.light-mode .admin-sidebar .nav-link:hover,
        body.light-mode .admin-sidebar .nav-link.active {
            background-color: var(--nes-blue);
            color: var(--nes-white);
            border-color: var(--nes-black);
        }

        .admin-main {
            padding: 24px;
            overflow-x: auto;
        }

        body.dark-mode .admin-main {
            background-color: var(--nes-black);
        }

        body.light-mode .admin-main {
            background-color: var(--nes-light-gray);
        }

        /* Admin 卡片样式 */
        .admin-card {
            border: 4px solid;
            margin-bottom: 24px;
        }

        body.dark-mode .admin-card {
            background-color: var(--nes-dark-gray);
            border-color: var(--nes-gray);
        }

        body.light-mode .admin-card {
            background-color: var(--nes-white);
            border-color: var(--nes-dark-gray);
        }

        .admin-card-header {
            padding: 16px;
            border-bottom: 4px solid;
            font-family: var(--font-pixel);
            font-size: 16px;
        }

        body.dark-mode .admin-card-header {
            background-color: var(--nes-blue);
            color: var(--nes-white);
            border-bottom-color: var(--nes-white);
        }

        body.light-mode .admin-card-header {
            background-color: var(--nes-red);
            color: var(--nes-white);
            border-bottom-color: var(--nes-black);
        }

        .admin-card-body {
            padding: 16px;
        }

        .admin-card-footer {
            padding: 12px 16px;
            border-top: 2px dashed;
        }

        body.dark-mode .admin-card-footer {
            border-top-color: var(--nes-gray);
        }

        body.light-mode .admin-card-footer {
            border-top-color: var(--nes-light-gray);
        }

        /* 表格样式 */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th,
        .admin-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid;
        }

        body.dark-mode .admin-table th,
        body.dark-mode .admin-table td {
            border-bottom-color: var(--nes-gray);
        }

        body.light-mode .admin-table th,
        body.light-mode .admin-table td {
            border-bottom-color: var(--nes-light-gray);
        }

        .admin-table th {
            font-family: var(--font-pixel);
            font-size: 16px;
        }

        body.dark-mode .admin-table th {
            background-color: var(--nes-blue);
            color: var(--nes-white);
        }

        body.light-mode .admin-table th {
            background-color: var(--nes-red);
            color: var(--nes-white);
        }

        .admin-table tr:hover {
            opacity: 0.9;
        }

        /* 按钮组 */
        .btn-group {
            display: flex;
            gap: 4px;
        }

        /* 表单样式 */
        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-family: var(--font-pixel);
            font-size: 16px;
            margin-bottom: 8px;
        }

        body.dark-mode .form-label {
            color: var(--nes-green);
        }

        body.light-mode .form-label {
            color: var(--nes-blue);
        }

        .form-control,
        .form-select {
            width: 100%;
            padding: 8px 12px;
            border: 4px solid;
            font-family: var(--font-sans);
            font-size: 16px;
        }

        body.dark-mode .form-control,
        body.dark-mode .form-select {
            background-color: var(--nes-black);
            border-color: var(--nes-gray);
            color: var(--nes-white);
        }

        body.light-mode .form-control,
        body.light-mode .form-select {
            background-color: var(--nes-white);
            border-color: var(--nes-dark-gray);
            color: var(--nes-black);
        }

        /* 徽章样式 */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-family: var(--font-pixel);
            font-size: 14px;
            border: 2px solid;
        }

        .bg-primary { background-color: var(--nes-blue); color: var(--nes-white); border-color: var(--nes-black); }
        .bg-success { background-color: var(--nes-green); color: var(--nes-black); border-color: var(--nes-black); }
        .bg-warning { background-color: var(--nes-orange); color: var(--nes-black); border-color: var(--nes-black); }
        .bg-danger { background-color: var(--nes-red); color: var(--nes-white); border-color: var(--nes-black); }
        .bg-info { background-color: var(--nes-purple); color: var(--nes-white); border-color: var(--nes-black); }
        .bg-secondary { background-color: var(--nes-gray); color: var(--nes-white); border-color: var(--nes-black); }

        /* 标签页 */
        .nav-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
            border-bottom: 4px solid;
            padding-bottom: 8px;
        }

        body.dark-mode .nav-tabs {
            border-bottom-color: var(--nes-gray);
        }

        body.light-mode .nav-tabs {
            border-bottom-color: var(--nes-light-gray);
        }

        .nav-tabs .nav-link {
            padding: 8px 16px;
            font-family: var(--font-pixel);
            font-size: 16px;
            border: 4px solid transparent;
            cursor: pointer;
        }

        body.dark-mode .nav-tabs .nav-link {
            color: var(--nes-white);
        }

        body.dark-mode .nav-tabs .nav-link.active,
        body.dark-mode .nav-tabs .nav-link:hover {
            background-color: var(--nes-green);
            color: var(--nes-black);
            border-color: var(--nes-black);
        }

        body.light-mode .nav-tabs .nav-link {
            color: var(--nes-black);
        }

        body.light-mode .nav-tabs .nav-link.active,
        body.light-mode .nav-tabs .nav-link:hover {
            background-color: var(--nes-blue);
            color: var(--nes-white);
            border-color: var(--nes-black);
        }

        /* 面包屑 */
        .breadcrumb {
            display: flex;
            list-style: none;
            gap: 8px;
            font-family: var(--font-pixel);
            font-size: 14px;
            flex-wrap: wrap;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: '>';
            margin-right: 8px;
        }

        /* 警告框 */
        .alert {
            padding: 16px;
            border: 4px solid;
            margin-bottom: 16px;
            font-family: var(--font-sans);
        }

        .alert-info {
            background-color: var(--nes-blue);
            color: var(--nes-white);
            border-color: var(--nes-white);
        }

        .alert-warning {
            background-color: var(--nes-orange);
            color: var(--nes-black);
            border-color: var(--nes-black);
        }

        /* 分页 */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 16px;
        }

        /* 列表组 */
        .list-group {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-group-item {
            padding: 12px 16px;
            border-bottom: 2px solid;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        body.dark-mode .list-group-item {
            border-bottom-color: var(--nes-gray);
        }

        body.light-mode .list-group-item {
            border-bottom-color: var(--nes-light-gray);
        }

        /* 进度条 */
        .progress {
            height: 20px;
            border: 2px solid;
            background-color: transparent;
        }

        .progress-bar {
            height: 100%;
            background-color: var(--nes-blue);
        }

        /* 工具类 */
        .text-muted { opacity: 0.7; }
        .text-center { text-align: center; }
        .text-decoration-none { text-decoration: none; }
        .text-truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .d-flex { display: flex; }
        .justify-content-between { justify-content: space-between; }
        .justify-content-center { justify-content: center; }
        .align-items-center { align-items: center; }
        .align-items-start { align-items: flex-start; }
        .flex-column { flex-direction: column; }
        .flex-grow-1 { flex-grow: 1; }
        .flex-wrap { flex-wrap: wrap; }
        .gap-1 { gap: 8px; }
        .gap-2 { gap: 16px; }
        .gap-3 { gap: 24px; }

        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 8px; }
        .mb-2 { margin-bottom: 16px; }
        .mb-3 { margin-bottom: 24px; }
        .mb-4 { margin-bottom: 32px; }
        .mt-1 { margin-top: 8px; }
        .mt-2 { margin-top: 16px; }
        .mt-3 { margin-top: 24px; }
        .me-1 { margin-right: 8px; }
        .me-2 { margin-right: 16px; }
        .me-3 { margin-right: 24px; }
        .ms-1 { margin-left: 8px; }
        .ms-2 { margin-left: 16px; }
        .ms-3 { margin-left: 24px; }

        .p-0 { padding: 0; }
        .p-1 { padding: 8px; }
        .p-2 { padding: 16px; }
        .p-3 { padding: 24px; }
        .p-4 { padding: 32px; }
        .py-4 { padding-top: 32px; padding-bottom: 32px; }
        .py-5 { padding-top: 48px; padding-bottom: 48px; }

        .w-100 { width: 100%; }

        /* 移动端顶部导航栏压缩 */
        @media (max-width: 768px) {
            /* 顶部导航栏整体压缩 */
            header {
                padding: 8px 12px !important;
            }

            /* Logo 缩小 */
            header .site-logo {
                width: 28px !important;
                height: 28px !important;
            }

            /* 标题区域压缩 */
            header h1 {
                font-size: 14px !important;
                line-height: 1.2;
            }

            header .site-brand span {
                font-size: 10px !important;
            }

            /* 按钮压缩为图标模式 */
            header .nes-btn {
                padding: 4px 8px;
                font-size: 12px;
                border-width: 2px;
            }

            header .nes-btn .nes-icon {
                margin: 0;
            }

            /* 隐藏按钮文字，只保留图标 */
            header .nes-btn .btn-text {
                display: none;
            }

            /* 用户信息区域压缩 */
            header .d-flex.gap-1.align-center span {
                font-size: 12px;
            }

            header .nes-badge {
                display: none;
            }

            /* 减少导航栏右侧间距 */
            header .d-flex.gap-1 {
                gap: 4px !important;
            }
        }

        /* 超小屏幕进一步压缩 */
        @media (max-width: 480px) {
            header {
                padding: 6px 8px !important;
            }

            header .site-logo {
                width: 24px !important;
                height: 24px !important;
            }

            header h1 {
                font-size: 12px !important;
            }

            /* 隐藏用户名，只显示图标 */
            header .user-info-text {
                display: none;
            }
        }

        /* 移动端折叠侧边栏 */
        .menu-toggle {
            display: none;
            background: none;
            border: 4px solid;
            padding: 4px 8px;
            cursor: pointer;
            font-family: var(--font-pixel);
        }

        body.dark-mode .menu-toggle {
            color: var(--nes-white);
            border-color: var(--nes-white);
        }

        body.light-mode .menu-toggle {
            color: var(--nes-black);
            border-color: var(--nes-black);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 998;
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* 响应式 - 移动端布局调整 */
        @media (max-width: 992px) {
            .admin-wrapper {
                grid-template-columns: 1fr;
            }
            .menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 4px;
            }
            .admin-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 260px;
                z-index: 999;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                border-right: 4px solid;
                overflow-y: auto;
            }
            .admin-sidebar.active {
                transform: translateX(0);
            }
            body.dark-mode .admin-sidebar {
                background-color: var(--nes-dark-gray);
                border-right-color: var(--nes-white);
            }
            body.light-mode .admin-sidebar {
                background-color: var(--nes-white);
                border-right-color: var(--nes-black);
            }
            /* 移动端侧边栏关闭按钮 */
            .sidebar-close {
                display: flex;
                justify-content: flex-end;
                margin-bottom: 8px;
            }
            .sidebar-close-btn {
                background: none;
                border: 2px solid;
                padding: 2px 6px;
                cursor: pointer;
                font-family: var(--font-pixel);
                font-size: 12px;
            }
            body.dark-mode .sidebar-close-btn {
                color: var(--nes-white);
                border-color: var(--nes-white);
            }
            body.light-mode .sidebar-close-btn {
                color: var(--nes-black);
                border-color: var(--nes-black);
            }
        }

        /* 表格响应式 */
        .table-responsive {
            overflow-x: auto;
        }

        /* 行布局 */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -8px;
        }

        .col,
        .col-md-2,
        .col-md-3,
        .col-md-4,
        .col-md-6,
        .col-md-8,
        .col-md-9,
        .col-lg-4,
        .col-lg-8,
        .col-xl-3 {
            padding: 0 8px;
            flex: 1;
        }

        .col-md-2 { flex: 0 0 16.666%; max-width: 16.666%; }
        .col-md-3 { flex: 0 0 25%; max-width: 25%; }
        .col-md-4 { flex: 0 0 33.333%; max-width: 33.333%; }
        .col-md-6 { flex: 0 0 50%; max-width: 50%; }
        .col-md-8 { flex: 0 0 66.666%; max-width: 66.666%; }
        .col-md-9 { flex: 0 0 75%; max-width: 75%; }
        .col-lg-4 { flex: 0 0 33.333%; max-width: 33.333%; }
        .col-lg-8 { flex: 0 0 66.666%; max-width: 66.666%; }
        .col-xl-3 { flex: 0 0 25%; max-width: 25%; }

        @media (max-width: 768px) {
            .col-md-2,
            .col-md-3,
            .col-md-4,
            .col-md-6,
            .col-md-8,
            .col-md-9,
            .col-lg-4,
            .col-lg-8,
            .col-xl-3 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 16px;
            }
        }
    </style>

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
<body class="dark-mode">
    <!-- 顶部导航 -->
    <header style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-bottom: 4px solid #e67e22; padding: 16px 24px;">
        <div class="container-fluid">
            <div class="d-flex justify-between align-center">
                <a href="/admin/dashboard" class="site-brand" style="text-decoration: none;">
                    <img src="<?php echo ASSET_URL; ?>/images/BGJQ.webp" alt="邦国新闻" class="site-logo pixelated" style="width: 40px; height: 40px;">
                    <div>
                        <h1 style="font-family: 'Outfit', 'Noto Sans SC', sans-serif; font-size: clamp(0.875rem, 1.4vw, 1.125rem); color: #fff; margin: 0;"><?php echo SITE_NAME; ?></h1>
                        <span style="font-size: clamp(0.5rem, 0.8vw, 0.75rem); color: #e67e22;">后台管理系统</span>
                    </div>
                </a>

                <div class="d-flex gap-1 align-center">
                    <span class="user-info-text" style="font-size: clamp(0.875rem, 1vw, 1rem); color: #ecf0f1;">
                        <i class="nes-icon user is-small"></i>
                        <?php echo $_SESSION['admin_username'] ?? '管理员'; ?>
                        <span class="nes-badge" style="margin-left: 8px;">
                            <span class="is-primary" style="font-size: clamp(0.5rem, 0.7vw, 0.6875rem);"><?php echo $_SESSION['admin_role'] ?? '未知角色'; ?></span>
                        </span>
                    </span>
                    <a href="/" target="_blank" class="nes-btn is-success" title="前台">
                        <i class="nes-icon home is-small"></i><span class="btn-text">前台</span>
                    </a>
                    <a href="/admin/logout" class="nes-btn is-error" title="退出">
                        <i class="nes-icon close is-small"></i><span class="btn-text">退出</span>
                    </a>
                    <button type="button" class="menu-toggle" id="menuToggle" aria-label="打开菜单">
                        <i class="nes-icon list is-small"></i> 菜单
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- 侧边栏遮罩层 -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-wrapper">
        <!-- 侧边栏 -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-close">
                <button type="button" class="sidebar-close-btn" id="sidebarClose" aria-label="关闭菜单">关闭</button>
            </div>
            <nav>
                <a href="/admin/dashboard" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : ''; ?>">
                    <i class="nes-icon star is-small"></i> 仪表盘
                </a>
                <a href="/admin/articles" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/articles') !== false ? 'active' : ''; ?>">
                    <i class="nes-icon list is-small"></i> 文章管理
                </a>
                <a href="/admin/categories" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/categories') !== false ? 'active' : ''; ?>">
                    <i class="nes-icon folder is-small"></i> 栏目管理
                </a>
                <a href="/admin/contributions" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/contributions') !== false ? 'active' : ''; ?>">
                    <i class="nes-icon envelope is-small"></i> 投稿管理
                </a>
                <a href="/admin/users" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : ''; ?>">
                    <i class="nes-icon user is-small"></i> 用户管理
                </a>
                <a href="/admin/push" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/push') !== false ? 'active' : ''; ?>">
                    <i class="nes-icon upload is-small"></i> 推送管理
                </a>
                <a href="/admin/statistics" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/statistics') !== false ? 'active' : ''; ?>">
                    <i class="nes-icon chart is-small"></i> 数据统计
                </a>
                <a href="/admin/settings" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/settings') !== false ? 'active' : ''; ?>">
                    <i class="nes-icon setting is-small"></i> 系统设置
                </a>
            </nav>
        </aside>

        <!-- 主内容区域 -->
        <main class="admin-main">

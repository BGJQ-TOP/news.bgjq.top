<?php
?>
<!-- 个人中心页面 -->
<!-- 面包屑导航 -->
<nav aria-label="面包屑导航">
    <ul class="breadcrumb">
        <li><a href="/" class="nes-text is-primary">首页</a></li>
        <li class="nes-text is-disabled" aria-current="page">个人中心</li>
    </ul>
</nav>

<div class="profile-container">
    <div class="nes-container with-title">
        <h1 class="title">
            <i data-icon="user" aria-hidden="true"></i> 个人中心
        </h1>

        <!-- 欢迎信息 -->
        <div class="nes-container is-rounded profile-welcome">
            <i data-icon="user" aria-hidden="true" class="welcome-icon"></i>
            <h2 class="nes-text is-primary welcome-heading">欢迎回来</h2>
            <p class="nes-text category-description welcome-desc">管理您的投稿和账号信息</p>
        </div>

        <!-- 功能卡片 -->
        <div class="row profile-cards">
            <div class="col-4 col-sm-12">
                <div class="nes-container is-rounded profile-card">
                    <i data-icon="list" aria-hidden="true" class="card-icon"></i>
                    <h2 class="nes-text card-title">我的投稿</h2>
                    <p class="nes-text is-disabled card-desc">查看投稿历史</p>
                    <a href="#" class="nes-btn is-primary card-btn">查看</a>
                </div>
            </div>

            <div class="col-4 col-sm-12">
                <div class="nes-container is-rounded profile-card">
                    <i data-icon="setting" aria-hidden="true" class="card-icon"></i>
                    <h2 class="nes-text card-title">账号设置</h2>
                    <p class="nes-text is-disabled card-desc">修改个人信息</p>
                    <a href="#" class="nes-btn is-success card-btn">设置</a>
                </div>
            </div>

            <div class="col-4 col-sm-12">
                <div class="nes-container is-rounded profile-card">
                    <i data-icon="close" aria-hidden="true" class="card-icon"></i>
                    <h2 class="nes-text card-title">退出登录</h2>
                    <p class="nes-text is-disabled card-desc">安全退出账号</p>
                    <a href="/logout" class="nes-btn is-error card-btn">退出</a>
                </div>
            </div>
        </div>

        <!-- 返回首页 -->
        <div class="profile-actions">
            <a href="/" class="nes-btn is-primary">
                <i data-icon="home" aria-hidden="true"></i> 返回首页
            </a>
        </div>
    </div>
</div>

<?php
$pageTitle = '仪表板';
?>

<!-- 页面标题和面包屑 -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="fas fa-tachometer-alt me-2 text-primary"></i>
            系统仪表板
        </h4>
        <nav aria-label="breadcrumb" class="mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">首页</a></li>
                <li class="breadcrumb-item active">仪表板</li>
            </ol>
        </nav>
    </div>
    <div class="text-left">
        <small class="text-muted">最后更新: <?php echo date('Y-m-d H:i:s'); ?></small>
    </div>
</div>

<!-- 统计卡片 -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="admin-card stat-card border-start border-primary border-4">
            <div class="admin-card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-primary small fw-bold">文章总数</div>
                        <div class="fs-5 fw-bold"><?php echo number_format($stats['total_articles']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-newspaper stat-icon text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="admin-card-footer bg-transparent">
                <small class="text-success">
                    <i class="fas fa-caret-up"></i>
                    今日新增: <?php echo $stats['today_articles']; ?> 篇
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="admin-card stat-card border-start border-success border-4">
            <div class="admin-card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-success small fw-bold">总阅读量</div>
                        <div class="fs-5 fw-bold"><?php echo number_format($stats['total_reads']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-eye stat-icon text-success"></i>
                    </div>
                </div>
            </div>
            <div class="admin-card-footer bg-transparent">
                <small class="text-muted">平均每篇: <?php echo $stats['total_articles'] > 0 ? number_format($stats['total_reads'] / $stats['total_articles'], 1) : 0; ?> 次</small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="admin-card stat-card border-start border-warning border-4">
            <div class="admin-card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-warning small fw-bold">总点赞量</div>
                        <div class="fs-5 fw-bold"><?php echo number_format($stats['total_likes']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-heart stat-icon text-warning"></i>
                    </div>
                </div>
            </div>
            <div class="admin-card-footer bg-transparent">
                <small class="text-muted">点赞率: <?php echo $stats['total_reads'] > 0 ? number_format($stats['total_likes'] / $stats['total_reads'] * 100, 1) : 0; ?>%</small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="admin-card stat-card border-start border-danger border-4">
            <div class="admin-card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-danger small fw-bold">待审核投稿</div>
                        <div class="fs-5 fw-bold"><?php echo number_format($stats['pending_contributions']); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-edit stat-icon text-danger"></i>
                    </div>
                </div>
            </div>
            <div class="admin-card-footer bg-transparent">
                <small class="text-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    需要及时处理
                </small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- 最新文章 -->
    <div class="col-lg-8 mb-4">
        <div class="admin-card">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-clock text-primary me-2"></i>
                    最新文章
                </h5>
                <a href="/admin/articles" class="nes-btn is-primary">查看全部</a>
            </div>
            <div class="admin-card-body p-0">
                <?php if (!empty($latestArticles)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($latestArticles as $article): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="/article/<?php echo $article['article_slug']; ?>" target="_blank" class="text-decoration-none">
                                        <?php echo $article['article_title']; ?>
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i><?php echo format_time($article['article_published_at']); ?> |
                                    <i class="fas fa-eye me-1"></i><?php echo $article['article_read_count']; ?> 阅读 |
                                    <i class="fas fa-heart me-1"></i><?php echo $article['article_like_count']; ?> 点赞
                                </small>
                            </div>
                            <div class="ms-3">
                                <span class="badge bg-<?php echo !empty($article['article_is_featured']) ? 'warning' : 'secondary'; ?>">
                                    <?php echo !empty($article['article_is_featured']) ? '推荐' : '普通'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>暂无文章数据</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- 待审核投稿 -->
    <div class="col-lg-4 mb-4">
        <div class="admin-card">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-edit text-warning me-2"></i>
                    待审核投稿
                </h5>
                <a href="/admin/contributions" class="nes-btn is-warning">立即处理</a>
            </div>
            <div class="admin-card-body p-0">
                <?php if (!empty($pendingContributions['data'])): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($pendingContributions['data'] as $contribution): ?>
                    <div class="list-group-item">
                        <h6 class="mb-1 text-truncate" title="<?php echo $contribution['article_title']; ?>">
                            <?php echo truncate_string($contribution['article_title'], 30); ?>
                        </h6>
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>用户 <?php echo $contribution['article_author_id']; ?> |
                            <i class="fas fa-clock me-1"></i><?php echo format_time($contribution['article_published_at'], 'm-d H:i'); ?>
                        </small>
                        <div class="mt-2">
                            <a href="/admin/contributions/review/<?php echo $contribution['id']; ?>" class="nes-btn is-primary">审核</a>
                            <a href="/admin/preview?slug=<?php echo $contribution['article_slug']; ?>" target="_blank" class="nes-btn">预览</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                    <p>暂无待审核投稿</p>
                    <small>所有投稿都已处理完成</small>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- 快捷操作 -->
        <div class="admin-card mt-4">
            <div class="admin-card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt text-success me-2"></i>
                    快捷操作
                </h5>
            </div>
            <div class="admin-card-body">
                <div class="d-flex flex-column gap-2">
                    <?php if ($this->adminUserModel->hasPermission($_SESSION['admin_role'], 'articles', 'create')): ?>
                    <a href="/admin/articles/create" class="nes-btn is-success">
                        <i class="fas fa-plus me-2"></i>发布新文章
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($this->adminUserModel->hasPermission($_SESSION['admin_role'], 'categories', 'create')): ?>
                    <a href="/admin/categories/create" class="nes-btn is-primary">
                        <i class="fas fa-folder-plus me-2"></i>新增栏目
                    </a>
                    <?php endif; ?>
                    
                    <a href="/admin/push" class="nes-btn">
                        <i class="fas fa-paper-plane me-2"></i>推送管理
                    </a>
                    
                    <a href="/admin/statistics" class="nes-btn is-warning">
                        <i class="fas fa-chart-line me-2"></i>查看统计
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 系统信息 -->
<div class="row mt-4">
    <div class="col-12">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle text-info me-2"></i>
                    系统信息
                </h5>
            </div>
            <div class="admin-card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>服务器时间:</strong> <?php echo date('Y-m-d H:i:s'); ?>
                    </div>
                    <div class="col-md-3">
                        <strong>PHP版本:</strong> <?php echo PHP_VERSION; ?>
                    </div>
                    <div class="col-md-3">
                        <strong>数据库:</strong> MariaDB
                    </div>
                    <div class="col-md-3">
                        <strong>内存使用:</strong> <?php echo round(memory_get_usage(true) / 1024 / 1024, 2); ?> MB
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 页面加载完成后执行
document.addEventListener('DOMContentLoaded', function() {
    // 自动刷新统计数据（每5分钟）
    setInterval(function() {
        // 这里可以添加AJAX请求来更新统计数据
        console.log('统计数据已刷新');
    }, 5 * 60 * 1000);
    
    // 卡片悬停效果
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // 实时时钟
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleString('zh-CN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        
        const clockElement = document.querySelector('.text-muted small');
        if (clockElement) {
            clockElement.textContent = '最后更新: ' + timeString;
        }
    }
    
    setInterval(updateClock, 1000);
    updateClock();
});
</script>

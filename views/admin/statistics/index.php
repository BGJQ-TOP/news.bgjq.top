<?php
$pageTitle = '数据统计';
?>

<!-- 页面标题 -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="fas fa-chart-line me-2 text-primary"></i>
            数据统计
        </h4>
        <nav aria-label="breadcrumb" class="mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">首页</a></li>
                <li class="breadcrumb-item active">数据统计</li>
            </ol>
        </nav>
    </div>
</div>

<!-- 统计卡片 -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="admin-card" style="border-color: var(--nes-blue);">
            <div class="admin-card-body text-center">
                <h3 class="mb-0" style="color: var(--nes-blue);"><?php echo $totalArticles; ?></h3>
                <small>文章总数</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="admin-card" style="border-color: var(--nes-green);">
            <div class="admin-card-body text-center">
                <h3 class="mb-0" style="color: var(--nes-green);"><?php echo $publishedArticles; ?></h3>
                <small>已发布</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="admin-card" style="border-color: var(--nes-orange);">
            <div class="admin-card-body text-center">
                <h3 class="mb-0" style="color: var(--nes-orange);"><?php echo $pendingArticles; ?></h3>
                <small>待审核</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card" style="border-color: var(--nes-purple);">
            <div class="admin-card-body text-center">
                <h3 class="mb-0" style="color: var(--nes-purple);"><?php echo $totalCategories; ?></h3>
                <small>栏目数</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card" style="border-color: var(--nes-red);">
            <div class="admin-card-body text-center">
                <h3 class="mb-0" style="color: var(--nes-red);"><?php echo $totalUsers; ?></h3>
                <small>用户数</small>
            </div>
        </div>
    </div>
</div>

<!-- 图表和列表 -->
<div class="row">
    <!-- 发布趋势 -->
    <div class="col-md-8 mb-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="mb-0">最近 7 天发布趋势</h5>
            </div>
            <div class="admin-card-body">
                <?php if (!empty($trendData)): ?>
                <canvas id="trendChart" height="100"></canvas>
                <?php else: ?>
                <p class="text-muted text-center py-4">暂无数据</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- 热门文章 -->
    <div class="col-md-4 mb-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="mb-0">热门文章</h5>
            </div>
            <div class="admin-card-body p-0">
                <?php if (!empty($popularArticles)): ?>
                <ul class="list-group">
                    <?php foreach ($popularArticles as $index => $article): ?>
                    <li class="list-group-item">
                        <div>
                            <span class="badge bg-<?php echo $index < 3 ? 'danger' : 'secondary'; ?> me-2">
                                <?php echo $index + 1; ?>
                            </span>
                            <a href="/article/<?php echo $article['article_slug']; ?>" target="_blank">
                                <?php echo htmlspecialchars($article['article_title']); ?>
                            </a>
                        </div>
                        <span class="badge bg-primary"><?php echo date('m-d', strtotime($article['article_created_at'])); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="text-muted text-center py-4">暂无数据</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- 栏目分布 -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="mb-0">栏目分布</h5>
            </div>
            <div class="admin-card-body">
                <?php if (!empty($categoryDistribution)): ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>栏目名称</th>
                                <th>文章数量</th>
                                <th>占比</th>
                                <th>分布图</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total = array_sum(array_column($categoryDistribution, 'article_count'));
                            foreach ($categoryDistribution as $category): 
                                $percentage = $total > 0 ? round(($category['article_count'] / $total) * 100, 2) : 0;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                <td><?php echo $category['article_count']; ?></td>
                                <td><?php echo $percentage; ?>%</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?php echo $percentage; ?>%;" 
                                             aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center py-4">暂无数据</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($trendData)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trendChart').getContext('2d');
    
    const data = {
        labels: [<?php echo implode(',', array_map(function($item) {
            return "'" . date('m-d', strtotime($item['date'])) . "'";
        }, $trendData)); ?>],
        datasets: [{
            label: '发布文章数',
            data: [<?php echo implode(',', array_column($trendData, 'count')); ?>],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1,
            fill: true
        }]
    };
    
    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    };
    
    new Chart(ctx, config);
});
</script>
<?php endif; ?>

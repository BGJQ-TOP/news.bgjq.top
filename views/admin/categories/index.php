<?php
$pageTitle = '栏目管理';
?>

<!-- 页面标题和操作按钮 -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="fas fa-folder me-2 text-primary"></i>
            栏目管理
        </h4>
        <nav aria-label="breadcrumb" class="mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">首页</a></li>
                <li class="breadcrumb-item active">栏目管理</li>
            </ol>
        </nav>
    </div>
    <div>
        <?php if ($this->adminUserModel->hasPermission($_SESSION['admin_role'], 'categories', 'create')): ?>
        <a href="/admin/categories/create" class="nes-btn is-success">
            <i class="fas fa-plus me-2"></i>添加新栏目
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- 栏目列表 -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="mb-0">栏目列表</h5>
    </div>
    
    <div class="admin-card-body p-0">
        <?php if (!empty($categories)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="20%">栏目名称</th>
                        <th width="15%">栏目编码</th>
                        <th width="15%">栏目别名</th>
                        <th width="10%">排序</th>
                        <th width="10%">状态</th>
                        <th width="10%">文章数</th>
                        <th width="10%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo $category['id']; ?></td>
                        <td>
                            <i class="fas fa-folder text-warning me-2"></i>
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </td>
                        <td><code><?php echo htmlspecialchars($category['category_code']); ?></code></td>
                        <td><?php echo htmlspecialchars($category['category_slug']); ?></td>
                        <td><?php echo $category['category_sort_order']; ?></td>
                        <td>
                            <?php if ($category['category_is_active']): ?>
                                <span class="badge bg-success">启用</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">禁用</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-primary">0</span>
                        </td>
                        <td>
                            <?php if ($this->adminUserModel->hasPermission($_SESSION['admin_role'], 'categories', 'edit')): ?>
                            <a href="/admin/categories/edit?id=<?php echo $category['id']; ?>" class="nes-btn is-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($this->adminUserModel->hasPermission($_SESSION['admin_role'], 'categories', 'delete')): ?>
                            <a href="/admin/categories/delete?id=<?php echo $category['id']; ?>" 
                               class="nes-btn is-error"
                               onclick="return confirm('确定要删除这个栏目吗？')">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-folder-open text-muted" style="font-size: 4rem;"></i>
            <p class="text-muted mt-3">暂无栏目</p>
        </div>
        <?php endif; ?>
    </div>
</div>

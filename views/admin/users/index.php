<?php
$pageTitle = '用户管理';
?>

<!-- 页面标题 -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="fas fa-users me-2 text-primary"></i>
            用户管理
        </h4>
        <nav aria-label="breadcrumb" class="mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">首页</a></li>
                <li class="breadcrumb-item active">用户管理</li>
            </ol>
        </nav>
    </div>
</div>

<!-- 用户列表 -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="mb-0">用户列表</h5>
    </div>
    
    <div class="admin-card-body p-0">
        <?php if (!empty($users)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="15%">用户名</th>
                        <th width="15%">游戏 ID</th>
                        <th width="15%">角色</th>
                        <th width="15%">所属国家</th>
                        <th width="15%">注册时间</th>
                        <th width="10%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($user['game_id']); ?>
                        </td>
                        <td>
                            <?php if ($user['role'] === 'secretary_general'): ?>
                                <span class="badge bg-danger" title="秘书长">
                                    <i class="fas fa-star me-1"></i>秘书长
                                </span>
                            <?php elseif ($user['role'] === 'permanent_member'): ?>
                                <span class="badge bg-primary" title="常任理事国">
                                    常任理事国
                                </span>
                            <?php elseif ($user['role'] === 'diplomat'): ?>
                                <span class="badge bg-info" title="外交官">
                                    外交官
                                </span>
                            <?php elseif ($user['role'] === 'observer'): ?>
                                <span class="badge bg-secondary" title="观察员">
                                    观察员
                                </span>
                            <?php elseif ($user['role'] === 'peacekeeper'): ?>
                                <span class="badge bg-success" title="维和人员">
                                    维和人员
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?php echo $user['role']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($user['country_name'])): ?>
                                <i class="fas fa-flag me-1 text-muted"></i>
                                <?php echo htmlspecialchars($user['country_name']); ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                        <td>
                            <button type="button" class="nes-btn is-primary" onclick="viewUser(<?php echo $user['id']; ?>)">
                                <i class="fas fa-eye"></i> 查看
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-users text-muted" style="font-size: 4rem;"></i>
            <p class="text-muted mt-3">暂无用户</p>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="admin-card-footer">
        <small class="text-muted">
            <i class="fas fa-info-circle me-1"></i>
            秘书长拥有管理员权限，可以访问所有后台功能。其他用户根据角色拥有不同的投稿权限。
        </small>
    </div>
</div>

<script>
function viewUser(id) {
    alert('用户详情功能开发中... (用户 ID: ' + id + ')');
}
</script>

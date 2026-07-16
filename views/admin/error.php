<?php
$pageTitle = '错误';
?>

<div class="text-center py-5">
    <i class="fas fa-exclamation-triangle text-muted" style="font-size: 4rem;"></i>
    <h4 class="mt-3 text-muted">出错了</h4>
    <p class="text-muted"><?php echo isset($message) ? htmlspecialchars($message) : '发生未知错误'; ?></p>
    <a href="/admin/dashboard" class="nes-btn is-primary mt-3">
        <i class="fas fa-home me-2"></i>返回首页
    </a>
</div>

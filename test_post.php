<?php
/**
 * 测试 POST 请求
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>POST 请求测试</h1>";
echo "<hr>";

echo "<h2>请求方法：</h2>";
echo "<p><strong>$_SERVER[REQUEST_METHOD]</strong></p>";

echo "<h2>POST 数据：</h2>";
if (!empty($_POST)) {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
} else {
    echo "<p>没有 POST 数据</p>";
}

echo "<h2>测试表单：</h2>";
?>
<form method="post" action="">
    <div>
        <label>用户名：<input type="text" name="username"></label>
    </div>
    <div>
        <label>密码：<input type="password" name="password"></label>
    </div>
    <div>
        <button type="submit" name="action" value="login">登录</button>
    </div>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<hr>";
    echo "<h2 style='color: green;'>表单提交成功！</h2>";
    echo "<p>用户名：" . htmlspecialchars($_POST['username'] ?? '') . "</p>";
    echo "<p>密码：" . htmlspecialchars($_POST['password'] ?? '') . "</p>";
    echo "<p>Action: " . htmlspecialchars($_POST['action'] ?? '') . "</p>";
}
?>

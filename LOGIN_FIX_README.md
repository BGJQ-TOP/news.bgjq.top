# 登录功能修复说明

## 修复内容

本次修复实现了使用 `sql/bgjq.sql` 数据库中的 `users` 表用户数据进行登录，具体改动如下：

### 1. 修改 `AdminUserModel.php`
- 添加了 `getUserByUsername()` 方法，用于从 `users` 表获取用户信息
- 在 `getRolePermissions()` 方法中添加了所有用户角色的权限配置：
  - `secretary_general` (秘书长): 拥有 `super_admin` 权限，可以访问所有后台管理功能
  - `permanent_member` (常任理事国): 可以创建、编辑文章，审核投稿
  - `diplomat` (外交官): 可以创建、编辑文章，查看投稿
  - `observer` (观察员): 只能查看文章和投稿
  - `peacekeeper` (维和人员): 可以创建、编辑文章，查看投稿

### 2. 修改 `AdminAuthController.php`
- 启用了 `AdminUserModel` 实例化
- 重写了 `authenticateUser()` 方法：
  - 从数据库 `users` 表验证用户名和密码
  - 秘书长角色 (`secretary_general`) 被授予 `super_admin` 权限
  - 其他角色保留原有角色信息，可以投稿
- 更新了登录表单提示信息

### 3. 修改 `AdminController.php`
- 改进了权限检查逻辑，使用变量存储角色，避免直接访问 SESSION

### 4. 修改 `AdminArticleController.php`
- 统一改进了所有权限检查逻辑，确保秘书长可以正常管理文章

## 用户角色说明

根据数据库 `users` 表中的 `role` 字段：

| 角色 | 权限级别 | 说明 |
|------|---------|------|
| `secretary_general` | 管理员 (super_admin) | 秘书长，拥有完整的后台管理权限 |
| `permanent_member` | 高级用户 | 常任理事国，可以投稿和审核 |
| `diplomat` | 中级用户 | 外交官，可以投稿 |
| `observer` | 普通用户 | 观察员，只能查看内容 |
| `peacekeeper` | 中级用户 | 维和人员，可以投稿 |

## 登录方式

1. 访问 `/admin/login` 页面
2. 使用 `users` 表中的用户名和密码登录
3. 秘书长账号将拥有完整的管理员权限
4. 其他用户可以投稿（需要相应的投稿权限）

## 测试方法

1. 访问 `/test_login.php` 运行测试脚本
2. 测试脚本会显示：
   - 数据库连接状态
   - 秘书长用户列表
   - 用户角色分布
   - 权限配置测试

3. 使用秘书长账号登录后台进行测试：
   - 访问 `/admin/login`
   - 输入秘书长用户名和密码
   - 验证是否可以访问所有后台功能

## 数据库用户示例

根据 `sql/bgjq.sql`，秘书长用户示例：
- 用户名：`LouieMAIN`
- 角色：`secretary_general`
- 国家 ID: 1 (养老院)

## 注意事项

1. 密码使用 bcrypt 加密，存储在 `users.password` 字段
2. 秘书长角色在登录后会被映射为 `super_admin`
3. 其他角色保留原始角色名称，但拥有相应的投稿权限
4. 会话超时时间为 2 小时（`SESSION_TIMEOUT`）

## 相关文件

- `models/AdminUserModel.php` - 管理员模型
- `controllers/AdminAuthController.php` - 认证控制器
- `controllers/AdminController.php` - 后台主控制器
- `controllers/AdminArticleController.php` - 文章管理控制器
- `config/config.php` - 配置文件
- `core/functions.php` - 核心函数（包含密码验证函数）

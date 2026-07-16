# 邦国崛起新闻系统

## 项目概述

邦国崛起新闻系统是一个基于PHP开发的现代化新闻内容管理系统，专为游戏资讯、官方公告、玩家投稿等场景设计。系统采用MVC架构，支持多子站内容推送、SEO优化、IndexNow自动提交等高级功能。

## 技术栈

- **后端**: PHP 8.1+
- **前端**: HTML5 + CSS3 + JavaScript (Bootstrap 5)
- **数据库**: MariaDB 10.6+
- **服务器**: Nginx 1.18+
- **部署环境**: Linux (Ubuntu 20.04+)

## 系统特性

### 核心功能
- 📰 完整的新闻发布和管理系统
- 📱 响应式设计，支持多终端访问
- 🔍 内置SEO优化功能
- 🔒 基于角色的权限管理系统
- 📊 数据统计和分析功能

### 特色功能
- 🌐 **多子站内容推送API** - 支持第三方子站自动推送内容
- 🔄 **IndexNow自动提交** - 自动向搜索引擎提交新内容
- 📈 **实时数据统计** - 阅读量、点赞数等实时统计
- 🛡️ **安全防护** - XSS过滤、SQL注入防护、CSRF保护

### 管理功能
- 👥 用户和权限管理
- 📂 栏目分类管理
- ✍️ 文章编辑和审核
- 📨 投稿审核系统
- 📊 数据统计分析

## 项目结构

```
news.bgjq.top/
├── api/                    # API接口目录
│   └── v1/
│       └── push.php       # 子站内容推送API
├── assets/                # 静态资源
│   ├── css/               # 样式文件
│   ├── js/                # JavaScript文件
│   └── images/            # 图片资源
├── config/                # 配置文件
│   ├── config.php         # 系统配置
│   └── database.php       # 数据库配置
├── controllers/           # 控制器
│   ├── AdminController.php # 后台控制器
│   ├── ArticleController.php # 文章控制器
│   └── ...
├── core/                  # 核心文件
│   ├── BaseModel.php      # 基础模型
│   ├── functions.php      # 核心函数
│   └── IndexNowService.php # IndexNow服务
├── deploy/                # 部署文件
│   ├── deploy.sh          # 自动部署脚本
│   └── nginx.conf         # Nginx配置
├── models/                # 数据模型
│   ├── ArticleModel.php   # 文章模型
│   ├── CategoryModel.php  # 栏目模型
│   └── ...
├── sql/                   # 数据库文件
│   ├── bgjq.sql           # 基础数据库
│   └── news_system_upgrade.sql # 系统升级脚本
├── tests/                 # 测试文件
│   └── SystemTest.php     # 系统测试
├── views/                 # 视图文件
│   ├── admin/             # 后台视图
│   ├── layouts/           # 布局文件
│   └── ...
├── index.php              # 入口文件
└── README.md              # 项目文档
```

## 快速开始

### 环境要求

- PHP 8.1+
- MariaDB 10.6+
- Nginx 1.18+
- Linux/Unix 系统

### 自动部署

1. **克隆项目**
```bash
git clone <repository-url>
cd news.bgjq.top
```

2. **运行部署脚本**
```bash
chmod +x deploy/deploy.sh
./deploy/deploy.sh
```

3. **访问系统**
- 前台地址: `https://news.bgjq.top`
- 后台地址: `https://news.bgjq.top/admin`
- 默认管理员: `admin` / `admin123`

### 手动部署

1. **配置数据库**
```sql
-- 导入基础数据库
mysql -u root -p < sql/bgjq.sql

-- 导入系统升级脚本
mysql -u root -p < sql/news_system_upgrade.sql
```

2. **配置Nginx**
```bash
# 复制Nginx配置
sudo cp deploy/nginx.conf /etc/nginx/sites-available/news.bgjq.top

# 启用网站
sudo ln -s /etc/nginx/sites-available/news.bgjq.top /etc/nginx/sites-enabled/

# 测试配置
sudo nginx -t

# 重启Nginx
sudo systemctl restart nginx
```

3. **配置环境变量**
复制 `config/env.example.php` 为 `config/env.php` 并修改相应配置。

## API文档

### 子站内容推送API

**端点**: `POST /api/v1/push`

**请求头**:
```
AppID: <子站AppID>
Sign: <请求签名>
Content-Type: application/json
```

**请求体**:
```json
{
    "title": "文章标题",
    "category_code": "official_notice",
    "content": "文章内容（至少200字符）",
    "timestamp": 1640995200,
    "nonce_str": "随机字符串",
    "cover_image": "封面图URL（可选）",
    "seo_title": "SEO标题（可选）",
    "seo_keywords": "SEO关键词（可选）",
    "seo_description": "SEO描述（可选）",
    "author": "作者（可选）",
    "source_url": "原文链接（可选）"
}
```

**响应**:
```json
{
    "code": 200,
    "msg": "推送成功",
    "data": {
        "article_id": 123,
        "push_time": "2024-01-01 12:00:00"
    }
}
```

### 支持的栏目编码

- `official_notice` - 官方公告
- `un_dynamic` - 联合国动态
- `server_news` - 服务器新闻
- `newbie_guide` - 新手攻略
- `country_story` - 国家故事
- `player_contribution` - 玩家投稿

## 系统配置

### 主要配置项

```php
// 数据库配置
define('DB_HOST', 'localhost');
define('DB_NAME', 'bgjq_news');
define('DB_USER', 'bgjq_news_user');
define('DB_PASS', 'your_password');

// 系统配置
define('SITE_URL', 'https://news.bgjq.top');
define('SITE_NAME', '邦国崛起新闻系统');
define('DEBUG_MODE', false);

// IndexNow配置
define('INDEXNOW_ENABLED', true);
define('INDEXNOW_API_KEY', 'your_indexnow_key');

// 安全配置
define('SESSION_TIMEOUT', 3600);
```

## 权限系统

### 用户角色

- **超级管理员** - 拥有所有权限
- **内容编辑** - 文章管理、栏目管理
- **投稿审核** - 审核用户投稿
- **数据查看** - 查看统计数据

### 权限模块

- 文章管理 (`articles`)
- 栏目管理 (`categories`) 
- 用户管理 (`users`)
- 投稿审核 (`contributions`)
- 推送管理 (`push`)
- 数据统计 (`statistics`)
- 系统设置 (`settings`)

## 开发指南

### 添加新功能

1. **创建数据模型**
```php
class NewModel extends BaseModel {
    protected $table = 'new_table';
    
    public function customMethod() {
        // 业务逻辑
    }
}
```

2. **创建控制器**
```php
class NewController {
    public function action() {
        // 控制器逻辑
        $this->render('view_name', $data);
    }
}
```

3. **创建视图**
```php
<!-- views/new/view_name.php -->
<div class="container">
    <h1><?php echo $data['title']; ?></h1>
</div>
```

### 扩展API

1. **创建API端点**
```php
// api/v1/new_endpoint.php
header('Content-Type: application/json');
// API逻辑
```

2. **更新路由**
```php
// index.php
$router->add('/api/v1/new_endpoint', 'NewApiController');
```

## 测试

### 运行系统测试

```bash
cd /var/www/news.bgjq.top
php tests/SystemTest.php
```

### 测试内容

- ✅ 数据库连接测试
- ✅ 核心函数测试
- ✅ 数据模型测试
- ✅ 控制器测试
- ✅ API接口测试
- ✅ 安全特性测试
- ✅ 性能测试

## 维护和监控

### 日志文件

- Nginx访问日志: `/var/log/nginx/news.bgjq.top.access.log`
- Nginx错误日志: `/var/log/nginx/news.bgjq.top.error.log`
- PHP错误日志: `/var/log/php/error.log`
- 系统日志: `/var/www/news.bgjq.top/logs/`

### 监控指标

- 网站访问量
- API调用频率
- 数据库性能
- 系统资源使用

## 故障排除

### 常见问题

1. **数据库连接失败**
   - 检查数据库服务状态
   - 验证数据库配置
   - 检查防火墙设置

2. **权限错误**
   - 检查文件权限
   - 验证用户角色配置
   - 检查会话设置

3. **API调用失败**
   - 验证签名算法
   - 检查时间戳有效性
   - 确认子站配置状态

### 技术支持

如有问题，请检查：
1. 系统日志文件
2. 错误信息提示
3. 网络连接状态

## 版本历史

### v1.0.0 (2024-01-01)
- ✅ 基础新闻系统功能
- ✅ 多子站内容推送API
- ✅ IndexNow自动提交
- ✅ 完整的后台管理系统
- ✅ 响应式前端设计
- ✅ 系统安全防护
- ✅ 自动化部署脚本

## 许可证

本项目采用MIT许可证。详情请参阅LICENSE文件。

## 贡献指南

欢迎提交Issue和Pull Request来改进这个项目。

## 联系方式

- 项目主页: https://news.bgjq.top
- 问题反馈: 创建GitHub Issue
- 技术支持: 系统管理员

---

**邦国崛起新闻系统** - 专业的游戏资讯内容管理平台
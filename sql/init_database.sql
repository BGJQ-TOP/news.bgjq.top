-- ============================================================
-- news.bgjq.top 新闻系统 - 数据库初始化脚本
-- 从零开始：建库、建用户、建表、插入默认数据
-- 执行方式：mysql -u root -p < init_database.sql
-- ============================================================

-- 1. 创建数据库
CREATE DATABASE IF NOT EXISTS `bgjq`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

-- 2. 创建数据库用户并授权
--    注意：修改密码为你的 .env 中的 DB_PASS
CREATE USER IF NOT EXISTS 'bgjq'@'localhost' IDENTIFIED BY 'BGJQ1314!';
GRANT ALL PRIVILEGES ON `bgjq`.* TO 'bgjq'@'localhost';
FLUSH PRIVILEGES;

USE `bgjq`;

-- ============================================================
-- 3. 建表
-- ============================================================

-- 3.1 邦国表（用户所属邦国）
CREATE TABLE IF NOT EXISTS `countries` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `flag_url` VARCHAR(255) DEFAULT NULL,
    `declaration` TEXT DEFAULT NULL,
    `government_type` ENUM('monarchy','democracy','guild','other') DEFAULT 'other',
    `population` INT(11) DEFAULT 0,
    `territory_chunks` INT(11) DEFAULT 0,
    `joined_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `is_active` TINYINT(1) DEFAULT 1,
    `description` TEXT DEFAULT NULL COMMENT '邦国简介',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='邦国表';

-- 3.2 用户表（社区用户，用于前台登录和后台管理员认证）
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL COMMENT 'bcrypt 密码哈希',
    `game_id` VARCHAR(100) NOT NULL,
    `country_id` INT(11) DEFAULT NULL,
    `role` ENUM('secretary_general','permanent_member','diplomat','observer','peacekeeper') DEFAULT 'observer',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `jhtuid` TEXT NOT NULL COMMENT '简幻通UID',
    `level` TEXT NOT NULL COMMENT '简幻通验证等级',
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_username` (`username`),
    KEY `idx_country_id` (`country_id`),
    KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';

-- 3.3 新闻栏目分类表
CREATE TABLE IF NOT EXISTS `news_categories` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `category_name` VARCHAR(50) NOT NULL COMMENT '栏目名称',
    `category_code` VARCHAR(50) NOT NULL COMMENT '栏目编码',
    `category_slug` VARCHAR(100) NOT NULL COMMENT 'URL 别名',
    `category_description` TEXT DEFAULT NULL COMMENT '栏目描述',
    `category_seo_title` VARCHAR(200) DEFAULT NULL COMMENT 'SEO 标题',
    `category_seo_keywords` VARCHAR(300) DEFAULT NULL COMMENT 'SEO 关键词',
    `category_seo_description` TEXT DEFAULT NULL COMMENT 'SEO 描述',
    `category_icon` VARCHAR(100) DEFAULT NULL COMMENT '栏目图标',
    `category_color` VARCHAR(20) DEFAULT NULL COMMENT '栏目颜色',
    `category_sort_order` INT(11) DEFAULT 0 COMMENT '排序',
    `category_article_count` INT(11) DEFAULT 0 COMMENT '文章数量',
    `category_is_active` TINYINT(1) DEFAULT 1 COMMENT '是否启用',
    `category_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `category_updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_category_code` (`category_code`),
    UNIQUE KEY `unique_category_slug` (`category_slug`),
    KEY `idx_category_sort_order` (`category_sort_order`),
    KEY `idx_category_is_active` (`category_is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='新闻栏目分类表';

-- 3.4 新闻文章主表
CREATE TABLE IF NOT EXISTS `news_articles` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `article_title` VARCHAR(200) NOT NULL COMMENT '文章标题',
    `article_content` LONGTEXT NOT NULL COMMENT '文章内容',
    `article_excerpt` TEXT DEFAULT NULL COMMENT '文章摘要',
    `article_category_id` INT(11) NOT NULL COMMENT '栏目 ID',
    `article_slug` VARCHAR(200) NOT NULL COMMENT 'URL 别名',
    `article_cover_image` VARCHAR(255) DEFAULT NULL COMMENT '封面图 URL',
    `article_seo_title` VARCHAR(200) DEFAULT NULL COMMENT 'SEO 标题',
    `article_seo_keywords` VARCHAR(300) DEFAULT NULL COMMENT 'SEO 关键词',
    `article_seo_description` TEXT DEFAULT NULL COMMENT 'SEO 描述',
    `article_read_count` INT(11) DEFAULT 0 COMMENT '阅读量',
    `article_like_count` INT(11) DEFAULT 0 COMMENT '点赞量',
    `article_comment_count` INT(11) DEFAULT 0 COMMENT '评论数',
    `article_status` ENUM('draft','pending','published','rejected') DEFAULT 'draft' COMMENT '文章状态',
    `article_is_featured` TINYINT(1) DEFAULT 0 COMMENT '是否推荐',
    `article_is_top` TINYINT(1) DEFAULT 0 COMMENT '是否置顶',
    `article_is_headline` TINYINT(1) DEFAULT 0 COMMENT '是否头条',
    `article_source_type` ENUM('admin','user_contribution','subsite_push','diplomat_announcement') DEFAULT 'admin' COMMENT '来源类型',
    `article_source_url` VARCHAR(255) DEFAULT NULL COMMENT '来源链接',
    `article_author_id` INT(11) DEFAULT NULL COMMENT '作者 ID',
    `article_author_name` VARCHAR(100) DEFAULT NULL COMMENT '作者名称',
    `article_reject_reason` TEXT DEFAULT NULL COMMENT '驳回原因',
    `article_published_at` TIMESTAMP NULL DEFAULT NULL COMMENT '发布时间',
    `article_updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `article_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `article_indexnow_submitted` TINYINT(1) DEFAULT 0 COMMENT 'IndexNow 是否已提交',
    `article_indexnow_submit_time` TIMESTAMP NULL DEFAULT NULL COMMENT 'IndexNow 提交时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_article_slug` (`article_slug`),
    KEY `idx_article_category_id` (`article_category_id`),
    KEY `idx_article_status` (`article_status`),
    KEY `idx_article_is_featured` (`article_is_featured`),
    KEY `idx_article_is_top` (`article_is_top`),
    KEY `idx_article_published_at` (`article_published_at`),
    KEY `idx_article_created_at` (`article_created_at`),
    KEY `idx_article_author_id` (`article_author_id`),
    FULLTEXT KEY `ft_article_title_content` (`article_title`, `article_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='新闻文章主表';

-- 3.5 新闻标签表
CREATE TABLE IF NOT EXISTS `news_tags` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tag_name` VARCHAR(50) NOT NULL COMMENT '标签名称',
    `tag_slug` VARCHAR(100) NOT NULL COMMENT 'URL 别名',
    `tag_description` TEXT DEFAULT NULL COMMENT '标签描述',
    `tag_usage_count` INT(11) DEFAULT 0 COMMENT '使用次数',
    `tag_is_hot` TINYINT(1) DEFAULT 0 COMMENT '是否热门',
    `tag_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `tag_updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_tag_name` (`tag_name`),
    UNIQUE KEY `unique_tag_slug` (`tag_slug`),
    KEY `idx_tag_usage_count` (`tag_usage_count`),
    KEY `idx_tag_is_hot` (`tag_is_hot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='新闻标签表';

-- 3.6 文章标签关联表
CREATE TABLE IF NOT EXISTS `news_article_tags` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `article_id` INT(11) NOT NULL,
    `tag_id` INT(11) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_article_id` (`article_id`),
    KEY `idx_tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='新闻文章标签关联表';

-- 3.7 点赞记录表
CREATE TABLE IF NOT EXISTS `news_likes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `article_id` INT(11) NOT NULL,
    `user_id` INT(11) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_article_user` (`article_id`, `user_id`),
    KEY `idx_article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='新闻点赞记录表';

-- 3.8 评论表
CREATE TABLE IF NOT EXISTS `news_comments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `article_id` INT(11) NOT NULL,
    `user_id` INT(11) NOT NULL,
    `parent_comment_id` INT(11) DEFAULT NULL,
    `comment_content` TEXT NOT NULL,
    `comment_status` ENUM('pending','approved','rejected') DEFAULT 'approved',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_article_id` (`article_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_parent_comment_id` (`parent_comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='新闻评论表';

-- 3.9 后台管理员表（独立于 users 表，用于后台管理系统）
CREATE TABLE IF NOT EXISTS `news_admin_users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL COMMENT '加盐 bcrypt 哈希',
    `email` VARCHAR(100) DEFAULT NULL,
    `role` ENUM('super_admin','admin','editor') DEFAULT 'editor',
    `permissions` TEXT DEFAULT NULL COMMENT '权限配置（JSON）',
    `is_active` TINYINT(1) DEFAULT 1,
    `last_login_at` TIMESTAMP NULL DEFAULT NULL,
    `last_login_ip` VARCHAR(50) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='后台管理员用户表';

-- 3.10 子站点配置表（API 推送接入）
CREATE TABLE IF NOT EXISTS `news_subsite_configs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `subsite_name` VARCHAR(100) NOT NULL COMMENT '子站点名称',
    `app_id` VARCHAR(50) NOT NULL COMMENT 'AppID',
    `app_secret` VARCHAR(100) NOT NULL COMMENT 'AppSecret',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '是否启用',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_app_id` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='子站点配置表';

-- 3.11 推送日志表（匹配 push.php 实际使用字段）
CREATE TABLE IF NOT EXISTS `news_push_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `subsite_app_id` VARCHAR(50) NOT NULL COMMENT '子站 AppID',
    `article_id` INT(11) DEFAULT NULL COMMENT '文章 ID',
    `title` VARCHAR(200) DEFAULT NULL COMMENT '推送标题',
    `category_code` VARCHAR(50) DEFAULT NULL COMMENT '栏目编码',
    `push_status` ENUM('pending','success','failed') DEFAULT 'pending' COMMENT '推送状态',
    `push_data` TEXT DEFAULT NULL COMMENT '推送原始数据（JSON）',
    `error_message` TEXT DEFAULT NULL COMMENT '错误信息',
    `ip_address` VARCHAR(50) DEFAULT NULL COMMENT '请求 IP',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_subsite_app_id` (`subsite_app_id`),
    KEY `idx_article_id` (`article_id`),
    KEY `idx_push_status` (`push_status`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='推送日志表';

-- 3.12 IndexNow 提交日志表（匹配 IndexNowService.php 实际使用字段）
CREATE TABLE IF NOT EXISTS `news_indexnow_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `url` VARCHAR(255) NOT NULL COMMENT '提交的 URL',
    `submit_type` VARCHAR(50) DEFAULT 'article_publish' COMMENT '提交类型',
    `submit_status` ENUM('pending','success','failed') DEFAULT 'pending' COMMENT '提交状态',
    `error_message` TEXT DEFAULT NULL COMMENT '错误信息',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_url` (`url`),
    KEY `idx_submit_status` (`submit_status`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='IndexNow 日志表';

-- 3.13 操作日志表（匹配 functions.php 实际使用字段）
CREATE TABLE IF NOT EXISTS `news_operation_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `admin_user_id` INT(11) DEFAULT NULL COMMENT '管理员用户 ID',
    `operation_type` VARCHAR(100) NOT NULL COMMENT '操作类型',
    `operation_target` VARCHAR(255) DEFAULT NULL COMMENT '操作目标',
    `operation_detail` TEXT DEFAULT NULL COMMENT '操作详情',
    `ip_address` VARCHAR(50) DEFAULT NULL COMMENT 'IP 地址',
    `user_agent` VARCHAR(255) DEFAULT NULL COMMENT '用户代理',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_admin_user_id` (`admin_user_id`),
    KEY `idx_operation_type` (`operation_type`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='操作日志表';

-- 3.14 轮播图表
CREATE TABLE IF NOT EXISTS `news_carousels` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `carousel_title` VARCHAR(200) DEFAULT NULL COMMENT '轮播图标题',
    `carousel_image` VARCHAR(255) NOT NULL COMMENT '轮播图 URL',
    `carousel_link` VARCHAR(255) DEFAULT NULL COMMENT '跳转链接',
    `carousel_sort_order` INT(11) DEFAULT 0 COMMENT '排序',
    `carousel_is_active` TINYINT(1) DEFAULT 1 COMMENT '是否启用',
    `carousel_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `carousel_updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_carousel_sort_order` (`carousel_sort_order`),
    KEY `idx_carousel_is_active` (`carousel_is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='轮播图表';

-- 3.15 搜索记录表
CREATE TABLE IF NOT EXISTS `search_records` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `search_keyword` VARCHAR(200) NOT NULL COMMENT '搜索关键词',
    `search_count` INT(11) DEFAULT 1 COMMENT '搜索次数',
    `last_searched_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后搜索时间',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_keyword` (`search_keyword`),
    KEY `idx_search_count` (`search_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='搜索记录表';

-- 3.16 权限授权表
CREATE TABLE IF NOT EXISTS `news_permissions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL COMMENT '被授权用户ID',
    `module` VARCHAR(50) NOT NULL COMMENT '模块名称',
    `action` VARCHAR(50) NOT NULL COMMENT '操作',
    `granted_by` INT UNSIGNED NOT NULL COMMENT '授权者用户ID',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_module_action` (`module`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户额外权限授权表';

-- ============================================================
-- 4. 插入默认数据
-- ============================================================

-- 4.1 默认栏目
INSERT INTO `news_categories` (`category_name`, `category_code`, `category_slug`, `category_description`, `category_sort_order`) VALUES
('官方公告', 'official_notice', 'official-notice', '官方公告与通知', 1),
('邦国新闻', 'country_news', 'country-news', '服务器邦国新闻综合报道', 2);

-- 4.2 默认邦国（系统邦国，id=1 为默认）
INSERT INTO `countries` (`id`, `name`, `declaration`, `description`) VALUES
(1, '默认邦国', '默认邦国', '系统默认邦国，新用户默认归属');

-- 4.3 默认管理员用户（密码使用 PASSWORD_SALT 加盐哈希）
--     密码: admin123
--     如需修改密码，请在 PHP 中运行：
--     echo password_hash('新密码' . PASSWORD_SALT, PASSWORD_DEFAULT);
INSERT INTO `news_admin_users` (`username`, `password_hash`, `email`, `role`) VALUES
('admin', '$2y$12$DzSxCdHO7rzPi8luOKA6aexNYTZ/Wrvwit3tjY9IBUhlnYTA/Olt2', 'admin@bgjq.top', 'super_admin');

-- ============================================================
-- 完成
-- ============================================================
SELECT '数据库初始化完成！' AS message;
SELECT '请修改 news_admin_users 表中 admin 用户的密码哈希。' AS notice;
SELECT '运行以下 PHP 代码生成密码哈希：' AS tip;
SELECT 'php -r "echo password_hash(\"你的密码\" . \"bgjq_news_system_2026\", PASSWORD_DEFAULT);"' AS command;
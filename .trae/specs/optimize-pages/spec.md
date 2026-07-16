# 邦国新闻页面优化 Spec

## Why
邦国新闻（news.bgjq.top）是一个基于 NES 像素风格的新闻资讯平台。当前页面存在以下问题：
1. **Emoji 滥用**：使用 Emoji 作为结构性图标（🔥 🕐 👁️ ❤️ 📅 👤 📰 ☀️ 🌙 💻），不符合专业 UI 规范
2. **可访问性问题**：大量交互元素缺少 `aria-label`，表单缺少 `autocomplete`，焦点状态不明确
3. **视觉层次混乱**：标题、按钮、卡片缺乏一致的视觉层级，色彩对比度不足
4. **动画与交互粗糙**：缺少 `prefers-reduced-motion` 支持，过渡动画使用 `transition: all`
5. **移动端适配不足**：触摸目标过小，导航栏在小屏幕下堆叠混乱
6. **代码冗余**：内联样式过多，CSS 变量系统不完善，响应式断点不一致

## What Changes
- **全局样式重构**：建立完善的 CSS 变量系统，统一颜色、间距、字体、阴影
- **图标替换**：将所有 Emoji 替换为 SVG 图标或 NES.css 图标
- **可访问性增强**：添加 ARIA 标签、焦点状态、键盘导航支持
- **动画优化**：添加 `prefers-reduced-motion` 支持，使用 `transform`/`opacity` 动画
- **移动端优化**：调整触摸目标大小，优化导航栏和侧边栏在小屏幕下的表现
- **布局改进**：统一网格系统，优化卡片、按钮、表单的视觉层次
- **代码清理**：移除内联样式，提取公共组件样式

## Impact
- 受影响页面：首页、文章详情页、搜索页、分类页、排行榜页、登录页、投稿页、个人中心页
- 受影响文件：
  - `assets/css/style.css`
  - `assets/css/responsive.css`
  - `assets/js/main.js`
  - `views/layouts/header.php`
  - `views/layouts/footer.php`
  - `views/home.php`
  - `views/article/detail.php`
  - `views/search.php`
  - `views/category.php`
  - `views/rankings.php`
  - `views/login.php`
  - `views/contribute.php`
  - `views/profile.php`

## ADDED Requirements

### Requirement: 全局 CSS 变量系统
The system SHALL provide a comprehensive CSS variable system for colors, spacing, typography, and shadows.

#### Scenario: 颜色变量
- **GIVEN** 页面使用 CSS 变量
- **THEN** 应包含 `--color-primary`, `--color-secondary`, `--color-success`, `--color-warning`, `--color-error`, `--color-background`, `--color-surface`, `--color-text-primary`, `--color-text-secondary` 等变量
- **AND** 支持浅色/深色模式切换

#### Scenario: 间距变量
- **GIVEN** 页面使用 CSS 变量
- **THEN** 应包含 `--space-xs: 4px`, `--space-sm: 8px`, `--space-md: 16px`, `--space-lg: 24px`, `--space-xl: 32px`, `--space-2xl: 48px` 等变量

#### Scenario: 字体变量
- **GIVEN** 页面使用 CSS 变量
- **THEN** 应包含 `--font-pixel`, `--font-sans`, `--font-mono` 等变量
- **AND** 定义 `--text-xs` 到 `--text-2xl` 的字体大小变量

### Requirement: Emoji 替换为 SVG 图标
The system SHALL replace all Emoji icons with SVG icons or NES.css icons.

#### Scenario: 首页 Emoji 替换
- **GIVEN** 首页包含 Emoji 图标
- **THEN** 将 🔥 替换为 SVG 火焰图标
- **AND** 将 🕐 替换为 SVG 时钟图标
- **AND** 将 👁️ ❤️ 📅 👤 📰 替换为对应的 SVG 图标

#### Scenario: 主题切换 Emoji 替换
- **GIVEN** 主题切换按钮使用 Emoji
- **THEN** 将 ☀️ 🌙 💻 替换为 SVG 太阳、月亮、电脑图标

### Requirement: 可访问性增强
The system SHALL improve accessibility across all pages.

#### Scenario: ARIA 标签
- **GIVEN** 页面包含交互元素
- **THEN** 所有 icon-only 按钮必须有 `aria-label`
- **AND** 所有表单输入必须有 `<label>` 或 `aria-label`
- **AND** 所有图片必须有 `alt` 属性

#### Scenario: 焦点状态
- **GIVEN** 页面包含可交互元素
- **THEN** 所有按钮、链接、输入框必须有可见的焦点状态
- **AND** 使用 `:focus-visible` 而非 `:focus`
- **AND** 焦点环颜色与主题色一致

#### Scenario: 键盘导航
- **GIVEN** 用户使用键盘导航
- **THEN** 所有交互元素必须可通过 Tab 键访问
- **AND** 下拉菜单支持 Esc 键关闭
- **AND** 搜索框支持 Enter 键触发搜索

### Requirement: 动画优化
The system SHALL optimize animations for performance and accessibility.

#### Scenario: Reduced Motion 支持
- **GIVEN** 用户启用 `prefers-reduced-motion`
- **THEN** 所有动画应禁用或简化为即时过渡
- **AND** 保留必要的状态变化（如颜色变化）

#### Scenario: 动画属性优化
- **GIVEN** 页面包含过渡动画
- **THEN** 仅使用 `transform` 和 `opacity` 进行动画
- **AND** 避免使用 `width`, `height`, `top`, `left` 等属性动画
- **AND** 不使用 `transition: all`

### Requirement: 移动端优化
The system SHALL optimize the UI for mobile devices.

#### Scenario: 触摸目标大小
- **GIVEN** 用户在移动设备上访问
- **THEN** 所有触摸目标最小尺寸为 44×44px
- **AND** 按钮、链接之间有足够的间距（≥8px）

#### Scenario: 导航栏优化
- **GIVEN** 屏幕宽度小于 768px
- **THEN** 导航栏应折叠为汉堡菜单或垂直布局
- **AND** 搜索框应全宽显示
- **AND** 侧边栏应移至主内容下方

### Requirement: 布局改进
The system SHALL improve the visual hierarchy and layout consistency.

#### Scenario: 卡片样式统一
- **GIVEN** 页面包含文章卡片
- **THEN** 所有卡片应有统一的圆角、阴影、边框样式
- **AND** 卡片悬停状态应有清晰的视觉反馈
- **AND** 卡片内容应有合理的内边距和间距

#### Scenario: 按钮样式统一
- **GIVEN** 页面包含按钮
- **THEN** 所有按钮应有统一的高度、圆角、字体大小
- **AND** 主按钮、次按钮、危险按钮应有明确的颜色区分
- **AND** 按钮悬停、按下、禁用状态应有清晰的视觉反馈

## MODIFIED Requirements

### Requirement: 现有响应式设计
**Current**: 使用 Bootstrap 风格的断点（576px, 768px, 992px, 1200px）
**Modified**: 使用更合理的断点（480px, 768px, 1024px, 1280px），并优化每个断点下的布局

#### Scenario: 断点优化
- **GIVEN** 屏幕宽度在 480px 以下
- **THEN** 所有内容应单列显示
- **AND** 字体大小适当缩小
- **AND** 间距适当缩小

- **GIVEN** 屏幕宽度在 768px 以下
- **THEN** 侧边栏应移至主内容下方
- **AND** 导航栏应折叠

### Requirement: 现有主题切换
**Current**: 使用 `body.dark-mode` 和 `body.light-mode` 类
**Modified**: 使用 `data-theme` 属性，并添加系统主题自动检测

#### Scenario: 主题切换改进
- **GIVEN** 用户选择"跟随系统"
- **THEN** 页面应自动检测 `prefers-color-scheme`
- **AND** 系统主题变化时自动切换

## REMOVED Requirements

### Requirement: 内联样式
**Reason**: 内联样式难以维护，不利于主题切换
**Migration**: 将所有内联样式提取到 CSS 类中

### Requirement: Bootstrap 依赖
**Reason**: 项目已使用自定义 CSS，Bootstrap 造成样式冲突
**Migration**: 移除 Bootstrap CSS 和 JS 引用，使用自定义网格系统

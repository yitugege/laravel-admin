# Laravel Admin

这是一个基于 Laravel 框架开发的管理后台系统。
php artisan route:clear      # 清理路由缓存
php artisan config:clear     # 清理配置缓存
php artisan cache:clear      # 清理应用缓存
php artisan view:clear       # 清理视图缓存
php artisan route:cache      # 重新生成路由缓存（生产环境建议用）

php artisan queue:restart && php artisan queue:work redis --queue=sync --tries=3 --timeout=300 --verbose
## 项目结构

```
├── app/                 # 应用程序核心代码
├── config/             # 配置文件
├── database/           # 数据库迁移和种子文件
├── public/             # 公共访问目录
├── resources/          # 视图和未编译的资源文件
├── routes/             # 路由定义
├── storage/            # 应用程序存储目录
├── tests/              # 测试文件
└── vendor/             # Composer 依赖
```

## 环境要求

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js >= 16
- pnpm

## 安装步骤

1. 克隆项目
```bash
git clone [项目地址]
```

2. 安装 PHP 依赖
```bash
composer install
```

3. 安装前端依赖
```bash
pnpm install
```

4. 配置环境变量
```bash
cp .env.example .env
php artisan key:generate
```

5. 运行数据库迁移
```bash
php artisan migrate
```

6. 启动开发服务器
```bash
php artisan serve
```

## 主要功能

- 用户认证和授权
- 后台管理界面
- 数据管理
- API 接口

## 开发规范

- 遵循 PSR-4 自动加载规范
- 使用 Laravel 最佳实践
- 代码风格遵循 PSR-12

## 测试

运行测试：
```bash
php artisan test
```

## 部署

1. 生产环境配置
2. 数据库迁移
3. 缓存配置
4. 静态资源编译

## 维护者

[维护者信息]

## 许可证

[许可证信息]

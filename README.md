# know-that finder
The know-that/finder is the view page of the laravel log or other files.
## 安装
```bash
$ composer require know-that/finder
```

## 发布配置
```bash
$ php artisan vendor:publish --tag=kt.finder --force
```
此操作会生成以下文件  
1、`public` 目录下生成对应的 css 与 js 的静态文件，同时  
2、`config` 目录下生成配置文件 `config/know-that/finder.php`

## 访问
运行 `php artisan serve`，打开对应链接如：`http://127.0.0.1:8000/know-that/finder`

## 可替代品（日志可视化）
https://github.com/rap2hpoutre/laravel-log-viewer  
https://github.com/opcodesio/log-viewer

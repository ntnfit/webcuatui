# toilamerp.com

Website cá nhân kiêm marketplace addon SAP Business One, công cụ thuế và blog.

## Công nghệ

- Backend: Laravel 13 (PHP 8.3+), Filament 5 (admin `/admin` và khu vực khách hàng `/customer`), Livewire 4
- Giao diện public: Blade + Tailwind CSS v4 (design token trong `resources/css/app.css`), build bằng Vite
- Cơ sở dữ liệu: MySQL (production), SQLite in-memory cho test

## Chức năng chính

- `/` trang chủ, `/marketplace` danh mục addon SAP B1 (lọc theo tích hợp và phiên bản SAP), `/marketplace/{slug}` chi tiết + form yêu cầu báo giá
- `/tools` các công cụ, `/blogs` blog, `/shop` cửa hàng, `/sitemap.xml` sitemap động
- Quản trị addon trong Filament: mục "E-commerce Management" > Products (tab Addon)

## Cài đặt local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# chỉnh DB_* trong .env, hoặc dùng SQLite: DB_CONNECTION=sqlite và tạo file database/database.sqlite
php artisan migrate --seed
php artisan storage:link
```

Chạy dev:

```bash
php artisan serve
npm run dev
```

Build production assets: `npm run build`.

## Kiểm thử

```bash
php artisan test
```

Test dùng SQLite in-memory (xem `phpunit.xml`), không cần MySQL.

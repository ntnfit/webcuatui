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

## Tin công nghệ tự động

Mỗi ngày (mặc định 09:00 giờ Việt Nam) hệ thống đọc các feed RSS/Atom, chọn tối đa 10 tin, nhờ Claude viết lại thành bài SEO tiếng Việt kèm ảnh và nguồn tham khảo, rồi đăng tự động.

- Cấu hình toàn bộ ở admin: `/admin/cau-hinh-tin-tu-dong` (công tắc tổng, đăng tự động hay lưu nháp, giờ chạy, số bài, model, prompt bổ sung, ảnh, API key mã hóa). Giá trị lưu DB ghi đè `config/news.php`, vốn đọc từ biến môi trường trong `.env.example`.
- Nguồn tin, nhật ký chạy và gỡ bài nhanh: nhóm "Tin tự động" trong admin.
- Cần: `php artisan migrate`, `php artisan db:seed --class=NewsSourceSeeder`, `php artisan storage:link`, cron `schedule:run` mỗi phút và queue worker (nút "Chạy ngay").
- Lệnh: `php artisan news:crawl [--dry-run] [--limit=N] [--source=tên|id]`. `--dry-run` chỉ in danh sách sẽ đăng, không gọi Claude, không ghi gì.

## Kiểm thử

```bash
php artisan test
```

Test dùng SQLite in-memory (xem `phpunit.xml`), không cần MySQL.

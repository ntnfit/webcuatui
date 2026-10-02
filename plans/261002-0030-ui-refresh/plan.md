---
title: Làm lại UI public (Blade + Tailwind v4), tối ưu tốc độ
status: draft
created: 2026-10-02
branch: feat/ui-refresh
---

# Outcome

Giao diện public hiện đại, nhất quán, nhanh (LCP < 2,5 s, TTFB < 0,3 s với cache), định vị lại từ
"portfolio cá nhân" sang "SAP B1 addons + tools + blog". Giữ Filament cho admin và tools, không đổi sang React.

# Hiện trạng (đã đo trên toilamerp.com)

- TTFB ban đầu ~1,06 s → hiện ~0,35–0,5 s sau OPcache + cache + bỏ ads/Livewire khỏi trang chủ.
- Có hai bộ layout song song: `layouts/main.blade.php` (kiểu GitHub dark, đang dùng cho trang chủ/blog)
  và `components/layouts/*` + `components/home/*` (cũ, nhiều file không còn dùng) → dư thừa.
- Font nạp từ bunny.net + Google Fonts (2 nguồn), chưa self-host dù có `spatie/laravel-google-fonts`.
- Thẻ bài viết: `getDataArray()` luôn trả `asset('storage/'.cover_photo_path)` kể cả khi null → ảnh hỏng;
  `stars` dùng `rand(20,50)` giả.
- README ghi "React", test Breeze cũ fail (22/25), route `/test` còn trong `web.php`.

# Thiết kế

- Design tokens (màu, spacing, radius, typography) khai báo trong `@theme` của Tailwind v4, dark/light.
- Một layout duy nhất `layouts/app` + các partial: navbar (Marketplace, Tools, Blog, Liên hệ), footer, hero.
- Trang chủ mới: hero định vị SAP B1 integrations → lưới addon nổi bật → tools (Tax) → bài viết mới → liên hệ.
- Hiệu năng: ảnh WebP/AVIF qua `spatie/image` + `srcset`, `loading=lazy`, font self-host `display=swap`,
  chỉ nạp JS ở trang cần (Livewire/Alpine), preload ảnh LCP, cache HTML trang khách ở Cloudflare
  (cần cấu hình dashboard Cloudflare).
- Quảng cáo: không chèn vào hero/trang chủ; chỉ ở bài viết, có chỗ dành sẵn để tránh nhảy layout.
- Truy cập: tương phản AA, focus ring, `prefers-reduced-motion`, nhãn form.

# Phases

1. Dọn dẹp: xóa component không dùng, gộp layout, sửa bug thumbnail/stars, sửa README, xóa `/test`, xóa/viết lại test Breeze.
2. Design tokens + layout + navbar/footer mới.
3. Trang chủ mới + trang blog list/detail theo tokens.
4. Trang marketplace + tools (kết hợp 2 plan còn lại).
5. Tối ưu: ảnh, font, cache Cloudflare, đo lại bằng Lighthouse + New Relic (đã gắn APM).
6. Kiểm thử trình duyệt (desktop/mobile, light/dark) và deploy.

# Rủi ro

- Làm lại UI khi nội dung và định vị chưa chốt dễ phải sửa; nên chốt copy trang chủ trước pha 3.
- SEO: giữ nguyên URL `/blogs/{slug}`, canonical, sitemap; không đổi slug.

# Câu hỏi mở

- Giữ phong cách terminal/GitHub-dark hiện tại hay chuyển sang phong cách SaaS sáng, sạch?
- Có logo/brand màu cố định không (hiện dùng `logo.jpg`)?
- Có bản tiếng Anh cho khách nước ngoài không?

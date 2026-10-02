---
title: Marketplace addon SAP B1 (VAS, e-invoice, banking, SePay, Magento, Shopify)
status: draft
created: 2026-10-02
branch: feat/marketplace
---

# Outcome

Một trang marketplace trên web để giới thiệu và bán addon tích hợp SAP Business One:
VAS, hóa đơn điện tử, ngân hàng, SePay, Magento ↔ SAP B1, Shopify ↔ SAP B1.
Giai đoạn 1 (đã chốt): catalog + trang sản phẩm + liên hệ/đặt mua, dùng lại Shop/Order/Coupon hiện có.

# Non-goals (giai đoạn 1)

- Không tự cấp license, không tải addon sau thanh toán (giai đoạn 2).
- Không viết bản thân các addon; plan này chỉ là kênh bán/giới thiệu.

# Hiện trạng tái sử dụng

`Product` (name, slug, price, sale_price, image, status, category_id), `Order`, `OrderDetail`, `Coupon`,
`ShopController`, route `/shop`. Có trang liên hệ (`Contacts`, `ContactReason`) và sitemap.

# Thiết kế giai đoạn 1

- Mở rộng `products` bằng migration mới (không phá dữ liệu cũ): `type` (physical|addon), `sap_versions` (json),
  `db_support` (sqlserver|hana), `integration` (SePay|Shopify|Magento|Bank|EInvoice|VAS), `features` (json),
  `gallery` (json), `docs_url`, `demo_url`, `billing` (one_time|yearly|quote).
- Route: `/marketplace` (lọc theo integration/phiên bản), `/marketplace/{slug}`; giữ `/shop` hoạt động.
- Mỗi trang sản phẩm: mô tả, tính năng, sơ đồ luồng dữ liệu, phiên bản SAP hỗ trợ, bảng giá, FAQ,
  nút "Yêu cầu báo giá" (dùng form liên hệ có gắn product) và "Đặt mua" khi `billing != quote`.
- SEO: JSON-LD `Product`/`SoftwareApplication`, sitemap bổ sung, trang theo từ khóa
  ("tích hợp Shopify SAP B1", "hóa đơn điện tử SAP B1", "SePay SAP B1").
- Admin: Filament `ProductResource` thêm tab Addon với các field trên; import mẫu 6 addon ban đầu qua seeder.
- Thanh toán: bắt đầu bằng chuyển khoản + xác nhận tay; đề xuất VietQR/SePay webhook ở giai đoạn 2.

# Phases

1. Migration + model + seeder 6 addon (nội dung tiếng Việt, có bản EN sau).
2. Trang `/marketplace` và `/marketplace/{slug}` (Blade + Tailwind, theo plan UI).
3. Form báo giá/liên hệ gắn product, email thông báo (`Mail` đã có).
4. Admin Filament, SEO, sitemap, test (Pest feature test cho listing/filter/show).
5. Giai đoạn 2 (không thuộc phạm vi hiện tại): `License` theo MST + hardware id, trang "Addon của tôi" ở customer panel, tải file, webhook SePay.

# Rủi ro

- Nội dung sản phẩm (tính năng thật, giá) do bạn cung cấp; mình chỉ dựng khung và bản nháp.
- Không hứa tính năng chưa có addon thật.
- Dùng lại bảng `products` có thể lẫn hàng hóa vật lý; cột `type` tách rõ.

# Câu hỏi mở

- "VAS" là chuẩn mực kế toán Việt Nam (báo cáo/thông tư) hay dịch vụ giá trị gia tăng?
- Bán bản quyền vĩnh viễn, theo năm, hay báo giá theo dự án? Có cần giá công khai không?
- Những addon nào đã có thật và demo được?
- Ngôn ngữ trang: chỉ tiếng Việt hay thêm tiếng Anh?

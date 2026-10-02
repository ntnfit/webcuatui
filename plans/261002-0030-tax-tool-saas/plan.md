---
title: Tax tool (hóa đơn điện tử GDT) trong /tools, đăng nhập + đa công ty
status: draft
created: 2026-10-02
branch: feat/tax-tool
source: E:\Workplace\Software\tax-vn-app (Electron: server/tax-vn-routes.js 916 dòng, database.js, renderer/app.js 757 dòng)
---

# Outcome

Người dùng đăng nhập vào web, thêm nhiều công ty (mỗi công ty một MST), đăng nhập cổng
`hoadondientu.gdt.gov.vn` bằng captcha, rồi tra cứu hóa đơn bán ra / mua vào, xem chi tiết,
xuất XML (zip), Excel, PDF. Chức năng tương đương app Electron, chạy trên Laravel.

# Non-goals

- Không lưu mật khẩu cổng thuế của người dùng.
- Không làm app Electron mới; không giữ Node làm microservice (đã chốt: viết lại bằng PHP).
- Chưa làm: ký/phát hành hóa đơn, đối chiếu sổ SAP B1 (để plan marketplace).

# Quyết định kiến trúc

| Chủ đề | Quyết định | Lý do |
|---|---|---|
| Đăng nhập | Guard `customer` đã có (`Customer`, `CustomerPanelProvider`) | Có sẵn, tách khỏi admin |
| UI | Filament panel `customer` + **multi-tenancy** (`tenant(Company::class)`) | Tenancy của Filament khớp đúng "nhiều công ty", có sẵn switcher, table, filter, export |
| Gọi GDT | `GdtClient` dùng `Illuminate\Http\Client`, port header trình duyệt từ `server/gdt-browser-headers.js` | Giữ hành vi đã chạy được |
| TLS | **Bật verify TLS** (Electron đang `rejectUnauthorized:false`, không port) | Chạy trên server, không chấp nhận MITM |
| Token GDT | Lưu mã hóa (`encrypted` cast), có `expires_at`, không lưu username/mật khẩu | Giảm rủi ro dữ liệu nhạy cảm |
| Export lớn | Queue job (queue `database` đã có) + thông báo khi xong | Tránh timeout PHP-FPM |
| PDF | Pha 1: HTML preview + XML + Excel. PDF pha sau (dompdf hoặc Browsershot) | Browsershot cần Chromium, RAM server 3.9 GB còn mailcow |

# Data model

- `companies`: id, name, mst (unique per owner), address, timestamps.
- `company_customer` (pivot): company_id, customer_id, role (owner|member).
- `gdt_sessions`: company_id, token (encrypted), expires_at.
- `tax_invoices`: company_id, direction (sold|purchase), source (standard|mtt), mst_seller, mst_buyer,
  number, symbol, template, issued_at, total_before_tax, total_tax, total_payment, status, raw (json),
  unique (company_id, direction, symbol, number, mst_seller). Index theo (company_id, issued_at).
- `tax_export_runs`: company_id, type (xml_zip|excel|pdf_zip), filters (json), status, file_path, expires_at.

# Phases

0. **Spike (bắt buộc làm trước):** từ server 103 gọi `GET /api/captcha` của GDT. Nếu WAF/captcha chặn IP
   datacenter thì phải đổi thiết kế (ví dụ chạy request qua máy người dùng) trước khi đầu tư tiếp.
   Acceptance: lấy được captcha và login thử 1 MST thật từ server.
1. **Nền tảng:** migration, model, policy, Filament customer panel + tenancy, trang đăng ký/đăng nhập
   customer, trang quản lý công ty. Acceptance: 1 user tạo 2 công ty, chuyển qua lại, không thấy dữ liệu của nhau.
2. **GDT login:** `GdtClient`, trang "Kết nối cổng thuế" (hiện captcha, nhập mật khẩu, không lưu), lưu token,
   hiển thị hạn token, tự yêu cầu đăng nhập lại khi 401.
3. **Hóa đơn:** port `fetchAllInvoicePages` (phân trang `state`, size 50), list bán ra/mua vào, bộ lọc ngày,
   chi tiết hóa đơn, cache vào `tax_invoices`. Acceptance: số liệu khớp app Electron trên cùng MST/kỳ.
4. **Export:** XML đơn + zip, Excel (`phpspreadsheet` đã có qua filament-excel), HTML preview, job nền.
5. **PDF + hoàn thiện:** PDF zip, rate limit theo user/công ty, giới hạn kỳ tối đa, trang `/tools` liệt kê tool và yêu cầu đăng nhập.
6. **Test + deploy:** Pest + `Http::fake()` cho GDT, test tenancy isolation, backup DB trước migrate.

# Rủi ro

- WAF/captcha chặn IP server (xử lý ở pha 0).
- Điều khoản sử dụng cổng thuế khi tự động hóa; cần trang điều khoản nói rõ người dùng tự cung cấp thông tin đăng nhập, web không lưu mật khẩu.
- Dữ liệu hóa đơn là dữ liệu kinh doanh: cô lập theo công ty bằng global scope + policy, có test.
- Tải: export lớn tốn RAM; giới hạn kỳ và chạy bằng queue.

# Câu hỏi mở

- Chạy GDT từ server có bị chặn không? (quyết định ở pha 0)
- Tool Tax miễn phí hay gói trả phí/giới hạn số công ty?
- Khách đăng ký tự do hay duyệt tay?

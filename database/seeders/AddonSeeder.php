<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Seeds the SAP Business One addon catalogue.
 *
 * The copy is a generic, honest description of what each kind of integration does.
 * It names no customers, benchmarks or certifications, and publishes no prices
 * (every addon is quote based). Review scope per addon before going live.
 * Safe to run repeatedly: rows are matched by slug.
 */
class AddonSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->addons() as $addon) {
            Product::updateOrCreate(
                ['slug' => $addon['slug']],
                $addon + [
                    'type' => Product::TYPE_ADDON,
                    'billing' => Product::BILLING_QUOTE,
                    'price' => 0,
                    'sale_price' => null,
                    'quantity' => 0,
                    'status' => 'active',
                    'sap_versions' => ['9.3', '10.0'],
                    'db_support' => ['sqlserver', 'hana'],
                ]
            );
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function addons(): array
    {
        return [
            [
                'name' => 'Báo cáo kế toán VAS cho SAP B1',
                'slug' => 'bao-cao-ke-toan-vas-sap-b1',
                'integration' => 'VAS',
                'summary' => 'Bộ báo cáo kế toán theo chuẩn mực kế toán Việt Nam (VAS) chạy trực tiếp trên dữ liệu SAP Business One.',
                'description' => "Addon bổ sung các mẫu báo cáo kế toán theo chuẩn mực kế toán Việt Nam (VAS) vào SAP Business One, để kế toán không phải xuất dữ liệu ra Excel rồi dựng lại biểu mẫu.\n\nCác báo cáo lấy dữ liệu trực tiếp từ sổ cái và các phân hệ của SAP B1, có thể xuất ra Excel hoặc PDF. Danh sách báo cáo cụ thể và cách ánh xạ tài khoản được thống nhất với kế toán của doanh nghiệp khi khảo sát.",
                'features' => [
                    'Báo cáo tài chính và sổ sách theo biểu mẫu VAS, lấy từ dữ liệu SAP B1',
                    'Ánh xạ hệ thống tài khoản của doanh nghiệp sang tài khoản theo VAS',
                    'Lọc theo kỳ kế toán, chi nhánh và dự án',
                    'Xuất Excel và PDF',
                    'Điều chỉnh biểu mẫu khi quy định thay đổi',
                ],
                'data_flow' => [
                    ['from' => 'SAP B1 (sổ cái, phân hệ)', 'to' => 'Addon VAS', 'note' => 'Đọc dữ liệu theo kỳ và điều kiện lọc'],
                    ['from' => 'Addon VAS', 'to' => 'Biểu mẫu báo cáo', 'note' => 'Ánh xạ tài khoản, tổng hợp số liệu'],
                    ['from' => 'Biểu mẫu báo cáo', 'to' => 'Excel / PDF', 'note' => 'Xuất để lưu trữ hoặc nộp'],
                ],
                'faqs' => [
                    ['q' => 'Addon có thay đổi dữ liệu trong SAP B1 không?', 'a' => 'Không. Các báo cáo chỉ đọc dữ liệu, không ghi ngược lại vào sổ sách.'],
                    ['q' => 'Có thể bổ sung biểu mẫu riêng của doanh nghiệp không?', 'a' => 'Có thể, phạm vi và thời gian được thống nhất khi khảo sát yêu cầu.'],
                ],
            ],
            [
                'name' => 'Tích hợp hóa đơn điện tử cho SAP B1',
                'slug' => 'tich-hop-hoa-don-dien-tu-sap-b1',
                'integration' => 'EInvoice',
                'summary' => 'Phát hành và theo dõi hóa đơn điện tử ngay từ chứng từ bán hàng trong SAP Business One.',
                'description' => "Addon kết nối SAP Business One với nhà cung cấp dịch vụ hóa đơn điện tử mà doanh nghiệp đang dùng, để phát hành hóa đơn từ chứng từ bán hàng mà không phải nhập lại dữ liệu ở hệ thống khác.\n\nTrạng thái hóa đơn (đã phát hành, bị hủy, điều chỉnh) được ghi nhận lại trên chứng từ SAP B1. Nhà cung cấp hóa đơn được hỗ trợ cần được xác nhận trước khi triển khai.",
                'features' => [
                    'Phát hành hóa đơn từ chứng từ bán hàng (A/R Invoice)',
                    'Ghi nhận số hóa đơn, ký hiệu và trạng thái về SAP B1',
                    'Xử lý hóa đơn thay thế và điều chỉnh theo quy trình của nhà cung cấp',
                    'Nhật ký giao dịch để đối soát khi có lỗi',
                    'Gửi lại hoặc tra cứu hóa đơn đã phát hành',
                ],
                'data_flow' => [
                    ['from' => 'SAP B1 (A/R Invoice)', 'to' => 'Addon hóa đơn điện tử', 'note' => 'Lấy thông tin người mua, hàng hóa, thuế'],
                    ['from' => 'Addon hóa đơn điện tử', 'to' => 'Nhà cung cấp hóa đơn điện tử', 'note' => 'Gửi yêu cầu phát hành qua API'],
                    ['from' => 'Nhà cung cấp hóa đơn điện tử', 'to' => 'SAP B1', 'note' => 'Trả về số hóa đơn và trạng thái'],
                ],
                'faqs' => [
                    ['q' => 'Hỗ trợ nhà cung cấp hóa đơn nào?', 'a' => 'Tùy nhà cung cấp mà doanh nghiệp đang dùng và có API. Vui lòng gửi yêu cầu để được xác nhận.'],
                    ['q' => 'Dữ liệu hóa đơn có nằm trên máy chủ của bạn không?', 'a' => 'Addon chạy cùng môi trường SAP B1 của doanh nghiệp; hóa đơn được lưu tại nhà cung cấp dịch vụ hóa đơn điện tử.'],
                ],
            ],
            [
                'name' => 'Tích hợp ngân hàng cho SAP B1',
                'slug' => 'tich-hop-ngan-hang-sap-b1',
                'integration' => 'Bank',
                'summary' => 'Nhập sao kê ngân hàng và đối soát với chứng từ thanh toán trong SAP Business One.',
                'description' => "Addon giúp đưa sao kê ngân hàng vào SAP Business One và đối chiếu với các khoản phải thu, phải trả, giảm thao tác nhập tay của kế toán.\n\nCách lấy dữ liệu (tệp sao kê hoặc kết nối API của ngân hàng) phụ thuộc vào ngân hàng doanh nghiệp sử dụng và cần được xác nhận khi khảo sát.",
                'features' => [
                    'Nhập sao kê từ tệp hoặc từ API ngân hàng nếu ngân hàng hỗ trợ',
                    'Gợi ý đối soát giao dịch với hóa đơn và phiếu thu/chi',
                    'Tạo chứng từ thanh toán (Incoming/Outgoing Payment) sau khi kế toán xác nhận',
                    'Đánh dấu giao dịch đã đối soát và chưa đối soát',
                    'Nhật ký thao tác để kiểm tra',
                ],
                'data_flow' => [
                    ['from' => 'Ngân hàng (tệp sao kê / API)', 'to' => 'Addon ngân hàng', 'note' => 'Nhập giao dịch'],
                    ['from' => 'Addon ngân hàng', 'to' => 'Kế toán', 'note' => 'Gợi ý khớp với hóa đơn, chờ xác nhận'],
                    ['from' => 'Addon ngân hàng', 'to' => 'SAP B1', 'note' => 'Tạo chứng từ thanh toán đã duyệt'],
                ],
                'faqs' => [
                    ['q' => 'Có tự động ghi nhận thanh toán không cần kiểm tra?', 'a' => 'Mặc định kế toán xác nhận trước khi tạo chứng từ; việc tự động hóa được thống nhất theo quy trình của doanh nghiệp.'],
                    ['q' => 'Ngân hàng nào được hỗ trợ?', 'a' => 'Phụ thuộc định dạng sao kê hoặc API của từng ngân hàng. Gửi yêu cầu để được xác nhận.'],
                ],
            ],
            [
                'name' => 'Tích hợp SePay cho SAP B1',
                'slug' => 'tich-hop-sepay-sap-b1',
                'integration' => 'SePay',
                'summary' => 'Nhận thông báo giao dịch từ SePay và đối soát thanh toán với đơn hàng, hóa đơn trong SAP Business One.',
                'description' => "SePay là dịch vụ thông báo biến động số dư ngân hàng. Addon nhận các thông báo giao dịch này và đối chiếu nội dung chuyển khoản với đơn hàng hoặc hóa đơn trong SAP Business One.\n\nPhù hợp khi doanh nghiệp muốn biết nhanh khoản thanh toán đã về mà không phải kiểm tra sao kê thủ công.",
                'features' => [
                    'Nhận thông báo giao dịch từ SePay qua webhook',
                    'Đối chiếu nội dung chuyển khoản với mã đơn hàng hoặc hóa đơn',
                    'Cập nhật trạng thái thanh toán hoặc tạo chứng từ thu sau khi khớp',
                    'Danh sách giao dịch chưa khớp để xử lý thủ công',
                    'Kiểm tra tính hợp lệ của yêu cầu webhook',
                ],
                'data_flow' => [
                    ['from' => 'Ngân hàng', 'to' => 'SePay', 'note' => 'Phát sinh giao dịch chuyển khoản'],
                    ['from' => 'SePay', 'to' => 'Addon SePay', 'note' => 'Gửi webhook thông báo giao dịch'],
                    ['from' => 'Addon SePay', 'to' => 'SAP B1', 'note' => 'Đối chiếu và ghi nhận thanh toán'],
                ],
                'faqs' => [
                    ['q' => 'Có cần tài khoản SePay riêng không?', 'a' => 'Có. Doanh nghiệp cần đăng ký dịch vụ SePay và liên kết tài khoản ngân hàng của mình.'],
                    ['q' => 'Giao dịch ghi sai nội dung thì sao?', 'a' => 'Giao dịch không khớp được đưa vào danh sách chờ để kế toán xử lý.'],
                ],
            ],
            [
                'name' => 'Tích hợp Magento với SAP B1',
                'slug' => 'tich-hop-magento-sap-b1',
                'integration' => 'Magento',
                'summary' => 'Đồng bộ sản phẩm, tồn kho, giá và đơn hàng giữa cửa hàng Magento và SAP Business One.',
                'description' => "Addon kết nối cửa hàng Magento với SAP Business One để hai hệ thống dùng chung một nguồn dữ liệu sản phẩm, tồn kho và đơn hàng, giảm việc nhập liệu lặp lại và sai lệch số liệu.\n\nPhạm vi đồng bộ (danh mục, giá theo nhóm khách hàng, nhiều kho, trạng thái đơn) được xác định theo nhu cầu của từng doanh nghiệp.",
                'features' => [
                    'Đồng bộ sản phẩm, giá và tồn kho từ SAP B1 lên Magento',
                    'Đưa đơn hàng Magento về SAP B1 thành Sales Order',
                    'Tạo hoặc khớp khách hàng (Business Partner) từ thông tin đơn hàng',
                    'Cập nhật trạng thái giao hàng ngược lại cho Magento',
                    'Nhật ký đồng bộ và cơ chế thử lại khi lỗi',
                ],
                'data_flow' => [
                    ['from' => 'SAP B1', 'to' => 'Magento', 'note' => 'Sản phẩm, giá, tồn kho'],
                    ['from' => 'Magento', 'to' => 'SAP B1', 'note' => 'Đơn hàng và thông tin khách hàng'],
                    ['from' => 'SAP B1', 'to' => 'Magento', 'note' => 'Trạng thái xử lý và giao hàng'],
                ],
                'faqs' => [
                    ['q' => 'Hỗ trợ phiên bản Magento nào?', 'a' => 'Phiên bản Magento cần được xác nhận khi khảo sát vì cách kết nối có thể khác nhau.'],
                    ['q' => 'Đồng bộ theo thời gian thực hay theo lịch?', 'a' => 'Có thể cấu hình theo lịch hoặc theo sự kiện tùy yêu cầu và khối lượng giao dịch.'],
                ],
            ],
            [
                'name' => 'Tích hợp Shopify với SAP B1',
                'slug' => 'tich-hop-shopify-sap-b1',
                'integration' => 'Shopify',
                'summary' => 'Đồng bộ sản phẩm, tồn kho và đơn hàng giữa cửa hàng Shopify và SAP Business One.',
                'description' => "Addon kết nối cửa hàng Shopify với SAP Business One: thông tin sản phẩm và tồn kho đi từ SAP B1 sang Shopify, đơn hàng đi ngược lại để xử lý trong SAP B1.\n\nGiúp bộ phận bán hàng online và kế toán/kho cùng làm việc trên một bộ dữ liệu thay vì nhập liệu hai nơi.",
                'features' => [
                    'Đồng bộ sản phẩm, giá và tồn kho từ SAP B1 lên Shopify',
                    'Đưa đơn hàng Shopify về SAP B1 thành Sales Order hoặc A/R Invoice',
                    'Khớp hoặc tạo khách hàng từ thông tin đơn hàng',
                    'Cập nhật trạng thái hoàn tất đơn và vận đơn lên Shopify',
                    'Nhật ký đồng bộ và cơ chế thử lại khi lỗi',
                ],
                'data_flow' => [
                    ['from' => 'SAP B1', 'to' => 'Shopify', 'note' => 'Sản phẩm, giá, tồn kho'],
                    ['from' => 'Shopify', 'to' => 'SAP B1', 'note' => 'Đơn hàng qua webhook hoặc API'],
                    ['from' => 'SAP B1', 'to' => 'Shopify', 'note' => 'Trạng thái xử lý, vận đơn'],
                ],
                'faqs' => [
                    ['q' => 'Có hỗ trợ nhiều kho không?', 'a' => 'Có thể ánh xạ kho SAP B1 với địa điểm tồn kho của Shopify, phạm vi cụ thể thống nhất khi khảo sát.'],
                    ['q' => 'Có cần ứng dụng riêng trên Shopify không?', 'a' => 'Tùy cách kết nối được chọn; sẽ được tư vấn cụ thể trong báo giá.'],
                ],
            ],
        ];
    }
}

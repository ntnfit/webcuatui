<section id="about" class="py-16 px-6 sm:px-10 lg:px-20 bg-gray-50 dark:bg-gray-900 transition-colors duration-500">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-8 tracking-tight transition-all duration-500 font-sans text-gray-800 dark:text-white opacity-100 translate-y-0">
            ✨ Về Tôi
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center transition-all duration-500 opacity-100 translate-y-0">
            <!-- Thông tin giới thiệu -->
            <div class="space-y-6">
                <p class="text-base md:text-lg text-gray-700 dark:text-gray-300 leading-relaxed font-sans transition-colors duration-500">
                    Xin chào! Mình là <strong class="text-sky-600 dark:text-sky-400 transition-colors duration-500">Harry Dev</strong>,
                    một lập trình viên đam mê với hơn 5 năm kinh nghiệm xây dựng ứng dụng web, hệ thống doanh nghiệp và tích hợp giải pháp công nghệ.
                    Mình luôn hướng đến việc tạo ra sản phẩm đẹp, nhanh, tối ưu và dễ sử dụng.
                </p>

                <ul class="space-y-3">
                    @foreach([
                        'Phát triển WebApp với React, Next.js và Tailwind CSS',
                        'Triển khai & tối ưu hệ thống ERP cho doanh nghiệp',
                        'Tối ưu hóa hiệu suất và trải nghiệm người dùng (UX/UI)',
                        'Thiết kế API và tích hợp hệ thống SAP B1 / OData / SDK',
                    ] as $item)
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 mt-1 text-sky-500 dark:text-blue-400 mr-3 flex-shrink-0 transition-colors duration-500"><path d="M20 6 9 17l-5-5"/></svg>
                            <span class="text-gray-700 dark:text-gray-300 font-sans transition-colors duration-500">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Hình ảnh minh họa -->
            <div class="relative group">
                <div class="rounded-2xl overflow-hidden shadow-lg dark:shadow-gray-800/30 transform transition-all duration-500 group-hover:scale-105 group-hover:shadow-2xl">
                    <img src="/images/me.jpg" alt="Harry Dev" class="object-cover w-full h-full transition-all duration-500 ease-in-out group-hover:opacity-95">
                </div>
                <div class="absolute -inset-1 rounded-2xl bg-gradient-to-tr from-blue-300 via-pink-200 to-purple-300 dark:from-blue-900/30 dark:via-pink-900/20 dark:to-purple-900/30 opacity-20 blur-2xl z-[-1] transition-colors duration-500"></div>
            </div>
        </div>
    </div>
</section>

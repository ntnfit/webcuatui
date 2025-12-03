<section id="projects" class="py-24 bg-gray-50 dark:bg-gray-900 transition-colors duration-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Phần mô tả -->
        <div class="text-center mb-16 animate-fade-in-up">
            <div class="inline-block px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-medium mb-4 transition-colors duration-500">
                Dự án đã triển khai
            </div>
            <h2 class="text-3xl md:text-4xl font-bold mb-4 tracking-tight text-gray-900 dark:text-white transition-colors duration-500">
                Đối tác & Khách hàng
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto transition-colors duration-500">
                Tôi đã có cơ hội làm việc và phát triển các giải pháp công nghệ cho nhiều đối tác trong và ngoài nước. Dưới đây là một số khách hàng tiêu biểu mà tôi đã có cơ hội hợp tác.
            </p>
        </div>

        <!-- Grid logo -->
        <div class="relative animate-fade-in">
            <!-- Gradient overlays -->
            <div class="absolute left-0 top-0 bottom-0 w-20 bg-gradient-to-r from-gray-50 dark:from-gray-900 to-transparent z-10 pointer-events-none"></div>
            <div class="absolute right-0 top-0 bottom-0 w-20 bg-gradient-to-l from-gray-50 dark:from-gray-900 to-transparent z-10 pointer-events-none"></div>

            <!-- Logo grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 md:gap-12 items-center overflow-hidden">
                @foreach([
                    ['id' => '1', 'name' => 'San Hà Foods', 'logo' => 'https://sanha.vn/wp-content/uploads/2024/07/logo.png', 'link' => 'https://sanha.vn/', 'description' => 'Rise up with quality · Safe food supply chain from farm to table · SanHàFoodstore · Foodie Buddy · A+ Gourmet Food · SanHà HORECA.'],
                    ['id' => '2', 'name' => 'Hồng Ký', 'logo' => 'https://www.hongky.com/wp-content/uploads/2024/08/hing.png', 'link' => 'https://www.hongky.com/', 'description' => 'Công ty Hồng Ký sản xuất và phân phối các sản phẩm về máy móc cơ khí'],
                    ['id' => '3', 'name' => 'E-block', 'logo' => 'https://eblock.com.vn/wp-content/uploads/2024/07/logo-header.png', 'link' => 'https://eblock.com.vn', 'description' => 'Autoclaved aerated concrete is a modern construction material, including AAC EBLOCK bricks and AAC EPANEL panels, creating sustainable, energy-saving buildings.'],
                    ['id' => '4', 'name' => 'Tazmo', 'logo' => 'https://tazmo-vn.com/wp-content/uploads/2021/12/logo_TAZMO_2021.png', 'link' => 'https://tazmo-vn.com', 'description' => 'TAZMO Việt Nam sản xuất thiết bị tự động hóa (FA) và các loại máy móc công nghiệp.'],
                    ['id' => '5', 'name' => 'Aeondelight', 'logo' => 'https://aeondelight-vietnam.com.vn/wp-content/uploads/2024/02/logo-6.svg', 'link' => 'https://aeondelight-vietnam.com.vn', 'description' => 'Over 12 years of offering Facility Management Services, AEON Delight Vietnam has been most esteemed in this sector.'],
                    ['id' => '6', 'name' => 'Cjolivenetworks', 'logo' => 'https://en.cjolivenetworks.co.kr/images/common/logo-white.svg', 'link' => 'https://en.cjolivenetworks.co.kr', 'description' => 'CJ OliveNetworks, a lifestyle innovation company that leads the change in space and daily life based on digital technology and data.'],
                    ['id' => '7', 'name' => 'Việt Hưng Sài Gòn', 'logo' => 'https://cdn.nhansu.vn/uploads/images/0B91D74C/logo/2018-12/fc4f6b551f484115966604ba4e6adffb_logo-Viet-Hung.png', 'link' => 'http://viethung.com.vn/', 'description' => 'Công ty bao bì Việt Hưng Sài Gòn'],
                    ['id' => '8', 'name' => 'betagen', 'logo' => 'https://www.betagen.co.th/images/logo.png', 'link' => 'https://www.betagen.co.th/', 'description' => 'Betagen is a leading manufacturer of high-quality pharmaceutical products in Thailand.'],
                    ['id' => '9', 'name' => 'woodsland', 'logo' => 'https://woodsland.vn/wp-content/uploads/2024/07/logo.svg', 'link' => 'https://woodsland.vn', 'description' => 'Woodsland is a leading manufacturer of high-quality pharmaceutical products in Vietnam.'],
                    ['id' => '10', 'name' => 'Nissey', 'logo' => 'http://www.nihon-s.co.jp/wp-content/uploads/elementor/thumbs/logo001-poowskldjo65xxcjfqkcvjrcq33ci0zr2933ho3v3g.png', 'link' => 'http://www.nihon-s.co.jp/group-company/nissey-vietnam/', 'description' => 'Nissey is a leading manufacturer of high-quality pharmaceutical products in Vietnam.'],
                    ['id' => '11', 'name' => 'Nam Dung', 'logo' => 'https://lh6.googleusercontent.com/proxy/pbw0HOscYPaji1hh6BrJjC7o_XHWLKAl-jkswJzk0gSQSKWjJjr8XNT7gkS1NGVzzCFaNSIbxKlJTvbY-95syIKODB3KwoEhAOZqwQ', 'link' => 'http://www.namdung.vn', 'description' => 'Nam Dung is a leading manufacturer of high-quality pharmaceutical products in Vietnam.'],
                    ['id' => '12', 'name' => 'usm', 'logo' => 'https://usm.com.vn/wp-content/themes/usm/images/logo.svg', 'link' => ' https://usm.com.vn/', 'description' => 'usm is a leading manufacturer of high-quality pharmaceutical products in Vietnam.']
                ] as $index => $company)
                    <a href="{{ $company['link'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center group {{ $index % 3 === 0 ? 'col-span-2' : 'col-span-1' }}">
                        <div class="relative w-full h-12 md:h-16 transform transition-transform duration-300 group-hover:scale-105">
                            <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="w-full h-full object-contain grayscale group-hover:grayscale-0 opacity-60 group-hover:opacity-100 transition duration-300">
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- View more button -->
        <div class="text-center mt-16 animate-fade-in-up">
            <a href="#contact" class="font-semibold inline-flex items-center px-6 py-3 rounded-full bg-gradient-to-r from-sky-500 to-indigo-500 dark:from-purple-500 dark:to-pink-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition-all duration-300 transform hover:scale-105">
                <span>Liên hệ hợp tác</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>

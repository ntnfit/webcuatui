<section id="home" class="min-h-screen flex flex-col items-center justify-center px-6 py-20 relative overflow-hidden bg-white dark:bg-gray-900 transition-colors duration-500">
    <!-- Fancy background -->
    <div class="absolute inset-0 -z-10">
        <div class="absolute inset-0 bg-gradient-to-b from-white via-blue-50 to-transparent opacity-90 dark:from-gray-900 dark:via-gray-800 dark:to-transparent transition-colors duration-500"></div>
        <div class="absolute inset-0 bg-[url('/pattern.svg')] opacity-10 dark:opacity-5 transition-opacity duration-500"></div>
    </div>

    <div class="max-w-4xl mx-auto text-center transition-all duration-1000 opacity-100 translate-y-0">
        <!-- Avatar glow -->
        <div class="mb-6 flex justify-center">
            <div class="relative">
                <div class="absolute inset-0 rounded-full bg-gradient-to-r from-sky-300 to-pink-400 blur-2xl animate-glow dark:from-purple-500 dark:to-pink-500 transition-colors duration-500"></div>
                <div class="h-28 w-28 rounded-full overflow-hidden border-4 border-white dark:border-gray-800 shadow-xl relative z-10 transition-colors duration-500">
                    <img src="/images/me.jpg" alt="Profile" class="h-full w-full object-cover">
                </div>
            </div>
        </div>

        <div class="inline-block px-4 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs font-medium mb-4 animate-fade-in font-sans transition-colors duration-500">
            ✨ Chào mừng đến với portfolio của tôi ✨
        </div>

        <h1 class="text-4xl md:text-6xl font-bold tracking-tight text-gray-800 dark:text-white mb-4 leading-tight font-sans transition-colors duration-500">
            Xin chào, tôi là 
            <span class="bg-gradient-to-r from-sky-500 to-purple-500 dark:from-purple-400 dark:to-pink-400 bg-clip-text text-transparent transition-colors duration-500" id="typed-name">
                Harry Dev
            </span>
        </h1>

        <div class="h-14 mb-4">
            <p class="text-lg md:text-2xl text-gray-600 dark:text-gray-300 font-medium font-sans transition-colors duration-500">
                Tôi là 
                <span class="text-sky-600 dark:text-purple-400 transition-colors duration-500" id="typed-text"></span>
                <span class="inline-block w-1.5 h-6 bg-sky-500 dark:bg-purple-400 ml-1 animate-blink rounded-sm transition-colors duration-500"></span>
            </p>
        </div>

        <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 mb-6 max-w-2xl mx-auto animate-fade-in font-sans transition-colors duration-500">
            Tôi tạo ra những trải nghiệm sống động, chức năng và tập trung vào người dùng với sự chính xác và chú ý đến từng chi tiết.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mb-8 animate-fade-in">
            <a href="#projects" class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-indigo-500 dark:from-purple-500 dark:to-pink-500 text-white rounded-full font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 font-sans">
                🚀 Xem dự án
            </a>
            <a href="/blogs" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-full font-medium transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-800 font-sans transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                📚 Đọc blog
            </a>
        </div>

        <!-- Callout -->
        <div class="relative z-10 mb-6 animate-fade-in">
            <div class="flex items-center justify-center">
                <div class="px-6 py-5 bg-gradient-to-r from-blue-100 via-purple-100 to-pink-100 dark:from-purple-900/40 dark:via-gray-800 dark:to-pink-900/30 rounded-2xl shadow-lg dark:shadow-purple-900/20 backdrop-blur-lg border border-white/20 dark:border-white/10 max-w-xl w-full transition-all duration-500">
                    <p class="text-md md:text-lg text-gray-800 dark:text-gray-200 font-medium text-center transition-colors duration-500">
                        💡 Portfolio này được xây dựng bằng 
                        <span class="font-semibold text-sky-600 dark:text-purple-400 transition-colors duration-500">
                            Laravel Blade, Tailwind và một chút phép màu ✨
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Scroll-down -->
        <div class="animate-bounce">
            <a href="#about" aria-label="Scroll down" class="group flex flex-col items-center gap-1 text-gray-400 dark:text-gray-500 hover:text-sky-500 dark:hover:text-purple-400 transition-colors duration-300">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300 transition-colors duration-300">
                    Khám phá thêm
                </span>
                <div class="relative">
                    <div class="absolute inset-0 rounded-full bg-gradient-to-r from-sky-300 to-pink-400 blur-xl opacity-0 group-hover:opacity-100 transition-all duration-300 dark:from-purple-500 dark:to-pink-500"></div>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="relative z-10 transform group-hover:scale-110 transition-all duration-300">
                        <path d="M12 5V19M12 19L19 12M12 19L5 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </a>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const texts = [
            "Lập trình viên WebApp",
            "Chuyên gia ERP",
            "Chuyên gia React & Vue",
            "Chuyên gia tích hợp hệ thống",
        ];
        const typedTextElement = document.getElementById('typed-text');
        let currentTextIndex = 0;
        let charIndex = 0;
        let isTyping = true;

        function type() {
            const textToType = texts[currentTextIndex];
            
            if (isTyping) {
                if (charIndex <= textToType.length) {
                    typedTextElement.textContent = textToType.substring(0, charIndex);
                    charIndex++;
                    setTimeout(type, 100);
                } else {
                    isTyping = false;
                    setTimeout(type, 1500);
                }
            } else {
                if (charIndex >= 0) {
                    typedTextElement.textContent = textToType.substring(0, charIndex);
                    charIndex--;
                    setTimeout(type, 50);
                } else {
                    isTyping = true;
                    currentTextIndex = (currentTextIndex + 1) % texts.length;
                    setTimeout(type, 500);
                }
            }
        }

        type();
    });
</script>

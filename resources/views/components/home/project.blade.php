<link href="https://cdn.jsdelivr.net/npm/pagedone@1.2.2/src/css/pagedone.css " rel="stylesheet"/>
<div x-cloak  x-data="projectSlider()"
     class="mx-auto w-full max-w-screen-lg px-5 space-y-20 pt-20">
    <div x-ref="header" class="text-center ">
        <div x-ref="header_introducing" class="font-medium text-dolphin dark:text-gray-100">
            Giới thiệu
        </div>
        <div class="pt-2 text-2xl sm:text-3xl  dark:text-white">
                <span x-ref="header_new" class="inline-block">
                    Các
                </span>
            <span x-ref="header_version3" class="inline-block font-black">
                    Dự án
                </span>
            <span x-ref="header_features" class="inline-block">
                    ....!
                </span>
        </div>
    </div>
<div class="relative rounded-lg p-8 shadow-lg overflow-hidden mx-auto">
            <!-- Animated Border -->
            <div class="absolute inset-0 border-2 rounded-lg animate-border-move"></div>
            <!-- Content -->
            <div class="relative z-10 mx-auto max-w-5xl overflow-hidden">
                <h1 class="mb-4 text-2xl font-bold dark:text-green-400">Dự án gần đây</h1>

                <!-- Wrapper for sliding -->
                <div class="relative"     @mousedown="startDrag($event)"
                     @mousemove="onDrag($event)"
                     @mouseup="endDrag()"
                     @mouseleave="endDrag()"
                     @touchstart="startDrag($event)"
                     @touchmove="onDrag($event)"
                     @touchend="endDrag()">
                    <div
                        class="flex transition-transform duration-500 gap-2"
                        :style="`transform: translateX(-${currentProject * 100}%)`"
                    >
                        <!-- Project slides -->
                        <template x-for="(project, index) in projects" :key="index">
                            <div class="flex-none w-full grid grid-cols-1 gap-8 md:grid-cols-2 pr-6">
                                <!-- Left: Image -->
                                <div>
                                    <img
                                        :src="project.image"
                                        alt="Project Thumbnail"
                                        class="rounded-lg shadow-md transition-transform duration-500"
                                    />
                                </div>

                                <!-- Right: Content -->
                                <div class="w-full pr-6">
                                    <h2 class="mb-2 text-xl font-semibold text-green-400" x-text="project.title"></h2>
                                    <p class="mb-6  dark:text-gray-400" x-text="project.description"></p>
                                    <!-- Project Info -->
                                    <h3 class="mb-2 font-semibold  text-pink-400">Thông tin dự án</h3>
                                    <div class="space-y-2 pr-2  dark:text-gray-300">
                                        <div class="flex justify-between">
                                            <span>Khách hàng:</span>
                                            <span x-text="project.client"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Thời gian:</span>
                                            <span x-text="project.completionTime"></span>
                                        </div>
                                        <div class="flex justify-between gap-2">
                                            <span>Công nghệ:</span>
                                            <span class="flex-wrap" x-text="project.technologies"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-6 flex items-center justify-end gap-4">
                    <button
                        @click="prevProject()"
                        class="rounded-full bg-gray-800 p-2  shadow hover:bg-gray-700  text-white"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </button>
                    <button
                        @click="nextProject()"
                        class="rounded-full bg-gray-800 p-2  shadow hover:bg-gray-700 text-white "
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>

            </div>
</div>
    <div class="w-full relative">
        <div class="swiper progress-slide-carousel swiper-container relative">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="bg-indigo-50 rounded-2xl h-96 flex justify-center items-center">
                        <span class="text-3xl font-semibold text-indigo-600">Slide 1 </span>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="bg-indigo-50 rounded-2xl h-96 flex justify-center items-center">
                        <span class="text-3xl font-semibold text-indigo-600">Slide 2 </span>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="bg-indigo-50 rounded-2xl h-96 flex justify-center items-center">
                        <span class="text-3xl font-semibold text-indigo-600">Slide 3 </span>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination !bottom-2 !top-auto !w-80 right-0 mx-auto bg-gray-100"></div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/pagedone@1.2.2/src/js/pagedone.js"></script>
@push('scripts')
    <script>
        function projectSlider() {
            return {
                currentProject: 0,

                projects: [
                    {
                        title: 'Go Live dự án ERP SAP Business One',
                        description: '  Công ty Grant Thornton Việt Nam vừa hoàn thành dự án triển khai SAP Business One tại công ty CJ'
                            + ' Oliver Networks Vina, một dự án đầy thử thách và thành công. Sau 4 tháng triển khai, dự án đã'
                            + 'chính thức Go-live',
                        client: 'CJ Olive Networks Vina',
                        completionTime: '3 tháng',
                        technologies: 'SQL Server, SAP B1, E-Invoice,Crystal report',
                        image: '{{ Vite::asset('resources/images/home/cj.webp') }}',
                    },
                    {
                        title: 'Nâng cấp hệ thống ERP SAP Business One',
                        description: 'Công Ty TNHH BETAGEN VIỆT NAM, đã chính thức khởi động dự án nâng cấp giải pháp Quản trị tổng'
                            + 'thể doanh nghiệp SAP Business One tại Việt Nam.',
                        client: 'Betagen Việt Nam',
                        completionTime: '3 tháng',
                        technologies: 'SAP HANA, SAP B1, E-Invoice,Crystal report',
                        image: '{{ Vite::asset('resources/images/home/betagen.webp') }}',
                    },

                    {
                        title: '  Dự án PGT & PDM',
                        description: 'Dự án nội bộ về module FI và PP SAP S4HANA.',
                        client: 'bosch Global Software Technologies ',
                        completionTime: '1 năm',
                        technologies: 'SAP HANA, SAP ABAP, UI5/FIORI',
                        image: '{{ Vite::asset('resources/images/home/Bosch.png') }}',
                    },
                    {
                        title: '  Dự án SAP Business One và iVend Retails cho Công ty SANHA',
                        description: 'Dự án Công ty SANHA được khởi động vào ngày 16/04/2020 với giải pháp triển khai đồng thời hai'
                            + 'giải pháp: ERP SAP B1 - Giải pháp quản lí tổng thể nguồn lực doanh nghiệp và POS - Giải pháp'
                            + 'quản lý chuỗi cửa bán hàng bán lẻ, siêu thị.',
                        client: 'San Hà Foods',
                        completionTime: '6 tháng',
                        technologies: 'SAP HANA, SAP B1, Ivend, POS Retail,Ivend',
                        image: '{{ Vite::asset('resources/images/home/San-Ha-5.webp') }}',
                    },
                    {
                        title: ' Dự án SAP Business One cho Công Ty Cổ Phần Bao Bì Việt Hưng Sài Gòn.',
                        description: 'Công ty Cổ Phần Bao Bì Việt Hưng Sài Gòn (VHSG) là chi nhánh của công ty Bao Bì Việt Hưng, có'
                            + 'tổng diện tích 60.000 m2, trong đó 40.000 m2 được sử dụng làm diện tích nhà xưởng.',
                        client: 'Việt Hưng Sai Gon',
                        completionTime: '6 tháng',
                        technologies: 'Crystal report, SAP B1, SQLSERVER',
                        image: '{{ Vite::asset('resources/images/home/viethung.webp') }}',
                    },
                    {
                        title: ' Dự án SAP Business One cho Công ty Cơ khí Hồng ký.',
                        description: 'Cơ khí Hồng Ký là đơn vị sản xuất MÁY BIẾN THẾ HÀN - MÁY HÀN ĐIỆN TỬ TIG/MIG/INVERTER - MÁY CẮT'
                            + 'PLASMA - ĐỘNG CƠ ĐIỆN (MOTOR) - MÁY KHOAN VÀ MÁY CHẾ BIẾN GỖ - MÁY NGÀNH THÉP - VẬT LIỆU MÀI MÒN'
                            + 'chuyên nghiệp có quy mô lớn nhất Việt Nam.',
                        client: 'Cơ khí Hồng Ký',
                        completionTime: '6 tháng',
                        technologies: 'Crystal report, SAP B1, SAP HANA',
                        image: '{{ Vite::asset('resources/images/home/hongky.webp') }}',
                    }
                ],
                interval: null,
                startX: 0,
                endX: 0,

                init() {
                    this.autoSlide();
                },
                autoSlide() {
                    this.interval = setInterval(() => {
                        this.nextProject();
                    }, 5000); // 5 giây
                },
                stopAutoSlide() {
                    clearInterval(this.interval);
                },
                nextProject() {
                    this.currentProject = (this.currentProject + 1) % this.projects.length;
                },
                prevProject() {
                    this.currentProject = (this.currentProject - 1 + this.projects.length) % this.projects.length;
                },
                startDrag(event) {
                    this.stopAutoSlide();
                    this.startX = event.touches ? event.touches[0].clientX : event.clientX;
                },
                onDrag(event) {
                    this.endX = event.touches ? event.touches[0].clientX : event.clientX;
                },
                endDrag() {
                    if (this.startX > this.endX + 50) {
                        this.nextProject();
                    } else if (this.startX < this.endX - 50) {
                        this.prevProject();
                    }
                    this.autoSlide();
                },
            };
            }
        var swiper = new Swiper(".progress-slide-carousel", {
            loop: true,
            fraction: true,
            autoplay: {
                delay: 1200,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".progress-slide-carousel .swiper-pagination",
                type: "progressbar",
            },
        });
        </script>
    @endpush


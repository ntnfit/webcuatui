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
</div>

@push('scripts')
    <script>
        function projectSlider() {
            return {
                currentProject: 0,
                isTransitioning: false,
                projects: [
                    {
                        title: 'Go Live dự án ERP SAP Business One',
                        description: '  Công ty Grant Thornton Việt Nam vừa hoàn thành dự án triển khai SAP Business One tại công ty CJ'
                            +' Oliver Networks Vina, một dự án đầy thử thách và thành công. Sau 4 tháng triển khai, dự án đã'
                           +'chính thức Go-live',
                            client: 'CJ Olive Networks Vina',
                            completionTime: '3 tháng',
                            technologies:'SQL Server, SAP B1, E-Invoice,Crystal report',
                            image: '{{ Vite::asset('resources/images/home/cj.webp') }}',
                        },
                        {
                            title: 'Nâng cấp hệ thống ERP SAP Business One',
                            description: 'Công Ty TNHH BETAGEN VIỆT NAM, đã chính thức khởi động dự án nâng cấp giải pháp Quản trị tổng'
                                +'thể doanh nghiệp SAP Business One tại Việt Nam.',
                            client: 'Betagen Việt Nam',
                            completionTime: '3 tháng',
                            technologies:'SAP HANA, SAP B1, E-Invoice,Crystal report',
                            image: '{{ Vite::asset('resources/images/home/betagen.webp') }}',
                        },

                        {
                            title: '  Dự án PGT & PDM',
                            description: 'Dự án nội bộ về module FI và PP SAP S4HANA.',
                            client: 'bosch Global Software Technologies ',
                            completionTime: '1 năm',
                            technologies:'SAP HANA, SAP ABAP, UI5/FIORI',
                            image: '{{ Vite::asset('resources/images/home/Bosch.png') }}',
                        },
                        {
                            title: '  Dự án SAP Business One và iVend Retails cho Công ty SANHA',
                            description: 'Dự án Công ty SANHA được khởi động vào ngày 16/04/2020 với giải pháp triển khai đồng thời hai'
                                +'giải pháp: ERP SAP B1 - Giải pháp quản lí tổng thể nguồn lực doanh nghiệp và POS - Giải pháp'
                                +'quản lý chuỗi cửa bán hàng bán lẻ, siêu thị.',
                            client: 'San Hà Foods',
                            completionTime: '6 tháng',
                            technologies:'SAP HANA, SAP B1, Ivend, POS Retail,Ivend',
                            image: '{{ Vite::asset('resources/images/home/San-Ha-5.webp') }}',
                        },
                        {
                            title: ' Dự án SAP Business One cho Công Ty Cổ Phần Bao Bì Việt Hưng Sài Gòn.',
                            description: 'Công ty Cổ Phần Bao Bì Việt Hưng Sài Gòn (VHSG) là chi nhánh của công ty Bao Bì Việt Hưng, có'
                                +'tổng diện tích 60.000 m2, trong đó 40.000 m2 được sử dụng làm diện tích nhà xưởng.',
                            client: 'Việt Hưng Sai Gon',
                            completionTime: '6 tháng',
                            technologies: 'Crystal report, SAP B1, SQLSERVER',
                            image: '{{ Vite::asset('resources/images/home/viethung.webp') }}',
                        },
                        {
                            title: ' Dự án SAP Business One cho Công ty Cơ khí Hồng ký.',
                            description: 'Cơ khí Hồng Ký là đơn vị sản xuất MÁY BIẾN THẾ HÀN - MÁY HÀN ĐIỆN TỬ TIG/MIG/INVERTER - MÁY CẮT'
                                +'PLASMA - ĐỘNG CƠ ĐIỆN (MOTOR) - MÁY KHOAN VÀ MÁY CHẾ BIẾN GỖ - MÁY NGÀNH THÉP - VẬT LIỆU MÀI MÒN'
                                +'chuyên nghiệp có quy mô lớn nhất Việt Nam.',
                            client: 'Cơ khí Hồng Ký',
                            completionTime: '6 tháng',
                            technologies: 'Crystal report, SAP B1, SAP HANA',
                            image: '{{ Vite::asset('resources/images/home/hongky.webp') }}',
                        }
                    ],
                interval: null,
                startX: 0,
                endX: 0,
                autoSlide() {
                    this.interval = setInterval(() => {
                        this.nextProject();
                    }, 5000); // Chuyển sau mỗi 5 giây
                },
                stopAutoSlide() {
                    clearInterval(this.interval);
                },
                nextProject() {
                    this.isTransitioning = true;
                    setTimeout(() => {
                        this.currentProject = (this.currentProject + 1) % this.projects.length;
                        this.isTransitioning = false;
                    }, 300);
                },
                prevProject() {
                    this.isTransitioning = true;
                    setTimeout(() => {
                        this.currentProject = (this.currentProject - 1 + this.projects.length) % this.projects.length;
                        this.isTransitioning = false;
                    }, 300);
                },
                startDrag(event) {
                    this.stopAutoSlide();
                    this.startX = event.clientX || event.touches[0].clientX;
                },
                onDrag(event) {
                    this.endX = event.clientX || event.touches[0].clientX;
                },
                endDrag() {
                    if (this.startX > this.endX + 50) {
                        this.nextProject();
                    } else if (this.startX < this.endX - 50) {
                        this.prevProject();
                    }
                    this.autoSlide();
                },
                init() {
                    this.autoSlide();
                }
                }
            }
        </script>
    @endpush
    {{--<div x-cloak x-data="{}" class="mx-auto w-full max-w-screen-lg px-5 pt-20">--}}
{{--    <div x-data="{}" x-init="$nextTick(() => {--}}
{{--        if (reducedMotion) return--}}
{{--        gsap.timeline({--}}
{{--                scrollTrigger: {--}}
{{--                    trigger: $refs.header,--}}
{{--                    start: 'top bottom-=150px',--}}
{{--                },--}}
{{--            })--}}
{{--            .fromTo(--}}
{{--                $refs.header_introducing, {--}}
{{--                    autoAlpha: 0,--}}
{{--                    y: -30,--}}
{{--                }, {--}}
{{--                    autoAlpha: 1,--}}
{{--                    y: 0,--}}
{{--                    duration: 0.7,--}}
{{--                    ease: 'circ.out',--}}
{{--                },--}}
{{--            )--}}
{{--            .fromTo(--}}
{{--                $refs.header_new, {--}}
{{--                    autoAlpha: 0,--}}
{{--                    x: -30,--}}
{{--                    y: 30,--}}
{{--                }, {--}}
{{--                    autoAlpha: 1,--}}
{{--                    x: 0,--}}
{{--                    y: 0,--}}
{{--                    duration: 0.7,--}}
{{--                    ease: 'circ.out',--}}
{{--                },--}}
{{--                '>-0.6',--}}
{{--            )--}}
{{--            .fromTo(--}}
{{--                $refs.header_version3, {--}}
{{--                    autoAlpha: 0,--}}
{{--                    y: 30,--}}
{{--                }, {--}}
{{--                    autoAlpha: 1,--}}
{{--                    y: 0,--}}
{{--                    duration: 0.7,--}}
{{--                    ease: 'circ.out',--}}
{{--                },--}}
{{--                '>-0.6',--}}
{{--            )--}}
{{--            .fromTo(--}}
{{--                $refs.header_features, {--}}
{{--                    autoAlpha: 0,--}}
{{--                    x: 30,--}}
{{--                    y: 30,--}}
{{--                }, {--}}
{{--                    autoAlpha: 1,--}}
{{--                    x: 0,--}}
{{--                    y: 0,--}}
{{--                    duration: 0.7,--}}
{{--                    ease: 'circ.out',--}}
{{--                },--}}
{{--                '>-0.6',--}}
{{--            )--}}
{{--        gsap.fromTo(--}}
{{--            $refs.feature_1, {--}}
{{--                autoAlpha: 0,--}}
{{--                x: -20,--}}
{{--            }, {--}}
{{--                autoAlpha: 1,--}}
{{--                x: 0,--}}
{{--                duration: 0.7,--}}
{{--                ease: 'circ.out',--}}
{{--                scrollTrigger: {--}}
{{--                    trigger: $refs.feature_1,--}}
{{--                    start: 'top bottom-=150px',--}}
{{--                },--}}
{{--            },--}}
{{--        )--}}
{{--        gsap.fromTo(--}}
{{--            $refs.feature_2, {--}}
{{--                autoAlpha: 0,--}}
{{--                x: 20,--}}
{{--            }, {--}}
{{--                autoAlpha: 1,--}}
{{--                x: 0,--}}
{{--                duration: 0.7,--}}
{{--                ease: 'circ.out',--}}
{{--                scrollTrigger: {--}}
{{--                    trigger: $refs.feature_2,--}}
{{--                    start: 'top bottom-=150px',--}}
{{--                },--}}
{{--            },--}}
{{--        )--}}
{{--        gsap.fromTo(--}}
{{--            $refs.feature_3, {--}}
{{--                autoAlpha: 0,--}}
{{--                x: -20,--}}
{{--            }, {--}}
{{--                autoAlpha: 1,--}}
{{--                x: 0,--}}
{{--                duration: 0.7,--}}
{{--                ease: 'circ.out',--}}
{{--                scrollTrigger: {--}}
{{--                    trigger: $refs.feature_3,--}}
{{--                    start: 'top bottom-=150px',--}}
{{--                },--}}
{{--            },--}}
{{--        )--}}
{{--        gsap.fromTo(--}}
{{--            $refs.feature_4, {--}}
{{--                autoAlpha: 0,--}}
{{--                x: 20,--}}
{{--            }, {--}}
{{--                autoAlpha: 1,--}}
{{--                x: 0,--}}
{{--                duration: 0.7,--}}
{{--                ease: 'circ.out',--}}
{{--                scrollTrigger: {--}}
{{--                    trigger: $refs.feature_4,--}}
{{--                    start: 'top bottom-=150px',--}}
{{--                },--}}
{{--            },--}}
{{--        )--}}
{{--        gsap.fromTo(--}}
{{--            $refs.feature_5, {--}}
{{--                autoAlpha: 0,--}}
{{--                x: -20,--}}
{{--            }, {--}}
{{--                autoAlpha: 1,--}}
{{--                x: 0,--}}
{{--                duration: 0.7,--}}
{{--                ease: 'circ.out',--}}
{{--                scrollTrigger: {--}}
{{--                    trigger: $refs.feature_5,--}}
{{--                    start: 'top bottom-=150px',--}}
{{--                },--}}
{{--            },--}}
{{--        )--}}
{{--        gsap.fromTo(--}}
{{--            $refs.feature_6, {--}}
{{--                autoAlpha: 0,--}}
{{--                x: 20,--}}
{{--            }, {--}}
{{--                autoAlpha: 1,--}}
{{--                x: 0,--}}
{{--                duration: 0.7,--}}
{{--                ease: 'circ.out',--}}
{{--                scrollTrigger: {--}}
{{--                    trigger: $refs.feature_6,--}}
{{--                    start: 'top bottom-=150px',--}}
{{--                },--}}
{{--            },--}}
{{--        )--}}
{{--        gsap.to($refs.geometric_shape_1, {--}}
{{--            yPercent: -100,--}}
{{--            rotate: 100,--}}
{{--            scrollTrigger: {--}}
{{--                trigger: $refs.feature_1,--}}
{{--                scrub: 1.5,--}}
{{--                start: 'top bottom-=200px',--}}
{{--                end: 'bottom+=300px center',--}}
{{--            },--}}
{{--        })--}}
{{--        gsap.to($refs.geometric_shape_2, {--}}
{{--            yPercent: -100,--}}
{{--            xPercent: -50,--}}
{{--            rotate: 180,--}}
{{--            scrollTrigger: {--}}
{{--                trigger: $refs.feature_2,--}}
{{--                scrub: 1.5,--}}
{{--                start: 'top bottom-=200px',--}}
{{--                end: 'bottom+=300px center',--}}
{{--            },--}}
{{--        })--}}
{{--        gsap.to($refs.geometric_shape_3, {--}}
{{--            yPercent: -100,--}}
{{--            xPercent: -30,--}}
{{--            rotate: 100,--}}
{{--            scrollTrigger: {--}}
{{--                trigger: $refs.feature_3,--}}
{{--                scrub: 1.5,--}}
{{--                start: 'top bottom-=200px',--}}
{{--                end: 'bottom+=300px center',--}}
{{--            },--}}
{{--        })--}}
{{--        gsap.to($refs.geometric_shape_4, {--}}
{{--            yPercent: -100,--}}
{{--            xPercent: -30,--}}
{{--            rotate: 100,--}}
{{--            scrollTrigger: {--}}
{{--                trigger: $refs.feature_4,--}}
{{--                scrub: 1.5,--}}
{{--                start: 'top bottom-=200px',--}}
{{--                end: 'bottom+=300px center',--}}
{{--            },--}}
{{--        })--}}
{{--        gsap.to($refs.geometric_shape_5, {--}}
{{--            yPercent: -100,--}}
{{--            xPercent: -50,--}}
{{--            rotate: 100,--}}
{{--            scrollTrigger: {--}}
{{--                trigger: $refs.feature_5,--}}
{{--                scrub: 1.5,--}}
{{--                start: 'top bottom-=200px',--}}
{{--                end: 'bottom+=300px center',--}}
{{--            },--}}
{{--        })--}}
{{--        gsap.to($refs.geometric_shape_6, {--}}
{{--            yPercent: -100,--}}
{{--            xPercent: -50,--}}
{{--            rotate: 45,--}}
{{--            scrollTrigger: {--}}
{{--                trigger: $refs.feature_6,--}}
{{--                scrub: 1.5,--}}
{{--                start: 'top bottom-=200px',--}}
{{--                end: 'bottom+=500px center',--}}
{{--            },--}}
{{--        })--}}
{{--    })">--}}
{{--        <div x-ref="header" class="text-center">--}}
{{--            <div x-ref="header_introducing" class="font-medium text-dolphin dark:text-gray-100">--}}
{{--                Giới thiệu--}}
{{--            </div>--}}
{{--            <div class="pt-2 text-2xl sm:text-3xl  dark:text-white">--}}
{{--                <span x-ref="header_new" class="inline-block">--}}
{{--                    Các--}}
{{--                </span>--}}
{{--                <span x-ref="header_version3" class="inline-block font-black">--}}
{{--                    Dự án--}}
{{--                </span>--}}
{{--                <span x-ref="header_features" class="inline-block">--}}
{{--                    ....!--}}
{{--                </span>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div x-ref="features" class="space-y-32 pt-20">--}}
{{--            --}}{{-- Feature 1 --}}
{{--            <div x-ref="feature_1"--}}
{{--                class="relative flex flex-wrap items-center justify-around gap-10 lg:justify-center lg:gap-x-32">--}}
{{--                <div class="absolute -left-10 top-40 hidden lg:block">--}}
{{--                    <img x-ref="geometric_shape_1"--}}
{{--                        src="{{ Vite::asset('resources/images/home/geometric-shape-1.webp') }}" alt="Shape"--}}
{{--                        class="block w-14" />--}}
{{--                </div>--}}

{{--                --}}{{-- Screenshot --}}
{{--                <div--}}
{{--                    class="relative h-80 w-full max-w-[23rem] shrink-0 overflow-hidden rounded-3xl bg-gradient-to-tl from-orange-400 to-orange-200 shadow-xl shadow-black/5">--}}
{{--                    <div class="absolute left-5 top-5 w-[22rem] overflow-hidden rounded-lg shadow-xl">--}}
{{--                        <img src="{{ Vite::asset('resources/images/home/Bosch.png') }}" alt="Action modals"--}}
{{--                            class="w-full" />--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                --}}{{-- Feature Notes --}}
{{--                <div>--}}
{{--                    <div class="relative inline-block">--}}
{{--                        <img src="{{ Vite::asset('resources/images/home/handpoint.webp') }}" alt="Hand pointing"--}}
{{--                            class="w-12" />--}}
{{--                        <div class="absolute -bottom-4 left-4 -z-10 h-7 w-7 rounded-full bg-black/50 blur-md"></div>--}}
{{--                    </div>--}}
{{--                    --}}{{-- Title --}}
{{--                    <div class="max-w-[15rem] pt-5 text-2xl font-bold dark:text-gray-100">--}}
{{--                        Dự án nội bộ (Bosch).--}}
{{--                    </div>--}}

{{--                    --}}{{-- Description --}}
{{--                    <div class="max-w-xs pt-3 font-medium text-dolphin dark:text-white">--}}
{{--                        Sử dụng HANA Modeling & UI5 triển khai hệ thống FI(S4/HANA).--}}
{{--                        Fast support cho dự án nội bộ phân hệ PP(S4/Hana).--}}
{{--                        Family UI5 Expert.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            --}}{{-- Feature 2 --}}
{{--            <div x-ref="feature_2"--}}
{{--                class="relative flex flex-wrap items-center justify-around gap-10 lg:justify-center lg:gap-x-32">--}}
{{--                <div class="absolute -right-16 top-40 hidden lg:block">--}}
{{--                    <img x-ref="geometric_shape_2"--}}
{{--                        src="{{ Vite::asset('resources/images/home/geometric-shape-2.webp') }}" alt="Shape"--}}
{{--                        class="block w-14" />--}}
{{--                </div>--}}

{{--                --}}{{-- Screenshot --}}
{{--                <div--}}
{{--                    class="relative h-80 w-full max-w-[23rem] shrink-0 overflow-hidden rounded-3xl bg-gradient-to-tl from-orange-400 to-orange-200 shadow-xl shadow-black/5">--}}
{{--                    <div class="absolute left-5 top-5 w-[30rem] overflow-hidden rounded-lg shadow-xl">--}}
{{--                        <img src="{{ Vite::asset('resources/images/home/cj.webp') }}" alt="Table report"--}}
{{--                            class="w-full" />--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                --}}{{-- Feature Notes --}}
{{--                <div>--}}
{{--                    <div class="relative inline-block">--}}
{{--                        <img src="{{ Vite::asset('resources/images/home/report.webp') }}" alt="Report"--}}
{{--                            class="w-16" />--}}
{{--                        <div class="absolute -bottom-4 left-4 -z-10 h-7 w-7 rounded-full bg-black/50 blur-md"></div>--}}
{{--                    </div>--}}
{{--                    --}}{{-- Title --}}
{{--                    <div class="max-w-[15rem] pt-5 text-2xl font-bold dark:text-gray-100">--}}
{{--                        Go live dự án SAP B1 tại công ty CJ Olivenetworks Vina.--}}
{{--                    </div>--}}

{{--                    --}}{{-- Description --}}
{{--                    <div class="max-w-xs pt-3 font-medium text-dolphin dark:text-white ">--}}
{{--                        Công ty Grant Thornton Việt Nam vừa hoàn thành dự án triển khai SAP Business One tại công ty CJ--}}
{{--                        Olivenetworks Vina, một dự án đầy thử thách và thành công. Sau 4 tháng triển khai, dự án đã--}}
{{--                        chính thức Go-live--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            --}}{{-- Feature 3 --}}
{{--            <div x-ref="feature_3"--}}
{{--                class="relative flex flex-wrap items-center justify-around gap-10 lg:justify-center lg:gap-x-32">--}}
{{--                <div class="absolute -left-5 top-40 hidden lg:block">--}}
{{--                    <img x-ref="geometric_shape_3"--}}
{{--                        src="{{ Vite::asset('resources/images/home/geometric-shape-3.webp') }}" alt="Shape"--}}
{{--                        class="block w-16" />--}}
{{--                </div>--}}

{{--                --}}{{-- Screenshot --}}
{{--                <div--}}
{{--                    class="relative h-80 w-full max-w-[23rem] shrink-0 overflow-hidden rounded-3xl bg-gradient-to-tl from-orange-400 to-orange-200 shadow-xl shadow-black/5">--}}
{{--                    <div class="absolute left-5 top-5 w-[30rem] overflow-hidden rounded-lg shadow-xl">--}}
{{--                        <img src="{{ Vite::asset('resources/images/home/betagen.webp') }}" alt="Tenant switcher"--}}
{{--                            class="w-full" />--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                --}}{{-- Feature Notes --}}
{{--                <div>--}}
{{--                    <div class="relative inline-block">--}}
{{--                        <img src="{{ Vite::asset('resources/images/home/cloud.webp') }}" alt="Cloud"--}}
{{--                            class="w-20" />--}}
{{--                        <div class="absolute -bottom-4 left-4 -z-10 h-7 w-7 rounded-full bg-black/50 blur-md"></div>--}}
{{--                    </div>--}}
{{--                    --}}{{-- Title --}}
{{--                    <div class="max-w-[15rem] pt-5 text-2xl font-bold  dark:text-gray-100 ">--}}
{{--                        Nâng cấp SAP Business One cho Betagen Việt Nam lên nền tảng HANA Engine.--}}
{{--                    </div>--}}

{{--                    --}}{{-- Description --}}
{{--                    <div class="max-w-xs pt-3 font-medium text-dolphin dark:text-white ">--}}
{{--                        Công Ty TNHH BETAGEN VIỆT NAM, đã chính thức khởi động dự án nâng cấp giải pháp Quản trị tổng--}}
{{--                        thể doanh nghiệp SAP Business One tại Việt Nam.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            --}}{{-- Feature 4 --}}
{{--            <div x-ref="feature_4"--}}
{{--                class="relative flex flex-wrap items-center justify-around gap-10 lg:justify-center lg:gap-x-32">--}}
{{--                <div class="absolute -right-10 top-40 hidden lg:block">--}}
{{--                    <img x-ref="geometric_shape_4"--}}
{{--                        src="{{ Vite::asset('resources/images/home/geometric-shape-4.webp') }}" alt="Shape"--}}
{{--                        class="block w-12" />--}}
{{--                </div>--}}

{{--                --}}{{-- Screenshot --}}
{{--                <div--}}
{{--                    class="relative h-80 w-full max-w-[23rem] shrink-0 overflow-hidden rounded-3xl bg-gradient-to-tl from-orange-400 to-orange-200 shadow-xl shadow-black/5">--}}
{{--                    <div class="absolute left-5 top-5 w-[30rem] overflow-hidden rounded-lg shadow-xl">--}}
{{--                        <img src="{{ Vite::asset('resources/images/home/viethung.webp') }}" alt="Infolist"--}}
{{--                            class="w-full" />--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                --}}{{-- Feature Notes --}}
{{--                <div>--}}
{{--                    <div class="relative inline-block">--}}
{{--                        <img src="{{ Vite::asset('resources/images/home/featherpaper.webp') }}" alt="Quill on paper"--}}
{{--                            class="w-16" />--}}
{{--                        <div class="absolute -bottom-4 left-4 -z-10 h-7 w-7 rounded-full bg-black/50 blur-md"></div>--}}
{{--                    </div>--}}
{{--                    --}}{{-- Title --}}
{{--                    <div class="max-w-[15rem] pt-5 text-2xl font-bold  dark:text-gray-100">--}}
{{--                        Dự án SAP Business One cho Công Ty Cổ Phần Bao Bì Việt Hưng Sài Gòn.--}}
{{--                    </div>--}}

{{--                    --}}{{-- Description --}}
{{--                    <div class="max-w-xs pt-3 font-medium text-dolphin dark:text-white ">--}}
{{--                        Công ty Cổ Phần Bao Bì Việt Hưng Sài Gòn (VHSG) là chi nhánh của công ty Bao Bì Việt Hưng, có--}}
{{--                        tổng diện tích 60.000 m2, trong đó 40.000 m2 được sử dụng làm diện tích nhà xưởng.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            --}}{{-- Feature 5 --}}
{{--            <div x-ref="feature_5"--}}
{{--                class="relative flex flex-wrap items-center justify-around gap-10 lg:justify-center lg:gap-x-32">--}}
{{--                <div class="absolute bottom-0 left-0 hidden lg:block">--}}
{{--                    <img x-ref="geometric_shape_5"--}}
{{--                        src="{{ Vite::asset('resources/images/home/geometric-shape-5.webp') }}" alt="Shape"--}}
{{--                        class="block w-14" />--}}
{{--                </div>--}}

{{--                --}}{{-- Screenshot --}}
{{--                <div--}}
{{--                    class="relative h-80 w-full max-w-[23rem] shrink-0 overflow-hidden rounded-3xl bg-gradient-to-tl from-orange-400 to-orange-200 shadow-xl shadow-black/5">--}}
{{--                    <div class="absolute left-5 top-5 w-[30rem] overflow-hidden rounded-lg shadow-xl">--}}
{{--                        <img src="{{ Vite::asset('resources/images/home/hongky.webp') }}" alt="Multiple panels"--}}
{{--                            class="w-full" />--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                --}}{{-- Feature Notes --}}
{{--                <div>--}}
{{--                    <div class="relative inline-block">--}}
{{--                        <img src="{{ Vite::asset('resources/images/home/infinity.webp') }}" alt="Infinity"--}}
{{--                            class="w-16" />--}}
{{--                        <div class="absolute -bottom-4 left-4 -z-10 h-7 w-7 rounded-full bg-black/50 blur-md"></div>--}}
{{--                    </div>--}}
{{--                    --}}{{-- Title --}}
{{--                    <div class="max-w-[15rem] pt-5 text-2xl font-bold  dark:text-gray-100">--}}
{{--                        Dự án SAP Business One cho Công ty Cơ khí Hồng ký.--}}
{{--                    </div>--}}

{{--                    --}}{{-- Description --}}
{{--                    <div class="max-w-xs pt-3 font-medium text-dolphin dark:text-white ">--}}
{{--                        Cơ khí Hồng Ký là đơn vị sản xuất MÁY BIẾN THẾ HÀN - MÁY HÀN ĐIỆN TỬ TIG/MIG/INVERTER - MÁY CẮT--}}
{{--                        PLASMA - ĐỘNG CƠ ĐIỆN (MOTOR) - MÁY KHOAN VÀ MÁY CHẾ BIẾN GỖ - MÁY NGÀNH THÉP - VẬT LIỆU MÀI MÒN--}}
{{--                        chuyên nghiệp có quy mô lớn nhất Việt Nam.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            --}}{{-- Feature 6 --}}
{{--            <div x-ref="feature_6"--}}
{{--                class="relative flex flex-wrap items-center justify-around gap-10 lg:justify-center lg:gap-x-32">--}}
{{--                <div class="absolute -bottom-20 -right-10 hidden lg:block">--}}
{{--                    <img x-ref="geometric_shape_6"--}}
{{--                        src="{{ Vite::asset('resources/images/home/geometric-shape-6.webp') }}" alt="Shape"--}}
{{--                        class="block w-14" />--}}
{{--                </div>--}}

{{--                --}}{{-- Screenshot --}}
{{--                <div--}}
{{--                    class="relative h-80 w-full max-w-[23rem] shrink-0 overflow-hidden rounded-3xl--}}
{{--                     bg-gradient-to-tl from-orange-400 to-orange-200 shadow-xl shadow-black/5--}}
{{--                     dark: hover:from-orange-500 hover:to-orange-300">--}}
{{--                    <div class="absolute left-5 top-5 w-[30rem] overflow-hidden rounded-lg shadow-xl">--}}
{{--                        <img src="{{ Vite::asset('resources/images/home/San-Ha-5.webp') }}" alt="Theme"--}}
{{--                            class="w-full" />--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                --}}{{-- Feature Notes --}}
{{--                <div>--}}
{{--                    <div class="relative inline-block">--}}
{{--                        <img src="{{ Vite::asset('resources/images/home/colorpalette.webp') }}" alt="Color palette"--}}
{{--                            class="w-16" />--}}
{{--                        <div class="absolute -bottom-4 left-4 -z-10 h-7 w-7 rounded-full bg-black/50 blur-md"></div>--}}
{{--                    </div>--}}
{{--                    --}}{{-- Title --}}
{{--                    <div class="max-w-[15rem] pt-5 text-2xl font-bold  dark:text-gray-100">--}}
{{--                        Dự án SAP Business One và iVend Retails cho Công ty SANHA.--}}
{{--                    </div>--}}

{{--                    --}}{{-- Description --}}
{{--                    <div class="max-w-xs pt-3 font-medium text-dolphin dark:text-white ">--}}
{{--                        Dự án Công ty SANHA được khởi động vào ngày 16/04/2020 với giải pháp triển khai đồng thời hai--}}
{{--                        giải pháp: ERP SAP B1 - Giải pháp quản lí tổng thể nguồn lực doanh nghiệp và POS - Giải pháp--}}
{{--                        quản lý chuỗi cửa bán hàng bán lẻ, siêu thị.--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}


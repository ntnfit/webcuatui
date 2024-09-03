<div x-cloak x-data="{}" class="mx-auto w-full max-w-screen-lg px-5 pt-20">
    <div x-data="{}" x-init="$nextTick(() => {
        if (reducedMotion) return
        gsap.timeline({
                scrollTrigger: {
                    trigger: $refs.header,
                    start: 'top bottom-=150px',
                },
            })
            .fromTo(
                $refs.header_introducing, {
                    autoAlpha: 0,
                    y: -30,
                }, {
                    autoAlpha: 1,
                    y: 0,
                    duration: 0.7,
                    ease: 'circ.out',
                },
            )
            .fromTo(
                $refs.header_new, {
                    autoAlpha: 0,
                    x: -30,
                    y: 30,
                }, {
                    autoAlpha: 1,
                    x: 0,
                    y: 0,
                    duration: 0.7,
                    ease: 'circ.out',
                },
                '>-0.6',
            )
            .fromTo(
                $refs.header_version3, {
                    autoAlpha: 0,
                    y: 30,
                }, {
                    autoAlpha: 1,
                    y: 0,
                    duration: 0.7,
                    ease: 'circ.out',
                },
                '>-0.6',
            )
            .fromTo(
                $refs.header_features, {
                    autoAlpha: 0,
                    x: 30,
                    y: 30,
                }, {
                    autoAlpha: 1,
                    x: 0,
                    y: 0,
                    duration: 0.7,
                    ease: 'circ.out',
                },
                '>-0.6',
            )
        gsap.fromTo(
            $refs.feature_1, {
                autoAlpha: 0,
                x: -20,
            }, {
                autoAlpha: 1,
                x: 0,
                duration: 0.7,
                ease: 'circ.out',
                scrollTrigger: {
                    trigger: $refs.feature_1,
                    start: 'top bottom-=150px',
                },
            },
        )
        gsap.fromTo(
            $refs.feature_2, {
                autoAlpha: 0,
                x: 20,
            }, {
                autoAlpha: 1,
                x: 0,
                duration: 0.7,
                ease: 'circ.out',
                scrollTrigger: {
                    trigger: $refs.feature_2,
                    start: 'top bottom-=150px',
                },
            },
        )
        gsap.fromTo(
            $refs.feature_3, {
                autoAlpha: 0,
                x: -20,
            }, {
                autoAlpha: 1,
                x: 0,
                duration: 0.7,
                ease: 'circ.out',
                scrollTrigger: {
                    trigger: $refs.feature_3,
                    start: 'top bottom-=150px',
                },
            },
        )
        gsap.fromTo(
            $refs.feature_4, {
                autoAlpha: 0,
                x: 20,
            }, {
                autoAlpha: 1,
                x: 0,
                duration: 0.7,
                ease: 'circ.out',
                scrollTrigger: {
                    trigger: $refs.feature_4,
                    start: 'top bottom-=150px',
                },
            },
        )
        gsap.fromTo(
            $refs.feature_5, {
                autoAlpha: 0,
                x: -20,
            }, {
                autoAlpha: 1,
                x: 0,
                duration: 0.7,
                ease: 'circ.out',
                scrollTrigger: {
                    trigger: $refs.feature_5,
                    start: 'top bottom-=150px',
                },
            },
        )
        gsap.fromTo(
            $refs.feature_6, {
                autoAlpha: 0,
                x: 20,
            }, {
                autoAlpha: 1,
                x: 0,
                duration: 0.7,
                ease: 'circ.out',
                scrollTrigger: {
                    trigger: $refs.feature_6,
                    start: 'top bottom-=150px',
                },
            },
        )
        gsap.to($refs.geometric_shape_1, {
            yPercent: -100,
            rotate: 100,
            scrollTrigger: {
                trigger: $refs.feature_1,
                scrub: 1.5,
                start: 'top bottom-=200px',
                end: 'bottom+=300px center',
            },
        })
        gsap.to($refs.geometric_shape_2, {
            yPercent: -100,
            xPercent: -50,
            rotate: 180,
            scrollTrigger: {
                trigger: $refs.feature_2,
                scrub: 1.5,
                start: 'top bottom-=200px',
                end: 'bottom+=300px center',
            },
        })
        gsap.to($refs.geometric_shape_3, {
            yPercent: -100,
            xPercent: -30,
            rotate: 100,
            scrollTrigger: {
                trigger: $refs.feature_3,
                scrub: 1.5,
                start: 'top bottom-=200px',
                end: 'bottom+=300px center',
            },
        })
        gsap.to($refs.geometric_shape_4, {
            yPercent: -100,
            xPercent: -30,
            rotate: 100,
            scrollTrigger: {
                trigger: $refs.feature_4,
                scrub: 1.5,
                start: 'top bottom-=200px',
                end: 'bottom+=300px center',
            },
        })
        gsap.to($refs.geometric_shape_5, {
            yPercent: -100,
            xPercent: -50,
            rotate: 100,
            scrollTrigger: {
                trigger: $refs.feature_5,
                scrub: 1.5,
                start: 'top bottom-=200px',
                end: 'bottom+=300px center',
            },
        })
        gsap.to($refs.geometric_shape_6, {
            yPercent: -100,
            xPercent: -50,
            rotate: 45,
            scrollTrigger: {
                trigger: $refs.feature_6,
                scrub: 1.5,
                start: 'top bottom-=200px',
                end: 'bottom+=500px center',
            },
        })
    })">
        <div x-ref="header" class="text-center">
            <div x-ref="header_introducing" class="font-medium text-dolphin">
                Giới thiệu
            </div>
            <div class="pt-2 text-2xl sm:text-3xl">
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
        <div x-ref="features" class="space-y-32 pt-20">
            {{-- Feature 1 --}}
            <div x-ref="feature_1"
                class="relative flex flex-wrap items-center justify-around gap-10 lg:justify-center lg:gap-x-32">
                <div class="absolute -left-10 top-40 hidden lg:block">
                    <img x-ref="geometric_shape_1"
                        src="{{ Vite::asset('resources/images/home/geometric-shape-1.webp') }}" alt="Shape"
                        class="block w-14" />
                </div>

                {{-- Screenshot --}}
                <div
                    class="relative h-80 w-full max-w-[23rem] shrink-0 overflow-hidden rounded-3xl bg-gradient-to-tl from-orange-400 to-orange-200 shadow-xl shadow-black/5">
                    <div class="absolute left-5 top-5 w-[22rem] overflow-hidden rounded-lg shadow-xl">
                        <img src="{{ Vite::asset('resources/images/home/Bosch.png') }}" alt="Action modals"
                            class="w-full" />
                    </div>
                </div>

                {{-- Feature Notes --}}
                <div>
                    <div class="relative inline-block">
                        <img src="{{ Vite::asset('resources/images/home/handpoint.webp') }}" alt="Hand pointing"
                            class="w-12" />
                        <div class="absolute -bottom-4 left-4 -z-10 h-7 w-7 rounded-full bg-black/50 blur-md"></div>
                    </div>
                    {{-- Title --}}
                    <div class="max-w-[15rem] pt-5 text-2xl font-bold">
                        Dự án nội bộ (Bosch).
                    </div>

                    {{-- Description --}}
                    <div class="max-w-xs pt-3 font-medium text-dolphin">
                        Sử dụng HANA Modeling & UI5 triển khai hệ thống FI(S4/HANA).
                        Fast support cho dự án nội bộ phân hệ PP(S4/Hana).
                        Family UI5 Expert.
                    </div>
                </div>
            </div>

            {{-- Feature 2 --}}
            <div x-ref="feature_2"
                class="relative flex flex-wrap items-center justify-around gap-10 lg:justify-center lg:gap-x-32">
                <div class="absolute -right-16 top-40 hidden lg:block">
                    <img x-ref="geometric_shape_2"
                        src="{{ Vite::asset('resources/images/home/geometric-shape-2.webp') }}" alt="Shape"
                        class="block w-14" />
                </div>

                {{-- Screenshot --}}
                <div
                    class="relative h-80 w-full max-w-[23rem] shrink-0 overflow-hidden rounded-3xl bg-gradient-to-tl from-orange-400 to-orange-200 shadow-xl shadow-black/5">
                    <div class="absolute left-5 top-5 w-[30rem] overflow-hidden rounded-lg shadow-xl">
                        <img src="{{ Vite::asset('resources/images/home/cj.webp') }}" alt="Table report"
                            class="w-full" />
                    </div>
                </div>

                {{-- Feature Notes --}}
                <div>
                    <div class="relative inline-block">
                        <img src="{{ Vite::asset('resources/images/home/report.webp') }}" alt="Report"
                            class="w-16" />
                        <div class="absolute -bottom-4 left-4 -z-10 h-7 w-7 rounded-full bg-black/50 blur-md"></div>
                    </div>
                    {{-- Title --}}
                    <div class="max-w-[15rem] pt-5 text-2xl font-bold">
                        Go live dự án SAP B1 tại công ty CJ Olivenetworks Vina.
                    </div>

                    {{-- Description --}}
                    <div class="max-w-xs pt-3 font-medium text-dolphin">
                        Công ty Grant Thornton Việt Nam vừa hoàn thành dự án triển khai SAP Business One tại công ty CJ
                        Olivenetworks Vina, một dự án đầy thử thách và thành công. Sau 4 tháng triển khai, dự án đã
                        chính thức Go-live
                    </div>
                </div>
            </div>

            {{-- Feature 3 --}}
            <div x-ref="feature_3"
                class="relative flex flex-wrap items-center justify-around gap-10 lg:justify-center lg:gap-x-32">
                <div class="absolute -left-5 top-40 hidden lg:block">
                    <img x-ref="geometric_shape_3"
                        src="{{ Vite::asset('resources/images/home/geometric-shape-3.webp') }}" alt="Shape"
                        class="block w-16" />
                </div>

                {{-- Screenshot --}}
                <div
                    class="relative h-80 w-full max-w-[23rem] shrink-0 overflow-hidden rounded-3xl bg-gradient-to-tl from-orange-400 to-orange-200 shadow-xl shadow-black/5">
                    <div class="absolute left-5 top-5 w-[30rem] overflow-hidden rounded-lg shadow-xl">
                        <img src="{{ Vite::asset('resources/images/home/betagen.webp') }}" alt="Tenant switcher"
                            class="w-full" />
                    </div>
                </div>

                {{-- Feature Notes --}}
                <div>
                    <div class="relative inline-block">
                        <img src="{{ Vite::asset('resources/images/home/cloud.webp') }}" alt="Cloud"
                            class="w-20" />
                        <div class="absolute -bottom-4 left-4 -z-10 h-7 w-7 rounded-full bg-black/50 blur-md"></div>
                    </div>
                    {{-- Title --}}
                    <div class="max-w-[15rem] pt-5 text-2xl font-bold">
                        Nâng cấp SAP Business One cho Betagen Việt Nam lên nền tảng HANA Engine.
                    </div>

                    {{-- Description --}}
                    <div class="max-w-xs pt-3 font-medium text-dolphin">
                        Công Ty TNHH BETAGEN VIỆT NAM, đã chính thức khởi động dự án nâng cấp giải pháp Quản trị tổng
                        thể doanh nghiệp SAP Business One tại Việt Nam.
                    </div>
                </div>
            </div>

            {{-- Feature 4 --}}
            <div x-ref="feature_4"
                class="relative flex flex-wrap items-center justify-around gap-10 lg:justify-center lg:gap-x-32">
                <div class="absolute -right-10 top-40 hidden lg:block">
                    <img x-ref="geometric_shape_4"
                        src="{{ Vite::asset('resources/images/home/geometric-shape-4.webp') }}" alt="Shape"
                        class="block w-12" />
                </div>

                {{-- Screenshot --}}
                <div
                    class="relative h-80 w-full max-w-[23rem] shrink-0 overflow-hidden rounded-3xl bg-gradient-to-tl from-orange-400 to-orange-200 shadow-xl shadow-black/5">
                    <div class="absolute left-5 top-5 w-[30rem] overflow-hidden rounded-lg shadow-xl">
                        <img src="{{ Vite::asset('resources/images/home/viethung.webp') }}" alt="Infolist"
                            class="w-full" />
                    </div>
                </div>

                {{-- Feature Notes --}}
                <div>
                    <div class="relative inline-block">
                        <img src="{{ Vite::asset('resources/images/home/featherpaper.webp') }}" alt="Quill on paper"
                            class="w-16" />
                        <div class="absolute -bottom-4 left-4 -z-10 h-7 w-7 rounded-full bg-black/50 blur-md"></div>
                    </div>
                    {{-- Title --}}
                    <div class="max-w-[15rem] pt-5 text-2xl font-bold">
                        Dự án SAP Business One cho Công Ty Cổ Phần Bao Bì Việt Hưng Sài Gòn.
                    </div>

                    {{-- Description --}}
                    <div class="max-w-xs pt-3 font-medium text-dolphin">
                        Công ty Cổ Phần Bao Bì Việt Hưng Sài Gòn (VHSG) là chi nhánh của công ty Bao Bì Việt Hưng, có
                        tổng diện tích 60.000 m2, trong đó 40.000 m2 được sử dụng làm diện tích nhà xưởng.
                    </div>
                </div>
            </div>

            {{-- Feature 5 --}}
            <div x-ref="feature_5"
                class="relative flex flex-wrap items-center justify-around gap-10 lg:justify-center lg:gap-x-32">
                <div class="absolute bottom-0 left-0 hidden lg:block">
                    <img x-ref="geometric_shape_5"
                        src="{{ Vite::asset('resources/images/home/geometric-shape-5.webp') }}" alt="Shape"
                        class="block w-14" />
                </div>

                {{-- Screenshot --}}
                <div
                    class="relative h-80 w-full max-w-[23rem] shrink-0 overflow-hidden rounded-3xl bg-gradient-to-tl from-orange-400 to-orange-200 shadow-xl shadow-black/5">
                    <div class="absolute left-5 top-5 w-[30rem] overflow-hidden rounded-lg shadow-xl">
                        <img src="{{ Vite::asset('resources/images/home/hongky.webp') }}" alt="Multiple panels"
                            class="w-full" />
                    </div>
                </div>

                {{-- Feature Notes --}}
                <div>
                    <div class="relative inline-block">
                        <img src="{{ Vite::asset('resources/images/home/infinity.webp') }}" alt="Infinity"
                            class="w-16" />
                        <div class="absolute -bottom-4 left-4 -z-10 h-7 w-7 rounded-full bg-black/50 blur-md"></div>
                    </div>
                    {{-- Title --}}
                    <div class="max-w-[15rem] pt-5 text-2xl font-bold">
                        Dự án SAP Business One cho Công ty Cơ khí Hồng ký.
                    </div>

                    {{-- Description --}}
                    <div class="max-w-xs pt-3 font-medium text-dolphin">
                        Cơ khí Hồng Ký là đơn vị sản xuất MÁY BIẾN THẾ HÀN - MÁY HÀN ĐIỆN TỬ TIG/MIG/INVERTER - MÁY CẮT
                        PLASMA - ĐỘNG CƠ ĐIỆN (MOTOR) - MÁY KHOAN VÀ MÁY CHẾ BIẾN GỖ - MÁY NGÀNH THÉP - VẬT LIỆU MÀI MÒN
                        chuyên nghiệp có quy mô lớn nhất Việt Nam.
                    </div>
                </div>
            </div>

            {{-- Feature 6 --}}
            <div x-ref="feature_6"
                class="relative flex flex-wrap items-center justify-around gap-10 lg:justify-center lg:gap-x-32">
                <div class="absolute -bottom-20 -right-10 hidden lg:block">
                    <img x-ref="geometric_shape_6"
                        src="{{ Vite::asset('resources/images/home/geometric-shape-6.webp') }}" alt="Shape"
                        class="block w-14" />
                </div>

                {{-- Screenshot --}}
                <div
                    class="relative h-80 w-full max-w-[23rem] shrink-0 overflow-hidden rounded-3xl bg-gradient-to-tl from-orange-400 to-orange-200 shadow-xl shadow-black/5">
                    <div class="absolute left-5 top-5 w-[30rem] overflow-hidden rounded-lg shadow-xl">
                        <img src="{{ Vite::asset('resources/images/home/San-Ha-5.webp') }}" alt="Theme"
                            class="w-full" />
                    </div>
                </div>

                {{-- Feature Notes --}}
                <div>
                    <div class="relative inline-block">
                        <img src="{{ Vite::asset('resources/images/home/colorpalette.webp') }}" alt="Color palette"
                            class="w-16" />
                        <div class="absolute -bottom-4 left-4 -z-10 h-7 w-7 rounded-full bg-black/50 blur-md"></div>
                    </div>
                    {{-- Title --}}
                    <div class="max-w-[15rem] pt-5 text-2xl font-bold">
                        Dự án SAP Business One và iVend Retails cho Công ty SANHA.
                    </div>

                    {{-- Description --}}
                    <div class="max-w-xs pt-3 font-medium text-dolphin">
                        Dự án Công ty SANHA được khởi động vào ngày 16/04/2020 với giải pháp triển khai đồng thời hai
                        giải pháp: ERP SAP B1 - Giải pháp quản lí tổng thể nguồn lực doanh nghiệp và POS - Giải pháp
                        quản lý chuỗi cửa bán hàng bán lẻ, siêu thị.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

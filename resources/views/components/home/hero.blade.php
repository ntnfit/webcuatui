<div x-cloak x-data="{}"
    class="mx-auto w-full max-w-screen-lg overflow-x-clip px-5 pt-20 min-[550px]:px-10 sm:overflow-x-visible">
    <div class="relative flex items-start justify-center min-[550px]:justify-start lg:gap-40 xl:justify-between">
        {{-- Left Side --}}
        <div class="min-[500px]:pl-10 sm:shrink-0 sm:pl-14 xl:pl-0">
            {{-- Accelerated --}}
            <div class="relative translate-x-10 text-3xl font-black italic min-[500px]:translate-x-0 lg:text-4xl"
                x-data="{}" x-init="() => {
                    if (reducedMotion) return
                    gsap.timeline()
                        .fromTo(
                            $refs.supercar, {
                                autoAlpha: 1,
                                x: -450,
                            }, {
                                autoAlpha: 0,
                                x: 0,
                                duration: 1.1,
                                ease: 'circ.in',
                            },
                        )
                        .fromTo(
                            $refs.accelerated, {
                                autoAlpha: 0,
                                x: -100,
                            }, {
                                autoAlpha: 1,
                                x: 0,
                                duration: 0.5,
                                ease: 'circ.out',
                            },
                            '>-0.1',
                        )
                        .fromTo(
                            $refs.shadow, {
                                autoAlpha: 0,
                                x: -100,
                            }, {
                                autoAlpha: 1,
                                x: 0,
                                duration: 0.7,
                                ease: 'circ.out',
                            },
                            '<0.01',
                        )
                        .fromTo(
                            $refs.line1, {
                                autoAlpha: 0,
                                x: -50,
                            }, {
                                autoAlpha: 1,
                                x: 0,
                                duration: 0.3,
                                ease: 'circ.out',
                            },
                            '<0.2',
                        )
                        .fromTo(
                            $refs.line2, {
                                autoAlpha: 0,
                                x: -50,
                            }, {
                                autoAlpha: 1,
                                x: 0,
                                duration: 0.3,
                                ease: 'circ.out',
                            },
                            '<0.02',
                        )
                        .fromTo(
                            $refs.line3, {
                                autoAlpha: 0,
                                x: -50,
                            }, {
                                autoAlpha: 1,
                                x: 0,
                                duration: 0.3,
                                ease: 'circ.out',
                            },
                            '<0.02',
                        )
                }">
                {{-- Title --}}
                <div x-ref="accelerated"
                    class="bg-gradient-to-r from-[#FFB46F] to-[#B9C0B9] bg-clip-text text-transparent">
                    Web của tớ
                </div>

                {{-- Shadow --}}
                <div x-ref="shadow" class="absolute -left-2 top-1 -z-10 select-none text-[#FFEFE1]">
                    Web của tớ
                </div>

                {{-- Speed Lines --}}
                <div class="absolute -left-12 top-1/2 -translate-y-1/2 space-y-1">
                    <div class="translate-x-5">
                        <div x-ref="line1"
                            class="h-0.5 w-7 rounded-full bg-gradient-to-r from-transparent to-[#FFC089]"></div>
                    </div>
                    <div>
                        <div x-ref="line2"
                            class="h-0.5 w-10 rounded-full bg-gradient-to-r from-transparent to-[#FFC089]"></div>
                    </div>
                    <div class="-translate-x-4">
                        <div x-ref="line3"
                            class="h-0.5 w-12 rounded-full bg-gradient-to-r from-transparent to-[#FFC089]"></div>
                    </div>
                </div>

                {{-- Supercar --}}
                <div class="absolute right-0 top-1/2 -translate-y-1/2">
                    <img x-ref="supercar" src="{{ Vite::asset('resources/images/home/supercar.webp') }}" alt="Car"
                        class="w-28 opacity-0" />
                </div>
            </div>

            {{-- Header --}}
            <div class="group/header pt-3" x-data="{}" x-init="() => {
                if (reducedMotion) return
                gsap.fromTo(
                    $refs.title, {
                        autoAlpha: 0,
                        x: 20,
                    }, {
                        autoAlpha: 1,
                        x: 0,
                        duration: 0.5,
                    },
                )
                gsap.fromTo(
                    $refs.description, {
                        autoAlpha: 0,
                        x: -20,
                    }, {
                        autoAlpha: 1,
                        x: 0,
                        duration: 0.5,
                    },
                )
                gsap.fromTo(
                    $refs.star1, {
                        autoAlpha: 0,
                        scale: 0,
                        rotate: 200,
                        x: 50,
                    }, {
                        autoAlpha: 1,
                        scale: 1,
                        rotate: 0,
                        x: 0,
                        duration: 0.8,
                        ease: 'expo.out',
                    },
                )
                gsap.fromTo(
                    $refs.star2, {
                        autoAlpha: 0,
                        scale: 0,
                        rotate: -200,
                        x: -60,
                    }, {
                        autoAlpha: 1,
                        scale: 1,
                        rotate: 0,
                        x: 0,
                        duration: 0.8,
                        ease: 'expo.out',
                    },
                )
            }">
                <div class="relative space-y-3 font-black">
                    {{-- Title --}}
                    <div x-ref="title">
                        <div class="relative max-w-fit text-6xl lg:text-7xl">
                            HARRY

                            {{-- Heart Shapes --}}
                            <div
                                class="absolute -right-7 -top-5 -translate-x-1 rotate-12 text-rose-500 opacity-0 duration-200 ease-out group-hover/header:translate-x-0 group-hover/header:opacity-100 motion-reduce:transition-none min-[500px]:-right-5 min-[500px]:-top-7">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M2 9.137C2 14 6.02 16.591 8.962 18.911C10 19.729 11 20.5 12 20.5s2-.77 3.038-1.59C17.981 16.592 22 14 22 9.138c0-4.863-5.5-8.312-10-3.636C7.5.825 2 4.274 2 9.137Z" />
                                </svg>
                            </div>
                            <div
                                class="absolute -right-12 top-2 -translate-x-1 -rotate-12 text-rose-500 opacity-0 transition delay-75 duration-200 ease-out group-hover/header:translate-x-0 group-hover/header:opacity-100 motion-reduce:transition-none min-[500px]:-right-10 min-[500px]:top-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="scale-75" width="24" height="24"
                                    viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M2 9.137C2 14 6.02 16.591 8.962 18.911C10 19.729 11 20.5 12 20.5s2-.77 3.038-1.59C17.981 16.592 22 14 22 9.138c0-4.863-5.5-8.312-10-3.636C7.5.825 2 4.274 2 9.137Z" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-4xl lg:text-5xl">
                            Code dạo và làm ERP
                            <span class="inline-block -translate-x-2 text-butter">
                                .
                            </span>
                        </div>
                    </div>

                    {{-- Star --}}
                    <div x-ref="star1" class="absolute -left-14 top-1 lg:-left-20">
                        <svg width="31" height="31" fill="none"
                            class="hidden scale-75 min-[450px]:block min-[500px]:scale-100"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="m17.664 29.354 3.06-8.755 8.679-3.27a2.183 2.183 0 0 0-.05-4.092l-8.754-3.06-3.27-8.679a2.183 2.183 0 0 0-4.092.05l-3.06 8.754-8.68 3.27a2.183 2.183 0 0 0 .05 4.092l8.755 3.06 3.27 8.679a2.183 2.183 0 0 0 4.092-.05Zm-5.325-9.391a2.142 2.142 0 0 0-1.325-1.294l-8.741-3.06 8.665-3.27a2.143 2.143 0 0 0 1.294-1.325l3.06-8.741 3.27 8.665a2.142 2.142 0 0 0 1.325 1.294l8.74 3.06-8.664 3.27a2.141 2.141 0 0 0-1.294 1.325l-3.06 8.741-3.27-8.665Z"
                                fill="#0F033A" />
                        </svg>
                    </div>
                </div>
                <div class="relative pt-5">
                    {{-- Description --}}
                    <div x-ref="description" class="text-xl font-medium leading-normal opacity-90 lg:text-2xl">
                        Nơi chia sẻ kiến thức về lập trình,
                        <br />
                        công nghệ và các dự án ERP
                        <br />
                        và Hơn thế nữa.
                    </div>

                    {{-- Star --}}
                    <div x-ref="star2" class="absolute -right-10 top-1 min-[500px]:right-10 lg:-right-5">
                        <svg width="31" height="31" fill="none" class="hidden scale-[0.65] min-[450px]:block"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="m17.664 29.354 3.06-8.755 8.679-3.27a2.183 2.183 0 0 0-.05-4.092l-8.754-3.06-3.27-8.679a2.183 2.183 0 0 0-4.092.05l-3.06 8.754-8.68 3.27a2.183 2.183 0 0 0 .05 4.092l8.755 3.06 3.27 8.679a2.183 2.183 0 0 0 4.092-.05Zm-5.325-9.391a2.142 2.142 0 0 0-1.325-1.294l-8.741-3.06 8.665-3.27a2.143 2.143 0 0 0 1.294-1.325l3.06-8.741 3.27 8.665a2.142 2.142 0 0 0 1.325 1.294l8.74 3.06-8.664 3.27a2.141 2.141 0 0 0-1.294 1.325l-3.06 8.741-3.27-8.665Z"
                                fill="#0F033A" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Links --}}
            <div x-data="{}" x-init="() => {
                if (reducedMotion) return
                gsap.fromTo(
                    $refs.getstarted, {
                        autoAlpha: 0,
                        x: -10,
                        y: 10,
                    }, {
                        autoAlpha: 1,
                        x: 0,
                        y: 0,
                        duration: 0.5,
                    },
                )
                gsap.fromTo(
                    $refs.discord, {
                        autoAlpha: 0,
                        x: 10,
                        y: -10,
                    }, {
                        autoAlpha: 1,
                        x: 0,
                        y: 0,
                        duration: 0.5,
                    },
                )
            }"
                class="flex flex-col gap-5 pt-10 text-white min-[500px]:flex-row min-[500px]:items-center">
                <a x-ref="getstarted" href="" class="group relative block">
                    {{-- Button --}}
                    <div
                        class="flex items-center justify-center gap-3 rounded-bl-3xl rounded-tr-3xl bg-midnight px-9 py-4 transition duration-200 will-change-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5 motion-reduce:transition-none">
                        <div>Bắt đầu thôi</div>
                        <div
                            class="transition duration-300 will-change-transform group-hover:translate-x-1 motion-reduce:transition-none">
                            <svg width="24" height="25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 12.992h2.5m13.5 0-6-6m6 6-6 6m6-6H9.5" stroke="#fff" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>

                    {{-- Shadow --}}
                    <div
                        class="absolute inset-0 -z-10 h-full w-full -translate-x-1.5 translate-y-1.5 rounded-bl-3xl rounded-tr-3xl bg-butter transition duration-300 will-change-transform group-hover:-translate-x-2 group-hover:translate-y-2 group-hover:bg-rose-300 motion-reduce:transition-none">
                    </div>
                </a>
                <a x-ref="zalo" href="https://zalo.me/0981710031" target="_blank" class="group relative block">
                    {{-- Button --}}
                    <div
                        class="flex items-center justify-center gap-3 rounded-br-3xl rounded-tl-3xl bg-butter px-9 py-4 transition duration-200 will-change-transform group-hover:-translate-x-0.5 group-hover:-translate-y-0.5 motion-reduce:transition-none">
                        <div>Liên hệ</div>
                        <div
                            class="transition duration-300 will-change-transform group-hover:scale-105 motion-reduce:transition-none">

                            <svg xmlns="http://www.w3.org/2000/svg"class="w-5" fill="none" viewBox="0 0 100 100">
                                <path fill="#3a83c1"
                                    d="M69,82H31c-7.18,0-13-5.82-13-13V31c0-7.18,5.82-13,13-13h38c7.18,0,13,5.82,13,13v38 C82,76.18,76.18,82,69,82z">
                                </path>
                                <path fill="#3070b7"
                                    d="M82,63.61V69c0,7.18-5.82,13-13,13H36.3l-8.15-8.15C28.06,73.76,28,73.64,28,73.5l0.92,0.26 c0.59,0.3,1.92,0.74,4.58,0.74c3.54,0,6.25-1.24,9.2-2.12c1.08-0.33,2.25-0.3,3.31,0.09c3.57,1.31,7.44,2.03,11.49,2.03 C67.34,74.5,76.13,70.26,82,63.61z">
                                </path>
                                <path fill="#fefdef"
                                    d="M82,31v33.24C75.81,71,66.92,74.88,57.5,74.88c-4.01,0-7.94-0.7-11.66-2.07 c-0.95-0.35-2.02-0.37-2.99-0.08c-0.67,0.2-1.31,0.42-1.96,0.63c-2.24,0.74-4.57,1.52-7.39,1.52c-4.09,0-5.24-1.03-5.35-1.15 c-0.12-0.12-0.17-0.29-0.14-0.45c0.03-0.16,0.14-0.3,0.29-0.36c0.04-0.02,4.34-1.92,6.02-5.55c0.33-0.73,0.17-1.6-0.41-2.18 C28.16,59.4,25,51.83,25,43.88C25,33.36,30.43,23.77,39.59,18H69C76.18,18,82,23.82,82,31z">
                                </path>
                                <path fill="#3a83c1"
                                    d="M71.5,40c-3.309,0-6,2.916-6,6.5s2.691,6.5,6,6.5s6-2.916,6-6.5S74.809,40,71.5,40z M71.5,49.5 c-1.309,0-2.375-1.346-2.375-3s1.066-3,2.375-3s2.375,1.346,2.375,3S72.809,49.5,71.5,49.5z">
                                </path>
                                <path fill="#3a83c1"
                                    d="M61.75,35.5c-0.966,0-1.75,0.783-1.75,1.75v14c0,0.967,0.784,1.75,1.75,1.75s1.75-0.783,1.75-1.75 v-14C63.5,36.283,62.716,35.5,61.75,35.5z">
                                </path>
                                <path fill="#3a83c1"
                                    d="M54.5,46.5c0,1.657-1.119,3-2.5,3s-2.5-1.343-2.5-3s1.119-3,2.5-3S54.5,44.843,54.5,46.5z M56.25,40.25c-0.732,0-1.357,0.45-1.618,1.088C53.727,40.502,52.601,40,51.375,40C48.406,40,46,42.91,46,46.5s2.406,6.5,5.375,6.5 c1.226,0,2.352-0.502,3.257-1.338c0.261,0.638,0.887,1.088,1.618,1.088c0.966,0,1.75-0.783,1.75-1.75v-9 C58,41.033,57.216,40.25,56.25,40.25z">
                                </path>
                                <path fill="#3a83c1"
                                    d="M43.25,49h-5.667l6.732-9.756c0.549-0.796,0.349-1.886-0.447-2.435 c-0.302-0.208-0.646-0.308-0.987-0.309c-0.002,0-0.005-0.001-0.007-0.001H34.5c-0.897,0-1.625,0.728-1.625,1.625 s0.728,1.625,1.625,1.625h5.214L32.81,49.756c-0.369,0.535-0.411,1.231-0.109,1.808S33.6,52.5,34.25,52.5h9 c0.966,0,1.75-0.783,1.75-1.75S44.216,49,43.25,49z">
                                </path>
                                <g>
                                    <path fill="#1f212b"
                                        d="M71.5,53.5c-3.584,0-6.5-3.141-6.5-7s2.916-7,6.5-7s6.5,3.141,6.5,7S75.084,53.5,71.5,53.5z M71.5,40.5c-3.033,0-5.5,2.691-5.5,6s2.467,6,5.5,6s5.5-2.691,5.5-6S74.533,40.5,71.5,40.5z M71.5,50 c-1.585,0-2.875-1.57-2.875-3.5S69.915,43,71.5,43s2.875,1.57,2.875,3.5S73.085,50,71.5,50z M71.5,44 c-1.034,0-1.875,1.121-1.875,2.5S70.466,49,71.5,49s1.875-1.121,1.875-2.5S72.534,44,71.5,44z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M61.75,53.5c-1.241,0-2.25-1.01-2.25-2.25v-14c0-1.24,1.009-2.25,2.25-2.25S64,36.01,64,37.25v14 C64,52.49,62.991,53.5,61.75,53.5z M61.75,36c-0.689,0-1.25,0.561-1.25,1.25v14c0,0.689,0.561,1.25,1.25,1.25S63,51.939,63,51.25 v-14C63,36.561,62.439,36,61.75,36z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M51.375,53.5c-3.239,0-5.875-3.141-5.875-7s2.636-7,5.875-7c1.121,0,2.192,0.371,3.13,1.08 c0.419-0.518,1.054-0.83,1.745-0.83c1.241,0,2.25,1.01,2.25,2.25v9c0,1.24-1.009,2.25-2.25,2.25c-0.691,0-1.325-0.312-1.745-0.83 C53.567,53.129,52.496,53.5,51.375,53.5z M51.375,40.5c-2.688,0-4.875,2.691-4.875,6s2.187,6,4.875,6 c1.056,0,2.064-0.417,2.917-1.205c0.12-0.11,0.289-0.156,0.447-0.121c0.16,0.035,0.292,0.147,0.355,0.299 c0.193,0.473,0.647,0.777,1.155,0.777c0.689,0,1.25-0.561,1.25-1.25v-9c0-0.689-0.561-1.25-1.25-1.25 c-0.508,0-0.962,0.305-1.155,0.777c-0.062,0.151-0.195,0.264-0.355,0.299c-0.158,0.034-0.327-0.01-0.447-0.121 C53.439,40.917,52.431,40.5,51.375,40.5z M52,50c-1.654,0-3-1.57-3-3.5s1.346-3.5,3-3.5s3,1.57,3,3.5S53.654,50,52,50z M52,44 c-1.103,0-2,1.121-2,2.5s0.897,2.5,2,2.5s2-1.121,2-2.5S53.103,44,52,44z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M43.25,53h-9c-0.839,0-1.603-0.462-1.992-1.204c-0.39-0.743-0.336-1.634,0.14-2.324l6.363-9.222 H34.5c-1.172,0-2.125-0.953-2.125-2.125S33.328,36,34.5,36h8.375c0.011,0,0.022,0,0.034,0.001c0.447,0.007,0.876,0.144,1.244,0.396 c0.495,0.342,0.827,0.855,0.936,1.446c0.108,0.592-0.02,1.189-0.361,1.685L38.536,48.5h4.714c1.241,0,2.25,1.01,2.25,2.25 S44.491,53,43.25,53z M34.5,37c-0.62,0-1.125,0.505-1.125,1.125S33.88,39.25,34.5,39.25h5.214c0.186,0,0.356,0.104,0.443,0.268 c0.086,0.165,0.074,0.363-0.031,0.517L33.221,50.04c-0.265,0.384-0.294,0.878-0.078,1.291C33.36,51.744,33.784,52,34.25,52h9 c0.689,0,1.25-0.561,1.25-1.25s-0.561-1.25-1.25-1.25h-5.667c-0.186,0-0.356-0.104-0.443-0.268 c-0.086-0.165-0.074-0.363,0.031-0.517l6.732-9.756c0.392-0.567,0.249-1.348-0.319-1.739c-0.216-0.147-0.449-0.192-0.733-0.221 H34.5z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M57.5,74.875c-4.013,0-7.937-0.693-11.663-2.061c-0.952-0.351-2.015-0.379-2.991-0.081 c-0.662,0.197-1.309,0.412-1.956,0.627c-2.245,0.744-4.566,1.515-7.391,1.515c-4.089,0-5.236-1.029-5.354-1.146 s-0.168-0.284-0.138-0.447c0.031-0.162,0.141-0.299,0.292-0.365c0.043-0.019,4.339-1.916,6.015-5.55 c0.337-0.722,0.172-1.596-0.41-2.178C28.163,59.402,25,51.834,25,43.875c0-10.864,5.801-20.745,15.518-26.432 c0.236-0.139,0.544-0.06,0.684,0.179c0.139,0.238,0.059,0.545-0.179,0.685C31.616,23.812,26,33.369,26,43.875 c0,7.693,3.06,15.012,8.615,20.607c0.877,0.877,1.122,2.205,0.608,3.305c-1.359,2.945-4.17,4.796-5.621,5.594 c0.677,0.233,1.885,0.494,3.898,0.494c2.663,0,4.806-0.711,7.076-1.464c0.656-0.218,1.313-0.436,1.981-0.636 c1.18-0.359,2.469-0.325,3.625,0.1c3.615,1.327,7.423,2,11.318,2c2.655,0,5.307-0.321,7.88-0.955 c0.269-0.064,0.539,0.098,0.605,0.366c0.066,0.268-0.098,0.539-0.366,0.604C62.968,74.544,60.236,74.875,57.5,74.875z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M74.5,70.208c-0.17,0-0.335-0.087-0.429-0.243c-0.142-0.236-0.065-0.544,0.172-0.686 c0.332-0.199,0.661-0.404,0.984-0.614c0.233-0.15,0.542-0.085,0.692,0.146c0.151,0.231,0.085,0.541-0.146,0.691 c-0.334,0.218-0.673,0.429-1.016,0.634C74.676,70.186,74.588,70.208,74.5,70.208z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M69.5,72.638c-0.201,0-0.39-0.121-0.466-0.319c-0.1-0.258,0.028-0.548,0.286-0.647 c1.083-0.418,2.049-0.846,2.954-1.305c0.244-0.123,0.547-0.027,0.672,0.22c0.125,0.246,0.027,0.548-0.22,0.673 c-0.935,0.474-1.931,0.914-3.046,1.346C69.621,72.627,69.56,72.638,69.5,72.638z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M69,83H31c-7.72,0-14-6.28-14-14V31c0-7.72,6.28-14,14-14h38c7.72,0,14,6.28,14,14v38 C83,76.72,76.72,83,69,83z M31,19c-6.617,0-12,5.383-12,12v38c0,6.617,5.383,12,12,12h38c6.617,0,12-5.383,12-12V31 c0-6.617-5.383-12-12-12H31z">
                                    </path>
                                </g>
                            </svg>
                        </div>
                    </div>

                    {{-- Shadow --}}
                    <div
                        class="absolute inset-0 -z-10 h-full w-full translate-x-1.5 translate-y-1.5 rounded-br-3xl rounded-tl-3xl bg-[#F1E3E3] transition duration-300 will-change-transform group-hover:translate-x-2 group-hover:translate-y-2 group-hover:bg-stone-200 motion-reduce:transition-none">
                    </div>
                </a>
            </div>

            {{-- Decoration Arrow --}}
            <div x-data="{}" x-init="() => {
                if (reducedMotion) return
                gsap.fromTo(
                    $refs.arrow, {
                        autoAlpha: 0,
                        x: -10,
                    }, {
                        autoAlpha: 1,
                        x: 0,
                        duration: 0.5,
                        ease: 'circ.out',
                        delay: 0.2,
                    },
                )
            }"
                class="hidden -translate-x-16 pt-2 min-[500px]:block lg:-translate-x-32">
                <img x-ref="arrow" src="{{ Vite::asset('resources/svg/home/decoration-up-arrow-red.svg') }}"
                    alt="Up arrow" class="w-32" />
            </div>
        </div>

        {{-- Right Side --}}
        <div class="absolute -top-10 right-10 -z-10 hidden min-[500px]:block md:relative md:right-auto md:top-auto">
            <div x-data="{}" x-init="() => {
                if (reducedMotion) return
                gsap.timeline()
                    .fromTo(
                        $refs.rocket, {
                            autoAlpha: 0,
                            scale: 0.9,
                            x: -50,
                            y: 50,
                        }, {
                            autoAlpha: 1,
                            scale: 1,
                            x: 0,
                            y: 0,
                            duration: 0.8,
                            ease: 'circ.out',
                        },
                    )
                    .to($refs.rocket, {
                        keyframes: {
                            x: [0, 20],
                            y: [0, -20],
                        },
                        duration: 5,
                        repeat: -1,
                        yoyo: true,
                    })
                gsap.timeline()
                    .fromTo(
                        $refs.rocket2, {
                            autoAlpha: 0,
                            scale: 0.9,
                            x: -50,
                            y: 50,
                        }, {
                            autoAlpha: 1,
                            scale: 1,
                            x: 0,
                            y: 0,
                            duration: 0.8,
                            ease: 'circ.out',
                        },
                    )
                    .to($refs.rocket2, {
                        keyframes: {
                            x: [0, 20],
                            y: [0, -20],
                        },
                        duration: 5,
                        repeat: -1,
                        yoyo: true,
                    })
                gsap.timeline()
                    .fromTo(
                        $refs.rocket3, {
                            autoAlpha: 0,
                            scale: 0.9,
                            x: -50,
                            y: 50,
                        }, {
                            autoAlpha: 1,
                            scale: 1,
                            x: 0,
                            y: 0,
                            duration: 0.8,
                            ease: 'circ.out',
                        },
                    )
                    .to($refs.rocket3, {
                        keyframes: {
                            x: [0, 20],
                            y: [0, -20],
                        },
                        duration: 5,
                        repeat: -1,
                        yoyo: true,
                    })
                gsap.timeline()
                    .fromTo(
                        $refs.rocket4, {
                            autoAlpha: 0,
                            scale: 0.9,
                            x: -50,
                            y: 50,
                        }, {
                            autoAlpha: 1,
                            scale: 1,
                            x: 0,
                            y: 0,
                            duration: 0.8,
                            ease: 'circ.out',
                        },
                    )
                    .to($refs.rocket4, {
                        keyframes: {
                            x: [0, 20],
                            y: [0, -20],
                        },
                        duration: 5,
                        repeat: -1,
                        yoyo: true,
                    })
                gsap.timeline()
                    .fromTo(
                        $refs.circle1, {
                            autoAlpha: 0,
                            scale: 0,
                        }, {
                            autoAlpha: 1,
                            scale: 1,
                            duration: 0.7,
                            ease: 'back.out',
                        },
                    )
                    .fromTo(
                        $refs.circle2, {
                            autoAlpha: 0,
                            scale: 0,
                        }, {
                            autoAlpha: 1,
                            scale: 1,
                            duration: 0.7,
                            ease: 'back.out',
                        },
                        '<0.1',
                    )
            }" class="relative">
                {{-- Rocket --}}
                <div class="flex flex-wrap w-32 min-[550px]:w-40 md:w-60 lg:w-80">
                    <div class="w-1/2 p-1">
                        <img x-ref="rocket" src="{{ Vite::asset('resources/images/home/sap.jpg') }}" alt="sap"
                            class="w-full" />
                    </div>
                    <div class="w-1/2 p-1">
                        <img x-ref="rocket2" src="{{ Vite::asset('resources/images/home/netsuite.jpg') }}"
                            alt="netsuite" class="w-full" />
                    </div>
                    <div class="w-1/2 p-1">
                        <img x-ref="rocket3" src="{{ Vite::asset('resources/images/home/webapp.jpg') }}"
                            alt="thiet ke web" class="w-full">
                    </div>
                    <div class="w-1/2 p-1">
                        <img x-ref="rocket4" src="{{ Vite::asset('resources/images/home/integrate.webp') }}"
                            alt="Rocket" class="w-full" />
                    </div>
                </div>

                {{-- Decoration Circles --}}
                <div x-ref="circle1"
                    class="absolute -right-4 bottom-0 hidden h-3 w-3 rounded-full bg-[#FFCEA0] min-[550px]:block md:h-4 md:w-4">
                </div>
                <div x-ref="circle2"
                    class="absolute -bottom-20 right-20 hidden h-5 w-5 rounded-full bg-[#FFE69A] min-[550px]:block md:h-7 md:w-7">
                </div>
            </div>
        </div>
    </div>
</div>

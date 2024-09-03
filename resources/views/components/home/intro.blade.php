<div x-cloak x-data="{}" class="mx-auto w-full max-w-screen-lg px-10 pt-40 lg:px-5">
    <div x-data="{}" x-init="() => {
        if (reducedMotion) return
        gsap.timeline({
                delay: 0.2,
                scrollTrigger: {
                    trigger: $refs.header,
                    start: 'top bottom',
                },
            })
            .fromTo(
                $refs.header, {
                    autoAlpha: 0,
                }, {
                    autoAlpha: 1,
                    duration: 0.7,
                    ease: 'circ.out',
                },
            )
            .fromTo(
                $refs.mockup, {
                    autoAlpha: 0,
                }, {
                    autoAlpha: 1,
                    duration: 0.7,
                    ease: 'circ.out',
                },
                '>-0.5',
            )
    }"
        class="flex flex-col items-center justify-center gap-20 md:flex-row md:gap-10 lg:justify-between">
        <div x-ref="header">
            {{-- Live Demo --}}
            <div class="text-3xl">
                <span>Freelancer</span>
                <span class="font-bold">SAP, NetSuite, Tích hợp, WebApp,...</span>
            </div>

            {{-- Description --}}
            <div class="min-w-[18rem] max-w-[22rem] pt-7 font-medium text-dolphin">
                Mình hiện tại cũng là 1 chuyên viên tư vấn các giải pháp từ SAP/WebApp & ERP khác.
                Blog này là nơi mình viết về những trải nghiệm hằng ngày. Chỉ sẻ những công nghệ, công cụ mình hay sử
                dụng cho các dư án.
                <br>
                Ngoài ra tớ có nhận các Job về <b>bảo trì, nâng cấp, quản trị</b> hệ thống SAP.
                Đào tạo về tích hợp hệ thống <b>SAP B1,Addon-on cho SAP B1, NetSuite, WebApp</b>,..v.v.
                <br />
                Cám ơn mọi người đã ghé thăm!!!
            </div>

            {{-- Links --}}
            <div class="flex flex-wrap items-center gap-5 pt-20">
                <a href="tel:+840981710031"
                    class="group/button flex items-center justify-center gap-3 rounded-xl bg-butter px-7 py-3 text-white transition duration-200 motion-reduce:transition-none">
                    <div>5$/giờ hỗ trợ <br>+84981710031 </div>
                    <div
                        class="transition duration-300 group-hover/button:translate-x-1 motion-reduce:transition-none motion-reduce:group-hover/button:transform-none">

                        <svg width="24" height="25" fill="none" data-name="24x24/On Light/Support"
                            xmlns="http://www.w3.org/2000/svg" fill="#9f33d1">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <rect id="view-box" width="24" height="24" fill="none"></rect>
                                <path id="Shape"
                                    d="M8,17.751a2.749,2.749,0,0,1,5.127-1.382C15.217,15.447,16,14,16,11.25v-3c0-3.992-2.251-6.75-5.75-6.75S4.5,4.259,4.5,8.25v3.5a.751.751,0,0,1-.75.75h-1A2.753,2.753,0,0,1,0,9.751v-1A2.754,2.754,0,0,1,2.75,6h.478c.757-3.571,3.348-6,7.022-6s6.264,2.429,7.021,6h.478a2.754,2.754,0,0,1,2.75,2.75v1a2.753,2.753,0,0,1-2.75,2.75H17.44A5.85,5.85,0,0,1,13.5,17.84,2.75,2.75,0,0,1,8,17.751Zm1.5,0a1.25,1.25,0,1,0,1.25-1.25A1.251,1.251,0,0,0,9.5,17.751Zm8-6.75h.249A1.251,1.251,0,0,0,19,9.751v-1A1.251,1.251,0,0,0,17.75,7.5H17.5Zm-16-2.25v1A1.251,1.251,0,0,0,2.75,11H3V7.5H2.75A1.251,1.251,0,0,0,1.5,8.751Z"
                                    transform="translate(1.75 2.25)" fill="#eb70e7"></path>
                            </g>
                        </svg>

                    </div>

                </a>

                <a target="_blank" href="mailto:ntnguyen0310@gmail.com"
                    class="flex items-center justify-center gap-3 rounded-xl bg-dawn-pink px-7 py-3 text-hurricane transition duration-300 hover:bg-dawn-pink/70 motion-reduce:transition-none">
                    <div>Liên hệ hợp tác</div>
                </a>
            </div>
        </div>

        {{-- Demo Mockup --}}
        <div x-ref="mockup" class="group/mockup relative grid w-full max-w-lg">
            {{-- Mockup --}}
            <div
                class="w-[95%] self-center justify-self-center overflow-hidden rounded-bl-xl rounded-br-xl rounded-tl-lg rounded-tr-lg shadow-lg shadow-black/5 transition-all duration-1000 [grid-area:1/-1] [transform-style:preserve-3d] group-hover/mockup:[transform:perspective(1500px)_rotateY(0deg)_rotateX(0deg)_translateZ(0)] motion-reduce:transition-none motion-reduce:group-hover/mockup:transform-none md:[transform:perspective(1500px)_rotateY(-10deg)_rotateX(5deg)_translateZ(0)]">
                {{-- Window Header --}}
                <div class="flex h-6 w-full items-center gap-5 bg-[#262B2F]/80 px-3">
                    <div class="flex items-center gap-1">
                        <div class="h-1.5 w-1.5 rounded-full bg-red-400"></div>
                        <div class="h-1.5 w-1.5 rounded-full bg-yellow-400"></div>
                        <div class="h-1.5 w-1.5 rounded-full bg-emerald-400"></div>
                    </div>
                    <div class="flex-1 pr-10 text-center text-[0.6rem] text-white/40">
                        <span>Freelancer</span>
                        <span class="font-bold">SAP, NetSuite, Tích hợp, WebApp,...</span>
                    </div>
                </div>

                {{-- Screenshot --}}
                <img src="{{ Vite::asset('resources/images/home/demo.png') }}" alt="Filament demo" class="w-full" />
            </div>

            {{-- Decoration Background --}}
            <div
                class="relative -z-10 h-[120%] w-full self-center justify-self-center rounded-[3rem] bg-gradient-to-br from-dawn-pink to-transparent [grid-area:1/-1] md:left-10 md:rotate-2 md:justify-self-start lg:h-[25rem] lg:w-[110%]">
            </div>
        </div>
    </div>
</div>

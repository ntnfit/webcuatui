<footer x-cloak x-data="{}" class="mx-auto w-full max-w-screen-lg space-y-24 px-5 pt-24">


    <div x-data="{}" x-ref="footer" x-init="() => {
        if (reducedMotion) return
        gsap.timeline({
            scrollTrigger: {
                trigger: $refs.footer,
                start: 'top bottom',
            },
        }).fromTo(
            $refs.footer, {
                autoAlpha: 0,
                y: 20,
            }, {
                autoAlpha: 1,
                y: 0,
                duration: 0.7,
                ease: 'circ.out',
            },
        )
    }" class="py-10">
        <div class="flex flex-wrap items-start justify-between gap-x-40 gap-y-10">
            <a href="/"
                class="block p-2 transition duration-300 will-change-transform hover:scale-105 motion-reduce:transition-none">
                <div class="text-black">
                    <img src="{{ Vite::asset('resources/images/home/logo.svg') }}" alt="logo"
                        class="h-auto w-36 overflow-visible min-[400px]:w-44 md:w-52">
                </div>
            </a>
            <div class="flex flex-1 flex-wrap justify-start gap-x-40 gap-y-3">
                <div class="flex flex-col items-start gap-3 text-sm font-medium">
                    <a href="{{ route('home') }}"
                        class="p-2 transition duration-300 will-change-transform hover:translate-x-1 hover:text-black motion-reduce:transition-none motion-reduce:hover:transform-none">
                        Home
                    </a>

                    <a href="{{ route('tools.index') }}"
                        class="p-2 transition duration-300 will-change-transform hover:translate-x-1 hover:text-black motion-reduce:transition-none motion-reduce:hover:transform-none">
                        Công cụ
                    </a>
                    <a href="{{ route('blogs') }}"
                        class="p-2 transition duration-300 will-change-transform hover:translate-x-1 hover:text-black motion-reduce:transition-none motion-reduce:hover:transform-none">
                        blogs
                    </a>

                </div>
                {{-- <div class="flex flex-col items-start gap-3 text-sm font-medium">
                    <a href="https://shop.filamentphp.com"
                        class="p-2 transition duration-300 will-change-transform hover:translate-x-1 hover:text-black motion-reduce:transition-none motion-reduce:hover:transform-none">
                        Shop
                    </a>
                    <a href="{{ route('team') }}"
                        class="p-2 transition duration-300 will-change-transform hover:translate-x-1 hover:text-black motion-reduce:transition-none motion-reduce:hover:transform-none">
                        Meet Our Team
                    </a>
                    <a target="_blank" href="https://status.filamentphp.com"
                        class="p-2 transition duration-300 will-change-transform hover:translate-x-1 hover:text-black motion-reduce:transition-none motion-reduce:hover:transform-none">
                        Status
                    </a>
                    <a target="_blank" href="https://github.com/filamentphp/filament/discussions/new"
                        class="p-2 transition duration-300 will-change-transform hover:translate-x-1 hover:text-black motion-reduce:transition-none motion-reduce:hover:transform-none">
                        Help
                    </a>
                    <a target="_blank" href="https://github.com/filamentphp/filament?sponsor=1"
                        class="p-2 transition duration-300 will-change-transform hover:translate-x-1 hover:text-black motion-reduce:transition-none motion-reduce:hover:transform-none">
                        Sponsor
                    </a>
                </div> --}}
            </div>
        </div>
        <div class="mt-7 flex flex-wrap items-start justify-between gap-10 border-t border-slate-200 pt-5">
            <div class="text-sm font-medium text-hurricane/50">
                &copy; {{ date('Y') }} HARRY DEV. All rights reserved.
            </div>
            <div class="flex flex-wrap items-center gap-3.5 text-hurricane">

                <a target="_blank" href="https://www.linkedin.com/in/nguyen0310"
                    class="grid h-[2.6rem] w-[2.6rem] place-items-center rounded-xl bg-merino transition duration-300 hover:text-black motion-reduce:transition-none">
                    <svg class="w-[1.4rem]" fill="none" viewBox="0 0 35 35" aria-hidden="true">
                        <g id="Linkedln">
                            <path fill="currentColor"
                                d="M26.49,30H5.5A3.35,3.35,0,0,1,3,29a3.35,3.35,0,0,1-1-2.48V5.5A3.35,3.35,0,0,1,3,3,3.35,3.35,0,0,1,5.5,2h21A3.35,3.35,0,0,1,29,3,3.35,3.35,0,0,1,30,5.5v21A3.52,3.52,0,0,1,26.49,30ZM9.11,11.39a2.22,2.22,0,0,0,1.6-.58,1.83,1.83,0,0,0,.6-1.38A2.09,2.09,0,0,0,10.68,8a2.14,2.14,0,0,0-1.51-.55A2.3,2.3,0,0,0,7.57,8,1.87,1.87,0,0,0,7,9.43a1.88,1.88,0,0,0,.57,1.38A2.1,2.1,0,0,0,9.11,11.39ZM11,13H7.19V24.54H11Zm13.85,4.94a5.49,5.49,0,0,0-1.24-4,4.22,4.22,0,0,0-3.15-1.27,3.44,3.44,0,0,0-2.34.66A6,6,0,0,0,17,14.64V13H13.19V24.54H17V17.59a.83.83,0,0,1,.1-.43,2.73,2.73,0,0,1,.7-1,1.81,1.81,0,0,1,1.28-.44,1.59,1.59,0,0,1,1.49.75,3.68,3.68,0,0,1,.44,1.9v6.15h3.85ZM17,14.7a.05.05,0,0,1,.06-.06v.06Z">

                            </path>
                        </g>
                    </svg>
                </a>
                <a target="_blank" href="https://github.com/ntnfit"
                    class="grid h-[2.6rem] w-[2.6rem] place-items-center rounded-xl bg-merino transition duration-300 hover:text-black motion-reduce:transition-none">
                    <svg class="w-[1.6rem]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</footer>

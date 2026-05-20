<section id="home" class="min-h-dvh flex items-center bg-[#0d1117] border-b border-[#21262d] relative overflow-hidden">

    {{-- Subtle dot grid --}}
    <div class="absolute inset-0 opacity-[0.025]"
         style="background-image: radial-gradient(#58a6ff 1px, transparent 1px); background-size: 28px 28px;"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 w-full py-20 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-4 lg:gap-5">

            {{-- Terminal window --}}
            <div class="bg-[#161b22] border border-[#30363d] rounded-lg overflow-hidden">
                {{-- Chrome bar --}}
                <div class="flex items-center gap-1.5 px-4 py-3 border-b border-[#30363d] bg-[#21262d] select-none">
                    <span class="w-3 h-3 rounded-full bg-[#f85149]"></span>
                    <span class="w-3 h-3 rounded-full bg-[#d29922]"></span>
                    <span class="w-3 h-3 rounded-full bg-[#3fb950]"></span>
                    <span class="ml-4 text-[10px] font-mono text-[#656d76]">harry@devbox — ~/profile.sh</span>
                </div>

                {{-- Body --}}
                <div class="p-6 sm:p-8 font-mono">
                    <p class="text-[11px] text-[#656d76] mb-4">Last login: {{ now()->format('D M d H:i:s Y') }} on ttys001</p>

                    <div class="flex items-center gap-2 text-sm mb-6">
                        <span class="text-[#3fb950]">harry@devbox</span>
                        <span class="text-[#656d76]">:~$</span>
                        <span class="text-[#e6edf3]">cat profile.json</span>
                    </div>

                    <div class="space-y-2 text-sm mb-6 pl-4 border-l-2 border-[#30363d]">
                        <div class="flex gap-3">
                            <span class="text-[#656d76] shrink-0 w-12">name</span>
                            <span class="text-[#e6edf3]">"Harry Dev"</span>
                        </div>
                        <div class="flex gap-3 items-center">
                            <span class="text-[#656d76] shrink-0 w-12">role</span>
                            <span class="text-[#58a6ff]">"<span id="typed-role"></span>"</span>
                            <span class="text-[#3fb950] animate-pulse leading-none">▋</span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-[#656d76] shrink-0 w-12">loc</span>
                            <span class="text-[#e6edf3]">"Ho Chi Minh City, VN"</span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-[#656d76] shrink-0 w-12">exp</span>
                            <span class="text-[#e6edf3]">"5+ years"</span>
                        </div>
                        <div class="flex gap-3">
                            <span class="text-[#656d76] shrink-0 w-12">focus</span>
                            <span class="text-[#f0883e]">["Web", "ERP", "Integration"]</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-sm mb-5">
                        <span class="text-[#3fb950]">harry@devbox</span>
                        <span class="text-[#656d76]">:~$</span>
                        <span class="text-[#656d76] animate-pulse">█</span>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2.5">
                        <a href="#projects"
                           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#238636] hover:bg-[#2ea043] border border-[#3fb950]/40 text-white text-sm font-mono rounded transition-colors">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                            ./view-projects
                        </a>
                        <a href="/blogs"
                           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#21262d] hover:bg-[#30363d] border border-[#30363d] hover:border-[#58a6ff] text-[#e6edf3] text-sm font-mono rounded transition-colors">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                            cat blog/
                        </a>
                        <a href="#contact"
                           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#21262d] hover:bg-[#30363d] border border-[#30363d] hover:border-[#58a6ff] text-[#8b949e] hover:text-[#e6edf3] text-sm font-mono rounded transition-colors">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            ./contact
                        </a>
                    </div>
                </div>
            </div>

            {{-- Right column --}}
            <div class="flex flex-row lg:flex-col gap-4">

                {{-- Status widget --}}
                <div class="flex-1 bg-[#161b22] border border-[#30363d] rounded-lg p-5 font-mono">
                    <div class="text-[10px] text-[#656d76] uppercase tracking-widest mb-3">// status</div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#3fb950] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#3fb950]"></span>
                        </span>
                        <span class="text-xs text-[#3fb950]">available for work</span>
                    </div>
                    <div class="space-y-2 text-[11px]">
                        <div class="flex justify-between">
                            <span class="text-[#656d76]">open_to</span>
                            <span class="text-[#e6edf3]">freelance</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#656d76]">timezone</span>
                            <span class="text-[#e6edf3]">GMT+7</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#656d76]">language</span>
                            <span class="text-[#e6edf3]">VI · EN</span>
                        </div>
                    </div>
                </div>

                {{-- Stats widget --}}
                <div class="flex-1 bg-[#161b22] border border-[#30363d] rounded-lg p-5 font-mono">
                    <div class="text-[10px] text-[#656d76] uppercase tracking-widest mb-3">// stats</div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <div class="text-xl font-bold text-[#e6edf3] tabular-nums">5+</div>
                            <div class="text-[10px] text-[#656d76]">years</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-[#e6edf3] tabular-nums">12+</div>
                            <div class="text-[10px] text-[#656d76]">clients</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-[#3fb950] tabular-nums">50+</div>
                            <div class="text-[10px] text-[#656d76]">projects</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-[#58a6ff] tabular-nums">∞</div>
                            <div class="text-[10px] text-[#656d76]">coffee</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scroll hint --}}
        <div class="mt-12 flex items-center gap-3">
            <div class="h-px flex-1 bg-[#21262d]"></div>
            <a href="#about" class="flex items-center gap-1.5 text-[10px] font-mono text-[#656d76] hover:text-[#8b949e] transition-colors">
                scroll down
                <svg class="h-3 w-3 animate-bounce" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
            </a>
            <div class="h-px flex-1 bg-[#21262d]"></div>
        </div>
    </div>
</section>

<script>
(function() {
    const roles = ['Full-Stack Developer', 'SAP B1 Consultant', 'ERP Integration Expert', 'Laravel & React Dev'];
    const el = document.getElementById('typed-role');
    let ri = 0, ci = 0, typing = true;

    function tick() {
        const text = roles[ri];
        if (typing) {
            el.textContent = text.slice(0, ++ci);
            if (ci >= text.length) { typing = false; setTimeout(tick, 1800); return; }
        } else {
            el.textContent = text.slice(0, --ci);
            if (ci <= 0) { typing = true; ri = (ri + 1) % roles.length; setTimeout(tick, 400); return; }
        }
        setTimeout(tick, typing ? 80 : 40);
    }
    tick();
})();
</script>

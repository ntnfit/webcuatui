<section id="about" class="py-16 bg-[#0d1117] border-b border-[#21262d]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <div class="text-[10px] font-mono text-[#656d76] uppercase tracking-widest mb-6 flex items-center gap-3">
            <span class="text-[#3fb950]">//</span> about
            <div class="h-px flex-1 bg-[#21262d]"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-5">

            {{-- Photo panel --}}
            <div class="bg-[#161b22] border border-[#30363d] rounded-lg overflow-hidden">
                <div class="aspect-square">
                    <img src="/images/me.jpg" alt="Harry Dev"
                         class="w-full h-full object-cover object-top grayscale hover:grayscale-0 transition-all duration-500">
                </div>
                <div class="p-4 font-mono border-t border-[#21262d]">
                    <div class="text-sm font-semibold text-[#e6edf3] mb-1">Harry Dev</div>
                    <div class="text-[11px] text-[#8b949e]">Full-Stack · SAP · ERP</div>
                    <div class="flex items-center gap-1.5 mt-2">
                        <svg class="h-3 w-3 text-[#656d76]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="text-[10px] text-[#656d76]">Ho Chi Minh City, VN</span>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="space-y-5">
                {{-- Bio --}}
                <div class="bg-[#161b22] border border-[#30363d] rounded-lg p-6">
                    <div class="text-[10px] font-mono text-[#656d76] mb-3">README.md</div>
                    <p class="text-sm text-[#c9d1d9] leading-7 mb-4">
                        Mình là <span class="text-[#58a6ff] font-mono">Harry Dev</span> — một lập trình viên đam mê
                        với hơn <span class="text-[#e6edf3] font-mono">5 năm kinh nghiệm</span> xây dựng ứng dụng web,
                        hệ thống doanh nghiệp và tích hợp giải pháp SAP ERP. Mình tin rằng code tốt là code mà
                        người đọc không cần comments để hiểu.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach([
                            'Phát triển WebApp với React, Laravel, Tailwind',
                            'Triển khai & tối ưu hệ thống ERP doanh nghiệp',
                            'Thiết kế API & tích hợp SAP B1 / OData / SDK',
                            'Tối ưu hiệu suất và trải nghiệm người dùng',
                        ] as $item)
                            <div class="flex items-start gap-2 text-sm">
                                <span class="text-[#3fb950] font-mono mt-0.5 shrink-0">▸</span>
                                <span class="text-[#8b949e]">{{ $item }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Timeline strip --}}
                <div class="bg-[#161b22] border border-[#30363d] rounded-lg p-5 font-mono">
                    <div class="text-[10px] text-[#656d76] uppercase tracking-widest mb-4">// git log --oneline</div>
                    <div class="space-y-3">
                        @foreach([
                            ['hash' => 'a3f9c21', 'year' => '2024', 'msg' => 'feat: Freelance SAP & Web consultant'],
                            ['hash' => '7d8b034', 'year' => '2022', 'msg' => 'feat: Senior developer at tech company'],
                            ['hash' => 'c2e5f18', 'year' => '2020', 'msg' => 'feat: SAP B1 integration specialist'],
                            ['hash' => '1a4d792', 'year' => '2019', 'msg' => 'init: first commit — web developer'],
                        ] as $entry)
                            <div class="flex items-center gap-3 text-xs">
                                <span class="text-[#f0883e] shrink-0">{{ $entry['hash'] }}</span>
                                <span class="text-[#656d76] shrink-0">{{ $entry['year'] }}</span>
                                <span class="text-[#c9d1d9]">{{ $entry['msg'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

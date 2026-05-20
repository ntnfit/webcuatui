<section id="skills" class="py-16 bg-[#0d1117] border-b border-[#21262d]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <div class="text-[10px] font-mono text-[#656d76] uppercase tracking-widest mb-6 flex items-center gap-3">
            <span class="text-[#3fb950]">//</span> tech stack
            <div class="h-px flex-1 bg-[#21262d]"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                [
                    'label'  => 'Frontend',
                    'color'  => '#58a6ff',
                    'border' => '#1f3a5c',
                    'bg'     => '#121d2f',
                    'icon'   => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
                    'tags'   => ['React', 'Next.js', 'TypeScript', 'Tailwind CSS', 'Vue.js', 'Alpine.js'],
                ],
                [
                    'label'  => 'Backend',
                    'color'  => '#bc8cff',
                    'border' => '#3d1d6b',
                    'bg'     => '#1e1228',
                    'icon'   => '<rect width="20" height="8" x="2" y="2" rx="2"/><rect width="20" height="8" x="2" y="14" rx="2"/><line x1="6" x2="6.01" y1="6" y2="6"/><line x1="6" x2="6.01" y1="18" y2="18"/>',
                    'tags'   => ['Laravel', 'PHP', 'REST API', 'OAuth2', 'Node.js', 'Livewire'],
                ],
                [
                    'label'  => 'Database',
                    'color'  => '#3fb950',
                    'border' => '#1a3a1a',
                    'bg'     => '#0f1f0f',
                    'icon'   => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>',
                    'tags'   => ['MySQL', 'SQL Server', 'SAP HANA', 'PostgreSQL', 'Redis'],
                ],
                [
                    'label'  => 'ERP / SAP',
                    'color'  => '#f0883e',
                    'border' => '#5a3000',
                    'bg'     => '#2a1800',
                    'icon'   => '<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
                    'tags'   => ['SAP B1', 'DI API', 'Service Layer', 'OData', 'SDK', 'Workflow'],
                ],
            ] as $group)
                <div class="bg-[#161b22] border border-[#30363d] rounded-lg p-5 hover:border-[{{ $group['color'] }}]/50 transition-colors group">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="p-1.5 rounded border" style="background:{{ $group['bg'] }};border-color:{{ $group['border'] }};">
                            <svg class="h-4 w-4" style="color:{{ $group['color'] }};" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $group['icon'] !!}</svg>
                        </div>
                        <span class="text-sm font-mono font-semibold" style="color:{{ $group['color'] }};">{{ $group['label'] }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($group['tags'] as $tag)
                            <span class="px-2 py-0.5 text-[10px] font-mono text-[#8b949e] bg-[#21262d] border border-[#30363d] rounded">
                                {{ $tag }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Marquee --}}
        <div class="mt-8 relative overflow-hidden">
            <div class="absolute left-0 top-0 h-full w-16 bg-gradient-to-r from-[#0d1117] to-transparent z-10 pointer-events-none"></div>
            <div class="absolute right-0 top-0 h-full w-16 bg-gradient-to-l from-[#0d1117] to-transparent z-10 pointer-events-none"></div>
            <div class="flex gap-6 animate-[marquee_25s_linear_infinite] hover:[animation-play-state:paused] whitespace-nowrap w-max">
                @php
                    $techs = ['React', 'Laravel', 'TypeScript', 'SAP B1', 'Next.js', 'Tailwind', 'MySQL', 'Vue.js', 'PHP', 'Redis', 'Docker', 'Git', 'OData', 'REST API', 'Node.js'];
                @endphp
                @foreach(array_merge($techs, $techs) as $t)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#161b22] border border-[#30363d] rounded text-[11px] font-mono text-[#656d76]">
                        <span class="text-[#3fb950]">$</span> {{ $t }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</section>

<style>
@keyframes marquee {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
}
</style>

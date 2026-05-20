<section id="skills" class="py-16 bg-gh-base border-b border-gh-subtle">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <div class="text-[10px] font-mono text-gh-subtle uppercase tracking-widest mb-6 flex items-center gap-3">
            <span class="text-gh-green">//</span> tech stack
            <div class="h-px flex-1 bg-gh-raised"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                [
                    'label'    => 'Frontend',
                    'tc'       => 'text-gh-blue',
                    'ic_bg'    => 'bg-gh-b-blue border-gh-b-blue',
                    'hov'      => 'hover:border-blue-400/50 dark:hover:border-[#58a6ff]/50',
                    'icon'     => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
                    'tags'     => ['React', 'Next.js', 'TypeScript', 'Tailwind CSS', 'Vue.js', 'Alpine.js'],
                ],
                [
                    'label'    => 'Backend',
                    'tc'       => 'text-gh-purple',
                    'ic_bg'    => 'bg-gh-b-purple border-gh-b-purple',
                    'hov'      => 'hover:border-purple-400/50 dark:hover:border-[#bc8cff]/50',
                    'icon'     => '<rect width="20" height="8" x="2" y="2" rx="2"/><rect width="20" height="8" x="2" y="14" rx="2"/><line x1="6" x2="6.01" y1="6" y2="6"/><line x1="6" x2="6.01" y1="18" y2="18"/>',
                    'tags'     => ['Laravel', 'PHP', 'REST API', 'OAuth2', 'Node.js', 'Livewire'],
                ],
                [
                    'label'    => 'Database',
                    'tc'       => 'text-gh-green',
                    'ic_bg'    => 'bg-gh-b-green border-gh-b-green',
                    'hov'      => 'hover:border-green-500/50 dark:hover:border-[#3fb950]/50',
                    'icon'     => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>',
                    'tags'     => ['MySQL', 'SQL Server', 'SAP HANA', 'PostgreSQL', 'Redis'],
                ],
                [
                    'label'    => 'ERP / SAP',
                    'tc'       => 'text-gh-orange',
                    'ic_bg'    => 'bg-gh-b-orange border-gh-b-orange',
                    'hov'      => 'hover:border-orange-400/50 dark:hover:border-[#f0883e]/50',
                    'icon'     => '<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
                    'tags'     => ['SAP B1', 'DI API', 'Service Layer', 'OData', 'SDK', 'Workflow'],
                ],
            ] as $group)
                <div class="bg-gh-surface border border-gh rounded-lg p-5 {{ $group['hov'] }} transition-colors group">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="p-1.5 rounded border {{ $group['ic_bg'] }}">
                            <svg class="h-4 w-4 {{ $group['tc'] }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $group['icon'] !!}</svg>
                        </div>
                        <span class="text-sm font-mono font-semibold {{ $group['tc'] }}">{{ $group['label'] }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($group['tags'] as $tag)
                            <span class="px-2 py-0.5 text-[10px] font-mono text-gh-muted bg-gh-raised border border-gh rounded">
                                {{ $tag }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Marquee --}}
        <div class="mt-8 relative overflow-hidden">
            <div class="absolute left-0 top-0 h-full w-16 z-10 pointer-events-none" style="background:linear-gradient(to right,var(--gh-bg),transparent)"></div>
            <div class="absolute right-0 top-0 h-full w-16 z-10 pointer-events-none" style="background:linear-gradient(to left,var(--gh-bg),transparent)"></div>
            <div class="flex gap-6 animate-[marquee_25s_linear_infinite] hover:[animation-play-state:paused] whitespace-nowrap w-max">
                @php
                    $techs = ['React', 'Laravel', 'TypeScript', 'SAP B1', 'Next.js', 'Tailwind', 'MySQL', 'Vue.js', 'PHP', 'Redis', 'Docker', 'Git', 'OData', 'REST API', 'Node.js'];
                @endphp
                @foreach(array_merge($techs, $techs) as $t)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gh-surface border border-gh rounded text-[11px] font-mono text-gh-subtle">
                        <span class="text-gh-green">$</span> {{ $t }}
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

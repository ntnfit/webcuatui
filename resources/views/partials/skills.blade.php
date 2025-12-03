<section id="skills" class="py-24 px-6 bg-[#1A1F2C] dark:bg-gray-900 text-white relative overflow-hidden transition-colors duration-500">
    <div class="absolute inset-0 bg-gradient-to-br from-[#1A1F2C] via-[#252A3A] to-[#1A1F2C] dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 z-0 transition-colors duration-500"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <!-- Skills Header -->
        <div class="text-center mb-16 animate-fade-in-up">
            <div class="inline-block px-3 py-1 rounded-full bg-blue-500/10 dark:bg-blue-900/20 text-blue-500 dark:text-blue-400 text-xs font-medium mb-4 transition-colors duration-500">
                • Tech Stack
            </div>

            <h2 class="text-4xl md:text-5xl font-bold mb-8 tracking-tight text-white dark:text-gray-100 transition-colors duration-500">
                My Technical Expertise
            </h2>

            <p class="text-lg text-gray-300 dark:text-gray-400 max-w-2xl mx-auto transition-colors duration-500">
                I specialize in creating responsive web applications and ERP integrations
                using modern frameworks and technologies.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">
            <!-- Left column - Skill groups -->
            <div class="space-y-8">
                @foreach([
                    [
                        'title' => 'Frontend',
                        'skills' => 'ReactJS, VueJS, TailwindCSS, UI/UX',
                        'icon' => 'code',
                        'color' => 'text-blue-600 dark:text-blue-400'
                    ],
                    [
                        'title' => 'Backend',
                        'skills' => 'Laravel, REST API, OAuth2',
                        'icon' => 'server',
                        'color' => 'text-purple-600 dark:text-purple-400'
                    ],
                    [
                        'title' => 'Database',
                        'skills' => 'SQL Server, MySQL, SAP HANA',
                        'icon' => 'database',
                        'color' => 'text-green-500 dark:text-green-400'
                    ],
                    [
                        'title' => 'ERP Integration',
                        'skills' => 'SAP B1 SDK (DI API, Service Layer), Workflow tư vấn',
                        'icon' => 'boxes',
                        'color' => 'text-pink-500 dark:text-pink-400'
                    ]
                ] as $group)
                    <div class="group relative p-[1px] rounded-2xl overflow-hidden bg-white/5 dark:bg-gray-900/5 transition-colors duration-500 animate-fade-in-left">
                        <!-- Animated gradient border -->
                        <div class="absolute inset-0">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        </div>

                        <!-- Content container -->
                        <div class="relative bg-white/5 dark:bg-gray-900/70 backdrop-blur-sm rounded-2xl p-6 hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-300 shadow-sm">
                            <div class="flex items-center mb-3">
                                <!-- Icon placeholder - using simple SVGs -->
                                <div class="mr-3 transition-colors duration-300 text-gray-700 dark:text-gray-400 {{ $group['color'] }} group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                    @if($group['icon'] == 'code')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                                    @elseif($group['icon'] == 'server')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="8" x="2" y="2" rx="2" ry="2"/><rect width="20" height="8" x="2" y="14" rx="2" ry="2"/><line x1="6" x2="6.01" y1="6" y2="6"/><line x1="6" x2="6.01" y1="18" y2="18"/></svg>
                                    @elseif($group['icon'] == 'database')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s 9-1.34 9-3V5"/></svg>
                                    @elseif($group['icon'] == 'boxes')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                                    @endif
                                </div>
                                <h3 class="text-xl font-semibold transition-colors duration-300 text-gray-900 dark:text-gray-200 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-blue-600 group-hover:via-purple-600 group-hover:to-pink-600 dark:group-hover:text-transparent dark:group-hover:bg-clip-text dark:group-hover:bg-gradient-to-r dark:group-hover:from-blue-500 dark:group-hover:via-purple-500 dark:group-hover:to-pink-500">
                                    {{ $group['title'] }}
                                </h3>
                            </div>
                            <p class="ml-9 transition-colors duration-300 text-gray-700 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200">
                                {{ $group['skills'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Right column - Tech icons with animations -->
            <div class="relative w-full overflow-hidden mt-12 md:mt-0 rounded-xl bg-[#232839]/50 p-6 animate-fade-in-right">
                <div class="mb-4 text-xl text-center text-white font-semibold">
                    <span class="text-blue-500">Tech</span> Stack
                </div>

                <div class="relative w-full overflow-hidden">
                    <div class="flex space-x-8 animate-marquee whitespace-nowrap">
                        @php
                            $icons = [
                                ['name' => 'React', 'icon' => '⚛️', 'color' => 'text-blue-500', 'darkColor' => 'dark:text-blue-400'],
                                ['name' => 'TypeScript', 'icon' => '📘', 'color' => 'text-blue-600', 'darkColor' => 'dark:text-blue-500'],
                                ['name' => 'Next.js', 'icon' => '▲', 'color' => 'text-black', 'darkColor' => 'dark:text-white'],
                                ['name' => 'Node.js', 'icon' => '🟢', 'color' => 'text-green-600', 'darkColor' => 'dark:text-green-500'],
                                ['name' => 'MongoDB', 'icon' => '🍃', 'color' => 'text-green-500', 'darkColor' => 'dark:text-green-400'],
                                ['name' => 'PostgreSQL', 'icon' => '🐘', 'color' => 'text-blue-700', 'darkColor' => 'dark:text-blue-600'],
                                ['name' => 'GraphQL', 'icon' => '📊', 'color' => 'text-pink-600', 'darkColor' => 'dark:text-pink-500'],
                                ['name' => 'Docker', 'icon' => '🐳', 'color' => 'text-blue-400', 'darkColor' => 'dark:text-blue-300'],
                                ['name' => 'AWS', 'icon' => '☁️', 'color' => 'text-orange-500', 'darkColor' => 'dark:text-orange-400'],
                                ['name' => 'Git', 'icon' => '📦', 'color' => 'text-orange-600', 'darkColor' => 'dark:text-orange-500'],
                                ['name' => 'Laravel', 'icon' => '🔥', 'color' => 'text-red-500', 'darkColor' => 'dark:text-red-400'],
                                ['name' => 'SAP B1', 'icon' => '💼', 'color' => 'text-blue-800', 'darkColor' => 'dark:text-blue-300'],
                            ];
                        @endphp
                        
                        <!-- Duplicate for infinite scroll -->
                        @foreach(array_merge($icons, $icons) as $icon)
                            <div class="inline-flex flex-col items-center justify-center space-y-2 px-4 py-3 hover:bg-white/5 rounded-lg transition-all duration-300 transform hover:scale-110 hover:-translate-y-1 cursor-pointer">
                                <span class="text-4xl">{{ $icon['icon'] }}</span>
                                <span class="text-xs font-medium {{ $icon['color'] }} {{ $icon['darkColor'] }}">
                                    {{ $icon['name'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Gradient overlays for smooth edges -->
                <div class="absolute left-0 top-0 h-full w-16 bg-gradient-to-r from-[#1A1F2C] to-transparent z-10 pointer-events-none"></div>
                <div class="absolute right-0 top-0 h-full w-16 bg-gradient-to-l from-[#1A1F2C] to-transparent z-10 pointer-events-none"></div>
            </div>
        </div>
    </div>

    <!-- Background light effects -->
    <div class="absolute top-1/3 -left-24 w-72 h-72 bg-blue-500/10 dark:bg-blue-600/10 rounded-full filter blur-3xl transition-colors duration-500"></div>
    <div class="absolute bottom-1/4 -right-24 w-80 h-80 bg-purple-500/10 dark:bg-purple-600/10 rounded-full filter blur-3xl transition-colors duration-500"></div>
</section>

<style>
    @keyframes marquee {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    .animate-marquee {
        animation: marquee 30s linear infinite;
    }
    .animate-marquee:hover {
        animation-play-state: paused;
    }
</style>

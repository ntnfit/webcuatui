<section id="projects" class="py-16 bg-gh-base border-b border-gh-subtle">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <div class="text-[10px] font-mono text-gh-subtle uppercase tracking-widest mb-2 flex items-center gap-3">
            <span class="text-gh-green">//</span> clients & partners
            <div class="h-px flex-1 bg-gh-raised"></div>
        </div>
        <p class="text-xs font-mono text-gh-subtle mb-8">
            <span class="text-gh-muted">{{ 12 }}</span> companies trusted · domestic &amp; international
        </p>

        @php
        $companies = [
            ['name' => 'San Hà Foods',      'logo' => 'https://sanha.vn/wp-content/uploads/2024/07/logo.png',                                                                   'link' => 'https://sanha.vn/'],
            ['name' => 'Hồng Ký',           'logo' => 'https://www.hongky.com/wp-content/uploads/2024/08/hing.png',                                                             'link' => 'https://www.hongky.com/'],
            ['name' => 'E-block',           'logo' => 'https://eblock.com.vn/wp-content/uploads/2024/07/logo-header.png',                                                       'link' => 'https://eblock.com.vn'],
            ['name' => 'Tazmo',             'logo' => 'https://tazmo-vn.com/wp-content/uploads/2021/12/logo_TAZMO_2021.png',                                                    'link' => 'https://tazmo-vn.com'],
            ['name' => 'Aeon Delight',      'logo' => 'https://aeondelight-vietnam.com.vn/wp-content/uploads/2024/02/logo-6.svg',                                               'link' => 'https://aeondelight-vietnam.com.vn'],
            ['name' => 'CJ OliveNetworks',  'logo' => 'https://en.cjolivenetworks.co.kr/images/common/logo-white.svg',                                                          'link' => 'https://en.cjolivenetworks.co.kr'],
            ['name' => 'Việt Hưng SG',      'logo' => 'https://cdn.nhansu.vn/uploads/images/0B91D74C/logo/2018-12/fc4f6b551f484115966604ba4e6adffb_logo-Viet-Hung.png',         'link' => 'http://viethung.com.vn/'],
            ['name' => 'Betagen',           'logo' => 'https://www.betagen.co.th/images/logo.png',                                                                              'link' => 'https://www.betagen.co.th/'],
            ['name' => 'Woodsland',         'logo' => 'https://woodsland.vn/wp-content/uploads/2024/07/logo.svg',                                                               'link' => 'https://woodsland.vn'],
            ['name' => 'Nissey',            'logo' => 'https://www.nihon-s.co.jp/wp-content/uploads/elementor/thumbs/logo001-poowskldjo65xxcjfqkcvjrcq33ci0zr2933ho3v3g.png',   'link' => 'http://www.nihon-s.co.jp/group-company/nissey-vietnam/'],
            ['name' => 'Nam Dung',          'logo' => 'https://lh6.googleusercontent.com/proxy/pbw0HOscYPaji1hh6BrJjC7o_XHWLKAl-jkswJzk0gSQSKWjJjr8XNT7gkS1NGVzzCFaNSIbxKlJTvbY-95syIKODB3KwoEhAOZqwQ', 'link' => 'http://www.namdung.vn'],
            ['name' => 'USM',               'logo' => 'https://usm.com.vn/wp-content/themes/usm/images/logo.svg',                                                               'link' => 'https://usm.com.vn/'],
        ];
        @endphp

        <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-px bg-gh-raised border border-gh-subtle rounded-lg overflow-hidden">
            @foreach($companies as $company)
                <a href="{{ $company['link'] }}" target="_blank" rel="noopener noreferrer"
                   class="group flex items-center justify-center p-5 bg-gh-base hover:bg-gh-surface transition-colors">
                    <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}"
                         class="h-8 w-full object-contain grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-300"
                         onerror="this.parentElement.style.display='none'">
                </a>
            @endforeach
        </div>

        <div class="mt-8 text-center">
            <a href="#contact"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gh-surface hover:bg-gh-raised border border-gh hover:border-[#58a6ff] text-sm font-mono text-gh-muted hover:text-gh-blue rounded transition-colors">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                liên hệ hợp tác →
            </a>
        </div>
    </div>
</section>

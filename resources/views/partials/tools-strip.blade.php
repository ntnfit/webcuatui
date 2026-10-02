<section class="border-b border-line-soft" aria-labelledby="home-tools">
    <div class="container-page py-14">
        <div class="mb-6 flex items-end justify-between gap-4">
            <h2 id="home-tools" class="eyebrow">tools</h2>
            <a href="{{ route('tools.index') }}" class="font-mono text-xs text-link hover:underline">Tất cả công cụ →</a>
        </div>
        <a href="{{ route('tools.index') }}" class="card group flex flex-col gap-2 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-2">
                    <span class="badge badge-green">Thuế</span>
                </div>
                <h3 class="text-base font-semibold transition-colors group-hover:text-link">Công cụ thuế cho doanh nghiệp</h3>
                <p class="mt-1 max-w-xl text-sm text-muted">Làm việc với dữ liệu thuế trong khu vực khách hàng, đăng nhập để sử dụng.</p>
            </div>
            <span class="font-mono text-xs text-link group-hover:underline">Xem công cụ →</span>
        </a>
    </div>
</section>

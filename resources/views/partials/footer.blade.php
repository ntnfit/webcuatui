<footer class="border-t border-line-soft bg-base py-10">
    <div class="container-page">
        <div class="grid gap-8 sm:grid-cols-[1.4fr_1fr_1fr]">
            <div class="font-mono">
                <div class="flex items-center gap-1">
                    <span class="text-accent">~/</span>
                    <span class="text-sm font-semibold text-fg">HarryDev</span>
                </div>
                <p class="mt-3 max-w-xs text-xs leading-relaxed text-muted">
                    Addon tích hợp SAP Business One, công cụ cho kế toán và chia sẻ kinh nghiệm ERP.
                </p>
            </div>

            <nav aria-label="Liên kết chân trang" class="font-mono text-xs">
                <div class="mb-3 text-[10px] uppercase tracking-widest text-subtle">Khám phá</div>
                <ul class="space-y-2 text-muted">
                    <li><a href="{{ route('marketplace.index') }}" class="hover:text-link">Marketplace</a></li>
                    <li><a href="{{ route('tools.index') }}" class="hover:text-link">Tools</a></li>
                    <li><a href="{{ route('blogs.index') }}" class="hover:text-link">Blog</a></li>
                    <li><a href="{{ route('shop.index') }}" class="hover:text-link">Cửa hàng</a></li>
                </ul>
            </nav>

            <div class="font-mono text-xs">
                <div class="mb-3 text-[10px] uppercase tracking-widest text-subtle">Liên hệ</div>
                <ul class="space-y-2 text-muted">
                    <li><a href="mailto:sapb1devbtp@gmail.com" class="hover:text-link">sapb1devbtp@gmail.com</a></li>
                    <li><a href="tel:0981710031" class="hover:text-link">0981 710 031</a></li>
                    <li class="flex gap-4 pt-1">
                        <a href="https://github.com/ntnfit" target="_blank" rel="noopener noreferrer" class="hover:text-fg">GitHub</a>
                        <a href="https://www.linkedin.com/in/nguyen0310/" target="_blank" rel="noopener noreferrer" class="hover:text-fg">LinkedIn</a>
                    </li>
                </ul>
            </div>
        </div>

        <p class="mt-8 border-t border-line-soft pt-6 font-mono text-[11px] text-subtle">© {{ date('Y') }} HarryDev</p>
    </div>
</footer>

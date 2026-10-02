<section id="contact" class="scroll-mt-16" aria-labelledby="home-contact">
    <div class="container-page py-14">
        <h2 id="home-contact" class="eyebrow mb-6">liên hệ</h2>

        <div class="grid gap-5 lg:grid-cols-[300px_1fr]">
            <div class="card space-y-4 p-5 font-mono text-sm">
                <a href="mailto:sapb1devbtp@gmail.com" class="block text-muted hover:text-link">sapb1devbtp@gmail.com</a>
                <a href="tel:0981710031" class="block text-muted hover:text-link">0981 710 031</a>
                <div class="flex gap-4 border-t border-line-soft pt-4 text-xs">
                    <a href="https://github.com/ntnfit" target="_blank" rel="noopener noreferrer" class="text-muted hover:text-fg">GitHub</a>
                    <a href="https://www.linkedin.com/in/nguyen0310/" target="_blank" rel="noopener noreferrer" class="text-muted hover:text-fg">LinkedIn</a>
                </div>
            </div>

            <div class="card p-5 sm:p-6">
                <form data-json-form="{{ url('/api/contact') }}" class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="c-name" class="field-label">Họ và tên <span class="text-danger">*</span></label>
                            <input id="c-name" name="name" type="text" required autocomplete="name" class="field">
                        </div>
                        <div>
                            <label for="c-email" class="field-label">Email <span class="text-danger">*</span></label>
                            <input id="c-email" name="email" type="email" required autocomplete="email" class="field">
                        </div>
                        <div>
                            <label for="c-phone" class="field-label">Điện thoại</label>
                            <input id="c-phone" name="phone_number" type="tel" autocomplete="tel" class="field">
                        </div>
                        <div>
                            <label for="c-company" class="field-label">Công ty</label>
                            <input id="c-company" name="company_name" type="text" autocomplete="organization" class="field">
                        </div>
                    </div>
                    <div>
                        <label for="c-message" class="field-label">Nội dung <span class="text-danger">*</span></label>
                        <textarea id="c-message" name="message" rows="4" required class="field resize-y" placeholder="Mình có thể giúp gì cho bạn?"></textarea>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <button type="submit" class="btn btn-primary">Gửi tin nhắn</button>
                        <p data-form-status role="status" class="text-xs font-mono"></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

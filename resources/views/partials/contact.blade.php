<section id="contact" class="py-16 bg-[#0d1117] border-b border-[#21262d]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <div class="text-[10px] font-mono text-[#656d76] uppercase tracking-widest mb-6 flex items-center gap-3">
            <span class="text-[#3fb950]">//</span> contact
            <div class="h-px flex-1 bg-[#21262d]"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-5">

            {{-- Info panel --}}
            <div class="space-y-4">
                <div class="bg-[#161b22] border border-[#30363d] rounded-lg p-5 font-mono">
                    <div class="text-[10px] text-[#656d76] uppercase tracking-widest mb-4">// reach out</div>
                    <div class="space-y-4">
                        <a href="mailto:sapb1devbtp@gmail.com"
                           class="flex items-center gap-3 text-xs text-[#8b949e] hover:text-[#58a6ff] transition-colors group">
                            <span class="p-2 bg-[#21262d] border border-[#30363d] rounded group-hover:border-[#58a6ff]/50 transition-colors shrink-0">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </span>
                            sapb1devbtp@gmail.com
                        </a>
                        <a href="tel:0981710031"
                           class="flex items-center gap-3 text-xs text-[#8b949e] hover:text-[#58a6ff] transition-colors group">
                            <span class="p-2 bg-[#21262d] border border-[#30363d] rounded group-hover:border-[#58a6ff]/50 transition-colors shrink-0">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.99 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.92 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </span>
                            0981 710 031
                        </a>
                    </div>

                    <div class="mt-6 pt-4 border-t border-[#21262d]">
                        <div class="text-[10px] text-[#656d76] mb-3">// socials</div>
                        <div class="flex gap-2">
                            <a href="https://github.com/ntnfit" target="_blank" rel="noopener noreferrer"
                               class="p-2 bg-[#21262d] border border-[#30363d] rounded text-[#8b949e] hover:text-[#e6edf3] hover:border-[#58a6ff]/50 transition-colors">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg>
                            </a>
                            <a href="https://www.linkedin.com/in/nguyen0310/" target="_blank" rel="noopener noreferrer"
                               class="p-2 bg-[#21262d] border border-[#30363d] rounded text-[#8b949e] hover:text-[#e6edf3] hover:border-[#58a6ff]/50 transition-colors">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-[#161b22] border border-[#30363d] rounded-lg p-5 font-mono">
                    <div class="text-[10px] text-[#656d76] uppercase tracking-widest mb-3">// response time</div>
                    <div class="text-xs text-[#8b949e] space-y-1.5">
                        <div class="flex justify-between">
                            <span>email</span><span class="text-[#3fb950]">&lt; 24h</span>
                        </div>
                        <div class="flex justify-between">
                            <span>phone</span><span class="text-[#3fb950]">&lt; 4h</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="bg-[#161b22] border border-[#30363d] rounded-lg overflow-hidden" x-data="contactForm()">
                <div class="flex items-center gap-1.5 px-4 py-3 border-b border-[#30363d] bg-[#21262d] select-none">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#f85149]"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#d29922]"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#3fb950]"></span>
                    <span class="ml-3 text-[10px] font-mono text-[#656d76]">new_message.json</span>
                </div>

                <div class="p-6">
                    <div x-show="sent" x-transition class="text-center py-8 font-mono">
                        <div class="text-[#3fb950] text-4xl mb-4">✓</div>
                        <div class="text-sm text-[#e6edf3] mb-1">Message sent successfully</div>
                        <div class="text-xs text-[#656d76]">I'll get back to you within 24h</div>
                    </div>

                    <form x-show="!sent" @submit.prevent="submit" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-mono text-[#656d76] mb-1.5">name <span class="text-[#f85149]">*</span></label>
                                <input type="text" name="name" required x-model="form.name"
                                       placeholder="Họ và tên"
                                       class="w-full px-3 py-2.5 bg-[#0d1117] border border-[#30363d] rounded text-sm font-mono text-[#e6edf3] placeholder-[#656d76] focus:outline-none focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]/20 transition-colors">
                            </div>
                            <div>
                                <label class="block text-[10px] font-mono text-[#656d76] mb-1.5">email <span class="text-[#f85149]">*</span></label>
                                <input type="email" name="email" required x-model="form.email"
                                       placeholder="email@example.com"
                                       class="w-full px-3 py-2.5 bg-[#0d1117] border border-[#30363d] rounded text-sm font-mono text-[#e6edf3] placeholder-[#656d76] focus:outline-none focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]/20 transition-colors">
                            </div>
                            <div>
                                <label class="block text-[10px] font-mono text-[#656d76] mb-1.5">phone</label>
                                <input type="tel" name="phone_number" x-model="form.phone"
                                       placeholder="0981 710 031"
                                       class="w-full px-3 py-2.5 bg-[#0d1117] border border-[#30363d] rounded text-sm font-mono text-[#e6edf3] placeholder-[#656d76] focus:outline-none focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]/20 transition-colors">
                            </div>
                            <div>
                                <label class="block text-[10px] font-mono text-[#656d76] mb-1.5">company</label>
                                <input type="text" name="company_name" x-model="form.company"
                                       placeholder="Tên công ty"
                                       class="w-full px-3 py-2.5 bg-[#0d1117] border border-[#30363d] rounded text-sm font-mono text-[#e6edf3] placeholder-[#656d76] focus:outline-none focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]/20 transition-colors">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-mono text-[#656d76] mb-1.5">message <span class="text-[#f85149]">*</span></label>
                            <textarea name="message" required rows="4" x-model="form.message"
                                      placeholder="Tôi có thể giúp gì cho bạn?"
                                      class="w-full px-3 py-2.5 bg-[#0d1117] border border-[#30363d] rounded text-sm font-mono text-[#e6edf3] placeholder-[#656d76] focus:outline-none focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]/20 transition-colors resize-none"></textarea>
                        </div>

                        <div x-show="error" x-text="error" class="text-xs font-mono text-[#f85149] bg-[#2a0f0f] border border-[#f85149]/30 rounded px-3 py-2"></div>

                        <button type="submit" :disabled="loading"
                                class="flex items-center gap-2 px-5 py-2.5 bg-[#238636] hover:bg-[#2ea043] disabled:opacity-50 disabled:cursor-not-allowed border border-[#3fb950]/40 text-white text-sm font-mono rounded transition-colors cursor-pointer">
                            <svg x-show="!loading" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4z"/><path d="M22 2 11 13"/></svg>
                            <svg x-show="loading" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            <span x-text="loading ? 'Sending...' : 'send message'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function contactForm() {
    return {
        sent: false, loading: false, error: '',
        form: { name: '', email: '', phone: '', company: '', message: '' },
        async submit() {
            this.loading = true; this.error = '';
            try {
                const res = await fetch('/api/contact', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(this.form),
                });
                if (res.ok) { this.sent = true; }
                else { this.error = 'Gửi thất bại. Vui lòng thử lại sau.'; }
            } catch { this.error = 'Lỗi kết nối. Vui lòng thử lại.'; }
            finally { this.loading = false; }
        }
    };
}
</script>

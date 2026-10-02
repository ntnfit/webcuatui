import '../css/app.css';

// Public site behaviour in plain JS so pages never need Alpine/Livewire just for the navbar.

const root = document.documentElement;

function applyTheme(isDark) {
    root.classList.toggle('dark', isDark);
    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.setAttribute('aria-pressed', String(isDark));
    });
}

function initThemeToggle() {
    applyTheme(root.classList.contains('dark'));
    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const isDark = !root.classList.contains('dark');
            applyTheme(isDark);
            try {
                localStorage.setItem('appearance', isDark ? 'dark' : 'light');
            } catch (e) {
                // Storage can be blocked (private mode); the toggle still works for this page view.
            }
        });
    });
}

function initMobileMenu() {
    const button = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('[data-menu]');
    if (!button || !menu) return;

    const setOpen = (open) => {
        menu.classList.toggle('hidden', !open);
        button.setAttribute('aria-expanded', String(open));
    };

    button.addEventListener('click', () => setOpen(menu.classList.contains('hidden')));
    menu.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => setOpen(false)));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') setOpen(false);
    });
}

// Plain JSON form posts used by the home contact form (data-json-form="/api/contact").
function initJsonForms() {
    document.querySelectorAll('form[data-json-form]').forEach((form) => {
        const status = form.querySelector('[data-form-status]');
        const submit = form.querySelector('[type="submit"]');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (status) status.textContent = '';
            if (submit) submit.disabled = true;

            try {
                const res = await fetch(form.dataset.jsonForm, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify(Object.fromEntries(new FormData(form))),
                });

                if (res.ok) {
                    form.reset();
                    if (status) {
                        status.textContent = 'Đã gửi. Mình sẽ phản hồi trong vòng 24 giờ.';
                        status.className = 'text-xs font-mono text-accent';
                    }
                } else if (status) {
                    status.textContent = 'Gửi thất bại, vui lòng kiểm tra thông tin và thử lại.';
                    status.className = 'text-xs font-mono text-danger';
                }
            } catch (e) {
                if (status) {
                    status.textContent = 'Lỗi kết nối, vui lòng thử lại.';
                    status.className = 'text-xs font-mono text-danger';
                }
            } finally {
                if (submit) submit.disabled = false;
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initThemeToggle();
    initMobileMenu();
    initJsonForms();
});

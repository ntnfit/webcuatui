/**
 * TinyMCE Alpine.js component for Filament.
 * Loaded lazily via x-load / x-load-src — must be a default ESM export.
 */
export default function tinyeditor({
    state,
    editorId,
    plugins = '',
    toolbar = '',
    height = 500,
    minHeight = 300,
    menubar = false,
    toolbarSticky = false,
    toolbarStickyOffset = 64,
    darkMode = 'auto',
    customConfigs = {},
    uploadUrl = '',
    uploadToken = '',
    disabled = false,
}) {
    return {
        state,
        isUploading: false,
        _morphHandler: null,

        // ── Helpers ────────────────────────────────────────────────────────

        editor() {
            return window.tinymce?.get(editorId) ?? null;
        },

        _skin() {
            if (darkMode === 'force') return 'oxide-dark';
            if (darkMode === false)   return 'oxide';
            if (darkMode === 'class') return document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide';
            if (darkMode === 'media') return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide';
            // auto — respect this site's localStorage appearance key
            const pref = localStorage.getItem('appearance') ?? 'system';
            if (pref === 'dark') return 'oxide-dark';
            if (pref === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches) return 'oxide-dark';
            return 'oxide';
        },

        // ── Lifecycle ──────────────────────────────────────────────────────

        init() {
            if (disabled) return;

            this._boot();

            // Sync editor content when Livewire updates state externally
            this.$watch('state', (value) => {
                const ed = this.editor();
                if (!ed || this.isUploading) return;
                if (value !== ed.getContent()) ed.setContent(value ?? '');
            });

            // Re-initialise after Livewire morphs the DOM (e.g. inside Repeaters)
            this._morphHandler = () => {
                this.$nextTick(() => {
                    if (document.getElementById(editorId) && !this.editor()) this._boot();
                });
            };
            document.addEventListener('livewire:morph', this._morphHandler);
        },

        destroy() {
            this.editor()?.destroy();
            if (this._morphHandler) document.removeEventListener('livewire:morph', this._morphHandler);
        },

        // ── Editor boot ────────────────────────────────────────────────────

        _boot() {
            // TinyMCE CDN may still be loading — retry until available
            if (!window.tinymce) { setTimeout(() => this._boot(), 120); return; }
            if (this.editor()) return;

            const skin = this._skin();

            window.tinymce.init({
                selector: `#${editorId}`,
                plugins,
                toolbar,
                height,
                min_height: minHeight,
                menubar,
                toolbar_sticky: toolbarSticky,
                toolbar_sticky_offset: toolbarStickyOffset,
                statusbar: false,
                promotion: false,
                license_key: 'gpl',
                skin,
                content_css: skin === 'oxide-dark' ? 'dark' : 'default',
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                images_upload_url: uploadUrl,
                images_upload_credentials: true,
                automatic_uploads: true,
                images_upload_handler: (blobInfo, progress) => this._upload(blobInfo, progress),
                ...customConfigs,
                setup: (editor) => {
                    editor.on('init', () => {
                        if (this.state) editor.setContent(this.state);
                    });
                    // Push content to Livewire on blur or any content change
                    editor.on('blur change', () => {
                        this.state = editor.getContent();
                    });
                    // Disable Filament modal x-trap while a TinyMCE dialog is open
                    editor.on('OpenWindow', () => {
                        this.$el.closest('[x-trap\\.noscroll]')?.setAttribute('x-trap.noscroll', 'false');
                    });
                    editor.on('CloseWindow', () => {
                        this.$el.closest('[x-trap\\.noscroll]')?.setAttribute('x-trap.noscroll', 'isOpen');
                    });
                },
            });
        },

        // ── Image upload ───────────────────────────────────────────────────

        _upload(blobInfo, progress) {
            return new Promise((resolve, reject) => {
                this.isUploading = true;

                const body = new FormData();
                body.append('file', blobInfo.blob(), blobInfo.filename());

                fetch(uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': uploadToken,
                        'Accept': 'application/json',
                    },
                    body,
                })
                    .then((r) => {
                        if (!r.ok) throw new Error(`HTTP ${r.status}`);
                        return r.json();
                    })
                    .then((data) => {
                        if (!data.location) throw new Error('Missing location in response');
                        resolve(data.location);
                    })
                    .catch((err) => reject(`Upload failed: ${err.message}`))
                    .finally(() => { this.isUploading = false; });
            });
        },
    };
}

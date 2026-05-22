/**
 * TinyMCE Alpine.js component for Filament.
 *
 * Loaded lazily via Filament's x-load / x-load-src mechanism.
 * Must be a default ESM export — called with NO arguments.
 *
 * Config is read from data-* attributes on the host element to avoid
 * double-quote escaping issues inside HTML attributes:
 *   data-state-path   — Livewire state path (e.g. "data.body")
 *   data-editor-config — JSON blob with all TinyMCE options
 */
export default function tinyeditor() {
    return {
        state: null,
        isUploading: false,

        /** @type {Record<string, any>} */
        _cfg: {},
        _morphHandler: null,

        // ── Helpers ────────────────────────────────────────────────────────

        editor() {
            return window.tinymce?.get(this._cfg.editorId) ?? null;
        },

        _skin() {
            const mode = this._cfg.darkMode;
            if (mode === 'force') return 'oxide-dark';
            if (mode === false)   return 'oxide';
            if (mode === 'class') return document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide';
            if (mode === 'media') return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide';
            // auto — honour this site's localStorage appearance key
            const pref = localStorage.getItem('appearance') ?? 'system';
            if (pref === 'dark') return 'oxide-dark';
            if (pref === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches) return 'oxide-dark';
            return 'oxide';
        },

        // ── Lifecycle ──────────────────────────────────────────────────────

        init() {
            // Read config from data attributes (avoids HTML attribute quote issues)
            const statePath = this.$el.dataset.statePath;
            this._cfg = JSON.parse(this.$el.dataset.editorConfig || '{}');

            if (this._cfg.disabled) return;

            // Two-way bind to Livewire state
            this.state = this.$wire.$entangle(statePath);

            this._boot();

            // Sync editor when Livewire updates state from outside (e.g. reset)
            this.$watch('state', (value) => {
                const ed = this.editor();
                if (!ed || this.isUploading) return;
                if (value !== ed.getContent()) ed.setContent(value ?? '');
            });

            // Re-initialise after Livewire morphs the DOM (e.g. Repeater add-row)
            this._morphHandler = () => {
                this.$nextTick(() => {
                    if (document.getElementById(this._cfg.editorId) && !this.editor()) {
                        this._boot();
                    }
                });
            };
            document.addEventListener('livewire:morph', this._morphHandler);
        },

        destroy() {
            this.editor()?.destroy();
            if (this._morphHandler) {
                document.removeEventListener('livewire:morph', this._morphHandler);
            }
        },

        // ── Editor init ────────────────────────────────────────────────────

        _boot() {
            // TinyMCE CDN script may still be loading — retry until ready
            if (!window.tinymce) { setTimeout(() => this._boot(), 120); return; }
            if (this.editor()) return;

            const { editorId, plugins, toolbar, height, minHeight, menubar,
                    toolbarSticky, toolbarStickyOffset, customConfigs,
                    uploadUrl, uploadToken } = this._cfg;

            const skin = this._skin();

            window.tinymce.init({
                selector: `#${editorId}`,
                plugins: plugins ?? '',
                toolbar: toolbar ?? '',
                height: height ?? 500,
                min_height: minHeight ?? 300,
                menubar: menubar ?? false,
                toolbar_sticky: toolbarSticky ?? false,
                toolbar_sticky_offset: toolbarStickyOffset ?? 64,
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
                images_upload_handler: (blobInfo, progress) =>
                    this._upload(blobInfo, progress, uploadUrl, uploadToken),
                ...(customConfigs ?? {}),
                setup: (editor) => {
                    editor.on('init', () => {
                        if (this.state) editor.setContent(this.state);
                    });
                    // Push HTML to Livewire on blur or any content mutation
                    editor.on('blur change', () => {
                        this.state = editor.getContent();
                    });
                    // Disable Filament modal x-trap while a TinyMCE dialog is open
                    editor.on('OpenWindow', () => {
                        this.$el.closest('[x-trap\\.noscroll]')
                            ?.setAttribute('x-trap.noscroll', 'false');
                    });
                    editor.on('CloseWindow', () => {
                        this.$el.closest('[x-trap\\.noscroll]')
                            ?.setAttribute('x-trap.noscroll', 'isOpen');
                    });
                },
            });
        },

        // ── Image upload ───────────────────────────────────────────────────

        _upload(blobInfo, progress, uploadUrl, uploadToken) {
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

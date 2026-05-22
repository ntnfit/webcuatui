export default function tinyeditor({ state = null, statePath = null, editorConfig = null } = {}) {
    return {
        state,
        statePath,
        isUploading: false,
        _cfg: {},
        _morphHandler: null,

        editor() {
            return window.tinymce?.get(this._cfg.editorId) ?? null;
        },

        _skin() {
            const mode = this._cfg.darkMode;
            if (mode === 'force') return 'oxide-dark';
            if (mode === false) return 'oxide';
            if (mode === 'class') return document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide';
            if (mode === 'media') return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide';

            const pref = localStorage.getItem('appearance') ?? 'system';
            if (pref === 'dark') return 'oxide-dark';
            if (pref === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches) return 'oxide-dark';

            return 'oxide';
        },

        init() {
            this._cfg = editorConfig ?? JSON.parse(this.$el.dataset.editorConfig || '{}');
            this.statePath = this.statePath ?? this.$el.dataset.statePath;

            if (this._cfg.disabled) return;

            this._boot();

            this.$watch('state', (value) => {
                const ed = this.editor();
                if (!ed || this.isUploading) return;

                const content = this._contentFromState(value);
                if (content !== ed.getContent()) ed.setContent(content);
            });

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

        _boot() {
            if (!window.tinymce) {
                setTimeout(() => this._boot(), 120);
                return;
            }

            if (this.editor()) return;

            const targetEl = document.getElementById(this._cfg.editorId);
            if (!targetEl) {
                setTimeout(() => this._boot(), 120);
                return;
            }

            const {
                plugins,
                toolbar,
                height,
                minHeight,
                menubar,
                toolbarSticky,
                toolbarStickyOffset,
                customConfigs,
                uploadUrl,
                uploadToken,
            } = this._cfg;

            const skin = this._skin();

            window.tinymce.init({
                target: targetEl,
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
                images_upload_handler: (blobInfo, progress) => this._upload(blobInfo, progress, uploadUrl, uploadToken),
                ...(customConfigs ?? {}),
                setup: (editor) => {
                    editor.on('init', () => {
                        editor.setContent(this._contentFromState(this.state));
                    });
                    editor.on('blur change', () => {
                        this.state = editor.getContent();
                    });
                    editor.on('OpenWindow', () => {
                        this.$el.closest('[x-trap\\.noscroll]')?.setAttribute('x-trap.noscroll', 'false');
                    });
                    editor.on('CloseWindow', () => {
                        this.$el.closest('[x-trap\\.noscroll]')?.setAttribute('x-trap.noscroll', 'isOpen');
                    });
                },
            });
        },

        _contentFromState(value) {
            if (typeof value === 'string') return value;
            if (typeof value?.initialValue === 'string') return value.initialValue;

            return '';
        },

        _upload(blobInfo, progress, uploadUrl, uploadToken) {
            return new Promise((resolve, reject) => {
                this.isUploading = true;

                const body = new FormData();
                body.append('file', blobInfo.blob(), blobInfo.filename());

                fetch(uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': uploadToken,
                        Accept: 'application/json',
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
                    .finally(() => {
                        this.isUploading = false;
                    });
            });
        },
    };
}

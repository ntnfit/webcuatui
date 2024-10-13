<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{ state: $wire.entangle('{{ $getStatePath() }}'), initialized: false }"
        x-init="(() => {
            $nextTick(() => {
                if (!initialized) {
                    ClassicEditor
                        .create($refs.ckeditor, {
                            toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
                            ckfinder: {
                                // Nếu cần upload hình ảnh, bạn có thể cấu hình thêm ở đây.
                                // uploadUrl: '/your-upload-endpoint'
                            },
                        })
                        .then(editor => {
                            editor.model.document.on('change:data', () => {
                                state = editor.getData();
                            });
                            initialized = true;
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }
            });
        })()"
        x-cloak
        wire:ignore
    >
        <textarea
            x-model="state"
            x-ref="ckeditor"
            id="ckeditor-{{ $getId() }}"
            placeholder="{{ $getPlaceholder() }}"
        >{{ $getState() }}</textarea>
    </div>
</x-dynamic-component>

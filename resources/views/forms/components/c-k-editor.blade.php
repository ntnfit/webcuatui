<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"

    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"

    wire:ignore
>
{{--	<textarea wire:ignore--}}
{{--              wire:model.lazy="{{ $getId() }}"--}}
{{--              id="editor"--}}
{{--		{{ $attributes->merge(['class' => 'form-control']) }}--}}
{{-->	</textarea>--}}
    <textarea id="editor"></textarea>

        <textarea id="editor"></textarea>
    </div>
    @once
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    ClassicEditor
                        .create(document.querySelector('#editor'))
                        .catch(error => {
                            console.error(error);
                        });
                });
            </script>
        @endpush
    @endonce
</x-dynamic-component>

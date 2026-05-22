@php
    $editorId  = 'tinyeditor-' . $getId();
    $statePath = $getStatePath();
    $disabled  = $isDisabled();
    $tinyJsUrl = asset('vendor/tinymce/tinymce.min.js');
    $cssUrl    = \Filament\Support\Facades\FilamentAsset::getStyleHref('tinymce-editor');
    $editorConfig = [
        'editorId'            => $editorId,
        'plugins'             => $getPlugins(),
        'toolbar'             => $getToolbar(),
        'height'              => $getHeight(),
        'minHeight'           => $getMinHeight(),
        'menubar'             => $isMenuBarVisible(),
        'toolbarSticky'       => $isToolbarSticky(),
        'toolbarStickyOffset' => $getToolbarStickyOffset(),
        'darkMode'            => $getDarkMode(),
        'customConfigs'       => json_decode($getCustomConfigs()),
        'uploadUrl'           => $getUploadUrl(),
        'uploadToken'         => csrf_token(),
        'disabled'            => $disabled,
    ];
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">

    @once
    <style>
        .fi-fo-repeater .tox-tinymce,
        .fi-fo-builder  .tox-tinymce,
        [wire\:sortable] .tox-tinymce  { z-index: 1    !important; }
        .fi-fo-repeater .tox-tinymce-aux,
        .fi-fo-builder  .tox-tinymce-aux,
        [wire\:sortable] .tox-tinymce-aux { z-index: 9999 !important; }
        .fi-fo-repeater .tox .tox-toolbar,
        .fi-fo-builder  .tox .tox-toolbar { position: relative !important; }
    </style>
    @endonce

    {{--
        x-load-js / x-load-css must be JSON arrays.
        Use {{ json_encode() }} so double-quotes are HTML-escaped to &quot;
        (getAttribute() decodes them back → valid JSON for Filament's x-load parser).

        x-data stays simple ("tinyeditor" with no inline args) to avoid
        @js() outputs breaking the double-quoted attribute.
        All config is passed via data-* + @json() which uses " for quotes.
    --}}
    <div
        wire:ignore
        x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('tinyeditor') }}"
        x-load-js="{{ json_encode([$tinyJsUrl]) }}"
        x-load-css="{{ json_encode(array_filter([$cssUrl])) }}"
        x-data="tinyeditor"
        data-state-path="{{ $statePath }}"
        data-editor-config="@json($editorConfig)"
    >
        @unless ($disabled)
            <textarea id="{{ $editorId }}"></textarea>
        @else
            <div class="prose dark:prose-invert max-w-none px-4 py-3"
                 x-text=""
                 x-init="$el.innerHTML = ($wire.get('{{ $statePath }}') ?? '')">
            </div>
        @endunless
    </div>

</x-dynamic-component>

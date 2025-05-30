@php
    $name = $getName() ?? config('filament-ckeditor-field.upload_url');
    $uploadUrl = $getUploadUrl();
@endphp
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.2.0/ckeditor5.css" />
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    wire:ignore
>
    <script type="importmap">
        {
            "imports": {
                "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.2.0/ckeditor5.js",
                "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.2.0/"
            }
        }
    </script>
    <script type="text/javascript">
        // Initialize the instance and event listener flags if not already set
        if (!window.ckeditorInstances["ckeditor-{{ $name }}"]) {
            window.ckeditorInstances["ckeditor-{{ $name }}"] = {
                instance: null,
                eventListenerAdded: false
            };
        }

        function createCKEditor() {
            // Destroy existing editor to prevent duplicates
            if (window.ckeditorInstances["ckeditor-{{ $name }}"].instance) {
                // destroyCKEditor();
                return;
            }

            // Create new editor instance
            ClassicEditor
                .create(document.querySelector('#ckeditor-{{ $name }}'), {
                    plugins: [
                        AccessibilityHelp,
                        Alignment,
                        Autoformat,
                        AutoImage,
                        AutoLink,
                        Autosave,
                        BlockQuote,
                        Bold,
                        Code,
                        CodeBlock,
                        Essentials,
                        FindAndReplace,
                        FontBackgroundColor,
                        FontColor,
                        FontFamily,
                        FontSize,
                        GeneralHtmlSupport,
                        Heading,
                        Highlight,
                        HorizontalLine,
                        HtmlComment,
                        HtmlEmbed,
                        ImageBlock,
                        ImageCaption,
                        ImageInline,
                        ImageInsert,
                        ImageInsertViaUrl,
                        ImageResize,
                        ImageStyle,
                        ImageTextAlternative,
                        ImageToolbar,
                        ImageUpload,
                        Indent,
                        IndentBlock,
                        Italic,
                        Link,
                        LinkImage,
                        List,
                        ListProperties,
                        MediaEmbed,
                        PageBreak,
                        Paragraph,
                        PasteFromOffice,
                        RemoveFormat,
                        SelectAll,
                        ShowBlocks,
                        SimpleUploadAdapter,
                        SourceEditing,
                        SpecialCharacters,
                        SpecialCharactersArrows,
                        SpecialCharactersCurrency,
                        SpecialCharactersEssentials,
                        SpecialCharactersLatin,
                        SpecialCharactersMathematical,
                        SpecialCharactersText,
                        Strikethrough,
                        Style,
                        Subscript,
                        Superscript,
                        Table,
                        TableCaption,
                        TableCellProperties,
                        TableColumnResize,
                        TableProperties,
                        TableToolbar,
                        TextTransformation,
                        TodoList,
                        Underline,
                        Undo
                    ],
                    toolbar: {
                        items: [
                            'undo',
                            'redo',
                            '|',
                            'sourceEditing',
                            'showBlocks',
                            '|',
                            'heading',
                            'style',
                            '|',
                            'fontSize',
                            'fontFamily',
                            'fontColor',
                            'fontBackgroundColor',
                            '|',
                            'bold',
                            'italic',
                            'underline',
                            '|',
                            'link',
                            'insertImage',
                            'insertTable',
                            'highlight',
                            'blockQuote',
                            'codeBlock',
                            '|',
                            'alignment',
                            '|',
                            'bulletedList',
                            'numberedList',
                            'todoList',
                            'outdent',
                            'indent'
                        ],
                        shouldNotGroupWhenFull: false
                    },
                    autosave: {
                        save( editor ) {
                            Livewire.dispatch('contentUpdated', { content: editor.getData(), editor: 'ckeditor-{{ $name }}' })
                        }
                    },
                    fontFamily: {
                        supportAllValues: true
                    },
                    fontSize: {
                        options: [10, 12, 14, 'default', 18, 20, 22],
                        supportAllValues: true
                    },
                    heading: {
                        options: [
                            {
                                model: 'paragraph',
                                title: 'Paragraph',
                                class: 'ck-heading_paragraph'
                            },
                            {
                                model: 'heading1',
                                view: 'h1',
                                title: 'Heading 1',
                                class: 'ck-heading_heading1'
                            },
                            {
                                model: 'heading2',
                                view: 'h2',
                                title: 'Heading 2',
                                class: 'ck-heading_heading2'
                            },
                            {
                                model: 'heading3',
                                view: 'h3',
                                title: 'Heading 3',
                                class: 'ck-heading_heading3'
                            },
                            {
                                model: 'heading4',
                                view: 'h4',
                                title: 'Heading 4',
                                class: 'ck-heading_heading4'
                            },
                            {
                                model: 'heading5',
                                view: 'h5',
                                title: 'Heading 5',
                                class: 'ck-heading_heading5'
                            },
                            {
                                model: 'heading6',
                                view: 'h6',
                                title: 'Heading 6',
                                class: 'ck-heading_heading6'
                            }
                        ]
                    },
                    htmlSupport: {
                        allow: [
                            {
                                name: /^.*$/,
                                styles: true,
                                attributes: true,
                                classes: true
                            }
                        ]
                    },
                    image: {
                        toolbar: [
                            'toggleImageCaption',
                            'imageTextAlternative',
                            '|',
                            'imageStyle:inline',
                            'imageStyle:wrapText',
                            'imageStyle:breakText',
                            '|',
                            'resizeImage'
                        ]
                    },
                    link: {
                        addTargetToExternalLinks: true,
                        defaultProtocol: 'https://',
                        decorators: {
                            toggleDownloadable: {
                                mode: 'manual',
                                label: 'Downloadable',
                                attributes: {
                                    download: 'file'
                                }
                            }
                        }
                    },
                    list: {
                        properties: {
                            styles: true,
                            startIndex: true,
                            reversed: true
                        }
                    },
                    menuBar: {
                        isVisible: true
                    },
                    placeholder: 'Type or paste your content here!',
                    style: {
                        definitions: [
                            {
                                name: 'Article category',
                                element: 'h3',
                                classes: ['category']
                            },
                            {
                                name: 'Title',
                                element: 'h2',
                                classes: ['document-title']
                            },
                            {
                                name: 'Subtitle',
                                element: 'h3',
                                classes: ['document-subtitle']
                            },
                            {
                                name: 'Info box',
                                element: 'p',
                                classes: ['info-box']
                            },
                            {
                                name: 'Side quote',
                                element: 'blockquote',
                                classes: ['side-quote']
                            },
                            {
                                name: 'Marker',
                                element: 'span',
                                classes: ['marker']
                            },
                            {
                                name: 'Spoiler',
                                element: 'span',
                                classes: ['spoiler']
                            },
                            {
                                name: 'Code (dark)',
                                element: 'pre',
                                classes: ['fancy-code', 'fancy-code-dark']
                            },
                            {
                                name: 'Code (bright)',
                                element: 'pre',
                                classes: ['fancy-code', 'fancy-code-bright']
                            }
                        ]
                    },
                    table: {
                        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
                    },
                    @isset($uploadUrl)

                    simpleUpload: {
                        uploadUrl: '{{ $uploadUrl }}',
                        withCredentials: true,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }

                    @endisset
                })
                .then(editor => {
                    window.ckeditorInstances["ckeditor-{{ $name }}"].instance = editor;

                    // Listen to changes
                    editor.model.document.on('change:data', () => {
                        // Emit Livewire event
                        Livewire.dispatch('contentUpdated', { content: editor.getData(), editor: 'ckeditor-{{ $name }}' })
                    });
                })
                .catch(err => {
                    console.error(err);
                });
        }

        function destroyCKEditor() {
            if (window.ckeditorInstances["ckeditor-{{ $name }}"].instance) {
                window.ckeditorInstances["ckeditor-{{ $name }}"].instance.destroy()
                    .then(() => {
                        window.ckeditorInstances["ckeditor-{{ $name }}"].instance = null;
                    })
                    .catch(err => {
                        console.error('Failed to destroy editor:', err);
                    });
            }
        }
    </script>

    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            init() {
                // Remove existing event listeners to prevent duplicates
                document.removeEventListener('livewire:navigated', createCKEditor);
                document.removeEventListener('livewire:navigate', destroyCKEditor);

                // Add event listeners if not already added
                if (!window.ckeditorInstances['ckeditor-{{ $name }}'].eventListenerAdded) {
                    // todo: Look into the { once: true } option if necessary
                    document.addEventListener('livewire:navigated', createCKEditor/*, { once: true }*/);
                    document.addEventListener('livewire:navigate', destroyCKEditor);
                    window.ckeditorInstances['ckeditor-{{ $name }}'].eventListenerAdded = true;
                }

                Livewire.on('contentUpdated', (payload) => {
                    this.state = payload.content;
                });
            }
        }"
        x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('ckeditor'))]",
        x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('ckeditor'))]"
    >
        <textarea
            id="ckeditor-{{ $name }}"
            name="{{ $name }}"
            x-model="state"
        ></textarea>
    </div>
</x-dynamic-component>

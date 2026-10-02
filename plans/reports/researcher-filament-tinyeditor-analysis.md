# Filament TinyEditor Plugin Analysis & Reimplementation Guide

**Research Date:** 2026-05-22  
**Source:** https://github.com/amidesfahani/filament-tinyeditor (v4.x for Filament 4)  
**Status:** Complete source code extraction & analysis

---

## Executive Summary

The filament-tinyeditor plugin is a self-contained integration of TinyMCE 8.0.2 into Filament 4.x forms. Reimplementation requires:
- **1 PHP class** (TinyEditor extends Filament Field)
- **1 Alpine.js component** (~600 lines)
- **1 Blade view** (~120 lines)
- **1 config file** (optional but recommended)
- **1 CSS file** (RTL fixes only)
- **TinyMCE 8.0.2** loaded via CDN or npm package

**Complexity Level:** Medium  
**Integration Points:** Filament's form builder, Livewire file uploads, Alpine.js  
**Critical Dependencies:** Filament 4.0+, Livewire 3.x, Alpine.js 3.x

---

## Architecture Overview

### Class Hierarchy
```
Field (Filament base)
  └─ TinyEditor (extends Field)
       ├─ implements: Contracts\CanBeLengthConstrained
       ├─ uses: Concerns\CanBeLengthConstrained
       ├─ uses: Concerns\HasExtraInputAttributes
       ├─ uses: Concerns\HasFileAttachments
       ├─ uses: Concerns\HasPlaceholder
       ├─ uses: Concerns\InteractsWithToolbarButtons (custom trait)
       └─ uses: HasExtraAlpineAttributes (Filament)
```

### Component Flow

```
TinyEditor (PHP) 
  ↓
tiny-editor.blade.php (Blade template)
  ├─ Loads Alpine.js component via x-load-src
  ├─ Loads TinyMCE language files via x-load-js
  ├─ Loads CSS via x-load-css
  └─ Initializes Alpine data with x-data="tinyeditor({...})"
       ↓
tinyeditor Alpine component (JavaScript)
  ├─ Manages TinyMCE editor lifecycle
  ├─ Handles image uploads via Livewire
  ├─ Syncs content with Livewire state
  └─ Supports RTL, dark mode, modal interactions
```

---

## TinyMCE Configuration

### Version & Loading
- **TinyMCE Version:** 8.0.2
- **Loading Method:** CDN (jsDelivr) OR Composer package
- **Language Support:** 50+ languages via tinymce-i18n package v25.8.4
- **Language Package:** langs8 (supports v8.0.2)

**CDN URL:** `https://cdn.jsdelivr.net/npm/tinymce@8.0.2/tinymce.js`  
**Language URL Pattern:** `https://cdn.jsdelivr.net/npm/tinymce-i18n@25.8.4/langs8/{lang}.min.js`

### Composer Dependency
```
"tinymce/tinymce": "^8.0.2"
```

---

## File Structure & Implementation

### 1. TinyEditor.php (836 lines)

**Location:** `app/Forms/Components/TinyEditor.php`

**Key Properties:**
```php
protected string $view = 'filament-tinyeditor::tiny-editor';
protected string $profile = 'default';  // Profiles: default, simple, minimal, full, custom
protected bool $isSimple = false;
protected string $direction = 'ltr';   // 'ltr' | 'rtl' | 'auto'
protected int $height = 0;             // Editor height in px
protected int $minHeight = 500;
protected int $maxHeight = 0;
protected bool $toolbarSticky = true;
protected int $toolbarStickyOffset = 64;
protected string $toolbar;
protected string $darkMode = 'auto';   // 'auto' | 'force' | 'class' | 'media' | false | 'custom'
protected string $skinsUI = 'oxide';   // UI skin
protected string $skinsContent = 'default';  // Content area skin
protected string|Closure $language;    // Laravel locale code
protected bool $textPattern = true;
protected array $externalPlugins = [];
protected array|Closure $customConfigs = [];

// File attachment properties
protected ?FileAttachmentProvider $fileAttachmentProvider = null;
protected string|null $fileAttachmentsDiskName = null;
protected string|null $fileAttachmentsVisibility = null;
protected string|null $fileAttachmentsDirectory = null;

// Image upload properties
protected string|array|bool|Closure $imageList = false;
protected string|array|bool $imageClassList = false;
protected string|bool|Closure $imagesUploadUrl = false;
protected bool $imageAdvtab = false;
protected bool $imageDescription = true;

// URL handling
protected bool $relativeUrls = false;
protected bool $removeScriptHost = true;
protected bool $convertUrls = true;
```

**Key Methods:**

```php
// Builder pattern methods
->profile(string $profile)                    // Set predefined toolbar profile
->height(int $height)                         // Set editor height
->minHeight(int $minHeight)                   // Set minimum height (default: 500px)
->maxHeight(int $maxHeight)                   // Set maximum height
->width(int $width)                           // Set width
->minWidth(int $minWidth)                     // Set minimum width
->maxTinyWidth(int $maxWidth)                 // Set max width
->rtl() / ->ltr()                            // Set direction
->direction(string $direction)                // 'ltr' | 'rtl' | 'auto'
->showMenuBar()                               // Enable TinyMCE menu bar
->toolbarSticky(bool $sticky)                 // Enable sticky toolbar
->toolbarStickyOffset(int $offset)            // Sticky toolbar offset (px)
->toolbarMode(string $mode)                   // 'sliding' | 'wrap' | 'floating'
->toolbarLocation(string $location)           // 'auto' | 'top' | 'bottom'
->inlineTiny(bool $inline)                    // Enable inline mode
->language(string|Closure $lang)              // Set UI language
->imageList(string|array|Closure $list)       // Image picker list
->imagesUploadUrl(string|Closure $url)        // Image upload endpoint
->imageDescription(bool $enable)              // Enable image description field
->textPattern(bool $enable)                   // Enable text patterns (default: true)
->setRelativeUrls(bool $relative)             // Use relative URLs
->setRemoveScriptHost(bool $remove)           // Remove script host from URLs
->setConvertUrls(bool $convert)               // Convert absolute URLs
->fileAttachmentsDisk(string $disk)           // Storage disk for uploads
->fileAttachmentsVisibility(string $vis)      // File visibility (public|private)
->fileAttachmentsDirectory(string $dir)       // Upload directory path
->fileAttachmentProvider(FileAttachmentProvider $provider)

// Configuration methods
->setCustomConfigs(array|Closure $configs)    // Additional TinyMCE config
->setExternalPlugins(array $plugins)          // { name => url } of external plugins
->options(array $options)                     // Set multiple options at once

// Getter methods used in Blade
->getPlugins(): string                        // Returns space-separated plugin list
->getToolbar(): string                        // Returns toolbar config string
->getInterfaceLanguage(): string              // Returns TinyMCE locale code
->getLanguageId(): string                     // Returns language script ID
->getDirection(): string                      // Returns 'ltr' or 'rtl'
->getLanguageURL(string $lang): string        // CDN URL for language pack
->getId(): string                             // Unique field ID
->getCustomConfigs(): string                  // JSON string of custom configs
->getLicenseKey(): string                     // License key from config
```

**Important Methods - File Attachment Handling:**

```php
beforeStateDehydrated(function (...) {
    // Called when form is saved
    // 1. Finds all <img> tags with data-id attributes
    // 2. Moves files from temporary disk to permanent storage
    // 3. Updates image src and data-id attributes
    // 4. Cleans up unused temporary files
})

deleteUploadedImage(array $payload): void
    // Called when image is removed from editor
    // Deletes the corresponding uploaded file

getFileAttachmentProvider(): ?FileAttachmentProvider
    // Returns configured attachment handler

getFileAttachmentsVisibility(): ?string
    // Returns visibility level for uploaded files
```

**TinyMCE Locale Mapping:**

```php
// Converts Laravel locales to TinyMCE language codes
'ar' → 'ar'
'fa' → 'fa'
'de' → 'de'
'es' → 'es'
'fr' → 'fr-FR'
'it' → 'it'
'ja' → 'ja'
'pt_BR' → 'pt-BR'
'pt_PT' → 'pt-PT'
'ru' → 'ru'
'zh-CN' → 'zh-Hans'
'zh-TW' → 'zh-Hant'
// ... 40+ total language mappings
```

---

### 2. tiny-editor.blade.php (~120 lines)

**Location:** `resources/views/components/forms/tiny-editor.blade.php`

**Key Elements:**

```blade
<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    
    <!-- Z-index fixes for repeater/builder conflicts -->
    <style>
        .fi-fo-repeater .tox-tinymce { z-index: 1 !important; }
        .fi-fo-repeater .tox-tinymce-aux { z-index: 9999 !important; }
    </style>

    <!-- Main editor container -->
    <div x-data="{ isModalOpen: false }"
         x-init="$el.closest('.fi-modal')?.addEventListener(...)">
        
        <x-filament::input.wrapper :valid="!$errors->has($statePath)">
            
            <!-- Alpine component loader -->
            <div wire:ignore
                 x-load
                 x-load-src="{{ FilamentAsset::getAlpineComponentSrc('tinyeditor', ...) }}"
                 x-load-css="[...]"
                 x-load-js="[...]"
                 x-data="tinyeditor({
                     // State & paths
                     state: $wire.$entangle('{$statePath}'),
                     statePath: @js($statePath),
                     selector: '#{{ $textareaID }}',
                     
                     // Toolbar & plugins
                     plugins: '{{ $getPlugins() }}',
                     external_plugins: {{ $getExternalPlugins() }},
                     toolbar: '{{ $getToolbar() }}',
                     
                     // Dimensions
                     height: @js($getHeight()),
                     min_height: @js($getMinHeight()),
                     max_height: @js($getMaxHeight()),
                     width: @js($getWidth()),
                     min_width: @js($getMinWidth()),
                     max_width: @js($getTinyMaxWidth()),
                     
                     // Appearance
                     language: '{{ $getInterfaceLanguage() }}',
                     directionality: '{{ $getDirection() }}',
                     skin: ... // Dynamic based on dark mode setting,
                     content_css: ... // Dynamic,
                     toolbar_sticky: {{ $getToolbarSticky() ? 'true' : 'false' }},
                     toolbar_sticky_offset: {{ $getToolbarStickyOffset() }},
                     toolbar_mode: '{{ $getToolbarMode() }}',
                     toolbar_location: '{{ $getToolbarLocation() }}',
                     
                     // Images
                     image_list: {!! $getImageList() !!},
                     images_upload_url: @js($getImagesUploadUrl()),
                     image_description: @js($getImageDescription()),
                     
                     // Custom
                     custom_configs: {{ $getCustomConfigs() }},
                     license_key: '{{ $getLicenseKey() }}',
                     
                     // Setup callback
                     setup: (editor) => {
                         editor.on('blur change keyup', () => {
                             $wire.set('{{ $statePath }}', editor.getContent());
                         });
                     }
                 })">
                
                <!-- Disabled state: read-only HTML preview -->
                @if ($isDisabled)
                    <div x-html="state" 
                         class="prose dark:prose-invert ...">
                    </div>
                @else
                    <!-- Active state: hidden input, TinyMCE targets this -->
                    <input id="{{ $textareaID }}" 
                           type="hidden" 
                           x-ref="tinymce">
                @endif
            </div>
        </x-filament::input.wrapper>
    </div>
</x-dynamic-component>
```

**Critical Details:**

1. **State Binding:** Uses `$wire.$entangle('{$statePath}')` (two-way binding)
2. **Disabled State:** Shows read-only HTML preview with Tailwind prose styling
3. **Active State:** Uses hidden input element that TinyMCE attaches to
4. **Asset Loading:** Uses `x-load-src`, `x-load-css`, `x-load-js` for lazy loading
5. **Modals:** Special handling for Filament modals (open/close-tinyeditor-modal events)
6. **Z-index:** Fixes for repeaters/builders/sortables

---

### 3. Alpine.js Component (~600 lines)

**Location:** `resources/js/tinymce.js`

**Exported Function:**
```js
export default function tinyeditor({
    // State & paths
    state,                           // Two-way bound state
    statePath,                       // Path in Livewire (e.g., 'content')
    selector,                        // CSS selector for hidden input (e.g., '#tiny-editor-...')
    
    // TinyMCE config
    plugins,                         // Space-separated list
    external_plugins,                // { name: url } object
    toolbar,                         // Pipe-separated toolbar config
    text_patterns,                   // Boolean or object
    language,                        // TinyMCE locale code (e.g., 'en', 'fa')
    language_url,                    // CDN URL for language pack
    directionality,                  // 'ltr' or 'rtl'
    
    // Dimensions
    height, max_height, min_height,
    width, max_width, min_width,
    resize,                          // false, true, 'both', 'vertical', 'horizontal'
    
    // Appearance
    skin,                            // 'oxide', 'oxide-dark', 'tinymce-5', etc.
    content_css,                     // 'default', 'dark', custom URL
    content_style,                   // Raw CSS for editor content
    toolbar_sticky,                  // boolean
    toolbar_sticky_offset,           // pixels
    toolbar_mode,                    // 'sliding', 'wrap', 'floating'
    toolbar_location,                // 'auto', 'top', 'bottom'
    inline,                          // boolean (contenteditable mode)
    toolbar_persist,                 // boolean
    menubar,                         // boolean
    
    // Images
    image_list,                      // false, string (JSON), or array
    image_advtab,                    // boolean
    image_description,               // boolean
    image_class_list,                // string (JSON) or array
    images_upload_url,               // string (endpoint URL)
    
    // URLs
    relative_urls,                   // boolean
    remove_script_host,              // boolean
    convert_urls,                    // boolean
    
    // Custom
    custom_configs,                  // Object of additional TinyMCE config
    license_key,                     // 'gpl' or Cloud API key
    setup,                           // Function(editor) callback
    
    // Livewire
    disabled,                        // boolean
    key,                             // Component key for Livewire calls
    
    // UI
    placeholder,                     // string
    locale,                          // Laravel locale for i18n
    uploadingMessage,                // Message shown during upload
})
```

**Returned Alpine Object Structure:**

```js
{
    // Properties
    editor(),                        // Function: returns tinymce.get(editorId)
    state,                           // Current content
    isUploadingFile,                 // Flag for upload in progress
    isModalOpen,                     // Modal visibility flag
    
    // Lifecycle methods
    init(),                          // Called on Alpine init
        // 1. Initializes editor
        // 2. Sets up watchers for state changes
        // 3. Listens for file upload events
        // 4. Handles modal events
        // 5. Listens for Livewire morph/navigate events
        // 6. Sets up Repeater MutationObserver
    
    // Editor lifecycle
    initEditor(content),             // Initialize TinyMCE with content
    tryInitializeEditor(content),    // Retry until DOM element ready
    updateEditorContent(content),    // Update editor content from state
    putCursorToEnd(),                // Move cursor to end
    delete(),                        // Destroy editor instance
    
    // Event handlers
    getFileAttachmentUrl(fileKey),   // Call Livewire to get temp URL
    
    // Helpers
    isInsideRepeater(),              // Check if in repeater/builder/sortable
}
```

**Critical Implementation Details:**

#### 1. TinyMCE Configuration Object

The `initEditor()` method builds a comprehensive TinyMCE config:

```js
const tinyConfig = {
    selector: selector,
    language: language,
    language_url: language_url,
    directionality: directionality,
    statusbar: false,
    promotion: false,
    height: height,
    max_height: max_height,
    min_height: min_height,
    width: width,
    max_width: max_width,
    min_width: min_width,
    resize: resize,
    skin: skin,
    content_css: content_css,
    content_style: content_style,
    plugins: plugins,
    external_plugins: external_plugins,
    toolbar: toolbar,
    text_patterns: text_patterns,
    // Disable sticky toolbar in repeaters (causes positioning issues)
    toolbar_sticky: this.isInsideRepeater() ? false : toolbar_sticky,
    toolbar_sticky_offset: toolbar_sticky_offset,
    toolbar_mode: toolbar_mode,
    toolbar_location: toolbar_location,
    inline: inline,
    toolbar_persist: toolbar_persist,
    menubar: menubar,
    ui_mode: 'split',
    
    // Menu configuration
    menu: {
        file: { title: "File", items: "..." },
        edit: { title: "Edit", items: "..." },
        view: { title: "View", items: "..." },
        insert: { title: "Insert", items: "..." },
        format: { title: "Format", items: "..." },
        tools: { title: "Tools", items: "..." },
        table: { title: "Table", items: "..." },
        help: { title: "Help", items: "..." }
    },
    
    // Fonts
    font_size_formats: fontSizeFormats,
    fontfamily: fontFamilyFormats,
    font_family_formats: fontFamilyFormats,
    
    // URLs
    relative_urls: relative_urls,
    remove_script_host: remove_script_host,
    convert_urls: convert_urls,
    
    // Images
    image_list: image_list,
    image_advtab: image_advtab,
    image_description: image_description,
    image_class_list: image_class_list,
    images_upload_url: images_upload_url,
    
    // Merge custom configs last (override everything)
    ...custom_configs,
    
    // Setup callback
    setup: function(editor) {
        // Store editor instance in global map
        editors[statePath] = editor.id;
        
        // Event listeners
        editor.on("blur", () => { state = editor.getContent(); });
        editor.on("change", () => { state = editor.getContent(); });
        editor.on("init", (e) => {
            if (content != null) editor.setContent(content);
        });
        
        // Modal handling
        editor.on("OpenWindow", (e) => {
            // Disable x-trap on Filament modal
            target?.setAttribute("x-trap.noscroll", "false");
        });
        editor.on("CloseWindow", (e) => {
            // Re-enable x-trap
            target?.setAttribute("x-trap.noscroll", "isOpen");
        });
        
        // Custom setup callback
        if (typeof setup === "function") setup(editor);
    },
    
    // Image upload handler (critical!)
    images_upload_handler: (blobInfo, progress) => new Promise(...),
    
    // Image deletion callback
    removeImagesEventCallback: (imageSrc) => {...},
    
    // Init callback for mutation observer
    init_instance_callback: function(editor) {...},
    
    automatic_uploads: true,
};

tinymce.init(tinyConfig);
```

#### 2. Image Upload Handler

```js
images_upload_handler: (blobInfo, progress) => new Promise((success, failure) => {
    // 1. Mark file as uploading
    this.isUploadingFile = true;
    
    // 2. Generate unique file key (UUID-like)
    let fileKey = ...generateUUID();
    
    // 3. Dispatch form-processing-started event
    dispatchFormEvent(editor, 'form-processing-started', {
        message: uploadingMessage
    });
    
    // 4. Upload via Livewire
    this.$wire.upload(
        `componentFileAttachments.${statePath}.${fileKey}`,
        blobInfo.blob(),
        () => {
            // Success callback: get temp URL from Livewire
            this.getFileAttachmentUrl(fileKey).then((tempUrl) => {
                // Tag the image with data-id
                editor.once('SetContent', ({ content, ... }) => {
                    const imgs = editor.getBody().querySelectorAll('img:not([data-id])');
                    if (imgs.length > 0) {
                        const img = imgs[imgs.length - 1];
                        img.setAttribute('data-id', fileKey);
                    }
                });
                
                success(tempUrl);
                this.isUploadingFile = false;
                dispatchFormEvent(editor, 'form-processing-finished');
            });
        },
        (error) => {
            failure("Upload failed: " + error);
            this.isUploadingFile = false;
            dispatchFormEvent(editor, 'form-processing-finished');
        },
        (event) => {
            progress(event.detail.progress * 100);  // Update progress bar
        }
    );
})
```

**Critical Points:**
- Each image gets a unique `data-id` attribute for tracking
- Files are temporarily uploaded to Livewire's temporary disk
- On form save, the PHP `beforeStateDehydrated` hook moves them to permanent storage
- Unused temporary files are cleaned up

#### 3. State Watchers & Sync

```js
init() {
    // Watch for state changes (external updates)
    this.$watch('state', (value) => {
        if (!this.editor()) return;
        if (this.isUploadingFile) return;  // Don't refresh during upload
        
        if (value !== this.editor().getContent()) {
            this.updateEditorContent(value);
            this.putCursorToEnd();
        }
    });
    
    // Listen for file upload start/end events
    window.addEventListener('rich-editor-uploading-file', (event) => {
        if (event.detail.livewireId !== this.$wire.id) return;
        if (event.detail.key !== key) return;
        this.isUploadingFile = true;
    });
    
    window.addEventListener('rich-editor-uploaded-file', (event) => {
        this.isUploadingFile = false;
    });
}

// Editor change listeners
editor.on('blur', () => { state = editor.getContent(); });
editor.on('change', () => { state = editor.getContent(); });
```

#### 4. Repeater Support

```js
// After DOM morph (Livewire v3)
this._reinitOnMorph = () => {
    if (this.isUploadingFile) return;
    this.$nextTick(() => {
        const editorElement = document.querySelector(this.selector);
        if (editorElement && !this.editor()) {
            this.tryInitializeEditor(normalizeContent(this.state));
        }
    });
};
document.addEventListener('livewire:morph', this._reinitOnMorph);

// MutationObserver for repeater changes
const repeaterContainer = this.$el.closest('[wire\\:sortable]') || 
                         this.$el.closest('.fi-fo-repeater');
if (repeaterContainer) {
    this._repeaterObserver = new MutationObserver(() => {
        // Reinit if target element exists but editor is not initialized
    });
    this._repeaterObserver.observe(repeaterContainer, {
        childList: true,
        subtree: true
    });
}
```

---

### 4. Configuration File

**Location:** `config/filament-tinyeditor.php`

```php
<?php

return [
    // TinyMCE versions
    'version' => [
        'tiny' => '8.0.2',
        'language' => [
            'version' => '25.8.4',      // tinymce-i18n version
            'package' => 'langs8',       // Package for v8.x
        ],
        'licence_key' => env('TINY_LICENSE_KEY', 'no-api-key'),
    ],
    
    // 'cloud' = CDN, 'vendor' = composer package
    'provider' => 'cloud',
    
    // RTL support
    'direction' => 'ltr',  // or 'rtl' or 'auto'
    
    // Dark mode: 'auto'|'force'|'class'|'media'|false|'custom'
    'darkMode' => 'auto',
    
    // Skins
    'skins' => [
        'ui' => 'oxide',           // UI theme
        'content' => 'default',    // Content theme (shown in editor)
    ],
    
    // Toolbar profiles
    'profiles' => [
        'default' => [
            'plugins' => 'accordion autoresize codesample directionality advlist link image lists preview pagebreak searchreplace wordcount code fullscreen insertdatetime media table emoticons',
            'toolbar' => 'undo redo removeformat | fontfamily fontsize ... | bold italic ...',
            'upload_directory' => null,
            'custom_configs' => [],
            'external_plugins' => [],
            'image_list' => false,
            'images_upload_url' => false,
            'image_description' => true,
        ],
        
        'simple' => [
            'plugins' => 'autoresize directionality emoticons link wordcount',
            'toolbar' => 'removeformat | bold italic | rtl ltr | numlist bullist | link emoticons',
        ],
        
        'minimal' => [
            'plugins' => 'link wordcount',
            'toolbar' => 'bold italic link numlist bullist',
        ],
        
        'full' => [
            'plugins' => 'accordion autoresize codesample directionality advlist autolink link image lists charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media table emoticons template help',
            'toolbar' => 'undo redo removeformat | ... (very comprehensive)',
        ],
    ],
    
    // Custom language URLs (optional)
    'languages' => [
        // 'fa' => 'https://cdn.jsdelivr.net/npm/tinymce-i18n@25.8.4/langs8/fa.min.js',
        // 'ar' => 'https://cdn.jsdelivr.net/npm/tinymce-i18n@25.8.4/langs8/ar.min.js',
    ],
    
    // Extra toolbar configuration
    'extra' => [
        'toolbar' => [
            'fontsize' => '10px 12px 13px 14px 16px 18px 20px',
            'fontfamily' => 'Arial=arial,helvetica,sans-serif; Courier New=courier new,courier,monospace;',
            'content_style' => 'body { font-family: "Arial", sans-serif; }',
        ]
    ]
];
```

---

### 5. CSS File

**Location:** `resources/css/style.css`

```css
/* RTL fixes */
.tox[dir="rtl"] {
    font-family: Shabnam,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif !important;
}

.tox[dir="rtl"] .tox-label {
    padding: 0 0 4px 8px !important;
}

.tox .tox-form__group {
    margin-bottom: 12px !important;
}

.tox .tox-dropzone {
    background: transparent !important;
}

.tox .tox-dialog__body-nav-item {
    margin-bottom: 12px !important;
}

/* Z-index fixes for repeaters (in Blade template inline styles) */
.fi-fo-repeater .tox-tinymce,
.fi-fo-builder .tox-tinymce,
[wire\:sortable] .tox-tinymce {
    z-index: 1 !important;
}

.fi-fo-repeater .tox-tinymce-aux,
.fi-fo-builder .tox-tinymce-aux,
[wire\:sortable] .tox-tinymce-aux {
    z-index: 9999 !important;
}

.fi-fo-repeater .tox .tox-toolbar,
.fi-fo-builder .tox .tox-toolbar,
[wire\:sortable] .tox .tox-toolbar {
    position: relative !important;
}

.fi-fo-repeater .tox .tox-editor-header,
.fi-fo-builder .tox .tox-editor-header,
[wire\:sortable] .tox .tox-editor-header {
    position: relative !important;
    z-index: auto !important;
}
```

---

### 6. Service Provider

**Location:** `app/Providers/TinyEditorServiceProvider.php`

```php
<?php

namespace App\Providers;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TinyEditorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-tinyeditor')
            ->hasConfigFile()
            ->hasViews()
            ->hasAssets();
            
        // Optional: publish TinyMCE vendor files
        if (file_exists(base_path('vendor/tinymce/tinymce'))) {
            $this->publishes([
                base_path('vendor/tinymce/tinymce') => public_path('vendor/tinymce')
            ], 'public');
        }
    }

    public function packageBooted(): void
    {
        $tinyVersion = config('filament-tinyeditor.version.tiny', '8.0.2');
        $licenseKey = config('filament-tinyeditor.version.licence_key', 'no-api-key');
        
        // Build language asset list
        $languages = [];
        $tiny_languages = Tiny::getLanguages();  // All 50+ language URLs
        
        foreach ($tiny_languages as $locale => $url) {
            $languages[] = Js::make($locale, $url)->loadedOnRequest();
        }
        
        // Determine TinyMCE source
        $provider = config('filament-tinyeditor.provider', 'cloud');
        
        $mainJs = 'https://cdn.jsdelivr.net/npm/tinymce@' . $tinyVersion . '/tinymce.js';
        
        if ($licenseKey !== 'no-api-key') {
            // Use Cloud CDN with license key
            $mainJs = 'https://cdn.tiny.cloud/1/' . $licenseKey . '/tinymce/' . $tinyVersion . '/tinymce.min.js';
        }
        
        if ($provider === 'vendor') {
            // Use locally served files
            $mainJs = secure_asset('vendor/tinymce/tinymce.min.js');
        }
        
        // Register assets
        FilamentAsset::register([
            Css::make('tiny-css', __DIR__ . '/../../resources/css/style.css'),
            Js::make('tinymce', $mainJs),
            AlpineComponent::make('tinyeditor', __DIR__ . '/../../resources/js/tinymce.js'),
            ...$languages,
        ], package: 'filament-tinyeditor');
    }
}
```

---

## Integration Points

### 1. Filament Form Builder

```php
use App\Forms\Components\TinyEditor;

public function form(Form $form): Form
{
    return $form->schema([
        TinyEditor::make('content')
            ->profile('default')
            ->height(500)
            ->minHeight(400)
            ->columnSpan('full')
            ->required(),
            
        TinyEditor::make('excerpt')
            ->profile('simple')
            ->height(250)
            ->columnSpan('half'),
    ]);
}
```

### 2. Livewire File Uploads

- Uses Livewire's built-in file upload system
- Temporary files stored in: `storage/app/livewire-tmp/`
- Disk configured in `config/livewire.php` (default: 'local')
- On form save, `beforeStateDehydrated` moves files to permanent storage

### 3. State Synchronization

- Two-way binding via `$wire.$entangle('{$statePath}')`
- Alpine watches for external changes
- TinyMCE change/blur events update state
- Livewire morphs trigger reinit for repeaters

### 4. Dark Mode Support

```php
// Auto (system preference)
$darkMode = 'auto'
skin = localStorage.getItem('theme') === 'dark' ? 'oxide-dark' : 'oxide'

// Force
$darkMode = 'force'
skin = 'oxide-dark'

// CSS class (based on html[class] or document.body)
$darkMode = 'class'
skin = document.querySelector('html').classList.contains('dark') ? 'oxide-dark' : 'oxide'

// Media query
$darkMode = 'media'
skin = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'

// Custom
$darkMode = 'custom'
skin = $skinsUI()  // from config

// Disabled
$darkMode = false
skin = 'oxide'
```

---

## Configuration Options Reference

### Top-Level Builder Methods

| Method | Type | Default | Notes |
|--------|------|---------|-------|
| `profile(string)` | string | 'default' | One of: default, simple, minimal, full |
| `height(int)` | int | 0 | Editor height in pixels |
| `minHeight(int)` | int | 500 | Minimum height |
| `maxHeight(int)` | int | 0 | Maximum height (0 = unlimited) |
| `width(int)` | int | 0 | Editor width |
| `minWidth(int)` | int | 500 | Minimum width |
| `maxTinyWidth(int)` | int | 0 | Maximum width |
| `rtl()` / `ltr()` | void | - | Set text direction |
| `direction(string)` | string | 'ltr' | 'ltr', 'rtl', or 'auto' |
| `showMenuBar()` | void | false | Enable TinyMCE menu bar |
| `toolbarSticky(bool)` | bool | true | Sticky toolbar when scrolling |
| `toolbarStickyOffset(int)` | int | 64 | Offset from top (px) |
| `toolbarMode(string)` | string | 'sliding' | 'sliding', 'wrap', 'floating' |
| `toolbarLocation(string)` | string | 'auto' | 'auto', 'top', 'bottom' |
| `inlineTiny(bool)` | bool | false | Contenteditable mode |
| `language(string\|Closure)` | string | app()->getLocale() | Laravel locale code |
| `setRelativeUrls(bool)` | bool | false | Use relative URLs |
| `setRemoveScriptHost(bool)` | bool | true | Remove domain from URLs |
| `setConvertUrls(bool)` | bool | true | Convert absolute URLs |
| `fileAttachmentsDisk(string)` | string | config | Storage disk |
| `fileAttachmentsVisibility(string)` | string | 'public' | File visibility |
| `fileAttachmentsDirectory(string)` | string | 'uploads' | Upload directory |
| `imageList(array\|string)` | mixed | false | Array or JSON of image options |
| `imagesUploadUrl(string)` | string | '' | Image upload endpoint |
| `imageDescription(bool)` | bool | true | Show description field |
| `textPattern(bool)` | bool | true | Enable markdown-like patterns |
| `resize(bool\|string)` | mixed | false | 'both', 'vertical', 'horizontal' |
| `setCustomConfigs(array)` | array | [] | Additional TinyMCE config |
| `setExternalPlugins(array)` | array | [] | External plugin URLs |

### Profile Configuration (config file)

Each profile supports:
- `plugins`: Space-separated plugin list
- `toolbar`: Pipe-separated toolbar groups
- `upload_directory`: Directory for images
- `external_plugins`: { name => url } of plugins
- `custom_configs`: Additional TinyMCE config
- `image_list`: Image picker data
- `images_upload_url`: Image upload endpoint
- `image_description`: Show description field

---

## Key Implementation Challenges & Solutions

### 1. Z-index Issues in Repeaters

**Problem:** TinyMCE modals (dialogs, popups) appear behind repeater elements.

**Solution:** 
- Main editor: `z-index: 1`
- Modals/popups (`tox-tinymce-aux`): `z-index: 9999`
- Toolbar: `position: relative` (remove z-index)

### 2. Editor Not Showing in Repeaters

**Problem:** When repeater items are added, TinyMCE fails to initialize on new instances.

**Solution:**
- Listen to `livewire:morph` events
- Use `MutationObserver` on repeater container
- Retry initialization if target element exists but editor not initialized
- Skip reinit during file uploads

### 3. State Sync During File Upload

**Problem:** Livewire morph during upload causes editor refresh, losing upload state.

**Solution:**
- Set `isUploadingFile` flag during upload
- Skip state watchers and reinit when flag is true
- Listen to `rich-editor-uploading-file` and `rich-editor-uploaded-file` events

### 4. Modal Trapping in Filament

**Problem:** TinyMCE dialogs can't be interacted with due to `x-trap` on modal.

**Solution:**
- Listen to `editor.on('OpenWindow')` event
- Set `x-trap.noscroll="false"` on modal
- Listen to `editor.on('CloseWindow')`
- Restore `x-trap.noscroll="isOpen"`

### 5. File Attachment Lifecycle

**Problem:** Temporary files need to be moved to permanent storage and tracked.

**Solution:**
- Generate unique `data-id` for each image
- Store in temporary disk during upload
- On form save, `beforeStateDehydrated` hook:
  - Parses HTML for images with `data-id`
  - Moves from temp to permanent storage
  - Updates image src attributes
  - Cleans up unused temp files

### 6. Language Loading

**Problem:** TinyMCE language packs must load asynchronously.

**Solution:**
- Load language pack via `x-load-js` (lazy)
- Pass `language_url` to Alpine component
- TinyMCE fetches pack on init

---

## Default Toolbar Profiles

### Default
```
undo redo removeformat | fontfamily fontsize fontsizeinput font_size_formats styles | 
bold italic underline | rtl ltr | alignjustify alignleft aligncenter alignright | 
numlist bullist outdent indent | forecolor backcolor | blockquote table toc hr | 
image link media codesample emoticons | wordcount fullscreen
```

### Simple
```
removeformat | bold italic | rtl ltr | numlist bullist | link emoticons
```

### Minimal
```
bold italic link numlist bullist
```

### Full
```
undo redo removeformat | fontfamily fontsize fontsizeinput font_size_formats styles | 
bold italic underline | rtl ltr | alignjustify alignright aligncenter alignleft | 
numlist bullist outdent indent accordion | forecolor backcolor | blockquote table toc hr | 
image link anchor media codesample emoticons | visualblocks print preview wordcount fullscreen help
```

---

## Supported TinyMCE Plugins

**Default Plugins:**
```
accordion autoresize codesample directionality advlist 
link image lists preview pagebreak searchreplace wordcount 
code fullscreen insertdatetime media table emoticons
```

**Full Plugins:**
```
accordion autoresize codesample directionality advlist autolink 
link image lists charmap preview anchor pagebreak searchreplace 
wordcount visualblocks visualchars code fullscreen insertdatetime 
media table emoticons template help
```

**Simple Plugins:**
```
autoresize directionality emoticons link wordcount
```

---

## Asset Registration

Assets are registered and loaded lazily via Filament's asset system:

1. **CSS:** `style.css` (RTL fixes only)
2. **JavaScript (Main):** 
   - CDN: `https://cdn.jsdelivr.net/npm/tinymce@8.0.2/tinymce.js`
   - Cloud: `https://cdn.tiny.cloud/1/{KEY}/tinymce/8.0.2/tinymce.min.js`
   - Vendor: `secure_asset('vendor/tinymce/tinymce.min.js')`
3. **Alpine Component:** `tinymce.js` (exported as ESM)
4. **Language Packs:** Loaded on-request by ID (50+ variants)

---

## Testing Checklist

- [ ] Editor initializes on page load
- [ ] Content syncs with Livewire state on blur/change
- [ ] File uploads work and images appear
- [ ] Images persist after form save
- [ ] Editor works in repeaters (add/remove items)
- [ ] Editor works in modals (open/close)
- [ ] RTL direction works correctly
- [ ] Dark mode toggles correctly
- [ ] All toolbar buttons functional
- [ ] Language switching works
- [ ] Disabled state shows read-only preview
- [ ] Custom configs merge properly
- [ ] External plugins load
- [ ] Sticky toolbar works
- [ ] Image descriptions save

---

## Source Code References

- **Main Class:** `/src/TinyEditor.php` (836 lines)
- **View:** `/resources/views/tiny-editor.blade.php` (~120 lines)
- **Alpine Component:** `/resources/js/tinymce.js` (~600 lines)
- **Service Provider:** `/src/TinyeditorServiceProvider.php` (78 lines)
- **Config:** `/config/filament-tinyeditor.php`
- **CSS:** `/resources/css/style.css`
- **Version:** TinyMCE 8.0.2, TinyMCE i18n 25.8.4

---

## Not Covered by This Plugin

- Custom TinyMCE plugins beyond external URLs
- Image cropping/resize UI
- Document merge tags
- Version/revision tracking
- Real-time collaboration
- Comments/annotations
- Template insertion (only TinyMCE built-in support)

---

## Unresolved Questions

None. All source code extracted and documented.

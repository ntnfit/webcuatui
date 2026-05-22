<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;
use Illuminate\Support\Facades\URL;

class TinyEditor extends Field
{
    protected string $view = 'forms.components.tiny-editor';

    protected string $profile = 'default';
    protected int $height = 500;
    protected int $minHeight = 300;
    protected bool $showMenuBar = false;
    protected bool $toolbarSticky = false;
    protected int $toolbarStickyOffset = 64;
    protected string|false $darkMode = 'auto';
    protected array $customConfigs = [];
    protected ?string $uploadDisk = null;
    protected ?string $uploadDirectory = null;

    public function profile(string $profile): static
    {
        $this->profile = $profile;
        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;
        return $this;
    }

    public function minHeight(int $minHeight): static
    {
        $this->minHeight = $minHeight;
        return $this;
    }

    public function showMenuBar(bool $show = true): static
    {
        $this->showMenuBar = $show;
        return $this;
    }

    public function toolbarSticky(bool $sticky = true): static
    {
        $this->toolbarSticky = $sticky;
        return $this;
    }

    public function toolbarStickyOffset(int $offset): static
    {
        $this->toolbarStickyOffset = $offset;
        return $this;
    }

    public function darkMode(string|false $mode): static
    {
        $this->darkMode = $mode;
        return $this;
    }

    public function setCustomConfigs(array $configs): static
    {
        $this->customConfigs = $configs;
        return $this;
    }

    /** Mirror Filament RichEditor API for drop-in replacement */
    public function fileAttachmentsDisk(string $disk): static
    {
        $this->uploadDisk = $disk;
        return $this;
    }

    public function fileAttachmentsVisibility(string $visibility): static
    {
        // visibility is implicit from disk; kept for API compatibility
        return $this;
    }

    public function fileAttachmentsDirectory(string $directory): static
    {
        $this->uploadDirectory = $directory;
        return $this;
    }

    // ─── Getters used in blade ────────────────────────────────────────────────

    public function getPlugins(): string
    {
        return config("filament-tinyeditor.profiles.{$this->profile}.plugins", '');
    }

    public function getToolbar(): string
    {
        return config("filament-tinyeditor.profiles.{$this->profile}.toolbar", '');
    }

    public function getHeight(): int { return $this->height; }

    public function getMinHeight(): int { return $this->minHeight; }

    public function isMenuBarVisible(): bool { return $this->showMenuBar; }

    public function isToolbarSticky(): bool { return $this->toolbarSticky; }

    public function getToolbarStickyOffset(): int { return $this->toolbarStickyOffset; }

    public function getDarkMode(): string|false { return $this->darkMode; }

    public function getCustomConfigs(): string
    {
        return json_encode($this->customConfigs ?: (object) []);
    }

    public function getUploadUrl(): string
    {
        $disk = $this->uploadDisk ?? config('filament-tinyeditor.upload_disk', 'public');
        $dir  = $this->uploadDirectory ?? config('filament-tinyeditor.upload_directory', 'uploads');

        // Signed URL binds disk/directory so they can't be tampered with client-side
        return URL::temporarySignedRoute('tinymce.upload', now()->addDay(), [
            'disk' => $disk,
            'dir'  => $dir,
        ]);
    }
}

<?php

namespace App\Forms\Components;


use Closure;
use Filament\Forms\Components\Field;

class CKEditor extends Field
{
    protected string | Closure $content = '';

    protected string $name = 'ckeditor';

    protected ?string $uploadUrl = null;

    protected string $view = 'forms.components.c-k-editor';

    public static function make(string $name = 'ckeditor', ?string $uploadUrl = null): static
    {
        return app(static::class, [
            'name' => $name,
            'uploadUrl' => $uploadUrl,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);
    }

    public function uploadUrl(string | Closure | null $uploadUrl): self
    {
        $this->uploadUrl = $uploadUrl;

        return $this;
    }

    public function content(string | Closure $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): string
    {
        return $this->evaluate($this->content);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUploadUrl(): ?string
    {
        return $this->evaluate($this->uploadUrl);
    }
}

<?php

namespace App\Filament\Pages;

use App\Services\BackupService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\WithFileUploads;

class BackupManager extends Page
{
    use WithFileUploads;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static ?string $navigationLabel = 'Backup & Restore';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.backup-manager';

    public $restoreFile = null;

    public array $backups = [];

    public function mount(): void
    {
        $this->loadBackups();
    }

    public function loadBackups(): void
    {
        $this->backups = app(BackupService::class)->listBackups();
    }

    public function createBackup(): void
    {
        try {
            $filename = app(BackupService::class)->createBackup();

            Notification::make()
                ->title("Backup created: {$filename}")
                ->success()
                ->send();

            $this->loadBackups();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Backup failed: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function downloadBackup(string $filename): mixed
    {
        $service = app(BackupService::class);
        $path = $service->getBackupPath($filename);

        if (! file_exists($path)) {
            Notification::make()->title('File not found.')->danger()->send();
            return null;
        }

        return response()->streamDownload(function () use ($path) {
            $fp = fopen($path, 'rb');
            while (! feof($fp)) {
                echo fread($fp, 8192);
                flush();
            }
            fclose($fp);
        }, $filename, ['Content-Type' => 'application/zip']);
    }

    public function deleteBackup(string $filename): void
    {
        try {
            app(BackupService::class)->deleteBackup($filename);

            Notification::make()
                ->title("Deleted: {$filename}")
                ->success()
                ->send();

            $this->loadBackups();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Delete failed: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function restoreBackup(): void
    {
        $this->validate(['restoreFile' => 'required|file|mimes:zip|max:512000']);

        try {
            $tmpPath = $this->restoreFile->getRealPath();
            app(BackupService::class)->restoreFromZip($tmpPath);

            Notification::make()
                ->title('Restore completed successfully.')
                ->success()
                ->send();

            $this->restoreFile = null;
            $this->loadBackups();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Restore failed: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}

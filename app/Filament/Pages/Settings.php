<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use App\Models\settings as SettingsModel;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Log;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.pages.settings';
    protected  SettingsModel $settings;
    public ?array $data = [];
    protected $site_name;
    public function mount(): void
    {
        $this->data = SettingsModel::first()?->toArray() ?: [];
        if (isset($this->data['site_logo']) && is_string($this->data['site_logo'])) {
            $this->data['site_logo'] = [
                'name' => $this->data['site_logo'],
            ];
        }
        if (isset($this->data['site_favicon']) && is_string($this->data['site_favicon'])) {
            $this->data['site_favicon'] = [
                'name' => $this->data['site_favicon'],
            ];
        }
        $this->data['more_configs'] = $this->data['more_configs'] ?? [];
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Application')
                            ->icon('heroicon-o-tv')
                            ->schema([
                                TextInput::make('site_name')
                                ->label('Site Name')
                                ->required()
                                ->placeholder('Enter the site name'),
                                TextInput::make('site_description')
                                ->label('Site Description')
                                ->required()
                                ->placeholder('Enter the site description'),
                                Grid::make()->schema([
                                    FileUpload::make('site_logo')
                                        ->label('Site Logo')
                                        ->image()
                                        ->directory('assets')
                                        ->visibility('public')
                                        ->moveFiles()
                                        ->imageEditor()
                                        ->getUploadedFileNameForStorageUsing(fn () => 'site_logo.png')
                                        ->columnSpan(2),
                                    FileUpload::make('site_favicon')
                                        ->label('Site Favicon')
                                        ->image()
                                        ->directory('assets')
                                        ->visibility('public')
                                        ->moveFiles()
                                        ->getUploadedFileNameForStorageUsing(fn () => 'site_favicon.ico')
                                        ->columnSpan(2),
                                ])
                                    ->columns(4),


                            ]),
                    ])
            ])->statePath('data');

    }
    public function create(): void
    {
       // dd($this->form->getState());
        $data = $this->form->getState();
        $data = $this->clearVariables($data);
        SettingsModel::updateOrCreate([], $data);
        $this->successNotification(__('filament-general-settings::default.settings_saved'));
    }
    private function successNotification(string $title): void
    {
        Notification::make()
            ->title($title)
            ->success()
            ->send();
    }

    private function clearVariables(array $data): array
    {
        unset(
            $data['seo_preview'],
            $data['seo_description'],
            $data['default_email_provider'],
            $data['smtp_host'],
            $data['smtp_port'],
            $data['smtp_encryption'],
            $data['smtp_timeout'],
            $data['smtp_username'],
            $data['smtp_password'],
            $data['mailgun_domain'],
            $data['mailgun_secret'],
            $data['mailgun_endpoint'],
            $data['postmark_token'],
            $data['amazon_ses_key'],
            $data['amazon_ses_secret'],
            $data['amazon_ses_region'],
            $data['mail_to'],
        );

        return $data;
    }
}

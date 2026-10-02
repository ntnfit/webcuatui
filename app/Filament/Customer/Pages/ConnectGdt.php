<?php

namespace App\Filament\Customer\Pages;

use App\Filament\Customer\Resources\TaxInvoices\TaxInvoiceResource;
use App\Models\Company;
use App\Services\Gdt\GdtException;
use App\Services\Gdt\GdtSessionService;
use App\Services\Gdt\TaxRateLimiter;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use RuntimeException;

/**
 * Logs the active company into the GDT portal. The portal password and captcha answer live only
 * in this request; the page stores nothing but the token the portal returns.
 */
class ConnectGdt extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationLabel = 'Kết nối cổng thuế';

    protected static ?string $title = 'Kết nối cổng thuế';

    protected static ?string $slug = 'connect-gdt';

    protected static ?int $navigationSort = 1;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public ?string $captchaKey = null;

    public ?string $captchaSvg = null;

    public function mount(): void
    {
        $this->form->fill(['username' => $this->company()->mst]);
        $this->loadCaptcha();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Trạng thái')->components([
                Html::make(fn () => new HtmlString($this->statusHtml())),
            ]),
            Section::make('Đăng nhập cổng hoadondientu.gdt.gov.vn')
                ->description('Thông tin đăng nhập chỉ dùng một lần để lấy phiên làm việc và không được lưu lại.')
                ->components([
                    TextInput::make('username')->label('Tên đăng nhập (mã số thuế)')->required()->maxLength(50),
                    TextInput::make('password')->label('Mật khẩu cổng thuế')->password()->revealable()->required()
                        ->autocomplete('off')->maxLength(255),
                    Html::make(fn () => new HtmlString($this->captchaHtml())),
                    TextInput::make('captcha')->label('Mã captcha')->required()->maxLength(10)->autocomplete('off'),
                ]),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('connect')
                ->footer([
                    Actions::make([
                        Action::make('connect')->label('Kết nối')->submit('connect'),
                        Action::make('refreshCaptcha')->label('Đổi captcha')->color('gray')->action(fn () => $this->loadCaptcha()),
                    ]),
                ]),
        ]);
    }

    public function connect(GdtSessionService $sessions, TaxRateLimiter $limiter): void
    {
        $data = $this->form->getState();
        // Drop secrets from component state immediately, whatever happens next.
        $this->data = ['username' => $data['username']];

        try {
            $limiter->hit('connect', $this->company(), Filament::auth()->user());
            $sessions->connect($this->company(), $data['username'], $data['password'], $data['captcha'], (string) $this->captchaKey);
        } catch (GdtException|RuntimeException $e) {
            Notification::make()->title('Kết nối thất bại')->body($e->getMessage())->danger()->send();
            $this->loadCaptcha();

            return;
        }

        Notification::make()->title('Đã kết nối cổng thuế')->success()->send();
        $this->redirect(TaxInvoiceResource::getUrl());
    }

    public function disconnect(GdtSessionService $sessions): void
    {
        $sessions->disconnect($this->company());
        Notification::make()->title('Đã ngắt kết nối')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('disconnect')->label('Ngắt kết nối')->color('danger')->requiresConfirmation()
                ->visible(fn () => app(GdtSessionService::class)->activeSession($this->company()) !== null)
                ->action(fn () => $this->disconnect(app(GdtSessionService::class))),
        ];
    }

    public function loadCaptcha(): void
    {
        $this->captchaKey = null;
        $this->captchaSvg = null;
        $this->data['captcha'] = null;

        try {
            $captcha = app(GdtSessionService::class)->captcha();
            $this->captchaKey = $captcha['key'] ?? null;
            $this->captchaSvg = $captcha['content'] ?? null;
        } catch (GdtException $e) {
            Notification::make()->title('Không lấy được captcha')->body($e->getMessage())->warning()->send();
        }
    }

    private function company(): Company
    {
        /** @var Company */
        return Filament::getTenant();
    }

    private function statusHtml(): string
    {
        $session = app(GdtSessionService::class)->activeSession($this->company());

        if (! $session) {
            return '<p>Công ty <b>'.e($this->company()->name).'</b> chưa kết nối cổng thuế hoặc phiên đã hết hạn.</p>';
        }

        return '<p>Đã kết nối. Phiên hết hạn lúc <b>'.e($session->expires_at->timezone(config('app.timezone'))->format('H:i d/m/Y')).'</b>.</p>';
    }

    /** The captcha is an SVG from the portal; rendering it through <img> keeps any markup inert. */
    private function captchaHtml(): string
    {
        if (! $this->captchaSvg) {
            return '<p>Chưa có captcha, hãy bấm "Đổi captcha".</p>';
        }

        return '<img alt="captcha" style="height:60px;background:#fff" src="data:image/svg+xml;base64,'.base64_encode($this->captchaSvg).'"/>';
    }
}

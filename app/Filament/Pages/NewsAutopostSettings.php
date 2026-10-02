<?php

namespace App\Filament\Pages;

use App\Filament\Pages\NewsAutopost\NewsSettingsFormSchema;
use App\Jobs\RunNewsCrawl;
use App\Models\User;
use App\Services\News\ApiKeyTester;
use App\Services\News\NewsSettings;
use App\Services\News\Pipeline\DailyQuota;
use App\Services\News\Pipeline\NewsPipeline;
use App\Services\News\Pipeline\RunOptions;
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
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Throwable;

/**
 * Admin page "Cấu hình tin tự động": every knob of the news auto-poster. Values are stored in the
 * database and override config/news.php. API keys are write-only here (stored encrypted, only a
 * configured / not configured status is ever shown).
 */
class NewsAutopostSettings extends Page
{
    private const KEY_LABELS = ['anthropic' => 'Anthropic (Claude)', 'unsplash' => 'Unsplash', 'pexels' => 'Pexels'];

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Tin tự động';

    protected static ?string $navigationLabel = 'Cấu hình tin tự động';

    protected static ?string $title = 'Cấu hình tin tự động';

    protected static ?string $slug = 'cau-hinh-tin-tu-dong';

    protected static ?int $navigationSort = 1;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** @var list<array{title: string, source: string, score: int, url: string}> */
    public array $dryRunPreview = [];

    /** Only back-office users (the admin guard) may open this page; customers never can. */
    public static function canAccess(): bool
    {
        return Filament::auth()->user() instanceof User;
    }

    public function mount(): void
    {
        $this->form->fill(NewsSettingsFormSchema::load(app(NewsSettings::class)));
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(NewsSettingsFormSchema::tabs());
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Html::make(fn () => new HtmlString($this->statusHtml())),
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([Actions::make([Action::make('save')->label('Lưu cấu hình')->submit('save')])]),
            $this->keysSection(),
            Html::make(fn () => new HtmlString($this->previewHtml())),
        ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $settings = app(NewsSettings::class);

        foreach (NewsSettingsFormSchema::KEYS as $key => $cast) {
            $value = data_get($state, $key);
            $settings->set($key, match ($cast) {
                'bool' => (bool) $value,
                'int' => (int) $value,
                'nullable-int' => filled($value) ? (int) $value : null,
                'array' => array_values(array_filter((array) $value, 'filled')),
                default => (string) ($value ?? ''),
            });
        }

        Notification::make()->title('Đã lưu cấu hình')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('dryRun')->label('Chạy thử (dry-run)')->icon('heroicon-o-beaker')->color('gray')
                ->action(function () {
                    $result = app(NewsPipeline::class)->run(new RunOptions(dryRun: true));
                    $this->dryRunPreview = $result->preview->map(fn ($i) => [
                        'title' => $i->title, 'source' => (string) $i->source?->name, 'score' => (int) $i->score, 'url' => $i->url,
                    ])->all();
                    Notification::make()->title('Chạy thử xong: '.count($this->dryRunPreview).' tin sẽ được đăng')->success()->send();
                }),
            Action::make('runNow')->label('Chạy ngay')->icon('heroicon-o-play')->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Chạy tin tự động ngay bây giờ?')
                ->modalDescription('Tác vụ chạy nền, tôn trọng giới hạn số bài mỗi ngày và sẽ gọi Claude (tốn phí API).')
                ->action(function () {
                    if (! app(NewsSettings::class)->enabled()) {
                        Notification::make()->title('Đang tắt công tắc tổng')->body('Bật tin tự động rồi chạy lại.')->warning()->send();

                        return;
                    }
                    RunNewsCrawl::dispatch();
                    Notification::make()->title('Đã đưa vào hàng đợi')->body('Xem kết quả ở mục Nhật ký chạy.')->success()->send();
                }),
        ];
    }

    private function keysSection(): Section
    {
        $settings = app(NewsSettings::class);
        $components = [Text::make('Key được lưu mã hóa trong cơ sở dữ liệu và không bao giờ hiển thị lại. Nếu để trống, hệ thống dùng biến môi trường tương ứng.')];

        foreach (self::KEY_LABELS as $provider => $label) {
            $components[] = Text::make(fn () => $label.': '.match ($settings->secretSource($provider)) {
                'db' => 'Đã cấu hình (lưu trong hệ thống)',
                'env' => 'Đã cấu hình (biến môi trường)',
                default => 'Chưa cấu hình',
            })->weight('bold');
            $components[] = Actions::make([
                Action::make("test_{$provider}")->label('Kiểm tra kết nối')->color('gray')->icon('heroicon-o-signal')
                    ->action(function () use ($provider, $label) {
                        $result = app(ApiKeyTester::class)->test($provider);
                        Notification::make()->title($label.($result['ok'] ? ': OK' : ': lỗi'))->body($result['message'])->{$result['ok'] ? 'success' : 'danger'}()->send();
                    }),
                Action::make("change_{$provider}")->label('Đổi key')->color('gray')->icon('heroicon-o-key')
                    ->modalHeading("Nhập key {$label}")
                    ->schema([TextInput::make('key')->label('API key')->password()->required()->minLength(8)->maxLength(500)->autocomplete('off')])
                    ->action(function (array $data) use ($provider, $label) {
                        app(NewsSettings::class)->setSecret($provider, $data['key']);
                        Notification::make()->title("Đã lưu key {$label}")->success()->send();
                    }),
                Action::make("clear_{$provider}")->label('Xóa key')->color('danger')->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->visible(fn () => app(NewsSettings::class)->secretSource($provider) === 'db')
                    ->action(function () use ($provider, $label) {
                        app(NewsSettings::class)->clearSecret($provider);
                        Notification::make()->title("Đã xóa key {$label}")->success()->send();
                    }),
            ])->key("key-actions-{$provider}");
        }

        return Section::make('API keys')->icon('heroicon-o-key')->components($components);
    }

    private function statusHtml(): string
    {
        try {
            $quota = app(DailyQuota::class);
            $posts = (int) app(NewsSettings::class)->get('posts_per_day');
            $done = $quota->createdToday();
        } catch (Throwable) {
            return '';
        }

        return '<p class="text-sm">Hôm nay đã tạo <strong>'.e((string) $done).'/'.e((string) $posts).'</strong> bài tự động.</p>';
    }

    private function previewHtml(): string
    {
        if ($this->dryRunPreview === []) {
            return '';
        }

        $rows = collect($this->dryRunPreview)->map(fn (array $r) => '<li><strong>'.e($r['title']).'</strong> <span class="text-gray-500">('.e($r['source']).', điểm '.e((string) $r['score']).')</span><br><a class="text-primary-600 underline" href="'.e($r['url']).'" target="_blank" rel="noopener nofollow">'.e($r['url']).'</a></li>')->implode('');

        return '<div class="text-sm"><p class="font-semibold mb-2">Kết quả chạy thử (chưa ghi gì vào hệ thống):</p><ol class="list-decimal ps-5 space-y-2">'.$rows.'</ol></div>';
    }
}

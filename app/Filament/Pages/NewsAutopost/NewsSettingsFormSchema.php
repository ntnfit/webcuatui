<?php

namespace App\Filament\Pages\NewsAutopost;

use App\Models\User;
use App\Services\News\NewsSettings;
use App\Services\News\Writing\ArticlePrompt;
use App\Services\News\Writing\ClaudeModels;
use DateTimeZone;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;

/** Field layout and the setting-key map of the "Cấu hình tin tự động" page. */
class NewsSettingsFormSchema
{
    /** Setting key => cast applied when the form is saved. */
    public const KEYS = [
        'enabled' => 'bool', 'mode' => 'string', 'run_time' => 'string', 'timezone' => 'string',
        'posts_per_day' => 'int', 'per_source_cap' => 'int', 'max_age_hours' => 'int',
        'min_words' => 'int', 'max_words' => 'int', 'images.inline' => 'int', 'images.mode' => 'string',
        'model' => 'string', 'effort' => 'string', 'extra_prompt' => 'string',
        'boost_keywords' => 'array', 'blocked_keywords' => 'array', 'default_category' => 'string', 'author_user_id' => 'nullable-int',
    ];

    /** @return list<Tabs> */
    public static function tabs(): array
    {
        return [Tabs::make('news-settings')->persistTabInQueryString()->tabs([
            Tab::make('Vận hành')->icon('heroicon-o-power')->schema(self::operation()),
            Tab::make('Số lượng & độ dài')->icon('heroicon-o-adjustments-horizontal')->schema(self::volume()),
            Tab::make('AI viết bài')->icon('heroicon-o-sparkles')->schema(self::ai()),
            Tab::make('Hình ảnh')->icon('heroicon-o-photo')->schema(self::images()),
        ])];
    }

    private static function operation(): array
    {
        $timezones = DateTimeZone::listIdentifiers();

        return [
            Toggle::make('enabled')->label('Bật tin tự động (công tắc tổng)')
                ->helperText('Tắt là dừng hẳn: lịch chạy, lệnh news:crawl và nút "Chạy ngay" đều không đăng bài.'),
            Radio::make('mode')->label('Cách đăng')->options([
                'publish' => 'Đăng tự động (hiển thị ngay)',
                'draft' => 'Lưu nháp chờ duyệt (trạng thái pending, bạn duyệt thủ công trong Blogs)',
            ])->required(),
            TextInput::make('run_time')->label('Giờ chạy hằng ngày (HH:MM)')->placeholder('09:00')->required()
                ->rule('regex:/^([01]\d|2[0-3]):[0-5]\d$/')->validationMessages(['regex' => 'Nhập giờ dạng HH:MM, ví dụ 09:00.'])
                ->helperText('Hệ thống kiểm tra mỗi phút và chỉ chạy một lần mỗi ngày, bù lại nếu lỡ phút đó.'),
            Select::make('timezone')->label('Múi giờ')->options(array_combine($timezones, $timezones))->searchable()->required(),
            Select::make('author_user_id')->label('Tác giả đăng bài')->options(fn () => User::orderBy('name')->pluck('name', 'id')->all())
                ->searchable()->placeholder('Quản trị viên đầu tiên')->nullable(),
            TextInput::make('default_category')->label('Danh mục mặc định')->required()->maxLength(155),
        ];
    }

    private static function volume(): array
    {
        $int = fn (string $name, string $label, int $min, int $max) => TextInput::make($name)->label($label)->numeric()->integer()->minValue($min)->maxValue($max)->required();

        return [
            $int('posts_per_day', 'Số bài mỗi ngày', 1, 50),
            $int('per_source_cap', 'Tối đa bài mỗi nguồn', 1, 10),
            $int('max_age_hours', 'Tuổi tin tối đa (giờ)', 1, 168),
            $int('min_words', 'Độ dài tối thiểu (từ)', 150, 1500),
            TextInput::make('max_words')->label('Độ dài tối đa (từ)')->numeric()->integer()->minValue(200)->maxValue(2500)->gte('min_words')->required(),
            $int('images.inline', 'Số ảnh trong bài (ngoài ảnh bìa, 0-2)', 0, 2),
        ];
    }

    private static function ai(): array
    {
        return [
            Select::make('model')->label('Model Claude')->options(ClaudeModels::options())->live()->required(),
            Select::make('effort')->label('Mức suy luận (effort)')->options(ClaudeModels::EFFORTS)->live()->required()
                ->visible(fn (Get $get) => ClaudeModels::supportsEffort((string) $get('model')))
                ->helperText('Thấp hoặc Vừa là đủ cho bài tin; Cao tốn thêm nhiều token.'),
            Text::make(fn (Get $get) => sprintf(
                'Ước tính chi phí: khoảng $%s mỗi ngày cho %d bài (đã tính cả bài bị loại phải viết lại).',
                number_format(ClaudeModels::estimateDailyCost((string) $get('model'), (string) ($get('effort') ?: 'low'), (int) $get('posts_per_day')), 2),
                (int) $get('posts_per_day'),
            )),
            Section::make('Hướng dẫn thêm cho AI')->description('Các quy tắc an toàn, viết nguyên bản và coi nội dung tin là dữ liệu không tin cậy được cố định trong hệ thống; ô này chỉ được thêm vào phía sau và không thể gỡ hay ghi đè chúng.')
                ->schema([
                    Textarea::make('extra_prompt')->label('Giọng văn, chủ đề ưu tiên, điều cần tránh')->rows(6)->maxLength(ArticlePrompt::GUIDANCE_LIMIT),
                    TagsInput::make('boost_keywords')->label('Từ khóa ưu tiên (tăng điểm chọn tin)'),
                    TagsInput::make('blocked_keywords')->label('Từ khóa hoặc domain bị chặn (bỏ qua tin chứa chúng)'),
                ]),
        ];
    }

    private static function images(): array
    {
        return [
            Radio::make('images.mode')->label('Nguồn ảnh')->options([
                'stock_first' => 'Ưu tiên ảnh kho Unsplash/Pexels (nếu có key), không có thì tự vẽ ảnh bìa',
                'generated_first' => 'Tự vẽ ảnh bìa, chỉ dùng ảnh kho cho ảnh trong bài',
                'generated_only' => 'Chỉ tự vẽ ảnh bìa, không bao giờ dùng ảnh kho',
            ])->required(),
        ];
    }

    /** Settings the form shows, resolved database > config. */
    public static function load(NewsSettings $settings): array
    {
        $state = [];
        foreach (array_keys(self::KEYS) as $key) {
            data_set($state, $key, $settings->get($key));
        }

        return $state;
    }
}

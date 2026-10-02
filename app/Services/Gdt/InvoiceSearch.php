<?php

namespace App\Services\Gdt;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

/** Validated lookup period plus the portal's RSQL-style `search` expression. */
final class InvoiceSearch
{
    /** The portal itself refuses wider windows. */
    public const MAX_DAYS = 30;

    public function __construct(
        public readonly string $direction,
        public readonly CarbonInterface $from,
        public readonly CarbonInterface $to,
        public readonly ?string $counterpartMst = null,
    ) {
        if (! in_array($direction, ['sold', 'purchase'], true)) {
            throw new InvalidArgumentException('Hướng hóa đơn không hợp lệ.');
        }

        $start = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();

        if ($start->gt($end)) {
            throw new InvalidArgumentException('Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc.');
        }

        if ($start->diffInDays($end) > self::MAX_DAYS) {
            throw new InvalidArgumentException('Khoảng thời gian tra cứu tối đa '.self::MAX_DAYS.' ngày.');
        }

        if ($counterpartMst !== null && $counterpartMst !== '' && ! preg_match('/^[0-9-]{10,14}$/', $counterpartMst)) {
            throw new InvalidArgumentException('Mã số thuế đối tác không hợp lệ.');
        }
    }

    /** @param array{direction: string, from: string, to: string, counterpart_mst?: ?string} $filters */
    public static function fromArray(array $filters): self
    {
        return new self(
            $filters['direction'],
            Carbon::parse($filters['from']),
            Carbon::parse($filters['to']),
            $filters['counterpart_mst'] ?? null,
        );
    }

    /** @return array{direction: string, from: string, to: string, counterpart_mst: ?string} */
    public function toArray(): array
    {
        return [
            'direction' => $this->direction,
            'from' => $this->from->toDateString(),
            'to' => $this->to->toDateString(),
            'counterpart_mst' => $this->counterpartMst ?: null,
        ];
    }

    public function expression(): string
    {
        $search = 'tdlap=ge='.$this->from->format('d/m/Y').'T00:00:00;tdlap=le='.$this->to->format('d/m/Y').'T23:59:59';

        if ($this->counterpartMst) {
            $search .= ';bmst=='.$this->counterpartMst;
        }

        return $search;
    }

    /** Human label used in export headers: "Từ ngày dd/mm/yyyy đến ngày dd/mm/yyyy". */
    public function label(): string
    {
        return 'Từ ngày '.$this->from->format('d/m/Y').' đến ngày '.$this->to->format('d/m/Y');
    }
}

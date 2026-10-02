<?php

use App\Models\Company;
use App\Models\GdtSession;
use App\Models\TaxInvoice;
use App\Services\Gdt\GdtClient;
use App\Services\Gdt\GdtException;
use App\Services\Gdt\InvoiceSearch;
use App\Services\Gdt\InvoiceSyncService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    GdtClient::$initialDelayMs = 0;
    $this->company = Company::factory()->licensed()->create();
    GdtSession::withoutGlobalScopes()->create([
        'company_id' => $this->company->id, 'token' => 'tok', 'expires_at' => now()->addHour(),
    ]);
});

function gdtRow(int $n, array $extra = []): array
{
    return array_merge([
        'nbmst' => '0300000001', 'khhdon' => 'C26TAA', 'shdon' => (string) $n, 'khmshdon' => 1,
        'nbten' => 'Nguoi ban', 'nmmst' => '0100000001', 'nmten' => 'Nguoi mua',
        'tdlap' => '2026-09-10T00:00:00', 'tgtcthue' => 1000, 'tgtthue' => 100, 'tgtttbso' => 1100,
        'tthai' => 1, 'ttxly' => 5,
    ], $extra);
}

function searchFor(string $direction = 'purchase'): InvoiceSearch
{
    return new InvoiceSearch($direction, Carbon::parse('2026-09-01'), Carbon::parse('2026-09-30'));
}

it('follows the state cursor across full pages of 50', function () {
    $first = array_map(fn ($i) => gdtRow($i), range(1, 50));
    $second = [gdtRow(51), gdtRow(52)];

    Http::fake(function (Request $request) use ($first, $second) {
        if (str_contains($request->url(), '/api/sco-query/')) {
            return Http::response(['datas' => []]);
        }
        parse_str(parse_url($request->url(), PHP_URL_QUERY), $q);

        return isset($q['state'])
            ? Http::response(['datas' => $second])
            : Http::response(['datas' => $first, 'state' => 'cursor-2']);
    });

    $result = app(InvoiceSyncService::class)->sync($this->company, searchFor());

    expect($result['count'])->toBe(52)
        ->and(TaxInvoice::withoutGlobalScopes()->where('company_id', $this->company->id)->count())->toBe(52);
    Http::assertSent(fn (Request $r) => str_contains($r->url(), 'size=50') && str_contains($r->url(), 'state=cursor-2'));
});

it('sends the date window as a portal search expression', function () {
    Http::fake(['*' => Http::response(['datas' => []])]);

    app(InvoiceSyncService::class)->sync($this->company, searchFor('sold'));

    Http::assertSent(fn (Request $r) => str_contains($r->url(), '/api/query/invoices/sold')
        && str_contains(urldecode($r->url()), 'tdlap=ge=01/09/2026T00:00:00;tdlap=le=30/09/2026T23:59:59'));
});

it('merges standard and mtt sources, deduplicating with standard first', function () {
    Http::fake([
        '*/api/query/invoices/purchase*' => Http::response(['datas' => [gdtRow(1), gdtRow(2)]]),
        '*/api/sco-query/invoices/purchase*' => Http::response(['datas' => [gdtRow(2), gdtRow(3)]]),
    ]);

    $result = app(InvoiceSyncService::class)->sync($this->company, searchFor());

    $bySource = TaxInvoice::withoutGlobalScopes()->orderBy('number')->pluck('source', 'number')->all();
    expect($result['count'])->toBe(3)
        ->and($bySource)->toBe(['1' => 'standard', '2' => 'standard', '3' => 'mtt']);
});

it('updates an existing invoice on re-sync and keeps its cached detail', function () {
    $payment = 1100;
    Http::fake(function () use (&$payment) {
        return Http::response(['datas' => [gdtRow(1, ['tgtttbso' => $payment])]]);
    });
    $service = app(InvoiceSyncService::class);
    $service->sync($this->company, searchFor());
    TaxInvoice::withoutGlobalScopes()->first()->update(['detail' => ['hdhhdvu' => [['ten' => 'A']]]]);

    $payment = 5500;
    $service->sync($this->company, searchFor());

    $invoice = TaxInvoice::withoutGlobalScopes()->sole();
    expect((float) $invoice->total_payment)->toBe(5500.0)
        ->and($invoice->detail['hdhhdvu'][0]['ten'])->toBe('A');
});

it('treats a failing mtt source as a warning', function () {
    Http::fake([
        '*/api/query/*' => Http::response(['datas' => [gdtRow(1)]]),
        '*/api/sco-query/*' => Http::response([], 500),
    ]);

    $result = app(InvoiceSyncService::class)->sync($this->company, searchFor());

    expect($result['count'])->toBe(1)->and($result['warnings'])->toHaveCount(1);
});

it('fails when the standard source errors', function () {
    Http::fake(['*' => Http::response([], 500)]);

    expect(fn () => app(InvoiceSyncService::class)->sync($this->company, searchFor()))
        ->toThrow(GdtException::class);
});

it('rejects periods over 30 days, reversed ranges and bad directions', function () {
    expect(fn () => new InvoiceSearch('sold', Carbon::parse('2026-01-01'), Carbon::parse('2026-03-01')))
        ->toThrow(InvalidArgumentException::class);
    expect(fn () => new InvoiceSearch('sold', Carbon::parse('2026-03-01'), Carbon::parse('2026-01-01')))
        ->toThrow(InvalidArgumentException::class);
    expect(fn () => new InvoiceSearch('other', Carbon::parse('2026-01-01'), Carbon::parse('2026-01-02')))
        ->toThrow(InvalidArgumentException::class);
    expect(new InvoiceSearch('sold', Carbon::parse('2026-01-01'), Carbon::parse('2026-01-31')))->toBeInstanceOf(InvoiceSearch::class);
});

it('caches invoice detail once', function () {
    Http::fake(['*/invoices/detail*' => Http::response(['hdhhdvu' => [['ten' => 'Hang']]])]);
    $invoice = TaxInvoice::factory()->create(['company_id' => $this->company->id]);
    $service = app(InvoiceSyncService::class);

    $service->loadDetail($invoice, new GdtClient('tok'));
    $service->loadDetail($invoice->fresh(), new GdtClient('tok'));

    expect($invoice->fresh()->detail['hdhhdvu'][0]['ten'])->toBe('Hang');
    Http::assertSentCount(1);
});

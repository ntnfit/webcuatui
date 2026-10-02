<?php

use App\Models\Company;
use App\Models\GdtSession;
use App\Services\Gdt\GdtClient;
use App\Services\Gdt\GdtException;
use App\Services\Gdt\GdtSessionService;
use App\Services\Gdt\GdtUnauthorizedException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    GdtClient::$initialDelayMs = 0;
});

function fakeJwt(int $exp): string
{
    $b64 = fn (array $d) => rtrim(strtr(base64_encode(json_encode($d)), '+/', '-_'), '=');

    return $b64(['alg' => 'HS512']).'.'.$b64(['exp' => $exp]).'.sig';
}

it('fetches the captcha with browser headers instead of the default client agent', function () {
    Http::fake(['hoadondientu.gdt.gov.vn/api/captcha' => Http::response(['key' => 'k1', 'content' => '<svg/>'])]);

    $captcha = app(GdtSessionService::class)->captcha();

    expect($captcha['key'])->toBe('k1');
    Http::assertSent(function (Request $request) {
        return str_contains($request->header('User-Agent')[0], 'Chrome/')
            && $request->header('Origin')[0] === 'https://hoadondientu.gdt.gov.vn'
            && $request->header('request-id')[0] !== '';
    });
});

it('stores only an encrypted token and expiry, never the credentials', function () {
    $exp = now()->addHour()->getTimestamp();
    $token = fakeJwt($exp);
    Http::fake(['hoadondientu.gdt.gov.vn/api/security-taxpayer/authenticate' => Http::response(['token' => $token])]);
    $company = Company::factory()->create();

    $session = app(GdtSessionService::class)->connect($company, '0100000001', 'S3cretPw!', 'abcd', 'k1');

    expect($session->expires_at->getTimestamp())->toBe($exp);

    $stored = DB::table('gdt_sessions')->first();
    expect($stored->token)->not->toBe($token)
        ->and(GdtSession::withoutGlobalScopes()->first()->token)->toBe($token);

    $dump = json_encode([DB::table('gdt_sessions')->get(), DB::table('companies')->get()]);
    expect($dump)->not->toContain('S3cretPw!')->not->toContain('0100000001"');
    expect($session->toArray())->not->toHaveKey('token');
});

it('replaces the previous token when a company reconnects', function () {
    Http::fake(['*' => Http::response(['token' => fakeJwt(now()->addHour()->getTimestamp())])]);
    $company = Company::factory()->create();
    $service = app(GdtSessionService::class);

    $service->connect($company, 'u', 'p', 'c', 'k');
    $service->connect($company, 'u', 'p', 'c', 'k');

    expect(GdtSession::withoutGlobalScopes()->count())->toBe(1);
});

it('surfaces a portal login failure without storing a session', function () {
    Http::fake(['*' => Http::response(['message' => 'Sai captcha'], 400)]);
    $company = Company::factory()->create();

    expect(fn () => app(GdtSessionService::class)->connect($company, 'u', 'p', 'bad', 'k'))
        ->toThrow(GdtException::class, 'Sai captcha');
    expect(GdtSession::withoutGlobalScopes()->count())->toBe(0);
});

it('refuses to build a client for a company without an active session', function () {
    $company = Company::factory()->licensed()->create();
    GdtSession::withoutGlobalScopes()->create([
        'company_id' => $company->id, 'token' => 'old', 'expires_at' => now()->subMinute(),
    ]);

    expect(fn () => app(GdtSessionService::class)->clientFor($company))
        ->toThrow(GdtUnauthorizedException::class);
});

it('drops the stored token when the portal answers 401', function () {
    Http::fake(['*' => Http::response([], 401)]);
    $company = Company::factory()->licensed()->create();
    GdtSession::withoutGlobalScopes()->create([
        'company_id' => $company->id, 'token' => 'tok', 'expires_at' => now()->addHour(),
    ]);

    expect(fn () => app(GdtSessionService::class)->run($company, fn (GdtClient $c) => $c->profile()))
        ->toThrow(GdtUnauthorizedException::class);
    expect(GdtSession::withoutGlobalScopes()->count())->toBe(0);
});

it('retries throttled requests with backoff and then succeeds', function () {
    Http::fakeSequence('hoadondientu.gdt.gov.vn/api/query/invoices/detail*')
        ->push([], 429)->push([], 429)->push(['shdon' => '7']);

    $detail = (new GdtClient('tok'))->invoiceDetail('query', '0100000001', 'C26TAA', '7', '1');

    expect($detail['shdon'])->toBe('7');
    Http::assertSentCount(3);
});

it('sends the bearer token and detail identifiers', function () {
    Http::fake(['*' => Http::response(['ok' => true])]);

    (new GdtClient('tok'))->invoiceDetail('sco-query', '0100000001', 'C26TAA', '7', '1');

    Http::assertSent(fn (Request $r) => $r->hasHeader('Authorization', 'Bearer tok')
        && str_contains($r->url(), '/api/sco-query/invoices/detail')
        && str_contains($r->url(), 'nbmst=0100000001'));
});

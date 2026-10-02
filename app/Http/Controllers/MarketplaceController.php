<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function index(Request $request): View
    {
        // Unknown filter values are ignored (shown as "all") rather than producing an empty page.
        $integration = array_key_exists((string) $request->query('integration'), Product::INTEGRATIONS)
            ? (string) $request->query('integration')
            : null;
        $sapVersion = in_array($request->query('sap_version'), Product::SAP_VERSIONS, true)
            ? $request->query('sap_version')
            : null;

        $addons = Product::active()
            ->addons()
            ->forIntegration($integration)
            ->forSapVersion($sapVersion)
            ->orderBy('name')
            ->get();

        return view('marketplace.index', [
            'addons' => $addons,
            'integration' => $integration,
            'sapVersion' => $sapVersion,
            'integrations' => Product::INTEGRATIONS,
            'sapVersions' => Product::SAP_VERSIONS,
        ]);
    }

    public function show(string $slug): View
    {
        $addon = Product::active()->addons()->where('slug', $slug)->firstOrFail();

        $related = Product::active()
            ->addons()
            ->where('id', '!=', $addon->id)
            ->orderBy('name')
            ->take(3)
            ->get();

        return view('marketplace.show', [
            'addon' => $addon,
            'related' => $related,
        ]);
    }

    /** Quote request for one addon. Stored as a contact so it shows up in the existing admin inbox. */
    public function quote(Request $request, string $slug, ContactController $contacts): RedirectResponse
    {
        $addon = Product::active()->addons()->where('slug', $slug)->firstOrFail();
        $backToForm = route('marketplace.show', $addon->slug).'#quote';

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:255'],
            'sap_version' => ['nullable', Rule::in(Product::SAP_VERSIONS)],
            'db' => ['nullable', Rule::in(array_keys(Product::DB_SUPPORT))],
            'message' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'name' => 'họ và tên',
            'email' => 'email',
            'phone' => 'số điện thoại',
            'company' => 'công ty',
            'sap_version' => 'phiên bản SAP B1',
            'db' => 'cơ sở dữ liệu',
            'message' => 'nội dung',
        ]);

        if ($validator->fails()) {
            return redirect($backToForm)->withErrors($validator)->withInput();
        }

        // Honeypot: real visitors never see or fill this field. Pretend success so bots learn nothing.
        if (filled($request->input('website'))) {
            return redirect($backToForm)->with('quote_sent', true);
        }

        $data = $validator->validated();

        $lines = ['Yêu cầu báo giá addon: '.$addon->name];
        if (! empty($data['sap_version'])) {
            $lines[] = 'Phiên bản SAP B1: '.$data['sap_version'];
        }
        if (! empty($data['db'])) {
            $lines[] = 'Cơ sở dữ liệu: '.Product::DB_SUPPORT[$data['db']];
        }
        if (! empty($data['message'])) {
            $lines[] = '';
            $lines[] = $data['message'];
        }

        $contacts->recordInquiry([
            'full_name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone'] ?? null,
            'company_name' => $data['company'] ?? null,
            'topic' => $addon->slug,
            'message' => implode("\n", $lines),
        ], null, 'Yêu cầu báo giá addon');

        return redirect($backToForm)->with('quote_sent', true);
    }
}

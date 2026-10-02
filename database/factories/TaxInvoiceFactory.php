<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\TaxInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaxInvoice>
 */
class TaxInvoiceFactory extends Factory
{
    protected $model = TaxInvoice::class;

    public function definition(): array
    {
        $number = (string) fake()->unique()->numberBetween(1, 99999999);
        $symbol = 'C26TAA';
        $sellerMst = fake()->numerify('##########');
        $before = fake()->numberBetween(100000, 9000000);
        $tax = (int) round($before * 0.1);

        return [
            'company_id' => Company::factory(),
            'direction' => TaxInvoice::DIRECTION_PURCHASE,
            'source' => TaxInvoice::SOURCE_STANDARD,
            'mst_seller' => $sellerMst,
            'mst_buyer' => fake()->numerify('##########'),
            'seller_name' => fake()->company(),
            'buyer_name' => fake()->company(),
            'number' => $number,
            'symbol' => $symbol,
            'template' => '1',
            'issued_at' => fake()->dateTimeBetween('-20 days', 'now'),
            'total_before_tax' => $before,
            'total_tax' => $tax,
            'total_payment' => $before + $tax,
            'currency' => 'VND',
            'status' => 1,
            'check_status' => 5,
            'raw' => [
                'nbmst' => $sellerMst, 'khhdon' => $symbol, 'shdon' => $number, 'khmshdon' => 1,
                'tgtcthue' => $before, 'tgtthue' => $tax, 'tgtttbso' => $before + $tax,
            ],
        ];
    }
}

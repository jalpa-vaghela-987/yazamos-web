<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\PlanDuration;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currency = Currency::query()->where('code', config('services.tranzila.currency'))->first();

        if ( !blank($currency) ) {
            Plan::query()->updateOrCreate(['id' => 1], [
                'currency_id' => $currency->id,
                'title' => 'Per user plan',
                'duration' => PlanDuration::MONTHLY,
                'amount' => 5,
                'notes' => "<p>5 {$currency->code} monthly subscription with access per user.</p>",
            ]);
        }
    }
}

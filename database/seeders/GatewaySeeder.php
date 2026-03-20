<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gateway;

class GatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'name'         => 'razorpay',
                'display_name' => 'Razorpay',
                'type'         => 'payment',
                'mode'         => 'test',
                'test_key'     => env('RAZORPAY_KEY_ID'),
                'test_secret'  => env('RAZORPAY_KEY_SECRET'),
                'live_key'     => null,
                'live_secret'  => null,
                'active'       => true,
            ],
            [
                'name'         => 'stripe',
                'display_name' => 'Stripe',
                'type'         => 'payment',
                'mode'         => 'test',
                'test_key'     => env('STRIPE_PUBLISHABLE_KEY'),
                'test_secret'  => env('STRIPE_SECRET'),
                'live_key'     => null,
                'live_secret'  => null,
                'active'       => true,
            ],
        ];

        foreach ($gateways as $data) {
            Gateway::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}

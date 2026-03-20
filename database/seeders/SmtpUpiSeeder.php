<?php

namespace Database\Seeders;

use App\Models\Gateway;
use Illuminate\Database\Seeder;

class SmtpUpiSeeder extends Seeder
{
    public function run(): void
    {
        Gateway::updateOrCreate(
            ['name' => 'smtp'],
            [
                'display_name' => 'SMTP Email',
                'type'         => 'email',
                'mode'         => 'live',
                'config'       => json_encode([
                    'host'       => 'smtp.gmail.com',
                    'port'       => 587,
                    'username'   => 'j83367806@gmail.com',
                    'password'   => 'zidq gkgg snub hztg',
                    'from_email' => 'j83367806@gmail.com',
                    'from_name'  => 'Amit Dairy & Sweets',
                    'encryption' => 'tls',
                ]),
                'active'       => true,
            ]
        );

        Gateway::updateOrCreate(
            ['name' => 'upi'],
            [
                'display_name' => 'UPI Payment',
                'type'         => 'payment',
                'mode'         => 'live',
                'config'       => json_encode([
                    'upi_id'        => 'amitdairy@okicici',
                    'merchant_name' => 'Amit Dairy & Sweets',
                ]),
                'active'       => true,
            ]
        );
    }
}

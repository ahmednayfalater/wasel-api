<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $adminUser = User::create([
            'first_name' => 'مدير',
            'last_name'  => 'النظام',
            'email'      => 'admin@wasel.com',
            'phone'      => '0599000001',
            'role'       => 'admin',
            'password'   => 'admin1234',
        ]);
        Admin::create(['user_id' => $adminUser->id]);

        // Provider
        $providerUser = User::create([
            'first_name' => 'أحمد',
            'last_name'  => 'المزود',
            'email'      => 'provider@wasel.com',
            'phone'      => '0599000002',
            'role'       => 'provider',
            'password'   => 'provider1234',
        ]);
        Provider::create([
            'user_id'      => $providerUser->id,
            'company_name' => 'شركة الكهرباء الأهلية',
            'price_KW'     => 2.5,
            'terms_subscr' => 'الدفع شهري، ويتم قطع الخدمة عند التأخر أكثر من أسبوع.',
            'status'       => 'active',
        ]);

        // Customer
        User::create([
            'first_name' => 'محمد',
            'last_name'  => 'الزبون',
            'email'      => 'customer@wasel.com',
            'phone'      => '0599000003',
            'role'       => 'customer',
            'password'   => 'customer1234',
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Merchant;
use App\Models\Affiliate;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create();
        $merchant = Merchant::factory()->for($user)->create();
        $affiliate = Affiliate::factory()->for($user)->for($merchant)->create();
        Order::factory()->for($merchant)->for($affiliate)->create();
    }
}

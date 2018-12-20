<?php

use Illuminate\Database\Seeder;

class CardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cards = factory(\App\Models\Cards::class, 1000)->make();
        \App\Models\Cards::insert($cards->toArray());
    }
}

<?php

use Illuminate\Database\Seeder;

class CasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cases = factory(\App\Models\Cases::class, 1000)->make();
        \App\Models\Cases::insert($cases->toArray());
    }
}

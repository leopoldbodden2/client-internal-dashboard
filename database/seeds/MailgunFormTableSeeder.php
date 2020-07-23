<?php

use Illuminate\Database\Seeder;

class MailgunFormTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\MailgunForms::class, 50)->create();
    }
}

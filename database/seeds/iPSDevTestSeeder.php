<?php

use Illuminate\Database\Seeder;
use App\Module;

class iPSDevTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        for ($i = 1; $i <= 7; $i++){
            Module::insert([
                [
                    'course_key' => 'ipa',
                    'name' => 'IPA Module ' . $i
                ],

                [
                    'course_key' => 'iea',
                    'name' => 'IEA Module ' . $i
                ],

                [
                    'course_key' => 'iaa',
                    'name' => 'IAA Module ' . $i
                ]
            ]);
        }

        $faker = Faker\Factory::create();
        \App\User::create(
            [
                'id' => 1,
                'name' => $faker->name,
                'email' => '5d0b38a81c7ae@test.com',
                'password' => bcrypt($faker->password),
            ]
        );
    }
}

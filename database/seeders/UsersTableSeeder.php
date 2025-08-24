<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Sample user data
        $users = [
            [
                'name' => 'Admin User',
                'image' => null,
                'email' => 'admin@computinggate.com',
                'phone' => '1234567890',
                'status' => 1,
                'type' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // password
                'current_team_id' => null,
                'profile_photo_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'country_id' => 2,
                'city_id' => 369,
                'remember_token' => Str::random(10),
            ],
            [
                'name' => "mohamed ali",
                'image' => null,
                'email' => "mohamed@gmail.com",
                'phone' => $faker->unique()->numerify('+966#########'),
                'status' => 1,
                'type' => "user",
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'current_team_id' => null,
                'profile_photo_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'country_id' => 2, // Assuming country ID 1 exists
                'city_id' => 369, // Assuming city ID 1 exists
                'remember_token' => Str::random(10),
            ],
        ];

        // Insert the user data
        DB::table('users')->insert($users);

        // Create more users using the faker

        // for ($i = 0; $i < 10; $i++) {
        //     DB::table('users')->insert([
        //         'name' => $faker->name,
        //         'image' => null,
        //         'email' => $faker->unique()->safeEmail,
        //         'phone' => $faker->unique()->numerify('+966#########'),
        //         'identity_id' => Str::random(10),
        //         'status' => 1,
        //         'age' => $faker->numberBetween(18, 70),
        //         'type' => $faker->randomElement(['user', 'supervisor', 'driver']),
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password'), // password
        //         'remember_token' => Str::random(10),
        //         'current_team_id' => null,
        //         'profile_photo_path' => null,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //         'name_ar' => $faker->name,
        //         'name_en' => $faker->name,
        //         'gender' => $faker->randomElement(['male', 'female']),
        //         'height_cm' => $faker->numberBetween(150, 200),
        //         'weight_kg' => $faker->numberBetween(50, 120),
        //         'country_id' => 2, // Assuming country ID 1 exists
        //         'city_id' => 369, // Assuming city ID 1 exists
        //         'matlop_number' => $faker->numerify('#########'),
        //         'role' => $faker->jobTitle,
        //         'suffers_disability' => $faker->boolean,
        //         'suffers_chronic_disease' => $faker->boolean,
        //         'education_level' => $faker->randomElement(['high_school', 'bachelor', 'master']),
        //         'language' => json_encode([$faker->languageCode => $faker->randomElement(['Basic', 'Intermediate', 'Fluent', 'Native'])]),
        //         'certificate_path' => null,
        //         'academic_qualification_path' => null,
        //         'cv_path' => null,
        //         'other_documents_path' => null,
        //         'bank_name' => $faker->randomElement(['Al Rajhi Bank', 'Saudi National Bank', 'Riyad Bank']),
        //         'iban' => $faker->iban('SA'),
        //         'card_holder_name' => $faker->name,
        //         'stc_pay_number' => null,
        //         'information_accurate' => $faker->boolean,
        //     ]);
        // }
    }
}

<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
            [
                "id" => 1,
                'name_ar' => 'الإمارات العربية المتحدة',
                'name_en' => 'United Arab Emirates',
                'image' => 'https://master.automark.site/upload/flags/Flag-of-United-Arab-Emirates.png',
                'latitude' => 25.276987,
                'longitude' => 55.296249,
                'code' => '+971',
                'symbol_ar' => 'درهم',
                'symbol_en' => 'AED',
                'exchange_rate' => 1,
                'country_tax' => 1,
                "created_at" => now()->subDays(30),
            ],
            [
                "id" => 2,
                'name_ar' => 'المملكة العربية السعودية',
                'name_en' => 'Saudi Arabia',
                'image' => 'https://master.automark.site/upload/flags/ksa.png',
                'latitude' => 24.774265,
                'longitude' => 46.738586,
                'code' => '+966',
                'symbol_ar' => 'ريال',
                'symbol_en' => 'SAR',
                'exchange_rate' => 1.02,
                'country_tax' => 1,
                "created_at" => now()->subDays(29),
            ],
            [
                "id" => 3,
                'name_ar' => 'سلطنة عمان',
                'name_en' => 'Oman',
                'image' => 'https://master.automark.site/upload/flags/oman.png',
                'latitude' => 23.614328,
                'longitude' => 58.545284,
                'code' => '+968',
                'symbol_ar' => 'ريال عماني',
                'symbol_en' => 'OMR',
                'exchange_rate' => 9.45,
                'country_tax' => 1,
                "created_at" => now()->subDays(28),

            ],
            [
                "id" => 4,
                'name_ar' => 'دولة الكويت',
                'name_en' => 'Kuwait',
                'image' => 'https://master.automark.site/upload/flags/kw.png',
                'latitude' => 29.378586,
                'longitude' => 47.990341,
                'code' => '+965',
                'symbol_ar' => 'دينار كويتي',
                'symbol_en' => 'KWD',
                'exchange_rate' => 0.084,
                'country_tax' => 1,
                "created_at" => now()->subDays(27),

            ],
            [
                "id" => 9,
                'name_ar' => 'دولة قطر',
                'name_en' => 'Qatar',
                'image' => 'https://app.automark.site/upload/flags/Flag_of_Qatar.svg.png',
                'latitude' => 25.286106,
                'longitude' => 51.534817,
                'code' => '+974',
                'symbol_ar' => 'ريال قطري',
                'symbol_en' => 'QA',
                'exchange_rate' => 1,
                'country_tax' => 1,
                "created_at" => now()->subDays(26),

            ],
            [
                "id" => 7,
                'name_ar' => 'جمهورية العراق',
                'name_en' => 'Iraq',
                'image' => 'https://app.automark.site/upload/flags/Flag_of_Iraq.svg.png',
                'latitude' => 33.312805,
                'longitude' => 44.361488,
                'code' => '+964',
                'symbol_ar' => 'دينار عراقي',
                'symbol_en' => 'IQD',
                'exchange_rate' => 356.02,
                'country_tax' => 1,
                "created_at" => now()->subDays(25),

            ],
            [
                "id" => 6,
                'name_ar' => 'مملكة البحرين',
                'name_en' => 'Bahrain',
                'image' => 'https://master.automark.site/upload/flags/bahreen.png',
                'latitude' => 26.201,
                'longitude' => 50.606998,
                'code' => '+973',
                'symbol_ar' => 'دينار بحريني',
                'symbol_en' => 'BHD',
                'exchange_rate' => 0.1,
                'country_tax' => 1,
                "created_at" => now()->subDays(24),

            ],
            [
                "id" => 8,
                'name_ar' => 'المملكة الأردنية الهاشمية',
                'name_en' => 'Jordan',
                'image' => 'https://app.automark.site/upload/flags/Flag_of_Jordan.svg.png',
                'latitude' => 31.963158,
                'longitude' => 35.930359,
                'code' => '+962',
                'symbol_ar' => 'دينار اردني',
                'symbol_en' => 'JOD',
                'exchange_rate' => 0.19,
                'country_tax' => 1,
                "created_at" => now()->subDays(23),

            ],
            [
                "id" => 5,
                'name_ar' => 'جمهورية مصر العربية',
                'name_en' => 'Egypt',
                'image' => 'https://master.automark.site/upload/flags/egypt.jpg',
                'latitude' => 30.033333,
                'longitude' => 31.233334,
                'code' => '+20',
                'symbol_ar' => 'جنية',
                'symbol_en' => 'EGP',
                'exchange_rate' => 8.4,
                'country_tax' => 1,
                "created_at" => now()->subDays(22),

            ],
        ];

        // Insert data into the countries_admins table
        foreach ($countries as $country) {
            DB::table('countries')->insert($country);
        }
    }
}

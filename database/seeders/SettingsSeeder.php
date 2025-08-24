<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
            [
                'name' => 'Computing Gate',
                'version' => '1.0.0',
                'is_production' => true,
                'email' => 'computinggate@gmail.com',
                'phone' => '+9660561788801',
                'phone_two' => '+9660561788802',
                'whatsapp' => '+9660561788801',
                'snapchat' => 'computinggate',
                'address' => 'Saudi Arabia, Jeddah',
                'twitter' => 'https://x.com/almahbahajj?s=21&t=Bk0r1rui_bdAImKThiUB0w',
                'facebook' => '',
                'instagram' => '',
                'linkedin' => '',
                'tiktok' => '',
                'google_play' => 'https://play.google.com/store/apps/details?id=com.myapplication',
                'app_store' => 'https://apps.apple.com/app/id1234567890',
                'youtube' => 'https://www.youtube.com/channel/UC1234567890',
                'website' => 'https://www.myapplication.com',
                'info' => 'Welcome to My Application! We provide excellent services.',
                'logo' => asset('logo.png'),
                'background_image' => 'path/to/background.jpg',
                'user_id' => 1, // Ensure this user ID exists in the users table
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

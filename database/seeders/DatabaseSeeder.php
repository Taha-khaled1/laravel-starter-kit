<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $this->call([
      CountryTableSeeder::class,
      CitySeeder::class,
      UsersTableSeeder::class,
      ContactUsSeeder::class,
      SettingsSeeder::class,
      // EventsTableSeeder::class,
    ]);
  }
}

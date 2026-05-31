<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding database...');
        $this->command->newLine();

        // Seed users
        $this->command->info('👥 Creating users...');

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->command->info('✅ Users created');
        $this->command->newLine();

        // Ask if user wants to seed products
        if ($this->command->confirm('Do you want to seed 10 MILLION products? (This will take hours/days!)', false)) {
            $this->call(ProductSeeder::class);
        } else {
            $this->command->info('⏭️  Skipping product seeding');
            $this->command->info('💡 Run "php artisan db:seed --class=ProductSeeder" to seed products later');
        }

        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed!');
    }
}

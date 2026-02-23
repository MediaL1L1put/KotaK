<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SkateModel;
use App\Models\Skate;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Создание администратора
        $this->createAdminUser();
        
        // Создание моделей коньков
        $this->createSkateModels();
    }

    private function createAdminUser(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();
        
        if (!$admin) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('✓ Администратор создан: admin@admin.com / admin123');
        } else {
            $this->command->info('✓ Администратор уже существует');
        }

        // Создаем тестового пользователя (не админа)
        $testUser = User::where('email', 'user@example.com')->first();
        
        if (!$testUser) {
            User::create([
                'name' => 'Test User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]);
        }
    }

    private function createSkateModels(): void
    {
        $models = [
            [
                'name' => 'Bauer Vapor X3.7',
                'brand' => 'Bauer',
                'description' => 'Профессиональные хоккейные коньки с термоформируемым ботинком',
            ],
            [
                'name' => 'CCM Jetspeed FT485',
                'brand' => 'CCM',
                'description' => 'Легкие и маневренные коньки для активного катания',
            ],
            [
                'name' => 'Risport RF3 Pro',
                'brand' => 'Risport',
                'description' => 'Для фигурного катания. Высокий уровень комфорта',
            ],
            [
                'name' => 'Graf Super 705',
                'brand' => 'Graf',
                'description' => 'Универсальные коньки для любительского катания',
            ],
        ];

        foreach ($models as $modelData) {
            $model = SkateModel::firstOrCreate(
                ['name' => $modelData['name']],
                $modelData
            );

            for ($size = 36; $size <= 45; $size++) {
                Skate::firstOrCreate(
                    [
                        'skate_model_id' => $model->id,
                        'size' => $size,
                    ],
                    [
                        'quantity' => rand(3, 8),
                        'available_quantity' => rand(2, 5),
                        'price_per_hour' => 150,
                    ]
                );
            }
        }

        $this->command->info('✓ Создано ' . count($models) . ' моделей коньков');
    }
}
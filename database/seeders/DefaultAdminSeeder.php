<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'user_type' => UserTypeEnum::SYS_ADMIN,
            'password' => Hash::make('D3f4ult01'),
            'email_verified_at' => now(),
        ]);

        Admin::create(['user_id' => $user->id]);
        $user->assignRole(UserTypeEnum::SYS_ADMIN->value)->save();
    }
}

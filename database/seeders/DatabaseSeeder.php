<?php

namespace Database\Seeders;


use App\Models\Account;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createUser();
    }


    private function createUser()
    {
        $sql = [
            'password' => Hash::make(env('DEFAULT_PASSWORD')),
            'is_valid' => true,
            'is_super' => true,
        ];
        if ($user = User::create($sql)) {
            Account::create([
                'account' => 'manager',
                'type' => Account::ACCOUNT,
                'user_id' => $user->id
            ]);
        }
    }

}
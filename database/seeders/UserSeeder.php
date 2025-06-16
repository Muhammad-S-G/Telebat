<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create();
        $user->assignRole('vendor');
        $token = $user->createToken('factory');
        $plainTextToken = $token->plainTextToken;
        $accessTokenId = $token->accessToken->id;
        $user->fcm_tokens()->create([
            'fcm_token' => 'test-fcm-token123',
            'token_id' => $accessTokenId,
            'platform' => 'Android'
        ]);
        $user->phone()->create([
            'phone_number' => '+963998495255',
            'phone_number_verified_at' => now()->addMinutes(53),
        ]);

        $this->command->info("🔑 Plain‑text token: {$plainTextToken}");
    }
}

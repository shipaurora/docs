<?php

namespace Database\Seeders\Demo;

use Aurora\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * The demo's one account, the Super Admin who wrote the docs, with the password "password".
     */
    public const USERS = [
        ['John Doe', 'john@example.com', 'Super Admin', 'Example user', 'en'],
    ];

    /**
     * The accounts as the sign-in page's demo mode offers them (Aurora\Support\Demo).
     *
     * @return array<int, array{name: string, email: string, role: string}>
     */
    public static function accounts(): array
    {
        return array_map(fn (array $user): array => ['name' => $user[0], 'email' => $user[1], 'role' => $user[2]], self::USERS);
    }

    public function run(): void
    {
        foreach (self::USERS as [$name, $email, $role, $title, $locale]) {
            // Seeding again leaves an account alone: its password, passkeys, authenticator app
            // and role are its owner's.
            if (User::query()->where('email', $email)->exists()) {
                continue;
            }

            $user = User::query()->create([
                'email' => $email,
                'name' => $name,
                'password' => 'password',
                'job_title' => $title,
                'locale' => $locale,
                'is_active' => true,
            ]);

            $user->forceFill(['email_verified_at' => now()])->save();
            $user->syncRoles([$role]);
        }
    }
}

<?php

namespace Database\Seeders\Demo;

use Aurora\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class UserSeeder extends Seeder
{
    /**
     * The demo's one account, the Super Admin who wrote the docs, with the password "password".
     */
    public const USERS = [
        ['Thomas Alrek', 'thomas@alrek.no', 'Super Admin', 'Creator of Aurora', 'avatars/thomas.jpg', 'en'],
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
        foreach (self::USERS as [$name, $email, $role, $title, $avatar, $locale]) {
            // Portraits in seeders/images/avatars are for people only (the rest of seeders/images
            // goes into the media library); without one, the person is shown with their initials.
            $source = __DIR__.'/images/'.$avatar;
            $avatar = file_exists($source) ? 'avatars/'.basename($avatar) : null;

            if ($avatar !== null) {
                Storage::disk('public')->put($avatar, file_get_contents($source));
            }

            $user = User::query()->updateOrCreate(['email' => $email], [
                'name' => $name,
                'password' => 'password',
                'job_title' => $title,
                'avatar' => $avatar,
                'locale' => $locale,
                'is_active' => true,
            ]);

            $user->forceFill(['email_verified_at' => now()])->save();
            $user->syncRoles([$role]);
        }
    }
}

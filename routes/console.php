<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('seed:admin', function () {
    $user = User::firstOrCreate([
        'email' => 'admin@example.com',
    ], [
        'name' => 'Admin User',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    $this->info('Seeded admin user: ' . $user->email . ' / password');
})->purpose('Create a default admin user for API testing');

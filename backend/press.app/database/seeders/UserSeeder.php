<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::updateOrCreate(['name' => 'Admin'], [
            'description' => 'Full access untuk mengelola website Sastra Arab.',
            'status' => 'Active',
        ]);

        $operator = Role::updateOrCreate(['name' => 'Operator'], [
            'description' => 'Dapat mengelola dan menerbitkan konten website.',
            'status' => 'Active',
        ]);

        $penulisRole = Role::updateOrCreate(['name' => 'Penulis'], [
            'description' => 'Akses dashboard penulis untuk mengelola artikel berita miliknya sendiri.',
            'status' => 'Active',
        ]);

        $kepalaPenulisRole = Role::updateOrCreate(['name' => 'Kepala Penulis'], [
            'description' => 'Akses dashboard kepala penulis untuk mengelola artikel dan akun penulis.',
            'status' => 'Active',
        ]);

        User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Administrator',
            'email' => 'admin@sastraarab.local',
            'password' => bcrypt('password'),
            'role_id' => $admin->id,
            'status' => 'Active',
            'last_active_at' => now(),
        ]);

        User::updateOrCreate(['username' => 'operator'], [
            'name' => 'Operator Konten',
            'email' => 'operator@sastraarab.local',
            'password' => bcrypt('password'),
            'role_id' => $operator->id,
            'status' => 'Active',
            'last_active_at' => now()->subHours(2),
        ]);

        User::updateOrCreate(['username' => 'penulis'], [
            'name' => 'Penulis Berita',
            'email' => 'penulis@sastraarab.local',
            'password' => bcrypt('password'),
            'role_id' => $penulisRole->id,
            'status' => 'Active',
            'last_active_at' => now()->subMinutes(25),
        ]);

        User::updateOrCreate(['username' => 'kepala_penulis'], [
            'name' => 'Kepala Penulis',
            'email' => 'kepala.penulis@sastraarab.local',
            'password' => bcrypt('password'),
            'role_id' => $kepalaPenulisRole->id,
            'status' => 'Active',
            'last_active_at' => now()->subMinutes(20),
        ]);

        $this->command?->newLine();
        $this->command?->info('Login seed accounts:');
        $this->command?->line('Admin         : username=admin          password=password');
        $this->command?->line('Operator      : username=operator       password=password');
        $this->command?->line('Penulis       : username=penulis        password=password');
        $this->command?->line('Kepala Penulis: username=kepala_penulis password=password');
        $this->command?->newLine();
    }
}

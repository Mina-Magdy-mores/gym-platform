<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. مسح الكاش الخاص بالصلاحيات لضمان استقرار الإعدادات
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. إنشاء الأدوار الأساسية الخاصة بالنظام
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'trainer']);
        Role::create(['name' => 'member']);
    }
}
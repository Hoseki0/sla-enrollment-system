<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE admins DROP CONSTRAINT IF EXISTS admins_role_check');
            DB::statement("ALTER TABLE admins ADD CONSTRAINT admins_role_check CHECK (role::text = ANY (ARRAY['super_admin'::text, 'admin'::text, 'registrar'::text, 'cashier'::text, 'teacher'::text]))");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE admins MODIFY COLUMN role ENUM('super_admin', 'admin', 'registrar', 'cashier', 'teacher') NOT NULL DEFAULT 'admin'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE admins DROP CONSTRAINT IF EXISTS admins_role_check');
            DB::statement("ALTER TABLE admins ADD CONSTRAINT admins_role_check CHECK (role::text = ANY (ARRAY['super_admin'::text, 'admin'::text, 'registrar'::text]))");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE admins MODIFY COLUMN role ENUM('super_admin', 'admin', 'registrar') NOT NULL DEFAULT 'admin'");
        }
    }
};

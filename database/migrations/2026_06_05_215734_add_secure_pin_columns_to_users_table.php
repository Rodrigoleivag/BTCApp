<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Añadir columna pin_hash si no existe
        if (!Schema::hasColumn('users', 'pin_hash')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('pin_hash')->nullable()->after('password');
            });
        }

        // Añadir columna pin_failed_attempts para tracking de intentos fallidos
        if (!Schema::hasColumn('users', 'pin_failed_attempts')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('pin_failed_attempts')->default(0)->after('pin_hash');
            });
        }

        // Añadir columna pin_locked_at para bloqueo temporal
        if (!Schema::hasColumn('users', 'pin_locked_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('pin_locked_at')->nullable()->after('pin_failed_attempts');
            });
        }

        // Añadir columna must_change_pin para forzar cambio de PIN en próximo login
        if (!Schema::hasColumn('users', 'must_change_pin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('must_change_pin')->default(true)->after('pin_locked_at');
            });
        }

        // Hashear PINs existentes (los que tengan PIN '0000' o cualquier valor en texto plano)
        // Nota: Como los PINs actuales están en texto plano, los usuarios deberán establecer un nuevo PIN
        // Esta migración marca todos los usuarios para que cambien su PIN en el próximo login
        DB::table('users')
            ->whereNull('pin_hash')
            ->orWhere('pin_hash', '')
            ->update([
                'must_change_pin' => true,
                'pin_failed_attempts' => 0,
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'must_change_pin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('must_change_pin');
            });
        }

        if (Schema::hasColumn('users', 'pin_locked_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('pin_locked_at');
            });
        }

        if (Schema::hasColumn('users', 'pin_failed_attempts')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('pin_failed_attempts');
            });
        }

        if (Schema::hasColumn('users', 'pin_hash')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('pin_hash');
            });
        }
    }
};

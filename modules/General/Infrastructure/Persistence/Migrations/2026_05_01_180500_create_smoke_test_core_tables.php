<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('agencias')) {
            Schema::create('agencias', function (Blueprint $table): void {
                $table->unsignedBigInteger('id_agencia')->primary();
                $table->string('nombre', 120);
                $table->string('direccion', 200)->nullable();
                $table->unsignedBigInteger('distrito_id')->nullable();
                $table->unsignedBigInteger('provincia_id')->nullable();
                $table->unsignedBigInteger('departamento_id')->nullable();
                $table->string('celular', 20)->nullable();
                $table->string('telefono', 20)->nullable();
                $table->text('cuentas')->nullable();
                $table->boolean('nueva_empresa')->default(false);
            });
        }

        if (!Schema::hasTable('cargos')) {
            Schema::create('cargos', function (Blueprint $table): void {
                $table->id();
                $table->string('cargo', 120);
                $table->string('descripcion', 255)->nullable();
                $table->boolean('jefatura')->default(false);
                $table->boolean('habilitado')->default(true);
                $table->string('datos_creacion', 255)->nullable();
                $table->string('datos_actualizacion', 255)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table): void {
                $table->string('dni', 20)->primary();
                $table->string('usuario', 60)->unique();
                $table->string('clave');
                $table->string('nombres', 120);
                $table->string('apellido_paterno', 120);
                $table->string('apellido_materno', 120)->nullable();
                $table->string('sexo', 10)->nullable();
                $table->string('direccion', 200)->nullable();
                $table->date('fecha_nacimiento')->nullable();
                $table->string('telefono', 20)->nullable();
                $table->string('correo_corporativo', 120)->nullable();
                $table->unsignedBigInteger('cargo_id')->nullable();
                $table->unsignedBigInteger('agencia_id')->nullable();
                $table->boolean('habilitado')->default(true);
                $table->boolean('actualizo_clave')->default(true);
                $table->unsignedBigInteger('distrito_id')->nullable();
                $table->unsignedBigInteger('provincia_id')->nullable();
                $table->unsignedBigInteger('departamento_id')->nullable();
                $table->string('usuario_real', 120)->nullable();
                $table->string('datos_creacion', 255)->nullable();
                $table->string('datos_actualizacion', 255)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sesiones')) {
            Schema::create('sesiones', function (Blueprint $table): void {
                $table->id();
                $table->string('usuario_id', 20)->nullable();
                $table->boolean('conectado')->default(false);
                $table->string('get_id', 255)->nullable();
                $table->text('datos_sesion')->nullable();
                $table->dateTime('ultima_accion')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('versiones')) {
            Schema::create('versiones', function (Blueprint $table): void {
                $table->increments('id_version');
                $table->string('numero_version', 30)->default('0.0.1');
                $table->string('observaciones', 255)->nullable();
                $table->timestamps();
            });
        }

        if (!DB::table('agencias')->where('id_agencia', 1)->exists()) {
            DB::table('agencias')->insert([
                'id_agencia' => 1,
                'nombre' => 'Agencia Principal',
                'direccion' => 'Pruebas Locales',
                'nueva_empresa' => 0,
            ]);
        }

        if (!DB::table('cargos')->where('id', 1)->exists()) {
            DB::table('cargos')->insert([
                'id' => 1,
                'cargo' => 'Administrador',
                'descripcion' => 'Cargo de pruebas',
                'jefatura' => 1,
                'habilitado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!DB::table('usuarios')->where('dni', '00000001')->exists()) {
            DB::table('usuarios')->insert([
                'dni' => '00000001',
                'usuario' => 'admin',
                'clave' => Hash::make('admin123'),
                'nombres' => 'Usuario',
                'apellido_paterno' => 'Admin',
                'apellido_materno' => 'Local',
                'cargo_id' => 1,
                'agencia_id' => 1,
                'habilitado' => 1,
                'actualizo_clave' => 1,
                'usuario_real' => 'admin.local',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!DB::table('versiones')->where('id_version', 1)->exists()) {
            DB::table('versiones')->insert([
                'id_version' => 1,
                'numero_version' => '1.0.0-local',
                'observaciones' => 'Bootstrap local para pruebas',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('versiones');
        Schema::dropIfExists('sesiones');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('cargos');
        Schema::dropIfExists('agencias');
    }
};


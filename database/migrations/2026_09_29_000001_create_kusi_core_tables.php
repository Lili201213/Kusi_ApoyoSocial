<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Roles
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 50)->unique();
                $table->string('descripcion', 255)->nullable();
                $table->timestamps();
            });
        }

        // 2. Permisos
        if (!Schema::hasTable('permisos')) {
            Schema::create('permisos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100)->unique();
                $table->string('descripcion', 255)->nullable();
                $table->timestamps();
            });
        }

        // 3. Permiso_Rol (Pivot)
        if (!Schema::hasTable('permiso_rol')) {
            Schema::create('permiso_rol', function (Blueprint $table) {
                $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
                $table->foreignId('permiso_id')->constrained('permisos')->onDelete('cascade');
                $table->primary(['rol_id', 'permiso_id']);
            });
        }

        // 4. Agregar campos propios de KUSI a la tabla 'users'
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'usuario')) {
                $table->string('usuario', 50)->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('users', 'telefono')) {
                $table->string('telefono', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'foto')) {
                $table->string('foto', 255)->nullable()->after('telefono');
            }
            if (!Schema::hasColumn('users', 'rol_id')) {
                $table->foreignId('rol_id')->nullable()->after('password')->constrained('roles');
            }
            if (!Schema::hasColumn('users', 'activo')) {
                $table->boolean('activo')->default(true)->after('rol_id');
            }
        });

        // 5. Tipos de Ayuda
        if (!Schema::hasTable('tipos_ayuda')) {
            Schema::create('tipos_ayuda', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100)->unique();
                $table->string('descripcion', 255)->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        // 6. Beneficiarios
        if (!Schema::hasTable('beneficiarios')) {
            Schema::create('beneficiarios', function (Blueprint $table) {
                $table->id();
                $table->string('dni', 15)->unique();
                $table->string('nombres', 100);
                $table->string('apellidos', 100);
                $table->date('fecha_nacimiento')->nullable();
                $table->enum('sexo', ['M', 'F', 'Otro'])->nullable();
                $table->string('direccion', 255)->nullable();
                $table->string('telefono', 20)->nullable();
                $table->unsignedTinyInteger('num_familiares')->default(1);
                $table->string('situacion', 255)->nullable();
                $table->text('observaciones')->nullable();
                $table->enum('estado', ['activo', 'inactivo'])->default('activo');
                $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['apellidos', 'nombres'], 'idx_benef_apellidos');
            });
        }

        // 7. Necesidades de Beneficiarios
        if (!Schema::hasTable('necesidades_beneficiario')) {
            Schema::create('necesidades_beneficiario', function (Blueprint $table) {
                $table->id();
                $table->foreignId('beneficiario_id')->constrained('beneficiarios')->onDelete('cascade');
                $table->foreignId('tipo_ayuda_id')->constrained('tipos_ayuda');
                $table->date('fecha_registro');
                $table->string('observacion', 255)->nullable();
                $table->enum('estado', ['pendiente', 'atendida'])->default('pendiente');
                $table->timestamps();
            });
        }

        // 8. Donantes
        if (!Schema::hasTable('donantes')) {
            Schema::create('donantes', function (Blueprint $table) {
                $table->id();
                $table->enum('tipo_persona', ['natural', 'juridica'])->default('natural');
                $table->string('nombre_razon_social', 150);
                $table->string('documento', 20)->nullable();
                $table->string('telefono', 20)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('direccion', 255)->nullable();
                $table->timestamps();
            });
        }

        // 9. Donaciones
        if (!Schema::hasTable('donaciones')) {
            Schema::create('donaciones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('donante_id')->constrained('donantes');
                $table->foreignId('user_id')->constrained('users');
                $table->date('fecha_donacion');
                $table->enum('estado', ['registrada', 'anulada'])->default('registrada');
                $table->text('observacion')->nullable();
                $table->string('motivo_anulacion', 255)->nullable();
                $table->timestamp('anulada_at')->nullable();
                $table->foreignId('anulada_por')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index('fecha_donacion', 'idx_don_fecha');
            });
        }

        // 10. Categorias de Producto
        if (!Schema::hasTable('categorias_producto')) {
            Schema::create('categorias_producto', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100)->unique();
                $table->timestamps();
            });
        }

        // 11. Productos
        if (!Schema::hasTable('productos')) {
            Schema::create('productos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('categoria_id')->nullable()->constrained('categorias_producto')->nullOnDelete();
                $table->string('nombre', 150);
                $table->string('descripcion', 255)->nullable();
                $table->string('unidad_medida', 30)->default('unidad');
                $table->decimal('stock_actual', 12, 2)->default(0);
                $table->decimal('stock_minimo', 12, 2)->default(0);
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        // 12. Detalle de Donaciones
        if (!Schema::hasTable('detalle_donaciones')) {
            Schema::create('detalle_donaciones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('donacion_id')->constrained('donaciones')->onDelete('cascade');
                $table->foreignId('producto_id')->constrained('productos');
                $table->decimal('cantidad', 12, 2);
                $table->timestamps();
            });
        }

        // 13. Ayudas Entregadas
        if (!Schema::hasTable('ayudas_entregadas')) {
            Schema::create('ayudas_entregadas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('beneficiario_id')->constrained('beneficiarios');
                $table->foreignId('tipo_ayuda_id')->constrained('tipos_ayuda');
                $table->foreignId('user_id')->constrained('users');
                $table->date('fecha_entrega');
                $table->string('descripcion', 255)->nullable();
                $table->text('observacion')->nullable();
                $table->timestamps();

                $table->index('fecha_entrega', 'idx_ayudas_fecha');
            });
        }

        // 14. Entregas de Alimentos
        if (!Schema::hasTable('entregas_alimentos')) {
            Schema::create('entregas_alimentos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('beneficiario_id')->constrained('beneficiarios');
                $table->foreignId('user_id')->constrained('users');
                $table->date('fecha');
                $table->enum('tipo_entrega', ['almuerzo', 'desayuno', 'cena', 'canasta'])->default('almuerzo');
                $table->unsignedSmallInteger('raciones')->default(1);
                $table->text('observacion')->nullable();
                $table->timestamps();

                $table->index('fecha', 'idx_entregas_fecha');
            });
        }

        // 15. Movimientos de Inventario
        if (!Schema::hasTable('movimientos_inventario')) {
            Schema::create('movimientos_inventario', function (Blueprint $table) {
                $table->id();
                $table->foreignId('producto_id')->constrained('productos');
                $table->foreignId('user_id')->constrained('users');
                $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste']);
                $table->decimal('cantidad', 12, 2);
                $table->decimal('stock_anterior', 12, 2)->default(0);
                $table->decimal('stock_posterior', 12, 2)->default(0);
                $table->string('motivo', 255)->nullable();
                $table->date('fecha');
                $table->foreignId('donacion_id')->nullable()->constrained('donaciones')->nullOnDelete();
                $table->foreignId('ayuda_entregada_id')->nullable()->constrained('ayudas_entregadas')->nullOnDelete();
                $table->foreignId('entrega_alimento_id')->nullable()->constrained('entregas_alimentos')->nullOnDelete();
                $table->timestamps();

                $table->index('fecha', 'idx_mov_fecha');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('entregas_alimentos');
        Schema::dropIfExists('ayudas_entregadas');
        Schema::dropIfExists('detalle_donaciones');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('categorias_producto');
        Schema::dropIfExists('donaciones');
        Schema::dropIfExists('donantes');
        Schema::dropIfExists('necesidades_beneficiario');
        Schema::dropIfExists('beneficiarios');
        Schema::dropIfExists('tipos_ayuda');
        Schema::dropIfExists('permiso_rol');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');
    }
};

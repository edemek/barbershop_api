<?php
/*
 * File name: 2021_01_16_160838_create_availability_hours_table.php
 * Last modified: 2025.02.01 at 12:21:24
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('availability_hours', function (Blueprint $table) {

            if (Schema::hasColumn('availability_hours', 'salon_id')) {
                // Drop the index by its name
                // $table->dropForeign(['salon_id']) ; // Replace with your actual index name
                // Check if the foreign key exists before dropping it
                // $this->dropForeignKeyIfExists('availability_hours', 'salon_id');
                $table->dropColumn('salon_id');
            }
        });
        Schema::dropIfExists('availability_hours');
        Schema::create('availability_hours', function (Blueprint $table) {
            $table->increments('id');
            $table->string('day', 16)->default('monday');
            $table->string('start_at', 16)->nullable();
            $table->string('end_at', 16)->nullable();
            $table->longText('data')->nullable();
            $table->unsignedBigInteger('salon_id');
            $table->foreign('salon_id')->references('id')->on('salons')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_hours');
    }

    private function dropForeignKeyIfExists(string $table, string $column)
    {
        $foreignKeys = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys($table);

        foreach ($foreignKeys as $foreignKey) {
            if (in_array($column, $foreignKey->getLocalColumns())) {
                Schema::table($table, function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign($foreignKey->getName());
                });
                break; // Exit after dropping the foreign key
            }
        }
    }
} ;
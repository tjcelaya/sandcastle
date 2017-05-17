<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class EntrustSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        echo "entrust up " . PHP_EOL;

        Schema::dropIfExists('role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission');
        Schema::dropIfExists('permission_role');

        // Create table for storing roles
        Schema::create('role', function (Blueprint $table) {
            $table->string('name')->primary();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for associating roles to users (Many-to-Many)
        Schema::create('role_user', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->string('role_name');

            $table->foreign('user_id')->references('id')->on('user')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_name')->references('name')->on('role')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['user_id', 'role_name']);
        });

        // Create table for storing permissions
        Schema::create('permission', function (Blueprint $table) {
            $table->string('name')->primary();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('permission_role', function (Blueprint $table) {
            $table->string('permission_name');
            $table->string('role_name');

            $table->foreign('permission_name')->references('name')->on('permission')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_name')->references('name')->on('role')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permission_name', 'role_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        echo "entrust down " . PHP_EOL;

        Schema::drop('permission_role');
        Schema::drop('permission');
        Schema::drop('role_user');
        Schema::drop('role');
    }
}

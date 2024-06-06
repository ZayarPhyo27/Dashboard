<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'user_name');
            $table->integer('role_id')->after('name')->nullable();
            $table->string('fcm_token')->after('password')->nullable();
            $table->string('device_id')->after('fcm_token')->nullable();
            $table->integer('is_content_update')->after('device_id')->nullable();
            $table->integer('created_by')->after('device_id')->nullable();
            $table->integer('updated_by')->after('device_id')->nullable();
            $table->integer('deleted_by')->after('is_content_update')->nullable();
            $table->softDeletes()->after('deleted_by');
            $table->dropColumn('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable();
            $table->renameColumn('user_name', 'name');
            $table->dropColumn('role_id');
            $table->dropColumn('is_content_update');
            $table->dropColumn('fcm_token');
            $table->dropColumn('device_id');
            $table->dropColumn('deleted_by');
            $table->dropColumn('deleted_at');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
}

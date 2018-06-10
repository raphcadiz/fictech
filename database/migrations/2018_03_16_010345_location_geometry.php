<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LocationGeometry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_locations', function (Blueprint $table) {
            $table->decimal('lng', 11, 8)->nullable()->after('address');
            $table->decimal('lat', 10, 8)->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_locations', function (Blueprint $table) {
            $table->dropColumn('lng');
            $table->dropColumn('lat');
        });
    }
}

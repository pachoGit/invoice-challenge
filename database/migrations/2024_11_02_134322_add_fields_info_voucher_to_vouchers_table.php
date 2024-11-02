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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('serie', 4)->nullable()->after('user_id');
            $table->string('correlative', 8)->nullable()->after('user_id');
            $table->string('type_voucher', 2)->nullable()->after('user_id');
            $table->string('type_currency', 3)->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('serie');
            $table->dropColumn('correlative');
            $table->dropColumn('type_voucher');
            $table->dropColumn('type_currency');
        });
    }
};

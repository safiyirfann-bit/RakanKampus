<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Profile photos used to be saved as files on local disk (the `photo`
     * column just held the storage path). Render's free web service plan
     * has no persistent disk, so that path gets wiped every time the app
     * redeploys or the free instance spins down after inactivity — the
     * photo would "disappear" for anyone who hadn't already cached it in
     * their browser. Storing the photo itself (as a base64 data URI) in
     * the database instead means it lives with the rest of the user's
     * row and survives redeploys/spin-downs like everything else does.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->longText('photo_data')->nullable()->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('photo_data');
        });
    }
};

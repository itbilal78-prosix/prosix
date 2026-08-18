<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // Agreement accepted or not
            $table->boolean('terms_accepted')
                ->default(false)
                ->after('status');

            // Server-side acceptance time
            $table->timestamp('terms_accepted_at')
                ->nullable()
                ->after('terms_accepted');

            // Agreement PDF
            $table->string('agreement_pdf')
                ->nullable()
                ->after('terms_accepted_at');

            // Agreement version
            $table->string('agreement_version', 50)
                ->nullable()
                ->after('agreement_pdf');

            // Customer IP
            // 45 supports IPv4 + IPv6
            $table->string('agreement_ip', 45)
                ->nullable()
                ->after('agreement_version');

            // Browser / Device information
            $table->text('agreement_user_agent')
                ->nullable()
                ->after('agreement_ip');

            // Where agreement was accepted
            $table->string('agreement_acceptance_source', 50)
                ->nullable()
                ->after('agreement_user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'terms_accepted',
                'terms_accepted_at',
                'agreement_pdf',
                'agreement_version',
                'agreement_ip',
                'agreement_user_agent',
                'agreement_acceptance_source',
            ]);
        });
    }
};

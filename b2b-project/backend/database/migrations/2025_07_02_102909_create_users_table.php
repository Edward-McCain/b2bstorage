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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->unique();
            $table->text('glink');
            $table->integer('role')->default(0);
            $table->text('acc_type');
            $table->integer('status_subscription');
            $table->text('first_name');
            $table->text('last_name');
            $table->text('user_name');
            $table->text('position');
            $table->text('email')->unique();
            $table->text('phone_number');
            $table->boolean('verified_email')->default(false);
            $table->boolean('phone_ok')->default(false);
            $table->text('password');
            $table->text('fcm_token')->nullable();
            $table->text('fcm_token_android')->nullable();
            $table->text('timezone');
            $table->text('last_logged_in');
            $table->boolean('is_online')->default(false);
            $table->text('language');
            $table->text('messages_language');
            $table->text('country');
            $table->text('city');
            $table->text('avatar_url')->nullable();
            $table->boolean('banned')->default(false);
            $table->text('currency');
            $table->decimal('balance', 15, 10)->default(0);
            $table->decimal('ref_balance', 15, 10)->default(0);
            $table->integer('demo_balance')->default(0);
            $table->decimal('bonus_balance', 15, 10)->default(0);
            $table->text('inn')->nullable();
            $table->bigInteger('comp_pinfl')->nullable();
            $table->boolean('comp_state')->default(false);
            $table->text('company_type')->nullable();
            $table->text('company_name')->nullable();
            $table->text('company_description')->nullable();
            $table->integer('company_rating')->default(0);
            $table->text('com_address')->nullable();
            $table->text('com_leader')->nullable();
            $table->text('comp_logo_url')->nullable();
            $table->text('comp_phone')->nullable();
            $table->text('comp_mail')->nullable();
            $table->text('comp_website_url')->nullable();
            $table->text('company_link')->nullable();
            $table->text('company_statuses')->nullable();
            $table->integer('comp_verified')->default(0);
            $table->integer('comp_tariff')->default(0);
            $table->boolean('deal_seen')->default(false);
            $table->boolean('notification_email')->default(true);
            $table->boolean('notification_email_deal')->default(true);
            $table->boolean('notification_email_system')->default(true);
            $table->boolean('notification_email_chat')->default(true);
            $table->boolean('notification_email_subscription')->default(true);
            $table->boolean('notification_sms_chat')->default(true);
            $table->boolean('notification_sms_custom')->default(true);
            $table->boolean('notification_sms_system')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('catch')->nullable();
            $table->bigInteger('reg_date');
            $table->boolean('moderated')->default(false);
            $table->text('gen_key')->nullable();
            $table->text('referer')->nullable();
            $table->text('invite_link')->nullable();
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $data = [
            [
                'name' => 'Admin Forgot Password',
                'subject' => 'Reset Password',
                'description' => '<p>Dear&nbsp;&nbsp;##USER_NAME##,<br /><br />You are receiving this email because we received a password reset request for your account. Click on below link for update your passowrd<br />##RESET_PASSWORD_LINK##<br /><br />If you did not request a password reset, no further action is required.<br /><br />Thanks &amp; Regards,<br />MightyHealth Team</p>',
                'status' => '1',
                'slug' => 'admin-reset-password',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Welcome Email',
                'subject' => 'Welcome to Our Community!',
                'description' => '<p>Dear ##USERNAME##,</p><p>We&#39;re thrilled to have you with us!</p><p>If you have any questions or need assistance, feel free to reach out.</p><p>Best,<br />The ##system_application_name## Team</p>',
                'status' => '1',
                'slug' => 'welcome-mail-to-user',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'New Enquiry to admin',
                'subject' => 'New Enquiry',
                'description' => '<p>Hello Admin,<br /><br />You received a new enquiry from ##USER_NAME## :<br />##Mobile## ##email##<br />##message##</p><p>Thanks<br />Team MightyHealth</p>',
                'status' => '1',
                'slug' => 'enquiry-to-admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('email_templates')->insert($data);
    }
}

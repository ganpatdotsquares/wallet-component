<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
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
                'title' => 'Website Name',
                'slug' => 'system_application_name',
                'config_value' => 'Timesheet',
                'manager' => 'general',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Admin Email',
                'slug' => 'admin_email',
                'config_value' => 'adminds@yopmail.com',
                'manager' => 'general',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'From Email',
                'slug' => 'from_emial',
                'config_value' => 'adminds@yopmail.com',
                'manager' => 'general',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Telephone',
                'slug' => 'telephone',
                'config_value' => '+91-7665880635',
                'manager' => 'general',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Office Location',
                'slug' => 'office_address',
                'config_value' => 'TDP Agency The Barn Moat House Farm IP7 7DB',
                'manager' => 'general',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Admin Page Limit',
                'slug' => 'admin_page_limit',
                'config_value' => '20',
                'manager' => 'general',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Admin Date Format',
                'slug' => 'admin_date_format',
                'config_value' => 'd F, Y',
                'manager' => 'general',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Admin Date Time Format',
                'slug' => 'admin_date_time_format',
                'config_value' => 'd F, Y H:i A',
                'manager' => 'general',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Default currency',
                'slug' => 'default_currency',
                'config_value' => 'USD',
                'manager' => 'general',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Currency Sign',
                'slug' => 'currency_sign',
                'config_value' => '$',
                'manager' => 'general',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Main Logo',
                'slug' => 'main_logo',
                'config_value' => 'tribal.png',
                'manager' => 'theme_images',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Main Favicon',
                'slug' => 'main_favicon',
                'config_value' => 'tribal.png',
                'manager' => 'theme_images',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Facebook URL',
                'slug' => 'facebook_url',
                'slug' => 'facebook_url',
                'config_value' => 'facebook.com',
                'manager' => 'social',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Twitter URL',
                'slug' => 'twitter_url',
                'slug' => 'twitter_url',
                'config_value' => 'twitter.com',
                'manager' => 'social',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Instagram URL',
                'slug' => 'instagram_url',
                'slug' => 'instagram_url',
                'config_value' => 'instagram.com',
                'manager' => 'social',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Google Plus URL',
                'slug' => 'google_plus_url',
                'slug' => 'google_plus_url',
                'config_value' => 'google.com',
                'manager' => 'social',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Youtube URL',
                'slug' => 'youtube_url',
                'slug' => 'youtube_url',
                'config_value' => 'youtube.com',
                'manager' => 'social',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Front Page Limit',
                'slug' => 'front_page_limit',
                'config_value' => '10',
                'manager' => 'general',
                'field_type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('settings')->insert($data);
    }
}

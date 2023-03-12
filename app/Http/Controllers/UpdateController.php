<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Model\Admin;
use App\Model\AdminRole;
use App\Model\BusinessSetting;
use App\Traits\ActivationClass;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class UpdateController extends Controller
{
    use ActivationClass;

    public function update_software_index()
    {
        return view('update.update-software');
    }

    public function update_software(Request $request)
    {
        Helpers::setEnvironmentValue('SOFTWARE_ID', 'MzI3OTE2MzE=');
        Helpers::setEnvironmentValue('BUYER_USERNAME', $request['username']);
        Helpers::setEnvironmentValue('PURCHASE_CODE', $request['purchase_key']);
        Helpers::setEnvironmentValue('SOFTWARE_VERSION', '6.0');
        Helpers::setEnvironmentValue('APP_NAME', 'grofresh');
        Helpers::setEnvironmentValue('APP_MODE', 'live');

        if (!$this->actch()) {
            return redirect(base64_decode('aHR0cHM6Ly82YW10ZWNoLmNvbS9zb2Z0d2FyZS1hY3RpdmF0aW9u'));
        }

        Artisan::call('migrate', ['--force' => true]);
        $previousRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.php');
        $newRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.txt');
        copy($newRouteServiceProvier, $previousRouteServiceProvier);

        Artisan::call('optimize:clear');

        if (BusinessSetting::where(['key' => 'terms_and_conditions'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'terms_and_conditions',
                'value' => ''
            ]);
        }
        if (BusinessSetting::where(['key' => 'razor_pay'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'razor_pay',
                'value' => '{"status":"1","razor_key":"","razor_secret":""}'
            ]);
        }
        if (BusinessSetting::where(['key' => 'minimum_order_value'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'minimum_order_value'], [
                'value' => 1
            ]);
        }

        //for modified language [new multi lang in admin]
        $languages = Helpers::get_business_settings('language');
        $lang_array = [];
        $lang_flag = false;

        foreach ($languages as $key => $language) {
            if (gettype($language) != 'array') {
                $lang = [
                    'id' => $key + 1,
                    'name' => $language,
                    'direction' => 'ltr',
                    'code' => $language,
                    'status' => 1,
                    'default' => $language == 'en' ? true : false,
                ];

                array_push($lang_array, $lang);
                $lang_flag = true;
            }
        }
        if ($lang_flag == true) {
            BusinessSetting::where('key', 'language')->update([
                'value' => $lang_array
            ]);
        }
        //lang end

        if (BusinessSetting::where(['key' => 'time_zone'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'time_zone'], [
                'value' => 'Pacific/Midway'
            ]);
        }
        if (BusinessSetting::where(['key' => 'decimal_point_settings'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'decimal_point_settings'], [
                'value' => 2
            ]);
        }

        DB::table('business_settings')->updateOrInsert(['key' => 'phone_verification'], [
            'value' => 0
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'msg91_sms'], [
            'key' => 'msg91_sms',
            'value' => '{"status":0,"template_id":null,"authkey":null}'
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => '2factor_sms'], [
            'key' => '2factor_sms',
            'value' => '{"status":"0","api_key":null}'
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'nexmo_sms'], [
            'key' => 'nexmo_sms',
            'value' => '{"status":0,"api_key":null,"api_secret":null,"signature_secret":"","private_key":"","application_id":"","from":null,"otp_template":null}'
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'twilio_sms'], [
            'key' => 'twilio_sms',
            'value' => '{"status":0,"sid":null,"token":null,"from":null,"otp_template":null}'
        ]);
        if (BusinessSetting::where(['key' => 'pagination_limit'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'pagination_limit'], [
                'value' => 10
            ]);
        }
        if (BusinessSetting::where(['key' => 'map_api_key'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'map_api_key'], [
                'value' => ''
            ]);
        }
        if (BusinessSetting::where(['key' => 'delivery_management'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'delivery_management'], [
                'value' => json_encode([
                    'status' => 0,
                    'min_shipping_charge' => 0,
                    'shipping_per_km' => 0,
                ]),
            ]);
        }
        if (BusinessSetting::where(['key' => 'recaptcha'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'recaptcha'], [
                'value' => '{"status":"0","site_key":"","secret_key":""}'
            ]);
        }

        if (BusinessSetting::where(['key' => 'time_format'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'time_format'], [
                'value' => '24'
            ]);
        }

        if (BusinessSetting::where(['key' => 'play_store_config'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'play_store_config'], [
                'value' => '{"status":"","link":"","min_version":""}'
            ]);
        } else {
            $play_store_config = Helpers::get_business_settings('play_store_config');
            DB::table('business_settings')->updateOrInsert(['key' => 'play_store_config'], [
                'value' => json_encode([
                    'status' => $play_store_config['status'],
                    'link' => $play_store_config['link'],
                    'min_version' => "1",
                ])
            ]);
        }

        if (BusinessSetting::where(['key' => 'app_store_config'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'app_store_config'], [
                'value' => '{"status":"","link":"","min_version":""}'
            ]);
        } else {
            $app_store_config = Helpers::get_business_settings('app_store_config');
            DB::table('business_settings')->updateOrInsert(['key' => 'app_store_config'], [
                'value' => json_encode([
                    'status' => $app_store_config['status'],
                    'link' => $app_store_config['link'],
                    'min_version' => "1",
                ])
            ]);
        }

        if (BusinessSetting::where(['key' => 'minimum_stock_limit'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'minimum_stock_limit'], [
                'value' => 1
            ]);
        }

        if (BusinessSetting::where(['key' => 'faq'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'faq'], [
                'value' => null
            ]);
        }

        if (BusinessSetting::where(['key' => 'google_social_login'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'google_social_login'], [
                'value' => 1
            ]);
        }

        if (BusinessSetting::where(['key' => 'facebook_social_login'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'facebook_social_login'], [
                'value' => 1
            ]);
        }

        //for role management
        $admin_role = AdminRole::get()->first();
        if (!$admin_role) {
            DB::table('admin_roles')->insertOrIgnore([
                'id' => 1,
                'name' => 'Master Admin',
                'module_access' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $admin = Admin::get()->first();
        if ($admin) {
            $admin->admin_role_id = 1;
            $admin->save();
        }


        $mail_config = \App\CentralLogics\Helpers::get_business_settings('mail_config');
        BusinessSetting::where(['key' => 'mail_config'])->update([
            'value' => json_encode([
                "status" => 0,
                "name" => $mail_config['name'],
                "host" => $mail_config['host'],
                "driver" => $mail_config['driver'],
                "port" => $mail_config['port'],
                "username" => $mail_config['username'],
                "email_id" => $mail_config['email_id'],
                "encryption" => $mail_config['encryption'],
                "password" => $mail_config['password']
            ]),
        ]);

        DB::table('branches')->insertOrIgnore([
            'id' => 1,
            'name' => 'Main Branch',
            'email' => 'main@gmail.com',
            'password' => '',
            'coverage' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect('/admin/auth/login');
    }
}

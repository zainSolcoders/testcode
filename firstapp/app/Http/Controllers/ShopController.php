<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Plan;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Http;

use Redirect;

class ShopController extends Controller
{
    public static function generate_install_url(Request $request) {
        $store = Store::where('shop_url', $request->shop )->first();
        $params['shop'] = $request->shop;
     
        BillingController::save_charge_id($request->charge_id,$request->shop);

        $shop_found = Store::where('shop_url', $params['shop'])->where('shopify_token','!=', '')->exists();
        if ($shop_found) {
            return Redirect::to(route('app_view',$request));
        }

        $redirect_url_for_token = secure_url('generate_token');
        $api_key = env('SHOPIFY_API_KEY');
        $scopes = env('SHOPIFY_SCOPES');
        // Build install/approval URL to redirect to
        $install_url = "https://" . $_GET['shop'] . "/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . $redirect_url_for_token;

        return Redirect::to($install_url);
    }

    public static function generate_and_save_token(Request $request) {

        // Set variables for our request
        $api_key = env('SHOPIFY_API_KEY');
        $shared_secret = env('SHOPIFY_API_SECRET');

        $params = $_GET; // Retrieve all request parameters
        $hmac = $_GET['hmac']; // Retrieve HMAC request parameter
        $params = array_diff_key($params, array('hmac' => '')); // Remove hmac from params
        ksort($params); // Sort params lexographically
        // Compute SHA256 digest
        $computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);
        // Use hmac data to check that the response is from Shopify or not


        if (hash_equals($hmac, $computed_hmac)) {
            $shop_found = Store::where('shop_url', $params['shop'])->where('shopify_token','!=', '')->exists();
            $store = Store::where('shop_url',$params['shop'])->first();
            $first_view =  "https://".$params['shop'].'/admin/apps/'.env('APP_NAME');
            if ($shop_found ) {
                return Redirect::to(route('app_view',$request));

            } else {

                    if( empty( $store->shopify_token ) ){
                    // Set variables for our request
                        $query = array(
                        "client_id" => $api_key, // Your API key
                        "client_secret" => $shared_secret, // Your app credentials (secret key)
                        "code" => $params['code'] // Grab the access key from the URL
                    );

                    // Generate access token URL
                    $access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";



                    // Configure curl client and execute request
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_URL, $access_token_url);
                    curl_setopt($ch, CURLOPT_POST, count($query));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
                    $result = curl_exec($ch);
                    curl_close($ch);

                    // Store the access token
                    $result = json_decode($result, true);
                    $access_token = $result['access_token'];


                    if( empty( $store->settings ) ){
                        $settings = '{"name":"Name","image":"Image","quantity":"Stock Status","price":"Price","active_column":["name","image","quantity","price"],"product_feature_enable":"1","check_product":"1","include_products":"","btn_styling_enable":1,"btn_bg":"#0dcaf0","btn_color":"#000000","btn_text":"Add to Cart"}';
                    }
                    else{
                        $settings =  $store->settings;
                    }
                    $args = [
                        'shopify_token' => $access_token,
                        'settings' => $settings
                    ];

                    Store::updateOrInsert(['shop_url' => $params['shop']],$args);

                    $webhook = WebhookController::create_uninstall_webhook( $params );

                }

                $isBillingActive = false;
                if(!empty( $store->current_charge_id ) ){
                    $isBillingActive = BillingController::check_billing($request);

                    if($isBillingActive == true){
                       return Redirect::to( $first_view );
                    }
                }

                if( empty( $store->current_charge_id ) || $isBillingActive == false ){
                    if(!empty($store->trial_expiration_date)){
                        $trial_url = Store::create_charge_without_trail($params['shop']);
                    }
                    else{
                        $trial_url = Store::create_trial($params['shop']);
                    }

                    if( $trial_url ){
                        return Redirect::to( $trial_url );
                    }
                }

                return Redirect::to( $first_view );

            }
        }
        else {
            // Someone is trying to be shady!
            die('This request is NOT from Shopify!');
        }
    }

    public static function gdpr_view_customer(Request $request) {

        return [];

    }

    public static function gdpr_delete_customer(Request $request) {

        return [];

    }

    public static function gdpr_delete_shop(Request $request) {

        return [];

    }

    public static function uninstall(Request $request) {

        if( isset( $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'] ) && ( isset( $_SERVER['HTTP_X_SHOPIFY_TOPIC'] ) &&  $_SERVER['HTTP_X_SHOPIFY_TOPIC'] == 'app/uninstalled' )   ){
            $shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
            $store = Store::where( 'shop_url',$shop )->first();
                if( !empty( $store ) && !empty( $store->shopify_token )  ){
                        $args = [
                            'shopify_token' => '',
                        ];

                    Store::updateOrInsert(['shop_url' => $shop],$args);
                }
            }
        return [];
    }

    public static function shopify_rest_call($token, $shop, $api_endpoint, $query = array(), $method = 'GET', $request_headers = array()) {

        // Build URL
        $url = "https://" . $shop .$api_endpoint;
        if (!is_null($query) && in_array($method, array('GET',  'DELETE'))) $url = $url . "?" . http_build_query($query);

        // Configure cURL
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
        // curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,  0);
        curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

        // Setup headers
        $request_headers[] = "";
        if (!is_null($token)) $request_headers[] = "X-Shopify-Access-Token: " . $token;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);

        if ($method != 'GET' && in_array($method, array('POST', 'PUT'))) {
            if (is_array($query)) $query = http_build_query($query);
            curl_setopt ($curl, CURLOPT_POSTFIELDS, $query);
        }

        // Send request to Shopify and capture any errors
        $response = curl_exec($curl);
        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);

        // Close cURL to be nice
        curl_close($curl);

        // Return an error is cURL has a problem
        if ($error_number) {
            return $error_message;
        } else {

            // No error, return Shopify's response by parsing out the body and the headers
            $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

            // Convert headers into an array
            $headers = array();
            $header_data = explode("\n",$response[0]);
            $headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
            array_shift($header_data); // Remove status, we've already set it above
            foreach($header_data as $part) {
                $h = explode(":", $part);
                $headers[trim($h[0])] = trim($h[1]);
            }

            // Return headers and Shopify's response
            return array('headers' => $headers, 'response' => $response[1]);

        }
    }

    public function saveSetting(Request $request) {
        
        $data = $request->all();
        $shop = $data["shop"];
        $store = Store::where('shop_url',$data['shop'])->first();
        $plan = Plan::find($store->plan_id);

        if (isset($data['product_feature_enable']) &&  $data['product_feature_enable'] == 1) {
            $setting['product_feature_enable'] = $data['product_feature_enable'];
            if(isset($data['include_products']) && !empty($data['include_products'])){
                if(count($data['include_products']) > $plan->products && $plan->products != 0){
                    $params['shop'] = $data['shop'];

                    $params['error'] = 'You can\'t select more then '.$plan->products.' products';
                    $params['host'] = $data["host"];
                    return Redirect::to(route('app_view', $params));
                }
                $setting['include_products'] = isset($data['include_products']) ? $data['include_products'] : "";
                $api_endpoint = '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/metafields.json';
                $solcoders_metafields = [
                    "metafield" => ["namespace" => "solcoders", "key" => "specific_products", "value" => json_encode($setting['include_products']), "type" => "multi_line_text_field"]
                ];
                $metaFieldCall = ShopController::shopify_rest_call($store->shopify_token, $request->shop, $api_endpoint, $solcoders_metafields, 'POST');
            }
        }
        else{
            $setting['product_feature_enable'] = 2;

            $api_endpoint = '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/metafields.json';
            $solcoders_metafields = [
                "metafield" => ["namespace" => "solcoders", "key" => "specific_products", "value" => "null", "type" => "multi_line_text_field"]
            ];
            $metaFieldCall = ShopController::shopify_rest_call($store->shopify_token, $request->shop, $api_endpoint, $solcoders_metafields, 'POST');
        }

        if (isset($data['image_feature_enable']) &&  $data['image_feature_enable'] == 1) {
            $setting['image_feature_enable'] = 1;
        }
        else{
            $setting['image_feature_enable'] = 2;
        }


        $themes = AppController::getThemes($shop);
        $theme_id = "";
        foreach($themes as $theme){
            if($theme->role == "main"){
                $theme_id = $theme->id;
            }
        }
        $filename = 'sections/main-product.liquid';
        $file = AppController::getAsset($shop, $theme_id, $filename);

        if(!empty($file)){
            $updatedValue = $file->value;
            if ($filename == 'sections/main-product.liquid') {

                if($setting['product_feature_enable'] == 1){
                    if (!str_contains($updatedValue, "{%comment%} smsva_start {%- endcomment -%}")) {
                        $updatedValue = str_replace("{%- when 'title' -%}","{%- when 'title' -%}\n             {%comment%} smsva_start {%- endcomment -%}\n             {% capture smsva_specific_products %} {% if shop.metafields.solcoders.specific_products and  shop.metafields.solcoders.specific_products != 'null' %} {{ shop.metafields.solcoders.specific_products }} {% else %} \"\" {% endif %} {% endcapture %}\n             {%- assign smsva_condition = false  -%}\n                 {%- if smsva_specific_products contains product.id  -%}\n                        {%- assign smsva_condition = false  -%}\n                 {% else %}\n                    {%- assign smsva_condition = true  -%}\n                {% endif %}\n",$updatedValue);
                        $updatedValue = str_replace("{%- when 'quantity_selector' -%}","{%- when 'quantity_selector' -%}\n             {%- if smsva_condition -%}\n",$updatedValue);
                        $updatedValue = str_replace("{%- when 'popup' -%}","{% endif %}\n            {%- when 'popup' -%}",$updatedValue);
                        $updatedValue = str_replace("{%- when 'variant_picker' -%}","{%- when 'variant_picker' -%}\n              {% if smsva_condition %}",$updatedValue);
                        $updatedValue = str_replace("{%- when 'buy_buttons' -%}","{% endif %}\n            {%- when 'buy_buttons' -%}\n            {% if smsva_condition %}",$updatedValue);
                        $updatedValue = str_replace("{%- when 'rating' -%}","{% endif %}\n            {%- when 'rating' -%}",$updatedValue);
                    }
                }
                else{
                    if (str_contains($updatedValue, "{%comment%} smsva_start {%- endcomment -%}")) {
                        $updatedValue = str_replace("{%- when 'title' -%}\n             {%comment%} smsva_start {%- endcomment -%}\n             {% capture smsva_specific_products %} {% if shop.metafields.solcoders.specific_products and  shop.metafields.solcoders.specific_products != 'null' %} {{ shop.metafields.solcoders.specific_products }} {% else %} \"\" {% endif %} {% endcapture %}\n             {%- assign smsva_condition = false  -%}\n                 {%- if smsva_specific_products contains product.id  -%}\n                        {%- assign smsva_condition = false  -%}\n                 {% else %}\n                    {%- assign smsva_condition = true  -%}\n                {% endif %}\n","{%- when 'title' -%}",$updatedValue);
                        $updatedValue = str_replace("{%- when 'quantity_selector' -%}\n             {%- if smsva_condition -%}\n","{%- when 'quantity_selector' -%}",$updatedValue);
                        $updatedValue = str_replace("{% endif %}\n            {%- when 'popup' -%}","{%- when 'popup' -%}",$updatedValue);
                        $updatedValue = str_replace("{%- when 'variant_picker' -%}\n              {% if smsva_condition %}","{%- when 'variant_picker' -%}",$updatedValue);
                        $updatedValue = str_replace("{% endif %}\n            {%- when 'buy_buttons' -%}\n            {% if smsva_condition %}","{%- when 'buy_buttons' -%}",$updatedValue);
                        $updatedValue = str_replace("{% endif %}\n            {%- when 'rating' -%}","{%- when 'rating' -%}",$updatedValue);
                    }

                }
                //dd($updatedValue);
                $api_endpoint = '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/themes/' . $theme_id . '/assets.json';
                $asset = ['asset' => [
                    "key" => $filename,
                    "value" => $updatedValue
                ]];
                $update = ShopController::shopify_rest_call($store->shopify_token, $shop, $api_endpoint, $asset, 'PUT');

                $update = json_decode($update['response']);
            }
        }
        $store->settings = $setting;
        $store->update();
        $params['shop'] = $data['shop'];
        $params['success'] = 'Settings Update Successfully';
        $params['host'] = $data["host"];
        return Redirect::to(route('app_view', $params));
    }
    public function getSettings(Request $request) {
        $store = Store::where('shop_url',$request->shop )->where('shopify_token','!=','' )->first();
        $settings = $store->settings != '' ?  json_decode($store->settings) : [];
        return $settings;
    }
    public function app_view(Request $request) {
        $store = Store::where('shop_url',$request->shop )->where('shopify_token','!=','' )->first();
        $isBillingActive = BillingController::check_billing($request);
        $products = Store::getProducts($request->shop);
        $plans = Plan::All();
        if($store){
            $host = $request->host;
            $getPlan = Plan::find($store->plan_id);
            $settings = $store->settings;
            if (is_string($settings)) {
                $settings = json_decode($store->settings);
            }
            // dd($settings);
            return view('shopify.app_view',compact('store','host','isBillingActive','products','settings','plans','getPlan'));
        }
        return 'Store not found';
    }

}

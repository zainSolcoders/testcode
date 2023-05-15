<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ShopController;


class Store extends Model
{
    use HasFactory;
    protected $table = 'shopify_stores_data';
    protected $fillable = [
        'shop_url',
        'shopify_token',
        'is_trial_expired',
        'current_charge_id',
    ];

    /**
     * Create 3-days trial for app.
     * @param string $shop_url
     * @return string|boolean
     */
    static function create_trial($shop_url)
    {

        $store = Store::where('shop_url', $shop_url)->first();
        if ($store) {
            $plan = Plan::get_default_plan();
            $first_view =  "https://" . $shop_url . '/admin/apps/' . env('APP_NAME');
            if ($store->trial_expiration_date) {

                $array = [
                    "recurring_application_charge" => [
                        "name" => $plan->name,
                        "price" => $plan->price,
                        "return_url" => $first_view,
                        "test" => env('PAYMENT_MODE', false)
                    ]
                ];
            } else {


                $array = [
                    "recurring_application_charge" => [
                        "name" => $plan->name,
                        "price" => $plan->price,
                        "return_url" => $first_view,
                        "test" => env('PAYMENT_MODE', false),
                        "trial_days" => 10
                    ]
                ];
            }
            $charge = ShopController::shopify_rest_call($store->shopify_token, $shop_url, '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/recurring_application_charges.json', $array, 'POST');

            $result = json_decode($charge['response'], JSON_PRETTY_PRINT);
            // dd($result['recurring_application_charge']['']);
            $confirmation_url = $result['recurring_application_charge']['confirmation_url'];


            return $confirmation_url;
        }
    }

    /**
     * Create charge for app.
     * @param string $shop_url
     * @return string|boolean
     */
    static function create_charge_without_trail($shop_url)
    {

        $store = Store::where('shop_url', $shop_url)->first();
        if ($store) {
            $first_view =  "https://" . $shop_url . '/admin/apps/' . env('APP_NAME');
            $array = [
                "recurring_application_charge" => [
                    "name" => env('PLAN_NAME'),
                    "price" => env('PLAN_PRICE'),
                    "return_url" => $first_view,
                    "test" => env('PAYMENT_MODE', false)
                ]
            ];

            $charge = ShopController::shopify_rest_call($store->shopify_token, $shop_url, '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/recurring_application_charges.json', $array, 'POST');
            // dd($charge);
            $result = json_decode($charge['response']);
            // return $result->recurring_application_charge;
            $confirmation_url = $result->recurring_application_charge->confirmation_url;


            return $confirmation_url;
        }
    }

    /**
     * Cancel charge for app.
     * @param string $shop_url
     * @return string|boolean
     */
    static function cancel_charge($shop_url)
    {

        $store = Store::where('shop_url', $shop_url)->first();
        if ($store) {
            $first_view =  "https://" . $shop_url . '/admin/apps/' . env('APP_NAME');

            $charge = ShopController::shopify_rest_call($store->shopify_token, $shop_url, '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/recurring_application_charges/' . $store->current_charge_id . '.json', [], 'DELETE');
            $first_view =  "https://" . $shop_url . '/admin/apps/' . env('APP_NAME');
            return $first_view;
        }
    }
    /**
     * Get Products with shop.
     * @param string $shop_url
     * @return JSON|Object
     */
    static function getProducts($shop_url)
    {

        $store = Store::where('shop_url', $shop_url)->first();
        if ($store) {
            $api_endpoint = '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/products.json';
            $products = ShopController::shopify_rest_call($store->shopify_token, $shop_url, $api_endpoint, array(), 'GET');
            $products = json_decode($products['response']);
            $products = $products->products;
            return $products;
        } else {
            return [];
        }
    }

    /**
     * Get Orders with shop.
     * @param string $shop_url
     * @return JSON|Object
     */
    static function getOrders($shop_url, $limit = 50, $sinceId = "")
    {

        $store = Store::where('shop_url', $shop_url)->first();
        if ($store) {
            
            $params = ["limit" => $limit];

            if($sinceId)
            $params["since_id"] = $sinceId;
            
            $api_endpoint = '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/orders/count.json';
            $ordersCount = ShopController::shopify_rest_call($store->shopify_token, $shop_url, $api_endpoint, $params, 'GET');
            $ordersCount = json_decode($ordersCount['response']);
          
            $api_endpoint = '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/orders.json';
            $orders = ShopController::shopify_rest_call($store->shopify_token, $shop_url, $api_endpoint, $params, 'GET');
              
            $orders = json_decode($orders['response']);
            $orders = $orders->orders ?? [];
            return ["orders" => $orders, "count" => $ordersCount];
        } else {
            return [];
        }
    }


    /**
     * Get Orders with shop.
     * @param string $shop_url
     * @return JSON|Object
     */
    static function getAllProducts($shop_url, $limit = 50, $sinceId = "")
    {

        $store = Store::where('shop_url', $shop_url)->first();
        if ($store) {
            
            $params = ["limit" => $limit];

            if($sinceId)
            $params["since_id"] = $sinceId;
            
            $api_endpoint = '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/products/count.json';
            $productsCount = ShopController::shopify_rest_call($store->shopify_token, $shop_url, $api_endpoint, $params, 'GET');
            $productsCount = json_decode($productsCount['response']);
          
            $api_endpoint = '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/products.json';
            $products = ShopController::shopify_rest_call($store->shopify_token, $shop_url, $api_endpoint, $params, 'GET');
              
            $products = json_decode($products['response']);
            $products = $products->products ?? [];
            return ["products" => $products, "count" => $productsCount];
        } else {
            return [];
        }
    }

    /**
     * Get Collections with shop.
     * @param string $shop_url
     * @return JSON|Object
     */
    static function getCollections($shop_url, $limit = 50, $sinceId = "")
    {

        $store = Store::where('shop_url', $shop_url)->first();
        if ($store) {
            
            $params = ["limit" => $limit];

            if($sinceId)
            $params["since_id"] = $sinceId;
            
            $api_endpoint = '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/custom_collections/count.json';
            $collectionsCount = ShopController::shopify_rest_call($store->shopify_token, $shop_url, $api_endpoint, $params, 'GET');
            $collectionsCount = json_decode($collectionsCount['response']);
          
            $api_endpoint = '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/custom_collections.json';
            $collections = ShopController::shopify_rest_call($store->shopify_token, $shop_url, $api_endpoint, $params, 'GET');
              
            $collections = json_decode($collections['response']);
            $collections = $collections->custom_collections ?? [];
            return ["collections" => $collections, "count" => $collectionsCount];
        } else {
            return [];
        }
    }

}

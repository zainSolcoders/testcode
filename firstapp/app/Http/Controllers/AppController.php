<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;

use Illuminate\Support\Facades\Http;

class AppController extends Controller
{
    public static function getShopifyDomain($url)
    {
        $response = Http::get("https://".$url);
        $string = $response->body();
        $explode = explode('Shopify.shop = "',$string);
        $explode = explode('Shopify.locale = "',$explode[1]);
        $shop=str_replace("\";\n","",$explode[0]);
        return $shop;
    }
    public static function getVariants( Request $request ) {
        $store = Store::where('shop_url', $request->shop)->first();

        if( !empty( $store->shopify_token ) ){
            $settings= $store->settings;
            if (is_string($settings)) {
                $settings = json_decode($store->settings);
            }
            if($request->productid){
                if($settings->product_feature_enable == 1 &&  !empty($settings->include_products)  && in_array($request->productid, $settings->include_products)){

                    $productid = $request->productid;
                    $currency = $request->currency;
                    $api_endpoint = '/admin/api/'.env('SHOPIFY_API_VERSION',"2023-01").'/products/'.$productid.'.json';
                    $product = ShopController::shopify_rest_call($store->shopify_token, $request->shop, $api_endpoint , array() ,'GET');
                    $product = json_decode($product['response']);
                    $product = $product->product;
                    $html = '';
                    if(count($product->variants) > 1 && count($product->options) == 1){
                        foreach($product->variants as $variant){
							if($settings->image_feature_enable == 1){
								$html .= "<div class='variant-select border p-1' data-id='$variant->id'>";
							}
							else{
								$html .= "<div class='variant-select border p-3' data-id='$variant->id'>";
							}

                            if($settings->image_feature_enable == 1){

                                if(isset($variant->image_id)){
                                    foreach($product->images as $productimage){
                                        if($variant->image_id == $productimage->id){
                                            $image = $productimage->src;
                                            continue;
                                        }
                                    }
                                }
                                else{
                                    $image = 'https://adpts.solcoder.com/public/images/white-image.png';
                                }
                                $html .= "<img src='$image' width='33px' height='33px' />";
                            }
                            else{

                                $html .= $variant->title;
                            }
                            $html .= "</div>";
                        }
                    }
                    else{
                        $html = 'No Record Found';
                    }
                }
                else{
                    $html = 'No Record Found';
                }

            }
            else{
                $html = 'No Record Found';
            }

            return json_encode($html);
        }
    }

    public static function getThemes($shop)
    {
        $store = Store::where('shop_url', $shop)->first();

        if (!empty($store->shopify_token)) {
            $api_endpoint = '/admin/api/' . env('SHOPIFY_API_VERSION', "2023-01") . '/themes.json';
            $themes = ShopController::shopify_rest_call($store->shopify_token, $shop, $api_endpoint, array(), 'GET');
            $themes = json_decode($themes['response']);
            $themes = $themes->themes;
            return $themes;
        } else {
            return response(['Store not Found']);
        }
    }

    public static function getAsset($shop, $theme_id, $filename)
    {
        $store = Store::where('shop_url', $shop)->first();

        if (!empty($store->shopify_token)) {
            $api_endpoint = '/admin/api/' . env('SHOPIFY_API_VERSION', '2023-01') . '/themes/' . $theme_id . '/assets.json?asset[key]=' . $filename . '&theme_id=' . $theme_id;


            $assets = ShopController::shopify_rest_call($store->shopify_token, $shop, $api_endpoint, array(), 'GET');
            $assets = json_decode($assets['response']);
            if(isset($assets)){
                if(!empty($assets->asset)){
                    $assets = $assets->asset;
                }
                else{
                    $assets = "";
                }
            }
            else{
                $assets = "";
            }
            return $assets;
        } else {
            return response(['Store not Found']);
        }
    }
}

<?php

namespace App\Http\Livewire;

use Illuminate\Http\Request;
use App\Http\Livewire\BaseListing;
use App\Models\Store;

class ShowProducts extends BaseListing
{
    /**
     * set sinceId
     * @var int
     */
    public $sinceId = "";


    /**
     * get products
     *
     * @return mixed
     */
    public function getProducts(Request $request)
    {
        $products = Store::getAllProducts(
            $request->shop,
            $this->limit,
            $this->sinceId
        );
        $this->totalPages = $products["count"];
        return $products["products"];
    }

    /**
     * Render View To Display Shopify Products
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('shopify.livewire.show-products', [
            'products' => $this->getProducts(),
            'pages' => $this->getPaginations()
        ]);
    }
}

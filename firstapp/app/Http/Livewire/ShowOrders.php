<?php

namespace App\Http\Livewire;

use Illuminate\Http\Request;
use App\Http\Livewire\BaseListing;
use App\Models\Store;

class ShowOrders extends BaseListing
{
    /**
     * set sinceId
     * @var int
     */
    public $sinceId = "";


    /**
     * get orders
     *
     * @return mixed
     */
    public function getOrders(Request $request)
    {
        $orders = Store::getOrders(
            $request->shop,
            $this->limit,
            $this->sinceId
        );

        $this->totalPages = $orders["count"];
        $this->sinceId = end($orders["orders"])->id;
        return $orders["orders"];
    }

    /**
     * Render View To Display Shopify Orders
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('shopify.livewire.show-orders', [
            'orders' => $this->getOrders(),
            'pages' => $this->getPaginations()
        ]);
    }
}

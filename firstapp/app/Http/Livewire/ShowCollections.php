<?php

namespace App\Http\Livewire;

use Illuminate\Http\Request;
use App\Http\Livewire\BaseListing;
use App\Models\Store;

class ShowCollections extends BaseListing
{
    /**
     * set sinceId
     * @var int
     */
    public $sinceId = "";


    /**
     * get Collections
     *
     * @return mixed
     */
    public function getCollections(Request $request)
    {
        $collections = Store::getCollections(
            $request->shop,
            $this->limit,
            $this->sinceId
        );
        $this->totalPages = $collections["count"];
        return $collections["collections"];
    }

    /**
     * Render View To Display Shopify Collections
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('shopify.livewire.show-collections', [
            'collections' => $this->getCollections(),
            'pages' => $this->getPaginations()
        ]);
    }
}

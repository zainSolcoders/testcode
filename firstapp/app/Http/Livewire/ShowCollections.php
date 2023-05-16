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
    public function getCollections(string $shop)
    {
        $collections = Store::getCollections(
            $shop,
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
    public function render(Request $request)
    {
        return view('shopify.livewire.show-collections', [
            'collections' => $this->getCollections($request->shop),
            'pages' => $this->getPaginations()
        ]);
    }
}

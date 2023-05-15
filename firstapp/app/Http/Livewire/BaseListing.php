<?php

namespace App\Http\Livewire;

use Livewire\Component;

abstract class BaseListing extends Component
{
    /**
     * set limit
     * @var int
     */
    public $limit = 10;

     /**
     * set totalPages
     * @var int
     */
    protected $totalPages = 10;

    /**
     * get paginations
     *
     * @return mixed
     */
    public function getPaginations()
    {
        return 5;
    }

    public function gotoPage($page){
        return $page;
    }
}

?>
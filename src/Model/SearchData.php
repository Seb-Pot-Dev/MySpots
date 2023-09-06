<?php

namespace App\Model;

use App\Entity\Module;

class SearchData
{
    /** @var string|null */
    public  $search = '';

    // Si array pose problème, changer en type Module 
    /** @var array|null */
    public array $moduleFilter = [];

    /**
     * @var boolean
     */
    public $official = false;
    /**
     * @var boolean
     */
    public $covered = false;
    /**
     * @var string|null
     */
    public $orderCreation= '';
}

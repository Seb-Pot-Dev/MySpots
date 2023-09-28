<?php
namespace App\Model;

use App\Entity\Module;

class SearchData
{
    /** @var string|null */
    public  $search = '';

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
     * @var boolean
     */
    public $onlyValidated = false;
    /**
     * @var string|null
     */
    public $order= '';

}

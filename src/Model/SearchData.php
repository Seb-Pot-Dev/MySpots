<?php

namespace App\Model;

use App\Entity\Module;

class SearchData
{
    /** @var string|null */
    public  $search = '';

    /** @var Module|null */
    public array $moduleFilter = [];

    // /**
    //  * @var boolean
    //  */
    // public $official = false;
    // /**
    //  * @var boolean
    //  */
    // public $covered = false;
}

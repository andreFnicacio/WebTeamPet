<?php
namespace App\Helpers\API\D4sign;

use App\Helpers\API\D4sign\Services\Documents;
use App\Helpers\API\D4sign\Services\Safes;
use App\Helpers\API\D4sign\Services\Templates;
use App\Helpers\API\D4sign\Services\Folders;
use App\Helpers\API\D4sign\Services\Account;
use App\Helpers\API\D4sign\Services\Batches;

class Client extends ClientBase
{
    public $documents;
    
    public function __construct()
    {
        $this->documents 	= new Documents($this);
        $this->safes 		= new Safes($this);
        $this->templates 	= new Templates($this);
        $this->folders	 	= new Folders($this);
        $this->account	 	= new Account($this);
        $this->batches	 	= new Batches($this);
    }
    
}

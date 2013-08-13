<?php

namespace Inventory\Model;

use Zend\ModuleManager\Feature\ServiceProviderInterface;

class InventoryDB
{
	protected $dbconn;
	protected $svc;

	public function __construct($key, $svc)
	{
		$this->svc = $svc;
		$this->dbconn = ($key == 'RO') ? $this->ro() : $this->rw;
	}

    private function rw()
    {
        return $this->svc->get('Database_RW');
    }

    private function ro()
    {
        return $this->svc->get('Database_RO');
    }
}


<?php

namespace Inventory\Model;

use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;

class InventoryDB
{
	protected $dbconn;
	protected $svc;

	public function __construct($key, $svc)
	{
		$this->svc = $svc;
		$this->dbconn = ($key == 'RO') ? $this->ro() : $this->rw;
	}

	public function viewComputer()
	{
		$sql = sprintf('CALL ComputerList()');
		return $this->query($sql);
	}

	public function addComputer($obj)
	{
		$sql = sprintf('CALL ComputerAddUpdate("%s", "%s", %s", "%s", %s", "%s")');
	}

	public function deleteComputer($obj)
	{
		$sql = sprintf('CALL ComputerDelete("%d")');
	}

	public function searchComputer($obj)
	{
		$sql = sprintf('CALL ComputerSearch("%s")', $obj->id);
		return $this->query($sql);
	}

    private function rw()
    {
        return $this->svc->get('Database_RW');
    }

    private function ro()
    {
        return $this->svc->get('Database_RO');
    }

	private function query($sql)
	{
		$query = $this->dbconn->createStatement($sql)->execute();
		if ($query->count() > 0)
			$result = new ResultSet();
			return $result->initialize($query)->toArray();

		return array('error' => 'Result set empty');
	}

	function __destruct()
	{
		unset($this->dbconn);
	}
}

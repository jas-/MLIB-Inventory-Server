<?php

namespace Inventory\Model;

use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class InventoryDB
{
	protected $dbconn;
	protected $svc;

	public function __construct($key, $svc)
	{
		$this->svc = $svc;
		$this->dbconn = ($key == 'RO') ? $this->ro() : $this->rw();
	}

	public function viewComputer()
	{
		$sql = sprintf('CALL ComputerList()');
		return $this->query($sql);
	}

	public function addComputer($obj)
	{
		$sql = sprintf('CALL ComputerAddUpdate("%s", "%s", "%s", "%s", "%s", "%s")',
					   $obj->hostname, $obj->model, $obj->sku, $obj->uuic,
					   $obj->serial, $obj->notes);

		$result = $this->query($sql);

		$r = ($result[0]['affected'] === 1) ? 'added new' : 'updated existing';

		if ($result[0]['affected'] > 0) {
			return array('success'=>'Successfully '.$r.' record');
		}
		return array('error'=>'Whoops, an error occured while adding new record');
	}

	public function deleteComputer($obj)
	{
var_dump($obj);
		$sql = sprintf('CALL ComputerDelete("%d")');
	}

	public function searchComputer($id)
	{
		$sql = sprintf('CALL ComputerSearch("%s")', $id);
		return $this->query($sql);
	}

	public function viewMonitor()
	{
		$sql = sprintf('CALL MonitorList()');
		return $this->query($sql);
	}

	public function searchMonitor($id)
	{
		$sql = sprintf('CALL MonitorSearch("%s")', $id);
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

		if ($query->count() > 0) {
			$result = new ResultSet();
			return $result->initialize($query)->toArray();
		}
		return array('error' => 'Whoopsie! No records found, perhaps a wildcard search may help? (ex. computer-name*)');
	}

	function __destruct()
	{
		//unset($this->dbconn);
	}
}

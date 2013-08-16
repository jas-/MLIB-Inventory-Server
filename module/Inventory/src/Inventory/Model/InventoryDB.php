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
		return array('error'=>'Whoops, an error occured while adding new computerrecord');
	}

	public function updateComputer($id, $obj)
	{
		$sql = sprintf('CALL ComputerUpdate("%d", "%s", "%s", "%s", "%s", "%s", "%s")',
					   $id, $obj['hostname'], $obj['model'], $obj['sku'], $obj['uuic'],
					   $obj['serial'], $obj['notes']);
		$result = $this->query($sql);

		if ($result[0]['affected'] > 0) {
			return array('success'=>'Successfully updated record');
		}
		return array('error'=>'Whoops, an error occured while updating computer record');
	}

	public function deleteComputer($id)
	{
		$sql = sprintf('CALL ComputerDelete("%d")', $id);
		$result = $this->query($sql);

		if ($result[0]['affected'] > 0) {
			return array('success'=>'Successfully deleted record');
		}
		return array('error'=>'Whoops, an error occured while deleting computer record');
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

	public function addMonitor($obj)
	{
		$sql = sprintf('CALL MonitorAddUpdate("%s", "%s", "%s", "%s")',
					   $obj->hostname, $obj->model, $obj->sku, $obj->serial);

		$result = $this->query($sql);

		$r = ($result[0]['affected'] === 1) ? 'added new' : 'updated existing';

		if ($result[0]['affected'] > 0) {
			return array('success'=>'Successfully '.$r.' record');
		}
		return array('error'=>'Whoops, an error occured while adding new monitor record');
	}

	public function updateMonitor($id, $obj)
	{
		$sql = sprintf('CALL MonitorUpdate("%d", "%s", "%s", "%s", "%s")',
					   $id, $obj['hostname'], $obj['model'], $obj['sku'], $obj['serial']);
		$result = $this->query($sql);

		if ($result[0]['affected'] > 0) {
			return array('success'=>'Successfully updated record');
		}
		return array('error'=>'Whoops, an error occured while updating monitor record');
	}

	public function searchMonitor($id)
	{
		$sql = sprintf('CALL MonitorSearch("%s")', $id);
		return $this->query($sql);
	}

	public function deleteMonitor($id)
	{
		$sql = sprintf('CALL MonitorDelete("%d")', $id);
		$result = $this->query($sql);

		if ($result[0]['affected'] > 0) {
			return array('success'=>'Successfully deleted record');
		}
		return array('error'=>'Whoops, an error occured while deleting monitor record');
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
		unset($this->dbconn);
	}
}

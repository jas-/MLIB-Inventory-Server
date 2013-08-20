<?php

namespace Inventory\Model\Db;

class ComputerDB extends AbstractDB
{
	protected $dbconn;

	public function __construct($key, $svc)
	{
		$this->dbconn = parent::__construct($key, $svc);
	}

	public function view()
	{
		$sql = sprintf('CALL ComputerList()');
		return parent::query($sql);
	}

	public function add($obj)
	{
		$sql = sprintf('CALL ComputerAddUpdate("%s", "%s", "%s", "%s", "%s", "%s")',
					   $obj['hostname'], $obj['model'], $obj['sku'], $obj['uuic'],
					   $obj['serial'], $obj['notes']);

		$result = parent::query($sql);

		$r = ((int)$result[0]['affected'] === 1) ? 'added new' : 'updated existing';

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully '.$r.' record');
		}
		return array('error'=>'Whoops, an error occured while adding new computerrecord');
	}

	public function update($id, $obj)
	{
		$sql = sprintf('CALL ComputerUpdate("%d", "%s", "%s", "%s", "%s", "%s", "%s")',
					   $id, $obj['hostname'], $obj['model'], $obj['sku'], $obj['uuic'],
					   $obj['serial'], $obj['notes']);
		$result = parent::query($sql);

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully updated record');
		}

		if ((int)$result[0]['affected'] == 0) {
			return array('warning'=>'No changes to computer record occured');
		}
		return array('error'=>'Whoops, an error occured while updating computer record');
	}

	public function search($id)
	{
		$sql = sprintf('CALL ComputerSearch("%s")', $id);
		return parent::query($sql);
	}

	public function delete($id)
	{
		$sql = sprintf('CALL ComputerDelete("%d")', $id);
		$result = parent::query($sql);

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully deleted record');
		}
		return array('error'=>'Whoops, an error occured while deleting computer record');
	}
}

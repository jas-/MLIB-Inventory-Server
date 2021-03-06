<?php

namespace Inventory\Model\Db;

class WarrantyDB extends AbstractDB
{
	protected $dbconn;

	public function __construct($key, $svc)
	{
		$this->dbconn = parent::__construct($key, $svc);
	}

	public function view()
	{
		$sql = sprintf('CALL WarrantyList()');
		return parent::query($sql);
	}

	public function add($obj)
	{
		$sql = sprintf('CALL WarrantyAddUpdate("%s", "%s")', $obj['eowd'], $obj['opd']);
		$result = parent::query($sql);

		$r = ((int)$result[0]['affected'] === 1) ? 'added new' : 'updated existing';

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully '.$r.' record');
		}
		return array('error'=>'Whoops, an error occured while adding new warranty record');
	}

	public function update($id, $obj)
	{
		$sql = sprintf('CALL WarrantyUpdate("%d", "%s", "%s")',
					   $id, $obj['eowd'], $obj['opd']);

		$result = parent::query($sql);

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully updated record');
		}

		if ((int)$result[0]['affected'] == 0) {
			return array('warning'=>'No changes to RMA record occured');
		}

		return array('error'=>'Whoops, an error occured while updating warranty record');
	}

	public function search($id)
	{
		$sql = sprintf('CALL WarrantySearch("%s")', $id);
		return parent::query($sql);
	}

	public function delete($id)
	{
		$sql = sprintf('CALL WarrantyDelete("%d")', $id);
		$result = parent::query($sql);

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully deleted record');
		}
		return array('error'=>'Whoops, an error occured while deleting warranty record');
	}
}

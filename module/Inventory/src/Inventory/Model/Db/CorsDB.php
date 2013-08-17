<?php

namespace Inventory\Model\Db;

class CorsDB extends AbstractDB
{
	protected $dbconn;

	public function __construct($key, $svc)
	{
		$this->dbconn = parent::__construct($key, $svc);
	}

	public function view()
	{
		$sql = sprintf('CALL CorsList()');
		return parent::query($sql);
	}

	public function add($obj)
	{
		$sql = sprintf('CALL CorsAddUpdate("%s", "%s", "%s")',
					   $obj['application'], $obj['url'], $obj['ip']);

		$result = parent::query($sql);

		$r = ((int)$result[0]['affected'] === 1) ? 'added new' : 'updated existing';

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully '.$r.' record');
		}
		return array('error'=>'Whoops, an error occured while adding new cors record');
	}

	public function update($id, $obj)
	{
		$sql = sprintf('CALL CorsUpdate("%d", "%s", "%s", "%s")',
					   $id, $obj['application'], $obj['url'], $obj['ip']);
		$result = parent::query($sql);

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully updated record');
		}

		if ((int)$result[0]['affected'] == 0) {
			return array('warning'=>'No changes to cors record occured');
		}
		return array('error'=>'Whoops, an error occured while updating cors record');
	}

	public function search($id)
	{
		$sql = sprintf('CALL CorsSearch("%s")', $id);
		return parent::query($sql);
	}

	public function delete($id)
	{
		$sql = sprintf('CALL CorsDelete("%d")', $id);
		$result = parent::query($sql);

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully deleted record');
		}
		return array('error'=>'Whoops, an error occured while deleting cors record');
	}
}

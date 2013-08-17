<?php

namespace Inventory\Model\Db;

class ModelDB extends AbstractDB
{
	protected $dbconn;

	public function __construct($key, $svc)
	{
		$this->dbconn = parent::__construct($key, $svc);
	}

	public function view()
	{
		$sql = sprintf('CALL ModelList(0)');
		return parent::query($sql);
	}

	public function add($obj)
	{
		$sql = sprintf('CALL ModelAddUpdate("%s", "%s", "%s", "%s", "%s")',
					   $obj['model'], $obj['eowd'], $obj['opd'], $obj['description'],
					   $obj['notes']);

		$result = parent::query($sql);

		$r = ((int)$result[0]['affected'] === 1) ? 'added new' : 'updated existing';

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully '.$r.' record');
		}
		return array('error'=>'Whoops, an error occured while adding new model record');
	}

	public function update($id, $obj)
	{
		$sql = sprintf('CALL ModelUpdate("%d", "%s", "%s", "%s", "%s", "%s")',
					   $id, $obj['model'], $obj['eowd'], $obj['opd'], $obj['description'],
					   $obj['notes']);
		$result = parent::query($sql);

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully updated record');
		}
		return array('error'=>'Whoops, an error occured while updating model record');
	}

	public function search($id)
	{
		$sql = sprintf('CALL ModelSearch("%s")', $id);
		return parent::query($sql);
	}

	public function delete($id)
	{
		$sql = sprintf('CALL ModelDelete("%d")', $id);
		$result = parent::query($sql);

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully deleted record');
		}
		return array('error'=>'Whoops, an error occured while deleting model record');
	}
}

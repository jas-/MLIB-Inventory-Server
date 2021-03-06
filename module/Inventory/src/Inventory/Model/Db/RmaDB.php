<?php

namespace Inventory\Model\Db;

class RmaDB extends AbstractDB
{
	protected $dbconn;

	public function __construct($key, $svc)
	{
		$this->dbconn = parent::__construct($key, $svc);
	}

	public function view()
	{
		$sql = sprintf('CALL RmaList()');
		return parent::query($sql);
	}

	public function add($obj)
	{
		$sql = sprintf('CALL RmaAddUpdate("%s", "%s", "%s", "%s","%s", "%s", "%s", "%s", "%s")',
					   $obj['date'], $obj['hostname'], $obj['sku'], $obj['uuic'], $obj['serial'],
					   $obj['model'], $obj['eowd'], $obj['part'], $obj['notes']);

		$result = parent::query($sql);

		$r = ((int)$result[0]['affected'] === 1) ? 'added new' : 'updated existing';

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully '.$r.' record');
		}
		return array('error'=>'Whoops, an error occured while adding new RMA record');
	}

	public function update($id, $obj)
	{
		$sql = sprintf('CALL RmaUpdate("%d", "%s", "%s", "%s", "%s","%s", "%s", "%s", "%s", "%s")',
					   $id, $obj['date'], $obj['hostname'], $obj['sku'], $obj['uuic'], $obj['serial'],
					   $obj['model'], $obj['eowd'], $obj['part'], $obj['notes']);

		$result = parent::query($sql);

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully updated record');
		}

		if ((int)$result[0]['affected'] == 0) {
			return array('warning'=>'No changes to RMA record occured');
		}

		return array('error'=>'Whoops, an error occured while updating RMA record');
	}

	public function search($id)
	{
		$sql = sprintf('CALL RmaSearch("%s")', $id);
		return parent::query($sql);
	}

	public function delete($id)
	{
		$sql = sprintf('CALL RmaDelete("%d")', $id);
		$result = parent::query($sql);

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully deleted record');
		}
		return array('error'=>'Whoops, an error occured while deleting RMA record');
	}
}

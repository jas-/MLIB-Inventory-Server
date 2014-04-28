<?php

namespace Inventory\Model\Db;

class MonitorDB extends AbstractDB
{
	protected $dbconn;

	public function __construct($key, $svc)
	{
		$this->dbconn = parent::__construct($key, $svc);
	}

	public function view()
	{
		$sql = sprintf('CALL MonitorList()');
		return parent::query($sql);
	}

	public function add($obj)
	{
		$sql = sprintf('CALL MonitorAddUpdate("%s", "%s", "%s", "%s", "%s", "%s", "%s")',
					   $obj['hostname'], $obj['model'], $obj['sku'], $obj['serial'],
             $obj['eowd'], $obj['opd'], $obj['notes']);

		$result = parent::query($sql);

		$r = ((int)$result[0]['affected'] === 1) ? 'added new' : 'updated existing';

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully '.$r.' record');
		}
		return array('error'=>'Whoops, an error occured while adding new monitor record');
	}

	public function update($id, $obj)
	{
		$sql = sprintf('CALL MonitorUpdate("%d", "%s", "%s", "%s", "%s", "%s", "%s", "%s")',
					   $id, $obj['hostname'], $obj['model'], $obj['sku'], $obj['serial'],
             $obj['eowd'], $obj['opd'], $obj['notes']);
echo $sql;
		$result = parent::query($sql);
print_r($result);
		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully updated record');
		}

		if ((int)$result[0]['affected'] == 0) {
			return array('warning'=>'No changes to monitor record occured');
		}
		return array('error'=>'Whoops, an error occured while updating monitor record');
	}

	public function search($id)
	{
		$sql = sprintf('CALL MonitorSearch("%s")', $id);
		return parent::query($sql);
	}

	public function delete($id)
	{
		$sql = sprintf('CALL MonitorDelete("%d")', $id);
		$result = parent::query($sql);

		if ((int)$result[0]['affected'] > 0) {
			return array('success'=>'Successfully deleted record');
		}
		return array('error'=>'Whoops, an error occured while deleting monitor record');
	}
}

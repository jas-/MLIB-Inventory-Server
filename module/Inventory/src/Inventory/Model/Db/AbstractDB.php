<?php

namespace Inventory\Model\Db;

use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\View\Model\JsonModel;

abstract class AbstractDB
{
	protected $dbconn, $svc;

	public function __construct($key, $svc)
	{
		$this->svc = $svc;
		$this->dbconn = ($key == 'RO') ? $this->ro() : $this->rw();
		return $this->dbconn;
	}

    public function rw()
    {
        return $this->svc->get('Database_RW');
    }

    public function ro()
    {
        return $this->svc->get('Database_RO');
    }

	public function query($sql)
	{
		$query = $this->dbconn->createStatement($sql)->execute();

		if ($query->count() > 0) {
			$result = new ResultSet();
			return $result->initialize($query)->toArray();
		}
		return array('error' => 'Whoopsie! No records found, perhaps a wildcard search may help? (ex. computer-name*)');
	}

	public abstract function view();
	public abstract function add($obj);
	public abstract function update($id, $obj);
	public abstract function search($search);
	public abstract function delete($id);

    public function response($obj)
    {
        return new JsonModel($obj);
    }

    public function wildcard($str)
    {
        return preg_replace('/\*/', '%', $str);
    }

}

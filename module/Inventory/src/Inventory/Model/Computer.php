<?php

namespace Inventory\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Validator;

use Inventory\Model\Validate\Hostname;
use Inventory\Model\Validate\Model;
use Inventory\Model\Validate\SKU;
use Inventory\Model\Validate\UUIC;
use Inventory\Model\Validate\Serial;

class Computer
{
    public $id, $hostname, $model, $sku, $uuic, $serial, $notes, $data;
    protected $chain;

	function __construct($data)
	{
		$this->exchangeArray($data);
	}

    public function exchangeArray($data)
    {
        $this->id = (isset($data->id)) ? $data->id : null;
        $this->hostname = (isset($data->hostname)) ? $data->hostname : null;
        $this->model = (isset($data->model))  ? $data->model : null;
        $this->sku = (isset($data->sku)) ? $data->sku : null;
        $this->uuic = (isset($data->uuic)) ? $data->uuic : null;
        $this->serial = (isset($data->serial))  ? $data->serial : null;
        $this->notes = (isset($data->notes)) ? $data->notes : null;
    }

	public function isValid()
    {
		if (!Hostname::isValid($this->hostname))
			return false;

		if (!Model::isValid($this->model))
			return false;

		if (!SKU::isValid($this->sku))
			return false;

		if (!UUIC::isValid($this->uuic))
			return false;

		if (!Serial::isValid($this->serial))
			return false;

		return true;
    }
}
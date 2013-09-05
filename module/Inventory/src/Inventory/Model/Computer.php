<?php

namespace Inventory\Model;

use Inventory\Model\Validate\Hostname;
use Inventory\Model\Validate\Model;
use Inventory\Model\Validate\SKU;
use Inventory\Model\Validate\UUIC;
use Inventory\Model\Validate\Serial;
use Inventory\Model\Sanitize\StripTags;

class Computer
{
    public $id, $hostname, $model, $sku, $uuic, $serial, $notes, $data;

	function __construct($data)
	{
		$this->exchangeArray($data);
	}

    public function exchangeArray($data)
    {
        $this->id = (isset($data->Id)) ? $data->Id : null;
        $this->hostname = (isset($data->Hostname)) ? $data->Hostname : (isset($data['Hostname'])) ? $data['Hostname'] : null;
        $this->model = (isset($data->Model))  ? $data->Model : (isset($data['Model'])) ? $data['Model'] : null;
        $this->sku = (isset($data->SKU)) ? $data->SKU : (isset($data['SKU'])) ? $data['SKU'] : null;
        $this->uuic = (isset($data->UUIC)) ? $data->UUIC : (isset($data['UUIC'])) ? $data['UUIC'] : null;
        $this->serial = (isset($data->Serial))  ? $data->Serial : (isset($data['Serial'])) ? $data['Serial'] : null;
        $this->notes = (isset($data->Notes)) ? $data->Notes : (isset($data['Notes'])) ? $data['Notes'] : null;
    }

	public function isValid()
    {
		if (!Hostname::isValid($this->hostname)) {
			return false;
		}

		if (!Model::isValid($this->model)) {
			return false;
		}

		if (!SKU::isValid($this->sku)) {
			return false;
		}

		if (!UUIC::isValid($this->uuic)) {
			return false;
		}

		if (!Serial::isValid($this->serial)) {
			return false;
		}

		return true;
    }

	public function doClean($str)
	{
		$clean = new StripTags;
		return $clean->doClean($str);
	}
}
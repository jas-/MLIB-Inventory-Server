<?php

namespace Inventory\Model;

use Inventory\Model\Validate\Hostnames;
use Inventory\Model\Validate\Model;
use Inventory\Model\Validate\SKU;
use Inventory\Model\Validate\UUIC;
use Inventory\Model\Validate\Serial;
use Inventory\Model\Sanitize\StripTags;

class Monitor
{
    public $id, $hostname, $model, $sku, $serial, $notes, $data;

	function __construct($data)
	{
		$this->exchangeArray(array_change_key_case((array)$data, CASE_LOWER));
	}

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->hostname = (isset($data['hostname'])) ? $data['hostname'] : null;
        $this->model = (isset($data['model'])) ? $data['model'] : null;
        $this->sku = (isset($data['sku'])) ? $data['sku'] : null;
        $this->serial = (isset($data['serial'])) ? $data['serial'] : null;
        $this->eowd = (isset($data['eowd'])) ? $data['eowd'] : null;
        $this->opd = (isset($data['opd'])) ? $data['opd'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
		$this->notes = (isset($data['notes'])) ? $data['notes'] : null;
    }

	public function isValid()
    {
		if (!Hostnames::isValid($this->hostname)) {
			return false;
		}

		if (!Model::isValid($this->model)) {
			return false;
		}

		if (!SKU::isValid($this->sku)) {
			return false;
		}

		if (!Serial::isValid($this->serial)) {
			return false;
		}

		if (!Date::isValid($this->eowd)) {
			return false;
		}

		if (!Date::isValid($this->opd)) {
			return false;
		}

		if (!empty($this->description)) {
			if (!Paragraph::isValid($this->description)) {
				return false;
			}
		}

		if (!empty($this->notes)) {
			if (!Paragraph::isValid($this->notes)) {
				return false;
			}
		}

		return true;
    }

	public function doClean($str)
	{
		$clean = new StripTags;
		return $clean->doClean($str);
	}
}
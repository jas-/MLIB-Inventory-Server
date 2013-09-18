<?php

namespace Inventory\Model;

use Inventory\Model\Validate\Date;
use Inventory\Model\Validate\Hostnames;
use Inventory\Model\Validate\Model;
use Inventory\Model\Validate\SKU;
use Inventory\Model\Validate\UUIC;
use Inventory\Model\Validate\Serial;
use Inventory\Model\Validate\Paragraph;
use Inventory\Model\Sanitize\StripTags;

class Rmas
{
    public $id, $hostname, $model, $sku, $serial, $notes, $data;

	function __construct($data)
	{
		$this->exchangeArray(array_change_key_case((array)$data, CASE_LOWER));
	}

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->date = (isset($data['date'])) ? $data['date'] : null;
        $this->hostname = (isset($data['hostname'])) ? $data['hostname'] : null;
        $this->sku = (isset($data['sku'])) ? $data['sku'] : null;
        $this->uuic = (isset($data['uuic'])) ? $data['uuic'] : null;
        $this->serial = (isset($data['serial'])) ? $data['serial'] : null;
		$this->model = (isset($data['model'])) ? $data['model'] : null;
		$this->part = (isset($data['part'])) ? $data['part'] : null;
		$this->notes = (isset($data['notes'])) ? $data['notes'] : null;
    }

	public function isValid()
    {
		if (!Date::isValid($this->date)) {
			return false;
		}

		if (!Hostnames::isValid($this->hostname)) {
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

		if (!Model::isValid($this->model)) {
			return false;
		}

		if (!Paragraph::isValid($this->part)) {
			return false;
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
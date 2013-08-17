<?php

namespace Inventory\Model;

use Inventory\Model\Validate\Date;
use Inventory\Model\Validate\Model;
use Inventory\Model\Validate\Paragraph;
use Inventory\Model\Sanitize\StripTags;

class Models
{
    public $id, $hostname, $model, $sku, $serial, $notes, $data;

	function __construct($data)
	{
		$this->exchangeArray($data);
	}

    public function exchangeArray($data)
    {
        $this->id = (isset($data->id)) ? $data->id : null;
        $this->model = (isset($data->model)) ? $data->model : (isset($data['model'])) ? $data['model'] : null;
        $this->eowd = (isset($data->eowd))  ? $data->eowd : (isset($data['eowd'])) ? $data['eowd'] : null;
        $this->opd = (isset($data->opd)) ? $data->opd : (isset($data['opd'])) ? $data['opd'] : null;
        $this->description = (isset($data->description))  ? $data->description : (isset($data['description'])) ? $data['description'] : null;
		$this->notes = (isset($data->notes))  ? $data->notes : (isset($data['notes'])) ? $data['notes'] : null;
    }

	public function isValid()
    {
		if (!Model::isValid($this->model)) {
			return false;
		}

		if (!Date::isValid($this->eowd)) {
			return false;
		}

		if (!Date::isValid($this->opd)) {
			return false;
		}

		if (!Paragraph::isValid($this->description)) {
			return false;
		}

		if (!Paragraph::isValid($this->notes)) {
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
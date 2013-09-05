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
        $this->id = (isset($data->Id)) ? $data->Id : null;
        $this->model = (isset($data->Model)) ? $data->Model : (isset($data['Model'])) ? $data['Model'] : null;
        $this->eowd = (isset($data->EOWD))  ? $data->EOWD : (isset($data['EOWD'])) ? $data['EOWD'] : null;
        $this->opd = (isset($data->OPD)) ? $data->OPD : (isset($data['OPD'])) ? $data['OPD'] : null;
        $this->description = (isset($data->Description))  ? $data->Description : (isset($data['Description'])) ? $data['Description'] : null;
		$this->notes = (isset($data->Notes))  ? $data->Notes : (isset($data['Notes'])) ? $data['Notes'] : null;
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
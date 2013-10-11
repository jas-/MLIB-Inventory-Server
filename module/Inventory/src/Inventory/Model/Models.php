<?php

namespace Inventory\Model;

use Inventory\Model\Validate\Date;
use Inventory\Model\Validate\Model;
use Inventory\Model\Validate\Paragraph;
use Inventory\Model\Sanitize\StripTags;

class Models
{
    public $id, $hostname, $model, $sku, $serial, $notes, $data;
    protected $errors;

	function __construct($data)
	{
		$this->exchangeArray(array_change_key_case((array)$data, CASE_LOWER));
	}

    public function exchangeArray($data)
    {
		$data['model'] = (isset($data['modelname'])) ? $data['modelname'] : $data['model'];

		$this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->model = (isset($data['model'])) ? $data['model'] : null;
		$this->eowd = (isset($data['eowd'])) ? $data['eowd'] : null;
        $this->opd = (isset($data['opd'])) ? $data['opd'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
		$this->notes = (isset($data['notes'])) ? $data['notes'] : null;
    }

	public function isValid()
    {
		if (!Model::isValid($this->model)) {
            $this->errors['model'] = 'Model value is invalid';
			return false;
		}

		if (!Date::isValid($this->eowd)) {
            $this->errors['eowd'] = 'EOWD value is invalid';
			return false;
		}

		if (!Date::isValid($this->opd)) {
            $this->errors['opd'] = 'OPD value is invalid';
			return false;
		}

		if (!empty($this->description)) {
			if (!Paragraph::isValid($this->description)) {
                $this->errors['description'] = 'Description value is invalid';
				return false;
			}
		}

		if (!empty($this->notes)) {
			if (!Paragraph::isValid($this->notes)) {
                $this->errors['notes'] = 'Notes value is invalid';
				return false;
			}
		}

		return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }

	public function doClean($str)
	{
		$clean = new StripTags;
		return $clean->doClean($str);
	}
}

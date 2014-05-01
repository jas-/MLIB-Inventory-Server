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
    $this->description = (isset($data['description'])) ? $data['description'] : null;
    }

	public function isValid()
    {
    $ret = true;

		if (!Model::isValid($this->model)) {
            $this->errors['model'] = 'Model value is invalid';
			$ret = false;
		}

		if (!empty($this->description)) {
			if (!Paragraph::isValid($this->description)) {
                $this->errors['description'] = 'Description value is invalid';
			$ret = false;
			}
		}

		return $ret;
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

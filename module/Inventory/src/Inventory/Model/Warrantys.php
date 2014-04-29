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

class Warrantys
{
    public $id, $hostname, $model, $sku, $serial, $notes, $data;
    protected $errors;

	function __construct($data)
	{
		$this->exchangeArray(array_change_key_case((array)$data, CASE_LOWER));
	}

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->eowd = (isset($data['eowd'])) ? $data['eowd'] : null;
        $this->opd = (isset($data['opd'])) ? $data['opd'] : null;
    }

	public function isValid()
    {
    $ret = true;

    if (!empty($this->eowd)){
  		if (!Date::isValid($this->eowd)) {
              $this->errors['eowd'] = 'Date value is invalid (YYYY-MM-DD)';
  			$ret = false;
  		}
    }

    if (!empty($this->opd)){
  		if (!Date::isValid($this->opd)) {
              $this->errors['opd'] = 'Date value is invalid (YYYY-MM-DD)';
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

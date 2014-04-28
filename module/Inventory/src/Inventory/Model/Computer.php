<?php

namespace Inventory\Model;

use Inventory\Model\Validate\Hostnames;
use Inventory\Model\Validate\Model;
use Inventory\Model\Validate\SKU;
use Inventory\Model\Validate\UUIC;
use Inventory\Model\Validate\Serial;
use Inventory\Model\Validate\Date;
use Inventory\Model\Validate\Paragraph;
use Inventory\Model\Sanitize\StripTags;

class Computer
{
  public $id, $hostname, $model, $sku, $uuic, $serial, $notes, $data;
  protected $errors;

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
    $this->uuic = (isset($data['uuic'])) ? $data['uuic'] : null;
    $this->serial = (isset($data['serial'])) ? $data['serial'] : null;
    $this->eowd = (isset($data['eowd'])) ? $data['eowd'] : null;
    $this->opd = (isset($data['opd'])) ? $data['opd'] : null;
    $this->description = (isset($data['description'])) ? $data['description'] : null;
		$this->notes = (isset($data['notes'])) ? $data['notes'] : null;
  }

	public function isValid()
  {
    $ret = true;

    if (!empty($this->hostname)){
  		if (!Hostnames::isValid($this->hostname)) {
        $this->errors['hostname'] = 'Hostname value is invalid';
        $ret = false;
      }
    }

    if (!empty($this->model)){
  		if (!Model::isValid($this->model)) {
        $this->errors['model'] = 'Model value is invalid';
        $ret = false;
      }
    }

		if (!SKU::isValid($this->sku)) {
      $this->errors['sku'] = 'SKU value is invalid';
      $ret = false;
		}

    if (!empty($this->uuic)){
  		if (!UUIC::isValid($this->uuic)) {
        $this->errors['uuic'] = 'UUIC value is invalid';
  			$ret = false;
  		}
    }

		if (!Serial::isValid($this->serial)) {
            $this->errors['serial'] = 'Serial value is invalid';
			$ret = false;
		}

    if (!empty($this->eowd)){
  		if (!Date::isValid($this->eowd)) {
        $this->errors['eowd'] = 'EOWD value is invalid';
        $ret = false;
      }
    }

    if (!empty($this->opd)){
      if (!Date::isValid($this->opd)) {
        $this->errors['opd'] = 'OPD value is invalid';
        $ret = false;
      }
    }

		if (!empty($this->description)) {
			if (!Paragraph::isValid($this->description)) {
        $this->errors['description'] = 'Description value is invalid';
				$ret = false;
			}
		}

		if (!empty($this->notes)) {
			if (!Paragraph::isValid($this->notes)) {
        $this->errors['notes'] = 'Notes value is invalid';
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

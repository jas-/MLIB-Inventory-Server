<?php

namespace Inventory\Model;

use Inventory\Model\Validate\Hostname;
use Inventory\Model\Validate\Model;
use Inventory\Model\Validate\SKU;
use Inventory\Model\Validate\UUIC;
use Inventory\Model\Validate\Serial;

class Search
{
    public $id;

	function __construct($data)
	{
		$this->exchangeArray($data);
	}

    public function exchangeArray($data)
    {
        $this->id = (isset($data->id)) ? $data->id : null;
    }

	public function isValid()
    {
		if ((!Hostname::isValid($this->id)) &&
			(!Model::isValid($this->id)) &&
			(!SKU::isValid($this->id)) &&
			(!UUIC::isValid($this->id)) &&
			(!Serial::isValid($this->serial)))
			return false;

		return true;
    }
}

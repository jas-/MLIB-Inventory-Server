<?php

namespace Inventory\Model;

use Inventory\Model\Validate\DeleteID;

class Delete
{
    public $id;

	function __construct($data)
	{
		$this->exchangeArray($data);
	}

    public function exchangeArray($data)
    {
        $this->id = (isset($data)) ? $data : null;
    }

	public function isValid()
    {
		if (!DeleteID::isValid($this->id)) {
			return false;
		}

		return true;
    }
}

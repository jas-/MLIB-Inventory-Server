<?php

namespace Inventory\Model;

use Inventory\Model\Validate\SearchPattern;

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
		if (!SearchPattern::isValid($this->id))
			return false;

		return true;
    }
}

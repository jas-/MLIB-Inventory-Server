<?php

namespace Inventory\Model;

use Inventory\Model\Validate\SearchPattern;
use Inventory\Model\Sanitize\StripTags;

class Search
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
		if (!SearchPattern::isValid($this->id)) {
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

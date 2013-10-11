<?php

namespace Inventory\Model;

use Inventory\Model\Validate\SearchPattern;
use Inventory\Model\Sanitize\StripTags;

class Search
{
    public $id;
    protected $errors;

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
            $this->errors['id'] = 'ID value is invalid';
			return false;
		}

		return true;
    }

    public getErrors()
    {
        return $this->errors;
    }

	public function doClean($str)
	{
		$clean = new StripTags;
		return $clean->doClean($str);
	}
}

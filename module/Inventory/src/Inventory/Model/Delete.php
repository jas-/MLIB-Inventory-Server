<?php

namespace Inventory\Model;

use Inventory\Model\Validate\DeleteID;
use Inventory\Model\Sanitize\StripTags;

class Delete
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
    $ret = true;

		if (!DeleteID::isValid($this->id)) {
            $this->errors['id'] = 'ID value is invalid';
			$ret = false;
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

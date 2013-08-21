<?php

namespace Inventory\Model\Validate;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

class Url extends AbstractValidator
{
    public function isValid($value)
    {
        $val = new Regex(array('pattern'=>'/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'));

        if ((!$val->isValid($value)) || (!self::filter($value))) {
			return false;
        }

        return true;
    }

	private function filter($value)
	{
		return filter_var($value, FILTER_VALIDATE_URL);
	}
}
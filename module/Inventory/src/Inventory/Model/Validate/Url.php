<?php

namespace Inventory\Model\Validate;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

class Hostname extends AbstractValidator
{
    public function isValid($value)
    {
        $val = new Regex(array('pattern'=>'/\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))/i'));

        if ((!$val->isValid($value)) || (!$this->filter($value))) {
			return false;
        }

        return true;
    }

	private function filter($value)
	{
		return filter_var($value, FILTER_VALIDATE_URL);
	}
}
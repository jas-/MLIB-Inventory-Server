<?php

namespace Inventory\Model\Validate;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

class Serial extends AbstractValidator
{
    public function isValid($value)
    {
		$val = new Regex(array('pattern'=>'/^[\w+_-]{3,128}$/i'));
		if (!$val->isValid($value)) {
			return false;
		}

        return true;
    }
}

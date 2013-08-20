<?php

namespace Inventory\Model\Validate;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

class DeleteID extends AbstractValidator
{
    public function isValid($value)
    {
		$val = new Regex(array('pattern'=>'/^[\d+]{1,100}$/i'));
		if (!$val->isValid($value)) {
			return false;
		}

        return true;
    }
}
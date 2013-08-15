<?php

namespace Inventory\Model\Validate;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

class UUIC extends AbstractValidator
{
    public function isValid($value)
    {
		$val = new Regex(array('pattern'=>'/^[\w+\-]{3,15}$/i'));
		if (!$val->isValid($value)) {
			return false;
		}

        return true;
    }
}
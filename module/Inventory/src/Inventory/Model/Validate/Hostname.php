<?php

namespace Inventory\Model\Validate;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

class Hostname extends AbstractValidator
{
    public function isValid($value)
    {
		if (strlen($value) < 4)
			return false;

		if (strlen($value) > 64)
			return false;

        $val = new Regex(array('pattern'=>'/^(([a-zA-Z]|[a-zA-Z][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$/Di'));
        if (!$val->isValid($value))
			return false;

        return true;
    }
}
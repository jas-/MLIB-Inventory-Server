<?php

namespace Inventory\Model\Validate;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

class Wildcard extends AbstractValidator
{
    public function isValid($value)
    {
        $val = new Regex(array('pattern'=>'/[*]/i'));
        if (!$val->isValid($value))
			return false;

        return true;
    }
}
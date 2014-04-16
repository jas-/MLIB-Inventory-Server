<?php

namespace Inventory\Model\Validate;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

class Date extends AbstractValidator
{
    public function isValid($value)
    {
        $val = new Regex(array('pattern'=>'/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/i'));
        if (!$val->isValid($value)) {
			return false;
        }

        return true;
    }
}

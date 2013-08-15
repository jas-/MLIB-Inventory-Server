<?php

namespace Inventory\Model\Validate;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

class SearchPattern extends AbstractValidator
{
    public function isValid($value)
    {
        $val = new Regex(array('pattern'=>'/[\s\S]*?/i'));
        if (!$val->isValid($value)) {
			return false;
        }

        return true;
    }
}
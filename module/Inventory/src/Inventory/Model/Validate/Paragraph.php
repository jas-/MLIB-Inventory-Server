<?php

namespace Inventory\Model\Validate;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

class Paragraph extends AbstractValidator
{
    public function isValid($value)
    {
        $val = new Regex(array('pattern'=>'/[a-z0-9-_ ]*?/i'));
        if (!$val->isValid($value)) {
			return false;
        }

        return true;
    }
}
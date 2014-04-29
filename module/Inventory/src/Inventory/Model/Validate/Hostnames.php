<?php

namespace Inventory\Model\Validate;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Hostname;
use Zend\Validator\Regex;

class Hostnames extends AbstractValidator
{
    public function isValid($value)
    {
		if (strlen($value) < 2) {
			return false;
		}

		if (strlen($value) > 64) {
			return false;
		}

        $val1 = new Regex(array('pattern'=>'/^(([a-zA-Z]|[a-zA-Z][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$/Di'));
		$val2 = new Hostname(Hostname::ALLOW_IP || ALLOW_LOCAL);

        if ((!$val1->isValid($value)) && (!$val2->isValid($value))) {
			return false;
        }

        return true;
    }
}

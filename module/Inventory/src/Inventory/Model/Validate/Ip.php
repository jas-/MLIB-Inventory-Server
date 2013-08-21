<?php

namespace Inventory\Model\Validate;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

class Ip extends AbstractValidator
{
    public function isValid($value)
    {
        $ipv4 = new Regex(array('pattern'=>'/((2[0-4]|1\d|[1-9])?\d|25[0-5])(\.(?1)){3}\z/Di'));
		$ipv6 = new Regex(array('pattern'=>'/^(((?=(?>.*?(::))(?!.+\3)))\3?|([\dA-F]{1,4}(\3|:(?!$)|$)|\2))(?4){5}((?4){2}|((2[0-4]|1\d|[1-9])?\d|25[0-5])(\.(?7)){3})\z/Di'));

        if ((!$ipv4->isValid($value)) && (!$ipv6->isValid($value)) || (!self::inet($value)) || (!self::filter($value))) {
			return false;
        }

        return true;
    }

	private function inet($value)
	{
		return inet_pton($value);
	}

	private function filter($value)
	{
		return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
	}
}
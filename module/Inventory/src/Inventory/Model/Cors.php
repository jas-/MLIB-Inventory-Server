<?php

namespace Inventory\Model;

use Inventory\Model\Validate\Paragraph;
use Inventory\Model\Validate\Url;
use Inventory\Model\Validate\Ip;
use Inventory\Model\Sanitize\StripTags;

class Cors
{
    public $id, $hostname, $model, $sku, $uuic, $serial, $notes, $data;

	function __construct($data)
	{
		$this->exchangeArray($data);
	}

    public function exchangeArray($data)
    {
        $this->id = (isset($data->id)) ? $data->id : null;
        $this->application = (isset($data->application)) ? $data->application : (isset($data['application'])) ? $data['application'] : null;
        $this->url = (isset($data->url))  ? $data->url : (isset($data['url'])) ? $data['url'] : null;
        $this->ip = (isset($data->ip)) ? $data->ip : (isset($data['ip'])) ? $data['ip'] : null;
    }

	public function isValid()
    {
		if (!Paragraph::isValid($this->application)) {
			return false;
		}

		if (!Url::isValid($this->url)) {
			return false;
		}

		if (!Ip::isValid($this->ip)) {
			return false;
		}

		return true;
    }

	public function doClean($str)
	{
		$clean = new StripTags;
		return $clean->doClean($str);
	}
}
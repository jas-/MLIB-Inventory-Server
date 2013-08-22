<?php

namespace Inventory\Model;

use Zend\Http\Response;
use Zend\Http\Headers;
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

	public function doResponse($request, $db)
	{
		$http = new Headers();
		$response = new Response();

		if (!$http->has('origin')) {
			$response->setContent('Required "Origin" header value not found');
			$response->setStatusCode(Response::STATUS_CODE_405);
			return false;
		}

		$validated = $this->valRequest($request);

		if (!$this->valRequest($http->get('origin'))) {
			$response->setContent('"Origin" value not on approved whitelist of referring applications');
			$response->setStatusCode(Response::STATUS_CODE_405);
			return false;
		}

		$response->setStatusCode(Response::STATUS_CODE_200);
		$response->getHeaders()->addHeaders(array(
			'Access-Control-Allow-Origin' => $validated,
			'Access-Control-Allow-Methods' => 'ORIGIN, GET, PUT, POST, DELETE',
			'Access-Control-Allow-Headers' => 'Content-MD5, X-Alt-Referer, X-Requested-With',
			'Access-Control-Allow-Credentials' => true,
			'Content-Type' => 'application/json',
		));
		return true;
	}

	private function valRequest($request)
	{
		print_r($request);
		return true;
	}
}
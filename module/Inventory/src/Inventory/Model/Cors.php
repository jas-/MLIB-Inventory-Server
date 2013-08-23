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

    protected $allowedCollectionMethods = array(
        'OPTIONS',
        'GET',
        'PUT',
        'POST',
        'DELETE',
    );

    protected $allowedResourceMethods = array(
        'GET',
        'POST',
        'PUT',
        'DELETE',
    );

    protected $allowedRequestHeaders = array(
        'accept',
        'origin',
        'content-md5',
        'content-type',
        'x-requested-with',
        'x-alt-referer',
    );

	function __construct($data = false)
	{
		if (isset($data))
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

    public function injectLinkHeader($e)
    {
        $response = $e->getResponse();
        $headers  = $response->getHeaders();
        $headers->addHeaderLine('Link', sprintf(
            '<%s>; rel="describedby"',
            'http://inventory.dev:8080'
            //$this->url('documentation-route-name')
        ));
    }

	public function doResponse($e)
	{
        $matches  = $e->getRouteMatch();
        $response = $e->getResponse();
        $request  = $e->getRequest();
        $method   = $request->getMethod();
        $headers  = $response->getHeaders();

/*
        if ($matches->getParam('id', false)) {
            if (!in_array($method, $this->allowedResourceMethods)) {
                $response->setStatusCode(405);
                return $response;
            }
            return;
        }
*/

        if (!in_array($method, $this->allowedCollectionMethods)) {
            $response->setStatusCode(405);
            return $response;
        }

        $headers->addHeaderLine('Access-Control-Allow-Origin', 'http://grid-dev.dev:8080');
        $headers->addHeaderLine('Access-Control-Allow-Methods', implode(
            ',',
            $this->allowedResourceMethods
        ));
        $headers->addHeaderLine('Access-Control-Allow-Credentials', 'true');
        $headers->addHeaderLine('Access-Control-Allow-Headers', implode(
            ',',
            $this->allowedRequestHeaders
        ));

		$this->injectLinkHeader($e);

        (strcasecmp($request->getMethod(), 'options') == 0) ?
            $response->setStatusCode(204) : $response->setStatusCode(200);

        return;
	}
}
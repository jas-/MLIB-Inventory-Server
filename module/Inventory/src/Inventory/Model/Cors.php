<?php

namespace Inventory\Model;

use Zend\Http\Response;
use Zend\Http\Headers;

use Inventory\Model\Db\CorsDB;
use Inventory\Model\Validate\Paragraph;
use Inventory\Model\Validate\Url;
use Inventory\Model\Validate\Ip;
use Inventory\Model\Sanitize\StripTags;

class Cors
{
    public $id, $hostname, $model, $sku, $uuic, $serial, $notes, $data;
    protected $errors;

    protected $allowedMethods = array(
		'OPTIONS',
        'GET',
        'POST',
        'PUT',
        'DELETE',
    );

    protected $allowedHeaders = array(
        'accept',
        'origin',
        'content-md5',
        'content-type',
        'x-requested-with',
        'x-alt-referer',
    );

	protected $allowedURI = array();

	function __construct($data = false, $svc = false)
	{
		if (isset($data)) {
			$this->exchangeArray(array_change_key_case((array)$data, CASE_LOWER));
		}

		if (isset($svc)) {
			$db = new CorsDB('RO', $svc);
			$this->allowedURI = $this->extractURL($db->view());
		}
	}

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->application = (isset($data['application'])) ? $data['application'] : null;
        $this->url = (isset($data['url'])) ? $data['url'] : null;
        $this->ip = (isset($data['ip'])) ? $data['ip'] : null;
    }

	public function isValid()
    {
    $ret = true;

		if (!Paragraph::isValid($this->application)) {
            $this->errors['application'] = 'Application value is invalid';
			$ret = false;
		}

		if (!Url::isValid($this->url)) {
            $this->errors['url'] = 'URL value is invalid';
			$ret = false;
		}

		if (!Ip::isValid($this->ip)) {
            $this->errors['ip'] = 'IP value is invalid';
			$ret = false;
		}

		return $ret;
    }

    public function getErrors()
    {
        return $this->errors;
    }

	public function doClean($str)
	{
		$clean = new StripTags;
		return $clean->doClean($str);
	}

    public function injectLinkHeader($e)
    {
        $response = $e->getResponse();
		$request  = $e->getRequest();
        $headers  = $response->getHeaders();
        $headers->addHeaderLine('Link', sprintf(
            '<%s>; rel="describedby"',
            $request->getUri()
        ));
    }

	public function doResponse($e)
	{
        $matches  = $e->getRouteMatch();
        $response = $e->getResponse();
        $request  = $e->getRequest();
        $method   = $request->getMethod();
        $headers  = $response->getHeaders();

		$this->injectLinkHeader($e);

        if (!in_array($method, $this->allowedMethods)) {
            $response->setStatusCode(405);
            return $response;
        }

		$origin = $this->getOrigin($headers);

		if (!in_array($origin, $this->allowedURI)) {
			$response->setStatusCode(401);
			return $response;
		}

        $headers->addHeaderLine('Access-Control-Allow-Origin', $origin);
        $headers->addHeaderLine('Access-Control-Allow-Credentials', 'true');
        $headers->addHeaderLine('Access-Control-Allow-Methods', implode(
            ',',
            $this->allowedMethods
        ));
        $headers->addHeaderLine('Access-Control-Allow-Headers', implode(
            ',',
            $this->allowedHeaders
        ));

        (strcasecmp($request->getMethod(), 'options') == 0) ?
            $response->setStatusCode(204) : $response->setStatusCode(200);

        return;
	}

	private function getOrigin($headers)
	{
		$origin = false;

		if ($headers->get('origin')) {
			$origin = $headers->get('origin');
		}

		if (getenv('HTTP_ORIGIN')) {
			$origin = getenv('HTTP_ORIGIN');
		}
		return $origin;
	}

	private function extractURL($array)
	{
		if (is_array($array)) {
			foreach($array as $key => $value) {
        if (isset($value['URL'])){
          $array[$key] = $value['URL'];
        }
			}
		}
		return $array;
	}
}

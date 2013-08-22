<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Headers;
use Zend\Http\Response;

use Inventory\Model\Db\ComputerDB;
use Inventory\Model\Db\CorsDB;
use Inventory\Model\Computer;
use Inventory\Model\Cors;
use Inventory\Model\Search;
use Inventory\Model\Delete;

class ComputerController extends AbstractRestfulController
{
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

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $events->attach('dispatch', array($this, 'checkOptions'), 10);
        $events->attach('dispatch', array($this, 'injectLinkHeader'), 20);
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

    public function checkOptions($e)
    {
        $matches  = $e->getRouteMatch();
        $response = $e->getResponse();
        $request  = $e->getRequest();
        $method   = $request->getMethod();

        if ($matches->getParam('id', false)) {
            if (!in_array($method, $this->allowedResourceMethods)) {
                $response->setStatusCode(404);
                return $response;
            }
            return;
        }

        if (!in_array($method, $this->allowedCollectionMethods)) {
            $response->setStatusCode(405);
            return $response;
        }
        return;
    }

    public function options()
    {
        $response = $this->getResponse();
        $headers  = $response->getHeaders();

        if ($this->params()->fromRoute('id', false)) {
            $headers->addHeaderLine('Allow', implode(
                ',', 
                $this->allowedResourceMethods
            ));
        }

        $headers->addHeaderLine('Allow', implode(
            ',',
            $this->allowedCollectionMethods
        ));

        return true;
    }

    public function getList()
    {
        $db = new ComputerDB('RO', $this->getServiceLocator());
        return $db->response($db->view());
    }

    public function get($id)
    {
        $search = new Search($id);
        $db = new ComputerDB('RO', $this->getServiceLocator()); 

        if ($search->isValid()) {
            $id = $search->doClean($id);
            $id = $db->wildcard($id);

            return $db->response($db->search($id));
        } else {
            return $db->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $db->response(array('error'=>'Unable to search records with given parameters'));
    }

    public function create($data)
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $computer = new Computer($data);
            $post = $computer->doClean($data);

            $db = new ComputerDB('RW', $this->getServiceLocator());

            if ($computer->isValid()) {
                return $db->response($db->add($post));
            } else {
                return $db->response(array('error'=>'Given parameters did meet validation requirements'));
            }
        }

        return $db->response(array('error'=>'Could not save record'));
    }

    public function update($id, $data)
    {
        $computer = new Computer($data);
        $db = new ComputerDB('RW', $this->getServiceLocator());

        if ($computer->isValid()) {
            $id = $computer->doClean($id);
            $post = $computer->doClean($data);

            return $db->response($db->update($id, $post));
        } else {
            return $db->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $db->response(array('error'=>'Could not edit record'));
    }

    public function delete($id)
    {
        $computer = new Delete($id);
        $db = new ComputerDB('RW', $this->getServiceLocator());

        if ($computer->isValid()) {
            $post = $computer->doClean($id);

            return $db->response($db->delete($post));
        } else {
            return $db->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $db->response(array('error'=>'Unable delete specified record'));
    }
}

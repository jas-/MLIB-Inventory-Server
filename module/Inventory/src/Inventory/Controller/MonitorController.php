<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Inventory\Model\InventoryDB;
use Inventory\Model\Monitor;
use Inventory\Model\Search;

class MonitorController extends AbstractRestfulController
{
    protected $inventory;

    public function getList()
    {
        $db = new InventoryDB('RO', $this->getServiceLocator());
        return $this->response($db->viewMonitor());
    }

    public function get($id)
    {
        $search = new Search($id);

        if ($search->isValid()) {
            $id = $this->wildcard($id);

            $db = new InventoryDB('RO', $this->getServiceLocator());
            return $this->response($db->searchMonitor($id));

        } else {
            return $this->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $this->response(array('error'=>'Unable to search records with given parameters'));
    }

    public function create($data)
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $monitor = new Monitor($request->getPost());

            if ($monitor->isValid()) {
                $db = new InventoryDB('RW', $this->getServiceLocator());
                return $this->response($db->addMonitor($request->getPost()));
            } else {
                return $this->response(array('error'=>'Given parameters did meet validation requirements'));
            }
        }

        return $this->response(array('error'=>'Could not save record'));
    }

    public function update($id, $data)
    {
        $monitor = new Monitor($data);

        if ($monitor->isValid()) {
            $db = new InventoryDB('RW', $this->getServiceLocator());
            return $this->response($db->addMonitor($data));
        } else {
            return $this->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $this->response(array('error'=>'Could not edit record'));
    }

    public function delete($id)
    {
        $monitor = new Delete($id);

        if ($monitor->isValid()) {
            $db = new InventoryDB('RW', $this->getServiceLocator());
            return $this->response($db->deleteMonitor($id));
        } else {
            return $this->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $this->response(array('error'=>'Unable delete specified record'));
    }

    private function response($obj)
    {
        return new JsonModel($obj);
    }

    private function wildcard($str)
    {
        return preg_replace('/\*/', '%', $str);
    }
}
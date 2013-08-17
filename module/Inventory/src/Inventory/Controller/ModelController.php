<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Inventory\Model\ModelDB;
use Inventory\Model\Model;
use Inventory\Model\Search;
use Inventory\Model\Delete;

class ModelController extends AbstractRestfulController
{
    protected $inventory;

    public function getList()
    {
        $db = new InventoryDB('RO', $this->getServiceLocator());
        return $this->response($db->viewModel());
    }

    public function get($id)
    {
        $search = new Search($id);

        if ($search->isValid()) {
            $id = $this->wildcard($id);

            $db = new InventoryDB('RO', $this->getServiceLocator());
            return $this->response($db->searchModel($id));
        } else {
            return $this->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $this->response(array('error'=>'Unable to search records with given parameters'));
    }

    public function create($data)
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $computer = new Model($request->getPost());

            if ($computer->isValid()) {
                $db = new InventoryDB('RW', $this->getServiceLocator());
                return $this->response($db->addModel($request->getPost()));
            } else {
                return $this->response(array('error'=>'Given parameters did meet validation requirements'));
            }
        }

        return $this->response(array('error'=>'Could not save record'));
    }

    public function update($id, $data)
    {
        $computer = new Model($data);

        if ($computer->isValid()) {
            $db = new InventoryDB('RW', $this->getServiceLocator());
            return $this->response($db->updateModel($id, $data));
        } else {
            return $this->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $this->response(array('error'=>'Could not edit record'));
    }

    public function delete($id)
    {
        $computer = new Delete($id);

        if ($computer->isValid()) {
            $db = new InventoryDB('RW', $this->getServiceLocator());
            return $this->response($db->deleteModel($id));
        } else {
            return $this->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $this->response(array('error'=>'Unable delete specified record'));
    }
}
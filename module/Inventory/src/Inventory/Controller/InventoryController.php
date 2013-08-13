<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Inventory\Model\InventoryDB;
use Inventory\Model\Computer;

class InventoryController extends AbstractRestfulController
{
    protected $inventory;

    public function getList(){
        $db = new InventoryDB('RO', $this->getServiceLocator());
        return $this->response($db->viewComputer());
    }

    public function get($id){
        $request = $this->getRequest();

        return $this->response(array('search'=>'Search available records'));
    }

    public function create($data){
        $request = $this->getRequest();

        if ($request->isPost()) {
            $computer = new Computer($request->getPost());

            if ($computer->isValid()) {
                $db = new InventoryDB('RW', $this->getServiceLocator());

                return $this->response(array('success'=>'Record saved successfully'));
            }
        }

        return $this->response(array('error'=>'Could not save record'));
    }

    public function update($id, $data){
        return $this->response(array('edit'=>'Edit specified record'));
    }

    public function delete($id){
        return $this->response(array('delete'=>'Delete specified record'));
    }

    private function response($obj)
    {
        return new JsonModel($obj);
    }
}
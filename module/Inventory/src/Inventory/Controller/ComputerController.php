<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Inventory\Model\InventoryDB;
use Inventory\Model\Computer;
use Inventory\Model\Search;

class ComputerController extends AbstractRestfulController
{
    protected $inventory;

    public function getList(){
        $db = new InventoryDB('RO', $this->getServiceLocator());
        return $this->response($db->viewComputer());
    }

    public function get($id){
        $search = new Search($id);

        if ($search->isValid()) {
            $id = $this->wildcard($id);

            $db = new InventoryDB('RO', $this->getServiceLocator());
            return $this->response($db->searchComputer($id));
        } else {
            return $this->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $this->response(array('error'=>'Unable to search records with given parameters'));
    }

    public function create($data){
        $request = $this->getRequest();

        if ($request->isPost()) {
            $computer = new Computer($request->getPost());

            if ($computer->isValid()) {
                $db = new InventoryDB('RW', $this->getServiceLocator());

                return $this->response(array('success'=>'Record saved successfully'));
            } else {
                return $this->response(array('error'=>'Given parameters did meet validation requirements'));
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

    private function wildcard($str)
    {
        return preg_replace('/\*/', '%', $str);
    }
}
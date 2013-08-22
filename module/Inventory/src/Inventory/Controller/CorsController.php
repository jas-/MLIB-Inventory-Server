<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Inventory\Model\Db\CorsDB;
use Inventory\Model\Cors;
use Inventory\Model\Search;
use Inventory\Model\Delete;

class CorsController extends AbstractRestfulController
{
    public function getList()
    {
        $db = new CorsDB('RO', $this->getServiceLocator());
        return $db->response($db->view());
    }

    public function get($id)
    {
        $search = new Search($id);
        $db = new CorsDB('RO', $this->getServiceLocator());

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
            $cors = new Cors($data);
            $post = $cors->doClean($data);

            $db = new CorsDB('RW', $this->getServiceLocator());

            if ($cors->isValid()) {
                return $db->response($db->add($post));
            } else {
                return $db->response(array('error'=>'Given parameters did meet validation requirements'));
            }
        }

        return $db->response(array('error'=>'Could not save record'));
    }

    public function update($id, $data)
    {
        $cors = new Cors($data);
        $db = new CorsDB('RW', $this->getServiceLocator());

        if ($cors->isValid()) {
            $id = $cors->doClean($id);
            $post = $cors->doClean($data);

            return $db->response($db->update($id, $post));
        } else {
            return $db->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $db->response(array('error'=>'Could not edit record'));
    }

    public function delete($id)
    {
        $cors = new Delete($id);
        $db = new CorsDB('RW', $this->getServiceLocator());

        if ($cors->isValid()) {
            $post = $cors->doClean($id);

            return $db->response($db->delete($post));
        } else {
            return $db->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $db->response(array('error'=>'Unable delete specified record'));
    }
}
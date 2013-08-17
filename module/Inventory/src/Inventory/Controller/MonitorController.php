<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Inventory\Model\Db\MonitorDB;
use Inventory\Model\Monitor;
use Inventory\Model\Search;
use Inventory\Model\Delete;

class MonitorController extends AbstractRestfulController
{
    public function getList()
    {
        $db = new MonitorDB('RO', $this->getServiceLocator());
        return $db->response($db->view());
    }

    public function get($id)
    {
        $search = new Search($id);
        $db = new MonitorDB('RO', $this->getServiceLocator());

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
            $monitor = new Monitor($request->getPost());
            $post = $monitor->doClean($data);

            $db = new MonitorDB('RW', $this->getServiceLocator());

            if ($monitor->isValid()) {
                return $db->response($db->add($request->getPost()));
            } else {
                return $db->response(array('error'=>'Given parameters did meet validation requirements'));
            }
        }

        return $db->response(array('error'=>'Could not save record'));
    }

    public function update($id, $data)
    {
        $monitor = new Monitor($data);
        $db = new MonitorDB('RW', $this->getServiceLocator());

        if ($monitor->isValid()) {
            $id = $monitor->doClean($id);
            $post = $monitor->doClean($data);

            return $db->response($db->update($id, $post));
        } else {
            return $db->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $db->response(array('error'=>'Could not edit record'));
    }

    public function delete($id)
    {
        $monitor = new Delete($id);
        $db = new MonitorDB('RW', $this->getServiceLocator());

        if ($monitor->isValid()) {
            $post = $monitor->doClean($id);
            return $db->response($db->delete($id));
        } else {
            return $db->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $db->response(array('error'=>'Unable delete specified record'));
    }
}

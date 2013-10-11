<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\EventManager\EventManagerInterface;

use Inventory\Model\Db\MonitorDB;
use Inventory\Model\Cors;
use Inventory\Model\Monitor;
use Inventory\Model\Search;
use Inventory\Model\Delete;

class MonitorController extends AbstractRestfulController
{
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $events->attach('dispatch', array($this, 'checkOptions'), 10);
    }

    public function checkOptions($e)
    {
        $cors = new Cors(null, $this->getServiceLocator());
        $cors->doResponse($e);
    }

    public function options()
    {
        return true;
    }

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
            return $db->response(array('error'=>'Given parameters did meet validation requirements',
                                       'details'=>$search->getErrors()));
        }

        return $db->response(array('error'=>'Unable to search records with given parameters'));
    }

    public function create($data)
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $monitor = new Monitor($request->getPost());
            $post = array_change_key_case($monitor->doClean($data), CASE_LOWER);

            $db = new MonitorDB('RW', $this->getServiceLocator());

            if ($monitor->isValid()) {
                return $db->response($db->add($request->getPost()));
            } else {
                return $db->response(array('error'=>'Given parameters did meet validation requirements',
                                           'details'=>$monitor->getErrors()));
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
            $post = array_change_key_case($monitor->doClean($data), CASE_LOWER);

            return $db->response($db->update($id, $post));
        } else {
            return $db->response(array('error'=>'Given parameters did meet validation requirements',
                                       'details'=>$monitor->getErrors()));
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
            return $db->response(array('error'=>'Given parameters did meet validation requirements',
                                       'details'=>$monitor->getErrors()));
        }

        return $db->response(array('error'=>'Unable delete specified record'));
    }
}

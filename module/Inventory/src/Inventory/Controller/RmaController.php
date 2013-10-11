<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\EventManager\EventManagerInterface;

use Inventory\Model\Db\RmaDB;
use Inventory\Model\Cors;
use Inventory\Model\Rmas;
use Inventory\Model\Search;
use Inventory\Model\Delete;

class RmaController extends AbstractRestfulController
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
        $db = new RmaDB('RO', $this->getServiceLocator());
        return $db->response($db->view());
    }

    public function get($id)
    {
        $search = new Search($id);
        $db = new RmaDB('RO', $this->getServiceLocator());

        if ($search->isValid()) {
            $id = $search->doClean($id);
            $id = $db->wildcard($id);

            return $db->response($db->search($id));
        } else {
            return $db->response(array('error'=>'Given parameters did meet validation requirements'
                                       'details'=>$search->getErrors()));
        }

        return $db->response(array('error'=>'Unable to search records with given parameters'));
    }

    public function create($data)
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $rma = new Rmas($data);
            $post = array_change_key_case($rma->doClean($data), CASE_LOWER);

            $db = new RmaDB('RW', $this->getServiceLocator());

            if ($rma->isValid()) {
                return $db->response($db->add($post));
            } else {
                return $db->response(array('error'=>'Given parameters did meet validation requirements',
                                           'details'=>$rma->getErrors()));
            }
        }

        return $db->response(array('error'=>'Could not save record'));
    }

    public function update($id, $data)
    {
        $rma = new Rmas($data);
        $db = new RmaDB('RW', $this->getServiceLocator());

        if ($rma->isValid()) {
            $id = $rma->doClean($id);
            $post = array_change_key_case($rma->doClean($data), CASE_LOWER);

            return $db->response($db->update($id, $post));
        } else {
            return $db->response(array('error'=>'Given parameters did meet validation requirements',
                                       'details'=>$rma->getErrors()));
        }

        return $db->response(array('error'=>'Could not edit record'));
    }

    public function delete($id)
    {
        $rma = new Delete($id);
        $db = new RmaDB('RW', $this->getServiceLocator());

        if ($rma->isValid()) {
            $post = $rma->doClean($id);

            return $db->response($db->delete($post));
        } else {
            return $db->response(array('error'=>'Given parameters did meet validation requirements',
                                       'details'=>$rma->getErrors()));
        }

        return $db->response(array('error'=>'Unable delete specified record'));
    }
}

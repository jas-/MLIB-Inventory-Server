<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\EventManager\EventManagerInterface;

use Inventory\Model\Db\ModelDB;
use Inventory\Model\Cors;
use Inventory\Model\Models;
use Inventory\Model\Search;
use Inventory\Model\Delete;

class ModelController extends AbstractRestfulController
{
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $events->attach('dispatch', array($this, 'checkOptions'), 10);
    }

    public function checkOptions($e)
    {
        $cors = new Cors();
        $cors->doResponse($e);
    }

    public function options()
    {
        return true;
    }

    public function getList()
    {
        $db = new ModelDB('RO', $this->getServiceLocator());
        return $db->response($db->view());
    }

    public function get($id)
    {
        $search = new Search($id);
        $db = new ModelDB('RO', $this->getServiceLocator());

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
            $model = new Models($data);
            $post = $model->doClean($data);

            $db = new ModelDB('RW', $this->getServiceLocator());

            if ($model->isValid()) {
                return $db->response($db->add($post));
            } else {
                return $db->response(array('error'=>'Given parameters did meet validation requirements'));
            }
        }

        return $db->response(array('error'=>'Could not save record'));
    }

    public function update($id, $data)
    {
        $model = new Models($data);
        $db = new ModelDB('RW', $this->getServiceLocator());

        if ($model->isValid()) {
            $id = $model->doClean($id);
            $post = $model->doClean($data);

            return $db->response($db->update($id, $post));
        } else {
            return $db->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $db->response(array('error'=>'Could not edit record'));
    }

    public function delete($id)
    {
        $model = new Delete($id);
        $db = new ModelDB('RW', $this->getServiceLocator());

        if ($model->isValid()) {
            $post = $model->doClean($id);

            return $db->response($db->delete($post));
        } else {
            return $db->response(array('error'=>'Given parameters did meet validation requirements'));
        }

        return $db->response(array('error'=>'Unable delete specified record'));
    }
}

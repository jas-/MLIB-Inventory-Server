<?php

/* define application path */
define('__SITE', realpath(dirname(__FILE__)));

/* load our class initialization object */
if (!file_exists(__SITE.'/controllers/controller.initialization.php')){
	exit('Necessary controller missing, unable to proceed. 0x0c0');
}
include __SITE.'/controllers/controller.initialization.php';

/*
 * Below here will be moved to the view/view.proxy.php file
 * All of this should be wrapped in the necessary XMLHttpRequest
 * validations and also provide a valid authentication token
 * but for testing and development purposes these are ommited
 * temporarily
 */

/* Add any machine you wish to have CORS access to this array */
$cors = array('http://mobile-inventory.scl.utah.edu',
			  'http://localhost:8080',
			  'http://inventory-client.dev:8080');

if (in_array($_SERVER['HTTP_ORIGIN'], $cors)) {
	header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
}

header('Access-Control-Max-Age: 1728000');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Cache-Control, Content-MD5, Content-Type, X-Alt-Referer, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('X-Powered-By: University Of Utah - Marriott Library');
header('Content-Type: application/json');
header('Cache-Control: No-Cache, no-store, must-revalidate');

/* Accomidate JSON objects */
$type = (!empty($_SERVER['HTTP_CONTENT_TYPE'])) ? $_SERVER['HTTP_CONTENT_TYPE'] : $_SERVER['CONTENT_TYPE'];
if (stripos($type, "application/json")===0) {
    $_POST = json_decode(file_get_contents("php://input"), true);
}

/* sanitize $_POST super globals */
if (!empty($_POST)){
	$post = $registry->val->__do($_POST);
}

/* sanitize $_GET super globals */
if (!empty($_GET)){
	$get = $registry->val->__do($_GET);
}

if (!empty($get)){
	switch($get['do']){
		case 'current':
			exit($registry->libs->JSONEncode(_all_computers($registry)));
		case 'all-models':
			exit($registry->libs->JSONEncode(_all_models($registry)));
		case 'search-model':
			exit($registry->libs->JSONEncode(_search_model($post, $registry)));
		case 'search':
			exit($registry->libs->JSONEncode(_search($post, $registry)));
		case 'add':
			exit($registry->libs->JSONEncode(_add($post, $registry)));
		case 'add-computer':
			exit($registry->libs->JSONEncode(_add_computer($post, $registry)));
		case 'add-monitor':
			exit($registry->libs->JSONEncode(_add_monitor($post, $registry)));
		case 'edit-computer':
			exit($registry->libs->JSONEncode(_edit_computer($post, $registry)));
		case 'edit-monitor':
			exit($registry->libs->JSONEncode(_edit_monitor($post, $registry)));
		default:
			exit($registry->libs->JSONencode(array('warning'=>'Invalid command recieved')));
	}
} else {
	exit($registry->libs->JSONEncode(array('warning'=>'No command recieved')));
}

/* Return all models */
function _all_models($registry)
{
	try{
		$sql = sprintf('CALL Inv_ModelAll()');
		$r = $registry->db->query($sql, true);
		$x = (($r)&&(is_array($r))&&(count($r)>=1)) ? $r : array('warning'=>'No results for model');
	} catch(Exception $e){
		$x = array('error'=>'An error occured when listing all models');
	}
	return $x;
}

/* Return all computers */
function _all_computers($registry)
{
	try{
		$sql = 'CALL Inv_List';
		$r = $registry->db->query($sql, true);
		if (count($r) > 1) {
			foreach($r as $key => $value) {
				$sql = sprintf('CALL Inv_ListMonitor("%s")', $value['Computer']);
				$m = $registry->db->query($sql, true);
				if (($m)&&(is_array($m))&&(count($m)>=1)){
					$r[$key]['Monitor'] = $m;
				}
			}
		}
		$x = (($r)&&(is_array($r))&&(count($r)>=1)) ? $r : array('warning'=>'No results for current inventory');

	} catch(Exception $e){
		$x = array('error'=>'An error occured when listing all inventory items');
	}
	return $x;
}

/* Search models */
function _search_model($obj, $registry)
{
	if (is_array($obj)){
		if (!empty($obj['model'])){
			try{
				$sql = sprintf('CALL Inv_ModelSearch("%s", "%s")', $registry->val->__do($obj['model'], 'string'), $registry->val->__do($_SESSION['token']));
				$r = $registry->db->query($sql);
				$x = (($r)&&(is_array($r))&&(count($r)>=1)) ? $r : array('warning'=>'No results for model');
			} catch(Exception $e){
				$x = array('error'=>'An error occured while attempting search');
			}
		} else {
			$x = array('error'=>'Must provide a model to search for');
		}
	} else {
		$x = array('error'=>'Empty search parameters');
	}
	return $x;
}

/* Search by hostname, model, serial, uuic or sku */
function _search($obj, $registry)
{
	if (is_array($obj)){
		if (!empty($obj['search'])) {
			$obj = array_map('wildcard', $obj);
			try{
				$sql = sprintf('CALL Inv_Search("%s")', $registry->val->__do(wildcard($obj['search'])));
				$r = $registry->db->query($sql, true);
				$x = (($r)&&(is_array($r))&&(count($r)>=1)) ? $r : array('warning'=>'No results for computer');
			} catch(Exception $e){
				$x = array('error'=>'An error occured while attempting search');
			}
		} else {
			$x = array('error'=>'Must provide a hostname, sku, uuic or serial to search for');
		}
	} else {
		$x = array('error'=>'Empty search parameters');
	}
	return $x;
}

/* Add new machine */
function _add($obj, $registry)
{

	if (is_array($obj)){
		if ((!empty($obj['sku']))&&(!empty($obj['uuic']))&&(!empty($obj['serial']))) {
			try{
				$sql = sprintf('CALL Inv_ComputerAddUpdate("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s")',
								$registry->val->__do($obj['model'], 'string'),
								$registry->val->__do($obj['sku'], 'string'),
								$registry->val->__do($obj['uuic'], 'string'),
								$registry->val->__do($obj['serial'], 'string'),
								$registry->val->__do($obj['hostname'], 'string'),
								$registry->val->__do($obj['location'], 'string'),
								$registry->val->__do($obj['eowd'], 'string'),
								$registry->val->__do($obj['opd'], 'string'),
								$registry->val->__do($obj['notes'], 'string'));
				$r = $registry->db->query($sql);

				/* Is there monitor data present? */
				if ((!empty($obj['msku']))&&(!empty($obj['monitor']))&&(!empty($obj['mserial']))) {
					$m['hostname'] = $obj['monitor'];
					$m['sku'] = $obj['msku'];
					$m['serial'] = $obj['mserial'];
					$t = _add_monitor($m, $registry);
				}

				if (($r)&&(is_array($r))&&(count($r)>=1)) {
					$r['success'] = 'Successfully added new record';
				}
				$x = (($r)&&(is_array($r))&&(count($r)>=1)) ? $r : array('error'=>'An entry for this record exists');
			} catch(Exception $e){
				$x = array('error'=>'An error occured while adding record');
			}
		} else {
			$x = array('error'=>'Necessary arguments for adding a machine are missing');
		}
	} else {
		$x = array('error'=>'Empty arguments necessary for adding or updating a machine record');
	}
	return $x;
}

/* Add/Edit computer records */
function _add_computer($obj, $registry)
{
	if (is_array($obj)){
		try{
			$sql = sprintf('CALL Inv_ComputerAddUpdate("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s")',
							$registry->val->__do($obj['model'], 'string'),
							$registry->val->__do($obj['sku'], 'string'),
							$registry->val->__do($obj['uuic'], 'string'),
							$registry->val->__do($obj['serial'], 'string'),
							$registry->val->__do($obj['hostname'], 'string'),
							$registry->val->__do($obj['location'], 'string'),
							$registry->val->__do($obj['eowd'], 'string'),
							$registry->val->__do($obj['opd'], 'string'),
							$registry->val->__do($obj['notes'], 'string'));
			$r = $registry->db->query($sql);
			$x = ($r['affected'] <= 0) ? array('info'=>'Record was not modified because no fields were changed') : array('success'=>'Successfully modified record "'.$registry->val->__do($obj['hostname'], 'string').'"');

		} catch(Exception $e){
			$x = array('error'=>'An error occured while adding record');
		}
	} else {
		$x = array('error'=>'Empty arguments necessary for adding or updating a machine record');
	}
	return $x;
}

/* Add/Edit monitor records */
function _add_monitor($obj, $registry)
{
	if (is_array($obj)){
			try{
				$sql = sprintf('CALL Inv_MonitorAddUpdate("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s")',
								$registry->val->__do($obj['msku'], 'string'),
								$registry->val->__do($obj['mserial'], 'string'),
								$registry->val->__do($obj['monitor'], 'string'),
								$registry->val->__do($obj['model'], 'string'),
								$registry->val->__do($obj['mmodel'], 'string'),
								$registry->val->__do($obj['mlocation'], 'string'),
								$registry->val->__do($obj['meowd'], 'string'),
								$registry->val->__do($obj['morder'], 'string'));

				$r = $registry->db->query($sql);

				$x = ($r['affected'] <= 0) ? array('info'=>'Record was not modified because no fields were changed') : array('success'=>'Successfully modified record "'.$registry->val->__do($obj['monitor'], 'string').'"');

			} catch(Exception $e){
				$x = array('error'=>'An error occured while adding record');
			}
	} else {
		$x = array('error'=>'Empty arguments necessary for adding or updating a machine record');
	}
	return $x;
}

function wildcard($x)
{
	return str_replace('*', '%', $x);
}

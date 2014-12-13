<?php

namespace Admin;

 

/**
 * DSF CMS
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://digitalus-media.com/license/new-bsd
 * If you did not receive a copy \of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@digitalus-media.com so we can send you a copy immediately.
 *
 * @category   DSF CMS
 * @package    DSF_CMS_Controllers
 * @copyright  Copyright (c) 2007 - 2008,  Digitalus Media USA (digitalus-media.com)
 * @license    http://digitalus-media.com/license/new-bsd     New BSD License
 * @version    $Id: UserController.php Tue Dec 25 19:48:48 EST 2007 19:48:48 forrest lyman $
 */


use DSF\Data\Storage as Storage;
use DSF\Filter\Post as Post;
use DSF\View\Error as Error;





class  UserController  extends \Zend_Controller_Action
{
	
	function init()
	{
	    $this->view->breadcrumbs = array(
	       $this->view->GetTranslation('Site Settings') =>   $this->getFrontController()->getBaseUrl() . '/admin/site'
	    );
	}
	
	/**
	 * render the user management interface
	 *
	 */
	function indexAction()
	{
	}

	/**
	 * open a user \for editing
	 *
	 */
	function openAction()
	{
	   
       $id = $this->_request->getParam('id', 0);
       if($id > 0){
           $u = new \User();
    	   $row = $u->find($id)->current();
    	   $this->view->user = $row;
    	   $this->view->userPermissions = $u->getAclResources($row);
       }

        $breadcrumbLabel = $this->view->GetTranslation('Open \User') . ": " . $this->view->user->first_name . ' ' . $this->view->user->last_name;
	    $this->view->breadcrumbs[$breadcrumbLabel] = $this->getFrontController()->getBaseUrl() . '/admin/user/open/id/' . $id;
	    $this->view->toolbarLinks = array();
	    $this->view->toolbarLinks[$this->view->GetTranslation('Delete')] = $this->getFrontController()->getBaseUrl() . '/admin/user/delete/id/' . $id;
	    $this->view->toolbarLinks[$this->view->GetTranslation('Add to my bookmarks')] = $this->getFrontController()->getBaseUrl() . '/admin/index/bookmark/url/admin_user_open_id_' . $id;
	}

	/**
	 * add a new user
	 *
	 */
	function createAction()
	{
	    if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    		$u = new \User();
            $user = $u->insertFromPost();
            $e = new  Error ();
            if(!$e->hasErrors()){
                $url = 'admin/user/open/id/' . $user->id;
                $this->_redirect($url);
            }else{
            	$storage = new  Storage ();
            	$storage->savePost();
            }
	    }
	    $this->view->toolbarLinks[$this->view->GetTranslation('Add to my bookmarks')] = $this->getFrontController()->getBaseUrl() . '/admin/index/bookmark/url/admin_user_create';
	}
	
	/**
	 * edit an existing user
	 *
	 */
	function editAction()
	{
	    $u = new \User();
        if( Post ::has('update_permissions')) {
            //update the users permissions
            $resources =  Post ::raw('acl_resources');
            $id =  Post ::int('id');
            $u->updateAclResources($id, $resources);
        }elseif( Post ::has('admin_user_password')) {
            $id =  Post ::int('id');
            $password =  Post ::get('newPassword');
            $passwordConfirm =  Post ::get('newConfirmPassword');
            $u->updatePassword($id, $password, true, $passwordConfirm);
        }else{
            $user = $u->updateFromPost();
            $id = $user->id;
        }
   		$url = 'admin/user/open/id/' . $id;

      $this->_redirect($url);

    }
    
    public function updateMyAccountAction()
    {
    	$u = new \User();
        if( Post ::int('update_password') === 1) {
            $id =  Post ::int('id');
            $password =  Post ::get('password');
            $passwordConfirm =  Post ::get('confirmation');
            $u->updatePassword($id, $password, true, $passwordConfirm);
        }else{
            $user = $u->updateFromPost();
            $id = $user->id;
        }
    	$url = 'admin/index';
    	$this->_redirect($url);
    }
    
    function copyAclAction()
    {
        $currentUser =  Post ::int('id');
        $copyFrom =  Post ::int('user_id');
        
        if($currentUser > 0 && $copyFrom > 0) {
            $u = new \User();
            $u->copyPermissions($copyFrom, $currentUser);
        }
        $url = 'admin/user/open/id/' . $currentUser;
        $this->_redirect($url);
    }
	
    /**
     * delete a user
     *
     */
	function deleteAction()
	{
	   $id = $this->_request->getParam('id');
	   $u = new \User();
	   $u->delete("id = " . $id);
	   $url = "admin/site";
       $this->_redirect($url);
	}

}

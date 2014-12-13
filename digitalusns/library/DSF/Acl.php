<?php

namespace DSF;

 

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
 * @package   DSF_Core_Library
 * @copyright  Copyright (c) 2007 - 2008,  Digitalus Media USA (digitalus-media.com)
 * @license    http://digitalus-media.com/license/new-bsd     New BSD License
 * @version    $Id: Acl.php Tue Dec 25 21:39:35 EST 2007 21:39:35 forrest lyman $
 */


use Zend\Controller\Front as Front;
use Zend\Acl\Resource as Resource;
use Zend\Acl\Role as Role;
use Zend\Acl as ZendAcl;





class  Acl  extends  ZendAcl 
{
    
    protected $resourceList; //the resources list is the public array and is not used internally
	/**
	 * load the acl resources and set up permissions
	 *
	 */
    public function __construct()
    {
        $this->addRole(new  Role ('admin'));
        $this->addRole(new  Role ('superadmin'));
       
        $this->loadResources();
        $this->loadCurrentUsersPermissions();
        
        //load common resources
        $this->add(new  Resource ('admin_auth'));
        
        //everybody
        $this->allow(null, 'admin_auth');
        
        //grant the super admin access to everything
       	$this->allow('superadmin');

    }
    
    public function loadResources()
    {
        $front =  Front ::getInstance();
        $ctlPaths = $front->getControllerDirectory();
        
        //set the path to all \of the modules
        foreach ($ctlPaths as $module => $path)  {
            if($module != 'public' && $module != 'front') {
                //clear the resource list items
                $resourceListItems = null;
                
                $path = str_replace('controllers', 'acl.xml',$path);
                
                //load the module resource
                $this->add(new  Resource ($module));
                
                //attempt to load each acl file
                if(file_exists($path)){
                    $xml = simplexml_load_file($path);
                    $controllers = $xml->children();
                    foreach ($controllers as $controller) {
                        $controllerName = (string)$controller->attributes()->name;
                        $controllerActions = $controller->children();
                        if(count($controllerActions) > 0){
                            foreach ($controllerActions as $action) {
                                //load each action separately
                                $actionName = (string)$action;
                                $key = $module . '_' . $controllerName . '_' . $actionName;
                                $this->add(new  Resource ($key), $module);
                                
                                //add the action to the public resource list
                                $resourceListItems[$controllerName][] = $actionName;
                            }
                        }else{
                            //set the resource at the controller level
                            $key = $module . '_' . $controllerName;
                            $this->add(new  Resource ($key), $module);
                            
                            //add the controller to the public resource list
                            $resourceListItems[$controllerName] = null;
                        }
                    }
                }
                
                $this->resourceList[$module] = $resourceListItems;
            } 
        }       
    }
    
    public function getResourceList()
    {
        return $this->resourceList;
    }
    
    public function loadCurrentUsersPermissions(){
        $user = new \User();
        $permissions = $user->getCurrentUsersAclResources();

        if($permissions) {
            foreach ($permissions as $key => $value) {
                if($value == 1){
                    $this->allow('admin',$key);
                }else{
                    $this->deny('admin',$key);
                }
            }
        }
    }
    
    private function getResourceFromRow($row)
    {
        if($row->admin_section == 'module'){
            return 'mod_' . $row->controller;
        }else{
            return $row->controller;
        }
    }
}

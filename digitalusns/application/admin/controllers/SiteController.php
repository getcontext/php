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
 * @version    $Id: SiteController.php Tue Dec 25 19:46:11 EST 2007 19:46:11 forrest lyman $
 */


use Zend\Session\SessionNamespace as SessionNamespace;
use DSF\Filter\Post as Post;
use DSF\Command as Command;
use DSF\Mail as Mail;





class  SiteController  extends \Zend_Controller_Action 
{
	public function init()
	{
	    $this->view->breadcrumbs = array(
	       $this->view->GetTranslation('Site Settings') =>   $this->getFrontController()->getBaseUrl() . '/admin/site'
	    );
	}
	
	/**
	 * render the main site admin interface
	 *
	 */
	public function indexAction()
	{
		$settings = new \SiteSettings();
		$this->view->settings = $settings->toObject();
		$this->view->toolbarLinks[$this->view->GetTranslation('Add to my bookmarks')] = $this->getFrontController()->getBaseUrl() . '/admin/index/bookmark/url/admin_site';
	}
	
	/**
	 * update the site settings file
	 *
	 */
	public function editAction()
	{
		$settings =  Post ::raw('setting');
		$s = new \SiteSettings();
		foreach ($settings as $k => $v) {
			$s->set($k, $v);
		}
		$s->save();
		$this->_redirect('admin/site');
	}
	
	/**
	 * the console provides an interface \for simple command scripts.
	 * those scripts go in library/DSF/Command/{script name}
	 *
	 */
	public function consoleAction()
	{
	    //set up a unique id \for this session
	    $session = new  SessionNamespace ('console_session');
	    $previousId = $session->id;
	    $session->id = md5(time());
	    $this->view->consoleSession = $session->id;
	    	    
	    //you must validate that the session ids match
	    if($this->_request->isPost() && !empty($previousId))
	    {
	        $this->view->commandExecuted = true;
	        $this->view->command = "Command: " .  Post ::get('command');
	        $this->view->date = time();
	        
	        //execute command
	        //validate the session
	        
	        if( Post ::get('consoleSession') == $previousId)
	        {
	            $this->view->lastCommand =  Post ::get('command');
	            if( Post ::get('runCommand'))
	            {
	               $results =  Command ::run( Post ::get('command'));
	            }elseif ( Post ::get('getInfo'))
	            {
	                $results =  Command ::info( Post ::get('command'));
	            }else{
	                $results = array('ERROR: invalid request');
	            }
	        }else{
	            $results[] = "ERROR: invalid session";
	        }
	        
	        $this->view->results = $results;
	    }
	    
	    $breadcrumbLabel = $this->view->GetTranslation('Site Console');
	    $this->view->breadcrumbs[$breadcrumbLabel] = $this->getFrontController()->getBaseUrl() . '/admin/site/console';
	    $this->view->toolbarLinks = array();
	    $this->view->toolbarLinks[$this->view->GetTranslation('Add to my bookmarks')] = $this->getFrontController()->getBaseUrl() . '/admin/index/bookmark/url/admin_site_console';
	    
	}
	
	public function mailTestAction()
	{
	    $settings = new \SiteSettings();
	    $message = new  Mail ();
	    $message->send(
	        $settings->get('default_email'), 
	        array($settings->get('default_email'), $settings->get('default_email_sender')), 
	        "Digitalus CMS Test Message", 
	        'test'
        );
        $this->_forward('index');
	}
	
	
}

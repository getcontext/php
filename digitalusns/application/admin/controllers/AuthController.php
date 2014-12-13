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
 * @version    $Id: AuthController.php Mon Dec 24 20:48:35 EST 2007 20:48:35 forrest lyman $
 */


use DSF\Toolbox\ToolboxString as ToolboxString;
use DSF\View\Message as Message;
use DSF\Filter\Post as Post;
use DSF\View\Error as Error;
use DSF\Auth as Auth;
use DSF\Mail as Mail;
use DSF\Uri as Uri;





class  AuthController  extends \Zend_Controller_Action
{
	
	function init()
	{
	    $this->view->breadcrumbs = array(
	       $this->view->GetTranslation('Login') =>   $this->getFrontController()->getBaseUrl() . '/admin/auth/login'
	    );
	}
	
    /**
     * if the form has not been submitted this renders the login form
     * if it has then it validates the data
     * if it is sound then it runs the  Auth _Adapter function
     * to authorise the request
     * on success it redirct to the admin home page
     *
     */
	function loginAction()
    {
        if ($this->_request->isPost()) {
            $uri =  Post ::get('uri');
            $username =  Post ::get('adminUsername');
            $password =  Post ::raw('adminPassword');

			$e = new  Error ();
            
            if($username == ''){
                $e->add($this->view->GetTranslation("You must enter a username."));
            }
            if($password == ''){
                $e->add($this->view->GetTranslation("You must enter a password."));
            }
            
            
            if (!$e->hasErrors()) {        
                $auth = new  Auth ($username, $password);
                $result = $auth->authenticate();
                if($result){    
                	if($uri == '' || $uri == 'admin/auth/login'){
                    	$uri = 'admin';
                	}
                     $this->_redirect($uri);
                }else{
					$e = new  Error ();
					$e->add($this->view->GetTranslation('The username or password you entered was not correct.'));
                }
            }
			$this->view->uri = $uri;
        }else{
            $this->view->uri =  Uri ::get();
        }
                    
    }
    
	/**
	 * kills the authorized user object
	 * then redirects to the main index page
	 *
	 */
	function logoutAction()
	{
		 Auth ::destroy();
		$this->_redirect("/");
	}
	
	function resetPasswordAction()
	{
		if (strtolower($_SERVER["REQUEST_METHOD"]) == "post") {
			$email =  Post ::get('email');  
			$user = new \User();      		
			$match = $user->getUserByUsername($email);
    		if($match){
    			//create the password
    			$password =  ToolboxString ::random(10); //10 character random string

    			//load the email data
    			$data['first_name'] = $match->first_name;
    			$data['last_name'] = $match->last_name;
    			$data['username'] = $match->email;
    			$data['password'] = $password;
    			
    			//get standard site settings
                $s = new \SiteSettings();
                $settings = $s->toObject();
                
                //attempt to send the email 
                $mail = new  Mail ();               
                if($mail->send($match->email, array($sender), "Password Reminder", 'passwordReminder', $data))
                {            
    	            //update the user's password
    	            $match->password = md5($password);
    	            $match->save();//save the new password
    	            $m = new  Message ();
    	            $m->add(
    	            	$this->view->GetTranslation("Your password has been reset \for security and sent to your email address")
   	            	);
                }else{
                    $e = new  Error ();
                    $e->add(
                    	$this->view->GetTranslation("Sorry, there was an error sending you your updated password.  Please contact us \for more help.")
                   	);
                }	            
    		}else{
	            $e = new  Error ();
	            $e->add(
	            	$this->view->GetTranslation("Sorry, we could not locate your account. Please contact us to resolve this issue.")
            	);
    		}
  		$url =  "admin/auth/login";
		$this->_redirect($url);
			
		 }
	}

}
 
  

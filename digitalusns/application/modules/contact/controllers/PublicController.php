<?php

namespace Mod\Contact;




use DSF\View\Message as Message;
use DSF\Filter\Post as Post;
use DSF\View\Error as Error;
use DSF\Mail as Mail;




class  PublicController  extends \Zend_Controller_Action 
{
    public function contactFormAction()
    {
        if($this->_request->isPost() &&  Post ::has('submitContactForm'))
        {
            $pageId = $this->_request->getParam('pageId');
            $p = new \Properties($pageId);
            $moduleData = $p->getGroup('modules')->items;
            $settings = new \SiteSettings();
            
            $m = new  Message ();
            $e = new  Error ();
            
            $sender =  Post ::get('email');
            $name =  Post ::get('name');
            $subject =  Post ::get('subject');
            $data['name'] = $name;
            $data['sender'] = $sender;
            $data['message'] =  Post ::get('message');
            
            if( Post ::int('copyMe') == 1)
            {
                $cc = $sender;
            }            
            
    		$mail = new  Mail ();
            if($mail->send($moduleData->params['email'], array($sender), $subject, 'contactForm', $data, $cc))
            {
                $m->add($moduleData->params['successMessage']);
            }else{
                $e->add($moduleData->params['errorMessage']);
            }
            
            //autoresponse
            if(!empty($moduleData->params['autoresponse_message']))
            {
                unset($data);
                $data['autoresponse'] = $moduleData->params['autorespond'];
                $response = new  Mail ();
                $response->send(
                    $sender, 
                    array($moduleData->params['email'], $moduleData->params['recipient']), 
                    $moduleData->params['autoresponse_subject'], 
                    'autoresponder',
                    $data);
            }
        
        }
		
        $this->view->recipient = $this->_request->getParam('recipient');
    }
}

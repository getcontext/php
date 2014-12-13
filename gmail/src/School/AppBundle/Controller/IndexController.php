<?php

namespace School\AppBundle\Controller;

use School\AppBundle\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Constraints\Email;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Salamon\Google\GmailConnector as Connector;

/**
 * Index controller.
 *
 */
class IndexController extends Controller
{
    //param task
    const PARAM_NAME = 'parameter';

    //gmail task
    const CLIENT_ID = '824364323813-n50d0jga5r52msv6cg21897mo2judkl4.apps.googleusercontent.com';
    const CLIENT_SECRET = 'txa7ah1sEvYXATnUvONcM8Fi';
    const REDIRECT_URL = 'http://localhost/simplesurance/web/app_dev.php/gmail.html';


    /**
     * param task
     *
     * @Route("/", name="index")
     * @Template()
     */
    public function indexAction()
    {
        $msg = null;
        $paramVal = $this->getRequest()->get(self::PARAM_NAME);
        if ($paramVal == null) {
            $msg = 'Parameter name "parameter" not set.';
        } else {
            $msg = 'Value of "parameter" is ' . $paramVal;
        }

        return array('message' => $msg);
    }


    /**
     * gmail task
     *
     * @Route("/gmail.html", name="index_gmail")
     * @Template()
     */
    public function gmailAction()
    {
        $out = array();
        $html = null;

        $connector = new Connector($this->getRequest(), self::CLIENT_ID, self::CLIENT_SECRET, self::REDIRECT_URL);
        $connector->auth();

        if ($connector->isAuth()) {
            $html .= "<table border='1'>";
            $html .= "<tr><td><a href='gmailComposeMessage'>NEW MESSAGE</a></td></tr>";
            foreach ($connector->read() as $msg) {
                $html .= "<tr><td><a href='gmailDisplayMessage/id/" . $msg['id'] . "'>" . $msg['subject'] . "</a></td></tr>";
            }
            $html .= "</table>";
        } else {
            $html = "please authenticate with google";
            $authUrl = $connector->getAuthUrl();
        }


        $out['message'] = $html;
        if (isset($authUrl))
            $out['authUrl'] = $authUrl;

        return $out;
    }

    /**
     * gmail task
     *
     * @Route("/gmailDisplayMessage/id/{id}", name="index_gmaildispmsg")
     * @Template()
     */
    public function gmailDisplayMessageAction($id)
    {
        $out = array();
        $html = null;

        $connector = new Connector($this->getRequest(), self::CLIENT_ID, self::CLIENT_SECRET, self::REDIRECT_URL);
        $connector->auth();

        if ($connector->isAuth()) {
            $msg = $connector->readMessage($id);
            $html .= "<table border='1'>";
            $html .= "<tr><td><a href='javascript:history.go(-1)'>back</a></td></tr>";
            $html .= "<tr><td>" . $msg['subject'] . "</td></tr>";
            $html .= "<tr><td>" . $msg['message'] . "</td></tr>";
            $html .= "</table>";
        } else {
            $html = "please authenticate with google";
            $authUrl = $connector->getAuthUrl();
        }


        $out['message'] = $html;
        if (isset($authUrl))
            $out['authUrl'] = $authUrl;

        return $out;
    }

    /**
     * gmail task
     *
     * @Route("/gmailComposeMessage", name="index_gmaildcmppmsg")
     * @Template()
     */
    public function gmailComposeMessageAction($id)
    {
        $out = array();
        $html = null;

        $connector = new Connector($this->getRequest(), self::CLIENT_ID, self::CLIENT_SECRET, self::REDIRECT_URL);
        $connector->auth();

        if ($connector->isAuth()) {
            $msg = $connector->readMessage($id);
            $html .= "<table border='1'>";
            $html .= "<tr><td><a href='javascript:history.go(-1)'>back</a></td></tr>";
            $html .= "<tr><td>" . $msg['subject'] . "</td></tr>";
            $html .= "<tr><td>" . $msg['message'] . "</td></tr>";
            $html .= "</table>";
        } else {
            $html = "please authenticate with google";
            $authUrl = $connector->getAuthUrl();
        }


        $out['message'] = $html;
        if (isset($authUrl))
            $out['authUrl'] = $authUrl;

        return $out;
    }

    /**
     * gmail task
     *
     * @Route("/gmailSend", name="index_gmailsend")
     * @Template()
     */
    public function gmailSendAction()
    {
        $out = array();
        $html = null;

        $connector = new Connector($this->getRequest(), self::CLIENT_ID, self::CLIENT_SECRET, self::REDIRECT_URL);
        $connector->auth();

        if ($connector->isAuth()) {

        } else {
            $html = "please authenticate with google";
            $authUrl = $connector->getAuthUrl();
        }


        $out['message'] = $html;
        if (isset($authUrl))
            $out['authUrl'] = $authUrl;

        return $out;
    }
}

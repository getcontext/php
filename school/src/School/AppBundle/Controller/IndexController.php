<?php

namespace School\AppBundle\Controller;

use School\AppBundle\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Constraints\Email;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Index controller.
 *
 */
class IndexController extends Controller
{
    //param task
    const PARAM_NAME = 'parameter';

    //gmail task
    const API_KEY = "AIzaSyAg4l3NH9YI_xgzFTaREdi9Xp8_7FogjDw";
    const CLIENT_ID = "615303666933-l8krok1mvt7u717qourorisbpce8s2rk.apps.googleusercontent.com";
    const CLIENT_SECRET = 'X6M1zJnKWMBhPV-6s-lxMILf';
    const ACCESS_TOKEN = '{
  "access_token": "ya29.bwDy_2dkwEfBYxwAAAA5nqrnVDExZVC3iC8Gv9UAu9bcLxC-r_84bKYVYjAWbQ",
  "token_type": "Bearer",
  "expires_in": 3600,
  "refresh_token": "1/wInLSeniirN6kgRZUNVFqFZr8ADczfcIOiWcS9zaxKU"
}';


    /**
     * param task
     *
     * @Route("/", name="_index")
     * @Template()
     */
    public function indexAction()
    {
        $msg = null;
        $paramVal = $this->getRequest()->get('parameter');
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
     * @Route("/gmail", name="_index_register")
     * @Template()
     */
    public function gmailAction()
    {
//        $this->sendGmailRequest2();
//        $this->sendGmailRequest3();
        $this->sendGmailRequest4();
        return array('message' => 'ok');
    }

    /**
     * @return array
     */
    private function sendGmailRequest()
    {
        ########## Google Settings.. Client ID, Client Secret from https://cloud.google.com/console #############

        $google_client_id = self::CLIENT_ID;
        $google_client_secret = self::CLIENT_SECRET;
        $google_redirect_url = 'http://localhost/school/web/app_dev.php/gmail'; //path to your script
        $google_developer_key = self::API_KEY;

        $gClient = new \Google_Client();
        $gClient->setApplicationName('Login to gmail');
        $gClient->setClientId($google_client_id);
        $gClient->setClientSecret($google_client_secret);
        $gClient->setRedirectUri($google_redirect_url);
        $gClient->setDeveloperKey($google_developer_key);
        $gClient->setAccessToken(self::ACCESS_TOKEN);
//        $gClient->authenticate();
        var_dump($gClient->getAccessToken());
        $google_oauthV2 = new \Google_Service_Oauth2($gClient);
        print_r($google_oauthV2->userinfo->get());
        die;
        if ($gClient->getAccessToken()) {
            //For logged in user, get details from google using access token
            $user = $google_oauthV2->userinfo->get();
//            var_dump($user);
//            $user_id              = $user['id'];
//            $user_name            = filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
//            $email                = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
//            $profile_url          = filter_var($user['link'], FILTER_VALIDATE_URL);
//            $profile_image_url    = filter_var($user['picture'], FILTER_VALIDATE_URL);
//            $personMarkup         = "$email<div><img src='$profile_image_url?sz=50'></div>";
//            $_SESSION['token']    = $gClient->getAccessToken();
        }

    }

    /**
     * @return array
     */
    private function sendGmailRequest2()
    {
        $google_client_id = self::CLIENT_ID;
        $google_client_secret = self::CLIENT_SECRET;
        $google_redirect_url = 'http://localhost/school/web/app_dev.php/gmail'; //path to your script
        $google_developer_key = self::API_KEY;

        $gClient = new \Google_Client();
        $gClient->setApplicationName('Login to gmail');
        $gClient->setClientId($google_client_id);
        $gClient->setClientSecret($google_client_secret);
        $gClient->setRedirectUri($google_redirect_url);
//        $gClient->setDeveloperKey($google_developer_key);
//        $gClient->setAccessToken(self::ACCESS_TOKEN);
        $gClient->addScope('email');
        //$client->addScope('profile');
        $gClient->addScope('https://mail.google.com');
        $gClient->setAccessType('offline');


        $service = new \Google_Service_Gmail($gClient);


        $optParams = [];
        $optParams['maxResults'] = 5; // Return Only 5 Messages
        $optParams['labelIds'] = 'INBOX'; // Only show messages in Inbox
        $messages = $service->users_messages->listUsersMessages('andrzej.salamon@gmail.com', $optParams);
        $list = $messages->getMessages();
        $messageId = $list[0]->getId(); // Grab first Message
    }

    private function sendGmailRequest3()
    {

        $client = new \Google_Client();
        $client->setApplicationName("Client_Library_Examples");
        $client->setClientId(self::CLIENT_ID);
        $client->setClientSecret(self::CLIENT_SECRET);
        $client->setDeveloperKey(self::API_KEY);
        $service = new \Google_Service_Gmail($client);
        $service->users_messages->listUsersMessages('andrzej.salamon@gmail.com');
    }


    private function sendGmailRequest4()
    {

        $client = new \Google_Client();
        $client->setApplicationName("Client_Library_Examples");

        $auth = new \Google_Auth_AppIdentity($client);
        $token = $auth->authenticateForScope(\Google_Service_Storage::DEVSTORAGE_READ_ONLY);
        if (!$token) {
            die("Could not authenticate to AppIdentity service");
        }
        $client->setAuth($auth);

        $service = new \Google_Service_Storage($client);
        $results = $service->buckets->listBuckets(str_replace("s~", "", $_SERVER['APPLICATION_ID']));
    }
}

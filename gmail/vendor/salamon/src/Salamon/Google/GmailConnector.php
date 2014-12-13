<?php
/**
 * @author andrzej.salamon@gmail.com
 *
 * all rights reserved but you can modify it and use it as yo want
 */

namespace Salamon\Google;

use Symfony\Component\HttpFoundation\Request;

final class GmailConnector
{
    private $clientId;
    private $clientSecret;
    private $redirectUrl;
    /**
     * @var \Google_Client
     */
    private $client;

    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $r, $clientId, $clientSecret, $redirectUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
        $this->request = $r;

        $this->client = new \Google_Client();
        $this->client->setClientId($this->clientId);
        $this->client->setClientSecret($this->clientSecret);
        $this->client->setRedirectUri($this->redirectUrl);
        $this->client->addScope('https://www.googleapis.com/auth/gmail.readonly');
        $this->client->addScope('https://www.googleapis.com/auth/userinfo.email');
        $this->client->addScope('https://www.googleapis.com/auth/userinfo.profile');
    }

    public function auth()
    {
        if ($this->request->get('code') != null) {
            $this->client->authenticate($this->request->get('code'));
            $this->setToken();
            header('Location: ' . filter_var($this->redirectUrl, FILTER_SANITIZE_URL));
        }
    }

    public function setToken()
    {
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
        } else {
            $_SESSION['access_token'] = $this->client->getAccessToken();
        }
    }

    public function isAuth()
    {
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            try {
                $this->setToken();
                $this->client->verifyIdToken();
                return true;
            } catch (\Google_Auth_Exception $e) {
                unset($_SESSION['access_token']);
                return false;
            }
        }
        return false;
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function read($limit = 10)
    {
        $this->setToken();

        $out = array();

        $tokenAttributes = $this->client->verifyIdToken()->getAttributes();
        $email = $tokenAttributes['payload']['email'];

        $service = new \Google_Service_Gmail($this->client);

        $optParams = [];
        $optParams['maxResults'] = $limit; // Return Only 5 Messages
        $optParams['labelIds'] = 'INBOX'; // Only show messages in Inbox
        $messages = $service->users_messages->listUsersMessages($email, $optParams);
        foreach ($messages->getMessages() as $msg) {
            $decodedMessage = $this->readMessage($msg->getId(), $service);
            $out[] = $decodedMessage;
        }

        return $out;
    }

    /**
     * @param $messageId
     * @internal param $msg
     * @return string
     */
    public function readMessage($messageId)
    {
        $out = array();
        $subject = null;
        $message = null;
        $optParamsGet = [];

        $service = new \Google_Service_Gmail($this->client);

        $optParamsGet['format'] = 'full';

        $tokenAttributes = $this->client->verifyIdToken()->getAttributes();
        $email = $tokenAttributes['payload']['email'];
        $message = $service->users_messages->get($email, $messageId, $optParamsGet);

        $headers = $message->getPayload()->getHeaders();
        foreach ($headers as $header) {
            if ($header->name == 'Subject') {
                $subject = $header->value;
                break;
            }
        }

        $parts = $message->getPayload()->getParts();
        $body = $parts[0]['body'];
        $rawData = $body->data;
        $sanitizedData = strtr($rawData, '-_', '+/');
        $message = base64_decode($sanitizedData);

        $out['id'] = $messageId;
        $out['subject'] = $subject;
        $out['message'] = $message;

        return $out;
    }

    public function sendMessage($to, $subject, $body)
    {
        try {
            $message = new \Google_Service_Gmail_Message();
//            $message->setSender("from@google.com");
//            $message->addTo($to);
//            $message->setSubject($subject);
//            $message->setTextBody($body);
//            $message->send();
        } catch (InvalidArgumentException $e) {

        }
    }

}
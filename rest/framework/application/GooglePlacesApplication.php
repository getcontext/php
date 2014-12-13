<?php
namespace Framework\Application;

use Framework;
use Framework\Application;
use Framework\Rest;
use Framework\Actions;
use Framework\Request;

require_once(dirname(__FILE__) . '/../Application.php');
require_once(dirname(__FILE__) . '/../Rest.php');
require_once(dirname(__FILE__) . '/../Actions.php');
require_once(dirname(__FILE__) . '/../Request.php');


/**
 *
 * @author Andrzej Salamon <andrzej.salamon@gmail.com>
 * @package framework
 */
class GooglePlacesApplication extends Application implements Actions
{

    const URL = "https://maps.googleapis.com/maps/api/place/textsearch/json?";
    const API_KEY = "AIzaSyDOyzzVGpgUCG3lHryTIkC2f7OJX7jMlQQ";

    /**
     * gets app instance
     * @return  GooglePlacesApplication
     */
    public static function get()
    {
        return parent::get();
    }

    public function execute(Request $request)
    {
        $rest = new Rest();
        $restReq = $rest->processRequest();

        switch ($restReq->getMethod()) {
            case 'get':
                break;
            default:
                $rest->sendResponse(501);
        }

        $output = array();
        $output['result'] = 'error';

        if ($request->getAction() == 'getInfo') {
            $output = $this->getInfo($request->getParam('query'));
        }

        $rest->sendResponse(200, json_encode($output), 'application/json');
    }

    /**
     * @param $query
     * @return array
     */
    private function getInfo($query)
    {
        $output = array();
        $output['result'] = 'error';
        if (!$query) return $output;

        $options = array("query" => $query, "key" => self::API_KEY, "sensor" => "false");
        $url = $url = self::URL . http_build_query($options, '', '&');

        $json = file_get_contents($url);

        $output['result'] = 'success';

        $response = json_decode($json, true);

        foreach ($response['results'] as $row) {
            $dataRow = array('name' => $row['name'], 'address' => $row['formatted_address']);
            $output['data'][] = $dataRow;
        }
        return $output;
    }
}

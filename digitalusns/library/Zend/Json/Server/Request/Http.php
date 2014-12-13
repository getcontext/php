<?php

namespace Zend\Json\Server\Request;



require_once 'Zend/Json/Server/Request.php';


use Zend\Json\Server\Request as Request;





class  Http  extends  Request 
{
    /**
     * Raw JSON pulled from POST body
     * @var string
     */
    protected $_rawJson;

    /**
     * Constructor
     *
     * Pull JSON request from raw POST body and use to populate request.
     * 
     * @return void
     */
    public function __construct()
    {
        $json = file_get_contents('php://input');
        $this->_rawJson = $json;
        if (!empty($json)) {
            $this->loadJson($json);
        }
    }

    /**
     * Get JSON from raw POST body
     * 
     * @return string
     */
    public function getRawJson()
    {
        return $this->_rawJson;
    }
}

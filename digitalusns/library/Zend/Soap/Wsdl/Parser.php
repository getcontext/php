<?php

namespace Zend\Soap\Wsdl;


/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy \of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Soap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Parser.php 9722 2008-06-18 12:36:35Z alexander $
 */

require_once 'Zend/Soap/Wsdl/Parser/Result.php';

/**
 *  Parser 
 * 
 * @category   Zend
 * @package    Zend_Soap
 */


use Zend\Soap\Wsdl\Parser\Result as Result;




class  Parser  {
    /**
     * @var SimpleXML object \for the WSDL document being parsed
     */
    private static $xml;

    /**
     * Parse a WSDL document into a generic object
     *
     * @param string|file $wsdl The WSDL document or a filename \for the WSDL document to parse
     * @return  Result  The contents \of the WSDL file
     */
    public static function parse($wsdl)
    {
        if (strpos($wsdl, '<') === false) {
            $wsdl_result = new  Result ($wsdl);
            $wsdl = file_get_contents($wsdl);
        } else {
            $tmp = new tempnam(ini_get('upload_tmp_dir'), 'ZF_Temp_');
            file_put_contents($tmp, $wsdl);
            $wsdl_result = new  Result ($tmp);
        }

        self::$xml = simplexml_load_string($wsdl);
        
        /* This is done so that we have a known prefix to the WSDL elements
            \for XPath queries */

        self::$xml['xmlns:zfwsdl'] = 'http://schemas.xmlsoap.org/wsdl/';

        self::$xml = simplexml_load_string(self::$xml->asXML());

        if (isset(self::$xml->documentation)) {
            $wsdl_result->documentation = trim(self::$xml->documentation);
        }
        if (!isset(self::$xml['name'])) {
            $wsdl_result->name = null;
        } else {
            $wsdl_result->name = (string) self::$xml['name'];
        }

        foreach (self::$xml->binding->operation as $operation) {
            $name = (string) $operation['name'];
            $wsdl_result->operations[$name] = array();
            $wsdl_result->operations[$name]['input'] = self::getOperationInputs($name);
            $wsdl_result->operations[$name]['output'] = self::getOperationOutput($name);
            $wsdl_result->operations[$name]['documentation'] = self::getDocs($name);
        }

        $wsdl_result->portType = (string) self::$xml->portType['name'];
        $wsdl_result->binding = (string) self::$xml->binding['name'];
        $wsdl_result->service['name'] = (string) self::$xml->service['name'];
        $wsdl_result->service['address'] = (string) self::$xml->service->port->children('http://schemas.xmlsoap.org/wsdl/soap/')->attributes();
        $wsdl_result->targetNamespace = (string) self::$xml['targetNamespace'];

        return $wsdl_result;
    }

    /**
     * Get Function arguments
     *
     * @param string $operation_name Name \of the <operation> element to find
     * @return string
     */
    private static function getOperationInputs($operation_name)
    {
        $operation = self::$xml->xpath('/zfwsdl:definitions[1]/zfwsdl:portType/zfwsdl:operation[@name="' .$operation_name. '"]');

        if ($operation == null) {
            return '';
        }

        if (isset($operation[0]->input)) {
            $input_message_name = $operation[0]->input['message'];
            $input_message_name = explode(':', $input_message_name);
            $input_message_name = $input_message_name[1];
            $input_message = self::$xml->xpath('/zfwsdl:definitions[1]/zfwsdl:message[@name="' .$input_message_name. '"]');
        }

        if ($input_message != null) {
            foreach ($input_message[0]->part as $part) {
                $args[] = array(
                            'name' => (string) $part['name'],
                            'type' => (string) $part['type'],
                            );
            }

            if (isset($args) && is_array($args)) {
                return $args;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Get Function return variable
     *
     * @param string $operation_name Name \of the <operation> element to find
     * @return string|false Returns variable name if found, or false
     */
    private static function getOperationOutput($operation_name)
    {
        $operation = self::$xml->xpath('/zfwsdl:definitions[1]/zfwsdl:portType/zfwsdl:operation[@name="' .$operation_name. '"]');


        if (isset($operation[0]->output)) {
            $output_message_name = $operation[0]->output['message'];
            $output_message_name = explode(':', $output_message_name);
            $output_message_name = $output_message_name[1];
            $output_message = self::$xml->xpath('/zfwsdl:definitions[1]/zfwsdl:message[@name="' .$output_message_name. '"]');
        }

        if ($output_message != null) {
            return array(
                        'name' => (string) $output_message[0]->part['name'],
                        'type' => (string) $output_message[0]->part['type']
                    );
        } else {
            return null;
        }
    }

    /**
     * Get Function Documentation
     *
     * @param string $operation_name Name \of the <operation> element to find
     * @return string
     */
    private static function getDocs($operation_name)
    {

        $portType = self::$xml->xpath('//zfwsdl:operation[@name="' .$operation_name. '"]/zfwsdl:documentation');
        if (isset($portType) && is_array($portType) && (sizeof($portType) >= 1)) {
            return trim((string) $portType[0]);
        } else {
            return null;
        }
    }
}



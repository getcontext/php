<?php

namespace Zend\InfoCard\Xml\KeyInfo;


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
 * @package    \Zend\InfoCard
 * @subpackage \Zend\InfoCard_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Default.php 9094 2008-03-30 18:36:55Z thomas $
 */

/**
 *  InfoCardXmlKeyInfoAbstract 
 */
require_once 'Zend/InfoCard/Xml/KeyInfo/Abstract.php';

/**
 *  SecurityTokenReference 
 */
require_once 'Zend/InfoCard/Xml/SecurityTokenReference.php';

/**
 * An object representation \of a XML <KeyInfo> block which doesn't provide a namespace
 * In this context, it is assumed to mean that it is the type \of KeyInfo block which
 * contains the SecurityTokenReference
 *
 * @category   Zend
 * @package    \Zend\InfoCard
 * @subpackage \Zend\InfoCard_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\InfoCard\Xml\SecurityTokenReference as SecurityTokenReference;
use Zend\InfoCard\Xml\KeyInfo\KeyInfoAbstract as InfoCardXmlKeyInfoAbstract;
use Zend\InfoCard\Xml\Exception as InfoCardXmlException;
use Zend\InfoCard\Xml\Element as Element;




class  KeyInfoDefault  extends  InfoCardXmlKeyInfoAbstract 
{
    /**
     * Returns the object representation \of the SecurityTokenReference block
     *
     * @throws  InfoCardXmlException 
     * @return  SecurityTokenReference 
     */
    public function getSecurityTokenReference()
    {
        $this->registerXPathNamespace('o', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');

        list($sectokenref) = $this->xpath('//o:SecurityTokenReference');

        if(!($sectokenref instanceof  Element )) {
            throw new  InfoCardXmlException ('Could not locate the Security Token \Reference');
        }

        return  SecurityTokenReference ::getInstance($sectokenref);
    }
}

<?php

namespace Zend\Search\Lucene\Analysis;


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
 * @package    \Zend\Search\Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**  Token  */
require_once 'Zend/Search/Lucene/Analysis/Token.php';


/**
 * Token filter converts (normalizes) Token ore removes it from a token stream.
 *
 * @category   Zend
 * @package    \Zend\Search\Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */




use Zend\Search\Lucene\Analysis\Token as Token;



abstract class  TokenFilter 
{
    /**
     * Normalize Token or remove it (if null is returned)
     *
     * @param  Token  $srcToken
     * @return  Token 
     */
    abstract public function normalize( Token  $srcToken);
}


<?php

namespace Zend\Search\Lucene\Analysis\TokenFilter;


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


/**  TokenFilter  */
require_once 'Zend/Search/Lucene/Analysis/TokenFilter.php';


/**
 * Lower case Token filter.
 *
 * @category   Zend
 * @package    \Zend\Search\Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Search\Lucene\Analysis\TokenFilter as TokenFilter;
use Zend\Search\Lucene\Analysis\Token as Token;
use Zend\Search\Lucene\Exception as SearchLuceneException;





class  LowerCaseUtf8  extends  TokenFilter 
{
    /**
     * Object constructor
     */
    public function __construct()
    {
        if (!function_exists('mb_strtolower')) {
            // mbstring extension is disabled
            require_once 'Zend/Search/Lucene/Exception.php';
            throw new  SearchLuceneException ('Utf8 compatible lower case filter needs mbstring extension to be enabled.');
        }
    }
    
    /**
     * Normalize Token or remove it (if null is returned)
     *
     * @param  Token  $srcToken
     * @return  Token 
     */
    public function normalize( Token  $srcToken)
    {
        $newToken = new  Token (
                                     mb_strtolower($srcToken->getTermText(), 'UTF-8'),
                                     $srcToken->getStartOffset(),
                                     $srcToken->getEndOffset());

        $newToken->setPositionIncrement($srcToken->getPositionIncrement());

        return $newToken;
    }
}


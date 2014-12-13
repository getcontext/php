<?php

namespace Zend\Search\Lucene\Analysis\Analyzer\Common\Utf8Num;


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


/**  Utf8Num  */
require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/Utf8Num.php';

/**  LowerCaseUtf8  */
require_once 'Zend/Search/Lucene/Analysis/TokenFilter/LowerCaseUtf8.php';


/**
 * @category   Zend
 * @package    \Zend\Search\Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Search\Lucene\Analysis\TokenFilter\LowerCaseUtf8 as LowerCaseUtf8;
use Zend\Search\Lucene\Analysis\Analyzer\Common\Utf8Num as Utf8Num;






class  CaseInsensitive  extends  Utf8Num 
{
    public function __construct()
    {
        parent::__construct();

        $this->addFilter(new  LowerCaseUtf8 ());
    }
}


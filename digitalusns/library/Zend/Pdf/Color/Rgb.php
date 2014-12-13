<?php

namespace Zend\Pdf\Color;


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
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** \Zend\Pdf\Exception */
require_once 'Zend/Pdf/Exception.php';

/**  Color  */
require_once 'Zend/Pdf/Color.php';

/**  Numeric  */
require_once 'Zend/Pdf/Element/Numeric.php';


/**
 * RGB color implementation
 *
 * @category   Zend
 * @package    \Zend\Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Pdf\Element\Numeric as Numeric;
use Zend\Pdf\Color as Color;




class  Rgb  extends  Color 
{
    /**
     * Red level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var  Numeric 
     */
    private $_r;

    /**
     * Green level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var  Numeric 
     */
    private $_g;

    /**
     * Blue level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var  Numeric 
     */
    private $_b;


    /**
     * Object constructor
     *
     * @param float $r
     * @param float $g
     * @param float $b
     */
    public function __construct($r, $g, $b)
    {
        /** Clamp values to legal limits. */
        if ($r < 0) { $r = 0; }
        if ($r > 1) { $r = 1; }

        if ($g < 0) { $g = 0; }
        if ($g > 1) { $g = 1; }

        if ($b < 0) { $b = 0; }
        if ($b > 1) { $b = 1; }

        $this->_r = new  Numeric ($r);
        $this->_g = new  Numeric ($g);
        $this->_b = new  Numeric ($b);
    }

    /**
     * Instructions, which can be directly inserted into content stream
     * to switch color.
     * Color set instructions differ \for stroking and nonstroking operations.
     *
     * @param boolean $stroking
     * @return string
     */
    public function instructions($stroking)
    {
        return $this->_r->toString() . ' '
             . $this->_g->toString() . ' '
             . $this->_b->toString() .     ($stroking? " RG\n" : " rg\n");
    }
}


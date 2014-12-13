<?php

namespace Zend\Pdf\Resource\Font\Simple;


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
 * @package    \Zend\Pdf
 * @subpackage Fonts
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**  Simple  */
require_once 'Zend/Pdf/Resource/Font/Simple.php';


/**
 * Abstract class definition \for the standard 14 Type 1 PDF fonts.
 *
 * The standard 14 PDF fonts are guaranteed to be availble in any PDF viewer
 * implementation. As such, they do not require much data \for the font's
 * resource dictionary. The majority \of the data provided by subclasses is \for
 * the benefit \of our own layout code.
 *
 * The standard fonts and the corresponding subclasses that manage them:
 * <ul>
 *  <li>Courier - {@link  Standard _Courier}
 *  <li>Courier-Bold - {@link  Standard _CourierBold}
 *  <li>Courier-Oblique - {@link  Standard _CourierOblique}
 *  <li>Courier-BoldOblique - {@link  Standard _CourierBoldOblique}
 *  <li>Helvetica - {@link  Standard _Helvetica}
 *  <li>Helvetica-Bold - {@link  Standard _HelveticaBold}
 *  <li>Helvetica-Oblique - {@link  Standard _HelveticaOblique}
 *  <li>Helvetica-BoldOblique - {@link  Standard _HelveticaBoldOblique}
 *  <li>Symbol - {@link  Standard _Symbol}
 *  <li>Times - {@link  Standard _Times}
 *  <li>Times-Bold - {@link  Standard _TimesBold}
 *  <li>Times-Italic - {@link  Standard _TimesItalic}
 *  <li>Times-BoldItalic - {@link  Standard _TimesBoldItalic}
 *  <li>ZapfDingbats - {@link  Standard _ZapfDingbats}
 * </ul>
 *
 * Font objects should be normally be obtained from the factory methods
 * {@link  Font ::fontWithName} and {@link  Font ::fontWithPath}.
 *
 * @package    \Zend\Pdf
 * @subpackage Fonts
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */



use Zend\Pdf\Resource\Font\Simple as Simple;
use Zend\Pdf\Element\Name as Name;
use Zend\Pdf\Font as Font;



abstract class  Standard  extends  Simple 
{
  /**** Public Interface ****/


  /* Object Lifecycle */

    /**
     * Object constructor
     */
    public function __construct()
    {
        $this->_fontType =  Font ::TYPE_STANDARD;

    	parent::__construct();
        $this->_resource->Subtype  = new  Name ('Type1');
    }

}

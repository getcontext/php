<?php
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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/* This file previously contained constants \for the PDF module as a whole. Those
 * constants have been moved to keep the constant definitions closer to the
 * actual classes that use them.
 *
 * To repair your code, you will need to perform the following global
 * search-and-replace operations:
 *
 *   \Zend\Pdf_Const::PAGESIZE_                 -> \Zend\Pdf\Page::SIZE_
 *   \Zend\Pdf_Const::SHAPEDRAW_FILLNSTROKE     -> \Zend\Pdf\Page::SHAPE_DRAW_FILL_AND_STROKE
 *   \Zend\Pdf_Const::SHAPEDRAW_                -> \Zend\Pdf\Page::SHAPE_DRAW_
 *   \Zend\Pdf_Const::FILLMETHOD_NONZEROWINDING -> \Zend\Pdf\Page::FILL_METHOD_NON_ZERO_WINDING
 *   \Zend\Pdf_Const::FILLMETHOD_EVENODD        -> \Zend\Pdf\Page::FILL_METHOD_EVEN_ODD
 *   \Zend\Pdf_Const::LINEDASHING_              -> \Zend\Pdf\Page::LINE_DASHING_
 *   \Zend\Pdf_Const::PDF_                      -> \Zend\Pdf::PDF_
 *   \Zend\Pdf_Const::pdfDate()                 -> \Zend\Pdf::pdfDate()
 *
 * In addition, font object creation has been revamped. Font objects are now
 * instantiated via a factory methods. You will also need to perform the
 * following global search-and-replace operations:
 *
 *   new \Zend\Pdf\Font_Standard(\Zend\Pdf_Const::FONT_  ->
 *     \Zend\Pdf\Font::fontWithName(\Zend\Pdf\Font::FONT_
 *
 *   _BOLDITALIC  ->  _BOLD_ITALIC
 *
 * Finally, the old static arrays $pageSizeAliases and $standardFonts have been
 * completely removed. There are no replacements.
 *
 *
 * This file will remain in the repository \for a while but will be removed
 * completely by 0.2.0.
 *
 */

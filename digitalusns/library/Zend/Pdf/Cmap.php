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
 * @subpackage Fonts
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** \Zend\Pdf\Exception */
require_once 'Zend/Pdf/Exception.php';

/** \Zend\Pdf\Cmap\ByteEncoding */
require_once 'Zend/Pdf/Cmap/ByteEncoding.php';

/** \Zend\Pdf\Cmap\ByteEncoding\ByteEncodingStatic */
require_once 'Zend/Pdf/Cmap/ByteEncoding/Static.php';

/** \Zend\Pdf\Cmap\SegmentToDelta */
require_once 'Zend/Pdf/Cmap/SegmentToDelta.php';

/** \Zend\Pdf\Cmap\TrimmedTable */
require_once 'Zend/Pdf/Cmap/TrimmedTable.php';


/**
 * Abstract helper class \for {@link \Zend\Pdf\Resource\Font} which manages font
 * character maps.
 *
 * Defines the public interface \for concrete subclasses which are responsible
 * \for mapping Unicode characters to the font's glyph numbers. Also provides
 * shared utility methods.
 *
 * Cmap objects should ordinarily be obtained through the factory method
 * {@link cmapWithTypeData()}.
 *
 * The supported character map types are those found in the OpenType spec. For
 * additional detail on the internal binary format \of these tables, see:
 * <ul>
 *  <li>{@link http://developer.apple.com/textfonts/TTRefMan/RM06/Chap6cmap.html}
 *  <li>{@link http://www.microsoft.com/OpenType/OTSpec/cmap.htm}
 *  <li>{@link http://partners.adobe.com/public/developer/opentype/index_cmap.html}
 * </ul>
 *
 * @todo Write code \for \Zend\Pdf\FontCmap_HighByteMapping class.
 * @todo Write code \for \Zend\Pdf\FontCmap_MixedCoverage class.
 * @todo Write code \for \Zend\Pdf\FontCmap_TrimmedArray class.
 * @todo Write code \for \Zend\Pdf\FontCmap_SegmentedCoverage class.
 *
 * @package    \Zend\Pdf
 * @subpackage Fonts
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class \Zend\Pdf\Cmap
{
  /**** Class Constants ****/


  /* Cmap Table Types */

    /**
     * Byte Encoding character map table type.
     */
    const TYPE_BYTE_ENCODING = 0x00;

    /**
     * High Byte Mapping character map table type.
     */
    const TYPE_HIGH_BYTE_MAPPING = 0x02;

    /**
     * Segment Value to Delta Mapping character map table type.
     */
    const TYPE_SEGMENT_TO_DELTA = 0x04;

    /**
     * Trimmed Table character map table type.
     */
    const TYPE_TRIMMED_TABLE = 0x06;

    /**
     * Mixed Coverage character map table type.
     */
    const TYPE_MIXED_COVERAGE = 0x08;

    /**
     * Trimmed Array character map table type.
     */
    const TYPE_TRIMMED_ARRAY = 0x0a;

    /**
     * Segmented Coverage character map table type.
     */
    const TYPE_SEGMENTED_COVERAGE = 0x0c;

    /**
     * Static Byte Encoding character map table type. Variant \of
     * {@link TYPE_BYTEENCODING}.
     */
    const TYPE_BYTE_ENCODING_STATIC = 0xf1;

    /**
     * Unknown character map table type.
     */
    const TYPE_UNKNOWN = 0xff;


  /* Special Glyph Names */

    /**
     * Glyph representing missing characters.
     */
    const MISSING_CHARACTER_GLYPH = 0x00;



  /**** Public Interface ****/


  /* Factory Methods */

    /**
     * Instantiates the appropriate concrete subclass based on the type \of cmap
     * table and returns the instance.
     *
     * The cmap type must be one \of the following values:
     * <ul>
     *  <li>{@link \Zend\Pdf\Cmap::TYPE_BYTE_ENCODING}
     *  <li>{@link \Zend\Pdf\Cmap::TYPE_BYTE_ENCODING_STATIC}
     *  <li>{@link \Zend\Pdf\Cmap::TYPE_HIGH_BYTE_MAPPING}
     *  <li>{@link \Zend\Pdf\Cmap::TYPE_SEGMENT_TO_DELTA}
     *  <li>{@link \Zend\Pdf\Cmap::TYPE_TRIMMED_TABLE}
     *  <li>{@link \Zend\Pdf\Cmap::TYPE_MIXED_COVERAGE}
     *  <li>{@link \Zend\Pdf\Cmap::TYPE_TRIMMED_ARRAY}
     *  <li>{@link \Zend\Pdf\Cmap::TYPE_SEGMENTED_COVERAGE}
     * </ul>
     *
     * Throws an exception if the table type is invalid or the cmap table data
     * cannot be validated.
     *
     * @param integer $cmapType Type \of cmap.
     * @param mixed $cmapData Cmap table data. Usually a string or array.
     * @return \Zend\Pdf\Cmap
     * @throws \Zend\Pdf\Exception
     */
    public static function cmapWithTypeData($cmapType, $cmapData)
    {
        switch ($cmapType) {
            case \Zend\Pdf\Cmap::TYPE_BYTE_ENCODING:
                return new \Zend\Pdf\Cmap\ByteEncoding($cmapData);

            case \Zend\Pdf\Cmap::TYPE_BYTE_ENCODING_STATIC:
                return new \Zend\Pdf\Cmap\ByteEncoding\ByteEncodingStatic($cmapData);

            case \Zend\Pdf\Cmap::TYPE_HIGH_BYTE_MAPPING:
                throw new \Zend\Pdf\Exception('High byte mapping cmap currently unsupported',
                                             \Zend\Pdf\Exception::CMAP_TYPE_UNSUPPORTED);

            case \Zend\Pdf\Cmap::TYPE_SEGMENT_TO_DELTA:
                return new \Zend\Pdf\Cmap\SegmentToDelta($cmapData);

            case \Zend\Pdf\Cmap::TYPE_TRIMMED_TABLE:
                return new \Zend\Pdf\Cmap\TrimmedTable($cmapData);

            case \Zend\Pdf\Cmap::TYPE_MIXED_COVERAGE:
                throw new \Zend\Pdf\Exception('Mixed coverage cmap currently unsupported',
                                             \Zend\Pdf\Exception::CMAP_TYPE_UNSUPPORTED);

            case \Zend\Pdf\Cmap::TYPE_TRIMMED_ARRAY:
                throw new \Zend\Pdf\Exception('Trimmed array cmap currently unsupported',
                                             \Zend\Pdf\Exception::CMAP_TYPE_UNSUPPORTED);

            case \Zend\Pdf\Cmap::TYPE_SEGMENTED_COVERAGE:
                throw new \Zend\Pdf\Exception('Segmented coverage cmap currently unsupported',
                                             \Zend\Pdf\Exception::CMAP_TYPE_UNSUPPORTED);

            default:
                throw new \Zend\Pdf\Exception("Unknown cmap type: $cmapType",
                                             \Zend\Pdf\Exception::CMAP_UNKNOWN_TYPE);
        }
    }


  /* Abstract Methods */

    /**
     * Object constructor
     *
     * Parses the raw binary table data. Throws an exception if the table is
     * malformed.
     *
     * @param string $cmapData Raw binary cmap table data.
     * @throws \Zend\Pdf\Exception
     */
    abstract public function __construct($cmapData);

    /**
     * Returns an array \of glyph numbers corresponding to the Unicode characters.
     *
     * If a particular character doesn't exist in this font, the special 'missing
     * character glyph' will be substituted.
     *
     * See also {@link glyphNumberForCharacter()}.
     *
     * @param array $characterCodes Array \of Unicode character codes (code points).
     * @return array Array \of glyph numbers.
     */
    abstract public function glyphNumbersForCharacters($characterCodes);

    /**
     * Returns the glyph number corresponding to the Unicode character.
     *
     * If a particular character doesn't exist in this font, the special 'missing
     * character glyph' will be substituted.
     *
     * See also {@link glyphNumbersForCharacters()} which is optimized \for bulk
     * operations.
     *
     * @param integer $characterCode Unicode character code (code point).
     * @return integer Glyph number.
     */
    abstract public function glyphNumberForCharacter($characterCode);

    /**
     * Returns an array containing the Unicode characters that have entries in
     * this character map.
     *
     * @return array Unicode character codes.
     */
    abstract public function getCoveredCharacters();

    /**
     * Returns an array containing the glyphs numbers that have entries in this character map.
     * Keys are Unicode character codes (integers)
     * 
     * This functionality is partially covered by glyphNumbersForCharacters(getCoveredCharacters())
     * call, but this method do it in more effective way (prepare complete list instead \of searching 
     * glyph \for each character code).
     *
     * @internal
     * @return array Array representing <Unicode character code> => <glyph number> pairs.
     */
    abstract public function getCoveredCharactersGlyphs();


  /**** Internal Methods ****/


  /* Internal Utility Methods */

    /**
     * Extracts a signed 2-byte integer from a string.
     *
     * Integers are always big-endian. Throws an exception if the index is out
     * \of range.
     *
     * @param string &$data
     * @param integer $index Position in string \of integer.
     * @return integer
     * @throws \Zend\Pdf\Exception
     */
    protected function _extractInt2(&$data, $index)
    {
        if (($index < 0) | (($index + 1) > strlen($data))) {
            throw new \Zend\Pdf\Exception("Index out \of range: $index",
                                         \Zend\Pdf\Exception::INDEX_OUT_OF_RANGE);
        }
        $number = ord($data[$index]);
        if (($number & 0x80) == 0x80) {    // negative
            $number = ~((((~ $number) & 0xff) << 8) | ((~ ord($data[++$index])) & 0xff));
        } else {
            $number = ($number << 8) | ord($data[++$index]);
        }
        return $number;
    }

    /**
     * Extracts an unsigned 2-byte integer from a string.
     *
     * Integers are always big-endian. Throws an exception if the index is out
     * \of range.
     *
     * @param string &$data
     * @param integer $index Position in string \of integer.
     * @return integer
     * @throws \Zend\Pdf\Exception
     */
    protected function _extractUInt2(&$data, $index)
    {
        if (($index < 0) | (($index + 1) > strlen($data))) {
            throw new \Zend\Pdf\Exception("Index out \of range: $index",
                                         \Zend\Pdf\Exception::INDEX_OUT_OF_RANGE);
        }
        $number = (ord($data[$index]) << 8) | ord($data[++$index]);
        return $number;
    }

    /**
     * Extracts an unsigned 4-byte integer from a string.
     *
     * Integers are always big-endian. Throws an exception if the index is out
     * \of range.
     *
     * NOTE: If you ask \for a 4-byte unsigned integer on a 32-bit machine, the
     * resulting value WILL BE SIGNED because PHP uses signed integers internally
     * \for everything. To guarantee portability, be sure to use bitwise or
     * similar operators on large integers!
     *
     * @param string &$data
     * @param integer $index Position in string \of integer.
     * @return integer
     * @throws \Zend\Pdf\Exception
     */
    protected function _extractUInt4(&$data, $index)
    {
        if (($index < 0) | (($index + 3) > strlen($data))) {
            throw new \Zend\Pdf\Exception("Index out \of range: $index",
                                         \Zend\Pdf\Exception::INDEX_OUT_OF_RANGE);
        }
        $number = (ord($data[$index]) << 24) | (ord($data[++$index]) << 16) |
                  (ord($data[++$index]) << 8) | ord($data[++$index]);
        return $number;
    }

}

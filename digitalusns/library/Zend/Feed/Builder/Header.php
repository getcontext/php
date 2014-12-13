<?php

namespace Zend\Feed\Builder;



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
 * @package    \Zend\Feed
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Header.php 8064 2008-02-16 10:58:39Z thomas $
 */


/**
 * @see  Loader 
 */
require_once 'Zend/Loader.php';

/**
 * @see  Itunes 
 */
require_once 'Zend/Feed/Builder/Header/Itunes.php';

/**
 * @see  Uri 
 */
require_once 'Zend/Uri.php';


/**
 * Header \of a custom build feed
 *
 * Classes implementing the \Zend\Feed\Builder\BuilderInterface interface
 * uses this class to describe the header \of a feed
 *
 * @category   Zend
 * @package    \Zend\Feed
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


use Zend\Feed\Builder\Header\Itunes as Itunes;
use Zend\Feed\Builder\Exception as FeedBuilderException;
use Zend\Validate\EmailAddress as EmailAddress;
use Zend\Validate\Int as Int;
use Zend\Uri\Http as Http;
use Zend\Loader as Loader;
use Zend\Uri as Uri;




class  Header  extends \ArrayObject
{
    /**
     * Constructor
     *
     * @param  string $title title \of the feed
     * @param  string $link canonical url \of the feed
     * @param  string $charset charset \of the textual data
     * @return void
     */
    public function __construct($title, $link, $charset = 'utf-8')
    {
        $this->offsetSet('title', $title);
        $this->offsetSet('link', $link);
        $this->offsetSet('charset', $charset);
        $this->setLastUpdate(time())
             ->setGenerator('\Zend\Feed');
    }

    /**
     * Read only properties accessor
     *
     * @param  string $name property to read
     * @return mixed
     */
    public function __get($name)
    {
        if (!$this->offsetExists($name)) {
            return NULL;
        }

        return $this->offsetGet($name);
    }

    /**
     * Write properties accessor
     *
     * @param string $name  name \of the property to set
     * @param mixed  $value value to set
     * @return void
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * Isset accessor
     *
     * @param  string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset accessor
     *
     * @param  string $key
     * @return void
     */
    public function __unset($key)
    {
        if ($this->offsetExists($key)) {
            $this->offsetUnset($key);
        }
    }

    /**
     * Timestamp \of the update date
     *
     * @param  int $lastUpdate
     * @return  Header 
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->offsetSet('lastUpdate', $lastUpdate);
        return $this;
    }

    /**
     * Timestamp \of the publication date
     *
     * @param  int $published
     * @return  Header 
     */
    public function setPublishedDate($published)
    {
        $this->offsetSet('published', $published);
        return $this;
    }

    /**
     * Short description \of the feed
     *
     * @param  string $description
     * @return  Header 
     */
    public function setDescription($description)
    {
        $this->offsetSet('description', $description);
        return $this;
    }

    /**
     * Sets the author \of the feed
     *
     * @param  string $author
     * @return  Header 
     */
    public function setAuthor($author)
    {
        $this->offsetSet('author', $author);
        return $this;
    }

    /**
     * Sets the author's email
     *
     * @param  string $email
     * @return  Header 
     * @throws  FeedBuilderException 
     */
    public function setEmail($email)
    {
         Loader ::loadClass('\Zend\Validate\EmailAddress');
        $validate = new  EmailAddress ();
        if (!$validate->isValid($email)) {
            /**
             * @see  FeedBuilderException 
             */
            require_once 'Zend/Feed/Builder/Exception.php';
            throw new  FeedBuilderException ("you have to set a valid email address into the email property");
        }
        $this->offsetSet('email', $email);
        return $this;
    }

    /**
     * Sets the copyright notice
     *
     * @param  string $copyright
     * @return  Header 
     */
    public function setCopyright($copyright)
    {
        $this->offsetSet('copyright', $copyright);
        return $this;
    }

    /**
     * Sets the image \of the feed
     *
     * @param  string $image
     * @return  Header 
     */
    public function setImage($image)
    {
        $this->offsetSet('image', $image);
        return $this;
    }

    /**
     * Sets the generator \of the feed
     *
     * @param  string $generator
     * @return  Header 
     */
    public function setGenerator($generator)
    {
        $this->offsetSet('generator', $generator);
        return $this;
    }

    /**
     * Sets the language \of the feed
     *
     * @param  string $language
     * @return  Header 
     */
    public function setLanguage($language)
    {
        $this->offsetSet('language', $language);
        return $this;
    }

    /**
     * Email address \for person responsible \for technical issues
     * Ignored if atom is used
     *
     * @param  string $webmaster
     * @return  Header 
     * @throws  FeedBuilderException 
     */
    public function setWebmaster($webmaster)
    {
         Loader ::loadClass('\Zend\Validate\EmailAddress');
        $validate = new  EmailAddress ();
        if (!$validate->isValid($webmaster)) {
            /**
             * @see  FeedBuilderException 
             */
            require_once 'Zend/Feed/Builder/Exception.php';
            throw new  FeedBuilderException ("you have to set a valid email address into the webmaster property");
        }
        $this->offsetSet('webmaster', $webmaster);
        return $this;
    }

    /**
     * How long in minutes a feed can be cached before refreshing
     * Ignored if atom is used
     *
     * @param  int $ttl
     * @return  Header 
     * @throws  FeedBuilderException 
     */
    public function setTtl($ttl)
    {
         Loader ::loadClass('\Zend\Validate\Int');
        $validate = new  Int ();
        if (!$validate->isValid($ttl)) {
            /**
             * @see  FeedBuilderException 
             */
            require_once 'Zend/Feed/Builder/Exception.php';
            throw new  FeedBuilderException ("you have to set an integer value to the ttl property");
        }
        $this->offsetSet('ttl', $ttl);
        return $this;
    }

    /**
     * PICS rating \for the feed
     * Ignored if atom is used
     *
     * @param  string $rating
     * @return  Header 
     */
    public function setRating($rating)
    {
        $this->offsetSet('rating', $rating);
        return $this;
    }

    /**
     * Cloud to be notified \of updates \of the feed
     * Ignored if atom is used
     *
     * @param  string| Http  $uri
     * @param  string               $procedure procedure to call, e.g. myCloud.rssPleaseNotify
     * @param  string               $protocol  protocol to use, e.g. soap or xml-rpc
     * @return  Header 
     * @throws  FeedBuilderException 
     */
    public function setCloud($uri, $procedure, $protocol)
    {
        if (is_string($uri) &&  Http ::check($uri)) {
            $uri =  Uri ::factory($uri);
        }
        if (!$uri instanceof  Http ) {
            /**
             * @see  FeedBuilderException 
             */
            require_once 'Zend/Feed/Builder/Exception.php';
            throw new  FeedBuilderException ('Passed parameter is not a valid HTTP URI');
        }
        if (!$uri->getPort()) {
            $uri->setPort(80);
        }
        $this->offsetSet('cloud', array('uri' => $uri,
                                        'procedure' => $procedure,
                                        'protocol' => $protocol));
        return $this;
    }

    /**
     * A text input box that can be displayed with the feed
     * Ignored if atom is used
     *
     * @param  string $title       the label \of the Submit button in the text input area
     * @param  string $description explains the text input area
     * @param  string $name        the name \of the text object in the text input area
     * @param  string $link        the URL \of the CGI script that processes text input requests
     * @return  Header 
     */
    public function setTextInput($title, $description, $name, $link)
    {
        $this->offsetSet('textInput', array('title' => $title,
                                            'description' => $description,
                                            'name' => $name,
                                            'link' => $link));
        return $this;
    }

    /**
     * Hint telling aggregators which hours they can skip
     * Ignored if atom is used
     *
     * @param  array $hours list \of hours in 24 format
     * @return  Header 
     * @throws  FeedBuilderException 
     */
    public function setSkipHours(array $hours)
    {
        if (count($hours) > 24) {
            /**
             * @see  FeedBuilderException 
             */
            require_once 'Zend/Feed/Builder/Exception.php';
            throw new  FeedBuilderException ("you can not have more than 24 rows in the skipHours property");
        }
        foreach ($hours as $hour) {
            if ($hour < 0 || $hour > 23) {
                /**
                 * @see  FeedBuilderException 
                 */
                require_once 'Zend/Feed/Builder/Exception.php';
                throw new  FeedBuilderException ("$hour has te be between 0 and 23");
            }
        }
        $this->offsetSet('skipHours', $hours);
        return $this;
    }

    /**
     * Hint telling aggregators which days they can skip
     * Ignored if atom is used
     *
     * @param  array $days list \of days to skip, e.g. Monday
     * @return  Header 
     * @throws  FeedBuilderException 
     */
    public function setSkipDays(array $days)
    {
        if (count($days) > 7) {
            /**
             * @see  FeedBuilderException 
             */
            require_once 'Zend/Feed/Builder/Exception.php';
            throw new  FeedBuilderException ("you can not have more than 7 days in the skipDays property");
        }
        $valid = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
        foreach ($days as $day) {
            if (!in_array(strtolower($day), $valid)) {
                /**
                 * @see  FeedBuilderException 
                 */
                require_once 'Zend/Feed/Builder/Exception.php';
                throw new  FeedBuilderException ("$day is not a valid day");
            }
        }
        $this->offsetSet('skipDays', $days);
        return $this;
    }

    /**
     * Sets the iTunes rss extension
     *
     * @param   Itunes  $itunes
     * @return  Header 
     */
    public function setITunes( Itunes  $itunes)
    {
        $this->offsetSet('itunes', $itunes);
        return $this;
    }
}

<?php

namespace DSF\Command;

 

/**
 * DSF CMS
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://digitalus-media.com/license/new-bsd
 * If you did not receive a copy \of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@digitalus-media.com so we can send you a copy immediately.
 *
 * @category   DSF CMS
 * @package   DSF_Core_Library
 * @copyright  Copyright (c) 2007 - 2008,  Digitalus Media USA (digitalus-media.com)
 * @license    http://digitalus-media.com/license/new-bsd     New BSD License
 * @version    $Id: List.php Tue Dec 25 20:01:46 EST 2007 20:01:46 forrest lyman $
 */


use DSF\Command\CommandAbstract as CommandAbstract;
use DSF\Filesystem\File as File;
use DSF\Toolbox\Regex as Regex;
use DSF\Command as Command;





class  CommandList  extends  CommandAbstract  
{
	/**
	 * display all \of the current commands
	 *
	 */
    function run()
    {
        $commands =  File ::getFilesByType( Command ::PATH_TO_COMMANDS, 'php');
        foreach ($commands as $command)
        {
            //clean up the list
            if($command != 'Abstract.php' && $command != 'List.php')
            {
                $cleanCommand =  Regex ::stripFileExtension($command);
                $link = "<a class='loadCommand' href='#' title='" . $cleanCommand . "'>" . $cleanCommand . "</a>";
                $this->log($link);
            }
        }
    }
    
    /**
     * returns info about the current command
     *
     */
    function info()
    {
        $this->log('The list command lists all \of the available functions.  Double click a function to open it.');
    }
}

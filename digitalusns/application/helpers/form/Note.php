<?php

namespace DSF\View\Helper\Form;








class  Note 
{

	/**
	 * pretty simple stuff.  just wraps the note.
	 */
	public function \Note($note){
		$xhtml = "<p class='note'>{$note}</p>";
	   return $xhtml;
	}
}

<?php

namespace DSF\View\Helper\General;








 class  FormatPercentage 
 {

	/**
	 * this helper formats a percentage
	 */
	public function FormatPercentage($num){
		return number_format($num,2) . " %";
	}
 }
 ?>
 

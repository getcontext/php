<?php

namespace DSF\View\Helper\General;








class  FormatCurrency 
{

	/**
	 * comments
	 */
	public function FormatCurrency($num){
		return "$" . number_format($num,2);
	}
}

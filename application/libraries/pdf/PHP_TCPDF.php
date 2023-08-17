<?php

	include_once ('TCPDF-main/tcpdf.php');
	// include_once ('tcpdf/tcpdf.php');
	

	class PHP_TCPDF extends TCPDF
	{
	    function __construct()
	    {
	        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	        // set image scale factor
	        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

	        // set some language-dependent strings (optional)
	        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	            require_once(dirname(__FILE__).'/lang/eng.php');
	            $this->setLanguageArray($l);
	        }

	        // ---------------------------------------------------------

	        // set font
	        $this->SetFont('msungstdlight', '', 10);
	    }
	}
?>
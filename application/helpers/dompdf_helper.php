<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use Dompdf\Dompdf;

function pdf_create($html, $filename='', $paper_size, $stream=TRUE) 
{
    require_once 'dompdf/autoload.inc.php';

    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    
    if($paper_size == 'a4'){
        $dompdf->set_paper('a4','portrait');    
    }elseif($paper_size == 'letter'){
        $dompdf->set_paper('letter','portrait');   
    }
    
    $dompdf->render();
    if ($stream) {
        $dompdf->stream($filename.".pdf", array('Attachment'=>false));
    } else {
        return $dompdf->output();
    }
}


function pdf_download($html, $filename='', $stream=TRUE) 
{
    require_once 'dompdf/autoload.inc.php';

    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->set_paper('a4','portrait');
    $dompdf->render();
    if ($stream) {
        $dompdf->stream($filename);
    } else {
        return $dompdf->output();
    }
}

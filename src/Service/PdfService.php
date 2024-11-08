<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
class PdfService
{
    private $domPdf;

    public function __construct() {
        $this->domPdf = new DomPdf();

        $pdfOptions = new Options();

        $pdfOptions->set('defaultFont', 'Garamond');

        $this->domPdf->setOptions($pdfOptions);
    }

    public function showPdfFile($html): void
    {

        $this->domPdf->loadHtml($html);

        $this->domPdf->setOptions(['isPhpEnabled' => true]);
        $this->domPdf->setOptions(['isHtml5ParserEnabled' => true]);
        $this->domPdf->setOptions(['isFontSubsettingEnabled' => true]);
        $this->domPdf->setOptions(['isRemoteEnabled' => true]);
        $this->domPdf->setOptions(['defaultFont' => 'DejaVu Serif']);
        $this->domPdf->setOptions(['isPhpEnabled' => true]);
        $this->domPdf->setOptions(['isHtml5ParserEnabled' => true]);
        $this->domPdf->setOptions(['isFontSubsettingEnabled' => true]);
        $this->domPdf->setOptions(['isRemoteEnabled' => true]);

        $this->domPdf->setPaper('A4','landscape');
        $this->domPdf->render();
        $this->domPdf->stream("details.pdf", [
            'Attachment' => true
        ]);
    }

    public function generateBinaryPDF($html) {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }
}
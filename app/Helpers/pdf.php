<?php

use Barryvdh\DomPDF\Facade\Pdf;

function exportPdf(
    string $view,
    array  $data,
    bool   $download,
    string $filename = 'document.pdf'
) {
    $pdf = Pdf::loadView($view, $data)
        ->setPaper('a4', 'portrait')
        ->setWarnings(false);

    return $download
        ? $pdf->download($filename)
        : $pdf->stream($filename);
}

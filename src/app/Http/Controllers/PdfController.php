<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpdf\Mpdf;

class PdfController extends Controller
{
    public function exportPdf()
    {
        // Inisialisasi mPDF
        $mpdf = new Mpdf();

        // Contoh HTML untuk isi PDF
        $html = '
            <h2 style="text-align:center;">Laporan Data Tanah</h2>
            <table border="1" cellpadding="8" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pemilik</th>
                        <th>Alamat</th>
                        <th>Luas (m²)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Budi Santoso</td>
                        <td>Jl. Merdeka No. 12</td>
                        <td>250</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Siti Aminah</td>
                        <td>Jl. Pahlawan No. 45</td>
                        <td>180</td>
                    </tr>
                </tbody>
            </table>
        ';

        // Tulis HTML ke PDF
        $mpdf->WriteHTML($html);

        // Output file PDF langsung ke browser
        return response($mpdf->Output('laporan-tanah.pdf', 'I'))
            ->header('Content-Type', 'application/pdf');
    }
}

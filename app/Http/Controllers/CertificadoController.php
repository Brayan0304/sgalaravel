<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Addstaff;
use TCPDF;

class CertificadoController extends Controller
{
    public function store(Request $request)
    {
        $reportId = $request->input('report_id');
        $employeeIds = explode(';', $request->input('employee_ids'));

        $request->validate([
            'report_id' => 'required',
            'employee_ids' => 'required',
        ]);

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Reporte');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 20, 15);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Write(0, "Datos del reporte con ID: $reportId");

        foreach ($employeeIds as $id) {
            $employee = Addstaff::find($id);
            if ($employee) {
                $pdf->Ln();
                $pdf->Write(0, "Empleado: {$employee->name} {$employee->apellidos}, Cargo: {$employee->cargo}");
            }
        }

        $pdfOutput = $pdf->Output("reporte_{$reportId}.pdf", 'S');

        return response($pdfOutput, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="reporte_'.$reportId.'.pdf"');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Addstaff;
use TCPDF;

class CertificadoController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Conexión directa a la base de datos usando PDO
            $connection = DB::connection('pgsql');
            $connection->getPdo(); // Verificar la conexión

            // Validación de los parámetros de entrada
            $request->validate([
                'report_id' => 'required|integer',
                'employee_ids' => 'required|string',
            ]);

            // Extraer los IDs de empleados del string
            $reportId = $request->input('report_id');
            $employeeIds = explode(';', $request->input('employee_ids'));

            // Realizar la consulta con parámetros bind para evitar inyecciones SQL
            $query = "SELECT * FROM reports WHERE id = :report_id";
            $report = DB::select($query, ['report_id' => $reportId]);

            // Si no se encuentra el reporte
            if (empty($report)) {
                return response()->json(['error' => 'Reporte no encontrado'], 404);
            }

            // Almacenar los resultados en una variable
            $reportData = $report[0];

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle('Reporte');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(($reportData->margen_izquierdo * 10), ($reportData->margen_superior * 10), ($reportData->margen_derecho * 10));

            // Recorrer los empleados encontrados y agregar su información al PDF
            foreach ($employeeIds as $id) {
                $pdf->AddPage();
                $pdf->SetFont('helvetica', 'B', $reportData->tamano_letra_titulo);

                $pdf->Write(0, $reportData->titulo, '', 0, 'C', true);
                $pdf->Ln(5);

                $pdf->SetFont('helvetica', 'B', $reportData->tamano_letra_titulo_2);
                $pdf->Write(0, $reportData->titulo_2, '', 0, 'C', true);
                $pdf->Ln(15);

                // Buscar todos los empleados de una vez
                $employees = Addstaff::whereIn('id', $employeeIds)->get();

                // Buscar al empleado por ID
                $employee = $employees->firstWhere('id', $id);

                $nombreCompleto = $employee->name . " " . $employee->apellidos;
                $reportData->parrafo = str_replace('{nombreCompleto}', $nombreCompleto, $reportData->parrafo);
                $reportData->parrafo = str_replace('{documento}', $employee->id, $reportData->parrafo);
                $reportData->parrafo = str_replace('{cargo}', $employee->cargo, $reportData->parrafo);

                $pdf->SetFont('helvetica', '', $reportData->tamano_letra_parrafo);
                $pdf->Write(0, $reportData->parrafo, '', 0, 'J', true);
                $pdf->Ln(10);

                $pdf->SetFont('helvetica', '', $reportData->tamano_letra_expedicion);
                $reportData->expedicion = str_replace('{dia}', date('d'), $reportData->expedicion);
                // Obtener el nombre del mes en palabras
                setlocale(LC_TIME, 'es_ES.UTF-8');

                $mes = strftime('%B', strtotime(date('Y-m-d')));
                $reportData->expedicion = str_replace('{mes}', $mes, $reportData->expedicion);
                $reportData->expedicion = str_replace('{anio}', date('Y'), $reportData->expedicion);
                $pdf->Write(0, $reportData->expedicion, '', 0, 'J', true);

            }

            // Generar y devolver el archivo PDF directamente al navegador
            return response()->stream(function () use ($pdf) {
                $pdf->Output('reporte.pdf', 'I');  // 'I' muestra el archivo en el navegador
            }, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="reporte_' . $reportId . '.pdf"',
            ]);

        } catch (\Exception $e) {
            // Captura cualquier error y muestra un mensaje de error
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }

}

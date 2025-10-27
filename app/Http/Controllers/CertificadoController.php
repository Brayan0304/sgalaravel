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
            // El campo report_id ahora es UUID/string (el modelo Reports genera UUID)
            $request->validate([
                'report_id' => 'required|string',
                'employee_ids' => 'required|string',
            ]);
            
            // Extraer los IDs de empleados del string y normalizarlos a strings
            $reportId = $request->input('report_id');
            $employeeIds = explode(';', $request->input('employee_ids'));
            // Trim, filtrar vacíos y asegurar que todos sean strings (Postgres requiere tipos coincidentes)
            $employeeIds = array_filter(array_map('trim', $employeeIds), fn($v) => $v !== '');
            $employeeIds = array_values(array_map('strval', $employeeIds));

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

                // Cargar todos los empleados de una vez y solo las columnas necesarias
                $employees = Addstaff::select(['id', 'name', 'apellidos', 'cargo'])
                    ->whereIn('id', $employeeIds)
                    ->get()
                    ->keyBy(function($item) { return (string) $item->id; });

                // Recorrer los ids y agregar una página por cada empleado encontrado
                foreach ($employeeIds as $eid) {
                    $eid = (string) trim($eid);

                    if (! isset($employees[$eid])) {
                        // Si no existe el empleado, saltar
                        continue;
                    }

                    $employee = $employees[$eid];

                    $pdf->AddPage();
                    $pdf->SetFont('helvetica', 'B', $reportData->tamano_letra_titulo);

                    $pdf->Write(0, $reportData->titulo, '', 0, 'C', true);
                    $pdf->Ln(5);

                    $pdf->SetFont('helvetica', 'B', $reportData->tamano_letra_titulo_2);
                    $pdf->Write(0, $reportData->titulo_2, '', 0, 'C', true);
                    $pdf->Ln(15);

                    // Usar copia de los campos para no mutar la plantilla original
                    $paragraph = $reportData->parrafo;
                    $nombreCompleto = $employee->name . " " . $employee->apellidos;
                    $paragraph = str_replace('{nombreCompleto}', $nombreCompleto, $paragraph);
                    $paragraph = str_replace('{documento}', $employee->id, $paragraph);
                    $paragraph = str_replace('{cargo}', $employee->cargo, $paragraph);

                    $pdf->SetFont('helvetica', '', $reportData->tamano_letra_parrafo);
                    $pdf->Write(0, $paragraph, '', 0, 'J', true);
                    $pdf->Ln(10);

                    $pdf->SetFont('helvetica', '', $reportData->tamano_letra_expedicion);
                    $expedicion = $reportData->expedicion;
                    $expedicion = str_replace('{dia}', date('d'), $expedicion);
                    // Obtener el nombre del mes en palabras
                    setlocale(LC_TIME, 'es_ES.UTF-8');

                    $mes = strftime('%B', strtotime(date('Y-m-d')));
                    $expedicion = str_replace('{mes}', $mes, $expedicion);
                    $expedicion = str_replace('{anio}', date('Y'), $expedicion);
                    $pdf->Write(0, $expedicion, '', 0, 'J', true);
                }

            }

            // Generar y devolver el archivo PDF directamente al navegador
            return response()->stream(function () use ($pdf) {
                $pdf->Output('reporte.pdf', 'I');  // 'I' muestra el archivo en el navegador
            }, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="reporte.pdf"',
            ]);

        } catch (\Exception $e) {
            // Captura cualquier error y muestra un mensaje de error
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }

}

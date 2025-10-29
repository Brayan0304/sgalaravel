<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Addstaff;
use App\Models\Addsalary;
use Carbon\Carbon;
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

                // Cargar todos los empleados de una vez (todas las columnas)
                $employees = Addstaff::whereIn('id', $employeeIds)
                    ->get()
                    ->keyBy(function ($item) {
                        return (string) $item->id;
                    });


                if (! isset($employees[$id])) {
                    // Si no existe el empleado, saltar
                    continue;
                }

                $employee = $employees[$id];

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
                $paragraph = str_replace('{Nombre del colaborador}', $nombreCompleto, $paragraph);
                $paragraph = str_replace('{Número de documento}', $employee->id, $paragraph);
                $paragraph = str_replace('{nombre del cargo}', $employee->cargo, $paragraph);
                $paragraph = str_replace('{Ciudad de expedición del documento}', $employee->municipio_expedicion, $paragraph);

                // Formatear la fecha de ingreso como "1 de octubre del 2025" en español
                $fechaIngresoFormatted = '';
                if (!empty($employee->fecha_ingreso)) {
                    try {
                        $fechaIngresoFormatted = Carbon::parse($employee->fecha_ingreso)
                            ->locale('es')
                            ->isoFormat('D [de] MMMM [del] YYYY');
                    } catch (\Exception $e) {
                        // Fallback simple si la parse falla
                        $fechaIngresoFormatted = Carbon::parse($employee->fecha_ingreso)->format('d/m/Y');
                    }
                }

                $paragraph = str_replace('{fecha de ingreso}', $fechaIngresoFormatted, $paragraph);

                $paragraph = str_replace('el {fecha de salida}', (!empty($employee->fecha_salida) ? 'el ' . Carbon::parse($employee->fecha_salida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : 'la actualidad'), $paragraph);

                // Obtener el salario desde la tabla addsalaries (modelo Addsalary)
                $cantidadValue = '';
                try {
                    $salary = Addsalary::where('id_empleado', (string) $employee->id)
                        ->orderByDesc('created_at')
                        ->first();

                    if (! $salary) {
                        $salary = Addsalary::where('id_empleado', (string) $employee->id)
                            ->orderByDesc('id')
                            ->first();
                    }

                    if ($salary && isset($salary->salario)) {
                        // Formatear el salario como número con separador de miles
                        $cantidadValue = number_format($salary->salario, 0, ',', '.');
                    } else {
                        $cantidadValue = $employee->cantidad ?? '';
                    }
                } catch (\Exception $e) {
                    $cantidadValue = $employee->cantidad ?? '';
                }

                $paragraph = str_replace('mensual', $salary->tiempo_pago ?? 'mensual', $paragraph);

                $paragraph = str_replace('{cantidad}', $cantidadValue, $paragraph);

                $pdf->SetFont('helvetica', '', $reportData->tamano_letra_parrafo);
                $pdf->Write(0, $paragraph, '', 0, 'J', true);
                $pdf->Ln(10);

                $pdf->SetFont('helvetica', '', $reportData->tamano_letra_expedicion);
                $expedicion = $reportData->expedicion;
                $expedicion = str_replace('{dia}', date('d'), $expedicion);
                // Obtener el nombre del mes en español de forma fiable (cross-platform)
                // Usamos Carbon para evitar problemas de locales en Windows
                $mes = Carbon::now()->locale('es')->isoFormat('MMMM');
                $expedicion = str_replace('{mes}', $mes, $expedicion);
                $expedicion = str_replace('{anio}', date('Y'), $expedicion);
                $pdf->Write(0, $expedicion, '', 0, 'J', true);
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class CsvController extends Controller
{
    private $storagePath = 'app/'; // Prefijo para almacenar archivos

    /**
     * Lista todos los ficheros CSV de la carpeta storage/app.
     *
     * @return JsonResponse La respuesta en formato JSON.
     */
    public function index(): JsonResponse
    {
        $files = Storage::files($this->storagePath); // Archivos en la ruta 'app/'
        $csvFiles = array_filter($files, fn($file) => pathinfo($file, PATHINFO_EXTENSION) === 'csv');

        return response()->json([
            'mensaje' => 'Listado de ficheros',
            'contenido' => array_map(fn($file) => basename($file), $csvFiles),
        ]);
    }

    /**
     * Crea un nuevo fichero CSV con el contenido proporcionado.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $filename = $request->input('filename');
        $content = $request->input('content');

        if (!$filename || !$content) {
            return response()->json(['mensaje' => 'Faltan parámetros'], 422);
        }

        $path = $this->storagePath . $filename;

        if (Storage::exists($path)) {
            return response()->json(['mensaje' => 'El fichero ya existe'], 409);
        }

        Storage::put($path, $content);
        return response()->json(['mensaje' => 'Guardado con éxito']);
    }

    /**
     * Muestra el contenido de un fichero CSV.
     *
     * @param string $id
     * @return JsonResponse
     */
    /**
 * Muestra el contenido de un fichero CSV.
 *
 * @param string $id
 * @return JsonResponse
 */
public function show(string $id): JsonResponse
{
    $path = $this->storagePath . $id;

    // Verificar si el archivo existe
    if (!Storage::exists($path)) {
        return response()->json(['mensaje' => 'Fichero no encontrado'], 404);
    }

    // Obtener contenido del archivo
    $content = Storage::get($path);
    $lines = array_filter(array_map('trim', explode("\n", $content))); // Dividir por líneas y eliminar vacías

    // Si el archivo está vacío, devolver error
    if (empty($lines)) {
        return response()->json(['mensaje' => 'Fichero vacío o corrupto'], 400);
    }

    // Extraer la primera fila como cabecera
    $headers = str_getcsv(array_shift($lines)); // Cabecera como array

    // Verificar que las cabeceras no estén vacías
    if (empty($headers)) {
        return response()->json(['mensaje' => 'Fichero CSV sin cabeceras válidas'], 400);
    }

    $data = [];

    // Procesar cada línea del archivo
    foreach ($lines as $line) {
        $row = str_getcsv($line); // Parsear fila CSV
        // Verificar que la fila tenga el mismo número de columnas que las cabeceras
        if (count($row) !== count($headers)) {
            return response()->json([
                'mensaje' => 'Fichero CSV con filas que no coinciden con las cabeceras',
                'linea_invalida' => $line,
            ], 400);
        }
        $data[] = array_combine($headers, $row); // Combinar cabeceras y valores
    }

    // Devolver respuesta JSON con el contenido procesado
    return response()->json([
        'mensaje' => 'Fichero leído con éxito',
        'contenido' => $data,
    ]);
}




    /**
     * Actualiza el contenido de un fichero CSV existente.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $content = $request->input('content');
        $path = $this->storagePath . $id;

        if (!$content) {
            return response()->json(['mensaje' => 'Faltan parámetros'], 422);
        }

        if (!Storage::exists($path)) {
            return response()->json(['mensaje' => 'Fichero no encontrado'], 404);
        }

        Storage::put($path, $content);
        return response()->json(['mensaje' => 'Fichero actualizado exitosamente']);
    }

    /**
     * Elimina un fichero CSV.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $path = $this->storagePath . $id;

        if (!Storage::exists($path)) {
            return response()->json(['mensaje' => 'Fichero no encontrado'], 404);
        }

        Storage::delete($path);
        return response()->json(['mensaje' => 'Fichero eliminado exitosamente']);
    }
}

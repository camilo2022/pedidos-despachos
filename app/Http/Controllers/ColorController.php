<?php

namespace App\Http\Controllers;

use App\Http\Requests\Color\ColorDeleteRequest;
use App\Http\Requests\Color\ColorIndexQueryRequest;
use App\Http\Requests\Color\ColorRestoreRequest;
use App\Http\Requests\Color\ColorStoreRequest;
use App\Http\Requests\Color\ColorUpdateRequest;
use App\Http\Resources\Color\ColorIndexQueryCollection;
use App\Models\Color;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ColorController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index()
    {
        try {
            return view('Dashboard.Colors.Index');
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(ColorIndexQueryRequest $request)
    {
        try {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            //Consulta por nombre
            $colors = Color::when($request->filled('search'),
                    function ($query) use ($request) {
                        $query->search($request->input('search'));
                    }
                )
                ->when($request->filled('start_date') && $request->filled('end_date'),
                    function ($query) use ($start_date, $end_date) {
                        $query->filterByDate($start_date, $end_date);
                    }
                )
                ->withTrashed() //Trae los registros 'eliminados'
                ->orderBy($request->input('column'), $request->input('dir'))
                ->paginate($request->input('perPage'));

            return $this->successResponse(
                new ColorIndexQueryCollection($colors),
                $this->getMessage('Success'),
                200
            );
        } catch (QueryException $e) {
            // Manejar la excepción de la base de datos
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('QueryException'),
                    'error' => $e->getMessage()
                ],
                500
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function create()
    {
        try {
            return $this->successResponse(
                '',
                'Ingrese los datos para hacer la validacion y registro.',
                204
            );
        } catch (Exception $e) {
            // Devolver una respuesta de error en caso de excepción
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function store(ColorStoreRequest $request)
    {
        try {
            $color = new Color();
            $color->name = $request->input('name');
            $color->code = $request->input('code');
            $color->save();

            return $this->successResponse(
                $color,
                'EL color fue registrado exitosamente.',
                201
            );
        } catch (ModelNotFoundException $e) {
            // Manejar la excepción de la base de datos
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('ModelNotFoundException'),
                    'error' => $e->getMessage()
                ],
                500
            );
        } catch (QueryException $e) {
            // Manejar la excepción de la base de datos
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('QueryException'),
                    'error' => $e->getMessage()
                ],
                500
            );
        } catch (Exception $e) {
            // Devolver una respuesta de error en caso de excepción
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function edit($id)
    {
        try {
            $color = Color::withTrashed()->findOrFail($id);
            
            return $this->successResponse(
                $color,
                'El color fue encontrado exitosamente.',
                204
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('ModelNotFoundException'),
                    'error' => $e->getMessage()
                ],
                404
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function update(ColorUpdateRequest $request, $id)
    {
        try {
            $color = Color::with('sample')->withTrashed()->findOrFail($id);
            $color->name = $request->input('name');
            $color->code = $request->input('code');
            $color->save();

            return $this->successResponse(
                $color,
                'El color fue actualizado exitosamente.',
                200
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('ModelNotFoundException'),
                    'error' => $e->getMessage()
                ],
                404
            );
        } catch (QueryException $e) {
            // Manejar la excepción de la base de datos
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('QueryException'),
                    'error' => $e->getMessage()
                ],
                500
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function delete(ColorDeleteRequest $request)
    {
        try {
            $color = Color::withTrashed()->findOrFail($request->input('id'))->delete();
            return $this->successResponse(
                $color,
                'El color de producto fue eliminado exitosamente.',
                204
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('ModelNotFoundException'),
                    'error' => $e->getMessage()
                ],
                404
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function restore(ColorRestoreRequest $request)
    {
        try {
            $color = Color::withTrashed()->findOrFail($request->input('id'))->restore();
            return $this->successResponse(
                $color,
                'El color de producto fue restaurado exitosamente.',
                204
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('ModelNotFoundException'),
                    'error' => $e->getMessage()
                ],
                404
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function syncSiesa()
    {
        try {
            $user = env('API_SIESA_USER');
            $password = env('API_SIESA_PASSWORD');

            $guzzleHttpClient = new GuzzleHttpClient(['base_uri' => 'http://45.76.251.153']);

            $auth = $guzzleHttpClient->request('POST', '/API_GT/api/login/authenticate', [
                'form_params' => [
                    'Username' => $user,
                    'Password' => $password,
                ]
            ]);
            
            $token = str_replace('"', '', $auth->getBody()->getContents());

            $query = $guzzleHttpClient->request('GET', 'http://45.76.251.153/API_GT/api/orgBless/getInfoReferencia', [
                'headers' => [ 'Authorization' => "Bearer {$token}"],
            ]);

            $items = json_decode($query->getBody()->getContents());

            $items = empty($items->detail) ? [] : $items->detail;
            
            foreach(collect($items)->pluck('CodigoColor')->unique() as $item) {
                $color = Color::where('code', $this->cleaned($item))->first();
                $color = $color ? $color : new Color();
                $color->name = $this->cleaned(collect($items)->where('CodigoColor', $item)->first()->Color);
                $color->code = $this->cleaned($item);
                $color->save();
            }

            return $this->successResponse(
                '',
                'Los colores de Siesa fueron sincronizados exitosamente.',
                204
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function syncTns()
    {
        try {
            $items = DB::connection('firebird')->table('COLOR')->get();

            foreach($items as $item) {
                $color = Color::where('code', $this->cleaned($item->CODCOLOR))->first();
                $color = $color ? $color : new Color();
                $color->name = $this->cleaned($item->DESCOLOR);
                $color->code = $this->cleaned($item->CODCOLOR);
                $color->save();
            }

            return $this->successResponse(
                '',
                'Los colores de Tns fueron sincronizados exitosamente.',
                204
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('Exception'),
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    private function cleaned($string)
    {
        try {
            $string = str_replace(['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú'], ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'], $string);
            $string = str_replace(["\r", "\n", "\t"], '', $string);
            $string = strtoupper($string);
            $string = trim($string);

            return $string;
        } catch (Exception $e) {
            return $string;
        }
    }
}

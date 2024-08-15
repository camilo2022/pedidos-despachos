<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientCreateRequest;
use App\Http\Requests\Client\ClientDeleteRequest;
use App\Http\Requests\Client\ClientDataRequest;
use App\Http\Requests\Client\ClientDestroyRequest;
use App\Http\Requests\Client\ClientEditRequest;
use App\Http\Requests\Client\ClientIndexQueryRequest;
use App\Http\Requests\Client\ClientRemoveRequest;
use App\Http\Requests\Client\ClientRestoreRequest;
use App\Http\Requests\Client\ClientStoreRequest;
use App\Http\Requests\Client\ClientUpdateRequest;
use App\Http\Requests\Client\ClientUploadRequest;
use App\Http\Requests\Client\ClientWalletRequest;
use App\Http\Requests\Client\ClientWalletsRequest;
use App\Http\Resources\Client\ClientIndexQueryCollection;
use App\Imports\Client\ClientImport;
use App\Models\City;
use App\Models\Client;
use App\Models\Country;
use App\Models\Departament;
use App\Models\File;
use App\Models\Person;
use App\Models\Wallet;
use App\Traits\ApiMessage;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{
    use ApiResponser;
    use ApiMessage;

    public function index()
    {
        try {
            return view('Dashboard.Clients.Index');
        } catch (Exception $e) {
            return back()->with('danger', 'Ocurrió un error al cargar la vista: ' . $e->getMessage());
        }
    }

    public function indexQuery(ClientIndexQueryRequest $request)
    {
        try {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            //Consulta por nombre
            $clients = Client::when($request->filled('search'),
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
                new ClientIndexQueryCollection($clients),
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

    public function create(ClientCreateRequest $request)
    {
        try {
            if($request->filled('country')) {
                $departaments = Departament::with('country')->whereHas('country', fn($query) => $query->where('name', $request->input('country')))->get();
                
                return $this->successResponse(
                    [
                        'departaments' => $departaments
                    ],
                    'Departamentos encontrados exitosamente.',
                    204
                );
            }

            if($request->filled('departament')) {
                $cities = City::with('departament')->whereHas('departament', fn($query) => $query->where('name', $request->input('departament')))->get();
                
                return $this->successResponse(
                    [
                        'cities' => $cities
                    ],
                    'Ciudades encontradas exitosamente.',
                    204
                );
            }

            $countries = Country::all();

            return $this->successResponse(
                [
                    'countries' => $countries
                ],
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

    public function store(ClientStoreRequest $request)
    {
        try {
            $client = new Client();
            $client->client_name = $request->input('client_name');
            $client->client_address = $request->input('client_address');
            $client->client_number_document = $request->input('client_number_document');
            $client->client_number_phone = $request->input('client_number_phone');
            $client->client_branch_code = $request->input('client_branch_code');
            $client->client_branch_name = $request->input('client_branch_name');
            $client->client_branch_address = $request->input('client_branch_address');
            $client->client_branch_number_phone = $request->input('client_branch_number_phone');
            $client->country = $request->input('country');
            $client->departament = $request->input('departament');
            $client->city = $request->input('city');
            $client->number_phone = $request->input('number_phone');
            $client->email = $request->input('email');
            $client->zone = $request->input('zone');
            //$client->type = $request->input('type');
            $client->save();

            return $this->successResponse(
                $client,
                'El cliente fue registrado exitosamente.',
                201
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('ModelNotFoundException'),
                    'error' => $e->getMessage()
                ],
                500
            );
        } catch (QueryException $e) {
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

    public function edit(ClientEditRequest $request, $id)
    {
        try {
            if($request->filled('country')) {
                $departaments = Departament::with('country')->whereHas('country', fn($query) => $query->where('name', $request->input('country')))->get();
                
                return $this->successResponse(
                    [
                        'departaments' => $departaments
                    ],
                    'Departamentos encontrados exitosamente.',
                    204
                );
            }

            if($request->filled('departament')) {
                $cities = City::with('departament')->whereHas('departament', fn($query) => $query->where('name', $request->input('departament')))->get();
                
                return $this->successResponse(
                    [
                        'cities' => $cities
                    ],
                    'Ciudades encontradas exitosamente.',
                    204
                );
            }

            $client = Client::with('wallet')->withTrashed()->findOrFail($id);
            $countries = Country::all();

            return $this->successResponse(
                [
                    'client' => $client,
                    'countries' => $countries
                ],
                'El cliente fue encontrado exitosamente.',
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

    public function update(ClientUpdateRequest $request, $id)
    {
        try {
            $client = Client::withTrashed()->findOrFail($id);
            $client->client_name = $request->input('client_name');
            $client->client_address = $request->input('client_address');
            $client->client_number_document = $request->input('client_number_document');
            $client->client_number_phone = $request->input('client_number_phone');
            $client->client_branch_code = $request->input('client_branch_code');
            $client->client_branch_name = $request->input('client_branch_name');
            $client->client_branch_address = $request->input('client_branch_address');
            $client->client_branch_number_phone = $request->input('client_branch_number_phone');
            $client->country = $request->input('country');
            $client->departament = $request->input('departament');
            $client->city = $request->input('city');
            $client->number_phone = $request->input('number_phone');
            $client->email = $request->input('email');
            $client->zone = $request->input('zone');
            //$client->type = $request->input('type');
            $client->save();

            return $this->successResponse(
                $client,
                'El cliente fue actualizado exitosamente.',
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

    public function show($id)
    {
        try {
            $client = Client::with('wallet', 'compra', 'cartera', 'bodega', 'administrador', 'chamber_of_commerce.user', 'rut.user', 'identity_card.user', 'signature_warranty.user')->withTrashed()->findOrFail($id);
            $wallet = Wallet::where('number_document', $client->client_number_document)->first();

            if(!$wallet) {
                $wallet = new Wallet();
                $wallet->number_document = $client->client_number_document;
                $wallet->save();
                $wallet = $wallet->fresh();
            }

            $documents = ['chamber_of_commerce', 'rut', 'identity_card', 'signature_warranty'];

            foreach ($documents as $document) {
                if (!is_null($client->{$document})) {
                    $path = $client->{$document}->path;
                    $client->{$document}->path = asset("storage/$path");
                }
            }

            return $this->successResponse(
                [
                    'client' => $client,
                    'wallet' => $wallet
                ],
                'El cliente fue encontrado exitosamente.',
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

    public function wallet(ClientWalletRequest $request)
    {
        try {
            $client = Client::withTrashed()->findOrFail($request->input('client_id'));
            
            $wallet = Wallet::where('number_document', $client->client_number_document)->first();
            $wallet = $wallet ? $wallet : new Wallet();
            $wallet->number_document = $client->client_number_document;
            $wallet->zero_to_thirty = $request->input('zero_to_thirty');
            $wallet->one_to_thirty = $request->input('one_to_thirty');
            $wallet->thirty_one_to_sixty = $request->input('thirty_one_to_sixty');
            $wallet->sixty_one_to_ninety = $request->input('sixty_one_to_ninety');
            $wallet->ninety_one_to_one_hundred_twenty = $request->input('ninety_one_to_one_hundred_twenty');
            $wallet->one_hundred_twenty_one_to_one_hundred_fifty = $request->input('one_hundred_twenty_one_to_one_hundred_fifty');
            $wallet->one_hundred_fifty_one_to_one_hundred_eighty_one = $request->input('one_hundred_fifty_one_to_one_hundred_eighty_one');
            $wallet->eldest_to_one_hundred_eighty_one = $request->input('eldest_to_one_hundred_eighty_one');
            $wallet->total = $request->input('total');
            $wallet->save();

            return $this->successResponse(
                [
                    'client' => $client
                ],
                'La cartera del cliente fue actualizada exitosamente.',
                200
            );
        } catch (QueryException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('QueryException'),
                    'error' => $e->getMessage()
                ],
                500
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

    public function data(ClientDataRequest $request)
    {
        try {            
            $client = Client::withTrashed()->findOrFail($request->input('client_id'));
            $messages = (object) array('success' => array(), 'warning' => array(), 'error' => array());

            $fileTypes = [
                'chamber_of_commerce' => 'CAMARA DE COMERCIO',
                'rut' => 'RUT',
                'identity_card' => 'DOCUMENTO DE IDENTIFICACION',
                'signature_warranty' => 'FIRMA GARANTIA',
            ];
            
            foreach ($fileTypes as $field => $type) {
                if ($request->hasFile($field)) {
                    $response = $this->file($request->file($field), $request->input('client_id'), $type, 'Clients/');
                    array_push($messages->{$response->type}, $response->message);
                }
            }

            $response = $this->person($request->input('compra'), $client, 'COMPRAS');
            array_push($messages->{$response->type}, $response->message);
            $response = $this->person($request->input('cartera'), $client, 'CARTERA');
            array_push($messages->{$response->type}, $response->message);
            $response = $this->person($request->input('bodega'), $client, 'BODEGA');
            array_push($messages->{$response->type}, $response->message);
            $response = $this->person($request->input('administrador'), $client, 'ADMINISTRADOR');
            array_push($messages->{$response->type}, $response->message);

            return $this->successResponse(
                [
                    'client' => $client,
                    'messages' => $messages
                ],
                'Las referencias personales/comerciales y documentos fueron guardados exitosamente.',
                200
            );
        } catch (QueryException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('QueryException'),
                    'error' => $e->getMessage()
                ],
                500
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

    private function file($document, $model_id, $type, $folder)
    {
        try {            
            $file = File::where('model_type', Client::class)->where('model_id', $model_id)->where('type', $type)->first();
            if ($file) {
                if (Storage::disk('public')->exists($file->path)) {
                    Storage::disk('public')->delete($file->path);
                }
                $file->delete();
            }

            $name = $document->getClientOriginalName();
            $file = new File();
            $file->model_type = Client::class;
            $file->model_id = $model_id;
            $file->type = $type;
            $file->name = $document->getClientOriginalName();
            $file->path = Storage::disk('public')->put($folder . $model_id, $document);
            $file->mime = $document->getMimeType();
            $file->extension = $document->getClientOriginalExtension();
            $file->size = $document->getSize();
            $file->user_id = Auth::user()->id;
            $file->metadata = json_encode((array) stat($document));
            $file->save();

            return (object) [
                'type' => 'success',
                'message' => "El archivo $name de tipo $type fue guardado exitosamente. "
            ];
        } catch (QueryException $e) {
            return (object) [
                'type' => 'error',
                'message' => "Ocurrio un error al guardar los datos del archivo $name de tipo $type (" . $e->getMessage() . "). "
            ];
        } catch (Exception $e) {
            return (object) [
                'type' => 'error',
                'message' => "Ocurrio un error al guardar el archivo $name de tipo $type (" . $e->getMessage() . "). "
            ];
        }
    }

    private function person($object, $client, $type)
    {
        try {            
            $person = Person::where('client_number_document', $client->client_number_document)->where('type', $type)->first();
            $person = $person ? $person : new Person();
            $person->client_number_document = $client->client_number_document;
            $person->type = $type;
            $person->name = $object['name'];
            $person->last_name = $object['last_name'];
            $person->phone_number = $object['phone_number'];
            $person->email = $object['email'];
            $person->save();
            
            return (object) [
                'type' => 'success',
                'message' => "La referencia personal/comercial de tipo $type fue guardada exitosamente. "
            ];
        } catch (QueryException $e) {
            return (object) [
                'type' => 'error',
                'message' => "Ocurrio un error al guardar los datos de la referencia personal/comercial de tipo $type (" . $e->getMessage() . "). "
            ];
        } catch (Exception $e) {
            return (object) [
                'type' => 'error',
                'message' => "Ocurrio un error al guardar la referencia personal/comercial de tipo $type (" . $e->getMessage() . "). "
            ];
        }
    }

    public function remove(ClientRemoveRequest $request)
    {
        try {
            $person = Person::withTrashed()->findOrFail($request->input('id'))->delete();
            
            return $this->successResponse(
                $person,
                'La referencia personal/comercial del cliente fue removida exitosamente.',
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

    public function destroy(ClientDestroyRequest $request)
    {
        try {
            $file = File::findOrFail($request->input('id'));
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
            $file->delete();

            return $this->successResponse(
                $file,
                'El archivo del cliente fue eliminado exitosamente.',
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

    public function delete(ClientDeleteRequest $request)
    {
        try {
            $client = Client::withTrashed()->findOrFail($request->input('id'))->delete();

            return $this->successResponse(
                $client,
                'El cliente fue eliminado exitosamente.',
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

    public function restore(ClientRestoreRequest $request)
    {
        try {
            $client = Client::withTrashed()->findOrFail($request->input('id'))->restore();

            return $this->successResponse(
                $client,
                'El cliente fue restaurado exitosamente.',
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

    public function uploadQuery()
    {
        try {
            return $this->successResponse(
                '',
                'Cargue el archivo para hacer la validacion y registro.',
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
    
    public function upload(ClientUploadRequest $request)
    {
        try {
            $wallets = Excel::toCollection(new ClientImport, $request->file('wallets'))->first();

            $walletsValidate = new ClientWalletsRequest();
            $walletsValidate->merge([
                'wallets' => $wallets->toArray(),
            ]);

            $validator = Validator::make(
                $walletsValidate->all(),
                $walletsValidate->rules(),
                $walletsValidate->messages()
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            foreach ($walletsValidate->wallets as $item) {
                $item = (object) $item;
                $wallet = Wallet::where('number_document', $item->documento)->first();
                $wallet = $wallet ? $wallet : new Wallet();
                $wallet->number_document = $item->documento;
                $wallet->zero_to_thirty = $item->cero_a_treinta ?? 0;
                $wallet->one_to_thirty = $item->uno_a_treinta ?? 0;
                $wallet->thirty_one_to_sixty = $item->treintayuno_a_sesenta ?? 0;
                $wallet->sixty_one_to_ninety = $item->sesentayuno_a_noventa ?? 0;
                $wallet->ninety_one_to_one_hundred_twenty = $item->noventayuno_a_cientoveinte ?? 0;
                $wallet->one_hundred_twenty_one_to_one_hundred_fifty = $item->cientoveintiuno_a_cientocincuenta ?? 0;
                $wallet->one_hundred_fifty_one_to_one_hundred_eighty_one = $item->cientocincuentayuno_a_cientoochenta ?? 0;
                $wallet->eldest_to_one_hundred_eighty_one = $item->mayor_a_cientoochentayuno ?? 0;
                $wallet->total = $item->total ?? ($item->cero_a_treinta ?? 0) + ($item->uno_a_treinta ?? 0) + ($item->treintayuno_a_sesenta ?? 0)+ ($item->sesentayuno_a_noventa ?? 0) + ($item->noventayuno_a_cientoveinte ?? 0) + ($item->cientoveintiuno_a_cientocincuenta ?? 0) + ($item->cientocincuentayuno_a_cientoochenta ?? 0) + ($item->mayor_a_cientoochentayuno ?? 0);
                $wallet->save();
            }

            return $this->successResponse(
                '',
                'Las carteras de los clientes fueron cargados exitosamente.',
                201
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                [
                    'message' => $this->getMessage('ValidationException'),
                    'errors' => $e->errors(),
                ],
                422
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
            //?CentroOperacion=001
            $query = $guzzleHttpClient->request('GET', 'http://45.76.251.153/API_GT/api/orgBless/getClientes', [
                'headers' => [ 'Authorization' => "Bearer {$token}"],
            ]);

            $items = json_decode($query->getBody()->getContents());

            $items = empty($items->detail) ? [] : $items->detail;

            foreach($items as $item) {
                $item->Direccion = !empty($this->cleaned($item->Direccion)) ? $this->cleaned($item->Direccion) : (!empty($this->cleaned($item->DireccionDespacho)) ? $this->cleaned($item->DireccionDespacho) : 'N/A') ;
                $item->DireccionDespacho = !empty($this->cleaned($item->DireccionDespacho)) ? $this->cleaned($item->DireccionDespacho) : $item->Direccion ;
                $item->Zona = !empty($this->cleaned($item->Zona)) ? $this->cleaned($item->Zona) : 'N/A';
                $item->Pais = isset($item->Pais) ? $item->Pais : 'COLOMBIA' ;
                $item->IdSucursal = sprintf('%03d', $this->cleaned($item->IdSucursal) ? $this->cleaned($item->IdSucursal) : 1);
                $item->IdSucursal = $item->IdSucursal == '000' ? '001' : $item->IdSucursal;
                $item->NitCli = preg_replace('/-.*/', '', $this->cleaned($item->NitCli));

                $client = Client::where('client_number_document', $item->NitCli)->where('client_branch_code', $item->IdSucursal ?? '001')->first();
                $client = $client ? $client : new Client();
                $client->client_name = $this->cleaned($item->RazonSocialCli);
                $client->client_address = $this->cleaned($item->Direccion);
                $client->client_number_document = $this->cleaned($item->NitCli);
                $client->client_number_phone = $this->cleaned($item->CelularTercero);
                $client->client_branch_code = $this->cleaned($item->IdSucursal);
                $client->client_branch_name = $this->cleaned($item->DescSucursal);
                $client->client_branch_address = $this->cleaned($item->DireccionDespacho);
                $client->client_branch_number_phone = $this->cleaned($item->CelularSucursal);
                $client->country = $this->cleaned($item->Pais);
                $client->departament = $this->cleaned($item->Departamento);
                $client->city = $this->cleaned($item->Ciudad);
                $client->number_phone = $this->cleaned($item->Telefono);
                $client->email = $this->cleaned($item->Correo);
                $client->zone = $this->cleaned($item->Zona);
                $client->save();
            }

            return $this->successResponse(
                '',
                'Los clientes de Siesa fueron sincronizados exitosamente.',
                204
            );
        } catch (Exception $e) {
            return $e->getMessage();
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
            $items = DB::connection('firebird')->table('TERCEROS')
                ->select(
                    'TERCEROS.NIT', 'TERCEROS.NITTRI', 'TERCEROS.NOMBRE', 'TERCEROS.DIRECC1', 'TERCEROS.DIRECC2', 'TERCEROS.EMAIL', 'TERCEROS.TELEF1', 'TERCEROS.NOMREGTRI', 
                    'TERCEROS.TELEF2', 'TERCEROS.CELULAR', 'TERCEROS.EMAIL', 'ZONAS.NOMBRE AS ZONA', 'CIUDANE.NOMBRE AS CIUDANE', 'CIUDANE.DEPARTAMENTO AS DEPARDANE', 'PAIS.NOMBRE as PAISDANE'
                )
                ->join('ZONAS', 'ZONAS.ZONAID', 'TERCEROS.ZONA1')
                ->join('CIUDANE', 'CIUDANE.CIUDANEID', 'TERCEROS.CIUDANEID')
                ->join('PAIS', 'PAIS.PAISID', 'CIUDANE.PAISID')
                ->whereRaw("CHAR_LENGTH(TERCEROS.NIT) - CHAR_LENGTH(REPLACE(TERCEROS.NIT, '-', '')) = 1")
                ->get()->map(function ($item) {
                    return $this->transformDataTns($item);
                });
                
            foreach($items as $item) {
                $item->DIRECC1 = !empty($this->cleaned($item->DIRECC1)) ? $this->cleaned($item->DIRECC1) : (!empty($this->cleaned($item->DIRECC2)) ? $this->cleaned($item->DIRECC2) : 'N/A') ;
                $item->DIRECC2 = !empty($this->cleaned($item->DIRECC2)) ? $this->cleaned($item->DIRECC2) : $item->DIRECC1 ;
                $item->SUC = sprintf('%03d', $this->cleaned($item->SUC) ? $this->cleaned($item->SUC) : 1);
                $item->SUC = $item->SUC == '000' ? '001' : $item->SUC;
                $item->NIT = preg_replace('/-.*/', '', $this->cleaned($item->NIT));

                $client = Client::where('client_number_document', $item->NIT)->where('client_branch_code', $item->SUC)->first();
                $client = $client ?? new Client();
                $client->client_name = $this->cleaned($item->NOMBRE);
                $client->client_address = $this->cleaned($item->DIRECC1);
                $client->client_number_document = $this->cleaned($item->NIT);
                $client->client_number_phone = $this->cleaned($item->TELEF1);
                $client->client_branch_code = $this->cleaned($item->SUC);
                $client->client_branch_name = $this->cleaned($item->NOMREGTRI);
                $client->client_branch_address = $this->cleaned($item->DIRECC2);
                $client->client_branch_number_phone = $this->cleaned($item->TELEF2);
                $client->country = $this->cleaned($item->PAISDANE);
                $client->departament = $this->cleaned($item->DEPARDANE);
                $client->city = $this->cleaned($item->CIUDANE);
                $client->number_phone = $this->cleaned($item->CELULAR);
                $client->email = $this->cleaned($item->EMAIL);
                $client->zone = $this->cleaned($item->ZONA);
                $client->save();
            }

            return $this->successResponse(
                '',
                'Los clientes de Tns fueron sincronizados exitosamente.',
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
            $string = mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
            $string = mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');

            $string = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ü', 'Á', 'É', 'Í', 'Ó', 'Ú'], ['a', 'e', 'i', 'o', 'u', 'u', 'A', 'E', 'I', 'O', 'U'], $string);
            $string = str_replace(['°', '¬', '´'], '', $string);
            $string = str_replace(["\r", "\n", "\t"], '', $string);
            $string = str_replace('"', '', $string);
            $string = strtoupper($string);
            $string = trim($string);

            /* $string = mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
            $string = mb_convert_encoding($string, 'UTF-8', 'auto'); */

            if (!mb_check_encoding($string, 'UTF-8')) {
                $string = mb_convert_encoding($string, 'UTF-8', 'auto');
            }

            return $string;
        } catch (Exception $e) {
            return $string;
        }
    }

    private function transformDataTns($item) 
    {
        $array = explode('-', $item->NIT);
        switch (count($array)) {
            case 2:
                if (strlen($array[1]) > 3) {
                    [$array[0], $array[1]] = [$array[1], $array[0]];
                }
                $item->NIT = "{$array[0]}";

                $item->SUC = sprintf('%03d', intval($array[1]) == 0 ? 1 : $array[1]);
                break;
            default:
                $item->NIT = "{$array[0]}";
                $item->SUC = "{$array[count($array)-1]}";
                break;
        }
        
        return $item;
    }
}

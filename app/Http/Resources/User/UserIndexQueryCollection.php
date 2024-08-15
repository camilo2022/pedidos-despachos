<?php
namespace App\Http\Resources\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;
class UserIndexQueryCollection extends ResourceCollection
{
     /**
     * OJO SI VOY A ENVIAR UN ARRAY DE MUCHOS DATOS SE USAN LAS COLLECTIOSNES PARA DARLE FORMATO A LA RESPUESTA
     * usarla de esta manera permite darle un formato mas definido a las variables de respuesta
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'users' => $this->collection->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'last_name' => $user->last_name,
                    'document_number' => $user->document_number,
                    'phone_number' => $user->phone_number,
                    'address' => $user->address,
                    'email' => $user->email,
                    'title' => $user->title,
                    'zone' => $user->zone,
                    'business' => $user->business,
                    'created_at' => $this->formatDate($user->created_at),
                    'updated_at' => $this->formatDate($user->updated_at),
                    'deleted_at' => $user->deleted_at,
                    'roles' => $user->roles,
                    'permissions' => $user->permissions,
                ];
            }),
            'meta' => [
                'pagination' => $this->paginationMeta(),
            ],
        ];
    }

    protected function formatDate($date)
    {
        return Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    protected function paginationMeta()
    {
        return [
            'total' => $this->total(),
            'count' => $this->count(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'total_pages' => $this->lastPage(),
        ];
    }
}

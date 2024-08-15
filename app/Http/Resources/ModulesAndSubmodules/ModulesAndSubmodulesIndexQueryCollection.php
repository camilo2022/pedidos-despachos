<?php

namespace App\Http\Resources\ModulesAndSubmodules;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ModulesAndSubmodulesIndexQueryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'modules' => $this->collection->map(function ($module) {
                return [
                    'id' => $module->id,
                    'module' => $module->name,
                    'type' => $module->type,
                    'icon' => $module->icon,
                    'roles' => $module->roles->map(function ($role) {
                            return [
                                'id' => $role->id,
                                'name' => $role->name
                            ];
                        }
                    )->toArray(),
                    'submodules' => $module->submodules->map(function ($submodule) {
                        return [
                            'id' => $submodule->id,
                            'name' => $submodule->name,
                            'url' => $submodule->url,
                            'icon' => $submodule->icon,
                            'permission' => [
                                'id' => $submodule->permission->id,
                                'name' => $submodule->permission->name,
                                'guard_name' => $submodule->permission->guard_name,
                                'created_at' => $this->formatDate($submodule->permission->created_at),
                                'updated_at' => $this->formatDate($submodule->permission->updated_at),
                            ],
                            'created_at' => $this->formatDate($submodule->created_at),
                            'updated_at' => $this->formatDate($submodule->updated_at),
                        ];
                    })->toArray(),
                    'created_at' => $this->formatDate($module->created_at),
                    'updated_at' => $this->formatDate($module->updated_at),
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

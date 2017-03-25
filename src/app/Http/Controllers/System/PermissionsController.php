<?php

namespace LaravelEnso\Core\App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use LaravelEnso\Core\App\DataTable\PermissionsTableStructure;
use LaravelEnso\Core\App\Enums\PermissionTypesEnum;
use LaravelEnso\Core\App\Http\Requests\ValidatePermissionRequest;
use LaravelEnso\Core\App\Models\Permission;
use LaravelEnso\Core\App\Models\PermissionsGroup;
use LaravelEnso\Core\App\Models\Role;
use LaravelEnso\DataTable\App\Traits\DataTable;

class PermissionsController extends Controller
{
    use DataTable;
    protected $tableStructureClass = PermissionsTableStructure::class;

    public static function getTableQuery()
    {
        $query = Permission::select(\DB::raw('permissions.id as DT_RowId, permissions.name, permissions.description, permissions.type, permissions_groups.name as grup, permissions.created_at, permissions.updated_at'))
            ->join('permissions_groups', 'permissions.permissions_group_id', '=', 'permissions_groups.id');

        return $query;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('laravel-enso/core::pages.system.permissions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissionTypes = new PermissionTypesEnum();
        $permissionTypes = $permissionTypes->getData();
        $permissionsGroups = PermissionsGroup::all()->pluck('name', 'id');

        return view('laravel-enso/core::pages.system.permissions.create', compact('permissionTypes', 'permissionsGroups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ValidatePermissionRequest $request
     * @param Permission                $permission
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ValidatePermissionRequest $request, Permission $permission)
    {
        $permission->fill($request->all());

        \DB::transaction(function () use ($permission) {
            $permission->save();
            $permission->roles()->attach(1);

            flash()->success(__('Permission created'));
        });

        return redirect('system/permissions/'.$permission->id.'/edit');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        $permission->load('permissions_group');
        $permissionTypes = new PermissionTypesEnum();
        $permissionTypes = $permissionTypes->getData();
        $permissionsGroups = PermissionsGroup::all()->pluck('name', 'id');
        $roles = Role::all()->pluck('name', 'id');
        $permission->roles_list;

        return view('laravel-enso/core::pages.system.permissions.edit', compact('permission', 'permissionTypes', 'permissionsGroups', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ValidatePermissionRequest $request, Permission $permission)
    {
        \DB::transaction(function () use ($request, $permission) {
            $permission->fill($request->all());
            $permission->save();
            $roles = $request->roles_list ? $request->roles_list : [];
            $permission->roles()->sync($roles);

            flash()->success(__('The Changes have been saved!'));
        });

        return back();
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return [
            'level'   => 'success',
            'message' => __('Operation was successfull'),
        ];
    }
}

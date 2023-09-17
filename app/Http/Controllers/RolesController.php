<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRolesRequest;
use App\Http\Requests\UpdateRolesRequest;
use App\Models\Role;
use App\Repositories\RolesRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Entrust;

class RolesController extends AppBaseController
{
    /** @var  RolesRepository */
    private $rolesRepository;

    public function __construct(RolesRepository $rolesRepo)
    {
        $this->rolesRepository = $rolesRepo;
    }

    /**
     * Display a listing of the Roles.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->rolesRepository->pushCriteria(new RequestCriteria($request));
        $roles = $this->rolesRepository->all();

        return view('papeis.index')
            ->with('roles', $roles);
    }

    /**
     * Show the form for creating a new Roles.
     *
     * @return Response
     */
    public function create()
    {
        return view('papeis.create')->with('papeis', new Role());
    }

    /**
     * Store a newly created Roles in storage.
     *
     * @param CreateRolesRequest $request
     *
     * @return Response
     */
    public function store(CreateRolesRequest $request)
    {
        $input = $request->all();

        $roles = $this->rolesRepository->create($input);

        Flash::success('Roles saved successfully.');

        return redirect(route('papeis.index'));
    }

    /**
     * Display the specified Roles.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
//        $roles = $this->rolesRepository->findWithoutFail($id);
//
//        if (empty($roles)) {
//            Flash::error('Roles not found');
//
//            return redirect(route('papeis.index'));
//        }
//
//        return view('papeis.show')->with('roles', $roles);
    }

    /**
     * Show the form for editing the specified Roles.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $roles = $this->rolesRepository->findWithoutFail($id);

        if (empty($roles)) {
            Flash::error('Roles not found');

            return redirect(route('papeis.index'));
        }

        return view('papeis.edit')->with('papeis', $roles);
    }

    /**
     * Update the specified Roles in storage.
     *
     * @param  int              $id
     * @param UpdateRolesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRolesRequest $request)
    {
        $roles = $this->rolesRepository->findWithoutFail($id);

        if (empty($roles)) {
            Flash::error('Roles not found');

            return redirect(route('papeis.index'));
        }

        $roles = $this->rolesRepository->update($request->all(), $id);

        Flash::success('Roles updated successfully.');

        return redirect(route('papeis.index'));
    }

    /**
     * Remove the specified Roles from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $roles = $this->rolesRepository->findWithoutFail($id);

        if (empty($roles)) {
            Flash::error('Roles not found');

            return redirect(route('papeis.index'));
        }

        $this->rolesRepository->delete($id);

        Flash::success('Roles deleted successfully.');

        return redirect(route('papeis.index'));
    }

    public function handlePermission($role_id, $permission_id, $operation = 'attach') {
        if(!Entrust::can('add_permission_role')) {
            return self::notAllowed('json');
        }

        $role = \App\Models\Role::find($role_id);
        $permission = \App\Models\Permission::find($permission_id);
        if($operation === 'attach') {
            $role->attachPermission($permission);
        } else {
            $role->detachPermission($permission);
        }

        return ['status' => true];
    }

    public function handleUserRole($user_id, $role_id, $operation = 'attach') {
        if(!Entrust::can('assign_role')) {
            return self::notAllowed('json');
        }

        $user = \App\User::find($user_id);
        $role = \App\Models\Role::find($role_id);

        if($operation === 'attach') {
            $user->attachRole($role);    
        } else {
            $user->detachRole($role);
        }
        

        return ['status' => true];   
    }
}
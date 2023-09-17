<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Repositories\PermissionRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PermissionController extends AppBaseController
{
    /** @var  PermissionRepository */
    private $permissionRepository;

    public function __construct(PermissionRepository $permissionRepo)
    {
        $this->permissionRepository = $permissionRepo;
    }

    /**
     * Display a listing of the Permission.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->permissionRepository->pushCriteria(new RequestCriteria($request));
        $permissoes = $this->permissionRepository->orderBy('menu')->all();

        return view('permissoes.index')
            ->with('permissoes', $permissoes);
    }

    /**
     * Show the form for creating a new Permission.
     *
     * @return Response
     */
    public function create()
    {
        return view('permissoes.create');
    }

    /**
     * Store a newly created Permission in storage.
     *
     * @param CreatePermissionRequest $request
     *
     * @return Response
     */
    public function store(CreatePermissionRequest $request)
    {
        $input = $request->all();

        $permission = $this->permissionRepository->create($input);

        self::setSuccess('Permissão salva com sucesso.');

        return redirect(route('permissoes.index'));
    }

    /**
     * Display the specified Permission.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $permission = $this->permissionRepository->findWithoutFail($id);

        if (empty($permission)) {
            Flash::error('Permission not found');

            return redirect(route('permissoes.index'));
        }

        return view('permissoes.show')->with('permission', $permission);
    }

    /**
     * Show the form for editing the specified Permission.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $permission = $this->permissionRepository->findWithoutFail($id);

        if (empty($permission)) {
            Flash::error('Permission not found');

            return redirect(route('permissoes.index'));
        }

        return view('permissoes.edit')->with('permission', $permission);
    }

    /**
     * Update the specified Permission in storage.
     *
     * @param  int              $id
     * @param UpdatePermissionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePermissionRequest $request)
    {
        $permission = $this->permissionRepository->findWithoutFail($id);

        if (empty($permission)) {
            Flash::error('Permission not found');

            return redirect(route('permissoes.index'));
        }

        $permission = $this->permissionRepository->update($request->all(), $id);

        Flash::success('Permission updated successfully.');

        return redirect(route('permissoes.index'));
    }

    /**
     * Remove the specified Permission from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $permission = $this->permissionRepository->findWithoutFail($id);

        if (empty($permission)) {
            Flash::error('Permission not found');

            return redirect(route('permissoes.index'));
        }

        $this->permissionRepository->delete($id);

        Flash::success('Permission deleted successfully.');

        return redirect(route('permissoes.index'));
    }
}
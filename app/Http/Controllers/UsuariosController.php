<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Hash,Validator};
use Flash;
use Response;
use Entrust;

class UsuariosController extends AppBaseController
{

    public function __construct()
    {
        if(!Entrust::hasRole('ADMINISTRADOR')) {
            return back();
        }
    }

    /**
     * Display a listing of the Usuarios.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Entrust::can('list_usuarios')) {
            return self::notAllowed();
        }

        $s = $request->filled('s') ? $request->get('s') : "";

        if(empty($s)) {
            $users = [];
        } else {
            $users = \App\User::where('name', 'LIKE', "%" . $s . "%")
                              ->orWhere('email', "LIKE", "%" . $s . "%")
                              ->get();
        }

        return view('usuarios.index', [
            'usuarios' => $users
        ]);
    }

    /**
     * Show the form for creating a new Usuarios.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        if(!Entrust::can('create_usuarios')) {
            return self::notAllowed();
        }

        return view('usuarios.create', [
            'user' => new User(),
        ]);
    }

    /**
     * Store a newly created Usuarios in storage.
     *
     * @param CreateUsuariosRequest $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        if(!Entrust::can('create_usuarios')) {
            return self::notAllowed();
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        $input['password'] = Hash::make($input['password']);

        $user = \App\User::create($input);

        self::setSuccess('Usuario criado com sucesso.');

        return redirect(route('usuarios.index'));
    }

    /**
     * Show the form for editing the specified Usuarios.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('edit_usuarios')) {
            return self::notAllowed();
        }

        $user = \App\User::find($id);

        return view('usuarios.edit')->with([
            'user' => $user,
        ]);
    }

    /**
     * Update the specified Usuarios in storage.
     *
     * @param  int              $id
     * @param UpdateUsuariosRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUsuariosRequest $request)
    {
        if(!Entrust::can('edit_clinicas')) {
            return self::notAllowed();
        }
        $clinicas = $this->clinicasRepository->findWithoutFail($id);

        if (empty($clinicas)) {
            Flash::error('Usuarios not found');

            return redirect(route('clinicas.index'));
        }

        $clinicas = $this->clinicasRepository->update($request->all(), $id);

        Flash::success('Usuarios updated successfully.');

        return redirect(route('clinicas.index'));
    }

    /**
     * Remove the specified Usuarios from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if(!Entrust::can('delete_clinicas')) {
            return self::notAllowed();
        }
        $clinicas = $this->clinicasRepository->findWithoutFail($id);

        if (empty($clinicas)) {
            Flash::error('Usuarios not found');

            return redirect(route('clinicas.index'));
        }

        $this->clinicasRepository->delete($id);

        Flash::success('Usuarios deleted successfully.');

        return redirect(route('clinicas.index'));
    }

    public function mudarsenha()
    {
        $user = Auth::user();
        return view('usuarios.mudarsenha')->with([
            'user' => $user,
        ]);
    }

    public function updatesenha(Request $request)
    {
        $user = Auth::user();
        $user->password = Hash::make($request->get('password'));
        $user->save();
        self::setSuccess('Senha alterada com sucesso');
        return back();
    }
}

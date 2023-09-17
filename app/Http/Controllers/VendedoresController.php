<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateVendedoresRequest;
use App\Http\Requests\UpdateVendedoresRequest;
use App\Models\Vendedores;
use App\Repositories\VendedoresRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Role;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Entrust;
use Image;

class VendedoresController extends AppBaseController
{
    /** @var  VendedoresRepository */
    private $vendedoresRepository;

    const UPLOAD_TO = 'vendedores/';

    public function __construct(VendedoresRepository $vendedoresRepo)
    {
        $this->vendedoresRepository = $vendedoresRepo;
    }

    /**
     * Display a listing of the Vendedores.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if(!Entrust::can('list_vendedores')) {
            return self::notAllowed();
        }
        $this->vendedoresRepository->pushCriteria(new RequestCriteria($request));
        $vendedores = $this->vendedoresRepository->orderBy('ativo', 'desc')->orderBy('nome')->all();

        return view('vendedores.index', [
            'vendedores' => $vendedores
        ]);
    }

    /**
     * Show the form for creating a new Vendedores.
     *
     * @return Response
     */
    public function create()
    {
        if(!Entrust::can('create_vendedores')) {
            return self::notAllowed();
        }
        return view('vendedores.create', [
            'vendedores' => new Vendedores(),
            'ufs' => self::$ufs
        ]);
    }

    public static function setAvatar($vendedores, Request $request)
    {
        if($request->hasFile('avatar')) {
            $avatar = $request->avatar;
            $extension = $avatar->extension();
            $path = static::UPLOAD_TO . $vendedores->id . '/' . 'avatar_300.' . $extension;
            $image = Image::make($avatar);
            $image->fit(300);

            \Storage::put($path, (string) $image->encode());

            $vendedores->avatar = $path;
            $vendedores->update();
        }
    }

    public static function setAssinatura($vendedores, Request $request)
    {
        if($request->hasFile('assinatura')) {
            $assinatura = $request->assinatura;
            $extension = $assinatura->extension();
            $path = static::UPLOAD_TO . $vendedores->id . '/' . 'assinatura.' . $extension;
            $image = Image::make($assinatura);
//            $image->fit(300);

            \Storage::put($path, (string) $image->encode());

            $vendedores->assinatura = $path;
            $vendedores->update();
        }
    }

    /**
     * Store a newly created Vendedores in storage.
     *
     * @param CreateVendedoresRequest $request
     *
     * @return Response
     */
    public function store(CreateVendedoresRequest $request)
    {
        if(!Entrust::can('create_vendedores')) {
            return self::notAllowed();
        }
        $input = $request->all();

        $vendedores = $this->vendedoresRepository->create($input);

        self::setAvatar($vendedores, $request);
        self::setSuccess('Vendedor salvo com sucesso.');

        return redirect(route('vendedores.index'));
    }

    /**
     * Display the specified Vendedores.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        if(!Entrust::can('list_vendedores')) {
            return self::notAllowed();
        }
        $vendedores = $this->vendedoresRepository->findWithoutFail($id);

        if (empty($vendedores)) {
            self::setError('Vendedor não encontrado.');

            return redirect(route('vendedores.index'));
        }

        return view('vendedores.show')->with('vendedores', $vendedores);
    }

    /**
     * Show the form for editing the specified Vendedores.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('list_vendedores')) {
            return self::notAllowed();
        }
        $vendedores = $this->vendedoresRepository->findWithoutFail($id);

        if (empty($vendedores)) {
            self::setError('Vendedor não encontrado.');

            return redirect(route('vendedores.index'));
        }

        return view('vendedores.edit')->with([
            'vendedores' => $vendedores,
            'ufs'      => self::$ufs
        ]);
    }

    /**
     * Update the specified Vendedores in storage.
     *
     * @param  int              $id
     * @param UpdateVendedoresRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateVendedoresRequest $request)
    {
        if(!Entrust::can('edit_vendedores')) {
            return self::notAllowed();
        }
        $vendedores = $this->vendedoresRepository->findWithoutFail($id);

        if (empty($vendedores)) {
            self::setError('Vendedor não encontrado.');

            return redirect(route('vendedores.index'));
        }

        $vendedores = $this->vendedoresRepository->update($request->all(), $id);
        self::setAvatar($vendedores, $request);
        self::setAssinatura($vendedores, $request);

        $user = $vendedores->user;
        if ($user) {
            $role_inside_sales = Role::where('name', 'INSIDE_SALES')->first();
            $has_role = $user->hasRole('INSIDE_SALES');
            if (!$has_role && $request->get('role_inside_sales')) {
                $user->roles()->attach($role_inside_sales);
            } elseif ($has_role && !$request->get('role_inside_sales')) {
                $user->roles()->detach($role_inside_sales);
            }
        }

        self::setSuccess('Vendedor salvo com sucesso.');

        return redirect(route('vendedores.index'));
    }

    /**
     * Remove the specified Vendedores from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if(!Entrust::can('delete_vendedores')) {
            return self::notAllowed();
        }
        $vendedores = $this->vendedoresRepository->findWithoutFail($id);

        if (empty($vendedores)) {
            Flash::error('Vendedores not found');

            return redirect(route('vendedores.index'));
        }

        $this->vendedoresRepository->delete($id);

        Flash::success('Vendedores deleted successfully.');

        return redirect(route('vendedores.index'));
    }

    public function avatar($id) {
        $vendedor = Vendedores::findOrFail($id);
        $path = storage_path('app/' . $vendedor->avatar);
        //dd($path);
        if (!\File::exists($path)) {
            //Get unset image
            abort(404);
        }

        $file = \File::get($path);
        $type = \File::mimeType($path);

        $response = \Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function assinatura($id) {
        $vendedor = Vendedores::findOrFail($id);
        $path = storage_path('app/' . $vendedor->assinatura);
        //dd($path);
        if (!\File::exists($path)) {
            abort(404);
        }

        $file = \File::get($path);
        $type = \File::mimeType($path);

        $response = \Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }
}

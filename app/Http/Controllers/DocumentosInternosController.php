<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Models\DocumentosInternos;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\Validator;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Entrust;
use Svg\Document;

class DocumentosInternosController extends AppBaseController
{
    public function index()
    {
        if(!Entrust::can('ver_documentos_internos')) {
            return self::notAllowed();
        }
        return view('documentos_internos.index');
    }


    public function upload(Request $request)
    {
        if(!Entrust::can('criar_documentos_internos')) {
            return self::notAllowed();
        }
        /**
         * @var $v \Illuminate\Validation\Validator
         */
        $v = Validator::make($request->all(), [
            'file' => 'file|required|mimes:pdf,tiff,bmp,jpg,png,jpeg,webp,csv,doc,docx,xls,xslx,txt'
        ]);

        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            $messages = str_replace('file', 'O arquivo', $messages);
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }

        if($request->file->isValid()) {
            $extension = $request->file->extension();
            $size = $request->file->getClientSize();
            $public = $request->get('publico');
            $mime = $request->file->getClientMimeType();
            $originalName = $request->file->getClientOriginalName();
            $description = "";
            if($request->filled('description')) {
                $description = $request->get('description');
            }
            $path = $request->file->store('uploads');

            $documento = new \App\Models\DocumentosInternos();
            if($request->get('id_plano')) {
                $documento->id_plano = $request->get('id_plano');
            }
            if($request->get('tipo')) {
                $documento->tipo = $request->get('tipo');
            }

            if($request->get('id_cupom')) {
                if(!in_array($documento->tipo, [DocumentosInternos::DOCUMENTO_ADITIVO, DocumentosInternos::DOCUMENTO_REGULAMENTO])) {
                    self::setError("O tipo de documento informado não é permitido para cupons. Tente novamente.", 'Oops.');

                    return back()
                        ->withErrors($v)
                        ->withInput();
                }

                $documento->id_cupom = $request->get('id_cupom');
            }

            $documento->save();

            $upload = \App\Models\Uploads::create([
                'original_name' => $originalName,
                'mime'          => $mime,
                'description'   => $description,
                'extension'     => $extension,
                'size'          => $size,
                'public'        => $public,
                'path'          => $path,
                'bind_with'     => 'documentos',
                'binded_id'     => $documento->id,
                'user_id'       => auth()->user()->id
            ]);

            if($upload) {
                self::setSuccess('Arquivo carregado com sucesso.');
                return back();
            }
        } else {
            self::setMessage("Erro no upload.\n\n" + $request->file->getError(), 'error', 'Falha');
            back();
        }
    }
}

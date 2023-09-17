<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-image"></i>Mudar Foto
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-4 text-center">
                <div id="upload-demo"></div>
            </div>
            <div class="col-md-4" style="padding:5%;">
                <strong>Selecione uma imagem:</strong>
                <input type="file" id="image" accept="image/gif, image/jpeg, image/png">
                <input type="hidden" name="id_clinica" value="{{ $clinica->id }}">
            </div>
        </div>
        <div class="margin-top-10">
            <button class="btn green upload-image"> Salvar </button>
        </div>
    </div>
</div>
<!-- Nome Grupo Field -->
<div id="display_name">
	<div class="form-group">
	    <label class="control-label col-md-3" for="display_name">
	        Nome
	        <span class="required"> * </span>
	    </label>
	    <div class="col-md-4">
	        <input name="display_name" v-model="displayName" data-required="1" class="form-control" required/>
	        <br>
	        <small>@{{ name }}</small>
	        <input type="hidden" name="name" v-model="name">
	    </div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3">
	        Menu (minúsculo)
	        <span class="required"> * </span>
	    </label>
	    <div class="col-md-4">
	        <input name="menu" :value="menuName" required @input="menuName = $event.target.value.toLowerCase()" data-required="1" class="form-control"/>
	    </div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3">
	        Descrição
	    </label>
	    <div class="col-md-4">
	        <textarea name="descricao" class="form-control" required></textarea>
	    </div>
	</div>
</div>


<!-- Submit Field -->
<!--
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('grupos.index') !!}" class="btn btn-default">Cancel</a>
</div>
-->
@section('scripts')
	@parent

	<script type="text/javascript">
		$permissionNameApp = new Vue({
			el: '#display_name',
			data: {
				displayName: "",
				menuName: ""
			},
			computed: {
				name: function() {
					return this.displayName.trim()
										.replace(/ /g, "_")
										.toLowerCase()
										.normalize('NFD')
										.replace(/[\u0300-\u036f]/g, "");
				}
			}
		});
	</script>
@endsection
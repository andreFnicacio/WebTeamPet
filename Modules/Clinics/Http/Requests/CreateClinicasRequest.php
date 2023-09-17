<?php

namespace Modules\Clinics\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Clinics\Entities\Clinicas;

class CreateClinicasRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Clinicas::$rules;
    }
}

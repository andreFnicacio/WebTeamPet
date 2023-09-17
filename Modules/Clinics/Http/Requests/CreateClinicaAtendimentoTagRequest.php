<?php

namespace Modules\Clinics\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Clinics\Entities\ClinicaAtendimentoTag;

class CreateClinicaAtendimentoTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
        return ClinicaAtendimentoTag::$rules;
        
    }
}

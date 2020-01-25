<?php

namespace FlyingFerret\Seat\WHTools\Validation;

use Illuminate\Foundation\Http\FormRequest;

class CertificateValidation extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'certificateName' => 'required|string',
            'selectedSkills' => 'required|array|min:1'
        ];
    }
}

?>

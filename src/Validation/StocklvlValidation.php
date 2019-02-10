<?php
namespace FlyingFerret\Seat\WHTools\Validation;
use Illuminate\Foundation\Http\FormRequest;
class StocklvlValidation extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'stockSelection' => 'nullable',
            'minlvl' => 'required',
            'selectedfit' => 'required'
        ];
    }
}
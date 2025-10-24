<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReclaimOvertimeProcessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'processId'                  => ['required','integer'],
            'details'                    => ['required','array'],

            'details.reclamationSem1'    => ['present','array','max:3'],
            'details.reclamationSem1.*'  => ['string','in:c,td,tp','distinct'],

            'details.reclamationSem2'    => ['present','array','max:3'],
            'details.reclamationSem2.*'  => ['string','in:c,td,tp','distinct'],

            'details.customReclamation' => ['nullable','string','max:1000'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator){
            $details = $this->input('details', []);
            if (empty($details['customReclamation']) && empty($details['reclamationSem1']) && empty($details['reclamationSem2'])) {
                $validator->errors()->add('details', 'At least one reclamation field must be provided.');
            }
        });
    }
}

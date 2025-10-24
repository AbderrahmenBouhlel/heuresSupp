<?php

namespace App\Modules\AcademicYear\V1\Http\request;
use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicYearRequest extends FormRequest
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
            'code' => 'required|string|max:255',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'semesters' => 'required|array|size:2',
            'semesters.*.code' => 'required|in:S1,S2',
            'semesters.*.start_date' => 'required|date',
            'semesters.*.end_date' => 'required|date|after:semesters.*.start_date',
        ];

        // The * is a wildcard.
        // Laravel applies the rule to each element of the semesters array.
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $semesters = $this->input('semesters', []);
            $start_date_year = $this->input('start_date');
            $end_date_year = $this->input('end_date');

            // AcademicYear start/end date check
            if ($start_date_year && $end_date_year && strtotime($start_date_year) > strtotime($end_date_year)) {
                $validator->errors()->add('start_date', 'Academic year start_date cannot be after end_date.');
            }

            // Semesters checks
            if (count($semesters) !== 2) {
                $validator->errors()->add('semesters', 'Exactly 2 semesters must be provided.');
                return;
            }

            $s1 = $semesters[0];
            $s2 = $semesters[1];

            // Check semester codes
            if ($s1['code'] !== 'S1' || $s2['code'] !== 'S2') {
                $validator->errors()->add('semesters', 'Semesters must be in order S1 then S2.');
            }

            // Check dates for each semester
            foreach ([$s1, $s2] as $i => $s) {
                if (strtotime($s['start_date']) > strtotime($s['end_date'])) {
                    $validator->errors()->add("semesters.$i.start_date", 'Semester start_date cannot be after end_date.');
                }
            }

            // Check S1/S2 order
            if (strtotime($s1['end_date']) > strtotime($s2['start_date'])) {
                $validator->errors()->add('semesters', 'Semester S1 must end before S2 starts.');
            }
        });
    }

}

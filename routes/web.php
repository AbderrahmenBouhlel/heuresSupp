<?php

use Illuminate\Support\Facades\Route;
use App\Models\Teacher;

Route::get('/admin', function () {
    $adminVar = (object)[
        'nom' => 'Ben Salah',
        'prenom' => 'Amine',
        'email' => 'amine.bensalah@issatso.rnu.tn',
        'departement_principal' => 'Informatique',
        'active_from' => '2020-09-01',
    ];
    return view('admin' , compact('adminVar'));  // resources/views/admin.blade.php
});

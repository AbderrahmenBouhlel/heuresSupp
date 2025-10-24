<?php

namespace App\Modules\Admin\V1\infrastructure\models;

use App\Modules\core\infrastructure\Excel\models\base\ColumnsModel;
use App\Modules\core\infrastructure\Excel\models\base\ColumnType;
use App\Modules\core\infrastructure\Excel\models\base\ExcelTableModel;
use App\Modules\Admin\V1\VOs\enums\DepartementEnum;


class TeachersAssignmentsTableModel extends ExcelTableModel{

    public function __construct() {
        parent::__construct(
            [
                new ColumnsModel(
                    name: 'name',
                    type:ColumnType::STRING,
                    required:true
                ),
                new ColumnsModel(
                    name: 'email',
                    type:ColumnType::STRING,
                    required:true,
                    pattern: '/^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,7}$/',
                    mustBeUnique: true
                ),
                new ColumnsModel(
                    name: 'teacher_id',
                    type:ColumnType::STRING,
                    required:true,
                    mustBeUnique: true
                    
                ),
                new ColumnsModel(
                    name: 'academic_year_code',
                    type: ColumnType::STRING,
                    required: true,
                    pattern: '/^\d{4}-\d{4}$/',
                    mustBeUniform: true
                ),
                new ColumnsModel(
                    name: 'Sem1_COUR',
                    type: ColumnType::FLOAT,
                    required: false,
                    min: 0,
                ),
                new ColumnsModel(
                    name: 'Sem1_TD',
                    type: ColumnType::FLOAT,
                    required: false,
                    min: 0,
                ),
                new ColumnsModel(
                    name: 'Sem1_TP',
                    type: ColumnType::FLOAT,
                    required: false,
                    min: 0,
                ),

                new ColumnsModel(
                    name: 'Sem2_COUR',
                    type: ColumnType::FLOAT,
                    required: false,
                    min: 0,
                ),

                new ColumnsModel(
                    name: 'Sem2_TD',
                    type: ColumnType::FLOAT,
                    required: false,
                    min: 0,
                ),

                new ColumnsModel(
                    name: 'Sem2_TP',
                    type: ColumnType::FLOAT,
                    required: false,
                    min: 0,
                ),

                new ColumnsModel(
                    name: 'department',
                    type:ColumnType::STRING,
                    required:false,
                    allowedValues:DepartementEnum::values()
                ),
            ]
        );
    }


    

}
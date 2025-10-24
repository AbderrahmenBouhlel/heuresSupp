<?php

namespace App\Modules\AcademicYear\V1\infrastructure\Excel\models\base\built\teacher;

use App\Modules\core\infrastructure\Excel\models\base\ColumnsModel;
use App\Modules\core\infrastructure\Excel\models\base\ColumnType;
use App\Modules\core\infrastructure\Excel\models\base\ExcelTableModel;
use App\Modules\Admin\V1\VOs\enums\DepartementEnum;
use App\Modules\Teacher\V1\VOs\enums\TeacherRoleEnum;

class NewTeacherTableModel extends ExcelTableModel{

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
                    pattern: '/^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,7}$/'
                ),
                new ColumnsModel(
                    name: 'department',
                    type:ColumnType::STRING,
                    required:true,
                    allowedValues:DepartementEnum::values()
                ),
                new ColumnsModel(
                    name: 'role',
                    type:ColumnType::STRING,
                    required:true,
                    allowedValues:TeacherRoleEnum::values()
                ),
                new ColumnsModel(
                    name: 'active_from_academic_year_code',
                    type: ColumnType::STRING,
                    required: true,
                    pattern: '/^\d{4}-\d{4}$/'
                ),
                new ColumnsModel(
                    name: 'active_until_academic_year_code',
                    type: ColumnType::STRING,
                    required: false,
                    pattern: '/^\d{4}-\d{4}$/'
                ),
            ]
        );
    }

}
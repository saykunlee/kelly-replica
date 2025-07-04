<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuCategoryModel extends Model
{
    protected $table = 'menu_categories';
    protected $primaryKey = 'no';
    protected $allowedFields = [
        'name',
        'order',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'is_deleted',
        'is_active',
        'type'
    ];


   
}

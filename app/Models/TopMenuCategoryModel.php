<?php

namespace App\Models;

use CodeIgniter\Model;

class TopMenuCategoryModel extends BaseModel
{
    protected $table = 'top_menu_categories';
    protected $primaryKey = 'no';
    protected $allowedFields = ['name', 'order', 'icon', 'created_by', 'created_at', 'updated_by', 'updated_at', 'is_deleted', 'is_active', 'type'];

    public function getActiveCategories($type = 'admin')
    {
        return $this->where('is_deleted', 0)
                    ->where('is_active', 1)
                    ->where('type', $type)
                    ->orderBy('order', 'ASC')
                    ->findAll();
    }
}

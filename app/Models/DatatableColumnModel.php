<?php

namespace App\Models;

use CodeIgniter\Model;

class DatatableColumnModel extends Model
{
    protected $table = 'datatable_columns';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'setting_id', 'data', 'title', 'width', 'className', 'orderable', 'render', 
        'category_id', 'created_by', 'created_at', 'updated_by', 'updated_at', 
        'is_deleted', 'is_active', 'visible', 'order'
    ];

    public function getColumnsBySettingId($settingId)
    {
        return $this->where('setting_id', $settingId)->orderBy('order', 'ASC')->findAll();
    }

    public function saveColumn($columnData)
    {
        return $this->insert($columnData);
    }
}
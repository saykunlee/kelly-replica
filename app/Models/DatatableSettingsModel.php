<?php

namespace App\Models;

use CodeIgniter\Model;

class DatatableSettingsModel extends Model
{
    protected $table = 'datatable_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['menu_id', 'name', 'pageLength', 'showCheckbox', 'category_id', 'created_by', 'created_at', 'updated_by', 'updated_at', 'is_deleted', 'is_active', 'url', 'dt_buttons'];

    public function getDatatableSettings($no)
    {
        return $this->where('menu_id', $no)->first();
    }

    public function saveDatatableSettings($name, $menuId, $url, $buttons)
    {
        $data = [
            'name' => $name,
            'menu_id' => $menuId,
            'url' => $url,
            'buttons' => $buttons
        ];

        if ($this->where('menu_id', $menuId)->countAllResults() > 0) {
            return $this->where('menu_id', $menuId)->set($data)->update();
        } else {
            return $this->insert($data);
        }
    }
}
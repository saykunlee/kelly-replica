<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'config';
    protected $primaryKey = 'cfg_key';
    // set allowed fields
    protected $allowedFields = ['cfg_key', 'cfg_value'];
}

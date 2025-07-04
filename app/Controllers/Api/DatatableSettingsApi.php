<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\DatatableSettingsModel;
use App\Models\DatatableColumnModel; // Add this line

class DatatableSettingsApi extends ResourceController
{
    public function __invoke($method)
    {
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \BadMethodCallException("Method {$method} does not exist.");
    }

    public function saveSettings()
    {
        $data = $this->request->getPost();
        $model = new DatatableSettingsModel();
        $columnModel = new DatatableColumnModel(); // Add this line

        // Check if the 'id' key exists in the data
        if (isset($data['id']) && $data['id']) {
            // Check if the setting exists
            $existingSetting = $model->find($data['id']);

            if ($existingSetting) {
                //set olny  name ,url ,dt_buttons, pageLength ,is_active ,showCheckbox 
                $update_data = [
                    'name' => $data['name'],
                    'url' => $data['url'],
                    'dt_buttons' => $data['dt_buttons'],
                    'pageLength' => $data['pageLength'],
                    'is_active' => $data['is_active'],
                    'showCheckbox' => $data['showCheckbox'],
                    'updated_by' => session()->get('mem_id')
                ];
                

                // Update existing setting
                if ($model->update($data['id'], $update_data)) {
                    return $this->respond(['status' => '200', 'message' => 'Settings updated successfully']);
                } else {
                    return $this->fail('Failed to update settings');
                }
            }
        }

        // Create new setting
        if ($model->insert($data)) {
            $newSettingId = $model->getInsertID(); // Get the ID of the newly inserted setting

            // Insert a dummy record into datatable_columns
            $dummyData = [
                'setting_id' => $newSettingId,
                'data' => null,
                'title' => '#',
                'width' => '25px',
                'className' => 'tx-center',
                'orderable' => 0,
                'render' => "function(data, type, row, meta) {
                    return meta.settings._iDisplayStart + meta.row + 1;
                }",
                'category_id' => null,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_by' => null,
                'updated_at' => null,
                'is_deleted' => 0,
                'is_active' => 1,
                'visible' => 1,
                'order' => null
            ];

            if ($columnModel->insert($dummyData)) {
                // Additional dummy data
                $additionalDummyData = [
                    'setting_id' => $newSettingId,
                    'order' => 100,
                    'data' => '',
                    'title' => '',
                    'width' => '30px',
                    'className' => 'tx-center',
                    'orderable' => 0,
                    'render' => "function(data, type, row) {
                        return '<a style=\"color: #596882;\" href=\"javascript:void(0);\" onclick=\"openModal(' + row.no + ');\"><i data-feather=\"more-vertical\"></i></a>';
                    }",
                    'category_id' => 0,
                    'created_by' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_by' => null,
                    'updated_at' => null,
                    'is_deleted' => 0,
                    'is_active' => 1,
                    'visible' => 1
                ];

                if ($columnModel->insert($additionalDummyData)) {
                    return $this->respond(['status' => '200', 'message' => 'Settings and dummy columns created successfully', 'id' => $newSettingId]);
                } else {
                    return $this->fail('Failed to create additional dummy column');
                }
            } else {
                return $this->fail('Failed to create dummy column');
            }
        } else {
            return $this->fail('Failed to create settings');
        }
    }

    public function saveColumn()
    {
        $data = $this->request->getPost();
        $model = new DatatableColumnModel();

        // Check if the column exists
        if (isset($data['id']) && $model->find($data['id'])) {
            // Update existing column
            if ($model->update($data['id'], $data)) {
                return $this->respond(['status' => '200', 'message' => 'Column updated successfully']);
            } else {
                return $this->fail('Failed to update column');
            }
        } else {
            // Create new column
            if ($model->insert($data)) {
                $newColumnId = $model->getInsertID(); // Get the ID of the newly inserted column
                return $this->respond(['status' => '200', 'message' => 'Column created successfully', 'id' => $newColumnId]);
            } else {
                return $this->fail('Failed to create column');
            }
        }
    }

    public function deleteColumn()
    {
        $data = $this->request->getPost();
        $columnModel = new DatatableColumnModel();

        if (isset($data['id']) && $data['id']) {
            if ($columnModel->delete($data['id'])) {
                return $this->respond(['status' => '200', 'message' => 'Column deleted successfully']);
            } else {
                return $this->fail('Failed to delete column');
            }
        } else {
            return $this->fail('Invalid column ID');
        }
    }

    public function deleteSettings()
    {
        $data = $this->request->getPost();
        $model = new DatatableSettingsModel();

        if (isset($data['id']) && $data['id']) {
            // Update the is_deleted field to 1
            if ($model->update($data['id'], ['is_deleted' => 1])) {
                return $this->respond(['status' => '200', 'message' => 'Settings marked as deleted successfully']);
            } else {
                return $this->fail('Failed to mark settings as deleted');
            }
        } else {
            return $this->fail('Invalid settings ID');
        }
    }
}

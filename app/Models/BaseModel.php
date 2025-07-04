<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $allowedFields = [];

    public function __construct()
    {
        parent::__construct();
        $this->initializeAllowedFields();
    }

    protected function initializeAllowedFields()
    {
        if ($this->table) {
            $this->allowedFields = $this->db->getFieldNames($this->table);
        }
    }
    public function getListWithDetails($table, $where, $orderColumn, $orderDirection, $start, $length)
    {
        $builder = $this->db->table($table);

        // WHERE 조건 추가
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                $builder->whereIn($key, $value);
            } else {
                $builder->where($key, $value);
            }
        }
        // length가 -1이 아닌 경우에만 limit을 설정합니다.
        if ($length != -1) {
            $builder->limit($length, $start);
        }

        // 정렬 및 페이징 처리
        $builder->orderBy($orderColumn, $orderDirection);

        // 데이터 가져오기
        $data = $builder->get()->getResultArray();

        // 결과를 배열로 반환
        return [
            'list' => $data, // 'list' 키를 포함하여 반환
            'total' => $this->countFilteredResults($table, $where) // 총 결과 수
        ];
    }
    //getListAll
    public function countTotalResults($table, $where)
    {
        $builder = $this->db->table($table);

        // WHERE 조건 추가
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                // 배열인 경우 IN 조건으로 처리
                $builder->whereIn($key, $value);
            } else {
                $builder->where($key, $value);
            }
        }

        // countAllResults 호출 시 두 번째 인자는 bool 타입이어야 함
        return $builder->countAllResults(true); // true 또는 false로 설정
    }


    public function countFilteredResults($table, $where)
    {
        $builder = $this->db->table($table);

        // WHERE 조건 추가
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                // 배열인 경우 IN 조건으로 처리
                $builder->whereIn($key, $value);

                //특정 항목에 대한 하드 코딩
                  // mem_personal_tags 배열 조건 처리
                  if ($key === 'mem_personal_tags') {
                       $value = function($builder) use ($value) {
                          foreach ($value as $tag) {
                              $builder->orLike('mem_personal_tags', ",{$tag},")
                                      ->orLike('mem_personal_tags', "{$tag},")
                                      ->orLike('mem_personal_tags', ",{$tag}")
                                      ->orLike('mem_personal_tags', "{$tag}");
                          }
                      };     
                }

            } else {
                $builder->where($key, $value);
            }
        }

        // countAllResults 호출 시 두 번째 인자는 bool 타입이어야 함
        return $builder->countAllResults(true); // true 또는 false로 설정
    }
    public function setTable($table)
    {
        $this->table = $table;
        $this->initializeAllowedFields();
        return $this;
    }

    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
        return $this;
    }

    public function setAllowedFields($fields)
    {
        $this->allowedFields = $fields;
        return $this;
    }
    public function getDetails($table, $id_column, $id_value)
    {


        return $this->db->table($table)
            ->select('*')
            ->where($id_column, $id_value)
            ->get()
            ->getRowArray();
    }
    //insertBatchWithColumns
    public function insertBatchWithColumns($tableName, $dataArray, $columns)
    {
        // 데이터 배열에서 지정된 컬럼만 포함하도록 필터링
        $filteredDataArray = array_map(function($record) use ($columns) {
            return array_intersect_key($record, array_flip($columns));
        }, $dataArray);

        // 필터링된 데이터를 insertBatch에 전달
        return $this->db->table($tableName)->insertBatch($filteredDataArray);
    }
}

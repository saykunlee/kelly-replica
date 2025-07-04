<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Cache\CacheInterface;

class EstimateModel extends Model
{
    protected $table = 'your_table_name';
    protected $primaryKey = 'id'; // 필요한 경우 변경

    protected $DBGroup = 'estimate'; // 사용할 데이터베이스 그룹을 지정

    public function __construct()
    {
        parent::__construct();

        // 필요 시 DB 그룹을 명시적으로 초기화
        $this->db = \Config\Database::connect($this->DBGroup, true);
    }
    public function getAdminListDt_($table, $where = [], $findex = 'no', $forder = 'desc', $limit = null, $offset = null)
    {
        $this->table = $table;

        $query = $this->db->table($this->table)
            ->where($where)
            ->orderBy($findex, $forder);

        if ($limit) {
            $query->limit($limit, $offset);
        }

        $result['list'] = $query->get()->getResultArray();
        $result['total_rows'] = $this->db->table($this->table)
            ->where($where)
            ->countAllResults();

        return $result;
    }
   // 필터링된 결과 수를 계산하는 메서드
   public function countFilteredResults($table, $where)
   {
       return $this->db->table($table)->where($where)->countAllResults();
   }
   //페이징을 사용하지 않는 전체 데이터
    public function getAdminListDtAll($table, $where, $orderBy = 'no', $order = 'desc', $limit = null, $offset = null)
    {
        // 캐시 키 생성
        $cacheKey = md5($table . serialize($where) . $orderBy . $order . $limit . $offset);

        // 캐시 인스턴스 가져오기
        $cache = \Config\Services::cache();

        // 캐시에서 데이터 가져오기
        $result = $cache->get($cacheKey);

        if ($result === null) {
            // 캐시된 데이터가 없으면 데이터베이스 쿼리 실행
            $builder = $this->db->table($table);
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    $builder->whereIn($key, $value);
                } else {
                    $builder->where($key, $value);
                }
            }
            $builder->orderBy($orderBy, $order);

            if ($limit !== null) {
                $builder->limit($limit, $offset);
            }

            $query = $builder->get();
            $result['list'] = $query->getResultArray();
            $result['total_rows'] = count($result['list']);

            // 결과를 캐시에 저장 (예: 10초 동안 캐시)
            $cache->save($cacheKey, $result, 10);
        }

        return $result;
    }
    // 데이터 테이블에 필요한 데이터를 가져오는 메서드
    public function getAdminListDt($table, $where, $orderBy = 'no', $orderDir = 'desc', $start = 0, $length = 10)
    {
        // 캐시 키 생성
        $cacheKey = md5($table . serialize($where) . $orderBy . $orderDir . $start . $length);

        // 캐시 인스턴스 가져오기
        $cache = \Config\Services::cache();

        // 캐시에서 데이터 가져오기
        $result = $cache->get($cacheKey);

        if ($result === null) {
            // 캐시된 데이터가 없으면 데이터베이스 쿼리 실행
            $builder = $this->db->table($table);
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    $builder->whereIn($key, $value);
                } else {
                    $builder->where($key, $value);
                }
            }
            $builder->orderBy($orderBy, $orderDir);

            // length가 -1이 아닌 경우에만 limit을 설정합니다.
            if ($length != -1) {
                $builder->limit($length, $start);
            }

            $query = $builder->get();
            $result['list'] = $query->getResultArray();
            $result['total_rows'] = count($result['list']);

            // 결과를 캐시에 저장 (예: 60초 동안 캐시)
            $cache->save($cacheKey, $result, 10);
        }

        // 로그 추가
        log_message('debug', 'Query: ' . $this->db->getLastQuery());

        return $result;
    }
}

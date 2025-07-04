<?php

namespace App\Models;

class MemberModel extends BaseModel
{
    protected $table = 'member';
    protected $primaryKey = 'mem_id';

   
    public function getMemberListWithDetails($where, $orderBy, $orderDir, $start, $length)
    {
        $query = $this->db->table('member')
            ->select('*')
            ->where($where)
            ->orderBy($orderBy, $orderDir);

        if ($length != -1) {
            $query->limit($length, $start);
        }

        $result = $query->get();

        return [
            'total_rows' => $result->getNumRows(),
            'list' => $result->getResultArray()
        ];
    }

    public function getMemberDetails($mem_id)
    {
        return $this->db->table($this->table)
            ->select('*')
            ->where('mem_id', $mem_id)
            ->get()
            ->getRowArray();
    }

    // Add this method
    public function isUserIdAvailable($userId)
    {
        $memberExists = $this->where('mem_userid', $userId)->countAllResults() > 0;
        $memberUserIdExists = $this->db->table('member_userid')->where('mem_userid', $userId)->countAllResults() > 0;
        return !$memberExists && !$memberUserIdExists;
    }
    public function isEmailAvailable($email)
    {
        $memberExists = $this->where('mem_email', $email)->countAllResults() > 0;
        return !$memberExists;
    }

    public function addMemberUserId($memberId, $memUserId)
    {
        /*
         mem_id     int(11) unsigned            NOT NULL,
        mem_userid varchar(100)     DEFAULT '' NOT NULL,
        mem_status int(11) unsigned DEFAULT 0  NOT NULL,
        */

        // Set the table to 'member_userid' and define the allowed fields
        $this->setTable('member_userid');

        // Prepare the data to be inserted
        $data = [
            'mem_id' => $memberId,
            'mem_userid' => $memUserId,
        ];

        // Insert the data into the 'member_userid' table
        return $this->insert($data);
    }
    public function isNicknameAvailable($nickname)
    {
        $memberExists = $this->where('mem_nickname', $nickname)->countAllResults() > 0;
        /*
         mni_id             int(11) unsigned AUTO_INCREMENT
        PRIMARY KEY,
        mem_id             int(11) unsigned DEFAULT 0         NOT NULL,
        mni_nickname       varchar(100)     DEFAULT ''        NOT NULL,
        mni_start_datetime datetime         DEFAULT SYSDATE() NULL,
            mni_end_datetime   datetime                           NULL
        */
        $memberNicknameExists = $this->db->table('member_nickname')->where('mni_nickname', $nickname)->countAllResults() > 0;


        return !$memberExists && !$memberNicknameExists;
    }

    // Add this method to get member meta data
    public function getMemberMeta($mem_id)
    {
        return $this->db->table('member_meta')
            ->select('mmt_key, mmt_value')
            ->where('mem_id', $mem_id)
            ->get()
            ->getResultArray();
    }
    public function getByBoth($identifier, $select = '*')
    {
        $this->setTable('member');
        return $this->select($select)
            ->groupStart()
            ->where('mem_userid', $identifier)
            ->orWhere('mem_email', $identifier)
            ->groupEnd()
            ->first();
    }
    //ge by email
    public function getByEmail($mem_email, $select = '*')
    {
        return $this->select($select)->where('mem_email', $mem_email)->first();
    }
    //getByUserid
    public function getByUserid($mem_userid, $select = '*')
    {
        return $this->select($select)->where('mem_userid', $mem_userid)->first();
    }


    public function updateLoginLog($data)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('member_login_log');
        return $builder->insert($data);
    }

    public function resetPassword($mem_id, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        return $this->update($mem_id, ['mem_password' => $hashedPassword]);
    }
    public function getLoginLogs($select, $where)
    {
        $this->setTable('member_login_log');
        return $this->db->table('member_login_log')
            ->select($select)
            ->where($where)
            ->orderBy('mll_id', 'DESC')
            ->get()
            ->getResultArray();
    }
    public function getLoginLoglist($where = [], $orderBy = 'mll_datetime', $orderDir = 'DESC', $start = 0, $length = 10)
    {
        $query = $this->db->table('member_login_log')
            ->select('*')
            ->where($where)
            ->orderBy($orderBy, $orderDir);

        if ($length != -1) {
            $query->limit($length, $start);
        }

        $result = $query->get();

        return [
            'total_rows' => $result->getNumRows(),
            'list' => $result->getResultArray()
        ];
    }
     // Add this method to get login log details
     public function getLoginLogDetails($mll_id)
     {
         return $this->db->table('member_login_log')
             ->select('*')
             ->where('mll_id', $mll_id)
             ->get()
            ->getRowArray();
    }
    public function countMemberFilteredResults($table, $where)
    {
        $builder = $this->db->table($table);
    
        // WHERE 조건 추가
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                // 배열인 경우 IN 조건으로 처리
                // mem_personal_tags 배열 조건 처리
                if ($key === 'mem_personal_tags') {
                    $builder->groupStart(); // 그룹 시작
                    foreach ($value as $tag) {
                        $builder->orWhere("FIND_IN_SET('$tag', mem_personal_tags) > 0");
                    }
                    $builder->groupEnd(); // 그룹 종료
                } else {
                    $builder->whereIn($key, $value);
                }   
            } else {
                $builder->where($key, $value);
            }
        }
        // countAllResults 호출 시 두 번째 인자는 bool 타입이어야 함
        return $builder->countAllResults(true); // true 또는 false로 설정
    }
    public function getListMemberWithDetails($table, $where, $orderColumn, $orderDirection, $start, $length)
    {
        $builder = $this->db->table($table);

           // WHERE 조건 추가
           foreach ($where as $key => $value) {
            if (is_array($value)) {
                // 배열인 경우 IN 조건으로 처리
    
                // mem_personal_tags 배열 조건 처리
                if ($key === 'mem_personal_tags') {
                    $builder->groupStart(); // 그룹 시작
                    foreach ($value as $tag) {
                        $builder->orWhere("FIND_IN_SET('$tag', mem_personal_tags) > 0");
                    }
                    $builder->groupEnd(); // 그룹 종료
                } else {
                    $builder->whereIn($key, $value);
                }
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
}

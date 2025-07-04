<?php

namespace App\Models;

class MemberDormantModel extends BaseModel
{
    protected $table = 'member_dormant';
    protected $primaryKey = 'mem_id';

    public function getByUserid($userid, $select = '*')
    {
        return $this->select($select)->where('mem_userid', $userid)->first();
    }

    public function getByEmail($email, $select = '*')
    {
        return $this->select($select)->where('mem_email', $email)->first();
    }

    public function getByBoth($useridOrEmail, $select = '*')
    {
        return $this->select($select)
                    ->groupStart()
                        ->where('mem_userid', $useridOrEmail)
                        ->orWhere('mem_email', $useridOrEmail)
                    ->groupEnd()
                    ->first();
    }

    public function recoverFromDormant($mem_id)
    {
        // Implement the logic to recover a dormant member
    }
}
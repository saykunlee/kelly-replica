<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberGroupModel extends BaseModel
{
    protected $table = 'member_group_member';
    protected $primaryKey = 'mgm_id';

    public function getMemberGroups($mem_id = null)
    {
        $builder = $this->db->table($this->table)
            ->select('member_group_member.mgr_id, member_group_member.mem_id, member_group.mgr_title')
            ->join('member_group', 'member_group.mgr_id = member_group_member.mgr_id', 'left');

        if ($mem_id !== null) {
            $builder->where('member_group_member.mem_id', $mem_id);
        }

        return $builder->get()->getResultArray();
    }

    // Find all group list from member_group table
    public function findAllGroups()
    {
        return $this->db->table('member_group')
            ->get()
            ->getResultArray();
    }

    // Replace member groups
    public function replaceMemberGroups($mem_id, $group_ids)
    {
        // Delete existing groups
        $this->db->table($this->table)->where('mem_id', $mem_id)->delete();

        // Insert new groups, avoiding duplicates
        foreach ($group_ids as $group_id) {
            $exists = $this->db->table($this->table)
                ->where('mem_id', $mem_id)
                ->where('mgr_id', $group_id)
                ->countAllResults();

            if ($exists == 0) {
                $this->db->table($this->table)->insert([
                    'mem_id' => $mem_id,
                    'mgr_id' => $group_id,
                    'mgm_datetime' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}
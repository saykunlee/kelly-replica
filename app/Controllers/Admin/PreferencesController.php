<?php

namespace App\Controllers\Admin;

class PreferencesController extends BaseAdminController
{
    public function basicSettings()
    {
        $data = $this->loadSidebarData();

        // Svelte 컴포넌트에 보낼 데이터를 준비합니다.
        $componentData = [
            'followers' => 340,
            'following' => 1563,
            'requests' => 19,
            'profileList' => [
                [
                    'name' => 'Zhen Juan Chiu',
                    'role' => 'Software Engineer',
                    'image' => 'https://via.placeholder.com/350',
                    'following' => false
                ],
                [
                    'name' => 'Zhen Juan Chiu',
                    'role' => 'Software Engineer',
                    'image' => 'https://via.placeholder.com/350',
                    'following' => false
                ],
                [
                    'name' => 'Barbara Marion',
                    'role' => 'Tech Executive',
                    'image' => 'https://via.placeholder.com/600',
                    'following' => false
                ],
                [
                    'name' => 'Christine Arnold',
                    'role' => 'Lead Designer',
                    'image' => 'https://via.placeholder.com/500',
                    'following' => true
                ],
                [
                    'name' => 'Zhen Juan Chiu',
                    'role' => 'Software Engineer',
                    'image' => 'https://via.placeholder.com/350',
                    'following' => false
                ],
                [
                    'name' => 'Zhen Juan Chiu',
                    'role' => 'Software Engineer',
                    'image' => 'https://via.placeholder.com/350',
                    'following' => false
                ],
                [
                    'name' => 'Barbara Marion',
                    'role' => 'Tech Executive',
                    'image' => 'https://via.placeholder.com/600',
                    'following' => false
                ],
                [
                    'name' => 'Christine Arnold',
                    'role' => 'Lead Designer',
                    'image' => 'https://via.placeholder.com/500',
                    'following' => true
                ],
                [
                    'name' => 'Zhen Juan Chiu',
                    'role' => 'Software Engineer',
                    'image' => 'https://via.placeholder.com/350',
                    'following' => false
                ],
                [
                    'name' => 'Zhen Juan Chiu',
                    'role' => 'Software Engineer',
                    'image' => 'https://via.placeholder.com/350',
                    'following' => false
                ],
                [
                    'name' => 'Barbara Marion',
                    'role' => 'Tech Executive',
                    'image' => 'https://via.placeholder.com/600',
                    'following' => false
                ],
                [
                    'name' => 'Christine Arnold',
                    'role' => 'Lead Designer',
                    'image' => 'https://via.placeholder.com/500',
                    'following' => true
                ],
                // 추가 인물 데이터를 여기에 추가할 수 있습니다.
            ]
        ];

        $data['componentData'] = json_encode($componentData); // 데이터를 JSON으로 인코딩하여 Svelte로 보냅니다.

        return view('admin/preferences/basicsettings', $data);
    }

    // 다른 admin 페이지들도 동일한 방식으로 추가합니다.
}

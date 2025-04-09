<?php

return [
    // ================= web menu =================
    'main' => [
        
        'MYPAGE' => [
            'name' => '마이페이지',
            'route' => 'mypage.intro',
            'param' => [],
            'url' => '',
            'dev' => false,
            'continue' => true,
            'blank'=>false,
        ],
    ],

    'sub' => [
        
        'MYPAGE' => [ // 마이페이지
            
            'S2' => [
                'name' => '개인정보 수정',
                'route' => 'mypage.pwCheck',
                'param' => [],
                'url' => null,
                'continue' => false,
                'blank'=>false,
            ],
            'S3' => [
                'name' => '비밀번호 수정',
                'route' => 'mypage.password',
                'param' => [],
                'url' => null,
                'continue' => false,
                'blank'=>false,
            ],
            'S4' => [
                'name' => '회비납부 조회',
                'route' => 'mypage.fee',
                'param' => [],
                'url' => null,
                'continue' => false,
                'blank'=>false,
            ],
            'S5' => [
                'name' => '학술대회 참석현황',
                'route' => 'mypage.work_attend',
                'param' => [],
                'url' => null,
                'continue' => false,
                'blank'=>false,
            ],
            'S6' => [
                'name' => '회원탈퇴',
                'route' => 'mypage.withdraw',
                'param' => [],
                'url' => null,
                'continue' => false,
                'blank'=>false,
            ],
        ],
    ],
];

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MemberExcel implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    private $userConfig;
    private $collection;
    private $total;
    private $row = 0;

    public function __construct($data)
    {
        $this->userConfig = getConfig('user');
        $this->collection = $data['collection'];
        $this->total = $data['total'];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            'No',
            '회원등급-세부등급',
            '아이디',
            '이름(국문)',
            '이름(영문)',

            '생년월일',
            '성별',
            '이메일',
            '이메일 수신 동의',
            '휴대폰 번호',

            'SMS 수신 동의',
            '우편물 수령지',
            '직장명',
            '대표이사',
            '부서',

            '업태,종목',
            '직종',
            '직위',
            '직위(기타)',
            '직장 우편번호',

            '직장 주소',
            '직장 주소 상세',
            '직장 전화번호',
            '직장 팩스번호',
            '담당자 성명',

            '담당자 전화번호',
            '담당자 이메일',
            '자택 우편번호',
            '자택 주소',
            '자택 주소 상세',

            '자택 전화번호',
            '학위',
            '졸업(예정)연도',
            '취득국가',
            '취득기관',

            '지도교수',
            '국문논문명',
            '영문논문명',
            '관리자메모',
            '가입일',
            '수정일',
        ];
    }

    public function map($data): array
    {
        $userConfig = $this->userConfig;

        return [
            $this->total - ($this->row++),
            $userConfig['level'][$data->level] ?? '',
            $data->id,
            $data->name_kr ?? '',
            $data->name_en ?? '',

            $data->birthday,
            $data->sex,
            $data->email ?? '',
            $userConfig['receptionYn'][$data->emailReception] ?? '',
            $data->phone ?? '',

            $userConfig['receptionYn'][$data->smsReception] ?? '',
            $userConfig['post'][$data->post] ?? '',
            $data->company ?? '',
            $data->ceo ?? '',
            $data->department ?? '',

            $data->business ?? '',
            $data->job ?? '',
            $data->position ?? '',
            $data->position_etc ?? '',
            $data->company_zipcode ?? '',

            $data->company_address ?? '',
            $data->company_address2 ?? '',
            $data->companyTel ?? '',
            $data->companyFax ?? '',
            $data->manager ?? '',

            $data->managerTel ?? '',
            $data->managerEmail ?? '',
            $data->home_zipcode ?? '',
            $data->home_address ?? '',
            $data->home_address2 ?? '',

            $data->homeTel ?? '',
            $data->degree ?? '',
            $data->graduate ?? '',
            $data->degreeCountry ?? '',
            $data->degreeAgency ?? '',

            $data->tutor ?? '',
            $data->journalKor ?? '',
            $data->journalEng ?? '',
            $data->memo ?? '',
            !empty($data->created_at) ? $data->created_at->format('Y-m-d') : '',
            !empty($data->updated_at) ? $data->updated_at->format('Y-m-d') : '',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // HTML을 허용할 셀 범위를 지정
                $event->sheet->getStyle("A:ZZ")->getAlignment()->setWrapText(true);

                // 텍스트 높이 가운데로 정렬
                $event->sheet->getStyle('A:ZZ')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // 텍스트 가운데로 정렬
                $event->sheet->getStyle('A:ZZ')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 폰트 bold & size
                $event->sheet->getDelegate()->getStyle('A1:ZZ1')->getFont()->setBold(true)->setSize(10);
            },
        ];
    }
}

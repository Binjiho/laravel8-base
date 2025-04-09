<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AbstractExcel implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    private $collection;
    private $total;
    private $row = 0;

    public function __construct($data)
    {
        $this->userConfig = getConfig('user');
        $this->workshopConfig = $data['workshopConfig'];
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
            '접수번호',
            '제출상태',
            '제출자 이름',
            '제출자 직장명(소속)',

            '이메일',
            '대주제',
            '논문제목(국문)',
            '논문제목(영문)',
            '최초 등록일',

            '최종 수정일',
            '삭제일',
        ];
    }

    public function map($data): array
    {
        $workshopConfig = $this->workshopConfig;

        return [
            $this->total - ($this->row++),
            $data->regnum,
            $workshopConfig['status'][$data->status] ?? '',
            $data->registration->name_kr ?? '',
            $data->registration->sosok_kr ?? '',

            $data->registration->email ?? '',
            $workshopConfig['topic'][$data->topic] ?? '',
            $data->title_kr ?? '',
            $data->title_en ?? '',

            !empty($data->created_at) ? $data->created_at->format('Y-m-d') : '',
            !empty($data->updated_at) ? $data->updated_at->format('Y-m-d') : '',
            !empty($data->deleted_at) ? $data->deleted_at->format('Y-m-d') : '',
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

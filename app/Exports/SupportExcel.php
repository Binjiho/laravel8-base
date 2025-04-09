<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SupportExcel implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
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
            '상태',
            '접수번호',
            '회사명',
            '담당자명',
            '담당자 핸드폰',

            '담당자 이메일',
            '구분',
            '금액',
            '결제방법',
            '결제상태',

            '입금 예정일',
            '최초 등록일',
            '최종 결제일',
            '삭제일',
        ];
    }

    public function map($data): array
    {
        $workshopConfig = $this->workshopConfig;

        return [
            $this->total - ($this->row++),
            $workshopConfig['status'][$data->status] ?? '',
            $data->regnum,
            $data->company ?? '',
            $data->manager ?? '',
            $data->phone ?? '',

            $data->email ?? '',
            $workshopConfig['grade'][$data->grade]['name'] ?? '',
            $data->price ?? '',
            $workshopConfig['spay_method'][$data->spay_method] ?? '',
            $workshopConfig['spayment_status'][$data->spayment_status] ?? '',

            !empty($data->deposit_date) ? $data->deposit_date->format('Y-m-d') : '',
            !empty($data->created_at) ? $data->created_at->format('Y-m-d') : '',
            !empty($data->payment_date) ? $data->payment_date->format('Y-m-d') : '',
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

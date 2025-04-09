<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RegistrationExcel implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    private $userConfig;
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
            '참가구분',
            '등록구분',
            '이름',
            '직장명(소속)',

            '이메일',
            '휴대폰 번호',
            '셔틀버스 수요조사',
            '등록비',
            '결제상태',

            '결제방법',
            '결제일',
            '최초등록일',
            '최종등록일',
            '환불신청일',

            '환불사유',
            '환불방법',
            '환불은행명',
            '환불계좌번호',
            '예금주',

            '삭제일',
            '메모',
        ];
    }

    public function map($data): array
    {
        $workshopConfig = $this->workshopConfig;

        return [
            $this->total - ($this->row++),
            $data->regnum,
            $workshopConfig['gubun'][$data->gubun] ?? '',
            $workshopConfig['category'][$data->category]['name'] ?? '',
            $data->name_kr ?? '',
            $data->sosok_kr ?? '',

            $data->email,
            $data->phone,
            $workshopConfig['shuttle_yn'][$data->shuttle_yn] ?? '',
            !empty($data->price) ? number_format($data->price ?? 0) : '',
            $workshopConfig['payment_status'][$data->payment_status] ?? '',

            $workshopConfig['payment_method'][$data->payment_method] ?? '',
            !empty($data->payment_date) ? $data->payment_date->format('Y-m-d') : '',
            !empty($data->created_at) ? $data->created_at->format('Y-m-d') : '',
            !empty($data->complete_at) ? $data->complete_at->format('Y-m-d') : '',
            !empty($data->refund_at) ? $data->refund_at->format('Y-m-d') : '',

            $data->refund_reason,
            $workshopConfig['refund_method'][$data->refund_method] ?? '',
            $data->refund_bank,
            $data->refund_num,
            $data->account_name,
            
            !empty($data->deleted_at) ? $data->deleted_at->format('Y-m-d') : '',
            $data->admin_memo ?? '',
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

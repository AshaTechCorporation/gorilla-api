@php
    $months = [
        1 => 'JAN',
        2 => 'FEB',
        3 => 'MAR',
        4 => 'APR',
        5 => 'MAY',
        6 => 'JUN',
        7 => 'JUL',
        8 => 'AUG',
        9 => 'SEP',
        10 => 'OCT',
        11 => 'NOV',
        12 => 'DEC',
    ];
@endphp

<table>
    @php
        // Assuming $data is not empty
        $firstProductTimeline = $data[0];
        $monthName = isset($months[$firstProductTimeline->month]) ? $months[$firstProductTimeline->month] : 'Unknown';
        $year = $firstProductTimeline->year;
    @endphp

    <tr></tr> <!-- Skip the rows -->
    <tr>
        <td></td> <!-- Skip the column -->
        <th colspan="47" rowspan="2"
            style="text-align:center; vertical-align: middle; font-size: 16px; background-color: yellow; border: 5px solid black; font-weight: bold;">
            Checklist Draft &amp; Stat_{{ $monthName }} {{ $year }}
        </th>
    </tr>
    <tr></tr> <!-- Skip the rows -->
    @foreach ($data as $productTimeline)
        {{-- @php
            $monthName = isset($months[$productTimeline->month]) ? $months[$productTimeline->month] : 'Unknown';
        @endphp
        <tr></tr> 
        <tr>
            <td></td> 
            <th colspan="47" rowspan="2"
                style="text-align:center; vertical-align: middle; font-size: 16px; background-color: yellow; border: 5px solid black; font-weight: bold;">
                Checklist Draft &amp; Stat_{{ $monthName }} {{ $productTimeline->year }}
            </th>
        </tr>
        <tr></tr>  --}}
        @foreach ($productTimeline->product_items as $productItem)
        <tr></tr> <!-- Skip the rows -->
            <tr>
                <td></td> <!-- Skip the column -->
                <th
                    style="border: 1px solid black; text-align:center; background-color: brown; color: #e8e8e8; font-weight: bold">
                    NO
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: brown; color: #e8e8e8; font-weight: bold;width: 200px">
                    Name/Channel
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: brown; color: #e8e8e8; font-weight: bold;width: 200px">

                </th>
                <th colspan="3"
                    style="border: 1px solid black; text-align:center; background-color: orange; font-weight: bold;">
                    Link ส่งดราฟ 1
                </th>
                <th colspan="3"
                    style="border: 1px solid black; text-align:center; background-color: orange; font-weight: bold;">
                    Link ส่งดราฟ 2
                </th>
                <th colspan="3"
                    style="border: 1px solid black; text-align:center; background-color: orange; font-weight: bold;">
                    Link ส่งดราฟ 3
                </th>
                <th rowspan="2"
                    style="border: 1px solid black; text-align:center; background-color: #90ee90; font-weight: bold; vertical-align: middle;width: 150px">
                    Draft Status
                </th>
                <th rowspan="2"
                    style="border: 1px solid black; text-align:center; background-color: brown; color: #e8e8e8; font-weight: bold;width: 150px;vertical-align: middle;">
                    วันที่ลงงาน
                </th>
                <th rowspan="2"
                    style="border: 1px solid black; text-align:center; background-color: brown; color: #e8e8e8; font-weight: bold;width: 150px;vertical-align: middle;">
                    Post Done
                </th>
                <th rowspan="2"
                    style="border: 1px solid black; text-align:center; background-color: brown; color: #e8e8e8; font-weight: bold;width: 150px;vertical-align: middle;">
                    Link Post
                </th>
                <th rowspan="2"
                    style="border: 1px solid black; text-align:center; background-color: brown; color: #e8e8e8; font-weight: bold;width: 150px;vertical-align: middle;">
                    Gen Code
                </th>
                <th colspan="4"
                    style="border: 1px solid black; text-align:center; background-color: blue; color: #e8e8e8; font-weight: bold;">
                    Stat
                </th>
                <th rowspan="2"
                    style="border: 1px solid black; text-align:center; background-color: black; color: #e8e8e8; font-weight: bold;width: 150px;vertical-align: middle;">
                    หมายเหตุ
                </th>
                <th rowspan="2"
                    style="border: 1px solid black; text-align:center; background-color: black; color: #e8e8e8; font-weight: bold;width: 150px;vertical-align: middle;">
                    ช่องทางการประสานงาน
                </th>
                <th colspan="23"
                    style="border: 1px solid black; text-align:center; background-color: yellow; font-weight: bold;">
                    รายละเอียดการจ่ายเงิน
                </th>
                <th rowspan="2"
                    style="border: 1px solid black; text-align:center; background-color: gray;font-weight: bold;width: 150px;vertical-align: middle;">
                    พนักงานรับผิดชอบ
                </th>
            </tr>
            <tr>
                <td></td> <!-- Skip the column -->
                <th colspan="2"
                    style="border: 1px solid black; text-align:center; background-color: #deb887; font-weight: bold; height: 50px; vertical-align: middle;">
                    {{ \App\Models\PlatformSocial::find($productItem->platform_social_id)->name }}
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: #deb887; font-weight: bold; height: 50px; vertical-align: middle;">
                    Link Social
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: #add8e6; font-weight: bold; height: 50px; vertical-align: middle; width: 150px">
                    ลิงค์ดราฟ 1
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: #ffcc99; font-weight: bold; height: 50px; vertical-align: middle; width: 200px">
                    สถานะ/ฟีดแบค จากทีมงาน
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: #90ee90; font-weight: bold; height: 50px; vertical-align: middle; width: 200px">
                    ฟึตแบคลูกค้า
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: #add8e6; font-weight: bold; height: 50px; vertical-align: middle; width: 150px">
                    ลิงค์ดราฟ 2
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: #ffcc99; font-weight: bold; height: 50px; vertical-align: middle; width: 200px">
                    สถานะ/ฟีดแบค จากทีมงาน
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: #90ee90; font-weight: bold; height: 50px; vertical-align: middle; width: 200px">
                    ฟึตแบคลูกค้า
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: #add8e6; font-weight: bold; height: 50px; vertical-align: middle; width: 150px">
                    ลิงค์ดราฟ 3
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: #ffcc99; font-weight: bold; height: 50px; vertical-align: middle; width: 200px">
                    สถานะ/ฟีดแบค จากทีมงาน
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: #90ee90; font-weight: bold; height: 50px; vertical-align: middle; width: 200px">
                    ฟึตแบคลูกค้า
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: blue; color: #e8e8e8; font-weight: bold; vertical-align: middle; width: 100px">
                    View
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: blue; color: #e8e8e8; font-weight: bold; vertical-align: middle; width: 100px">
                    Like
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: blue; color: #e8e8e8; font-weight: bold; vertical-align: middle; width: 100px">
                    Comment
                </th>
                <th
                    style="border: 1px solid black; text-align:center; background-color: blue; color: #e8e8e8; font-weight: bold; vertical-align: middle; width: 100px">
                    Share
                </th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle; width: 100px">
                    เรทจ้าง</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    ยอด</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    รายละเอียดค่าใช้จ่าย</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    ประเภทงาน</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    Vat 7%</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    หัก ณ ที่จ่าย<br>2%,3%,5%</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    ค่าสินค้า</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    ยอดโอน</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    วันที่โอนเงิน</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    หมายเหตุ</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    ชื่อบัญชี</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    เลขที่บัญชี</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    ธนาคาร</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    ชื่อตามบัตร<br>ประจำตัวประชาชน</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    เลขบัตรประชาชน</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    ที่อยู่ตามหน้าบัตร<br>ประจำตัวประชาชน</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    ที่อยู่จัดส่งสินค้ารีวิว</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    เบอร์ติดต่อ</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    ID Line</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    สำเนาหน้าบัตรประชาชน<br>สำเนาหน้าบุ๊คแบงค์</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    E-Mail ส่ง<br>ใบหัก ณ ที่จ่าย</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px">
                    ลิ้งใบหัก ณ ที่จ่าย</th>
                <th
                    style="border: 1px solid black; text-align: center; background-color: gray; font-weight: bold; height: 50px; vertical-align: middle;width: 100px;">
                    ใบเสนอราคา</th>
            </tr>
            <tr>
                <td></td> <!-- Skip the column -->
                <th colspan="47"
                    style="vertical-align: middle; background-color: gray; border: 5px solid black; font-weight: bold; font-size: 16px;">
                    Item ที่ {{ $productItem->id }} : {{ $productItem->name }}
                </th>
            </tr>
            @php
                $i = 0;
            @endphp
            @php
                $i = 0;
            @endphp
            @foreach ($productItem->project_timelines as $projectTimeline)
            <tr>
                <td style="border: 1px solid black;"></td> <!-- Skip the column -->
                <td style="text-align: center; border: 1px solid black;">{{ $i = $i + 1 }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->social_name ?? 'ไม่มีข้อมูล name' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->link_social ?? 'ไม่มีข้อมูล link' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->draft_link1 ?? 'ไม่มีข้อมูล link draft' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->admin_feedback1 ?? 'ไม่มีข้อมูล admin' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->client_feedback1 ?? 'ไม่มีข้อมูล client' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->draft_link2 ?? 'ไม่มีข้อมูล link draft2' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->client_feedback2 ?? 'ไม่มีข้อมูล client2' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->admin_feedback2 ?? 'ไม่มีข้อมูล admin2' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->draft_link3 ?? 'ไม่มีข้อมูล link draft3' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->client_feedback3 ?? 'ไม่มีข้อมูล client3' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->admin_feedback3 ?? 'ไม่มีข้อมูล admin3' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->draft_status ?? 'ไม่มีข้อมูล draft' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->post_date ?? 'ไม่มีข้อมูล post date' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->post_status ?? 'ไม่มีข้อมูล post status' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->post_link ?? 'ไม่มีข้อมูล post link' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->post_code ?? 'ไม่มีข้อมูล post code' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->stat_view ?? 'ไม่มีข้อมูล view' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->stat_like ?? 'ไม่มีข้อมูล like' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->stat_comment ?? 'ไม่มีข้อมูล comment' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->stat_share ?? 'ไม่มีข้อมูล share' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->note1 ?? 'ไม่มีข้อมูล note' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->contact ?? 'ไม่มีข้อมูล contact' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->pay_rate ?? 'ไม่มีข้อมูล rate ' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->sum_rate ?? 'ไม่มีข้อมูล sum rate' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->des_bill ?? 'ไม่มีข้อมูล des' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->content_style_id ?? 'ไม่มีข้อมูล content' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->vat ?? 'ไม่มีข้อมูล vat' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->withholding ?? 'ไม่มีข้อมูล withholding' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->product_price ?? 'ไม่มีข้อมูล price' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->transfer_amount ?? 'ไม่มีข้อมูล amount' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->transfer_date ?? 'ไม่มีข้อมูล date' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->note2 ?? 'ไม่มีข้อมูล note' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->bank_account ?? 'ไม่มีข้อมูล account' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->bank_id ?? 'ไม่มีข้อมูล bank' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->bank_brand ?? 'ไม่มีข้อมูล brand' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->name_of_card ?? 'ไม่มีข้อมูล name card' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->id_card ?? 'ไม่มีข้อมูล card id' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->address_of_card ?? 'ไม่มีข้อมูล address' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->product_address ?? 'ไม่มีข้อมูล product address' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->line_id ?? 'ไม่มีข้อมูล เบอร์โทร' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->line_id ?? 'ไม่มีข้อมูล line' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->image_card ?? 'ไม่มีข้อมูล image' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->transfer_email ?? 'ไม่มีข้อมูล transfer' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->transfer_link ?? 'ไม่มีข้อมูล transfer link' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->image_quotation ?? 'ไม่มีข้อมูล image quotation' }}</td>
                <td style="text-align: center; border: 1px solid black;">{{ $projectTimeline->ecode ?? 'ไม่มีข้อมูล ecode' }}</td>
            </tr>
            @endforeach
            <tr></tr>
        @endforeach
    @endforeach
</table>

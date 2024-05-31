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
            @foreach ($productItem->project_timelines as $projectTimeline)
                <tr>
                    <td></td> <!-- Skip the column -->
                    <td>{{ $projectTimeline->id }}</td>
                    <td>{{ $projectTimeline->social_name }}</td>
                    <td>{{ $projectTimeline->link_social }}</td>
                    <td>{{ $projectTimeline->draft_link1 }}</td>
                    <td>{{ $projectTimeline->admin_feedback1 }}</td>
                    <td>{{ $projectTimeline->client_feedback1 }}</td>
                    <td>{{ $projectTimeline->draft_link2 }}</td>
                    <td>{{ $projectTimeline->client_feedback2 }}</td>
                    <td>{{ $projectTimeline->admin_feedback2 }}</td>
                    <td>{{ $projectTimeline->draft_link3 }}</td>
                    <td>{{ $projectTimeline->client_feedback3 }}</td>
                    <td>{{ $projectTimeline->admin_feedback3 }}</td>
                    <td>{{ $projectTimeline->admin_status }}</td>
                    <td>{{ $projectTimeline->client_status }}</td>
                    <td>{{ $projectTimeline->draft_status }}</td>
                    <td>{{ $projectTimeline->post_date }}</td>
                    <td>{{ $projectTimeline->post_status }}</td>
                    <td>{{ $projectTimeline->post_link }}</td>
                    <td>{{ $projectTimeline->post_code }}</td>
                    <td>{{ $projectTimeline->stat_view }}</td>
                    <td>{{ $projectTimeline->stat_like }}</td>
                    <td>{{ $projectTimeline->stat_comment }}</td>
                    <td>{{ $projectTimeline->stat_share }}</td>
                    <td>{{ $projectTimeline->note1 }}</td>
                    <td>{{ $projectTimeline->contact }}</td>
                    <td>{{ $projectTimeline->pay_rate }}</td>
                    <td>{{ $projectTimeline->sum_rate }}</td>
                    <td>{{ $projectTimeline->des_bill }}</td>
                    <td>{{ $projectTimeline->content_style_id }}</td>
                    <td>{{ $projectTimeline->vat }}</td>
                    <td>{{ $projectTimeline->withholding }}</td>
                    <td>{{ $projectTimeline->product_price }}</td>
                    <td>{{ $projectTimeline->transfer_amount }}</td>
                    <td>{{ $projectTimeline->transfer_date }}</td>
                    <td>{{ $projectTimeline->note2 }}</td>
                    <td>{{ $projectTimeline->bank_account }}</td>
                    <td>{{ $projectTimeline->bank_id }}</td>
                    <td>{{ $projectTimeline->bank_brand }}</td>
                    <td>{{ $projectTimeline->name_of_card }}</td>
                    <td>{{ $projectTimeline->id_card }}</td>
                    <td>{{ $projectTimeline->address_of_card }}</td>
                    <td>{{ $projectTimeline->product_address }}</td>
                    <td>{{ $projectTimeline->line_id }}</td>
                    <td>{{ $projectTimeline->image_card }}</td>
                    <td>{{ $projectTimeline->transfer_email }}</td>
                    <td>{{ $projectTimeline->transfer_link }}</td>
                    <td>{{ $projectTimeline->image_quotation }}</td>
                    <td>{{ $projectTimeline->ecode }}</td>
                </tr>
            @endforeach
        @endforeach
    @endforeach
</table>

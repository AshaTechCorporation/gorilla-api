<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Html2Text\Html2Text;

class BlogController extends Controller
{
    public function getList()
    {
        $Item = Blog::get()->toarray();

        if (!empty($Item)) {

            for ($i = 0; $i < count($Item); $i++) {
                $Item[$i]['No'] = $i + 1;
                $Item[$i]['image'] = url($Item[$i]['image']);
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    public function getPage(Request $request)
    {
        $columns = $request->columns;
        $length = $request->length;
        $order = $request->order;
        $search = $request->search;
        $start = $request->start;
        $page = $start / $length + 1;

        $Status = $request->status;

        $col = array('id', 'name', 'image', 'description', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('', 'image', 'name', 'description', 'create_by');

        $D = Blog::select($col);

        if (isset($Status)) {
            $D->where('status', $Status);
        }

        if ($orderby[$order[0]['column']]) {
            $D->orderby($orderby[$order[0]['column']], $order[0]['dir']);
        }

        if ($search['value'] != '' && $search['value'] != null) {

            $D->Where(function ($query) use ($search, $col) {

                //search datatable
                $query->orWhere(function ($query) use ($search, $col) {
                    foreach ($col as &$c) {
                        $query->orWhere($c, 'like', '%' . $search['value'] . '%');
                    }
                });

                //search with
                // $query = $this->withPermission($query, $search);
            });
        }

        $d = $D->paginate($length, ['*'], 'page', $page);

        if ($d->isNotEmpty()) {

            //run no
            $No = (($page - 1) * $length);

            for ($i = 0; $i < count($d); $i++) {

                $No = $No + 1;
                $d[$i]->No = $No;
                $d[$i]->image = url($d[$i]->image);
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $d);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $loginBy = $request->login_by;

        if (!isset($request->name)) {
            return $this->returnErrorData('กรุณาระบุชื่อให้เรียบร้อย', 404);
        } else


            //     $checkName = Style::where('name', $request->name)->first();
            // if ($checkName) {
            //     return $this->returnErrorData('มีชื่อ ' . $request->name . ' ในระบบแล้ว', 404);
            // }


            DB::beginTransaction();

        try {
            $Item = new Blog();
            $Item->blog_category_id = $request->blog_category_id;
            $Item->name = $request->name;

            if ($request->image && $request->image != null && $request->image != 'null') {
                $Item->image = $this->uploadImage($request->image, '/images/blogs/');
            }

            $Item->description = $request->description;

            $Item->save();
            //

            //log
            $userId = "admin";
            $type = 'เพิ่มรายการ';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type . ' ' . $request->name;
            $this->Log($userId, $description, $type);
            //

            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Item = Blog::where('id', $id)
            ->first();

        if ($Item) {
            $Item->image = url($Item->image);
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blog $blog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        DB::beginTransaction();

        try {

            $Item = Blog::find($id);
            $Item->delete();

            //log
            $userId = "admin";
            $type = 'ลบผู้ใช้งาน';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ' . $type;
            $this->Log($userId, $description, $type);
            //

            DB::commit();

            return $this->returnUpdate('ดำเนินการสำเร็จ');
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
        }
    }

    public function updateData(Request $request)
    {

        DB::beginTransaction();

        try {
            if (!isset($request->id) || $request->id == "null") {
                $Item = new Blog();
            } else {
                $Item = Blog::find($request->id);
            }

            if (!$Item) {
                return $this->returnErrorData('ไม่พบรายการนี้ในระบบ', 404);
            }

            $Item->blog_category_id = $request->blog_category_id;
            $Item->name = $request->name;

            if ($request->image && $request->image != null && $request->image != 'null') {
                $Item->image = $this->uploadImage($request->image, '/images/blogs/');
            }

            $Item->description = $request->description;

            $Item->save();


            //

            //log
            $userId = "admin";
            $type = 'แก้ไข';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการเพิ่ม ' . $request->username;
            $this->Log($userId, $description, $type);
            //

            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e, 404);
        }
    }

    public function checkByKeyword(Request $request)
    {
        $return = array();
        $return2 = array();
        $tags = $request->tags;
        $description = $request->description;

        $keys = explode(',', $tags);
        $sum = 0;
        $html = new Html2Text($request->description); // Setup the html2text obj.
        $text = strtolower($html->getText()); // Execute the getText() function and convert all text to lower case to prevent work duplication
        $totalWordCount = str_word_count($text); // Get the total count of words in the text string
        $n = 0;
        foreach ($keys as $key) {
            $n++;
            array_push($return, array(
                "keyword" => $key,
                "count" => substr_count($description, $key),
                "density" => round((substr_count($description, $key) / $totalWordCount) * 100, 2)
            ));

            $sum = $sum + round((substr_count($description, $key) / $totalWordCount) * 100, 2);
        }

        $total =  $n * 5;
        $s2 = $sum / $total;

        $s3 = $s2 * 10;

        if ($s3 > 10) {
            $s4 = $total - $s3;
        } else {
            $s4 = $s3;
        }
        array_push($return2, array(
            "keys" => $return,
            "summary" => $sum,
            "point" => number_format(abs($s4), 0),
        ));

        return $this->returnSuccess('ดำเนินการสำเร็จ', $return2);
    }

    // public function checkKeyword(Request $request)
    // {
    //     $str = <<<__EOF
    //     $request->description
    //     __EOF;
    //     $keyword = $request->keyword;

    //     $Item = $this->getLongTailKeywords($str, $keyword);
    //     return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
    // }

    public function getLongTailKeywords($str, $keyword, $len = 3, $min = 2)
    {
        $keywords = array();
        $common = array('ฉัน', 'หนึ่ง', 'เกี่ยวกับ', 'อันหนึ่ง', 'และ', 'พวกเรา', 'กับ', 'ที่', 'เป็น', 'โดย', 'com', 'de', 'อังกฤษ', 'สำหรับ', 'จาก', 'อย่างไร', 'ใน', 'คือ', 'มัน', 'la', 'กับ', 'บน', 'หรือ', 'นั้น', 'ถึง', 'นี่', 'ถึง', 'กับ', 'อะไร', 'เมื่อไหร่', 'ที่ไหน', 'ใคร', 'จะ', 'ด้วยกัน', 'und', 'the', 'www');
        $str = preg_replace('/[^a-z0-9\s-]+/', '', strtolower(strip_tags($str)));
        $str = preg_split('/\s+-\s+|\s+/', $str, -1, PREG_SPLIT_NO_EMPTY);
        while (0 < $len--) for ($i = 0; $i < count($str) - $len; $i++) {
            $word = array_slice($str, $i, $len + 1);
            if (in_array($word[0], $common) || in_array(end($word), $common)) continue;
            $word = implode(' ', $word);
            if (!isset($keywords[$len][$word])) $keywords[$len][$word] = 0;
            $keywords[$len][$word]++;
        }
        $return = array();
        foreach ($keywords as &$keyword) {
            $keyword = array_filter($keyword, function ($v) use ($min) {
                return !!($v > $min);
            });
            arsort($keyword);
            $return = array_merge($return, $keyword);
        }
        return $return;
    }

    public function checkKeyword(Request $request)
    {

        if (isset($request->description)) { // Test the parameter is set.
            $html = new Html2Text($request->description); // Setup the html2text obj.
            $text = strtolower($html->getText()); // Execute the getText() function and convert all text to lower case to prevent work duplication
            $totalWordCount = str_word_count($text); // Get the total count of words in the text string
            $wordsAndOccurrence  = array_count_values(str_word_count($text, 1)); // Get each word and the occurrence count as key value array
            arsort($wordsAndOccurrence); // Sort into descending order of the array value (occurrence)

            $keywordDensityArray = [];
            // Build the array
            foreach ($wordsAndOccurrence as $key => $value) {
                $keywordDensityArray[] = [
                    "keyword" => $key, // keyword
                    "count" => $value, // word occurrences
                    "density" => round(($value / $totalWordCount) * 100, 2)
                ]; // Round density to two decimal places.
            }

            return $this->returnSuccess('ดำเนินการสำเร็จ', $keywordDensityArray);
        }
    }
}

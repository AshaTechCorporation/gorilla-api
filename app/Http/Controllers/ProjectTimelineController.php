<?php

namespace App\Http\Controllers;

use App\Models\ProjectTimeline;
use App\Models\ProductTimeline;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Influencer;
use App\Models\SubType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class ProjectTimelineController extends Controller
{
    public function getList($id)
    {
        $Item = ProjectTimeline::where('project_id', $id)
            ->get()->toarray();

        if (!empty($Item)) {

            for ($i = 0; $i < count($Item); $i++) {
                $Item[$i]['No'] = $i + 1;
                $Item[$i]['project'] = Project::find($id);
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getPage(Request $request)
    {
        $columns = $request->columns;
        $length = $request->length;
        $order = $request->order;
        $search = $request->search;
        $start = $request->start;
        $page = $start / $length + 1;


        $col = array('id', 'project_id', 'influencer_id', 'draft_link_1', 'client_feedback_1', 'admin_feedback_1', 'status_1', 'draft_link_2', 'client_feedback_2', 'admin_feedback_2', 'status_2', 'draft_status', 'post_date', 'post_status', 'post_link', 'post_code', 'stat_view', 'stat_like', 'stat_comment', 'stat_share', 'remark', 'created_at', 'updated_at');

        $orderby = array('id', 'project_id', 'influencer_id', 'draft_link_1', 'client_feedback_1', 'admin_feedback_1', 'status_1', 'draft_link_2', 'client_feedback_2', 'admin_feedback_2', 'status_2', 'draft_status', 'post_date', 'post_status', 'post_link', 'post_code', 'stat_view', 'stat_like', 'stat_comment', 'stat_share', 'remark', 'created_at', 'updated_at');

        $D = Project::select($col);

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
            });
        }

        $d = $D->paginate($length, ['*'], 'page', $page);

        if ($d->isNotEmpty()) {

            //run no
            $No = (($page - 1) * $length);

            for ($i = 0; $i < count($d); $i++) {

                $No = $No + 1;
                $d[$i]->No = $No;
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $d);
    }

    public function getProductTimelineByMonth(Request $request)
    {
        $project_id = $request->project_id;
        $month = $request->month;
        $year = $request->year;

        $item = ProductTimeline::where('project_id', $project_id)
            ->where('month', $month)
            ->where('year', $year)
            ->with('product_items')
            ->get();

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $item);
    }
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
        $loginBy = "admin";

        if (!isset($request->project_id)) {
            return $this->returnErrorData('กรุณาระบุ project_id ให้เรียบร้อย', 404);
        } else if (!isset($request->influencer_id)) {
            return $this->returnErrorData('กรุณาระบุ influencer_id ให้เรียบร้อย', 404);
        } else

            DB::beginTransaction();

        try {
            $Item = new ProjectTimeline();

            $Item->project_id = $request->project_id;

            $project_id = Project::find($Item->project_id);
            if (!$project_id) {
                return $this->returnErrorData('ไม่พบ project_id', 404);
            }

            $Item->influencer_id = $request->influencer_id;

            $influencer_id = Influencer::find($Item->influencer_id);
            if (!$influencer_id) {
                return $this->returnErrorData('ไม่พบ influencer_id', 404);
            }

            $Item->save();

            //log
            $userId = $loginBy;
            $type = 'เพิ่มข้อมูล';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
            $this->Log($userId, $description, $type);


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
     * @param  \App\Models\ProjectTimeline  $projectTimeline
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Item = ProjectTimeline::find($id);
        if (!$Item) {
            return $this->returnErrorData('ไม่พบข้อมูลที่ท่านต้องการ', 404);
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProjectTimeline  $projectTimeline
     * @return \Illuminate\Http\Response
     */
    public function edit(ProjectTimeline $projectTimeline)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProjectTimeline  $projectTimeline
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProjectTimeline $projectTimeline)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProjectTimeline  $projectTimeline
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = ProjectTimeline::find($id);
            $Item->delete();

            //log
            $userId = "admin";
            $type = 'ลบข้อมูล';
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
}

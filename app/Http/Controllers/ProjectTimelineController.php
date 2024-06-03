<?php

namespace App\Http\Controllers;

use App\Models\ProjectTimeline;
use App\Models\ProductTimeline;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProductItem;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Influencer;
use App\Models\SubType;
use App\Models\InfluProject;
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
        $platform_social_id = $request->platform_social_id;
    
        // Initialize the query
        $query = ProductTimeline::query()
            ->where('project_id', $project_id)
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('id', 'asc');
    
        // Apply whereHas if platform_social_id is provided
        if ($platform_social_id) {
            $query->whereHas('product_items', function ($query) use ($platform_social_id) {
                $query->where('platform_social_id', $platform_social_id);
            });
        }
    
        // Eager load product_items with project_timelines
        $query->with(['product_items' => function ($query) use ($platform_social_id) {
            if ($platform_social_id) {
                $query->where('platform_social_id', $platform_social_id);
            }
            $query->with('project_timelines');
        }]);
    
        // Execute the query
        $items = $query->get();
    
        // Flatten the product items
        $productItems = $items->flatMap(function ($item) {
            return $item->product_items;
        });
    
        // Convert the result to an array
        $productItemsArray = $productItems->toArray();
    
        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $productItemsArray);
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

        DB::beginTransaction();

        try {
            foreach ($request->tables as $table) {
                foreach ($table['rows'] as $value) {
                    $Item = new ProjectTimeline();

                    // $Item->project_id = $value['project_id'];
                    $Item->influencer_id = $value['influencer_id'];
                    $Item->product_item_id = $value['product_item_id'];
                    $Item->draft_link1 = $value['draft_link1'];
                    $Item->client_feedback1 = $value['client_feedback1'];
                    $Item->admin_feedback1 = $value['admin_feedback1'];
                    $Item->draft_link2 = $value['draft_link2'];
                    $Item->client_feedback2 = $value['client_feedback2'];
                    $Item->admin_feedback2 = $value['admin_feedback2'];
                    $Item->draft_link3 = $value['draft_link3'];
                    $Item->client_feedback3 = $value['client_feedback3'];
                    $Item->admin_feedback3 = $value['admin_feedback3'];
                    $Item->admin_status = $value['admin_status'];
                    $Item->client_status = $value['client_status'];
                    $Item->draft_status = $value['draft_status'];
                    $Item->post_date = $value['post_date'];
                    $Item->post_status = $value['post_status'];
                    $Item->post_link = $value['post_link'];
                    $Item->post_code = $value['post_code'];
                    $Item->stat_view = $value['stat_view'];
                    $Item->stat_like = $value['stat_like'];
                    $Item->stat_comment = $value['stat_comment'];
                    $Item->stat_share = $value['stat_share'];
                    $Item->note1 = $value['note1'];
                    $Item->note2 = $value['note2'];
                    $Item->contact = $value['contact'];
                    $Item->pay_rate = $value['pay_rate'];
                    $Item->sum_rate = $value['sum_rate'];
                    $Item->des_bill = $value['des_bill'];
                    $Item->content_style_id = $value['content_style_id'];
                    $Item->vat = $value['vat'];
                    $Item->withholding = $value['withholding'];
                    $Item->product_price = $value['product_price'];
                    $Item->transfer_amount = $value['transfer_amount'];
                    $Item->transfer_date = $value['transfer_date'];
                    $Item->bank_account = $value['bank_account'];
                    $Item->bank_id = $value['bank_id'];
                    $Item->bank_brand = $value['bank_brand'];
                    $Item->name_of_card = $value['name_of_card'];
                    $Item->id_card = $value['id_card'];
                    $Item->address_of_card = $value['address_of_card'];
                    $Item->product_address = $value['product_address'];
                    $Item->line_id = $value['line_id'];
                    $Item->image_card = $value['image_card'];
                    $Item->transfer_email = $value['transfer_email'];
                    $Item->transfer_link = $value['transfer_link'];
                    $Item->image_quotation = $value['image_quotation'];
                    $Item->ecode = $value['ecode'];
                    $Item->create_by = $loginBy;
                    $Item->update_by = $loginBy;

                    $Item->save();
                }
            }

            //log
            $userId = $loginBy;
            $type = 'เพิ่มข้อมูล';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
            $this->Log($userId, $description, $type);

            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {
            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e->getMessage(), 500);
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

    public function updateTimeline(Request $request)
    {
        $loginBy = "admin";

        DB::beginTransaction();

        try {
            foreach ($request->tables as $table) {
                foreach ($table['rows'] as $value) {
                    $Item = ProjectTimeline::find($value['item_id']);

                    $Item->influencer_id = $value['influencer_id'];

                    $existingProductTimeline = InfluProject::where('project_id', $value['project_id'])
                        ->where('influencer_id', $value['influencer_id'])
                        ->first();

                    if (!$existingProductTimeline) {
                        if ($value['project_id']) {
                            $project = Project::find($value['project_id']);

                            if ($project == null) {
                                return $this->returnErrorData('เกิดข้อผิดพลาดที่ $projects กรุณาลองใหม่อีกครั้ง ', 404);
                            } else {
                                $status = "working";
                                $project->influencers()->attach($value['influencer_id'], ['status' => $status]);
                            }
                        }
                    }


                    $Item->product_item_id = $value['product_item_id'];
                    $Item->social_name = $value['social_name'];
                    $Item->link_social = $value['link_social'];
                    $Item->draft_link1 = $value['draft_link1'];
                    $Item->client_feedback1 = $value['client_feedback1'];
                    $Item->admin_feedback1 = $value['admin_feedback1'];
                    $Item->draft_link2 = $value['draft_link2'];
                    $Item->client_feedback2 = $value['client_feedback2'];
                    $Item->admin_feedback2 = $value['admin_feedback2'];
                    $Item->draft_link3 = $value['draft_link3'];
                    $Item->client_feedback3 = $value['client_feedback3'];
                    $Item->admin_feedback3 = $value['admin_feedback3'];
                    $Item->admin_status = $value['admin_status'];
                    $Item->client_status = $value['client_status'];
                    $Item->draft_status = $value['draft_status'];
                    $Item->post_date = $value['post_date'];
                    $Item->post_status = $value['post_status'];
                    $Item->post_link = $value['post_link'];
                    $Item->post_code = $value['post_code'];
                    $Item->stat_view = $value['stat_view'];
                    $Item->stat_like = $value['stat_like'];
                    $Item->stat_comment = $value['stat_comment'];
                    $Item->stat_share = $value['stat_share'];
                    $Item->note1 = $value['note1'];
                    $Item->note2 = $value['note2'];
                    $Item->contact = $value['contact'];
                    $Item->pay_rate = $value['pay_rate'];
                    $Item->sum_rate = $value['sum_rate'];
                    $Item->des_bill = $value['des_bill'];
                    $Item->content_style_id = $value['content_style_id'];
                    $Item->vat = $value['vat'];
                    $Item->withholding = $value['withholding'];
                    $Item->product_price = $value['product_price'];
                    $Item->transfer_amount = $value['transfer_amount'];
                    $Item->transfer_date = $value['transfer_date'];
                    $Item->bank_account = $value['bank_account'];
                    $Item->bank_id = $value['bank_id'];
                    $Item->bank_brand = $value['bank_brand'];
                    $Item->name_of_card = $value['name_of_card'];
                    $Item->id_card = $value['id_card'];
                    $Item->address_of_card = $value['address_of_card'];
                    $Item->product_address = $value['product_address'];
                    $Item->line_id = $value['line_id'];
                    $Item->image_card = $value['image_card'];
                    $Item->transfer_email = $value['transfer_email'];
                    $Item->transfer_link = $value['transfer_link'];
                    $Item->image_quotation = $value['image_quotation'];
                    $Item->ecode = $value['ecode'];
                    $Item->create_by = $loginBy;
                    $Item->update_by = $loginBy;

                    $Item->save();
                }
            }

            //log
            $userId = $loginBy;
            $type = 'เพิ่มข้อมูล';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
            $this->Log($userId, $description, $type);

            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {
            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e->getMessage(), 500);
        }
    }

    public function updateStatus(Request $request)
    {
        $loginBy = "admin";
        DB::beginTransaction();
        try {
            $Item = ProjectTimeline::find($request->id);
            if ($request->user_type == 'employee') {
                if ($Item->round == 0) {
                    $Item->admin_status = $request->admin_status;
                    if ($Item->admin_status == "approve" && $Item->client_status == "approve") {
                        $Item->draft_status = "TRUE";
                        $Item->admin_feedback1 = $request->feedback;
                    } else {
                        $Item->admin_feedback1 = $request->feedback;
                    }
                } elseif ($Item->round == 1) {
                    $Item->admin_status = $request->admin_status;
                    if ($Item->client_status == "approve" && $Item->admin_status == "approve") {
                        $Item->draft_status = "TRUE";
                        $Item->admin_feedback2 = $request->feedback;
                    } else {
                        $Item->admin_feedback2 = $request->feedback;
                    }
                } elseif ($Item->round == 2) {
                    $Item->admin_status = $request->admin_status;
                    if ($Item->client_status == "approve" && $Item->admin_status == "approve") {
                        $Item->draft_status = "TRUE";
                        $Item->admin_feedback3 = $request->feedback;
                    } else {
                        $Item->admin_feedback3 = $request->feedback;
                    }
                } else {
                    $Item->draft_status = "FALSE";
                }
            }

            if ($request->user_type == 'customer') {
                if ($Item->round = 0) {
                    $Item->client_status = $request->client_status;
                    if ($Item->client_status == "approve" && $Item->admin_status == "approve") {
                        $Item->draft_status = "TRUE";
                        $Item->client_feedback1 = $request->feedback;
                    } else {
                        $Item->client_feedback1 = $request->feedback;
                        $Item->admin_status = "waiting";
                        $Item->round = 1;
                    }
                } elseif ($Item->round = 1) {
                    $Item->client_status = $request->client_status;
                    if ($Item->client_status == "approve" && $Item->admin_status == "approve") {
                        $Item->draft_status = "TRUE";
                        $Item->client_feedback2 = $request->feedback;
                    } else {
                        $Item->client_feedback2 = $request->feedback;
                        $Item->admin_status = "waiting";
                        $Item->round = 2;
                    }
                } elseif ($Item->round = 2) {
                    $Item->client_status = $request->client_status;
                    if ($Item->client_status == "approve" && $Item->admin_status == "approve") {
                        $Item->draft_status = "TRUE";
                        $Item->client_feedback3 = $request->feedback;
                    } else {
                        $Item->client_feedback3 = $request->feedback;
                        $Item->draft_status = "FALSE";
                    }
                }
            }
            $Item->update_by = $loginBy;
            $Item->save();
            DB::commit();
            return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e->getMessage(), 500);
        }
    }

    public function kpicalculate(Request $request)
    {
        try {
            $projectId = $request->input('project_id');
            $month = $request->input('month');
            $year = $request->input('year');
            $productItemId = $request->input('product_item_id');

            if ($projectId && !$month && !$year && !$productItemId) {
                // Query for the sum at the project level
                $project = Project::with('product_timelines.product_items.project_timelines')
                    ->findOrFail($projectId);

                $data = $this->calculateProjectTotals($project);

                return $this->returnSuccess('ดำเนินการสำเร็จ', $data);
            } elseif ($projectId && $month && $year && !$productItemId) {
                // Query for the sum at the product_timelines level
                $productTimeline = ProductTimeline::whereHas('projects', function ($query) use ($projectId) {
                    $query->where('id', $projectId);
                })->where('month', $month)
                    ->where('year', $year)
                    ->with('product_items.project_timelines')
                    ->firstOrFail();

                $data = $this->calculateProductTimelineTotals($productTimeline);

                return $this->returnSuccess('ดำเนินการสำเร็จ', $data);
            } elseif ($productItemId) {
                // Query for the sum at the product_items level
                $productItem = ProductItem::with('project_timelines')
                    ->findOrFail($productItemId);

                $data = $this->calculateProductItemTotals($productItem);

                return $this->returnSuccess('ดำเนินการสำเร็จ', $data);
            } else {
                return $this->returnErrorData('ข้อมูลไม่ถูกต้อง', 400);
            }
        } catch (\Throwable $e) {
            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e->getMessage(), 500);
        }
    }

    private function calculateProjectTotals($project)
    {
        $totalView = 0;
        $totalLike = 0;
        $totalComment = 0;
        $totalShare = 0;

        foreach ($project->product_timelines as $productTimeline) {
            foreach ($productTimeline->product_items as $productItem) {
                foreach ($productItem->project_timelines as $projectTimeline) {
                    $totalView += $projectTimeline->stat_view ?? 0;
                    $totalLike += $projectTimeline->stat_like ?? 0;
                    $totalComment += $projectTimeline->stat_comment ?? 0;
                    $totalShare += $projectTimeline->stat_share ?? 0;
                }
            }
        }

        return [
            'total_view' => $totalView,
            'total_like' => $totalLike,
            'total_comment' => $totalComment,
            'total_share' => $totalShare,
        ];
    }

    private function calculateProductTimelineTotals($productTimeline)
    {
        $totalView = 0;
        $totalLike = 0;
        $totalComment = 0;
        $totalShare = 0;

        foreach ($productTimeline->product_items as $productItem) {
            foreach ($productItem->project_timelines as $projectTimeline) {
                $totalView += $projectTimeline->stat_view ?? 0;
                $totalLike += $projectTimeline->stat_like ?? 0;
                $totalComment += $projectTimeline->stat_comment ?? 0;
                $totalShare += $projectTimeline->stat_share ?? 0;
            }
        }

        return [
            'total_view' => $totalView,
            'total_like' => $totalLike,
            'total_comment' => $totalComment,
            'total_share' => $totalShare,
        ];
    }

    private function calculateProductItemTotals($productItem)
    {
        $totalView = 0;
        $totalLike = 0;
        $totalComment = 0;
        $totalShare = 0;

        foreach ($productItem->project_timelines as $projectTimeline) {
            $totalView += $projectTimeline->stat_view ?? 0;
            $totalLike += $projectTimeline->stat_like ?? 0;
            $totalComment += $projectTimeline->stat_comment ?? 0;
            $totalShare += $projectTimeline->stat_share ?? 0;
        }

        return [
            'total_view' => $totalView,
            'total_like' => $totalLike,
            'total_comment' => $totalComment,
            'total_share' => $totalShare,
        ];
    }
}

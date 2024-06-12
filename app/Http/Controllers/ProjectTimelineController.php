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


        $col = array('id', 'product_timeline_id', 'product_id', 'name', 'platform_social_id', 'subscribe', 'description', 'qty');

        $orderby = array('id', 'product_timeline_id', 'product_id', 'name', 'platform_social_id', 'subscribe', 'description', 'qty');

        $D = ProductItem::select($col);

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

        if (isset($request->year)) {
            $year = $request->year;
            if ($year) {
                $D->whereHas('product_timelines', function ($query) use ($year) {
                    $query->where('year', $year);
                });
            }

            // Eager load product_items with project_timelines
            $D->with(['product_timelines' => function ($query) use ($year) {
                $query->where('year', $year);
            }]);
        }

        if (isset($request->month)) {

            $month = $request->month;
            if ($month) {
                $D->whereHas('product_timelines', function ($query) use ($month) {
                    $query->where('month', $month);
                });
            }

            // Eager load product_items with project_timelines
            $D->with(['product_timelines' => function ($query) use ($month) {
                $query->where('month', $month);
            }]);
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

    public function searchData(Request $request)
    {
        try {
            $key = $request->input('key');
            $Item = Influencer::where('fullname', 'like', "%{$key}%")
                ->limit(20)
                ->get()->toarray();

            if (!empty($Item)) {

                for ($i = 0; $i < count($Item); $i++) {
                    $Item[$i]['No'] = $i + 1;
                }
            }

            return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
        } catch (\Exception $e) {
            return $this->returnErrorData($e->getMessage(), 404);
        }
    }
    public function getProductTimelineByMonth(Request $request)
    {
        $project_id = $request->project_id;
        $month = $request->month;
        $year = $request->year;
        $platform_social_id = $request->platform_social_id;
        $key = $request->key;

        // Initialize the query
        $query = ProductTimeline::query()
            ->where('project_id', $project_id)
            ->orderBy('id', 'asc');

        if ($month && $year) {
            $query->where('month', $month)
                ->where('year', $year);
        }

        if ($platform_social_id) {
            $query->whereHas('product_items', function ($query) use ($platform_social_id) {
                $query->where('platform_social_id', $platform_social_id);
            });
        }

        $query->with(['product_items' => function ($query) use ($platform_social_id) {
            if ($platform_social_id) {
                $query->where('platform_social_id', $platform_social_id);
            }
            $query->with('project_timelines');
        }]);

        if ($key) {
            $query->whereHas('product_items', function ($query) use ($key) {
                $query->where('name', $key)
                    ->orWhereHas('project_timelines', function ($query) use ($key) {
                        $query->where('social_name', 'like', "%{$key}%")
                            ->orWhere('link_social', 'like', "%{$key}%")
                            ->orWhere('note1', 'like', "%{$key}%")
                            ->orWhere('note2', 'like', "%{$key}%")
                            ->orWhere('contact', 'like', "%{$key}%")
                            ->orWhere('ecode', 'like', "%{$key}%")
                            ->orWhere('create_by', 'like', "%{$key}%")
                            ->orWhere('update_by', 'like', "%{$key}%");
                    });
            });
        }

        // Get the items
        $items = $query->get();


        // Flatten the product items
        $productItems = $items->flatMap(function ($item) {
            return $item->product_items;
        });

        // Convert the result to an array
        $productItemsArray = $productItems->toArray();

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $productItemsArray);
    }

    public function addRowinItem(Request $request)
    {
        $loginBy = "admin";

        DB::beginTransaction();

        try {

            $ItemP = ProductItem::find($request->product_item_id);
            $ItemP->qty = $ItemP->qty + 1;
            $ItemP->save();
            $ItemInf = new ProjectTimeline();

            $ItemInf->product_item_id = $ItemP->id;
            $ItemInf->create_by = $loginBy;
            $ItemInf->ecode = 'IN' . $ItemP->id . '-' . "test";

            $ItemInf->save();

            //log
            $userId = $loginBy;
            $type = 'เพิ่มข้อมูล';
            $description = 'ผู้ใช้งาน ' . $userId . ' ได้ทำการ ';
            $this->Log($userId, $description, $type);

            DB::commit();

            return $this->returnSuccess('ดำเนินการสำเร็จ', $ItemInf);
        } catch (\Throwable $e) {
            DB::rollback();

            return $this->returnErrorData('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง ' . $e->getMessage(), 500);
        }
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

            $ItemP = ProductItem::find($Item->product_item_id);
            $ItemP->qty = $ItemP->qty - 1;
            $ItemP->save();

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
                    $Item->post_image = $value['post_image'];
                    $Item->payment_status = $Item['payment_status'];
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
                if ($request->round == 0) {
                    $Item->admin_status1 = $request->admin_status;
                    if ($Item->admin_status == "approve" && $Item->client_status == "approve") {
                        $Item->draft_status = "approve";
                        $Item->admin_feedback1 = $request->feedback;
                    } else {
                        $Item->admin_feedback1 = $request->feedback;
                    }
                } elseif ($request->round == 1) {
                    $Item->admin_status2 = $request->admin_status;
                    if ($Item->client_status == "approve" && $Item->admin_status == "approve") {
                        $Item->draft_status = "approve";
                        $Item->admin_feedback2 = $request->feedback;
                    } else {
                        $Item->admin_feedback2 = $request->feedback;
                    }
                } elseif ($request->round == 2) {
                    $Item->admin_status3 = $request->admin_status;
                    if ($Item->client_status == "approve" && $Item->admin_status == "approve") {
                        $Item->draft_status = "approve";
                        $Item->admin_feedback3 = $request->feedback;
                    } else {
                        $Item->admin_feedback3 = $request->feedback;
                    }
                } elseif ($request->round == 3) {
                    $Item->post_status = $request->admin_status;
                    if ($Item->client_status == "approve" && $Item->admin_status == "approve") {
                        $Item->draft_status = "approve";
                        // $Item->admin_feedback3 = $request->feedback;
                    } else {
                        // $Item->admin_feedback3 = $request->feedback;
                        $Item->round = '4';
                    }
                } else{
                    $Item->payment_status = $request->admin_status;
                }
            }

            if ($request->user_type == 'customer') {
                if ($request->round == 0) {
                    $Item->client_status1 = $request->client_status;
                    if ($Item->client_status == "approve" && $Item->admin_status == "approve") {
                        $Item->draft_status = "approve";
                        $Item->client_feedback1 = $request->feedback;
                    } else {
                        $Item->client_feedback1 = $request->feedback;
                        $Item->admin_status = "waiting";
                        $Item->round = '1';
                    }
                } elseif ($request->round == 1) {
                    $Item->client_status2 = $request->client_status;
                    if ($Item->client_status == "approve" && $Item->admin_status == "approve") {
                        $Item->draft_status = "approve";
                        $Item->client_feedback2 = $request->feedback;
                    } else {
                        $Item->client_feedback2 = $request->feedback;
                        $Item->admin_status = "waiting";
                        $Item->round = '2';
                    }
                } elseif ($request->round == 2) {
                    $Item->client_status3 = $request->client_status;
                    if ($Item->client_status == "approve" && $Item->admin_status == "approve") {
                        $Item->draft_status = "approve";
                        $Item->client_feedback3 = $request->feedback;
                    } else {
                        $Item->client_feedback3 = $request->feedback;
                        $Item->round = '3';
                        $Item->draft_status = "reject";
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
            $defauftdata = [
                'total_view' => 0,
                'total_like' => 0,
                'total_comment' => 0,
                'total_share' => 0,
            ];
            return $this->returnSuccess('ไม่มีข้อมูล', $defauftdata);
        }
    }

    public function kpionlyitem($id)
    {
        try {
            $productItemId = $id;
            if ($productItemId) {
                $productItem = ProductItem::with('project_timelines')
                    ->findOrFail($productItemId);

                $data = $this->calculateProductItemTotals($productItem);

                return  $data;
            } else {
                return $this->returnErrorData('ข้อมูลไม่ถูกต้อง', 400);
            }
        } catch (\Throwable $e) {
            $defauftdata = [
                'total_view' => 0,
                'total_like' => 0,
                'total_comment' => 0,
                'total_share' => 0,
            ];
            return $defauftdata;
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

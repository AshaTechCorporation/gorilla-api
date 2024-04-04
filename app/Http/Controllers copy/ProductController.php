<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Models\CategoryProduct;
use App\Models\Channel;
use App\Models\Floor;
use App\Models\ProductImages;
use App\Models\Products;
use App\Models\Shelf;
use App\Models\Clients;
use App\Models\SubCategoryProduct;
use App\Models\Area;
use App\Models\ProductRaw;
use App\Models\ProductUnit;
use App\Models\StockTransLine;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\Category;
use App\Models\unit;

class ProductController extends Controller
{
    public function getList()
    {
        $Item = Products::get()->toarray();

        if (!empty($Item)) {

            for ($i = 0; $i < count($Item); $i++) {
                $Item[$i]['No'] = $i + 1;
                $Item[$i]['images'] = ProductImages::where('product_id', $Item[$i]['id'])->get();

                for ($n = 0; $n <= count($Item[$i]['images']) - 1; $n++) {
                    $Item[$i]['images'][$n]['image'] = url($Item[$i]['images'][$n]['image']);
                }
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    public function getListType($id)
    {
        $Item = Products::where("category_product_id", $id)->get();

        if (!empty($Item)) {

            for ($i = 0; $i < count($Item); $i++) {
                $Item[$i]['No'] = $i + 1;
                $Item[$i]['images'] = ProductImages::where('product_id', $Item[$i]['id'])->get();

                for ($n = 0; $n <= count($Item[$i]['images']) - 1; $n++) {
                    $Item[$i]['images'][$n]['image'] = url($Item[$i]['images'][$n]['image']);
                }
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

        $type = $request->type;
        $category = $request->category;


        $col = array('id', 'code', 'min', 'type', 'category_product_id', 'sub_category_product_id', 'area_id', 'shelve_id', 'floor_id', 'channel_id', 'name', 'detail', 'code', 'qty', 'create_by', 'update_by', 'created_at', 'updated_at');

        $orderby = array('', 'code', 'min', 'max', 'type', 'category_product_id', 'sub_category_product_id', 'area_id', 'shelve_id', 'floor_id', 'channel_id', 'name', 'detail', 'code', 'qty', 'create_by', 'update_by', 'created_at', 'updated_at');

        $D = Products::select($col);

        if (isset($type)) {
            $D->where('type', $type);
        }

        if (isset($category)) {
            $D->where('category_product_id', $category);
        }


        if ($orderby[$order[0]['column']]) {
            $D->orderby($orderby[$order[0]['column']], $order[0]['dir']);
        }

        if ($search['value'] != '' && $search['value'] != "null") {

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
                $d[$i]->category_product = CategoryProduct::find($d[$i]->category_product_id);
                $d[$i]->sub_category_product = SubCategoryProduct::find($d[$i]->sub_category_product_id);

                $images = ProductImages::where('product_id', $d[$i]->id)->get();
                $d[$i]->images = ProductImages::where('product_id', $d[$i]->id)->orderBy('id', 'desc')->limit(1)->get();

                $d[$i]->area = Area::find($d[$i]->area_id);

                $d[$i]->shelf = Shelf::find($d[$i]->shelve_id);

                $d[$i]->floor = Floor::find($d[$i]->floor_id);
                $d[$i]->channel = Channel::find($d[$i]->channel_id);

                for ($n = 0; $n <= count($d[$i]->images) - 1; $n++) {
                    $d[$i]->images[$n]->image = url($d[$i]->images[$n]->image);
                }

                $d[$i]->product_units = ProductUnit::where('product_id',$d[$i]->id)->get();

                foreach ($d[$i]->product_units as $key => $value) {
                    $d[$i]->product_units[$key]->unit = unit::find($value['unit_id'])['name']; 
                }
            }
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $d);
    }


    public function getByCode($code)
    {
        $Item = Products::where('code', $code)->first();
        if (!empty($Item)) {
            $Item['No'] = 1;
            $Item['image'] = url($Item['image']);
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
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
            return $this->returnErrorData('กรุณาระบุชื่อสินค้าให้ถูกต้อง', 404);
        }

        if (!isset($request->category_product_id)) {
            return $this->returnErrorData('กรุณาระบุประเภทสินค้าให้ถูกต้อง', 404);
        }

        if (!isset($request->sub_category_product_id)) {
            return $this->returnErrorData('กรุณาระบุประเภทสินค้าย่อยให้ถูกต้อง', 404);
        }


        $check1 = CategoryProduct::find($request->category_product_id);
        if (!$check1) {
            return $this->returnErrorData('ไม่พบข้อมูล ประเภทสินค้า ในระบบ', 404);
        }

        $check2 = SubCategoryProduct::find($request->sub_category_product_id);
        if (!$check2) {
            return $this->returnErrorData('ไม่พบข้อมูล ประเภทสินค้าย่อย ในระบบ', 404);
        }

        $check3 = Products::where('code', $request->code)->first();
        if ($check3) {
            return $this->returnErrorData('มี code ในระบบอยู่แล้ว', 404);
        }


        DB::beginTransaction();

        try {

            $prefix = "#" . $check1->prefix . "-" . $check2->prefix . "-";
            $id = IdGenerator::generate(['table' => 'products', 'field' => 'code', 'length' => 13, 'prefix' => $prefix]);

            $Item = new Products();
            $Item->code = $id;
            $Item->category_product_id = $request->category_product_id;
            $Item->sub_category_product_id = $request->sub_category_product_id;
            $Item->area_id = $request->area_id;
            $Item->shelve_id = $request->shelve_id;
            $Item->floor_id = $request->floor_id;
            $Item->channel_id = $request->channel_id;
            $Item->name = $request->name;
            $Item->detail = $request->detail;
            $Item->qty = $request->qty != "null" ? $request->qty : 0;
            $Item->sale_price = $request->sale_price != "null" ? $request->sale_price : 0;
            $Item->cost = $request->cost != "null" ? $request->cost : 0;
            $Item->type = $request->type != "null" ? $request->type : 0;
            $Item->min = $request->min != "null" ? $request->min : 0;
            $Item->max = $request->max != "null" ? $request->max : 0;
            $Item->supplier_id = $request->supplier_id;
            $Item->stock_status = $request->stock_status;

            $Item->save();

            if (isset($request->image)) {
                $Files = new ProductImages();
                $Files->product_id =  $Item->id;
                $Files->image = $request->image;
                $Files->save();
            }

            foreach ($request->products as $key => $value) {

                $ItemF = ProductUnit::where('product_id', $Item->id)
                    ->where('unit_id', $value['unit_id'])
                    ->first();
                if ($ItemF) {
                    $ItemF->qty = $ItemF->qty + $value['qty'];
                    $ItemF->save();
                } else {
                    $ItemF = new ProductUnit();
                    $ItemF->product_id = $Item->id;
                    $ItemF->qty = $value['qty'];
                    $ItemF->unit_id = $value['unit_id'];
                    $ItemF->save();
                }
            }


            // $allowedfileExtension = ['jpg', 'png', 'jpeg', 'gif', 'webp'];
            // $files = $request->file('images');
            // $errors = [];

            // if ($files) {
            //     foreach ($files as $file) {

            //         if ($file->isValid()) {
            //             $extension = $file->getClientOriginalExtension();

            //             $check = in_array($extension, $allowedfileExtension);

            //             if ($check) {
            //                 $Files = new ProductImages();
            //                 $Files->product_id =  $Item->id;
            //                 $Files->image = $this->uploadImage($file, '/images/products/');
            //                 $Files->save();
            //             }
            //         }
            //     }
            // }




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

            return $this->returnErrorData('พบข้อผิดพลาด มีการบันทึกข้อมูลไม่ถูกต้องกรุณาตรวจสอบใหม่' . $e, 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Products  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Item = Products::where('id', $id)
            ->first();

        if ($Item) {
            $Item->area = Area::find($Item->area_id);
            // $Item->area->image = url($Item->area->image);
            $Item->shelf = Shelf::find($Item->shelve_id);
            // if($Item->shelf->image){
            //     $Item->shelf->image = url($Item->shelf->image);
            // }
            $Item->floor = Floor::find($Item->floor_id);
            $Item->channel = Channel::find($Item->channel_id);
            $Item->supplier = Supplier::find($Item->supplier_id);

            $Item->images = ProductImages::where('product_id', $Item->id)->get();

            for ($n = 0; $n <= count($Item->images) - 1; $n++) {
                $Item->images[$n]->image = url($Item->images[$n]->image);
            }

            $Item->category_product = CategoryProduct::find($Item->category_product_id);
            $Item->sub_category_product = SubCategoryProduct::find($Item->sub_category_product_id);
            $Item->raws = ProductRaw::where('product_id', $Item->id)->get();

            for ($n = 0; $n <= count($Item->raws) - 1; $n++) {
                $Item->raws[$n]->product = Products::find($Item->raws[$n]->raw_id);
            }

            $Item->product_units = ProductUnit::where('product_id', $Item->id)->get();
        }

        return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $Item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Products  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Products $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Products  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Products $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Products  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $Item = Products::find($id);
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
        if (!isset($request->id)) {
            return $this->returnErrorData('กรุณาระบเลือกข้อมูลสินค้าให้ถูกต้อง', 404);
        }

        if (!isset($request->name)) {
            return $this->returnErrorData('กรุณาระบุชื่อสินค้าให้ถูกต้อง', 404);
        }

        if (!isset($request->category_product_id)) {
            return $this->returnErrorData('กรุณาระบุประเภทสินค้าให้ถูกต้อง', 404);
        }

        if (!isset($request->sub_category_product_id)) {
            return $this->returnErrorData('กรุณาระบุประเภทสินค้าย่อยให้ถูกต้อง', 404);
        }


        $check1 = CategoryProduct::find($request->category_product_id);
        if (!$check1) {
            return $this->returnErrorData('ไม่พบข้อมูล ประเภทสินค้า ในระบบ', 404);
        }

        $check2 = SubCategoryProduct::find($request->sub_category_product_id);
        if (!$check2) {
            return $this->returnErrorData('ไม่พบข้อมูล ประเภทสินค้าย่อย ในระบบ', 404);
        }

        $check3 = Products::where('code', $request->code)->first();
        if ($check3) {
            return $this->returnErrorData('มี code ในระบบอยู่แล้ว', 404);
        }

        DB::beginTransaction();

        try {
            $Item = Products::find($request->id);

            if (!$Item) {
                return $this->returnErrorData('ไม่พบรายการนี้ในระบบ', 404);
            }

            $Item->category_product_id = $request->category_product_id;
            $Item->sub_category_product_id = $request->sub_category_product_id;
            $Item->area_id = $request->area_id;
            $Item->shelve_id = $request->shelve_id;
            $Item->floor_id = $request->floor_id;
            $Item->channel_id = $request->channel_id;
            $Item->name = $request->name;
            $Item->detail = $request->detail;
            $Item->qty = $request->qty != "null" ? $request->qty : 0;
            $Item->sale_price = $request->sale_price != "null" ? $request->sale_price : 0;
            $Item->cost = $request->cost != "null" ? $request->cost : 0;
            $Item->type = $request->type != "null" ? $request->type : 0;
            $Item->min = $request->min != "null" ? $request->min : 0;
            $Item->max = $request->max != "null" ? $request->max : 0;
            $Item->supplier_id = $request->supplier_id;
            $Item->stock_status = $request->stock_status;
            $Item->save();

            if (isset($request->image)) {
                ProductImages::where('product_id', $Item->id)->delete();
                $Files = new ProductImages();
                $Files->product_id =  $Item->id;
                $Files->image = $request->image;
                $Files->save();
            }


            foreach ($request->products as $key => $value) {

                $ItemF = ProductUnit::where('product_id', $Item->id)
                    ->where('unit_id', $value['unit_id'])
                    ->first();
                if ($ItemF) {
                    $ItemF->qty = $value['qty'];
                } else {
                    $ItemF = new ProductUnit();
                    $ItemF->product_id = $Item->id;
                    $ItemF->qty = $value['qty'];
                    $ItemF->unit_id = $value['unit_id'];
                }
                $ItemF->save();
            }

            // $allowedfileExtension = ['jpg', 'png', 'jpeg', 'gif', 'webp'];
            // $files = $request->file('images');
            // $errors = [];
            // if ($files) {
            //     foreach ($files as $file) {

            //         if ($file->isValid()) {
            //             $extension = $file->getClientOriginalExtension();

            //             $check = in_array($extension, $allowedfileExtension);

            //             if ($check) {
            //                 $Files = new ProductImages();
            //                 $Files->product_id =  $Item->id;
            //                 $Files->image = $this->uploadImage($file, '/images/products/');
            //                 $Files->save();
            //             }
            //         }
            //     }
            // }

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

            return $this->returnErrorData('พบข้อผิดพลาด มีการบันทึกข้อมูลไม่ถูกต้องกรุณาตรวจสอบใหม่', 404);
        }
    }

    public function deleteProductImage($id)
    {

        DB::beginTransaction();

        try {

            $Item = ProductImages::find($id);
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

    public function Export(Request $request)
    {
        $category = $request->category_id;
        $col = array('id', 'code', 'min', 'type', 'category_product_id', 'sub_category_product_id', 'area_id', 'shelve_id', 'floor_id', 'channel_id', 'name', 'detail', 'code', 'qty', 'create_by', 'update_by', 'created_at', 'updated_at');

        $D = Products::select($col);

        if (isset($category)) {
            $D->where('category_product_id', $category);
        }

        $data = $D->get()->toArray();
        $cat = CategoryProduct::find($category) ? CategoryProduct::find($category)['name'] : "-";
        if (!empty($data)) {

            for ($i = 0; $i < count($data); $i++) {
                $export_data[] = array(
                    'id' => trim($data[$i]['code']),
                    'name' => trim($data[$i]['name']),
                    'category_name' => CategoryProduct::find($data[$i]['category_product_id']) ? CategoryProduct::find($data[$i]['category_product_id'])['name'] : "-",
                    'sub_category_name' => SubCategoryProduct::find($data[$i]['sub_category_product_id']) ? SubCategoryProduct::find($data[$i]['sub_category_product_id'])['name'] : "-",
                    'area' => Area::find($data[$i]['area_id']) ? Area::find($data[$i]['area_id'])['name'] : "-",
                    'shelve' => Shelf::find($data[$i]['shelve_id']) ? Shelf::find($data[$i]['shelve_id'])['name'] : "-",
                    'floor' => Floor::find($data[$i]['floor_id']) ? Floor::find($data[$i]['floor_id'])['name'] : "-",
                    'channel' => Channel::find($data[$i]['channel_id']) ? Channel::find($data[$i]['channel_id'])['name'] : "-",
                    'qty' => trim($data[$i]['qty']),
                    'create_by' => trim($data[$i]['created_at']),
                    'created_at' => trim($data[$i]['updated_at']),
                );
            }

            $result = new ProductExport($export_data);



            return Excel::download($result, 'รายการสินค้า ' . $cat . '.xlsx');
        } else {

            $export_data[] = array(
                'id' => null,
                'name' => null,
                'category_name' => null,
                'sub_category_name' => null,
                'area' => null,
                'shelve' => null,
                'floor' => null,
                'channel' => null,
                'qty' => null,
                'create_by' => null,
                'created_at' => null,
            );

            $result = new ProductExport($export_data);
            return Excel::download($result, 'รายการสินค้า ' . $cat . '.xlsx');
        }
    }

    public function adjustStock(Request $request)
    {
        $Item = Products::get()->toArray();

        foreach ($Item  as $key => $value) {
            //    $ItemU = ProductUnit::where('product_id',$value['id'])->get();
            $ItemU = new ProductUnit();
            $ItemU->unit_id = 1;
            $ItemU->product_id = $value['id'];
            $ItemU->qty = $value['qty'];
            $ItemU->save();
        }
        $Item->product_units = $ItemU;
        return $this->returnSuccess('ดำเนินการสำเร็จ', $Item);
    }
}

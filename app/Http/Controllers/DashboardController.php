<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Influencer;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Project;

class DashboardController extends Controller
{
    public function getData()
    {
        try {
            $numInfu = Influencer::count();
            $numCustomer = Customer::count();
            $numEmployee = Employee::count();
            $ProjectALL = Project::count();
            $ProjectOngoing = Project::where("status","ongoing")->count();
            $ProjectClose = Project::where("status","closed")->count();

            $data = [
                'Influencer' => $numInfu,
                'Customer' => $numCustomer,
                'Employee' => $numEmployee,
                'ProjectALL'=>$ProjectALL,
                'ProjectOngoing'=>$ProjectOngoing,
                'ProjectClose'=>$ProjectClose
            ];

            return $this->returnSuccess('เรียกดูข้อมูลสำเร็จ', $data);
        } catch (\Throwable $th) {
            return $this->returnErrorData('เกิดข้อผิดพลาด', 404);
        }
    }
}
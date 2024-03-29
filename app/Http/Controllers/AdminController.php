<?php

namespace App\Http\Controllers;

use DB;
use App\Models\BusinessPlan;
use App\Models\BusinessUser;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function viewBusinessPlan(){
        if (!userTypeAccess(['admin'])) {
            return redirect()->route('logout.login');
        }
        $businessPlan = BusinessPlan::select('unique_id', 'name', 'profile')->where('status', 1)->get();
        
        $output = $businessPlan;

        return view('login.admin.viewBusinessPlan')->with('output', json_encode($output));
    }
    public function createBusinessPlan(){
        if (!userTypeAccess(['admin'])) {
            return redirect()->route('logout.login');
        }
        return view('login.admin.createBusinessPlan');
    }
    
    public function addAdminUser(){
        if (!userTypeAccess(['admin'])) {
            return redirect()->route('logout.login');
        }
        return view('login.admin.addAdminUser');
    }

    public function viewBusinessPlanDetails($unique_id){
        if (!userTypeAccess(['admin'])) {
            return redirect()->route('logout.login');
        }
        $businessPlan = BusinessPlan::select('unique_id', 'name', 'profile', 'id')
                        -> where('unique_id', $unique_id)
                        ->where('status', 1)
                        ->first();
        
        $businessPlanUser = DB::select('SELECT u.unique_id, u.name, u.display_id, u.type
                            FROM business_user bu JOIN user u ON bu.user_id = u.id
                            WHERE bu.business_plan_id = :businessPlanId
                                and bu.status = 1 and u.status = 1
                            ORDER BY u.type',
                            ['businessPlanId' => $businessPlan['id']]);

        return view('login.admin.viewBusinessPlanDetails')->with('businessPlan', $businessPlan)->with('businessPlanUser', $businessPlanUser);
    }

    public function ajaxSearchBusinessPlan(Request $request){
        if (!userTypeAccess(['admin'])) {
            return redirect()->route('logout.login');
        }
        $input = $request->only('name');
        
        $output = BusinessPlan::select('unique_id', 'name', 'profile')
                        -> where('name', 'LIKE', "%{$input['name']}%")
                        ->where('status', 1)
                        ->get();

        return response()->json(compact('output'));
    }

    public function ajaxCreateBusinessPlan(Request $request){
        if (!userTypeAccess(['admin'])) {
            return redirect()->route('logout.login');
        }
        $input = $request->only('companyName', 'companyProfile', 'name', 'displayId', 'email', 'password', 'passwordConfirmation', 'phone', 'DOB');

        $checkCompanyName = BusinessPlan::where('name', $input['companyName'])->count();
        $checkUserEmail = User::where('email', $input['email'])->count();
        $checkUserDisplayId = User::where('display_id', $input['displayId'])->count();

        if($checkCompanyName){
            // duplicated
            $output['result'] = 'false';
            $output['message'] = 'The company name has been used';
        }
        elseif($checkUserEmail){
            // duplicated
            $output['result'] = 'false';
            $output['message'] = 'The email has been used';
        }elseif ($checkUserDisplayId) {
            // duplicated
            $output['result'] = 'false';
            $output['message'] = 'The Display ID has been used';
        }else{
            // new business plan
            $uniqid = \getUniqid();
            $businessPlan = new BusinessPlan;
            $businessPlan->unique_id = $uniqid;
            $businessPlan->name = $input['companyName'];
            $businessPlan->profile = $input['companyProfile'];
            $businessPlan->save();

            // new user & set to businessp plan admin
            $uniqid = \getUniqid();
            $user = new User;
            $user->unique_id = $uniqid;
            $user->name = $input['name'];
            $user->display_id = $input['displayId'];  
            $user->email = $input['email'];
            $user->password = password_hash($input['password'], PASSWORD_DEFAULT, ['cost'=> 11]);
            $user->type = 'business admin';
            $user->save();

            // new business user
            $businessUser = new BusinessUser;
            $businessUser->business_plan_id = $businessPlan->id;
            $businessUser->user_id = $user->id;
            $businessUser->save();

            $output['result'] = 'true';
            $output['redirect'] = route('login.admin.viewBusinessPlanDetails');
            
            
        }
        return response()->json(compact('output'));

    }

    public function ajaxAddAdminUser(Request $request){
        if (!userTypeAccess(['admin'])) {
            return redirect()->route('logout.login');
        }
        $input = $request->only('name', 'displayId', 'email', 'password');

        $checkUserEmail = User::where('email', $input['email'])->count();
        $checkUserDisplayId = User::where('display_id', $input['displayId'])->count();

        if($checkUserEmail){
            // duplicated
            $output['result'] = 'false';
            $output['message'] = 'Your email has been used';
        }elseif ($checkUserDisplayId) {
            // duplicated
            $output['result'] = 'false';
            $output['message'] = 'Your Display ID has been used';
        }else{
            $uniqid = \getUniqid();
            $user = new User;
            $user->unique_id = $uniqid;
            $user->name = $input['name'];
            $user->display_id = $input['displayId'];  
            $user->email = $input['email'];
            $user->password = password_hash($input['password'], PASSWORD_DEFAULT, ['cost'=> 11]);
            $user->type = 'admin';
            
            $user->save();

            $businessUser = new BusinessUser;
            $businessUser->business_plan_id = \getMyBusinessPlanId();
            $businessUser->user_id = $user->id;

            $businessUser->save();

            $businessPlan = BusinessPlan::select('unique_id')
                        -> where('id', \getMyBusinessPlanId())
                        ->where('status', 1)
                        ->first();

            $output['result'] = 'true';
            $output['redirect'] = route('login.admin.viewBusinessPlanDetails', ['unique_id' => $businessPlan->unique_id]);
            
        }
        return response()->json(compact('output'));

    }
}

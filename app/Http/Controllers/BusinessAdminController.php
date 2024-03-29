<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use App\Models\BusinessUser;
use Illuminate\Http\Request;

class BusinessAdminController extends Controller
{
    public function viewUser(){
        if (!userTypeAccess(['business admin'])) {
            return redirect()->route('logout.login');
        }
        $output = app()->call('App\Http\Controllers\UserController@getUserList', ['colleague', '', '']); 

        return view('login.businessAdmin.viewUser')->with('output', json_encode($output));
    }
    public function addUser(){
        if (!userTypeAccess(['business admin'])) {
            return redirect()->route('logout.login');
        }

        return view('login.businessAdmin.addUser');
    }
    public function removeBusinessPlanUser($unique_id){
        if (!userTypeAccess(['business admin'])) {
            return redirect()->route('logout.login');
        }
        $user = User::select('id')->where('unique_id', $unique_id)->first();
        $user->status = 4;
        $user->save();

        $businessUser = BusinessUser::where('user_id', $user['id'])->first();
        $businessUser->status = 4;
        $businessUser->save();

        return back();
    }
    public function ajaxAddBusinessUser(Request $request){
        if (!userTypeAccess(['business admin'])) {
            return redirect()->route('logout.login');
        }
        $input = $request->only('type', 'name', 'displayId', 'email', 'password');

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
            $user->type = $input['type'];
            
            $user->save();

            $businessUser = new BusinessUser;
            $businessUser->business_plan_id = session('user.info.businessPlanId');
            $businessUser->user_id = $user->id;

            $businessUser->save();

            $output['result'] = 'true';
            $output['redirect'] = route('login.businessAdmin.viewUser');
            
        }
        return response()->json(compact('output'));

    }
}

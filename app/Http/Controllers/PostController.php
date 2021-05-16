<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Models\member;
use App\Models\shift;
use App\Models\shiftlist;

class PostController extends Controller
{

    function apiLogin(Request $request) {
        $today = date("Y-m-d H:i:s");
        $username = $request->username;
        $password = $request->hide;
        $users = member::where('username', $username)->where('password',$password)->first();
        if (isset($users)) {
            $id = $users->id;
            $user = $users->username;
            $token =  Str::random(16);
            member::where('username', $username)->update(['api_token' => $token,'updated_at' => $today]);
            $data = [
                'username' => $user,
                'api_token' => $token,
            ];
            return response()->json($data, 200);
        }else{
            return response()->json(false, 400);
        }
    }

    function apiGoogleLogin (Request $request) {
        $email = $request->username;
        $user =  member::where('username', $email .'@google')->first();
        $date = date('Y-m-d H:i:s');
        if ($user === null) {
            $token =  Str::random(16);
            member::insert(['username' => $email.'@google', 'password' => uniqid(), 'api_token' => $token ,'created_at' => $date, 'updated_at' => $date]);
            $user =  member::where('username', $email .'@google')->first();
            $username = mb_split("@",$user->username);
        }else{
            $token =  Str::random(16);
            $username = mb_split("@",$user->username);
            member::where('username', $email .'@google')->update(['api_token' => $token ,'updated_at' => $date]);
        }
        $data = [
            'username' => $username[0],
            'api_token' => $token,
        ];
        return response()->json($data, 200);
    }

    function apiCheckuser (Request $request) {
        $users = member::select('username')->where('username', $request->username)->get();
        $user = isset($users) ? $users : null;
        return $user;
    }

    function apiSignup(Request $request) {
        $token =  Str::random(16);
        $request->api_token = $token;
        $post = new member;
        $post->username = $request->input('username', '帳號');
        $post->password = $request->input('hide', '密碼');
        $post->api_token = $request->api_token;
        $ok = $post->save();
        $users = member::where('username', $post->username)->where('password',$post->password)->first();
        if ($users) {
            $user = $users->username;
            $data = [
                'username' => $user,
                'api_token' => $token,
            ];
        }
        return response()->json($data, 200);
    }

    function apiMember() {
        return response()->json(member::all(), 200);
    }

    function apiUploadEvent (Request $request) {
        $year = $request->year;
        $month = $request->month;
        $events = $request->events;
        $today = date("Y-m-d H:i:s");
        $member = member::select('id')->where('api_token', $request->api_token)->first();
        $data = shift::select('data')->where('member_id' , $member->id)->where('year' , $year)->where('month' , $month)->first();
        if (isset($data)) {
            shift::where('member_id' , $member->id)->where('year' , $year)->where('month' , $month)->update(['data' => $events]);
        }else{
            shift::insert(['member_id' => $member->id, 'year' => $year,'month' => $month, 'data' => $events,'created_at' => $today, 'updated_at' => $today]);
        }
        return true;
    }

    function apiGetEvent (Request $request) {
        $year = $request->year;
        $month = $request->month;
        $member = member::select('id')->where('api_token', $request->api_token)->first();
        $data = shift::select('data')->where('member_id' , $member->id)->where('year' , $year)->where('month' , $month)->first();
        if (isset($data)) {
            return $data;
        }else{
            return false;
        }
    }

    function apiDeleteEvent (Request $request) {
        $year = $request->year;
        $month = $request->month;
        $member = member::select('id')->where('api_token', $request->api_token)->first();
        $data = shift::select('data')->where('member_id' , $member->id)->where('year' , $year)->where('month' , $month)->delete();
        return 'ok';
    }

    function apiUploadShiftlist (Request $request) {
        $today = date("Y-m-d H:i:s");
        $shiftlist = json_decode($request->data);
        $num = count($shiftlist)-1;
        $member = member::select('id')->where('api_token', $request->api_token)->first();
        for ($i = 0 ; $i <= $num ; $i++) {
            $name = $shiftlist[$i]->name;
            $starttime = $shiftlist[$i]->starttime;
            $endtime = $shiftlist[$i]->endtime;
            $data = shiftlist::where('member_id' , $member->id)->where('shift' , $name)->first();
            if (isset($data)) {
                shiftlist::where('member_id' , $member->id)->where('shift' , $name)->update(['starttime' => $starttime,'endtime' => $endtime,'updated_at' => $today]);
            }else{
                shiftlist::insert(['member_id' => $member->id, 'shift' => $name,'starttime' => $starttime, 'endtime' => $endtime,'created_at' => $today, 'updated_at' => $today]);
            }
        }
        $last = shiftlist::select('id','shift AS name','starttime','endtime')->where('member_id' , $member->id)->latest('id')->first();    
        return response()->json($last,200);
    }

    function apiGetShiftlist (Request $request) {
        $member = member::select('id')->where('api_token', $request[0])->first();
        $data = shiftlist::select('id','shift AS name','starttime','endtime')->where('member_id' , $member->id)->get();
        return response()->json($data, 200);
    }
    
    function apiDeletesShiftlist (Request $request) {
        $id = $request->data;
        $member = member::select('id')->where('api_token', $request->api_token)->first();
        shiftlist::where('member_id' , $member->id)->where('id' , $id)->delete();
        return 'ok';
    }

    function apiUpdateShiftlist (Request $request) {
        $id = $request->id;
        $shift = $request->name;
        $starttime = $request->starttime;
        $endtime = $request->endtime;
        $today = date("Y-m-d H:i:s");
        $member = member::select('id')->where('api_token', $request->api_token)->first();
        shiftlist::where('member_id' , $member->id)->where('id' , $id)->update(['shift' => $shift,'starttime' => $starttime,'endtime' => $endtime,'updated_at' => $today]);
        return 'ok';
    }

}

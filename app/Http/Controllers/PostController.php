<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            if(!isset($_SESSION)){
                session_start(); 
            }
            $_SESSION["MemberID"] = $id; //Table 'member'裡的ID欄位值
            $_SESSION["MemberUser"] = $user; //Table 'member'裡的USERNAME欄位值
            member::where('username', $username)->update(['updated_at' => $today]);
            return response()->json($users, 200);    
        }else{
            return false;
        }
    }

    function apiGoogleLogin (Request $request) {
        $email = $request->username;
        $user =  member::where('username', $email .'@google')->first();
        $date = date('Y-m-d H:i:s');
        if ($user === null) {
            member::insert(['username' => $email.'@google', 'password' => uniqid(),'created_at' => $date, 'updated_at' => $date]);
            $user =  member::where('username', $email .'@google')->first();
            if(!isset($_SESSION)){
                session_start(); 
            }
            $_SESSION["MemberID"] = $user->id; //Table 'member'裡的ID欄位值
            $username = mb_split("@",$user->username);
            $_SESSION["MemberUser"] = $username[0];
        }else{
            if(!isset($_SESSION)){
                session_start(); 
            }
            $_SESSION["MemberID"] = $user->id; //Table 'member'裡的ID欄位值
            $username = mb_split("@",$user->username);
            $_SESSION["MemberUser"] = $username[0];
            member::where('username', $email .'@google')->update(['updated_at' => $date]);
        }
        return true;
    }

    function apiCheckuser (Request $request) {
        $users = member::select('username')->where('username', $request->username)->get();
        $user = isset($users) ? $users : null;
        return $user;
    }

    function apiSignup(Request $request) {
        $post = new member;
        $post->username = $request->input('username', '帳號');
        $post->password = $request->input('hide', '密碼');
        $ok = $post->save();
        $users = member::where('username', $post->username)->where('password',$post->password)->get();
        $id = $users[0]->id;
        $user = $users[0]->username;
        if ( count($users) === 1 ) {
            if(!isset($_SESSION)){
                session_start(); 
            }
            $_SESSION["MemberID"] = $id; //Table 'member'裡的ID欄位值
            $_SESSION["MemberUser"] = $user; //Table 'member'裡的USERNAME欄位值
        };
        return response()->json($users, 200);
    }

    function apiMember() {
        return response()->json(member::all(), 200);
    }

    function apiSession () {
        if(!isset($_SESSION)){
            session_start(); 
        }
        $obj = [
            'id' => isset($_SESSION["MemberID"]) ? $_SESSION["MemberID"] : null,
            'user'  => isset($_SESSION["MemberUser"]) ? $_SESSION["MemberUser"] : null,
        ];
        return $obj;
    }

    function apiClearSession() {
        if(!isset($_SESSION)){
            session_start(); 
        }
        session_unset();
        session_destroy();
    }

    function apiUploadEvent (Request $request) {
        $year = $request->year;
        $month = $request->month;
        $events = $request->events;
        $today = date("Y-m-d H:i:s");
        if(!isset($_SESSION)){
            session_start(); 
        }
        $data = shift::select('data')->where('member_id' , $_SESSION["MemberID"])->where('year' , $year)->where('month' , $month)->first();
        if (isset($data)) {
            shift::where('member_id' , $_SESSION["MemberID"])->where('year' , $year)->where('month' , $month)->update(['data' => $events]);
        }else{
            shift::insert(['member_id' => $_SESSION["MemberID"], 'year' => $year,'month' => $month, 'data' => $events,'created_at' => $today, 'updated_at' => $today]);
        }
        return true;
    }

    function apiGetEvent (Request $request) {
        $year = $request->year;
        $month = $request->month;
        if(!isset($_SESSION)){
            session_start(); 
        }
        $data = shift::select('data')->where('member_id' , $_SESSION["MemberID"])->where('year' , $year)->where('month' , $month)->first();
        if (isset($data)) {
            return $data;
        }else{
            return false;
        }
    }

    function apiDeleteEvent (Request $request) {
        $year = $request->year;
        $month = $request->month;
        if(!isset($_SESSION)){
            session_start(); 
        }
        $data = shift::select('data')->where('member_id' , $_SESSION["MemberID"])->where('year' , $year)->where('month' , $month)->delete();
        return 'ok';
    }

    function apiUploadShiftlist (Request $request) {
        $today = date("Y-m-d H:i:s");
        $shiftlist = json_decode($request[0]);
        $num = count($shiftlist)-1;
        if(!isset($_SESSION)){
            session_start(); 
        }
        for ($i = 0 ; $i <= $num ; $i++) {
            $name = $shiftlist[$i]->name;
            $starttime = $shiftlist[$i]->starttime;
            $endtime = $shiftlist[$i]->endtime;
            $data = shiftlist::where('member_id' , $_SESSION["MemberID"])->where('shift' , $name)->first();
            if (isset($data)) {
                shiftlist::where('member_id' , $_SESSION["MemberID"])->where('shift' , $name)->update(['starttime' => $starttime,'endtime' => $endtime,'updated_at' => $today]);
            }else{
                shiftlist::insert(['member_id' => $_SESSION["MemberID"], 'shift' => $name,'starttime' => $starttime, 'endtime' => $endtime,'created_at' => $today, 'updated_at' => $today]);
            }
        }
        $last = shiftlist::select('id','shift AS name','starttime','endtime')->where('member_id' , $_SESSION["MemberID"])->latest('id')->first();    
        return response()->json($last,200);
    }

    function apiGetShiftlist () {
        if(!isset($_SESSION)){
            session_start(); 
        }
        $data = shiftlist::select('id','shift AS name','starttime','endtime')->where('member_id' , $_SESSION["MemberID"])->get();
        return response()->json($data, 200);
    }
    
    function apiDeletesShiftlist (Request $request) {
        $id = $request->id;
        if(!isset($_SESSION)){
            session_start(); 
        }
        shiftlist::where('member_id' , $_SESSION["MemberID"])->where('id' , $id)->delete();
        return 'ok';
    }

    function apiUpdateShiftlist (Request $request) {
        $id = $request->id;
        $shift = $request->name;
        $starttime = $request->starttime;
        $endtime = $request->endtime;
        $today = date("Y-m-d H:i:s");
        if(!isset($_SESSION)){
            session_start(); 
        }
        shiftlist::where('member_id' , $_SESSION["MemberID"])->where('id' , $id)->update(['shift' => $shift,'starttime' => $starttime,'endtime' => $endtime,'updated_at' => $today]);
        return 'ok';
    }

}

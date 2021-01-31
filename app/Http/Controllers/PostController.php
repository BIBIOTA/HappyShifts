<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\member;

class PostController extends Controller
{

    function apiLogin(Request $request) {
        $today = date("Y-m-d H:i:s");
        $username = $request->username;
        $password = $request->hide;
        $users = member::where('username', $username)->where('password',$password)->get();
        $id = $users[0]->id;
        $user = $users[0]->username;
        if ( count($users) === 1 ) {
            if(!isset($_SESSION)){
                session_start(); 
            }
            $_SESSION["MemberID"] = $id; //Table 'member'裡的ID欄位值
            $_SESSION["MemberUser"] = $user; //Table 'member'裡的USERNAME欄位值
            member::where('username', $username)->update(['updated_at' => $today]);
        };
        return response()->json($users, 200);
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

}

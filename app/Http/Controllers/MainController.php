<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Events\ClassCallMessageEvent;

use App;
use File;
use JavaScript;
use LaravelLocalization;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index($u = null, Request $request)
    {
        $domain = explode(".", $request->getHost())[0];
        $user = !is_null($request->user()) ? User::withAll()->find($request->user()->id) : null;
        if(!is_null($user)){
            $user->courses = $user->type == 1 || $user->type == 3 ? $user->studentCourses : $user->teacherCourses;
            $user->subjects = $user->type == 1 || $user->type == 3 ? $user->studentSubjects : $user->teacherSubjects;
        }
        
        //Reset password
        $token = !is_null($request->token) ? $request->token : null;
        $oldEmail = !is_null($request->email) ? $request->email : null;

        //Lang
        $lang_files = File::files(resource_path() . '/lang/' . App::getLocale());
        $trans = array();
        foreach ($lang_files as $f) {
            $filename = pathinfo($f)['filename'];
            $trans[$filename] = trans($filename);
        }

        //Return all
        JavaScript::put(["user" => $user, "domain" => $domain, "trans" => $trans]);
        return view("layouts.master")->with("resetToken", $token)->with("oldEmail", $oldEmail);
    }

    public function test(Request $request)
    {
        ClassCallMessageEvent::dispatch(1, "off", null);
    }
}

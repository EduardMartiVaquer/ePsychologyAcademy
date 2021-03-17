<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Invitation;
use App\Models\CourseStudent;
use App\Models\CourseTeacher;
use App\Models\SubjectStudent;
use App\Models\SubjectTeacher;

use Carbon\Carbon;
use LaravelLocalization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ProfileController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', strtolower($request->get('email')))->first();
        
        if(!is_null($user) && Hash::check($request->get('password'), $user->password)){
            Auth::login($user, true);
            return response()->json(['status' => 'OK']);
        } else {
            return response()->json(['status' => 'FAIL']);
        }
    }

    public function register(Request $request)
    {
        if(is_null(User::where('email', strtolower($request->get('email')))->first())){
            //Validate email
            if(filter_var(strtolower($request->get('email')), FILTER_VALIDATE_EMAIL)){

                //Code & type
                $type = 1;
                if(!is_null($request->get('code'))){
                    $invitation = Invitation::where('code', $request->get('code'))->first();
                    if(!is_null($invitation)){
                        if($invitation->used == 1){
                            return response()->json(["status" => "CODE_USED"]);
                        }
                        $type = $invitation->type;
                    } else {
                        return response()->json(["status" => "CODE_ERROR"]);
                    }
                }
                
                //Setup name
                $name_arr = array();
                $name_strings = explode(' ', $request->get('name'));
                foreach ($name_strings as $name_string) {
                    if(str_replace(" ", "", $name_string) != ""){
                        array_push($name_arr, ucfirst(strtolower($name_string)));
                    }
                }
                $name = $name_arr[0];
                array_shift($name_arr);
                $last_name = count($name_arr) > 1 ? implode(" ", $name_arr) : null;

                //Create new user
                $user = new User();
                $user->status = 1;
                $user->type = $type;
                $user->name = $name;
                $user->last_name = $last_name;
                $user->email = strtolower($request->get('email'));
                $user->password = Hash::make($request->get('password'));
                $user->timezone = $request->get('timezone');
                $user->lang = LaravelLocalization::getCurrentLocale();
                $user->email_verified_at = Carbon::now()->timestamp;
                $user->save();

                if(!is_null($request->get('code'))){
                    if(!is_null($invitation)){
                        $subjects = explode(";", $invitation->subjects);
                        if($invitation->type == 1){
                            $course_student = CourseStudent::create([
                                'user' => $user->id,
                                'course' => $invitation->course
                            ]);
                            foreach ($subjects as $subject) {
                                $subject_student = SubjectStudent::create([
                                    'user' => $user->id,
                                    'subject' => (int) $subject
                                ]);
                            }
                        } elseif($invitation->type == 2){
                            $course_teacher = CourseTeacher::create([
                                'user' => $user->id,
                                'course' => $invitation->course
                            ]);
                            foreach ($subjects as $subject) {
                                $subject_student = SubjectTeacher::create([
                                    'user' => $user->id,
                                    'subject' => (int) $subject
                                ]);
                            }
                        }

                        $invitation->used = 1;
                        $invitation->save();
                    }
                }                    

                return response()->json(["status" => "OK"]);
            } else {
                return response()->json(['status' => 'INVALID_EMAIL']);
            }
        } else {
            return response()->json(['status' => 'USER_ALREADY_EXISTS']);
        }
    }

    public function sendResetPasswordLink(Request $request)
    {
        if(!is_null(User::where('email', strtolower($request->get('email')))->first())){
            $status = Password::sendResetLink(
                $request->only('email')
            );

            return response()->json(["status" => $status]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->to('/');
    }

    public function getNotifications(Request $request)
    { 
        return response()->json($request->user()->notifications);
    }

    public function markAllNotifications(Request $request)
    {
        if($request->get('mark')){
            $request->user()->unreadNotifications->markAsRead();
        } else {
            $request->user()->notifications->markAsUnread();
        }

        return response()->json(["status" => "OK"]);
    }

    public function updateUserInfo(Request $request)
    {
        $user = $request->user();

        if($user->email != strtolower($request->get('email'))){
            $check = User::where('email', strtolower($request->get('email')))->first();
            if(is_null($check)){
                $user->email = strtolower($request->get('email'));
            } else {
                return response()->json(["status" => "USER_ALREADY_EXISTS"]);
            }
        }
        $user->name = $request->get('name');
        $user->last_name = $request->get('last_name');
        $user->phone_prefix = $request->get('phone_prefix');
        $user->phone = $request->get('phone');
        $user->save();

        return response()->json(["status" => "OK", "user" => $user]);
    }

    public function checkEmailAddress(Request $request)
    {
        $user = User::where('email', strtolower($request->get('email')))->first();
        if(!is_null($user)){
            return response()->json(["status" => "OK"]);
        }
        return response()->json(["status" => "FAIL"]);
    }

    public function getInvitationCode(Request $request)
    {
        $invitation = Invitation::where('code', $request->get('code'))->first();
        if(!is_null($invitation)){
            return response()->json(["status" => "OK", "email" => $invitation->email]);
        }
        return response()->json(["status" => "OK", "email" => null]);
    }
}

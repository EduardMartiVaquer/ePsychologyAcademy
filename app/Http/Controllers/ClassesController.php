<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ClassFile;
use App\Models\ClassUser;
use App\Models\ClassEvent;
use App\Models\CourseTeacher;
use App\Models\CourseStudent;
use App\Models\SubjectTeacher;
use App\Models\SubjectStudent;

use App\Events\ClassCallMessageEvent;

use OpenTok\Role;
use OpenTok\Layout;
use OpenTok\Session;
use OpenTok\OpenTok;
use OpenTok\MediaMode;
use OpenTok\ArchiveMode;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClassesController extends Controller
{
    public function getClassEvent(Request $request)
    {
        if(is_null(ClassUser::where('user', $request->user()->id)->where('class', $request->get('class')))){
            return response()->json(["status" => "FAIL"]);
        }
        $class = ClassEvent::withAll()->find($request->get('classEvent'));

        return response()->json(["status" => "OK", "class" => $class]);
    }

    public function saveNewClass(Request $request)
    {
        $class_event = ClassEvent::create([
            'course' => $request->get('course'),
            'subject' => $request->get('subject'),
            'from' => Carbon::createFromFormat('Y-m-d H:i:s', $request->get('from'), $request->get('timezone'))->setTimezone('utc')->toDateTimeString(),
            'to' => Carbon::createFromFormat('Y-m-d H:i:s', $request->get('to'), $request->get('timezone'))->setTimezone('utc')->toDateTimeString(),
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'opentok_session' => null,
            'opentok_token' => null
        ]);

        if(is_null($request->get('subject'))){
            $teachers = CourseTeacher::where('course', $request->get('course'))->get();
            $students = CourseStudent::where('course', $request->get('course'))->get();
        } else {
            $teachers = SubjectTeacher::where('course', $request->get('course'))->get();
            $students = SubjectStudent::where('course', $request->get('course'))->get();
        }

        $add_class = false;
        foreach ($teachers as $teacher) {
            $class_user = ClassUser::create([
                'course' => $request->get('course'),
                'subject' => $request->get('subject'),
                'user' => $teacher->user,
                'type' => 2,
                'class' => $class_event->id
            ]);
            $add_class = $teacher->user == $request->user()->id;
        }
        foreach ($students as $student) {
            $class_user = ClassUser::create([
                'course' => $request->get('course'),
                'subject' => $request->get('subject'),
                'user' => $student->user,
                'type' => 1,
                'class' => $class_event->id
            ]);
            $add_class = $student->user == $request->user()->id;
        }

        $class_event = ClassEvent::withAll()->find($class_event->id);
        return response()->json(["status" => "OK", "class_event" => $class_event, "add_class" => $add_class]);
    }

    public function uploadClassFile(Request $request)
    {
        $short_path = Storage::disk('s3')->putFileAs('classes/'.$request->get('class'), $request->file('file'), $request->get('name'), 'public');
        $path = Storage::disk('s3')->url($short_path);
        $class_file = ClassFile::create([
            'class' => $request->get('class'),
            'name' => $request->get('name'),
            'path' => $path,
            'short_path' => $short_path,
            'type' => $request->get('type'),
            'extension' => $request->get('extension')
        ]);
        return response()->json(['file' => $class_file]);
    }

    public function sendPeerOffer(Request $request)
    {
        $offer = json_decode($request->get('offer'), true);
        broadcast(new ClassCallMessageEvent($request->get('class'), $offer, null, null))->toOthers();

        return response()->json(["status" => "OK"]);
    }

    public function getPeerOffer(Request $request)
    {
        $peer_connection = ClassPeerConnection::where('class', $request->get('class'))->first();
        return response()->json(["status" => "OK", "peer_connection" => $peer_connection]);
    }

    public function savePeerOffer(Request $request)
    {
        $peer_connection = ClassPeerConnection::where('class', $request->get('class'))->first();
        if(!is_null($peer_connection)){
            $peer_connection = new ClassPeerConnection();
            $peer_connection->class = $request->get('class');
        }
        $peer_connection->offer = $request->get('offer');
        $peer_connection->answer = $request->get('answer');
        $peer_connection->save();

        return response()->json(["status" => "OK", "peer_connection" => $peer_connection]);
    }

    public function sendPeerAnswer(Request $request)
    {
        $answer = json_decode($request->get('answer'), true);
        broadcast(new ClassCallMessageEvent($request->get('class'), null, $answer, null))->toOthers();

        return response()->json(["status" => "OK"]);
    }

    public function sendIceCandidate(Request $request)
    {
        $candidate = json_decode($request->get('candidate'), true);
        broadcast(new ClassCallMessageEvent($request->get('class'), null, null, $candidate))->toOthers();

        return response()->json(["status" => "OK"]);
    }

    public function startOTSession(Request $request)
    {
        $class_event = ClassEvent::find($request->get('classEvent'));
        $sessionId = $class_event->opentok_session;
        $opentok = new OpenTok(env('TOKBOX_KEY'), env('TOKBOX_SECRET'));

        if(is_null($sessionId)){
            $session = $opentok->createSession(array(
                'archiveMode' => ArchiveMode::MANUAL,
                'mediaMode' => MediaMode::ROUTED
            ));
            $sessionId = $session->getSessionId();
        }

        $token = $opentok->generateToken($sessionId, array(
            'role'       => Role::MODERATOR,
            'expireTime' => time()+(24 * 60 * 60),
            'data'       => 'name='.$request->user()->name,
            'initialLayoutClassList' => array('focus')
        ));

        $class_event->opentok_session = $sessionId;
        $class_event->opentok_token = $token;
        $class_event->save();

        return response()->json(["status" => "OK", "session_id" => $sessionId, "token" => $token]);
    }
}

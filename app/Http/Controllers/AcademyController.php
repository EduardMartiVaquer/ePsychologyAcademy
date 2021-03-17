<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subject;
use App\Models\ClassEvent;
use App\Models\CourseFile;
use App\Models\SubjectFile;
use App\Models\SubjectTeacher;
use App\Models\SubjectStudent;
use App\Models\ClassPeerConnection;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AcademyController extends Controller
{
    public function saveSubject(Request $request)
    {
        $subject = null;
        if(!is_null($request->get('subject'))){
            $subject = Subject::find($request->get('subject'));
        }
        if(is_null($subject)){
            $subject = new Subject();
        }
        $subject->course = $request->get('course');
        $subject->name = $request->get('name');
        $subject->description = $request->get('description');
        $subject->created_by = $request->user()->id;
        $subject->save();

        $teachers_ids = explode(";", $request->get('teachers'));
        foreach ($teachers_ids as $id) {
            if(is_null(SubjectTeacher::where('user', $id)->where('subject', $subject->id)->first())){
                $teacher = SubjectTeacher::create([
                    'user' => $id,
                    'subject' => $subject->id
                ]);
            }
        }

        $students_ids = explode(";", $request->get('students'));
        foreach ($students_ids as $id) {
            if(is_null(SubjectStudent::where('user', $id)->where('subject', $subject->id)->first())){
                $student = SubjectStudent::create([
                    'user' => $id,
                    'subject' => $subject->id
                ]);
            }
        }

        $subject = Subject::withAll()->find($subject->id);

        return response()->json(["status" => "OK", "subject" => $subject]);
    }

    public function getCalendarEvents(Request $request, $timezone)
    {
        $timezone = str_replace("xxx", "/", $timezone);
        $from = Carbon::createFromTimestamp($request->get('start') / 1000, $timezone)->setTimezone('utc')->toDateTimeString();
        $to = Carbon::createFromTimestamp($request->get('end') / 1000, $timezone)->setTimezone('utc')->toDateTimeString();

        $classes = $request->user()->classes()->where('from', '>=', $from)->where('to', '<=', $to)->get();

        $events_array = array();
        foreach ($classes as $class) {
            $class_subject = Subject::find($class->subject);
            array_push($events_array, array(
                "id" => $class->id,
                "title" => !is_null($class_subject) ? $class_subject->name : "Clase de ".Carbon::createFromFormat('Y-m-d H:i:s', $class->from)->setTimezone($timezone)->format('H:i')." - ".Carbon::createFromFormat('Y-m-d H:i:s', $class->to)->setTimezone($timezone)->format('H:i'),
                "start" => Carbon::createFromFormat('Y-m-d H:i:s', $class->from)->setTimezone($timezone)->toDateTimeString(),
                "end" => Carbon::createFromFormat('Y-m-d H:i:s', $class->to)->setTimezone($timezone)->toDateTimeString(),
                "backgroundColor" => "#298EC1",
                "borderColor" => "#298EC1",
                "allDay" => false,
                "display" => "block"
            ));
        }

        return response()->json($events_array);
    }

    public function uploadCourseFile(Request $request)
    {
        $short_path = Storage::disk('s3')->putFileAs('courses/'.$request->get('course'), $request->file('file'), $request->get('name'), 'public');
        $path = Storage::disk('s3')->url($short_path);
        $course_file = CourseFile::create([
            'course' => $request->get('course'),
            'name' => $request->get('name'),
            'path' => $path,
            'short_path' => $short_path,
            'type' => $request->get('type'),
            'extension' => $request->get('extension')
        ]);
        return response()->json(['file' => $course_file]);
    }

    public function uploadSubjectFile(Request $request)
    {
        $short_path = Storage::disk('s3')->putFileAs('subjects/'.$request->get('subject'), $request->file('file'), $request->get('name'), 'public');
        $path = Storage::disk('s3')->url($short_path);
        $subject_file = SubjectFile::create([
            'subject' => $request->get('subject'),
            'name' => $request->get('name'),
            'path' => $path,
            'short_path' => $short_path,
            'type' => $request->get('type'),
            'extension' => $request->get('extension')
        ]);
        return response()->json(['file' => $subject_file]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Hash;

use Validator;

use App\Teacher;

use App\User;

use App\Course;

class TeacherController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where('role_id', 2)->get();
        
        return view('teacher.teacherIndex', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('teacher.createTeacher');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'name'=>'required|max:50',
            'username'=>'required|max:50|unique:users',
            'specialization'=>'required|min:2',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = new User([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'role_id' => 2,
            'password' => Hash::make($request->get('password')),
        ]);

        $user->save();

        $teacher = new Teacher();
        $teacher->specialization = $request->get('specialization');
        $user->teacher()->save($teacher);

        return redirect('/teacher')->with('success', 'Teacher added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $users = User::where('role_id', 2)->get();
        
        return view('teacher.teacherIndex', compact('users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('teacher.teacherEdit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'=>'required|max:50',
            'username'=>'required|max:50|unique:users',
            'specialization'=>'required|min:2'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $user = User::with('teacher')->find($id);
        $user->name = $request->get('name');
        $user->username = $request->get('username');
        $user->teacher->specialization = $request->get('specialization');
        $user->push();
            
        return redirect('/teacher')->with('success', 'Teacher information updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->teacher->courses()->detach();
        $user->teacher()->delete();
        $user->delete();

        return redirect('/teacher')->with('success', 'Teacher deleted');
    }

    public function assignCourses()
    {
        $users = User::with('teacher')->where('role_id', 2)->get();
        $courses = Course::all();

        return view('teacher.assignCourses',compact('users','courses'));
    }

    public function saveCourses(Request $request)
    {
        if (count($request->courses) < 4) {
            $user = User::find($request->taecher);
            $user->teacher->courses()->sync($request->courses);

            return redirect('/teacher/assignCourse')->with('success', 'Assign operation has been updated');
        }else {
            return redirect('/teacher/assignCourse')->with('err', 'Teacher shouid assign to 3 courses at maximum');
        }
    }

    public function showCourses()
    {
        $user = User::find(\Auth::user()->id);
        $courses = $user->teacher->courses;

        return view('teacher.showCourses',compact('courses'));
    }
}

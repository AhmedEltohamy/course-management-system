@if(Auth::user()->role_id == 2)
@extends('layouts.app')
@section('content')

<div class="container">

    <div class="row">
        <h1>Your assigned courses</h1>
    </div>

    <div class="row">
        <table class="table table-striped">
            <thead>
                <tr>
                <td>Name</td>
                <td>Code</td>
                <td>Content</td>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                <tr>
                    <td>{{$course->name}}</td>
                    <td>{{$course->code}}</td>
                    <td>{{$course->content}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>    
</div>
@endsection

@else
<script type="text/javascript">
    window.location = "{{ url('/home') }}";
</script>
@endif
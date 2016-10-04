@extends('main')

@section('title', '| About')

@section('content')
        <div class="row">
            <div class="col-md-12">
                <h1>About   <i>{{$data['fullname']}}</i></h1>
                <p>A software developer that specializes in web and mobile technologies, making use of modern and latest technologies in efficiently scaling and buildnig apps to provide an excellent user experience. My email is {{$data['email']}}</p>
            </div>
        </div>

@endsection

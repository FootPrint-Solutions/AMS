@extends('template.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            {{-- Header --}}
            <div class="profile-header">
                <div class="row align-items-center">
                    <div class="col-auto profile-image">
                        <a href="#">
                            <img class="rounded-circle" alt="User Image" src="{{ asset('/img/profiles/avatar-02.jpg') }}">
                        </a>
                    </div>

                    <div class="col ms-md-n2 profile-user-info">
                        @auth
                            <h4 class="user-name mb-0">{{ auth()->user()->name }}</h4>
                            <h6 class="text-muted">{{ auth()->user()->email }}</h6>
                        @endauth
                    </div>

                    <div class="col-auto profile-btn">
                        <a href="" class="btn btn-primary">
                            Edit Profile Picture
                        </a>
                    </div>
                </div>
            </div>

            {{-- Content Title --}}
            <div class="profile-menu">
                <ul class="nav nav-tabs nav-tabs-solid">
                    {{-- About --}}
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#about-tab">About</a>
                    </li>

                    {{-- Password --}}
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#password-tab">Password</a>
                    </li>
                </ul>
            </div>

            {{-- Content Tab --}}
            <div class="tab-content profile-tab-cont">
                {{-- About Tab --}}
                <div class="tab-pane fade show active" id="about-tab">
                    @include('Profile.about')
                </div>

                {{-- Password Tab --}}
                <div id="password-tab" class="tab-pane fade">
                    @include('Profile.password')
                </div>

            </div>
        </div>
    </div>
@endsection

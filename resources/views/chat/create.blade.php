@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex align-items-center justify-content-around">
                <span class="h2 m-0">
                    <strong>
                        {{ __('New Chat') }}
                    </strong>
                </span>
                <span>
                    <div class="dropdown">
                      <a class="btn btn-primary text-white dropdown-toggle" href="#" role="button"
                         data-bs-toggle="dropdown"
                         aria-expanded="false">
                        {{auth()->user()->name}}
                      </a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:"
                               onclick="document.querySelector('#logout-form').submit()">Выйти</a></li>
                          <form action="{{route('logout')}}" method="post" id="logout-form">@csrf</form>
                      </ul>
                    </div>
                </span>
            </div>
        </div>
        <a href="{{route('chat.index')}}" class="text-white m-0 p-0 text-decoration-none w-100 h-100 text-center">
            <div class="bg-secondary bg-opacity-50 py-2 px-4 text-white">
                <i class="bi bi-arrow-left-circle"></i>
                <span class="ms-1">Back</span>
            </div>
        </a>
        <div class="card-body">
            <form action="{{route('chat.store')}}" method="post">
                @csrf
                <div class="form-group">
                    <select class="form-select"
                            name="user_id"
                            required
                            aria-label="Users list">
                        <option selected>Open this users' list</option>
                        @foreach($users as $user)
                            <option value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success text-white">{{__('Create')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex align-items-center justify-content-around">
                <span class="h2 m-0">
                    <strong>
                        {{ __('Chat') }}
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
                               onclick="document.querySelector('#logout-form').submit()">Logout</a></li>
                          <form action="{{route('logout')}}" method="post" id="logout-form">@csrf</form>
                      </ul>
                    </div>
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-end">
                <a href="{{route('chat.create')}}" class="btn btn-flat btn-sm btn-primary text-white py-1 px-2">New
                    conversation</a>
            </div>
            <vue-index>

            </vue-index>
        </div>
    </div>
@endsection

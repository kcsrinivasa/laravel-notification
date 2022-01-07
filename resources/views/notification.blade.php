@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Notifications
                    <span class="float-right">
                        <span class="d-flex">
                            <form action="{{route('notification.markAsRead.all')}}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit" class="mx-2 btn btn-sm btn-info">mark all as read</button>
                            </form>
                            | 
                            <form action="{{route('notification.destory')}}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="mx-2 btn btn-sm btn-danger">clear all</button>
                            </form>
                        </span>
                    </span>
                </div>

                <div class="card-body">
                   
                    <ul class="list-group">
                        @forelse($notifications as $notification)
                            <li class="list-group-item">
                                <a href="{{route('notification.show',$notification->id)}}" class="text-dark">{{$notification->data['name']}}</a>
                                @if($notification->unread())
                                    <span class="float-right">
                                        <form action="{{route('notification.markAsRead.individual',$notification->id)}}" method="POST">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-primary">mark as read</button>
                                        </form>
                                    </span>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item text-secondary">No notifications found</li>
                        @endforelse
                    </ul>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

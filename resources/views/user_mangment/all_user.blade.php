@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header  text-black">
            <h4 class="mb-0">Users Management</h4>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->status == 'pending')
                                <span class="badge bg-blank text-dark">{{ ucfirst($user->status) }}</span>
                            @else
                                <span class="badge bg-dark">{{ ucfirst($user->status) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->status == 'pending')
                                <form method="POST" action="{{ route('admin.users.approve', $user->id) }}">
                                    @csrf
                                    <button class="btn btn-dark btn-sm">
                                        Approve
                                    </button>
                                </form>
                            @else
                                <span class="text-success fw-bold">Approved</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

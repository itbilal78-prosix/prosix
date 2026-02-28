@extends('layouts.dashboard')

@section('content')
    <div class="container mt-4">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Users Management</h4>
            </div>

            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Registered At</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>

                                <td>
                                    @if ($user->status == 'pending')
                                        <span class="badge bg-warning text-dark">
                                            Pending OTP
                                        </span>
                                    @elseif($user->status == 'approved')
                                        <span class="badge bg-success">
                                            Verified
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $user->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PATCH')

                                        @if ($user->status == 'blocked')
                                            <button class="btn btn-success btn-sm">
                                                <i class="bi bi-unlock"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-danger btn-sm">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

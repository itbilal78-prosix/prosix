@extends('layouts.dashboard')

@section('content')

<style>

.contact-admin-page {
    padding: 25px;
}

.contact-admin-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 24px;
}

.contact-admin-header h2 {
    margin: 0;
    font-size: 26px;
    font-weight: 700;
    color: #111;
}

.contact-total-badge {
    background: #111;
    color: #fff;
    padding: 7px 13px;
    border-radius: 30px;
    font-size: 12px;
}

.contact-card {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 12px;
    overflow: hidden;
}

.contact-table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.contact-table {
    width: 100%;
    border-collapse: collapse;
}

.contact-table thead {
    background: #f7f7f7;
}

.contact-table th {
    padding: 14px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #555;
    border-bottom: 1px solid #e5e5e5;
    white-space: nowrap;
}

.contact-table td {
    padding: 15px 16px;
    font-size: 13px;
    color: #444;
    border-bottom: 1px solid #efefef;
    vertical-align: middle;
}

.contact-table tbody tr:last-child td {
    border-bottom: none;
}

.contact-table tbody tr.contact-unread {
    background: #fafafa;
}

.contact-name {
    font-weight: 700;
    color: #111;
}

.contact-email,
.contact-phone {
    color: #666;
    font-size: 12px;
}

.contact-message-short {
    max-width: 280px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.contact-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 5px 10px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 600;
}

.contact-status-new {
    background: #111;
    color: #fff;
}

.contact-status-read {
    background: #ededed;
    color: #555;
}

.contact-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.contact-view-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 32px;
    padding: 0 12px;
    border-radius: 5px;
    background: #111;
    color: #fff;
    text-decoration: none;
    font-size: 12px;
}

.contact-view-btn:hover {
    background: #333;
    color: #fff;
}

.contact-delete-btn {
    height: 32px;
    padding: 0 12px;
    border-radius: 5px;
    border: 1px solid #dedede;
    background: #fff;
    color: #d60000;
    font-size: 12px;
    cursor: pointer;
}

.contact-delete-btn:hover {
    background: #fff1f1;
}

.contact-empty {
    padding: 70px 20px;
    text-align: center;
    color: #777;
}

.contact-pagination {
    padding: 20px;
}

</style>


<div class="contact-admin-page">

    <div class="contact-admin-header">

        <h2>
            Contact Us Data
        </h2>


        <span class="contact-total-badge">

            {{ $messages->total() }}

            Messages

        </span>

    </div>


    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    <div class="contact-card">

        @if($messages->count())

            <div class="contact-table-wrapper">

                <table class="contact-table">

                    <thead>

                        <tr>

                            <th>#</th>

                            <th>Name</th>

                            <th>Email / Phone</th>

                            <th>Message</th>

                            <th>Status</th>

                            <th>Date</th>

                            <th>Action</th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($messages as $message)

                            <tr
                                class="{{ !$message->is_read ? 'contact-unread' : '' }}"
                            >

                                <td>
                                    {{ $message->id }}
                                </td>


                                <td>

                                    <div class="contact-name">

                                        {{ $message->first_name }}

                                        {{ $message->last_name }}

                                    </div>

                                </td>


                                <td>

                                    @if($message->email)

                                        <div class="contact-email">

                                            <i class="bi bi-envelope me-1"></i>

                                            {{ $message->email }}

                                        </div>

                                    @endif


                                    @if($message->phone)

                                        <div class="contact-phone">

                                            <i class="bi bi-telephone me-1"></i>

                                            {{ $message->phone }}

                                        </div>

                                    @endif

                                </td>


                                <td>

                                    <div class="contact-message-short">

                                        {{ $message->message }}

                                    </div>

                                </td>


                                <td>

                                    @if($message->is_read)

                                        <span
                                            class="contact-status contact-status-read"
                                        >
                                            Read
                                        </span>

                                    @else

                                        <span
                                            class="contact-status contact-status-new"
                                        >
                                            New
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    {{ $message->created_at->format('d M Y') }}

                                    <br>

                                    <small>

                                        {{ $message->created_at->format('h:i A') }}

                                    </small>

                                </td>


                                <td>

                                    <div class="contact-actions">

                                        <a
                                            href="{{ route('admin.contact-messages.show', $message) }}"
                                            class="contact-view-btn"
                                        >

                                            <i class="bi bi-eye me-1"></i>

                                            View

                                        </a>


                                        <form
                                            method="POST"
                                            action="{{ route('admin.contact-messages.destroy', $message) }}"
                                            onsubmit="return confirm('Delete this contact message?')"
                                        >

                                            @csrf

                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                class="contact-delete-btn"
                                            >

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            <div class="contact-pagination">

                {{ $messages->links() }}

            </div>

        @else

            <div class="contact-empty">

                <i
                    class="bi bi-inbox"
                    style="font-size:35px;"
                ></i>

                <div class="mt-3">

                    No contact messages found.

                </div>

            </div>

        @endif

    </div>

</div>

@endsection

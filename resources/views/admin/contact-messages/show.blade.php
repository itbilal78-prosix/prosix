@extends('layouts.dashboard')

@section('content')

<style>

.contact-view-page {
    padding: 25px;
    max-width: 900px;
}

.contact-view-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #555;
    text-decoration: none;
    margin-bottom: 18px;
    font-size: 13px;
}

.contact-view-back:hover {
    color: #000;
}

.contact-view-card {
    border: 1px solid #e5e5e5;
    border-radius: 12px;
    background: #fff;
    overflow: hidden;
}

.contact-view-header {
    padding: 22px 24px;
    background: #f7f7f7;
    border-bottom: 1px solid #e5e5e5;
}

.contact-view-header h2 {
    margin: 0 0 6px;
    font-size: 24px;
    font-weight: 700;
    color: #111;
}

.contact-view-date {
    font-size: 12px;
    color: #777;
}

.contact-view-info {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
    padding: 24px;
    border-bottom: 1px solid #eee;
}

.contact-view-field span {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #999;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.contact-view-field a,
.contact-view-field strong {
    font-size: 14px;
    color: #222;
    text-decoration: none;
}

.contact-view-message {
    padding: 24px;
}

.contact-view-message h4 {
    margin: 0 0 12px;
    font-size: 14px;
    font-weight: 700;
}

.contact-view-message p {
    margin: 0;
    line-height: 1.8;
    font-size: 14px;
    color: #444;
    white-space: pre-line;
}

@media(max-width:700px) {

    .contact-view-info {
        grid-template-columns: 1fr;
    }

}

</style>


<div class="contact-view-page">

    <a
        href="{{ route('admin.contact-messages.index') }}"
        class="contact-view-back"
    >

        <i class="bi bi-arrow-left"></i>

        Back to Contact Messages

    </a>


    <div class="contact-view-card">

        <div class="contact-view-header">

            <h2>

                {{ $contactMessage->first_name }}

                {{ $contactMessage->last_name }}

            </h2>


            <div class="contact-view-date">

                Received on

                {{ $contactMessage->created_at->format('d M Y, h:i A') }}

            </div>

        </div>


        <div class="contact-view-info">

            <div class="contact-view-field">

                <span>
                    Email
                </span>


                @if($contactMessage->email)

                    <a href="mailto:{{ $contactMessage->email }}">

                        {{ $contactMessage->email }}

                    </a>

                @else

                    <strong>
                        Not provided
                    </strong>

                @endif

            </div>


            <div class="contact-view-field">

                <span>
                    Phone
                </span>


                @if($contactMessage->phone)

                    <a href="tel:{{ $contactMessage->phone }}">

                        {{ $contactMessage->phone }}

                    </a>

                @else

                    <strong>
                        Not provided
                    </strong>

                @endif

            </div>

        </div>


        <div class="contact-view-message">

            <h4>
                Message
            </h4>


            <p>
                {{ $contactMessage->message }}
            </p>

        </div>

    </div>

</div>

@endsection

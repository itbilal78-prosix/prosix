<h2>New Artwork Request</h2>

<p><strong>Name:</strong> {{ $data->full_name }}</p>
<p><strong>Email:</strong> {{ $data->email }}</p>
<p><strong>Phone:</strong> {{ $data->phone }}</p>
<p><strong>Team:</strong> {{ $data->team_name }}</p>

<p><strong>Products:</strong></p>
<ul>
@foreach($data->products as $item)
    <li>{{ $item }}</li>
@endforeach
</ul>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>New Artwork Request</title>
</head>

<body style="font-family: Arial;background:#f4f4f4;padding:30px;">

<div style="max-width:700px;margin:auto;background:white;border-radius:10px;overflow:hidden;border:1px solid #ddd;">

<!-- HEADER -->
<div style="background:black;color:white;padding:20px;text-align:center;">
<h2 style="margin:0;">🔥 New Artwork Request</h2>
<p style="margin:5px 0 0;">Prosix Website Notification</p>
</div>

<!-- BODY -->
<div style="padding:25px;">

<h3 style="margin-top:0;">Customer Details</h3>

<table width="100%" style="border-collapse:collapse;">
<tr>
<td><strong>Name</strong></td>
<td>{{ $data->full_name }}</td>
</tr>

<tr>
<td><strong>Email</strong></td>
<td>{{ $data->email }}</td>
</tr>

<tr>
<td><strong>Phone</strong></td>
<td>{{ $data->phone }}</td>
</tr>

<tr>
<td><strong>Instagram</strong></td>
<td>{{ $data->instagram }}</td>
</tr>

<tr>
<td><strong>Address</strong></td>
<td>{{ $data->address }}</td>
</tr>

<tr>
<td><strong>Team Name</strong></td>
<td>{{ $data->team_name }}</td>
</tr>

<tr>
<td><strong>Role</strong></td>
<td>{{ $data->role }}</td>
</tr>

<tr>
<td><strong>Quantity</strong></td>
<td>{{ $data->quantity }}</td>
</tr>

<tr>
<td><strong>Team Color</strong></td>
<td>{{ $data->team_color }}</td>
</tr>

<tr>
<td><strong>Home / Away</strong></td>
<td>{{ $data->home_away }}</td>
</tr>

<tr>
<td><strong>Design Style</strong></td>
<td>{{ $data->design_style }}</td>
</tr>

<tr>
<td><strong>Material</strong></td>
<td>{{ $data->material }}</td>
</tr>

<tr>
<td><strong>Source</strong></td>
<td>{{ $data->source }}</td>
</tr>

</table>


<h3 style="margin-top:25px;">Selected Products</h3>

<ul>
@foreach($data->products as $item)
<li>{{ $item }}</li>
@endforeach
</ul>


@if($data->additional)
<h3>Additional Notes</h3>
<p>{{ $data->additional }}</p>
@endif


@if($data->artwork_file)

<h3>Uploaded Reference Images</h3>

@php
$images = json_decode($data->artwork_file);
@endphp

@foreach($images as $img)

<img src="{{ asset('uploads/artwork/'.$img) }}"
style="width:120px;margin:5px;border-radius:6px;border:1px solid #ccc;">

@endforeach

@endif


</div>

<!-- FOOTER -->
<div style="background:#f1f1f1;padding:15px;text-align:center;font-size:13px;">
Prosix Sports • Artwork Request Alert
</div>

</div>

</body>
</html>

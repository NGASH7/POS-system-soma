@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Credit Details</h1>
    <p>Credit ID: {{ $credit->id ?? 'N/A' }}</p>
</div>
@endsection

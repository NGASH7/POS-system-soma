@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Return</h1>
    <form action="{{ route('returns.update', $return->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="pending" {{ $return->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $return->status == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $return->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="completed" {{ $return->status == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control">{{ $return->notes }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('returns.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Return/Exchange</h1>
    <div class="card">
        <div class="card-body">
            <form id="returnForm">
                @csrf
                <div class="form-group">
                    <label>Search Sale (Invoice No or Customer)</label>
                    <div class="input-group">
                        <input type="text" id="searchSale" class="form-control" placeholder="Enter invoice number or customer name">
                        <button type="button" id="searchBtn" class="btn btn-primary">Search</button>
                    </div>
                </div>

                <div id="saleDetails" style="display:none;">
                    <h4>Sale Details</h4>
                    <div id="saleInfo"></div>
                    <div id="itemsList"></div>
                    <div id="exchangeSection" style="display:none;"></div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('searchBtn').addEventListener('click', function() {
    const search = document.getElementById('searchSale').value;
    if (!search) {
        alert('Please enter search term');
        return;
    }
    
    fetch('{{ route("returns.search") }}?search=' + encodeURIComponent(search))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show sale details
                document.getElementById('saleDetails').style.display = 'block';
                // Populate sale info
            } else {
                alert(data.message);
            }
        });
});
</script>
@endsection

<div class="receipt-embed-wrapper">
@include('pos.partials.receipt-styles')
@include('pos.partials.receipt-body')
</div>

<style>
    .receipt-embed-wrapper {
        display: flex;
        justify-content: center;
        width: 100%;
        min-height: min-content;
        padding-bottom: 8px;
    }

    .receipt-embed-wrapper .soma-receipt {
        width: 100%;
        max-width: 360px;
    }
</style>

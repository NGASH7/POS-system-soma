<style>
    .soma-receipt {
        font-family: 'Courier New', Courier, monospace;
        font-size: 13px;
        line-height: 1.5;
        color: #000000;
        max-width: 360px;
        margin: 0 auto;
        background: #fff;
    }

    .soma-receipt .text-center { text-align: center; }
    .soma-receipt .text-right { text-align: right; }

    .soma-receipt .store-name {
        font-size: 20px;
        font-weight: 800;
        letter-spacing: 2px;
        margin-bottom: 4px;
        color: #000000;
    }

    .soma-receipt .store-details {
        font-size: 12px;
        line-height: 1.6;
        color: #000000;
        font-weight: 600;
    }

    .soma-receipt .divider {
        border: none;
        border-top: 1px dashed #000000;
        margin: 12px 0;
    }

    .soma-receipt .invoice-no {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        word-break: break-all;
        color: #000000;
    }

    .soma-receipt .receipt-meta {
        font-size: 12px;
        line-height: 1.7;
        color: #000000;
        font-weight: 600;
    }

    .soma-receipt .items-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .soma-receipt .items-table th {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border-bottom: 1px solid #000000;
        padding: 6px 0;
        text-align: left;
        color: #000000;
    }

    .soma-receipt .items-table th.col-qty { text-align: center; width: 36px; }
    .soma-receipt .items-table th.col-price,
    .soma-receipt .items-table th.col-total { text-align: right; width: 72px; }

    .soma-receipt .items-table td {
        padding: 6px 0;
        font-size: 12px;
        vertical-align: top;
        border-bottom: 1px dotted #000000;
        color: #000000;
        font-weight: 600;
    }

    .soma-receipt .items-table td.col-qty { text-align: center; }
    .soma-receipt .items-table td.col-price,
    .soma-receipt .items-table td.col-total { text-align: right; white-space: nowrap; }

    .soma-receipt .item-name {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        padding-right: 8px;
        color: #000000;
        font-weight: 800;
    }

    .soma-receipt .amount {
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
        color: #000000;
    }

    .soma-receipt .row-line {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 12px;
        padding: 3px 0;
        font-size: 13px;
        font-weight: 600;
    }

    .soma-receipt .row-line .label { color: #000000; flex-shrink: 0; }
    .soma-receipt .row-line .value { font-weight: 800; text-align: right; color: #000000; }

    .soma-receipt .row-line.total {
        font-size: 17px;
        font-weight: 800;
        border-top: 2px dashed #000000;
        margin-top: 6px;
        padding-top: 8px;
    }

    .soma-receipt .row-line.total .label,
    .soma-receipt .row-line.total .value { color: #000000; font-weight: 800; }

    .soma-receipt .payment-details .row {
        display: flex;
        justify-content: space-between;
        padding: 3px 0;
        font-size: 13px;
    }

    .soma-receipt .payment-details .row .label { color: #000000; font-weight: 600; }
    .soma-receipt .payment-details .row .value { font-weight: 800; color: #000000; }

    .soma-receipt .customer-section {
        margin: 10px 0;
        padding: 10px 0;
        border-top: 1px dashed #000000;
        border-bottom: 1px dashed #000000;
    }

    .soma-receipt .customer-section p {
        font-size: 12px;
        line-height: 1.7;
        color: #000000;
        font-weight: 600;
    }

    .soma-receipt .footer-message {
        font-size: 12px;
        line-height: 1.7;
        color: #000000;
        font-weight: 600;
    }

    .soma-receipt .footer-message .thankyou {
        font-weight: 800;
        font-size: 14px;
        color: #000000;
        margin-bottom: 4px;
    }

    .soma-receipt .end-marker {
        font-size: 11px;
        letter-spacing: 1px;
        color: #000000;
        font-weight: 600;
    }

    .soma-receipt .timestamp {
        font-size: 11px;
        color: #000000;
        margin-top: 4px;
        font-weight: 600;
    }

    /* Print Styles */
    @media print {
        @page {
            size: 80mm auto;
            margin: 3mm;
        }

        .soma-receipt {
            max-width: 100%;
            width: 100%;
            font-size: 12px;
        }

        .soma-receipt .item-name {
            white-space: normal;
            word-break: break-word;
            overflow: visible;
            text-overflow: unset;
        }

        .soma-receipt .store-name {
            font-size: 16px;
        }

        .soma-receipt .row-line.total {
            font-size: 15px;
        }

        .soma-receipt .items-table td {
            border-bottom: 1px dotted #ddd;
        }

        .soma-receipt .divider {
            border-top: 1px dashed #666;
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .soma-receipt {
            color: #e5e7eb;
            background: #1f2937;
        }

        .soma-receipt .store-details,
        .soma-receipt .receipt-meta,
        .soma-receipt .row-line .label,
        .soma-receipt .payment-details .row .label,
        .soma-receipt .footer-message {
            color: #9ca3af;
        }

        .soma-receipt .items-table th {
            border-bottom-color: #4b5563;
        }

        .soma-receipt .items-table td {
            border-bottom-color: #374151;
        }

        .soma-receipt .divider {
            border-top-color: #4b5563;
        }

        .soma-receipt .row-line.total {
            border-top-color: #4b5563;
        }

        .soma-receipt .row-line.total .label,
        .soma-receipt .row-line.total .value {
            color: #e5e7eb;
        }

        .soma-receipt .store-name {
            color: #e5e7eb;
        }

        .soma-receipt .invoice-no {
            color: #e5e7eb;
        }

        .soma-receipt .footer-message .thankyou {
            color: #e5e7eb;
        }

        .soma-receipt .end-marker {
            color: #6b7280;
        }

        .soma-receipt .timestamp {
            color: #6b7280;
        }

        .soma-receipt .customer-section {
            border-top-color: #4b5563;
            border-bottom-color: #4b5563;
        }

        .soma-receipt .customer-section p {
            color: #9ca3af;
        }
    }
</style>
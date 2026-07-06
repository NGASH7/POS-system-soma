<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $sale->invoice_no }}</title>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            height: 100%;
            background: #f3f4f6;
            overflow: auto; /* Prevents the entire browser window from scrolling */
        }

        body {
            padding: 24px 16px 32px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        /* Action Buttons Container - FIXED VISIBILITY AT THE TOP */
        .action-bar {
            width: 100%;
            max-width: 400px;
            margin: 0 auto 16px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            flex-shrink: 0; /* Prevents actions from shrinking */
        }

        /* NEW Wrapper to constrain height and allow inner scrolling */
        .receipt-scroll-container {
            width: 100%;
            max-width: 400px;
            max-height: auto; /* Limits view height to prevent cutting off or escaping the screen */
            overflow-y: auto;  /* Enables smooth scrolling */
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            background: #fff;
            
            /* Custom sleek scrollbar styles */
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        /* Chrome, Edge, and Safari scrollbar tweaks */
        .receipt-scroll-container::-webkit-scrollbar {
            width: 6px;
        }
        .receipt-scroll-container::-webkit-scrollbar-track {
            background: transparent;
        }
        .receipt-scroll-container::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }

        .receipt-page {
            width: 100%;
            background: #fff;
            padding: 24px 20px;
            font-size: 13.5px; /* Slightly adjusted scale to fit more content visually */
        }

        .action-btn {
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            justify-content: center;
            min-width: 120px;
        }

        .action-btn i {
            font-size: 16px;
        }

        .btn-print {
            background: #4f46e5;
            color: #fff;
        }

        .btn-print:hover { 
            background: #4338ca; 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-save {
            background: #059669;
            color: #fff;
        }

        .btn-save:hover { 
            background: #047857; 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }

        .btn-close {
            background: #6b7280;
            color: #fff;
        }

        .btn-close:hover { 
            background: #4b5563; 
            transform: translateY(-2px);
        }

        .btn-pdf {
            background: #dc2626;
            color: #fff;
        }

        .btn-pdf:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: #1f2937;
            color: #fff;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 90%;
        }

        .toast.show {
            opacity: 1;
        }

        .toast-success {
            background: #065f46;
            border-left: 4px solid #34d399;
        }

        .toast-error {
            background: #991b1b;
            border-left: 4px solid #f87171;
        }

        .toast-info {
            background: #1e40af;
            border-left: 4px solid #60a5fa;
        }

        @media print {
            html, body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
                min-height: auto !important;
                overflow: visible !important; /* Force displays normal pagination limits for paper prints */
            }

            .receipt-scroll-container {
                max-height: none !important;
                overflow: visible !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .receipt-page {
                max-width: 100% !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 16px !important;
                margin: 0 !important;
                background: #fff !important;
            }

            .action-bar {
                display: none !important;
            }

            .toast {
                display: none !important;
            }
        }

        @media (max-width: 480px) {
            body { padding: 16px 12px 24px; }
            .receipt-scroll-container { max-height: auto; }
            .receipt-page { padding: 20px 16px; }
            .action-btn { padding: 10px 16px; font-size: 13px; min-width: 80px; }
            .action-bar { gap: 6px; }
        }
    </style>
    
    @include('pos.partials.receipt-styles')
</head>
<body>
    <div class="action-bar">
        <button class="action-btn btn-print" onclick="printReceipt()">
            <i class="fas fa-print"></i> Print
        </button>
        <button class="action-btn btn-save" onclick="saveReceipt()">
            <i class="fas fa-download"></i> Save
        </button>
        <button class="action-btn btn-pdf" onclick="saveAsPDF()">
            <i class="fas fa-file-pdf"></i> PDF
        </button>
        <button class="action-btn btn-close" onclick="closeWindow()">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    <div class="receipt-scroll-container">
        <div class="receipt-page" id="receipt-content">
            @include('pos.partials.receipt-body')
        </div>
    </div>

    <div id="toast" class="toast">
        <span id="toast-icon"><i class="fas fa-check-circle"></i></span>
        <span id="toast-message">Receipt saved successfully!</span>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        /**
         * Print the receipt
         */
        function printReceipt() {
            window.print();
            showToast('Printing receipt...', 'success');
        }

        /**
         * Save receipt as HTML file
         */
        function saveReceipt() {
            try {
                const receiptContent = document.getElementById('receipt-content').innerHTML;
                const styles = document.querySelector('style').innerHTML;
                
                const htmlContent = `
                    <!DOCTYPE html>
                    <html>
                        <head>
                            <meta charset="UTF-8">
                            <title>Receipt - {{ $sale->invoice_no }}</title>
                            <style>
                                * { margin: 0; padding: 0; box-sizing: border-box; }
                                body { 
                                    font-family: 'Courier New', monospace; 
                                    padding: 20px; 
                                    background: #fff;
                                    max-width: 400px;
                                    margin: 0 auto;
                                }
                                ${styles}
                            </style>
                        </head>
                        <body>
                            ${receiptContent}
                        </body>
                    </html>
                `;

                const blob = new Blob([htmlContent], { type: 'text/html' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `receipt-{{ $sale->invoice_no }}.html`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                
                showToast('Receipt saved successfully!', 'success');
            } catch (error) {
                console.error('Save error:', error);
                showToast('Failed to save receipt', 'error');
            }
        }

        /**
         * Save as PDF
         */
        function saveAsPDF() {
            showToast('Select "Save as PDF" in the print dialog', 'info');
            setTimeout(function() {
                window.print();
            }, 500);
        }

        /**
         * Close the current window
         */
        function closeWindow() {
            if (confirm('Are you sure you want to close this receipt?')) {
                window.close();
            }
        }

        /**
         * Show toast notification
         */
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            const toastIcon = document.getElementById('toast-icon');
            
            toastMessage.textContent = message;
            
            const icons = {
                success: '<i class="fas fa-check-circle"></i>',
                error: '<i class="fas fa-exclamation-circle"></i>',
                info: '<i class="fas fa-info-circle"></i>'
            };
            
            toastIcon.innerHTML = icons[type] || icons.success;
            toast.className = `toast toast-${type} show`;
            
            clearTimeout(window.toastTimeout);
            window.toastTimeout = setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        /**
         * Keyboard shortcuts
         */
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                printReceipt();
            }
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveReceipt();
            }
            if (e.key === 'Escape') {
                closeWindow();
            }
        });

        // Auto-print if opened from POS
        window.onload = function() {
            if (window.opener) {
                setTimeout(function() {
                    if (confirm('Print this receipt?')) {
                        printReceipt();
                    }
                }, 500);
            }
        };
    </script>
</body>
</html>
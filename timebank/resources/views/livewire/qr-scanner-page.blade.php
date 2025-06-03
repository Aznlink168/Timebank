<div class="py-2">
    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <h3 class="text-xl font-semibold text-gray-900 mb-2">QR Code Scanner</h3>
        <p class="text-sm text-gray-600 mb-4">
            Position the QR code within the frame to scan it. Ensure good lighting.
        </p>

        <div id="qr-reader-container" class="max-w-md mx-auto">
            <div id="qr-reader" class="border-2 border-dashed border-gray-300 rounded-lg p-2" style="width:100%;"></div>
        </div>

        <div id="qr-reader-results" class="mt-4 text-center text-sm text-gray-700 font-medium"></div>

        @if($success_message)
            <div x-data="{ show: true }" x-init="setTimeout(() => { show = false; Livewire.dispatch('scanProcessedUserFeedback'); }, 5000)" x-show="show"
                 class="mt-6 p-4 bg-green-100 text-green-800 border border-green-300 rounded-lg shadow-md">
                <p class="font-bold">Success!</p>
                <p>{{ $success_message }}</p>
            </div>
        @endif

        @if($error_message)
            <div x-data="{ show: true }" x-init="setTimeout(() => { show = false; Livewire.dispatch('scanProcessedUserFeedback'); }, 5000)" x-show="show"
                 class="mt-6 p-4 bg-red-100 text-red-800 border border-red-300 rounded-lg shadow-md">
                <p class="font-bold">Error!</p>
                <p>{{ $error_message }}</p>
            </div>
        @endif

        {{-- This message might be too fleeting if an error/success message immediately follows --}}
        @if($scan_result && !$success_message && !$error_message)
            <div class="mt-6 p-4 bg-blue-100 text-blue-800 border border-blue-300 rounded-lg shadow-md">
                Processing scanned code: {{ Str::limit($scan_result, 30) }}...
            </div>
        @endif

    </div>

    @push('scripts')
    <script src="{{ asset('node_modules/html5-qrcode/html5-qrcode.min.js') }}"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            // Ensure Livewire is fully loaded and initialized
             Livewire.on('scanProcessed', (message) => {
                // This event can be used to provide user feedback, e.g., non-alert based.
                // For now, the component itself handles showing messages.
                // We could potentially re-initialize or clear scanner here if needed.
                // console.log(message);
            });
        });

        function docReady(fn) {
            // see if DOM is already available
            if (document.readyState === "complete" || document.readyState === "interactive") {
                // call on next available tick
                setTimeout(fn, 1);
            } else {
                document.addEventListener("DOMContentLoaded", fn);
            }
        }

        docReady(function() {
            const qrReaderElement = document.getElementById('qr-reader');
            const qrReaderResultsElement = document.getElementById('qr-reader-results');

            if (qrReaderElement) {
                let html5QrcodeScanner;

                function onScanSuccess(decodedText, decodedResult) {
                    if (qrReaderResultsElement) {
                         qrReaderResultsElement.textContent = `Scan result: ${decodedText}`;
                    }
                    console.log(`Code matched = ${decodedText}`, decodedResult);
                    // Assuming 'processQrScanned' is the correct event name listened by your Livewire component
                    window.Livewire.dispatch('processQrScanned', { qrCodeToken: decodedText });

                    // Optional: Clear the scanner to prevent further scans until intended
                    // if (html5QrcodeScanner) {
                    //    html5QrcodeScanner.clear().catch(error => {
                    //        console.error("Failed to clear html5QrcodeScanner.", error);
                    //    });
                    //}
                }

                function onScanFailure(error) {
                    // console.warn(`Code scan error = ${error}`);
                    // No need to display continuous errors to the user, they'll just keep trying.
                }

                // Check if element is visible before rendering. This is a basic check.
                // More robust checks might be needed if the element visibility is complex.
                if (qrReaderElement.offsetParent !== null) {
                    html5QrcodeScanner = new Html5QrcodeScanner(
                        "qr-reader",
                        {
                            fps: 10,
                            qrbox: (viewfinderWidth, viewfinderHeight) => {
                                const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                                const qrboxSize = Math.floor(minEdge * 0.7); // Use 70% of the smaller edge
                                return { width: qrboxSize, height: qrboxSize };
                            },
                            rememberLastUsedCamera: true,
                            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
                        },
                        /* verbose= */ false
                    );
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                } else {
                     console.warn("QR Reader element is not visible, scanner not started.");
                     if(qrReaderResultsElement) {
                        qrReaderResultsElement.textContent = "QR Reader could not start - element not visible.";
                     }
                }

            } else {
                console.error("Element with ID 'qr-reader' not found.");
            }
        });
    </script>
    @endpush
</div>

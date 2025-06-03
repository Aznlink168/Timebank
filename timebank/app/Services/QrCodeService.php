<?php

namespace App\Services;

use App\Models\ServiceAssignment;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeService
{
    /**
     * Generates a unique QR code token, stores it on the ServiceAssignment model,
     * and returns the SVG representation of the QR code.
     * This method is typically called when an assignment is confirmed/accepted.
     *
     * @param ServiceAssignment $assignment The assignment for which to generate a QR code.
     * @return string The SVG string of the generated QR code.
     */
    public function generateForAssignment(ServiceAssignment $assignment): string
    {
        $token = Str::random(40); // Generate a cryptographically secure random token.
        $assignment->qr_code = $token; // Store the token on the assignment.
        $assignment->save(); // Persist the token to the database.

        // Setup QR code renderer
        $renderer = new ImageRenderer(
            new RendererStyle(400), // Size of the QR code image in pixels.
            new SvgImageBackEnd()   // Output format as SVG.
        );
        $writer = new Writer($renderer);
        return $writer->writeString($token); // Return the SVG string.
    }

    /**
     * Retrieves the QR code for a ServiceAssignment as a base64 encoded SVG data URL.
     * This is suitable for embedding directly into an HTML `<img>` tag.
     * Returns null if the assignment does not have a `qr_code` token.
     *
     * @param ServiceAssignment $assignment The assignment whose QR code is needed.
     * @return string|null The data URL (e.g., "data:image/svg+xml;base64,...") or null.
     */
    public function getQrCodeDataUrl(ServiceAssignment $assignment): ?string
    {
        if (empty($assignment->qr_code)) {
            // If no token exists, no QR code can be generated for display.
            // Consider calling ensureQrCodeToken() here if QR code should always exist at this point.
            return null;
        }

        // Setup QR code renderer for a slightly smaller display size.
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($assignment->qr_code);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Generates and ensures the QR code token is set on the assignment.
     * This can be called if a QR code is needed but might not have been generated yet.
     *
     * @param ServiceAssignment $assignment
     * @return string The QR code token.
     */
    public function ensureQrCodeToken(ServiceAssignment $assignment): string
    {
        if (empty($assignment->qr_code)) {
            $token = Str::random(40);
            $assignment->qr_code = $token;
            $assignment->save();
            return $token;
        }
        return $assignment->qr_code;
    }
}

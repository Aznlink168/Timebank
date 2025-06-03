<?php

namespace App\Services;

use Twilio\Rest\Client as TwilioClient;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;

class SmsService
{
    protected ?TwilioClient $client = null; // Holds the Twilio SDK client instance.
    protected ?string $twilioPhoneNumber = null; // The configured Twilio phone number from .env.

    /**
     * Constructor for SmsService.
     * Initializes the Twilio client if credentials are available in the .env file.
     * Logs warnings if credentials are not fully set up.
     */
    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $this->twilioPhoneNumber = env('TWILIO_PHONE_NUMBER');

        // Only attempt to initialize the client if all necessary credentials are present.
        if ($sid && $token && $this->twilioPhoneNumber) {
            try {
                $this->client = new TwilioClient($sid, $token);
            } catch (TwilioException $e) {
                // Log error if client initialization fails (e.g., invalid credentials)
                Log::error('Twilio SDK client could not be initialized: ' . $e->getMessage());
                $this->client = null;
            }
        } else {
            // Log a warning if Twilio is not configured, so SMS functionality will be disabled.
            Log::warning('Twilio credentials (TWILIO_SID, TWILIO_AUTH_TOKEN, TWILIO_PHONE_NUMBER) are not fully configured in .env. SMS sending will be disabled.');
        }
    }

    /**
     * Sends an SMS message to a given phone number.
     *
     * @param string $toPhoneNumber The recipient's phone number, ideally in E.164 format (e.g., +12223334444).
     * @param string $message The text content of the SMS.
     * @return bool True if the message was successfully sent (or queued by Twilio), false otherwise.
     */
    public function sendMessage(string $toPhoneNumber, string $message): bool
    {
        // Do not attempt to send if the client isn't initialized or the 'from' number is missing.
        if (!$this->client || !$this->twilioPhoneNumber) {
            Log::info("SMS not sent (Twilio client not initialized or 'from' phone number missing): To: {$toPhoneNumber}, Message: {$message}");
            // This indicates a configuration issue or intentional disabling if credentials are not set.
            return false;
        }

        // Basic validation for E.164 format. Twilio generally prefers this.
        // A more robust validation might involve a dedicated library.
        if (!preg_match('/^\+[1-9]\d{1,14}$/', $toPhoneNumber)) {
            Log::error("Invalid phone number format provided to SmsService: {$toPhoneNumber}. Must be E.164 format.");
            return false;
        }

        try {
            $this->client->messages->create(
                $toPhoneNumber,
                [
                    'from' => $this->twilioPhoneNumber,
                    'body' => $message,
                ]
            );
            Log::info("SMS sent successfully to {$toPhoneNumber}.");
            return true;
        } catch (TwilioException $e) {
            Log::error("Error sending SMS via Twilio: " . $e->getMessage() . " (Code: " . $e->getCode() . ")");
            // More specific error handling based on Twilio error codes could be added here.
        } catch (\Exception $e) {
            Log::error("Generic error sending SMS: " . $e->getMessage());
        }

        return false;
    }
}

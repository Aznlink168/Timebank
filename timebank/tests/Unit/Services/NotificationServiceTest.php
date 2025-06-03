<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Notification; // Eloquent model
use App\Services\SmsService;
use App\Services\NotificationService; // The class we're testing
use App\Mail\NewAssignmentNotification; // Example Mailable
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Mockery;

class NotificationServiceTest extends TestCase
{
    protected $smsServiceMock;
    protected $notificationService;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock User
        $this->user = Mockery::mock(User::class)->makePartial();
        $this->user->id = 1;
        $this->user->email = 'testuser@example.com';

        // Mock SmsService
        $this->smsServiceMock = Mockery::mock(SmsService::class);

        // Instantiate NotificationService with the mock SmsService
        $this->notificationService = new NotificationService($this->smsServiceMock);

        // Mock the Mail facade
        Mail::fake();

        // Mock the Notification Eloquent model using overload for static 'create'
        Mockery::mock('overload:App\Models\Notification');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_notify_user_sends_email_when_preference_is_email()
    {
        $this->user->notification_preference = 'email';
        $mailable = new NewAssignmentNotification($this->user, Mockery::mock('App\Models\ServiceRequest'));

        \App\Models\Notification::shouldReceive('create')->once(); // Using fully qualified namespace with overload
        Mail::shouldReceive('to')->with($this->user)->andReturnSelf(); // Mock 'to'
        Mail::shouldReceive('send')->with($mailable)->once();         // Assert 'send' is called
        $this->smsServiceMock->shouldNotReceive('sendMessage');

        $this->notificationService->notifyUser($this->user, 'test_type', 'Test Subject', $mailable, 'sms message');
    }

    public function test_notify_user_sends_sms_when_preference_is_sms_and_phone_exists()
    {
        $this->user->notification_preference = 'sms';
        $this->user->phone_number = '+15551234567'; // E.164 format
        $mailable = new NewAssignmentNotification($this->user, Mockery::mock('App\Models\ServiceRequest'));

        \App\Models\Notification::shouldReceive('create')->once(); // Using fully qualified namespace with overload
        Mail::shouldNotReceive('send');
        $this->smsServiceMock->shouldReceive('sendMessage')
            ->with($this->user->phone_number, 'test sms message')
            ->once()
            ->andReturn(true);

        $this->notificationService->notifyUser($this->user, 'test_type', 'Test Subject', $mailable, 'test sms message');
    }

    public function test_notify_user_sends_both_when_preference_is_both_and_phone_exists()
    {
        $this->user->notification_preference = 'both';
        $this->user->phone_number = '+15551234567';
        $mailable = new NewAssignmentNotification($this->user, Mockery::mock('App\Models\ServiceRequest'));

        \App\Models\Notification::shouldReceive('create')->once(); // Using fully qualified namespace with overload
        Mail::shouldReceive('to')->with($this->user)->andReturnSelf();
        Mail::shouldReceive('send')->with($mailable)->once();
        $this->smsServiceMock->shouldReceive('sendMessage')
            ->with($this->user->phone_number, 'test sms message')
            ->once()
            ->andReturn(true);

        $this->notificationService->notifyUser($this->user, 'test_type', 'Test Subject', $mailable, 'test sms message');
    }

    public function test_notify_user_sends_nothing_when_preference_is_none()
    {
        $this->user->notification_preference = 'none';
        $mailable = new NewAssignmentNotification($this->user, Mockery::mock('App\Models\ServiceRequest'));

        \App\Models\Notification::shouldReceive('create')->once(); // Using fully qualified namespace with overload
        Mail::shouldNotReceive('send');
        $this->smsServiceMock->shouldNotReceive('sendMessage');

        $this->notificationService->notifyUser($this->user, 'test_type', 'Test Subject', $mailable, 'test sms message');
    }

    public function test_notify_user_does_not_send_sms_if_phone_number_missing()
    {
        $this->user->notification_preference = 'sms'; // or 'both'
        $this->user->phone_number = null;
        $mailable = new NewAssignmentNotification($this->user, Mockery::mock('App\Models\ServiceRequest'));

        Log::shouldReceive('warning')->with(\Mockery::pattern('/user has no phone number/'))->once();

        \App\Models\Notification::shouldReceive('create')->once(); // Using fully qualified namespace with overload
        $this->smsServiceMock->shouldNotReceive('sendMessage');

        $this->notificationService->notifyUser($this->user, 'test_type', 'Test Subject', $mailable, 'test sms message');
    }

    public function test_notification_is_persisted_to_database()
    {
        $this->user->notification_preference = 'none'; // To prevent actual mail/SMS calls
        $testData = ['request_id' => 123];

        \App\Models\Notification::shouldReceive('create') // Using fully qualified namespace with overload
            ->with([
                'user_id' => $this->user->id,
                'type' => 'test_persistence',
                'message' => 'Persistence Subject',
                'data' => $testData,
            ])
            ->once();

        $this->notificationService->notifyUser(
            $this->user,
            'test_persistence',
            'Persistence Subject',
            null, // No mailable
            null, // No SMS
            $testData
        );
    }
}

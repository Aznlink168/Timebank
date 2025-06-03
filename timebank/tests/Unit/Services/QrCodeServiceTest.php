<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\ServiceAssignment;
use App\Services\QrCodeService;
// use Illuminate\Support\Facades\Str; // Will use FQCN
use Mockery;
use BaconQrCode\Writer;

class QrCodeServiceTest extends TestCase
{
    protected $qrCodeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->qrCodeService = new QrCodeService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_generate_for_assignment_creates_token_saves_and_returns_svg()
    {
        $assignmentMock = Mockery::mock(ServiceAssignment::class)->makePartial();
        $assignmentMock->shouldReceive('save')->once();

        $fakeToken = 'test_token_1234567890abcdefghijklmnopqrstuvwxyz';
        \Illuminate\Support\Facades\Str::shouldReceive('random')->with(40)->once()->andReturn($fakeToken);

        $svg = $this->qrCodeService->generateForAssignment($assignmentMock);

        $this->assertEquals($fakeToken, $assignmentMock->qr_code);
        $this->assertStringStartsWith('<svg', $svg);
        $this->assertStringContainsString($fakeToken, $svg); // The token should be findable in the SVG path data, though this is a loose check
    }

    public function test_get_qr_code_data_url_returns_correct_data_url()
    {
        $assignment = new ServiceAssignment(); // Using a real model instance, not hitting DB
        $fakeToken = 'existing_token_0987654321';
        $assignment->qr_code = $fakeToken;

        $dataUrl = $this->qrCodeService->getQrCodeDataUrl($assignment);

        $this->assertNotNull($dataUrl);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $dataUrl);

        $base64Part = str_replace('data:image/svg+xml;base64,', '', $dataUrl);
        $decodedSvg = base64_decode($base64Part);

        $this->assertStringStartsWith('<svg', $decodedSvg);
        $this->assertStringContainsString($fakeToken, $decodedSvg);
    }

    public function test_get_qr_code_data_url_returns_null_if_no_token()
    {
        $assignment = new ServiceAssignment();
        $assignment->qr_code = null;

        $dataUrl = $this->qrCodeService->getQrCodeDataUrl($assignment);
        $this->assertNull($dataUrl);
    }

    public function test_ensure_qr_code_token_generates_token_if_missing()
    {
        $assignmentMock = Mockery::mock(ServiceAssignment::class)->makePartial();
        $assignmentMock->qr_code = null; // Ensure it's initially null

        $fakeToken = 'newly_generated_token_for_ensure';
        \Illuminate\Support\Facades\Str::shouldReceive('random')->with(40)->once()->andReturn($fakeToken);
        $assignmentMock->shouldReceive('save')->once();

        $token = $this->qrCodeService->ensureQrCodeToken($assignmentMock);

        $this->assertEquals($fakeToken, $token);
        $this->assertEquals($fakeToken, $assignmentMock->qr_code);
    }

    public function test_ensure_qr_code_token_returns_existing_token()
    {
        $assignmentMock = Mockery::mock(ServiceAssignment::class)->makePartial();
        $existingToken = 'already_existing_token_123';
        $assignmentMock->qr_code = $existingToken;

        \Illuminate\Support\Facades\Str::shouldNotReceive('random'); // Should not be called if token exists
        $assignmentMock->shouldNotReceive('save'); // Should not save if token exists

        $token = $this->qrCodeService->ensureQrCodeToken($assignmentMock);
        $this->assertEquals($existingToken, $token);
    }
}

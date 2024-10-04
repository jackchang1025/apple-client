<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Apple\Client\DataConstruct\Phone;
use Apple\Client\Response\HasPhoneNumbers;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

class TestResponse
{
    use HasPhoneNumbers;

    protected Response $mockResponse;

    public function __construct(Response $mockResponse)
    {
        $this->mockResponse = $mockResponse;
    }

    public function json($key = null, $default = null)
    {
        return $this->mockResponse->json($key, $default);
    }

    public function authorizeSing()
    {
        return $this->mockResponse->authorizeSing();
    }
}

beforeEach(function () {
    $this->mockResponse = Mockery::mock(Response::class);
    $this->testResponse = new TestResponse($this->mockResponse);
});

it('gets trusted phone number when available', function () {
    $phoneData = [
        'number' => '1234567890',
        'countryCode' => 'US',
        'id' => 1,
    ];

    $this->mockResponse->shouldReceive('authorizeSing')
        ->andReturn([
            'direct' => [
                'twoSV' => [
                    'phoneNumberVerification' => [
                        'trustedPhoneNumber' => $phoneData,
                    ],
                ],
            ],
        ]);

    /**
     * @var Phone $trustedPhone
     */
    $trustedPhone = $this->testResponse->getTrustedPhoneNumber();

    expect($trustedPhone)->toBeInstanceOf(Phone::class);
    ;
});

it('returns null when trusted phone number is not available', function () {
    $this->mockResponse->shouldReceive('authorizeSing')
        ->andReturn([
            'direct' => [
                'twoSV' => [
                    'phoneNumberVerification' => [],
                ],
            ],
        ]);

    $trustedPhone = $this->testResponse->getTrustedPhoneNumber();

    expect($trustedPhone)->toBeNull();
});

it('gets all trusted phone numbers', function () {
    $phoneData = [
        [
            'number' => '1234567890',
            'countryCode' => 'US',
            'id' => 1,
        ],
        [
            'number' => '0987654321',
            'countryCode' => 'UK',
            'id' => 2,
        ],
    ];

    $this->mockResponse->shouldReceive('authorizeSing')
        ->andReturn([
            'direct' => [
                'twoSV' => [
                    'phoneNumberVerification' => [
                        'trustedPhoneNumbers' => $phoneData,
                    ],
                ],
            ],
        ]);

    $trustedPhones = $this->testResponse->getTrustedPhoneNumbers();

    expect($trustedPhones)->toBeInstanceOf(Collection::class);
    expect($trustedPhones)->toHaveCount(2);
    expect($trustedPhones[0])->toBeInstanceOf(Phone::class);
    expect($trustedPhones[1])->toBeInstanceOf(Phone::class);
});

it('returns empty collection when no trusted phone numbers are available', function () {
    $this->mockResponse->shouldReceive('authorizeSing')
        ->andReturn([
            'direct' => [
                'twoSV' => [
                    'phoneNumberVerification' => [
                        'trustedPhoneNumbers' => [],
                    ],
                ],
            ],
        ]);

    $trustedPhones = $this->testResponse->getTrustedPhoneNumbers();

    expect($trustedPhones)->toBeInstanceOf(Collection::class);
    expect($trustedPhones)->toBeEmpty();
});

it('gets phone number verification information', function () {
    $verificationData = [
        'status' => 'verified',
        'phoneNumber' => '1234567890',
    ];

    $this->mockResponse->shouldReceive('json')
        ->with('phoneNumberVerification', null)
        ->andReturn($verificationData);

    $verification = $this->testResponse->phoneNumberVerification();

    expect($verification)->toBe($verificationData);
});

it('returns null when phone number verification information is not available', function () {
    $this->mockResponse->shouldReceive('json')
        ->with('phoneNumberVerification', null)
        ->andReturn(null);

    $verification = $this->testResponse->phoneNumberVerification();

    expect($verification)->toBeNull();
});

it('handles JsonException when parsing phone data', function () {
    $this->mockResponse->shouldReceive('authorizeSing')
        ->andThrow(new JsonException('Invalid JSON'));

    $trustedPhone = $this->testResponse->getTrustedPhoneNumber();
})->throws(JsonException::class);

it('handles JsonException when parsing trusted phone numbers', function () {
    $this->mockResponse->shouldReceive('authorizeSing')
        ->andThrow(new JsonException('Invalid JSON'));

    $trustedPhones = $this->testResponse->getTrustedPhoneNumbers();
})->throws(JsonException::class);

it('handles JsonException when getting phone number verification', function () {
    $this->mockResponse->shouldReceive('json')
        ->with('phoneNumberVerification', null)
        ->andThrow(new JsonException('Invalid JSON'));

    $verification = $this->testResponse->phoneNumberVerification();
})->throws(JsonException::class);

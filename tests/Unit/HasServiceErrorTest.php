<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Weijiajia\DataConstruct\ServiceError;
use Weijiajia\Response\HasServiceError;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

class ResponseTest
{
    use HasServiceError;

    protected Response $mockResponse;

    public function __construct(Response $mockResponse)
    {
        $this->mockResponse = $mockResponse;
    }

    /**
     * @param $key
     * @param $default
     *
     * @throws JsonException
     *
     * @return mixed|array
     */
    public function json($key = null, $default = null): mixed
    {
        return $this->mockResponse->json($key, $default);
    }

    public function authorizeSing(): array
    {
        return $this->mockResponse->json();
    }
}

beforeEach(function () {
    $this->mockResponse = Mockery::mock(Response::class);
    $this->testResponse = new ResponseTest($this->mockResponse);
});

it('gets service errors from service_errors key', function () {
    $errorData = [
        ['code' => 'ERROR_1', 'message' => 'Error 1'],
        ['code' => 'ERROR_2', 'message' => 'Error 2'],
    ];

    $this->mockResponse->shouldReceive('json')
        ->with('service_errors', [])
        ->andReturn($errorData);

    $errors = $this->testResponse->getServiceErrors();

    expect($errors)->toBeInstanceOf(Collection::class)
        ->and($errors)->toHaveCount(2)
        ->and($errors->first())->toBeInstanceOf(ServiceError::class)
        ->and($errors->first()->getCode())->toBe('ERROR_1')
        ->and($errors->first()->getMessage())->toBe('Error 1');
});

it('gets service errors from validationErrors key when service_errors is empty', function () {
    $errorData = [
        ['code' => 'VALIDATION_1', 'message' => 'Validation Error 1'],
    ];

    $this->mockResponse->shouldReceive('json')
        ->with('service_errors', [])
        ->andReturn([]);

    $this->mockResponse->shouldReceive('json')
        ->with('validationErrors', [])
        ->andReturn($errorData);

    $errors = $this->testResponse->getServiceErrors();

    expect($errors)->toBeInstanceOf(Collection::class)
        ->and($errors)->toHaveCount(1)
        ->and($errors->first())->toBeInstanceOf(ServiceError::class)
        ->and($errors->first()->getCode())->toBe('VALIDATION_1')
        ->and($errors->first()->getMessage())->toBe('Validation Error 1');
});

it('returns empty collection when no errors are present', function () {
    $this->mockResponse->shouldReceive('json')
        ->with('service_errors', [])
        ->andReturn([]);

    $this->mockResponse->shouldReceive('json')
        ->with('validationErrors', [])
        ->andReturn([]);

    $this->mockResponse->shouldReceive('json')
        ->with('serviceErrors', [])
        ->andReturn([]);

    $errors = $this->testResponse->getServiceErrors();

    expect($errors)->toBeInstanceOf(Collection::class)
        ->and($errors)->toBeEmpty();
});

it('gets the first service error', function () {
    $errorData = [
        ['code' => 'ERROR_1', 'message' => 'Error 1'],
        ['code' => 'ERROR_2', 'message' => 'Error 2'],
    ];

    $this->mockResponse->shouldReceive('json')
        ->with('service_errors', [])
        ->andReturn($errorData);

    $error = $this->testResponse->getFirstServiceError();

    expect($error)->toBeInstanceOf(ServiceError::class)
        ->and($error->getCode())->toBe('ERROR_1')
        ->and($error->getMessage())->toBe('Error 1');
});

it('returns null when getting first service error and no errors are present', function () {
    $this->mockResponse->shouldReceive('json')
        ->with('service_errors', [])
        ->andReturn([]);

    $this->mockResponse->shouldReceive('json')
        ->with('validationErrors', [])
        ->andReturn([]);

    $this->mockResponse->shouldReceive('json')
        ->with('serviceErrors', [])
        ->andReturn([]);

    $error = $this->testResponse->getFirstServiceError();

    expect($error)->toBeNull();
});

it('gets authentication service errors', function () {
    $errorData = [
        ['code' => 'AUTH_ERROR_1', 'message' => 'Auth Error 1'],
        ['code' => 'AUTH_ERROR_2', 'message' => 'Auth Error 2'],
    ];

    $this->mockResponse->shouldReceive('json')
        ->withNoArgs()
        ->andReturn([
            'direct' => [
                'twoSV' => [
                    'phoneNumberVerification' => [
                        'serviceErrors' => $errorData,
                    ],
                ],
            ],
        ]);

    $errors = $this->testResponse->getAuthServiceErrors();

    expect($errors)->toBeInstanceOf(Collection::class)
        ->and($errors)->toHaveCount(2)
        ->and($errors->first())->toBeInstanceOf(ServiceError::class)
        ->and($errors->first()->getCode())->toBe('AUTH_ERROR_1')
        ->and($errors->first()->getMessage())->toBe('Auth Error 1');
});

it('gets the first authentication service error', function () {
    $errorData = [
        ['code' => 'AUTH_ERROR_1', 'message' => 'Auth Error 1'],
        ['code' => 'AUTH_ERROR_2', 'message' => 'Auth Error 2'],
    ];

    $this->mockResponse->shouldReceive('json')
        ->withNoArgs()
        ->andReturn([
            'direct' => [
                'twoSV' => [
                    'phoneNumberVerification' => [
                        'serviceErrors' => $errorData,
                    ],
                ],
            ],
        ]);

    $error = $this->testResponse->getFirstAuthServiceError();

    expect($error)->toBeInstanceOf(ServiceError::class)
        ->and($error->getCode())->toBe('AUTH_ERROR_1')
        ->and($error->getMessage())->toBe('Auth Error 1');
});

it('returns null when getting first auth service error and no errors are present', function () {
    $this->mockResponse->shouldReceive('json')
        ->withNoArgs()
        ->andReturn([]);

    $error = $this->testResponse->getFirstAuthServiceError();

    expect($error)->toBeNull();
});

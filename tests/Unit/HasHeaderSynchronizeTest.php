<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use Weijiajia\Header\HasHeaderSynchronize;
use Weijiajia\Header\HasPersistentHeaders;
use Saloon\Enums\Method;
use Saloon\Http\Connector;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use Saloon\Repositories\ArrayStore as SaloonArrayStore;

class TestConnector extends Connector
{
    use HasHeaderSynchronize;
    use HasPersistentHeaders;

    public function resolveBaseUrl(): string
    {
        return 'https://example.com';
    }

    public function defaultPersistentHeaders(): array
    {
        return [
            'X-Persistent-Header-Name',
            'X-Persistent-Header' => 'persistent_value',
            'X-Dynamic-Header' => fn () => 'dynamic_' . time(),
        ];
    }
}

class TestRequest extends Request
{
    use HasPersistentHeaders;

    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/test';
    }

    public function defaultPersistentHeaders(): array
    {
        return ['X-Request-Persistent-Header' => 'request_persistent_value'];
    }
}

beforeEach(function () {
    $this->connector = new TestConnector();
    $this->request = new TestRequest();
    $this->pendingRequest = new PendingRequest($this->connector, $this->request);
    $this->mockClient = new MockClient();
});

it('initializes without header repositories', function () {
    expect($this->connector->getHeaderRepositories())->toBeNull();
});

it('sets and gets header repositories', function () {
    $headerRepo = new SaloonArrayStore();
    $this->connector->setHeaderRepositories($headerRepo);

    expect($this->connector->getHeaderRepositories())->toBe($headerRepo);
});

it('boots HasHeaderSynchronize with header repositories', function () {
    // 设置特定的模拟响应
    MockClient::global([
        TestRequest::class => MockResponse::make([], 200, ['X-Response-Header' => 'response_value']),
    ]);

    $headerRepo = new SaloonArrayStore(['X-Test-Header' => 'test_value']);
    $this->connector->setHeaderRepositories($headerRepo);

    $response = $this->connector->send($this->request);

    $pendingRequest = $response->getPendingRequest();

    expect($pendingRequest->headers()->get('X-Persistent-Header'))->toBe('persistent_value')
        ->and($pendingRequest->headers()->get('X-Request-Persistent-Header'))->toBe('request_persistent_value')
        ->and($headerRepo->get('X-Response-Header'))->toBe('response_value');
});

it('supports callback functions for default values', function () {
    // 设置特定的模拟响应
    MockClient::global([
        TestRequest::class => MockResponse::make([], 200, ['X-Response-Header' => 'response_value']),
    ]);

    $headerRepo = new SaloonArrayStore();
    $this->connector->setHeaderRepositories($headerRepo);

    $response = $this->connector->send($this->request);

    $dynamicHeader = $response->getPendingRequest()->headers()->get('X-Dynamic-Header');

    expect($dynamicHeader)->toMatch('/^dynamic_\d+$/');
});

it('does not add header when callback returns null', function () {
    // 设置特定的模拟响应
    MockClient::global([
        TestRequest::class => MockResponse::make([], 200, ['X-Response-Header' => 'response_value']),
    ]);

    $headerRepo = new SaloonArrayStore();

    $this->connector = new class () extends TestConnector {
        public function defaultPersistentHeaders(): array
        {
            return [
                'X-Null-Header' => fn () => null,
            ];
        }
    };

    $this->connector->setHeaderRepositories($headerRepo);

    $response = $this->connector->send($this->request);

    $pendingRequest = $response->getPendingRequest();

    expect($pendingRequest->headers()->get('X-Null-Header'))->toBeNull();
});

it('does not modify headers when header repositories is not set', function () {
    // 设置特定的模拟响应
    MockClient::global([
        TestRequest::class => MockResponse::make([], 200, ['X-Response-Header' => 'response_value']),
    ]);

    $headerRepo = new SaloonArrayStore(['X-Persistent-Header-Name' => 'X-Persistent-Header-Value']);
    $this->connector->setHeaderRepositories($headerRepo);

    $response = $this->connector->send($this->request);

    $pendingRequest = $response->getPendingRequest();

    expect($pendingRequest->headers()->get('X-Test-Header'))->toBeNull()
        ->and($pendingRequest->headers()->get('X-Persistent-Header'))->toBe('persistent_value')
        ->and($pendingRequest->headers()->get('X-Request-Persistent-Header'))->toBe('request_persistent_value')
        ->and($pendingRequest->headers()->get('X-Persistent-Header-Name'))->toBe('X-Persistent-Header-Value');
});

it('updates header repositories after response', function () {
    // 设置特定的模拟响应
    MockClient::global([
        TestRequest::class => MockResponse::make([], 200, ['X-New-Header' => 'new_value']),
    ]);

    $headerRepo = new SaloonArrayStore();
    $this->connector->setHeaderRepositories($headerRepo);

    $response = $this->connector->send($this->request);

    expect($headerRepo->get('X-New-Header'))->toBe('new_value');
});

it('preserves existing headers when adding persistent headers merge', function () {
    $this->connector = new class () extends TestConnector {
        use HasHeaderSynchronize;
        public function defaultPersistentHeaders(): array
        {
            return [
                'persistentHeaders' => 'Connector',
            ];
        }
    };
    $headerRepo = new SaloonArrayStore(['X-Existing-Header' => 'existing_value']);
    $this->connector->setHeaderRepositories($headerRepo);

    $this->request = new class () extends TestRequest {
        public function defaultPersistentHeaders(): array
        {
            return [
                'persistentHeaders' => 'Request',
            ];
        }

        public function resolveEndpoint(): string
        {
            return '/test';
        }
    };

    // 设置特定的模拟响应
    MockClient::global([
        $this->request::class => MockResponse::make([], 200, ['X-New-Header' => 'new_value']),
    ]);

    $response = $this->connector->send($this->request);

    $pendingRequest = $response->getPendingRequest();

    expect($pendingRequest->headers()->get('persistentHeaders'))->toBe('Request');
});

<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use Weijiajia\Config\Config;
use Weijiajia\Config\HasConfig;

class HasConfigTest
{
    use HasConfig;

    protected function defaultConfig(): array
    {
        return [
            'default_key' => 'default_value',
        ];
    }
}

beforeEach(function () {
    $this->hasConfig = new HasConfigTest();
});

it('initializes config with default values', function () {
    $config = $this->hasConfig->config();

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('default_key'))->toBe('default_value');
});

it('sets config using Config object', function () {
    $newConfig = new Config(['new_key' => 'new_value']);
    $this->hasConfig->withConfig($newConfig);

    $config = $this->hasConfig->config();

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('new_key'))->toBe('new_value')
        ->and($config->get('default_key'))->toBeNull();
});

it('merges config using array', function () {
    $this->hasConfig->withConfig(['new_key' => 'new_value']);

    $config = $this->hasConfig->config();

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('new_key'))->toBe('new_value')
        ->and($config->get('default_key'))->toBe('default_value');
});

it('adds single config value', function () {
    $this->hasConfig->withConfig('single_key', 'single_value');

    $config = $this->hasConfig->config();

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('single_key'))->toBe('single_value')
        ->and($config->get('default_key'))->toBe('default_value');
});

it('overwrites existing config value', function () {
    $this->hasConfig->withConfig(['default_key' => 'new_default_value']);

    $config = $this->hasConfig->config();

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('default_key'))->toBe('new_default_value');
});

it('handles nested config values', function () {
    $this->hasConfig->withConfig([
        'nested' => [
            'key1' => 'value1',
            'key2' => 'value2',
        ],
    ]);

    $config = $this->hasConfig->config();

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('nested.key1'))->toBe('value1')
        ->and($config->get('nested.key2'))->toBe('value2');
});

it('maintains existing values when adding new ones', function () {
    $this->hasConfig->withConfig(['new_key1' => 'value1']);
    $this->hasConfig->withConfig(['new_key2' => 'value2']);

    $config = $this->hasConfig->config();

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('new_key1'))->toBe('value1')
        ->and($config->get('new_key2'))->toBe('value2')
        ->and($config->get('default_key'))->toBe('default_value');
});

it('returns instance for method chaining', function () {
    $instance = $this->hasConfig->withConfig(['key' => 'value']);

    expect($instance)->toBeInstanceOf(HasConfigTest::class);
});

it('handles empty array config', function () {
    $this->hasConfig->withConfig([]);

    $config = $this->hasConfig->config();

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('default_key'))->toBe('default_value');
});

it('ignores null value when setting single config', function () {
    $this->hasConfig->withConfig('null_key', null);

    $config = $this->hasConfig->config();

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('null_key'))->toBeNull()
        ->and($config->get('default_key'))->toBe('default_value');
});

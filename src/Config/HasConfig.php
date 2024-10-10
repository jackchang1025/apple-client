<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Weijiajia\Config;

use Saloon\Contracts\ArrayStore as ArrayStoreContract;
use Saloon\Traits\RequestProperties\HasConfig as HasBaseConfig;

trait HasConfig
{
    use HasBaseConfig;

    public function config(): ArrayStoreContract
    {
        return $this->config ??= new Config($this->defaultConfig());
    }

    /**
     * 设置配置.
     *
     * 支持传入 Config 对象或数组来配置
     *
     * @param ArrayStoreContract|array<string, mixed>|string $config
     * @param mixed|null                                     $value
     *
     * @return $this
     */
    public function withConfig(ArrayStoreContract|array|string $config, mixed $value = null): static
    {
        if ($config instanceof Config) {
            $this->config = $config;

            return $this;
        }

        if (is_array($config)) {
            $configObject = $this->config();
            $configObject->merge($config);

            return $this;
        }

        if ($value !== null) {
            $this->config()->add($config, $value);
        }

        return $this;
    }
}

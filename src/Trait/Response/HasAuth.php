<?php

namespace Apple\Client\Trait\Response;

trait HasAuth
{
    public function authorizeSing()
    {
        $document = $this->dom()
            ->filter('script[type="application/json"].boot_args')
            ->first();

        if (!$document->count()) {
            return null;
        }

        // 获取 script 标签的内容
        $jsonString = $document->text();

        // 解码 JSON 数据
        $data = json_decode($jsonString, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('JSON解析错误: '.json_last_error_msg());
        }

        return $data;
    }

    public function hasTrustedDevices():bool
    {
        return $this->authorizeSing()['direct']['hasTrustedDevices'] ?? false;
    }
}
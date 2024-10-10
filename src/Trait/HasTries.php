<?php

namespace Weijiajia\Trait;

use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Request;

trait HasTries
{
    use \Saloon\Traits\RequestProperties\HasTries;

    protected ?\Closure $handleRetry = null;

    public function getTries(): ?int
    {
        return $this->tries;
    }

    public function setTries(?int $tries): void
    {
        $this->tries = $tries;
    }

    public function getRetryInterval(): ?int
    {
        return $this->retryInterval;
    }

    public function setRetryInterval(?int $retryInterval): void
    {
        $this->retryInterval = $retryInterval;
    }

    public function getUseExponentialBackoff(): ?bool
    {
        return $this->useExponentialBackoff;
    }

    public function setUseExponentialBackoff(?bool $useExponentialBackoff): void
    {
        $this->useExponentialBackoff = $useExponentialBackoff;
    }

    public function getThrowOnMaxTries(): ?bool
    {
        return $this->throwOnMaxTries;
    }

    public function setThrowOnMaxTries(?bool $throwOnMaxTries): void
    {
        $this->throwOnMaxTries = $throwOnMaxTries;
    }

    public function getHandleRetry(): ?\Closure
    {
        return $this->handleRetry;
    }

    public function setHandleRetry(?\Closure $handleRetry): void
    {
        $this->handleRetry = $handleRetry;
    }

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        return $handleRetry($exception,$request);
    }

}

<?php

namespace Weijiajia\Trait;

use Saloon\Http\PendingRequest;

trait HasPipeline
{
    public function requestPipelineExists(PendingRequest $pendingRequest,string $name): bool
    {
        $pipes = $pendingRequest->getConnector()
            ->middleware()->getRequestPipeline()->getPipes();

        foreach ($pipes as $pipe) {
            if ($pipe->name === $name) {
                return true;
            }
        }

        return false;
    }

    public function responsePipelineExists(PendingRequest $pendingRequest,string $name): bool
    {
        $pipes = $pendingRequest->getConnector()
            ->middleware()->getResponsePipeline()->getPipes();

        foreach ($pipes as $pipe) {
            if ($pipe->name === $name) {
                return true;
            }
        }

        return false;
    }
}
<?php

namespace Apple\Client\test;

use GuzzleHttp\Client;

class demo
{
    use demo1, demo2;

    protected Client $client1;
    protected Client $client2;
    public function __construct(Client $client)
    {
        $this->client1 = $client;
        $this->client2 = $client;
    }

    public function getClient():Client
    {
        return $this->client1;
    }
}

trait demo1
{
    abstract function getClient(): Client;

    public function bootstrap1()
    {
        return $this->getClient()->get('/bootstrap1');
    }
}

trait demo2
{
    abstract function getClient(): Client;

    public function bootstrap2()
    {
        return $this->getClient()->get('/bootstrap2');
    }
}
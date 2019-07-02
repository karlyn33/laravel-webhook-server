<?php

namespace Spatie\WebhookServer\Tests\TestClasses;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Assert;

class TestClient
{
    private $requests = [];

    private $useResponseCode = 200;

    /**
     * @var bool
     */
    private $shouldFail = false;

    public function request(string $method, string $url, array $options)
    {
        $this->requests[] = compact('method', 'url', 'options');

        if ($this->shouldFail) {
            $request = new Request($method, $url);

            throw new RequestException('qwe', $request, new Response($this->useResponseCode));
        }

        return new Response($this->useResponseCode);
    }

    public function assertRequestCount(int $expectedCount)
    {
        Assert::assertCount($expectedCount, $this->requests);

        return $this;
    }

    public function assertRequestsMade(array $expectedRequests)
    {
        $this->assertRequestCount(count($expectedRequests));

        foreach ($expectedRequests as $index => $expectedRequest) {
            foreach ($expectedRequest as $name => $value) {
                Assert::assertEquals($value, $this->requests[$index][$name]);
            }
        }
    }

    public function letEveryRequestFail(int $code = 500)
    {
        $this->shouldFail = true;
        $this->useResponseCode = $code;
    }

    public function setUseResponseCode(int $code)
    {
        $this->shouldFail = true;
        $this->useResponseCode = $code;
    }
}

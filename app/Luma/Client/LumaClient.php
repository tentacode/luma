<?php declare(strict_types=1);

namespace App\Luma\Client;

use App\Luma\Exception\HueException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Webmozart\Assert\Assert;

use function Safe\json_decode;
use function Safe\json_encode;

final class LumaClient
{
    private ?string $bridgeIp = null;

    public function __construct()
    {
    }

    public function request(string $method, string $uri, ?string $body = null): Response
    {
        if ($this->bridgeIp === null) {
            $this->findBridgeIP();
        }

        Assert::ip($this->bridgeIp, 'Philips Hue Bridge IP could not be found.');

        $options = [];
        if (!empty($body)) {
            $options['body'] = $body;
        }

        $url = $this->getUrl($uri);
        $response = Http::send($method, $url, $options);
        Assert::eq($response->status(), 200, sprintf(
            'Excpected "%s" request to "%s" to be a 200 but the status code is "%s". Body : %s',
            $method,
            $url,
            $response->status(),
            PHP_EOL . $response->body()
        ));

        $jsonBody = $response->json();
        if (!empty($jsonBody[0]['error'])) {
            throw new HueException(
                sprintf(
                    'Request to "%s" failed with error : %s',
                    $url,
                    PHP_EOL . json_encode($jsonBody[0]['error'])
                ),
                (int)$jsonBody[0]['error']['type']
            );
        }

        return $response;
    }

    private function getUrl(string $uri): string
    {
        return sprintf(
            'http://%s/api/%s',
            $this->bridgeIp,
            trim($uri, '/')
        );
    }

    private function findBridgeIP(): void
    {
        $response = Http::get('https://discovery.meethue.com');
        $body = (string)$response->body();
        $data = json_decode($body, true);

        $ipAddress = $data[0]['internalipaddress'] ?? null;

        Assert::ip(
            $ipAddress,
            'Could not find Bridge IP in discovery. Body : ' . $body
        );

        $this->bridgeIp = $ipAddress;
    }
}

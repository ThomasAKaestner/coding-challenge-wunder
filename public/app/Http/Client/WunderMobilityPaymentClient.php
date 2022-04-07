<?php
declare(strict_types=1);

namespace App\Http\Client;

use App\DTOs\PaymentDto;
use App\Exceptions\PaymentClientException;
use GuzzleHttp\Client;
use Illuminate\Log\Logger;
use Throwable;

class WunderMobilityPaymentClient implements PaymentClient
{

    public function __construct(public Client $client, public Logger $logger)
    {
    }

    public function registerCustomer(PaymentDto $paymentDto): string
    {
        $uri = config('paymentproviders.wundermobility.endpoint');

        $options = [
            'body' => json_encode([
                'customerId' => $paymentDto->customerId,
                'iban' => $paymentDto->iban,
                'owner' => $paymentDto->owner,
            ])
        ];

        try {
            $response = $this->client->post($uri, $options);
        } catch (Throwable $exception) {
            $this->logger->warning(sprintf('%s: An error occured while calling the wundermobility payment endpoint', __CLASS__),
                [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTrace(),
                    'options' => $options
                ]);

            throw new PaymentClientException();
        }

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode <= 299) {
            return (string)$response->getBody();
        }

        $this->logger->warning(sprintf('%s: An error occured while calling the wundermobility payment endpoint', __CLASS__),
            [
                'options' => $options
            ]);

        throw new PaymentClientException();
    }
}

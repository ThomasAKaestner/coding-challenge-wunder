<?php
declare(strict_types=1);

namespace App\Http\Client;

use App\DTOs\PaymentDto;
use App\Exceptions\PaymentClientException;

interface PaymentClient
{
    /**
     * @param PaymentDto $paymentDto
     * @return string
     * @throws PaymentClientException
     */
    public function registerCustomer(PaymentDto $paymentDto): string;
}

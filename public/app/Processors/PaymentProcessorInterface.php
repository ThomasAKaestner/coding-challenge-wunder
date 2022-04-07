<?php
declare(strict_types=1);

namespace App\Processors;

use App\DTOs\UserDto;

interface PaymentProcessorInterface
{
    public function registerPayment(UserDto $userDto): string;
}

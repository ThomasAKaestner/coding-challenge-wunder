<?php
declare(strict_types=1);

namespace App\DTOs;

class PaymentDto
{
    public function __construct(public string $iban, public string $owner) {}
}

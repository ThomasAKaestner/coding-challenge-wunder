<?php
declare(strict_types=1);

namespace App\DTOs;

class UserDto
{
    public function __construct(
        public string     $firstName,
        public string     $lastName,
        public string     $telephone,
        public string     $street,
        public string     $house_number,
        public int        $zip_code,
        public string     $city,
        public ?int $customerId = null,
        public ?PaymentDto $paymentDto = null)
    {
    }
}

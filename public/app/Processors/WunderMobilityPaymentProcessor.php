<?php
declare(strict_types=1);

namespace App\Processors;

use App\DTOs\UserDto;
use App\Http\Client\WunderMobilityPaymentClient;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WunderMobilityPaymentProcessor implements PaymentProcessorInterface
{
    public function __construct(private WunderMobilityPaymentClient $paymentClient)
    {
    }

    public function registerPayment(UserDto $userDto): string
    {
        $paymentDataId = DB::transaction(function () use ($userDto) {
            $user = new User();
            $user->first_name = $userDto->firstName;
            $user->last_name = $userDto->lastName;
            $user->telephone = $userDto->telephone;
            $user->street = $userDto->street;
            $user->house_number = $userDto->house_number;
            $user->zip_code = $userDto->zip_code;
            $user->city = $userDto->city;
            $user->account_owner = $userDto->paymentDto->owner;
            $user->iban = $userDto->paymentDto->iban;
            $user->save();

            $user->fresh();

            $userDto->paymentDto->customerId = $user->customer_id;

            $body = $this->paymentClient->registerCustomer($userDto->paymentDto);

            $paymentDataId = ((array)json_decode($body))['paymentDataId'];
            $user->payment_data_id = $paymentDataId;
            $user->save();

            return $paymentDataId;
        }, 3);

        return $paymentDataId;
    }
}

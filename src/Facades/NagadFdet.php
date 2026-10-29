<?php

namespace Theshakhawat\NagadFdet\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array initiatePayment(float $amount, string $orderId, array $additionalMerchantInfo = [])
 * @method static array verifyPayment(string $paymentReferenceId)
 * @method static bool isSuccess(array $response)
 *
 * @see \Theshakhawat\NagadFdet\Services\NagadService
 */
class NagadFdet extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'nagad-fdet';
    }
}

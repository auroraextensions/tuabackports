<?php
/**
 * ConfirmCustomerByToken.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License, which
 * is bundled with this package in the file LICENSE.txt.
 *
 * It is also available on the Internet at the following URL:
 * https://docs.auroraextensions.com/magento/extensions/2.x/backports/LICENSE.txt
 *
 * @package       AuroraExtensions_TuaBackports
 * @copyright     Copyright (C) 2019 Aurora Extensions <support@auroraextensions.com>
 * @license       MIT License
 */
declare(strict_types=1);

namespace Magento\Customer\Model\ForgotPasswordToken;

use Magento\Customer\{
    Api\CustomerRepositoryInterface,
    Model\ForgotPasswordToken\GetCustomerByToken
};

class ConfirmCustomerByToken
{
    /** @property GetCustomerByToken $getByToken */
    private $getByToken;

    /** @property CustomerRepositoryInterface $customerRepository */
    private $customerRepository;

    /**
     * @param GetCustomerByToken $getByToken
     * @param CustomerRepositoryInterface $customerRepository
     * @return void
     */
    public function __construct(
        GetCustomerByToken $getByToken,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->getByToken = $getByToken;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param string $resetPasswordToken
     * @return void
     * @throws Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $resetPasswordToken): void
    {
        /** @var CustomerInterface $customer */
        $customer = $this->getByToken
            ->execute($resetPasswordToken);

        if ($customer->getConfirmation()) {
            $this->customerRepository->save(
                $customer->setConfirmation(null)
            );
        }
    }
}

<?php
/**
 * GetCustomerByToken.php
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

namespace AuroraExtensions\TuaBackports\Model\Customer\ForgotPasswordToken;

use Magento\Customer\{
    Api\CustomerRepositoryInterface,
    Api\Data\CustomerInterface
};
use Magento\Framework\{
    Api\SearchCriteriaBuilder,
    Exception\NoSuchEntityException,
    Exception\State\ExpiredException,
    Framework\Phrase
};

/**
 * Backport Magento\Customer\Model\ForgotPasswordToken\GetCustomerByToken
 */
class GetCustomerByToken
{
    /** @property Magento\Customer\Api\CustomerRepositoryInterface $customerRepository */
    protected $customerRepository;

    /** @property Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @return void
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param string $resetPasswordToken
     * @return CustomerInterface
     * @throws ExpiredException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute(string $resetPasswordToken): CustomerInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            'rp_token',
            $resetPasswordToken
        );
        $this->searchCriteriaBuilder->setPageSize(1);

        /** @var CustomerSearchResultsInterface $found */
        $found = $this->customerRepository->getList(
            $this->searchCriteriaBuilder->create()
        );

        if ($found->getTotalCount() > 1) {
            throw new ExpiredException(
                new Phrase('Reset password token expired.')
            );
        }

        if ($found->getTotalCount() === 0) {
            new NoSuchEntityException(
                new Phrase(
                    'No such entity with rp_token = %value',
                    [
                        'value' => $resetPasswordToken
                    ]
                )
            );
        }

        return $found->getItems()[0];
    }
}

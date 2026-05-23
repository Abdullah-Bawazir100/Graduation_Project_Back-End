<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Binding Interface with Implementation
        $this->app->bind(
            \App\Domain\Department\Repositories\DepartmentRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\DepartmentRepository::class
        );

        $this->app->bind(
            \App\Domain\User\Repositories\UserRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\UserRepository::class
        );

        $this->app->bind(
            \App\Domain\User\Interfaces\TokenServiceInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\TokenServiceRepository::class
        );

        $this->app->bind(
            \App\Domain\User\Interfaces\PasswordHashInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\PasswordHashRepository::class
        );

        $this->app->bind(
            \App\Domain\Activity_Type\Repositories\Activity_Type_RepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\Activity_Type_Repository::class
        );

        $this->app->bind(
            \App\Domain\PaymentType\Repositories\PaymentTypeRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\PaymentTypeRepository::class
        );

        $this->app->bind(
            \App\Domain\Region\Repositories\RegionRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\RegionRepository::class
        );

        $this->app->bind(
            \App\Domain\District\Repositories\DistrictRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\DistrictRepository::class
        );

        $this->app->bind(
            \App\Domain\Address\Repositories\AddressRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\AddressRepository::class
        );

        $this->app->bind(
            \App\Domain\JobType\Repositories\JobTypeRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\JobTypeRepository::class
        );

        $this->app->bind(
            \App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\TaxCollectorRepository::class
        );

        $this->app->bind(
            \App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\TaxPayerRepository::class
        );

        $this->app->bind(
            \App\Domain\Company\Repositories\CompanyRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\CompanyRepository::class
        );

        $this->app->bind(
            \App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\CharitableCompanyRepository::class
        );

        $this->app->bind(
            \App\Domain\TaxType\Repositories\TaxTypeRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\TaxTypeRepository::class
        );

        $this->app->bind(
            \App\Domain\TaxPayerMobile\Repositories\TaxPayerMobileRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\TaxPayerMobileRepository::class
        );

        $this->app->bind(
            \App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\TaxInformationRepository::class
        );

        $this->app->bind(
            \App\Domain\FileStatus\Repositories\FileStatusRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\FileStatusRepository::class
        );

        $this->app->bind(
            \App\Domain\File\Repositories\FileRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\FileRepository::class
        );

        $this->app->bind(
            \App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\FileMovementRepository::class
        );

        $this->app->bind(
            \App\Domain\Request\Repositories\TaxPayerRequestRepositoryInterface::class,
            \App\Infrastructure\Persistence\Eloquent\Repositories\TaxPayerRequestRepository::class
        );

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

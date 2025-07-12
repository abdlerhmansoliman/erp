<?php

namespace App\Providers;

use App\Models\Customer;
use App\Repositories\AuthRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Repositories\Interfaces\positionRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\PurchaseInvoiceRepositoryInterface;
use App\Repositories\Interfaces\SalesInvoiceRepositoryInterface;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Repositories\PositionRepository;
use App\Repositories\ProducetRepository;
use App\Repositories\PurchaseInvoiceRepository;
use App\Repositories\SalesInvoiceRepository;
use App\Repositories\SupplierRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind( AuthRepositoryInterface::class,AuthRepository::class );
        $this->app->bind( EmployeeRepositoryInterface::class,EmployeeRepository::class );
        $this->app->bind( DepartmentRepositoryInterface::class,DepartmentRepository::class );
        $this->app->bind( positionRepositoryInterface::class,PositionRepository::class );
        $this->app->bind( ProductRepositoryInterface::class,ProducetRepository::class );
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class );
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class );
        $this->app->bind(PurchaseInvoiceRepositoryInterface::class, PurchaseInvoiceRepository::class);
        $this->app->bind(SalesInvoiceRepositoryInterface::class, SalesInvoiceRepository::class);
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

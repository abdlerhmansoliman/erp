<?php

namespace App\Providers;

use App\Models\Stock;
use App\Repositories\AuthRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DashbordRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Interfaces\DashbordRepositoryInterface;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Interfaces\positionRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\PurchaseInvoiceRepositoryInterface;
use App\Repositories\Interfaces\PurchaseItemRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\SalesInvoicePaymentRepositoryInterface;
use App\Repositories\Interfaces\SalesInvoiceRepositoryInterface;
use App\Repositories\Interfaces\SalesItemRepositoryInterface;
use App\Repositories\Interfaces\SalesRetrunItemRepositoryInterface;
use App\Repositories\Interfaces\SalesRetrunRepositoryInterface;
use App\Repositories\Interfaces\StockRepositoryInterface;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Repositories\Interfaces\UnitRepositoryInterface;
use App\Repositories\Interfaces\UserRoleRepositoryInterface;
use App\Repositories\Interfaces\WarehouseRepositoryInterface;
use App\Repositories\PaymentRepository;
use App\Repositories\PositionRepository;
use App\Repositories\ProducetRepository;
use App\Repositories\PurchaseInvoiceRepository;
use App\Repositories\PurchaseItemRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SalesInvoicePaymentRepository;
use App\Repositories\SalesInvoiceRepository;
use App\Repositories\SalesItemRepository;
use App\Repositories\SalesRetrunItemRepository;
use App\Repositories\SalesRetrunRepository;
use App\Repositories\StockRepository;
use App\Repositories\SupplierRepository;
use App\Repositories\UnitRepository;
use App\Repositories\UserRoleRepository;
use App\Repositories\WarehouseRepository;
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
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(UserRoleRepositoryInterface::class, UserRoleRepository::class);
        $this->app->bind(UnitRepositoryInterface::class,UnitRepository::class);
        $this->app->bind(WarehouseRepositoryInterface::class,WarehouseRepository::class);
        $this->app->bind(StockRepositoryInterface::class,StockRepository::class);
        $this->app->bind(PurchaseItemRepositoryInterface::class,PurchaseItemRepository::class);
        $this->app->bind(SalesItemRepositoryInterface::class,SalesItemRepository::class);
        $this->app->bind(SalesRetrunRepositoryInterface::class,SalesRetrunRepository::class);
        $this->app->bind(SalesRetrunItemRepositoryInterface::class,SalesRetrunItemRepository::class);
        $this->app->bind(DashbordRepositoryInterface::class,DashbordRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class,PaymentRepository::class);
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

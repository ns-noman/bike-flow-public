<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function() {   
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('logs:clear');
    return 'View cache has been cleared';
});

Route::get('/',function(){return redirect()->route('admin.login');});
Route::get('admin',function(){return redirect()->route('admin.login');});
Route::get('login',function(){return redirect()->route('admin.login');});
Route::get('backend',function(){return redirect()->route('admin.login');});

    

//Item Module Routes
Route::namespace('App\Http\Controllers\backend\items')->group(function(){
    Route::prefix('backend/items')->middleware('admin')->group(function (){
        Route::prefix('categories')->controller(CategoryController::class)->group(function(){
            Route::get('','index')->name('categories.index');
            Route::get('create','createOrEdit')->name('categories.create');
            Route::get('edit/{id?}','createOrEdit')->name('categories.edit');
            Route::post('store','store')->name('categories.store');
            Route::put('update/{id}','update')->name('categories.update');
            Route::delete('delete/{id}','destroy')->name('categories.destroy');
        });
        Route::prefix('sub-categories')->controller(SubCategoryController::class)->group(function(){
            Route::get('','index')->name('sub-categories.index');
            Route::get('create','createOrEdit')->name('sub-categories.create');
            Route::get('edit/{id?}','createOrEdit')->name('sub-categories.edit');
            Route::post('store','store')->name('sub-categories.store');
            Route::put('update/{id}','update')->name('sub-categories.update');
            Route::delete('delete/{id}','destroy')->name('sub-categories.destroy');
        });

        Route::prefix('items')->controller(ItemController::class)->group(function(){
            Route::match(['get','post'],'','index')->name('items.index');
            Route::get('create','createOrEdit')->name('items.create');
            Route::get('edit/{id?}','createOrEdit')->name('items.edit');
            Route::post('store','store')->name('items.store');
            Route::put('update/{id}','update')->name('items.update');
            Route::delete('delete/{id}','destroy')->name('items.destroy');
            Route::get('sub-category/{id?}','subCategory')->name('items.sub-categories');
            Route::get('category/{id?}','categories')->name('items.categories');
            Route::get('list','list')->name('items.list');
        });
    });
});

//Expense Module Routes
Route::namespace('App\Http\Controllers\backend\expense')->group(function(){
    Route::prefix('backend')->middleware('admin')->group(function (){
        Route::prefix('expense-categories')->controller(ExpenseCategoryController::class)->group(function(){
            Route::get('','index')->name('expense-categories.index');
            Route::get('create','createOrEdit')->name('expense-categories.create');
            Route::get('edit/{id?}','createOrEdit')->name('expense-categories.edit');
            Route::post('store','store')->name('expense-categories.store');
            Route::put('update/{id}','update')->name('expense-categories.update');
            Route::get('list','list')->name('expense-categories.list');
        });
        
        Route::prefix('expense-heads')->controller(ExpenseHeadController::class)->group(function(){
            Route::get('','index')->name('expense-heads.index');
            Route::get('create','createOrEdit')->name('expense-heads.create');
            Route::get('edit/{id?}','createOrEdit')->name('expense-heads.edit');
            Route::post('store','store')->name('expense-heads.store');
            Route::put('update/{id}','update')->name('expense-heads.update');
            Route::get('list','list')->name('expense-heads.list');
        });

        Route::prefix('expenses')->controller(ExpenseController::class)->group(function(){
            Route::get('','index')->name('expenses.index');
            Route::get('create','createOrEdit')->name('expenses.create');
            Route::get('edit/{id?}','createOrEdit')->name('expenses.edit');
            Route::get('view/{id?}','view')->name('expenses.view');
            Route::get('expense-heads/{id}','expenseHead')->name('expenses.expense-heads');
            Route::post('store','store')->name('expenses.store');
            Route::put('update/{id}','update')->name('expenses.update');
            Route::post('details','details')->name('expenses.details');
            Route::delete('delete/{id}','destroy')->name('expenses.destroy');
            Route::get('list','list')->name('expenses.list');
            Route::get('approve/{id}','approve')->name('expenses.approve');
        });
        Route::prefix('reports')->controller(ExpenseController::class)->group(function(){
            Route::match(['get','post'],'','reports')->name('expenses.reports');
        });
    });
});


//Expense Module Routes
Route::namespace('App\Http\Controllers\backend\loans')->group(function(){
    Route::prefix('backend')->middleware('admin')->group(function (){
        Route::prefix('parties')->controller(PartyController::class)->group(function(){
            Route::get('','index')->name('parties.index');
            Route::post('store','store')->name('parties.store');
            Route::put('update/{id}','update')->name('parties.update');
            Route::get('create','createOrEdit')->name('parties.create');
            Route::get('edit/{id?}','createOrEdit')->name('parties.edit');
            Route::delete('delete/{id}','destroy')->name('parties.destroy');
            Route::get('list','list')->name('parties.list');
        });
        
        Route::prefix('loans')->controller(PartyLoanController::class)->group(function(){
            Route::get('','index')->name('loans.index');
            Route::post('store','store')->name('loans.store');
            Route::put('update/{id}','update')->name('loans.update');
            Route::get('create','createOrEdit')->name('loans.create');
            Route::get('invoice/{id}','inovice')->name('loans.invoice');
            Route::get('invoice/{id}/{print}','inovice')->name('loans.invoice.print');
            Route::post('payment/store','payment')->name('loans.payment.store');
            Route::delete('payment/destroy','destroy')->name('loans.payment.destroy');
            Route::get('list','list')->name('loans.list');
            Route::get('edit/{id?}','createOrEdit')->name('loans.edit');
            Route::delete('delete/{id}','destroy')->name('loans.destroy');
            Route::get('approve/{id}','approve')->name('loans.approve');
        });

        Route::prefix('party-payments')->controller(PartyPaymentController::class)->group(function(){
            Route::get('','index')->name('party-payments.index');
            Route::get('create','createOrEdit')->name('party-payments.create');
            Route::post('store','store')->name('party-payments.store');
            Route::post('due/invoice','dueInvoice')->name('party-payments.due.invoice');
            Route::get('list','list')->name('party-payments.list');
            Route::get('approve/{id}','approve')->name('party-payments.approve');
            Route::delete('delete/{id}','destroy')->name('party-payments.destroy');
        });

    });
});


Route::prefix('backend')->group(function () {
    Route::namespace('App\Http\Controllers\backend')->group(function(){
        Route::prefix('login')->controller(AdminController::class)->group(function(){
            Route::match(['get', 'post'],'', 'login')->name('admin.login');
        });
        Route::middleware('admin')->group(function (){

            Route::prefix('reports')->controller(ReportController::class)->group(function(){
                Route::match(['get', 'post'],'monthly-bike-sales','monthlyBikeSales')->name('reports.monthly-bike-sales');
                Route::match(['get', 'post'],'bike-inventory','bikeInventory')->name('reports.bike-inventory');
                Route::match(['get', 'post'],'monthly-expenses','monthlyExpense')->name('reports.monthly-expenses');
                Route::match(['get', 'post'],'profit-loss-statement','profitLossStatement')->name('reports.profit-loss-statement');
                Route::match(['get', 'post'],'account-ledger','accountLedger')->name('reports.account-ledger');
                Route::match(['get', 'post'],'accounts-reports','accountReport')->name('reports.accounts-reports');
                Route::match(['get', 'post'],'stock-reports','stockReport')->name('reports.stock-reports');
                Route::match(['get', 'post'],'stock-histories','stockHistory')->name('reports.stock-histories');
                Route::match(['get', 'post'],'my-bikes','myBikes')->name('reports.my-bikes');
                Route::match(['get', 'post'],'investment','investment')->name('reports.investment');
                Route::match(['get', 'post'],'bike-profit','bikeProfit')->name('reports.bike-profit');
                Route::match(['get', 'post'],'investor-ledger-report','investorLedgerReport')->name('reports.investor-ledger');
                Route::match(['get', 'post'],'bike-service-reports','bikeServiceReport')->name('reports.bike-service-reports');
            });


            Route::prefix('purchases')->controller(PurchaseController::class)->group(function(){
                Route::get('','index')->name('purchases.index');
                Route::post('store','store')->name('purchases.store');
                Route::put('update/{id}','update')->name('purchases.update');
                Route::get('create','createOrEdit')->name('purchases.create');
                
                Route::get('vouchar/{id}','vouchar')->name('purchases.vouchar');
                Route::get('vouchar/{id}/{print}','vouchar')->name('purchases.vouchar.print');

                Route::post('payment/store','payment')->name('purchases.payment.store');
                Route::get('list','list')->name('purchases.list');
                Route::get('edit/{id?}','createOrEdit')->name('purchases.edit');
                Route::delete('delete/{id}','destroy')->name('purchases.destroy');
                Route::get('approve/{id}','approve')->name('purchases.approve');

            });

            Route::prefix('payments')->controller(SupplierPaymentController::class)->group(function(){
                Route::get('','index')->name('payments.index');
                Route::get('create','createOrEdit')->name('payments.create');
                Route::post('store','store')->name('payments.store');
                Route::post('due/vouchars','dueVouchars')->name('payments.due.vouchars');
                Route::get('list','list')->name('payments.list');
                Route::get('approve/{id}','approve')->name('payments.approve');
                Route::delete('delete/{id}','destroy')->name('payments.destroy');
            });

            Route::prefix('suppliers')->controller(SupplierController::class)->group(function(){
                Route::get('','index')->name('suppliers.index');
                Route::post('store','store')->name('suppliers.store');
                Route::put('update/{id}','update')->name('suppliers.update');
                Route::get('create','createOrEdit')->name('suppliers.create');
                Route::get('edit/{id?}','createOrEdit')->name('suppliers.edit');
                Route::delete('delete/{id}','destroy')->name('suppliers.destroy');
            });



            
            Route::prefix('customer-payments')->controller(CustomerPaymentController::class)->group(function(){
                Route::get('','index')->name('customer-payments.index');
                Route::get('create','createOrEdit')->name('customer-payments.create');
                Route::post('store','store')->name('customer-payments.store');
                Route::post('due/invoice','dueInvoice')->name('customer-payments.due.invoice');
                Route::get('list','list')->name('customer-payments.list');
                Route::get('approve/{id}','approve')->name('customer-payments.approve');
                Route::delete('delete/{id}','destroy')->name('customer-payments.destroy');
            });

            Route::prefix('sales')->controller(SaleController::class)->group(function(){
                Route::get('','index')->name('sales.index');
                Route::post('store','store')->name('sales.store');
                Route::put('update/{id}','update')->name('sales.update');
                Route::get('create','createOrEdit')->name('sales.create');
                Route::get('invoice/{id}','inovice')->name('sales.invoice');
                Route::get('invoice/{id}/{print}','inovice')->name('sales.invoice.print');
                Route::post('payment/store','payment')->name('sales.payment.store');
                Route::get('list','list')->name('sales.list');
                Route::get('edit/{id?}','createOrEdit')->name('sales.edit');
                Route::delete('delete/{id}','destroy')->name('sales.destroy');
                Route::get('approve/{id}','approve')->name('sales.approve');
            });

            Route::prefix('customers')->controller(CustomerController::class)->group(function(){
                Route::get('','index')->name('customers.index');
                Route::post('store','store')->name('customers.store');
                Route::put('update/{id}','update')->name('customers.update');
                Route::get('create','createOrEdit')->name('customers.create');
                Route::get('edit/{id?}','createOrEdit')->name('customers.edit');
                Route::delete('delete/{id}','destroy')->name('customers.destroy');
            });

            Route::prefix('bike-service-records')->controller(BikeServiceRecordController::class)->group(function(){
                Route::get('','index')->name('bike-service-records.index');
                Route::get('create','createOrEdit')->name('bike-service-records.create');
                Route::get('edit/{id?}','createOrEdit')->name('bike-service-records.edit');
                Route::get('view/{id?}','view')->name('bike-service-records.view');
                Route::get('view/{id}/{print}','view')->name('bike-service-records.print');
                Route::post('store','store')->name('bike-service-records.store');
                Route::put('update/{id}','update')->name('bike-service-records.update');
                Route::delete('delete/{id}','destroy')->name('bike-service-records.destroy');
                Route::get('list','list')->name('bike-service-records.list');
                Route::get('approve/{id}','approve')->name('bike-service-records.approve');
            });

            
            Route::prefix('bike-services')->controller(BikeServiceController::class)->group(function(){
                Route::get('','index')->name('bike-services.index');
                Route::get('create','createOrEdit')->name('bike-services.create');
                Route::get('edit/{id?}','createOrEdit')->name('bike-services.edit');
                Route::post('store','store')->name('bike-services.store');
                Route::put('update/{id}','update')->name('bike-services.update');
                Route::delete('delete/{id}','destroy')->name('bike-services.destroy');
                Route::get('list','list')->name('bike-services.list');
            });



            Route::prefix('bike-service-categories')->controller(BikeServiceCategoryController::class)->group(function(){
                Route::get('','index')->name('bike-service-categories.index');
                Route::get('create','createOrEdit')->name('bike-service-categories.create');
                Route::get('edit/{id?}','createOrEdit')->name('bike-service-categories.edit');
                Route::post('store','store')->name('bike-service-categories.store');
                Route::put('update/{id}','update')->name('bike-service-categories.update');
                Route::delete('delete/{id}','destroy')->name('bike-service-categories.destroy');
                Route::get('list','list')->name('bike-service-categories.list');
            });

            Route::prefix('bike-sales')->controller(BikeSaleController::class)->group(function(){
                Route::get('','index')->name('bike-sales.index');
                Route::get('create','createOrEdit')->name('bike-sales.create');
                Route::get('edit/{id?}','createOrEdit')->name('bike-sales.edit');
                Route::get('invoice/{id?}','invoice')->name('bike-sales.invoice');
                Route::get('invoice/{id}/{print}','invoice')->name('bike-sales.invoice.print');
                Route::post('store','store')->name('bike-sales.store');
                Route::put('update/{id}','update')->name('bike-sales.update');
                Route::delete('delete/{id}','destroy')->name('bike-sales.destroy');                        
                Route::get('list','list')->name('bike-sales.list');
                Route::get('approve/{id}','approve')->name('bike-sales.approve'); 
                Route::get('name-transfers/{id}/{date}','nameTransfer')->name('bike-sales.name-transfers'); 
                Route::get('models/{brand_id}','models')->name('bike-sales.models');
                Route::get('buyer-search','buyerSearch')->name('bike-sales.buyer-search');
                Route::get('broker-search','brokerSearch')->name('bike-sales.broker-search');
                Route::get('bike-search','bikeSearch')->name('bike-sales.bike-search');
            });


            Route::prefix('bike-purchases')->controller(BikePurchaseController::class)->group(function(){
                Route::get('','index')->name('bike-purchases.index');
                Route::get('create','createOrEdit')->name('bike-purchases.create');
                Route::get('edit/{id?}','createOrEdit')->name('bike-purchases.edit');
                Route::get('repurchase/{id?}/{repurchase_id?}','createOrEdit')->name('bike-purchases.repurchase');
                Route::get('invoice/{id?}','invoice')->name('bike-purchases.invoice');
                Route::get('invoice/{id}/{print}','invoice')->name('bike-purchases.invoice.print');
                Route::post('store','store')->name('bike-purchases.store');
                Route::put('update/{id}','update')->name('bike-purchases.update');
                Route::delete('delete/{id}','destroy')->name('bike-purchases.destroy');                        
                Route::get('list','list')->name('bike-purchases.list');
                Route::get('approve/{id}','approve')->name('bike-purchases.approve'); 
                Route::get('models/{brand_id}','models')->name('bike-purchases.models');
                Route::get('seller-search','sellerSearch')->name('bike-purchases.seller-search');
                Route::get('broker-search','brokerSearch')->name('bike-purchases.broker-search');
                Route::get('bike-search','bikeSearch')->name('bike-purchases.bike-search');
            });

            Route::prefix('colors')->controller(ColorController::class)->group(function(){
                Route::get('','index')->name('colors.index');
                Route::get('create','createOrEdit')->name('colors.create');
                Route::get('edit/{id?}','createOrEdit')->name('colors.edit');
                Route::post('store','store')->name('colors.store');
                Route::put('update/{id}','update')->name('colors.update');
                Route::delete('delete/{id}','destroy')->name('colors.destroy');
                Route::get('list','list')->name('colors.list');
            });


            Route::prefix('bike-models')->controller(BikeModelController::class)->group(function(){
                Route::get('','index')->name('bike-models.index');
                Route::get('create','createOrEdit')->name('bike-models.create');
                Route::get('edit/{id?}','createOrEdit')->name('bike-models.edit');
                Route::post('store','store')->name('bike-models.store');
                Route::put('update/{id}','update')->name('bike-models.update');
                Route::delete('delete/{id}','destroy')->name('bike-models.destroy');
                Route::get('list','list')->name('bike-models.list');
            });
            Route::prefix('brands')->controller(BrandController::class)->group(function(){
                Route::get('','index')->name('brands.index');
                Route::get('create','createOrEdit')->name('brands.create');
                Route::get('edit/{id?}','createOrEdit')->name('brands.edit');
                Route::post('store','store')->name('brands.store');
                Route::put('update/{id}','update')->name('brands.update');
                Route::delete('delete/{id}','destroy')->name('brands.destroy');
                Route::get('list','list')->name('brands.list');
            });

            Route::prefix('accounts')->controller(AccountController::class)->group(function(){
                Route::get('','index')->name('accounts.index');
                Route::get('create','createOrEdit')->name('accounts.create');
                Route::get('edit/{id?}','createOrEdit')->name('accounts.edit');
                Route::post('store','store')->name('accounts.store');
                Route::put('update/{id}','update')->name('accounts.update');
                Route::delete('delete/{id}','destroy')->name('accounts.destroy');
                Route::get('list','list')->name('accounts.list');
            });

            Route::prefix('fundtransfers')->controller(FundTransferHistoryController::class)->group(function(){
                Route::get('','index')->name('fundtransfers.index');
                Route::get('create','createOrEdit')->name('fundtransfers.create');
                Route::get('edit/{id?}','createOrEdit')->name('fundtransfers.edit');
                Route::post('store','store')->name('fundtransfers.store');
                Route::put('update/{id}','update')->name('fundtransfers.update');
                Route::delete('delete/{id}','destroy')->name('fundtransfers.destroy');
                Route::get('list','list')->name('fundtransfers.list');
                Route::get('approve/{id}','approve')->name('fundtransfers.approve');
            });
            
            Route::prefix('payment-methods')->controller(PaymentMethodController::class)->group(function(){
                Route::get('','index')->name('payment-methods.index');
                Route::get('create','createOrEdit')->name('payment-methods.create');
                Route::get('edit/{id?}','createOrEdit')->name('payment-methods.edit');
                Route::post('store','store')->name('payment-methods.store');
                Route::put('update/{id}','update')->name('payment-methods.update');
                Route::delete('delete/{id}','destroy')->name('payment-methods.destroy');
                Route::get('list','list')->name('payment-methods.list');
            });

            Route::prefix('investor-transactions')->controller(InvestorTransactionController::class)->group(function(){
                Route::get('','index')->name('investor-transactions.index');
                Route::get('create','createOrEdit')->name('investor-transactions.create');
                Route::get('edit/{id?}','createOrEdit')->name('investor-transactions.edit');
                Route::post('store','store')->name('investor-transactions.store');
                Route::put('update/{id}','update')->name('investor-transactions.update');
                Route::delete('delete/{id}','destroy')->name('investor-transactions.destroy');
                Route::get('list','list')->name('investor-transactions.list');
                Route::get('approve/{id}','approve')->name('investor-transactions.approve');
            });

            Route::prefix('bike-profits')->controller(BikeProfitController::class)->group(function(){
                Route::get('','index')->name('bike-profits.index');
                Route::get('share-records/{id}','shareRecords')->name('bike-profits.share-records');
                Route::get('share-records/{bp_id}/create','createOrEdit')->name('bike-profits.share-records.create');
                Route::get('share-records/{bp_id}/edit/{bpsr_id}','createOrEdit')->name('bike-profits.share-records.edit');
                Route::get('share-records/approve/{bf_id}/{bfsr_id}','approve')->name('bike-profits.share-records.approve');
                Route::get('share-records-list','shareRecordsList')->name('bike-profits.share-records-list');

                Route::post('store','store')->name('bike-profits.store');
                Route::put('update/{id}','update')->name('bike-profits.update');
                Route::delete('delete/{id}','destroy')->name('bike-profits.share-records.destroy');

                Route::get('list','list')->name('bike-profits.list');
                Route::get('change-status/{id}','changeStatus')->name('bike-profits.change-status');
            });
            
            Route::prefix('investors')->controller(InvestorController::class)->group(function(){
                Route::get('','index')->name('investors.index');
                Route::get('create','createOrEdit')->name('investors.create');
                Route::get('edit/{id?}','createOrEdit')->name('investors.edit');
                Route::post('store','store')->name('investors.store');
                Route::put('update/{id}','update')->name('investors.update');
                Route::delete('delete/{id}','destroy')->name('investors.destroy');
                Route::get('list','list')->name('investors.list');
            });



            Route::prefix('menus')->controller(MenuController::class)->group(function(){
                Route::get('','index')->name('menus.index');
                Route::get('create','createOrEdit')->name('menus.create');
                Route::get('edit/{id?}/{addmenu?}','createOrEdit')->name('menus.edit');
                Route::post('store','store')->name('menus.store'); 
                Route::put('update/{id}','update')->name('menus.update');
                Route::delete('delete/{id}','destroy')->name('menus.destroy');
            });

            Route::prefix('frontend-menus')->controller(FrontendMenuController::class)->group(function(){
                Route::get('','index')->name('frontend-menus.index');
                Route::get('create','createOrEdit')->name('frontend-menus.create');
                Route::get('edit/{id?}/{addmenu?}','createOrEdit')->name('frontend-menus.edit');
                Route::post('store','store')->name('frontend-menus.store'); 
                Route::put('update/{id}','update')->name('frontend-menus.update');
                Route::delete('delete/{id}','destroy')->name('frontend-menus.destroy');
            });
            
            Route::prefix('logout')->controller(AdminController::class)->group(function(){
                Route::post('', 'logout')->name('admin.logout');
            });
            Route::prefix('dashboard')->controller(DashboardController::class)->group(function(){
                Route::get('','index')->name('dashboard.index');
                Route::get('bike-list','bikeList')->name('dashboard.bike-list');
                Route::get('summery-data/{dateRange?}','summeryData')->name('dashboard.summery-data');
            });
            Route::prefix('basic-infos')->controller(BasicInfoController::class)->group(function(){
                Route::get('','index')->name('basic-infos.index');
                Route::put('update/{id}','update')->name('basic-infos.update');
                Route::get('edit/{id?}','edit')->name('basic-infos.edit');
            });
            Route::prefix('admin')->group(function(){
                Route::prefix('roles')->controller(RoleController::class)->group(function(){
                    Route::get('','index')->name('roles.index');
                    Route::get('create','createOrEdit')->name('roles.create');
                    Route::get('edit/{id?}','createOrEdit')->name('roles.edit');
                    Route::post('store','store')->name('roles.store');
                    Route::put('update/{id}','update')->name('roles.update');
                    Route::delete('delete/{id}','destroy')->name('roles.destroy');
                    Route::get('all-roles','allRoles')->name('roles.all-roles');
                });
                Route::prefix('admins')->controller(AdminController::class)->group(function(){
                    Route::get('','index')->name('admins.index');
                    Route::get('create','createOrEdit')->name('admins.create');
                    Route::get('edit/{id?}','createOrEdit')->name('admins.edit');
                    Route::post('store','store')->name('admins.store');
                    Route::put('update/{id}','update')->name('admins.update');
                    Route::delete('delete/{id}','destroy')->name('admins.destroy');
                    Route::get('all-admins','allAdmins')->name('admins.all-admins');
                    Route::get('get-employees/{branch_id}','employeeList')->name('admins.employees-list');
                });
            });
            Route::prefix('password')->controller(AdminController::class)->group(function(){
                Route::match(['get', 'post'],'update/{id?}','updatePassword')->name('admin.password.update');
                Route::post('check-password','checkPassword')->name('admin.password.check');
            });
            Route::prefix('profile')->controller(AdminController::class)->group(function(){
                Route::match(['get', 'post'],'update-details/{id?}','updateDetails')->name('profile.update-details');;
            });
        });
    });
});

Route::namespace('App\Http\Controllers\frontend')->group(function(){
    Route::controller(HomeController::class)->group(function(){
        Route::get('/home',function(){
            return redirect()->route('admin.login');
        })->name('home.index');
    });
});


require __DIR__.'/auth.php';

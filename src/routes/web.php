<?php

Route::middleware(['web', 'auth', 'set-language'])
    ->namespace('LaravelEnso\Core\app\Http\Controllers')
    ->prefix('home')->as('home.')
    ->group(function () {
        Route::get('getTranslations', 'TranslationController')
            ->name('getTranslations');
    });

Route::middleware(['web', 'auth', 'core'])
    ->namespace('LaravelEnso\Core\app\Http\Controllers')
    ->group(function () {
        Route::prefix('core')->as('core.')
            ->group(function () {
                Route::prefix('preferences')->as('preferences.')
                    ->group(function () {
                        Route::patch('setPreferences/{route?}', 'PreferencesController@setPreferences')
                            ->name('setPreferences');
                        Route::post('resetToDefault/{route?}', 'PreferencesController@resetToDefault')
                            ->name('resetToDefault');
                    });
            });

        Route::prefix('administration')->as('administration.')
            ->group(function () {
                Route::namespace ('Owner')
                    ->prefix('owners')->as('owners.')
                    ->group(function () {
                        Route::get('initTable', 'OwnerTableController@initTable')
                            ->name('initTable');
                        Route::get('getTableData', 'OwnerTableController@getTableData')
                            ->name('getTableData');
                        Route::get('exportExcel', 'OwnerTableController@exportExcel')
                            ->name('exportExcel');

                        Route::get('getOptionsList', 'OwnerSelectController@getOptionsList')
                            ->name('getOptionsList');
                    });

                Route::resource('owners', 'Owner\OwnerController', ['except' => ['show']]);

                Route::namespace ('User')
                    ->prefix('users')->as('users.')
                    ->group(function () {
                        Route::get('initTable', 'UserTableController@initTable')
                            ->name('initTable');
                        Route::get('getTableData', 'UserTableController@getTableData')
                            ->name('getTableData');
                        Route::get('exportExcel', 'UserTableController@exportExcel')
                            ->name('exportExcel');
                        // Route::post('setTableData', 'UserTableController@setTableData')
                        //     ->name('setTableData');

                        Route::patch('updateProfile/{user}', 'ProfilePageController')
                            ->name('updateProfile');
                    });

                Route::resource('users', 'User\UserController');
            });
    });

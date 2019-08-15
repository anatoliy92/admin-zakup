<?php


Route::group(
    ['namespace' => 'Avl\AdminZakup\Controllers\Admin', 'middleware' => ['web', 'admin'], 'as' => 'adminzakup::'],
    function () {

        Route::group(
            ['namespace' => 'Ajax', 'prefix' => 'ajax'],
            function () {
                Route::post('/change-npa-date/{id}', 'NpaController@changeNpasDate');

                /* маршруты для работы с медиа */
                Route::post('zakup-images', 'MediaController@zakupImages');
                Route::post('zakup-files', 'MediaController@zakupFiles');
                Route::post('zakup-hide-images', 'MediaController@zakupHideImages');
                Route::post('zakup-hide-files', 'MediaController@zakupHideFiles');
                /* маршруты для работы с медиа */
            });

        Route::get('sections/{id}/zakup/move/{zakup}', 'TenderController@move')->name('sections.zakup.move');

        Route::post('sections/{id}/zakup/move/{zakup}', 'TenderController@moveSave')->name('sections.zakup.move.save');

        Route::resource('sections/{id}/zakup', 'TenderController', ['as' => 'sections']);
    });

Route::group(
    ['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localizationRedirect', 'web']],
    function () {
        Route::group(
            ['namespace' => 'Avl\AdminZakup\Controllers\Site'],
            function () {
                Route::get('zakup/registration', 'RegistrationController@index')->name('site.zakup.registration.index');
                Route::post('zakup/registration', 'RegistrationController@registration')->name('site.zakup.registration.registration');
            });
    });

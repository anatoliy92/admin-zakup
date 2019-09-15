<?php


Route::group(
    ['namespace' => 'Avl\AdminZakup\Controllers\Admin', 'middleware' => ['web', 'admin'], 'as' => 'adminzakup::'],
    function () {

        Route::group(
            ['namespace' => 'Ajax', 'prefix' => 'ajax'],
            function () {
                // Route::post('/change-npa-date/{id}', 'NpaController@changeNpasDate');

                /* маршруты для работы с медиа */
                Route::post('zakup-images', 'MediaController@zakupImages');
                Route::post('zakup-files', 'MediaController@zakupFiles');
                Route::post('zakup-hide-images', 'MediaController@zakupHideImages');
                Route::post('zakup-hide-files', 'MediaController@zakupHideFiles');
                /* маршруты для работы с медиа */
            });

        Route::get('sections/{id}/zakup/move/{zakup}', 'TenderController@move')->name('sections.zakup.move');

        Route::get('sections/{id}/zakup/{zakup_id}/contractors', 'TenderController@contractors')->name('sections.zakup.contractors');

        Route::post('sections/{id}/zakup/move/{zakup}', 'TenderController@moveSave')->name('sections.zakup.move.save');

        Route::resource('sections/{id}/zakup', 'TenderController', ['as' => 'sections']);

        Route::get('tender/contractor', 'ContractorController@index')->name('tender.contractor');
        Route::get('tender/contractor/{id}', 'ContractorController@show')->name('tender.contractor.show');
        Route::get('tender/contractor/{id}/block', 'ContractorController@block')->name('tender.contractor.block');
        Route::get('tender/contractor/{id}/unblock', 'ContractorController@unblock')->name('tender.contractor.unblock');
        Route::get('tender/contractor/{id}/remove', 'ContractorController@remove')->name('tender.contractor.remove');

    });

Route::group(
    ['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localizationRedirect', 'web']],
    function () {
        Route::group(
            ['namespace' => 'Avl\AdminZakup\Controllers\Site'],
            function () {
                Route::get('zakup/registration', 'RegistrationController@index')->name('site.zakup.registration.index');
                Route::post('zakup/registration', 'RegistrationController@registration')->name('site.zakup.registration.registration');
                Route::get('zakup/{alias}/', 'TenderController@index')->name('site.zakup.index');
                Route::get('zakup/{alias}/{id}', 'TenderController@show')->name('site.zakup.show')->where('id', '[0-9]+');
                Route::get('zakup/{alias}/{id}/confirm', 'TenderController@confirm')->name('site.zakup.confirm')->where('id', '[0-9]+');
                Route::get('zakup/{alias}/rubrics', 'TenderController@rubrics')->name('site.zakup.rubrics');
                Route::get('zakup/{alias}/rubrics/{rubric}', 'TenderController@rubricsShow')->name(
                    'site.zakup.rubrics.show')->where('rubric', '[0-9]+');
            });
    });

<?php
Route::get('{lang}/{param1?}/{param2?}/{param3?}', [
    'as'   => 'catalog',
    'uses' => 'CatalogController@viewСategory',
]);

Route::get('{all}', function ($all) {
    return redirect()->route('404', App::getLocale());
})->where(['all' => '.*']);
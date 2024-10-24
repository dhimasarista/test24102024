<?php
Route::get('/', function () {
    return redirect(route("l5-swagger.default.api"));
});
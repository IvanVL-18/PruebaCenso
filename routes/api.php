<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PermissionRoleController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\CensoController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\OccupationController;
use App\Http\Controllers\CatalogItemController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\IndexForCensoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuestionController;   
use App\Http\Controllers\AreaController; 

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//el grupo de rutas apiResource tiene las rutas index, show, store, update, destroy
//las rutas adicionales son para restaurar(restore(restaura un elemento borrado)), 
//eliminar definitivamente(force-delete)
// buscar por contenido(content)


Route::apiResource('units', UnitController::class);
Route::post('units/import', [UnitController::class, 'import'])->name('units.import');
Route::post('units/{id}/restore', [UnitController::class, 'restore'])->name('units.restore');
Route::delete('units/{id}/force-delete', [UnitController::class, 'forceDelete'])->name('units.force-delete');
Route::get('units/{content}/content', [UnitController::class, 'content'])->name('units.content');

Route::apiResource('roles', RoleController::class);
Route::post('roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
Route::delete('roles/{id}/force-delete', [RoleController::class, 'forceDelete'])->name('roles.force-delete');
Route::get('roles/{content}/content', [RoleController::class, 'content'])->name('roles.content');

Route::get('roles/{id}/get-permissions', [RoleController::class, 'get_permissions_by_role'])->name('roles.get-permissions');

Route::apiResource('permission-role', PermissionRoleController::class)->only(['show', 'store', 'update']);


Route::apiResource('sections', SectionController::class);
Route::post('sections/import', [SectionController::class, 'import'])->name('sections.import');
Route::post('sections/{id}/restore', [SectionController::class, 'restore'])->name('sections.restore');
Route::delete('sections/{id}/force-delete', [SectionController::class, 'forceDelete'])->name('sections.force-delete');
Route::get('sections/{content}/content', [SectionController::class, 'content'])->name('sections.content');

Route::apiResource('censos', CensoController::class);
Route::post('censos/test', [CensoController::class, 'test'])->name('censos.test');
Route::post('censos/{id}/restore', [CensoController::class, 'restore'])->name('censos.restore');
Route::delete('censos/{id}/force-delete', [CensoController::class, 'forceDelete'])->name('censos.force-delete');
Route::get('censos/{content}/content', [CensoController::class, 'content'])->name('censos.content');

Route::apiResource('catalogs', CatalogController::class);
Route::post('catalogs/{id}/restore', [CatalogController::class, 'restore'])->name('catalogs.restore');
Route::delete('catalogs/{id}/force-delete', [CatalogController::class, 'forceDelete'])->name('catalogs.force-delete');
Route::get('catalogs/{content}/content', [CatalogController::class, 'content'])->name('catalogs.content');
Route::put('catalogs/{id}', [CatalogController::class, 'update']);

Route::apiResource('institutions', InstitutionController::class);
Route::get('institutions/{id}/get-map', [InstitutionController::class, 'getMap'])->name('institutions.get-map');
Route::post('institutions/{id}/restore', [InstitutionController::class, 'restore'])->name('institutions.restore');
Route::delete('institutions/{id}/force-delete', [InstitutionController::class, 'forceDelete'])->name('institutions.force-delete');
Route::get('institutions/{content}/content', [InstitutionController::class, 'content'])->name('institutions.content'); 

Route::apiResource('users',UserController::class);
Route::post('users/import', [UserController::class, 'import'])->name('users.import');
Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
Route::post('users/content', [UserController::class, 'content'])->name('users.content');

Route::apiResource('permissions', PermissionController::class);
Route::post('permissions/{id}/restore', [PermissionController::class, 'restore'])->name('permissions.restore');
Route::delete('permissions/{id}/force-delete', [PermissionController::class, 'forceDelete'])->name('permissions.force-delete');
Route::get('permissions/{content}/content', [PermissionController::class, 'content'])->name('permissions.content');

Route::apiResource('occupations', OccupationController::class);
Route::post('occupations/{id}/restore', [OccupationController::class, 'restore'])->name('occupations.restore');
Route::delete('occupations/{id}/force-delete', [OccupationController::class, 'forceDelete'])->name('occupations.force-delete');
Route::get('occupations/{content}/content', [OccupationController::class, 'content'])->name('occupations.content');
 
Route::apiResource('index', IndexController::class);
Route::post('index/import', [IndexController::class, 'import'])->name('index.import');
Route::post('index/{id}/restore', [IndexController::class, 'restore'])->name('index.restore');
Route::delete('index/{id}/force-delete', [IndexController::class, 'forceDelete'])->name('index.force-delete');
Route::get('index/{content}/content', [IndexController::class, 'content'])->name('index.content');

Route::apiResource('indexs-for-censos', IndexForCensoController::class);
Route::post('indexs-for-censos/{id}/restore', [IndexForCensoController::class, 'restore'])->name('indexs-for-censos.restore');
Route::delete('indexs-for-censos/{id}/force-delete', [IndexForCensoController::class, 'forceDelete'])->name('indexs-for-censos.force-delete');
Route::get('indexs-for-censos/{content}/content', [IndexForCensoController::class, 'content'])->name('indexs-for-censos.content');

Route::apiResource('catalog-items', CatalogItemController::class);
Route::post('catalog-items/{id}/restore', [CatalogItemController::class, 'restore'])->name('catalog-items.restore');
Route::delete('catalog-items/{id}/force-delete', [CatalogItemController::class, 'forceDelete'])->name('catalog-items.force-delete');
Route::get('catalog-items/{content}/content', [CatalogItemController::class, 'content'])->name('catalog-items.content');
Route::get('count/{model}', [DashboardController::class, 'count'])->name('dashboard.count');
Route::get('institutions/centralized/count', [DashboardController::class, 'countCentralizedInstitutions'])->name('dashboard.countCentralizedInstitutions');


Route::apiResource('questions', QuestionController::class);
Route::post('questions/import', [QuestionController::class, 'import'])->name('questions.import');
Route::post('questions/{id}/restore', [QuestionController::class, 'restore'])->name('questions.restore');
Route::delete('questions/{id}/force-delete', [QuestionController::class, 'forceDelete'])->name('questions.force-delete');
Route::get('questions/{content}/content', [QuestionController::class, 'content'])->name('questions.content');

Route::apiResource('area', AreaController::class);
Route::post('area/{id}/restore', [AreaController::class, 'restore'])->name('area.restore');
Route::delete('area/{id}/force-delete', [AreaController::class, 'forceDelete'])->name('area.force-delete');
Route::get('area/{content}/content', [AreaController::class, 'content'])->name('area.content');




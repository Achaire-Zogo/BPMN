<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BPMNController;
use App\Http\Controllers\ProcessInstanceController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/bpmn', [BPMNController::class, 'index'])->name('bpmn.index');
Route::get('/bpmn/modeler/{id?}', [BPMNController::class, 'modeler'])->name('bpmn.modeler');
Route::post('/bpmn/save', [BPMNController::class, 'saveDiagram'])->name('bpmn.save');
Route::delete('/bpmn/delete/{id}', [BPMNController::class, 'deleteDiagram'])->name('bpmn.delete');

// Routes pour les instances de processus
Route::prefix('process')->group(function () {
    Route::get('/', [ProcessInstanceController::class, 'index'])->name('process.index');
    Route::get('/{id}', [ProcessInstanceController::class, 'show'])->name('process.show');
    Route::get('/start-sale/{diagramId}', [ProcessInstanceController::class, 'startSaleForm'])->name('process.start-sale');
    Route::post('/start/{diagramId}', [ProcessInstanceController::class, 'start'])->name('process.start');
    Route::post('/{instanceId}/execute', [ProcessInstanceController::class, 'executeTask'])->name('process.execute');
    Route::post('/{instanceId}/complete', [ProcessInstanceController::class, 'complete'])->name('process.complete');
});

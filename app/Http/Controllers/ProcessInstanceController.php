<?php

namespace App\Http\Controllers;

use App\Models\BPMNDiagram;
use App\Models\ProcessInstance;
use App\Models\Customer;
use App\Models\Product;
use App\Services\SaleProcessService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProcessInstanceController extends Controller
{
    protected $saleProcessService;

    public function __construct(SaleProcessService $saleProcessService)
    {
        $this->saleProcessService = $saleProcessService;
    }

    public function index()
    {
        $instances = ProcessInstance::with('diagram')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('process.index', compact('instances'));
    }

    public function startSaleForm($diagramId)
    {
        $diagram = BPMNDiagram::findOrFail($diagramId);
        $customers = Customer::all();
        $products = Product::all();

        return view('process.start-sale', compact('diagram', 'customers', 'products', 'diagramId'));
    }

    public function start($diagramId)
    {
        $diagram = BPMNDiagram::findOrFail($diagramId);
        
        // Créer une nouvelle instance de processus
        $instance = ProcessInstance::create([
            'bpmn_diagram_id' => $diagramId,
            'status' => 'running',
            'started_at' => Carbon::now(),
            'data' => [
                'sale_data' => request()->input('sale_data', [])
            ],
        ]);

        try {
            // Démarrer le processus de vente
            $this->saleProcessService->startSaleProcess($instance);

            return response()->json([
                'success' => true,
                'message' => 'Process started successfully',
                'instance' => $instance
            ]);
        } catch (\Exception $e) {
            $instance->update([
                'status' => 'failed',
                'data' => array_merge($instance->data, ['error' => $e->getMessage()])
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function executeTask(Request $request, $instanceId)
    {
        $instance = ProcessInstance::findOrFail($instanceId);
        
        try {
            switch ($instance->current_task) {
                case 'check_payment':
                    $this->saleProcessService->processPayment($instance);
                    break;
                    
                case 'generate_receipt':
                    $receiptData = $this->saleProcessService->generateReceipt($instance);
                    return response()->json([
                        'success' => true,
                        'message' => 'Receipt generated successfully',
                        'receipt' => $receiptData
                    ]);
                    break;

                default:
                    throw new \Exception('Unknown task: ' . $instance->current_task);
            }

            return response()->json([
                'success' => true,
                'message' => 'Task executed successfully',
                'instance' => $instance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $instance = ProcessInstance::with(['diagram'])->findOrFail($id);
        return view('process.show', compact('instance'));
    }

    public function complete($instanceId)
    {
        $instance = ProcessInstance::findOrFail($instanceId);
        
        $instance->update([
            'status' => 'completed',
            'completed_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Process completed successfully'
        ]);
    }
}

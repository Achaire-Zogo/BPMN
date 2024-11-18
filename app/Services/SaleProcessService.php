<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\ProcessInstance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SaleProcessService
{
    public function startSaleProcess(ProcessInstance $instance)
    {
        try {
            $saleData = $instance->data['sale_data'] ?? null;
            if (!$saleData) {
                throw new Exception('Données de vente manquantes');
            }

            DB::beginTransaction();

            // 1. Vérifier le stock
            $this->checkStock($saleData['items']);

            // 2. Créer la vente
            $sale = $this->createSale($saleData);

            // 3. Mettre à jour l'instance du processus
            $instance->update([
                'current_task' => 'check_payment',
                'data' => array_merge($instance->data, ['sale_id' => $sale->id])
            ]);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du démarrage du processus de vente: ' . $e->getMessage());
            throw $e;
        }
    }

    public function processPayment(ProcessInstance $instance)
    {
        try {
            DB::beginTransaction();

            $saleId = $instance->data['sale_id'];
            $sale = Sale::findOrFail($saleId);

            // Simuler le traitement du paiement
            $sale->update([
                'payment_status' => 'paid',
                'payment_date' => Carbon::now()
            ]);

            // Mettre à jour le stock
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                $product->decrement('stock', $item->quantity);
            }

            // Générer le numéro de reçu
            $sale->update([
                'receipt_number' => 'REC-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT)
            ]);

            // Mettre à jour l'instance du processus
            $instance->update([
                'current_task' => 'generate_receipt',
                'data' => array_merge($instance->data, ['payment_processed' => true])
            ]);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du traitement du paiement: ' . $e->getMessage());
            throw $e;
        }
    }

    public function generateReceipt(ProcessInstance $instance)
    {
        try {
            $saleId = $instance->data['sale_id'];
            $sale = Sale::with(['customer', 'items.product'])->findOrFail($saleId);

            // Générer le PDF du reçu (à implémenter avec une bibliothèque PDF)
            $receiptData = [
                'receipt_number' => $sale->receipt_number,
                'date' => $sale->payment_date->format('d/m/Y H:i'),
                'customer' => $sale->customer->name,
                'items' => $sale->items->map(function ($item) {
                    return [
                        'product' => $item->product->name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->subtotal
                    ];
                }),
                'total' => $sale->total_amount
            ];

            // Mettre à jour le statut de la vente
            $sale->update(['status' => 'completed']);

            // Terminer le processus
            $instance->update([
                'status' => 'completed',
                'completed_at' => Carbon::now(),
                'data' => array_merge($instance->data, ['receipt_generated' => true])
            ]);

            return $receiptData;
        } catch (Exception $e) {
            Log::error('Erreur lors de la génération du reçu: ' . $e->getMessage());
            throw $e;
        }
    }

    private function checkStock($items)
    {
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || $product->stock < $item['quantity']) {
                throw new Exception("Stock insuffisant pour le produit {$product->name}");
            }
        }
    }

    private function createSale($saleData)
    {
        $sale = Sale::create([
            'customer_id' => $saleData['customer_id'],
            'total_amount' => collect($saleData['items'])->sum(function ($item) {
                $product = Product::find($item['product_id']);
                return $product->price * $item['quantity'];
            }),
            'status' => 'processing',
            'payment_status' => 'pending'
        ]);

        foreach ($saleData['items'] as $item) {
            $product = Product::find($item['product_id']);
            $sale->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'subtotal' => $product->price * $item['quantity']
            ]);
        }

        return $sale;
    }
}

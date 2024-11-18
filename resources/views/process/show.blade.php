@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Détails du Processus #{{ $instance->id }}
            </h1>
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                @if($instance->status === 'completed') 
                    bg-green-100 text-green-800
                @elseif($instance->status === 'in_progress')
                    bg-blue-100 text-blue-800
                @else
                    bg-gray-100 text-gray-800
                @endif">
                {{ ucfirst($instance->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Informations du Processus</h2>
                <dl class="grid grid-cols-1 gap-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Diagramme</dt>
                        <dd class="text-sm text-gray-900">{{ $instance->diagram->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                        <dd class="text-sm text-gray-900">{{ $instance->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dernière mise à jour</dt>
                        <dd class="text-sm text-gray-900">{{ $instance->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            @if($instance->data && isset($instance->data['sale']))
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Détails de la Vente</h2>
                <dl class="grid grid-cols-1 gap-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Client</dt>
                        <dd class="text-sm text-gray-900">{{ $instance->data['sale']['customer_name'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total</dt>
                        <dd class="text-sm text-gray-900">{{ number_format($instance->data['sale']['total'], 2) }} €</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nombre d'articles</dt>
                        <dd class="text-sm text-gray-900">{{ count($instance->data['sale']['items']) }}</dd>
                    </div>
                </dl>
            </div>
            @endif
        </div>

        @if($instance->data && isset($instance->data['sale']['items']))
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Articles</h2>
            <div class="bg-white shadow overflow-hidden rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produit
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantité
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prix unitaire
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($instance->data['sale']['items'] as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item['product_name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item['quantity'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($item['unit_price'], 2) }} €
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($item['quantity'] * $item['unit_price'], 2) }} €
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($instance->status !== 'completed')
        <div class="flex justify-end space-x-4">
            <form action="{{ route('process.execute', ['instanceId' => $instance->id]) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                    Exécuter l'étape suivante
                </button>
            </form>
            
            <form action="{{ route('process.complete', ['instanceId' => $instance->id]) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">
                    Terminer le processus
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection

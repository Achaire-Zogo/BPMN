<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nouvelle Vente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col">
                <h2>Nouvelle Vente</h2>
            </div>
            <div class="col text-end">
                <a href="{{ route('bpmn.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="saleForm" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="customer" class="form-label">Client</label>
                        <select class="form-select" id="customer" required>
                            <option value="">Sélectionner un client</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="products-container">
                        <h4>Produits</h4>
                        <div class="product-item mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Produit</label>
                                    <select class="form-select product-select" required>
                                        <option value="">Sélectionner un produit</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                    data-price="{{ $product->price }}"
                                                    data-stock="{{ $product->stock }}">
                                                {{ $product->name }} (Stock: {{ $product->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Quantité</label>
                                    <input type="number" class="form-control quantity-input" min="1" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-danger d-block remove-product">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-product" class="btn btn-secondary mb-3">
                        <i class="fas fa-plus"></i> Ajouter un produit
                    </button>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Démarrer la vente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('saleForm');
            const addProductBtn = document.getElementById('add-product');
            const productsContainer = document.getElementById('products-container');
            const firstProductItem = productsContainer.querySelector('.product-item');

            // Ajouter un nouveau produit
            addProductBtn.addEventListener('click', function() {
                const newItem = firstProductItem.cloneNode(true);
                newItem.querySelector('.product-select').value = '';
                newItem.querySelector('.quantity-input').value = '';
                productsContainer.appendChild(newItem);

                // Ajouter les événements aux nouveaux éléments
                addProductItemEvents(newItem);
            });

            // Ajouter les événements au premier élément
            addProductItemEvents(firstProductItem);

            // Gérer la soumission du formulaire
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const customerId = document.getElementById('customer').value;
                const items = [];

                // Collecter les données des produits
                document.querySelectorAll('.product-item').forEach(item => {
                    const productSelect = item.querySelector('.product-select');
                    const quantityInput = item.querySelector('.quantity-input');
                    
                    if (productSelect.value && quantityInput.value) {
                        items.push({
                            product_id: parseInt(productSelect.value),
                            quantity: parseInt(quantityInput.value)
                        });
                    }
                });

                // Préparer les données
                const saleData = {
                    sale_data: {
                        customer_id: parseInt(customerId),
                        items: items
                    }
                };

                try {
                    const response = await fetch('/process/start/{{ $diagramId }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(saleData)
                    });

                    const result = await response.json();
                    if (result.success) {
                        alert('Vente démarrée avec succès !');
                        window.location.href = '{{ route('process.index') }}';
                    } else {
                        alert(result.message || 'Erreur lors du démarrage de la vente');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Erreur lors du démarrage de la vente');
                }
            });

            function addProductItemEvents(item) {
                // Gérer la suppression
                item.querySelector('.remove-product').addEventListener('click', function() {
                    if (document.querySelectorAll('.product-item').length > 1) {
                        item.remove();
                    }
                });

                // Vérifier le stock disponible
                const quantityInput = item.querySelector('.quantity-input');
                const productSelect = item.querySelector('.product-select');

                quantityInput.addEventListener('change', function() {
                    const option = productSelect.selectedOptions[0];
                    if (option) {
                        const stock = parseInt(option.dataset.stock);
                        if (parseInt(this.value) > stock) {
                            alert('Quantité non disponible en stock !');
                            this.value = stock;
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>

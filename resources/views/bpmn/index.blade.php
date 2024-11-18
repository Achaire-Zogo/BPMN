<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BPMN Diagrams</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Mes Diagrammes BPMN</h1>
            <a href="{{ route('bpmn.modeler') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Nouveau Diagramme
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($diagrams as $diagram)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-800">{{ $diagram->name }}</h2>
                            <span class="text-sm text-gray-500">
                                {{ $diagram->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-sm text-gray-500">
                                Modifié le {{ $diagram->updated_at->format('d/m/Y H:i') }}
                            </span>
                            <div class="flex space-x-2">
                                <a href="{{ route('bpmn.modeler', ['id' => $diagram->id]) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition duration-300">
                                    <i class="fas fa-edit text-lg"></i>
                                </a>
                                <a href="{{ route('process.start-sale', ['diagramId' => $diagram->id]) }}" 
                                   class="text-green-600 hover:text-green-800 transition duration-300">
                                    <i class="fas fa-shopping-cart text-lg"></i>
                                </a>
                                <button class="btn btn-sm btn-success start-process" data-id="{{ $diagram->id }}">
                                    <i class="fas fa-play"></i>
                                </button>
                                <button onclick="deleteDiagram({{ $diagram->id }})" 
                                        class="text-red-600 hover:text-red-800 transition duration-300">
                                    <i class="fas fa-trash text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3">
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <i class="fas fa-diagram-project text-gray-400 text-5xl mb-4"></i>
                        <p class="text-gray-600 text-lg">Aucun diagramme n'a été créé pour le moment.</p>
                        <a href="{{ route('bpmn.modeler') }}" 
                           class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                            Créer mon premier diagramme
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function deleteDiagram(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce diagramme ?')) {
                fetch(`/bpmn/delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue lors de la suppression');
                });
            }
        }

        // Gestionnaire pour le démarrage d'un processus
        document.querySelectorAll('.start-process').forEach(button => {
            button.addEventListener('click', async function() {
                const diagramId = this.dataset.id;
                if (confirm('Voulez-vous démarrer ce processus ?')) {
                    try {
                        const response = await fetch(`/process/start/${diagramId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        const result = await response.json();
                        if (result.success) {
                            alert('Processus démarré avec succès !');
                            window.location.href = '{{ route('process.index') }}';
                        }
                    } catch (error) {
                        console.error('Error starting process:', error);
                        alert('Erreur lors du démarrage du processus');
                    }
                }
            });
        });
    </script>
</body>
</html>

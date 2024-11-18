<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Process Instances</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col">
                <h2>Instances de processus</h2>
            </div>
            <div class="col text-end">
                <a href="{{ route('bpmn.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Retour aux diagrammes
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Diagramme</th>
                            <th>Statut</th>
                            <th>Tâche actuelle</th>
                            <th>Démarré le</th>
                            <th>Terminé le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($instances as $instance)
                        <tr>
                            <td>{{ $instance->id }}</td>
                            <td>{{ $instance->diagram->name }}</td>
                            <td>
                                <span class="badge bg-{{ $instance->status === 'completed' ? 'success' : ($instance->status === 'running' ? 'primary' : 'secondary') }}">
                                    {{ $instance->status }}
                                </span>
                            </td>
                            <td>{{ $instance->current_task ?? '-' }}</td>
                            <td>{{ $instance->started_at ? $instance->started_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $instance->completed_at ? $instance->completed_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                <a href="{{ route('process.show', $instance->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

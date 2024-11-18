<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BPMN Modeler</title>

    <!-- BPMN.js dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    

    <link rel="stylesheet" href="https://unpkg.com/bpmn-js@14.0.0/dist/assets/bpmn-js.css">
    <link rel="stylesheet" href="https://unpkg.com/bpmn-js@14.0.0/dist/assets/diagram-js.css">
    <link rel="stylesheet" href="https://unpkg.com/bpmn-js@14.0.0/dist/assets/bpmn-font/css/bpmn.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #canvas {
            height: 100%;
        }
        
        .button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Diagramme</h3>
                    </div>
                    <div class="card-body" style="height: 800px">
                        <div id="canvas"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="buttons">
                            <button class="button" id="save-button">Enregistrer</button>
                        </div>
                        <div class="mt-3">
                            <label for="diagram-name" class="form-label">Nom du diagramme</label>
                            <input type="text" id="diagram-name" class="form-control" placeholder="Nom du diagramme">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/bpmn-js@14.0.0/dist/bpmn-modeler.development.js"></script>
    <script>
        // Initialize the BPMN modeler
        var bpmnModeler = new BpmnJS({
            container: '#canvas'
        });

        // Create a new diagram
        async function createNewDiagram() {
            try {
                await bpmnModeler.createDiagram();
            } catch (err) {
                console.error('Error creating diagram', err);
            }
        }

        // Save diagram
        document.getElementById('save-button').addEventListener('click', async function() {
            const name = document.getElementById('diagram-name').value;
            if (!name) {
                showNotification('Veuillez entrer un nom pour le diagramme', 'error');
                return;
            }

            try {
                const { xml } = await bpmnModeler.saveXML({ format: true });
                
                const response = await fetch('{{ route('bpmn.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        xml: xml,
                        name: name,
                        id: {{ isset($diagram) ? $diagram->id : 'null' }}
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                if (result.success) {
                    showNotification('Diagramme sauvegardé avec succès');
                    setTimeout(() => {
                        window.location.href = '{{ route('bpmn.index') }}';
                    }, 1500);
                } else {
                    throw new Error('Erreur lors de la sauvegarde');
                }
            } catch (err) {
                console.error('Error saving diagram:', err);
                
                showNotification('Erreur lors de la sauvegarde du diagramme', 'error');
            }
        });

        function showNotification(message, type = 'success') {
                    const notification = document.createElement('div');
                    notification.classList.add('notification', `notification-${type}`);
                    notification.textContent = message;
                    document.body.appendChild(notification);
                    setTimeout(() => notification.remove(), 3000);
                }
        // Create new diagram on page load
        createNewDiagram();
    </script>
</body>
</html>

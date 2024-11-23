<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Response</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            max-height: 500px;
            overflow-y: auto;
        }
        .json-key { color: #881391; }
        .json-string { color: #268bd2; }
        .json-number { color: #859900; }
        .json-boolean { color: #cb4b16; }
        .json-null { color: #93a1a1; }
        .copy-button {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .response-container {
            position: relative;
            margin: 20px 0;
        }
        .status-success {
            color: #198754;
            border-left: 4px solid #198754;
        }
        .status-error {
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">API Response</h5>
                        <span class="badge {{ $success ? 'bg-success' : 'bg-danger' }}">
                            Status: {{ $success ? 'Success' : 'Error' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="response-container">
                            <button class="btn btn-sm btn-outline-secondary copy-button" onclick="copyToClipboard()">
                                Copy JSON
                            </button>
                            <pre id="json-display" class="{{ $success ? 'status-success' : 'status-error' }}"></pre>
                        </div>
                        
                        @if(isset($message))
                        <div class="alert {{ $success ? 'alert-success' : 'alert-danger' }} mt-3">
                            {{ $message }}
                        </div>
                        @endif
                        
                        <div class="mt-3">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
                            @if($success)
                                <a href="{{ route('nurseadmin.scheduleList') }}" class="btn btn-primary">View Schedules</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Format and display JSON
    const jsonData = @json($data);
    document.getElementById('json-display').textContent = 
        JSON.stringify(jsonData, null, 2);

    // Syntax highlighting
    function syntaxHighlight(json) {
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
            let cls = 'json-number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'json-key';
                } else {
                    cls = 'json-string';
                }
            } else if (/true|false/.test(match)) {
                cls = 'json-boolean';
            } else if (/null/.test(match)) {
                cls = 'json-null';
            }
            return '<span class="' + cls + '">' + match + '</span>';
        });
    }

    // Apply syntax highlighting
    const pre = document.getElementById('json-display');
    pre.innerHTML = syntaxHighlight(pre.textContent);

    // Copy to clipboard function
    function copyToClipboard() {
        const jsonText = JSON.stringify(@json($data), null, 2);
        navigator.clipboard.writeText(jsonText).then(() => {
            const button = document.querySelector('.copy-button');
            button.textContent = 'Copied!';
            button.classList.add('btn-success');
            setTimeout(() => {
                button.textContent = 'Copy JSON';
                button.classList.remove('btn-success');
            }, 2000);
        });
    }
    </script>
</body>
</html>
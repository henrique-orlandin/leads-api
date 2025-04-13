<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to CodeIgniter 4!</title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    
    <script>
        $(document).ready(function() {
            $('#leads-form').on('submit', function(e) {
                e.preventDefault();
                $('#error_message').text('');
                $('#to_import').text('0');
                $('#imported').text('0');
                $('#failed').text('0');

                const leads = JSON.parse($('#json').val());
                const token = $('#token').val();
                let toImportCount = leads.length;
                let importedCount = 0;
                let failedCount = 0;

                $('#to_import').text(toImportCount);
                let errorMessages = [];

                for (const lead of leads) {
                    $.ajax({
                        url: $(this).attr('action'),
                        type: $(this).attr('method'),
                        data: JSON.stringify(lead),
                        async: true,
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', `Bearer ${token}`);
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                importedCount++;
                                toImportCount--;

                                $('#imported').text(importedCount);
                            } else {
                                failedCount++;
                                toImportCount--;
                                
                                $('#failed').text(failedCount);
                                errorMessages.push(response.message);
                            }
                            $('#to_import').text(toImportCount);
                        },
                        error: function(xhr, status, error) {
                            failedCount++;
                            toImportCount--;
                            $('#to_import').text(toImportCount);
                            $('#failed').text(failedCount);
                            const response = JSON.parse(xhr.responseText);
                            if (response && response.messages) {
                                for (const property in response.messages) {
                                    errorMessages.push(`${property}: ${response.messages[property]}`);
                                }
                            } else {
                                errorMessages.push('An unexpected error occurred.');
                            }
                        }
                    });
                }
                const messages = errorMessages.join('<br/>');
                $('#error_message').html(messages);
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Leads API import test</h2>
        <form class="form" id="leads-form" method="post" action="/leads">
            <div class="form-group mb-3">
                <label for="token">API Token</label>
                <input type="text" class="form-control" id="token" name="token" required>
            </div>
            <div class="form-group mb-3">
                <label for="json">JSON data:</label>
                <textarea class="form-control" id="json" name="json" rows="12" required></textarea>
            </div>
            <div class="row">
                <div class="col-3">To import <span id="to_import">0</span></div>
                <div class="col-3">Imported <span id="imported">0</span></div>
                <div class="col-3">Failed <span id="failed">0</span></div>
                <div class="col-3 text-end">
                    <button type="submit" class="btn btn-primary">Import Leads</button>
                </div>
            </div>
        </form>
        <div class="ro">
            <p class="text-danger" id="error_message"></p>
        </div>
    </div>    

</body>
</html>

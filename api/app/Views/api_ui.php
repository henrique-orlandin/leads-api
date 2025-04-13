<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title; ?></title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="https://truepathleads.com/wp-content/uploads/2025/02/favicon-4.jpg">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

    <!-- bootstrap table https://bootstrap-table.com/ -->
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.css">
    <script src="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>

    <!-- fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <style>
        .logo {
            width: 90px;
            height: 90px;
            margin: 0 auto;
            background-color: #2169AF;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .action {
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .action:hover {
            color: #2169AF;
        }
    </style>

    <script>
        let token = null;
        $(document).ready(function() {
            $('#leads-loader').show();
            $.ajax({
                url: '/token',
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    if (response.token) {
                        token = response.token;
                        loadLeadsList();
                    } else {
                        $('#error_message').text('Failed to retrieve token.').show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#error_message').text('Failed to retrieve token.').show();
                }
            });

            $('#leads-form').on('submit', function(e) {
                e.preventDefault();
                $('#error_message').text('').hide();
                $('#to_import').text('0');
                $('#imported').text('0');
                $('#failed').text('0');
                
                let leads = [];
                try {
                    leads = JSON.parse($('#json').val());
                } catch (e) {
                    $('#error_message').text('Invalid JSON format.').show();
                    return;
                }
                
                $('#leads-form button').prepend('<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>');
                $('#leads-form button').prop('disabled', true);
                
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
                        },
                        complete: function() {
                            if (toImportCount === 0) {
                                $('#leads-form button').removeAttr('disabled').find('.spinner-border').remove();
                                loadLeadsList();
                                if (errorMessages.length > 0) {
                                    errorMessages.unshift('Some leads failed to import:');
                                    $('#error_message').html(errorMessages.join('<br/>')).show();
                                    setTimeout(function() {
                                        $('#error_message').fadeOut();
                                    }, 5000);
                                }
                            }
                        }
                    });
                }
            });
        });

        function loadLeadsList() {
            $.ajax({
                url: '/leads',
                type: 'get',
                dataType: 'json',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', `Bearer ${token}`);
                },
                success: function(response) {
                    if (!response.error) {
                        $('#leads-table').bootstrapTable("destroy");

                        if (response.length === 0) {
                            $('#leads-loader').html('No leads found.');
                            return;
                        } else {
                            $('#leads-loader').hide();
                        }

                        $('#leads-table').bootstrapTable({
                            sidePagination: "client",
                            detailView: false,
                            cookiesEnabled: ['bs.table.sortOrder', 'bs.table.sortName', 'bs.table.pageList', 'bs.table.columns', 'bs.table.searchText', 'bs.table.filterControl'],
                            pagination: true,
                            columns: [
                                {
                                    field: 'id',
                                    title: 'ID',
                                    visible: false,
                                    sortable: true
                                }, {
                                    field: 'created_at',
                                    title: 'Created At',
                                    visible: true,
                                    sortable: true,
                                    formatter: (value, row, index) => {
                                        console.log(value);
                                        const date = new Date(value);
                                        let month = date.getMonth() + 1;
                                        let day = date.getDate();
                                        month = month <= 9 ? month.toString().padStart(2, '0') : month;
                                        day = day <= 9 ? day.toString().padStart(2, '0') : day;
                                        return `${month}/${day}/${date.getFullYear()}`;
                                    }
                                }, 
                                {
                                    field: 'first_name',
                                    title: 'First Name',
                                    sortable: true,
                                }, 
                                {
                                    field: 'last_name',
                                    title: 'Last Name',
                                    sortable: true,
                                }, 
                                {
                                    field: 'email',
                                    title: 'Email',
                                    sortable: true,
                                },
                                {
                                    field: 'phone',
                                    title: 'Phone',
                                    sortable: true,
                                },
                                {
                                    field: 'birthdate',
                                    title: 'Birthdate',
                                    sortable: true,
                                    formatter: (value, row, index) => {
                                        if (value) {
                                            const date = new Date(value);
                                            let month = date.getMonth() + 1;
                                            let day = date.getDate();
                                            month = month <= 9 ? month.toString().padStart(2, '0') : month;
                                            day = day <= 9 ? day.toString().padStart(2, '0') : day;
                                            return `${month}/${day}/${date.getFullYear()}`;
                                        }
                                        return '-';
                                    }
                                },
                                {
                                    field: 'extra',
                                    title: 'Extra',
                                    width: '300px',
                                    sortable: false,
                                    formatter: (value, row, index) => {
                                        if (value) {
                                            const extra = JSON.parse(value);
                                            return Object.entries(extra).map(([key, val]) => `${key}: ${val}`).join('<br/>');
                                        }
                                        return '-';
                                    }
                                },
                                {
                                    field: 'action',
                                    title: 'Actions',
                                    align: 'center',
                                    formatter: (value, row, index) => {
                                        return `<a class="action" href="javascript: deleteLeadModal(${row.id});" title="Delete Lead">
                                                    <i class="fa fa-trash"></i>
                                                </a>`;
                                    }
                                }
                            ],
                            data: response,
                        });
                    } else {
                        $('#error_message').text('Failed to load leads list.').show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#error_message').text('Failed to load leads list.').show();
                }
            });
        }

        function deleteLeadModal(id) {
            $('#delete-lead-modal').modal('show');
            $('#delete-lead-modal .btn-confirm').off('click').on('click', function() {
                deleteLead(id);
            });
        }

        function deleteLead(id) {
            $('#delete-lead-modal .btn-confirm').prepend('<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>');
            $('#delete-lead-modal .btn-confirm').prop('disabled', true);
            
            $.ajax({
                url: `/leads/${id}`,
                type: 'delete',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', `Bearer ${token}`);
                },
                success: function(response) {
                    if (!response.error) {
                        loadLeadsList();
                        $('#delete-lead-modal .btn-confirm').removeAttr('disabled').find('.spinner-border').remove();
                        $('#delete-lead-modal').modal('hide');
                    } else {
                        $('#error_message').text(response.message).show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#error_message').text('Failed to delete lead.').show();
                }
            });
        }
    </script>
</head>
<body class="bg-light">
    <div class="py-5 text-center">
        <div class="rounded logo mb-4">
            <img src="https://truepathleads.com/wp-content/uploads/2025/02/logo-2-1-1-807x1024.png" alt="" width="72" height="72">
        </div>
        <h2>Leads API UI</h2>
        <p class="lead">This is a simple UI for leads management. All the actions use the Leads API.</p>
    </div>

    <div class="container">
        <div class="accordion" id="accordionExample">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <h5>Import Leads</h5>
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <form class="form" id="leads-form" method="post" action="/leads">
                            <div class="mb-3">
                                <label class="mb-2" for="json"><strong>Import JSON data:</strong></label>
                                <textarea class="form-control" id="json" name="json" rows="5" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-3"><p class="badge bg-primary-subtle border border-primary-subtle text-primary-emphasis rounded-pill">To import <span id="to_import">0</span></p></div>
                                <div class="col-3"><p class="badge bg-success-subtle border border-success-subtle text-success-emphasis rounded-pill">Imported <span id="imported">0</span></p></div>
                                <div class="col-3"><p class="badge bg-danger-subtle border border-danger-subtle text-danger-emphasis rounded-pill">Failed <span id="failed">0</span></p></div>
                                <div class="col-3 text-end">
                                    <button class="btn btn-primary" type="submit">Import Leads</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-danger mt-3" id="error_message" style="display: none;"></div>
        <div class="mt-4 mb-3 text-center" id="leads-loader" style="display: none;">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div class="row">
            <table 
                id="leads-table" 
                data-toolbar="#toolbar" 
                data-search="true" 
                data-advanced-search="false" 
                data-id-table="advancedTable" 
                data-cookie="true" 
                data-detail-formatter="detailFormatter" 
                data-row-style="rowStyle" 
                data-minimum-count-columns="2" 
                data-show-pagination-switch="false" 
                data-id-field="id" 
                data-page-size="100" 
                data-page-list="[100, 250, 500, 1000]" 
                data-show-footer="false" 
                data-unique-id="id" 
                data-response-handler="responseHandler">
            </table>  
        </div>
    </div>    

    <div id="delete-lead-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this lead?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-confirm">Proceed</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
            
    <div class="modal" id="delete-lead-modal">

    </div>
</body>
</html>

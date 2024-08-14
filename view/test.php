<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataTables Parent-Child Example</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <style>
        table.dataTable tbody tr.child {
            background-color: #f9f9f9;
        }
        .child-table {
            width: 100%;
        }
        .child-table th, .child-table td {
            padding: 8px;
            text-align: left;
        }
        .shown .details-btn::before {
            content: "-";
            font-weight: bold;
            margin-right: 5px;
        }
        .details-btn::before {
            content: "+";
            font-weight: bold;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <table id="example" class="display">
        <thead>
            <tr>
                <th>Parent ID</th>
                <th>Parent Name</th>
                <th>Details</th>
            </tr>
        </thead>
    </table>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {

            var parentData = [
                { "id": 1, "name": "Parent 1" },
                { "id": 2, "name": "Parent 2" },
                { "id": 3, "name": "Parent 3" }
            ];

            var childData = {
                "1": [
                    { "id": 101, "name": "Child 1-1", "description": "Description for Child 1-1" },
                    { "id": 102, "name": "Child 1-2", "description": "Description for Child 1-2" }
                ],
                "2": [
                    { "id": 201, "name": "Child 2-1", "description": "Description for Child 2-1" },
                    { "id": 202, "name": "Child 2-2", "description": "Description for Child 2-2" }
                ],
                "3": [
                    { "id": 301, "name": "Child 3-1", "description": "Description for Child 3-1" },
                    { "id": 302, "name": "Child 3-2", "description": "Description for Child 3-2" }
                ]
            };

            var table = $('#example').DataTable({
                data: parentData,
                columns: [
                    { "data": "id" },
                    { "data": "name" },
                    {
                        "data": null,
                        "defaultContent": "<button class='details-btn'>Details</button>"
                    }
                ],
                "drawCallback": function() {
                    // Ensure DataTables instance is available and properly referenced
                    var api = this.api();
                    console.log(api.rows());
                    api.rows().every(function() {
                        var row = this;
                        var parentId = row.data().id;
                        console.log("parentId",);
                        var data = childData[parentId];
                        if (data) {
                            if (!row.child.isShown()) {
                                row.child(format(data)).show();
                            }
                            $(row.node()).addClass('shown');
                        }
                    });
                }
            });

            $('#example').on('click', 'button.details-btn', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    var parentId = row.data().id;
                    var data = childData[parentId];
                    row.child(format(data)).show();
                    tr.addClass('shown');
                }
            });

            function format(data) {
                return `
                    <table class="child-table">
                        <thead>
                            <tr>
                                <th>Child ID</th>
                                <th>Child Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.map(child => `
                                <tr>
                                    <td>${child.id}</td>
                                    <td>${child.name}</td>
                                    <td>${child.description}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
            }
        });
    </script>
</body>
</html>

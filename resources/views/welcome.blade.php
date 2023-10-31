<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="container mt-3">
        <h3>Todos</h3>
        <p>Click on the button to open the modal.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" id="add_todo">
            Add Todo
        </button>
        <table class="table table-bordered">
            <thead>
                <th>Serial No.</th>
                <th>Name</th>
                <th>Action</th>
            </thead>
            <tbody id="list_todo">
                @foreach ($todos as $todo)
                    <tr id="row_todo{{ $todo->id }}">
                        <td>{{ $todo->id }}</td>
                        <td id="todo_name">{{ $todo->name }}</td>
                        <td>
                            <button type="button" id="edit_todo" data-id="{{ $todo->id }}"
                                class="btn btn-sm btn-info ml-1">Edit</button>
                            <button type="button" id="delete_todo" data-id="{{ $todo->id }}"
                                class="btn btn-sm btn-danger ml-1">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- The Modal -->
    <div class="modal" id="modal_todo">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form_todo">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal_title"></h4>
                        {{-- <button type="button" class="btn btn-danger" data-dismiss="modal">&times;</button> --}}
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <input type="text" name="name" id="name_todo" class="form-control"
                            placeholder="Enter todo...">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"
                            id="close_button">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'x-csrf-token': $('meta[name="csrf-token"]').attr('content')
                }
            })
        });

        $("#add_todo").on('click', function() {                                                                                                 
            $("#form_todo").trigger('reset');
            $('#modal_title').html("Add Todo");
            $("#modal_todo").modal('show');
        });

        $("#close_button").on('click', function() {
            $("#modal_todo").modal('hide');
        });

        // Edit Todo
        $("body").on('click', '#edit_todo', function() {
            var id = $(this).data('id');
            $.get('todos/' + id + '/edit',
                function(res) {
                    $("modal_title").html('Edit Todo');
                    $("#id").val(res.id);
                    $("#name_todo").val(res.name);
                    $("#modal_todo").modal('show');
                });
        });

        // Delete Todo
        $("body").on('click', '#delete_todo', function() {
            var id = $(this).data('id');
            confirm('Are you sure want to delete?');

            $.ajax({
                type: 'DELETE',
                url: 'todos/destroy/' + id
            }).done(function(response){
                $("#row_todo" + id).remove();
            });
        });

        // Save Data
        $("form").on('submit',function(e){
            e.preventDefault();
            $.ajax({
                url:"todos/store",
                data: $("#form_todo").serialize(),
                type: 'POST'
            }).done(function(response){
                var row = '<tr id ="row_todo'+ response.id +'">';
                row += '<td>' + response.id + '</td>';
                row += '<td>' + response.name + '</td>';
                row += '<td>' + '<button type="button" id="edit_todo" data-id="' +response.id+'" class="btn btn-info btn-sm">Edit</button>' + '<button type="button" id="delete_todo" data-id="' +response.id+'" class="btn btn-danger btn-sm">Delete</button>' 
                if($("#id").val()){
                    $("#row_todo" + response.id).replaceWith(row);
                }else{
                    $("#list_todo").prepend(row);
                }

                $("#form_todo").trigger('reset');
                $("#modal_todo").modal('hide');
            });
        });

    </script>
</body>

</html>
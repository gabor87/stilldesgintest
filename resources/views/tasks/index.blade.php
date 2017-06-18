@extends((Request::ajax() ? 'layouts.ajax' : 'layouts.app'))

@section('content')

<div id="container-tasks" class="container">
    <div id="container-tasks-search" class="row">
        <div class="pull-left">
            <button href="{{ url('/tasks/create') }}" class="btn btn-small btn-primary btn-create">Create new</button>
        </div>
        <div class="col-xs-3">
            <input class="form-control" placeholder="Keyword..." />
        </div>
        <div class="col-xs-3">
            <label>
                <input type="checkbox" name="hidedone" value="1"@if ($hideDone)checked="checked" @endif /> Hide done
            </label>
        </div>
    </div>
    <hr>
    <div id="container-tasks-list" data-url="{{ url('/tasks') }}" class="row">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Title</td>
                    <td>Description</td>
                    <td>Status</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                @if ($tasks->isEmpty() && !$searching)
                    <tr><td colspan="5">No tasks.</td></tr>
                @else
                    @foreach($tasks as $key => $value)
                        <tr>
                            <td>{{ $value->id }}</td>
                            <td>{{ $value->title }}</td>
                            <td>{{ $value->description }}</td>
                            <td>{{ $taskStatuses[$value->done] }}</td>

                            <td>
                                <button class="btn btn-small btn-info btn-edit" href="{{ URL::to('tasks/' . $value->id . '/update') }}">Edit</button>
                                <button class="btn btn-small btn-warning btn-delete" href="{{ URL::to('tasks/' . $value->id . '/delete') }}">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="__modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                
            </div>
        </div>

    </div>
</div>

<script>
    $(function () {
        var containerTasks = $('#container-tasks');
        var containerTasksList = $('#container-tasks-list');

        var load = function (url, params) {
            var inputs = containerTasks.find(':input').prop('disabled', true);
            
            return $.get(url, params, function (response) {
                containerTasksList.html(
                    $('<div>').html(response).find('#container-tasks-list').html()
                );
        
                inputs.prop('disabled', false);
            });
        },
        refresh = function () {
            return load(containerTasksList.attr('data-url'));
        };

        $('#container-tasks-search input').on('keyup', function () {
            var _this = $(this), xhr = null;

            if (xhr) {
                xhr.abort();
            }

            clearTimeout(_this.data('timeout'));
            _this.data('timeout', setTimeout(function () {
                xhr = load(containerTasksList.attr('data-url'), {
                    keyword: _this.val()
                });
            }), 300);
        });

        containerTasksList.on('click', '.btn-delete', function (event) {
            event.preventDefault();

            var _this = $(this);
            
            if (confirm('Are you sure?')) {
                load(_this.attr('href')).done(function (json) {
                    if (json.success) {
                        refresh();
                    } else {
                        alert('Internal server error, please try it later.');
                    }
                });
            }

            return false;
        });
        
        var modal = jQuery('#__modal');
        
        modal.on('click', '.btn-save', function (event) {
            event.preventDefault();
            
            var _this = $(this),
            form = modal.find('form');
            
            if (_this.prop('disabled')) {
                return;
            }
            _this.prop('disabled', true);
            
            $.post(form.attr('action'), form.serialize(), function (response) {
                modal.processResponse(response);
                _this.prop('disabled', false);
            });
            
            return false;
        });
        
        modal.on('hidden.bs.modal', function () {
            refresh();
        });
        
        modal.processResponse = function (response) {
            var newModalContent = $('<div>').html(response).find('.modal-content').html();
            modal.find('.modal-content').html(newModalContent);
            
            if (modal.find('.alert-success').length) {
                modal.modal('hide');
            }
                
            modal.find('form').submit(function () {
                modal.find('.btn-save').trigger('click');
                return false;
            });
        };

        containerTasks.on('click', '.btn-create, .btn-edit', function (event) {
            event.preventDefault();

            var _this = $(this);
            
            modal.find('.modal-title').html('');
            modal.find('.modal-body').html('Loading...');
            modal.find('.modal-footer').html('');
            
            $.get(_this.attr('href'), function (response) {
                modal.processResponse(response);
            });
            
            modal.modal();

            return false;
        });
        
        containerTasks.on('change', '[name="hidedone"]', function (event) {
            event.preventDefault();

            var _this = $(this);
            
            $.jCookie('hidedone', (_this.prop('checked') ? '1' : '0'), 365, {path: '/'});
            
            refresh();
            
            return false;
        });

    });
</script>

@endsection

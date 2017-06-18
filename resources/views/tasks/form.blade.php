<div id="__modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{ $modalTitle }}</h4>
            </div>
            <div class="modal-body">
                {!! Form::model($model, ['url' => ($model->exists ? url('/tasks/' . $model->id . '/update') : url('/tasks/create'))]) !!}
                
                    @if ($success)
                        <div class="alert alert-success">
                            Saved successfully.
                        </div>
                    @endif
                
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        {{ Form::label('title', 'Title') }}
                        {{ Form::text('title', $title, array('class' => 'form-control')) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('description', 'Description') }}
                        {{ Form::textarea('description', $description, array('class' => 'form-control')) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('status', 'Status') }}
                        {{ Form::select('status', \App\Task::statuses(), $status, array('class' => 'form-control')) }}
                    </div>

                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class' => 'btn btn-primary btn-save')) }}
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
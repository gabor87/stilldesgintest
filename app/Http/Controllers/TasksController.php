<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class TasksController extends Controller
{
    public function index()
    {
        $tasksQuery = \App\Task::query();
        
        $searching = false;
        if ($keyword = request()->get('keyword')) {
            $keyword = preg_replace("/\s+/", '%', $keyword);
            
            $tasksQuery->where('title', 'like', "%{$keyword}%")->orWhere('title', 'like', "%{$keyword}%");
            
            $searching = true;
        }
        
        if ($hideDone = !empty($_COOKIE['hidedone'])) {
            $tasksQuery->where('done', '!=', \App\Task::STATUS_DONE);
        }
        
        $tasksQuery
            ->orderByRaw("done_at IS NULL desc")
            ->orderBy('done_at', 'desc')
            ->orderBy('created_at', 'desc');
        
        $tasks = $tasksQuery->get();
        
        return view('tasks.index')
            ->with('tasks', $tasks)
            ->with('taskStatuses', \App\Task::statuses())
            ->with('searching', $searching)
            ->with('hideDone', $hideDone);
    }
    
    /**
     * 
     * @param type $id
     * @return \App\Task
     */
    private function loadModel($id = null)
    {
        if ($id) {
            if ($task = \App\Task::find($id)) {
                return $task;
            }
            
            abort(404);
        }
        
        $user = auth()->user();
        
        $task = new \App\Task();
        $task->user_id = $user->id;
        
        return $task;
    }
    
    private function save(Request $request, $id = null)
    {
        $model = $this->loadModel($id);
        
        $success = false;
        
        $errorMessages = [];
        if ($_POST) {
            $taskStatuses = \App\Task::statuses();
            
            $modelData = $request->all();
            
            $validator = validator($modelData, [
                'title' => 'required|max:255',
                'status' => 'required|in:' . join(',', array_keys($taskStatuses)),
            ]);
            
            $errors = $validator->errors();
            $errorMessages = $errors->all();
            
            if (!$errorMessages) {
                $model->title = $modelData['title'];
                $model->description = $modelData['description'];
                $model->done = $modelData['status'];
                
                if (
                        \App\Task::STATUS_DONE == $model->done
                        && empty($model->done_at)
                        ) {
                    $model->done_at = \Carbon\Carbon::now()->format(\Carbon\Carbon::ISO8601);
                }
                
                $success = $model->save();
            }
        } else {
            $modelData['title'] = $model->title;
            $modelData['description'] = $model->description;
            $modelData['status'] = $model->done;
        }
        
        return view('tasks.form')
                ->with('modalTitle', ($id ? "Edit task ({$model->title})" : 'Create task'))
                ->with('model', $model)
                ->with([
                    'title' => $modelData['title'],
                    'description' => $modelData['description'],
                    'status' => $modelData['status'],
                ])
                ->with('success', $success)
                ->with('errors', $errorMessages);
    }
    
    public function create(Request $request)
    {
        return $this->save($request);
    }
    
    public function update(Request $request, $id)
    {
        return $this->save($request, $id);
    }
    
    public function delete($id)
    {
        try {
            $task = $this->loadModel($id);
            $task->delete();

            $success = true;
        } catch (Exception $ex) {
            $success = false;
        }
        
        return response()->json([
            'success' => $success,
        ]);
    }
}

<?php

namespace App\Controllers;

use App\Models\Todo;
use Core\Controller;
use Core\View;
use JetBrains\PhpStorm\NoReturn;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TodoController extends Controller
{
    protected Todo $todo;

    public function __construct($routeParams)
    {
        $this->todo = new Todo();

        parent::__construct($routeParams);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index()
    {
        $todoItems = $this->todo->all();

        View::renderTemplate('todo/index.twig', compact('todoItems'));
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function create()
    {
        View::renderTemplate('todo/create.twig');
    }

    #[NoReturn] public function store()
    {
        $data = $this->request->all();
        $data['status'] = Todo::STATUS_PENDING;
        $this->todo->create($data);

        $this->redirect('/todos');
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function edit()
    {
        $todoItem = $this->todo->get($this->routeParams['id']);

        View::renderTemplate('todo/edit.twig', compact('todoItem'));
    }

    #[NoReturn] public function update()
    {
        $this->todo->update($this->routeParams['id'], $this->request->all());

        $this->redirect('/todos');
    }

    #[NoReturn] public function delete()
    {
        $this->todo->delete($this->routeParams['id']);

        $this->redirect('/todos');
    }
}

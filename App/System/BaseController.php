<?php

namespace App\System;

class BaseController
{
    public array $params;

    public function __construct()
    {
        try {
            Auth::authAccessToken();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function index(): string|false
    {
        $getParams = $_GET;
        $data = $this->model->all($getParams);

        return json_encode($data);
    }

    public function one(): array|string
    {
        try {
            return json_encode($this->model->one($this->params['id']));
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
        }
    }

    public function add()
    {
        try {
            $fields = json_decode(file_get_contents('php://input'), true);
            return json_encode($this->model->add($fields));
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
        }
    }

    public function delete(): string
    {
        try {
            return json_encode($this->model->delete($this->params['id']));
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
        }
    }

    public function edit(): string
    {
        try {
            $fields = json_decode(file_get_contents('php://input'), true);
            return json_encode($this->model->edit($this->params['id'], $fields));
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
        }
    }
}
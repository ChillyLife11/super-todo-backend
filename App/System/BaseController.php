<?php

namespace App\System;

class BaseController
{
    public array $params;

    public function index(): string|false
    {
        $data = $this->model->all();

        return json_encode($data);
    }

    public function one(): array|string
    {
        try {
            return json_encode($this->model->one($this->params['id']));
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function add()
    {
        try {
            $fields = json_decode(file_get_contents('php://input'), true);
            return json_encode(['id' => $this->model->add($fields)]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }
    public function delete(): string
    {
        try {
            return json_encode($this->model->delete($this->params['id']));
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }
}
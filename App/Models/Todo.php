<?php

namespace App\Models;

use Core\Model;
use PDOException;

class Todo extends Model
{
    protected string $table = 'todo_items';

    const STATUS_PENDING = 'pending';
    const STATUS_DONE = 'done';

    protected array $fillable = [
        'id',
        'name',
        'status',
        'priority',
    ];

    public function all(): bool|array
    {
        try {
            $this->db->query("SELECT * FROM $this->table ORDER BY priority, name");

            return $this->db->resultSet();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}

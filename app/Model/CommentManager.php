<?php

declare(strict_types=1);


namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

class CommentManager
{
    use SmartObject;

    public function __construct(
        private Explorer $db,
    ) { }

    public function getAll(): Selection
    {
        return $this->db->table('comment');
    }

    public function getById(int $id): ?ActiveRow
    {
        return $this->getAll()->get($id);
    }

    public function insert(array $values): ActiveRow
    {
        return $this->getAll()->insert($values);
    }

    public function getCommentsByPostId(int $postId, int $limit = null): Selection
    {
        $retVal = $this->getAll()
            ->where('post_id', $postId)
            ->order('created_at');

        if ($limit) {
            $retVal->limit($limit);
        }

        return $retVal;
    }
}
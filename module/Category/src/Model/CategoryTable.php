<?php

namespace Category\Model;

use Jajo\JSONDB;

class CategoryTable
{

    protected $table;

    function __construct($tableGateway)
    {
        $this->table = new JSONDB(__DIR__.'/../../../../data');
    }

    public function fetchAll()
    {
        $categories = $this->table->select('*')
                ->from('category.json')
                ->get();
        return $categories;
    }

    public function findBy($data)
    {
        $category = $this->table->select('*')
                ->from('category.json')
                ->where($data)
                ->get();
        if ($category) {
            return $category;
        } else {
            return false;
        }
    }

    public function save($data)
    {
        $data['id'] = count($this->fetchAll()) + 1;
        $this->table->insert('category.json', $data);
        return $data;
    }

    public function update($data)
    {
        $this->table->update($data)->from('category.json')->where(['id' => $data['id']])->trigger();
        $result = $this->findBy($data);
        return $result;
    }

    public function delete($id)
    {

        $this->table->delete()->from('category.json')->where(['id' => $id])->trigger();
    }

}

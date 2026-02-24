<?php
namespace App\Supplier\app\Models\Catalog;

class CategoryModel implements \JsonSerializable
{
    private $id;
    private $name;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
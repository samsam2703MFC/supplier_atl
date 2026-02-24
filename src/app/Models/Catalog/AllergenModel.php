<?php
namespace App\Supplier\app\Models\Catalog;

class AllergenModel implements \JsonSerializable
{
    private $id;
    private $name;
    private $code;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->code = $data['code'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code
        ];
    }
}
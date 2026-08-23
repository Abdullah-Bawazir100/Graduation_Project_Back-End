<?php

class TestEntity implements \JsonSerializable {
    public function __construct(
        public string $name,
        public ?string $hidden = "secret"
    ) {}
    
    public function jsonSerialize(): mixed {
        return [
            'name' => $this->name
        ];
    }
}

$c = collect([new TestEntity("ali"), new TestEntity("saud")]);
$arr = $c->toArray();
echo json_encode($arr);

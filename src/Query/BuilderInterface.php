<?php

namespace Cityware\Wmi\Query;

use Cityware\Wmi\ConnectionInterface;

interface BuilderInterface
{
    public function __construct(ConnectionInterface $connection);

    public function select($columns);

    public function where($column, $operator, $value);

    public function orWhere($column, $operator, $value);
}

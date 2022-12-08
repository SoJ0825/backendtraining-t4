<?php

namespace App\Core\Database;


interface RainfallSchema
{
    public function __construct($pdo);

    public function createRainfallsTable();

    public function createDistrictsTable();

    public function importData();
}
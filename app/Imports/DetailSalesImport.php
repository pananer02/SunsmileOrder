<?php

namespace App\Imports;

use App\Models\DetailSales;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DetailSalesImport implements ToModel, WithHeadingRow
{
    private $test;

    public function __construct()
    {
        //blockio init
        $this->test = time();
    }
    public function model(array $row)
    {
        $de = new DetailSales();
        $de->PriceID =  $this->test;
        $de->IDItem = $row['iditem'];
        $de->ItemName =  $row['itemname'];
        $de->save();
    }
}

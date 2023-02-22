<?php

namespace App\Exports;

use App\Models\DetailSales;
use Maatwebsite\Excel\Concerns\FromCollection;

class DetailSalesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DetailSales::all();
    }
}

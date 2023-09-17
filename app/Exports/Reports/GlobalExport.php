<?php

namespace App\Exports\Reports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GlobalExport implements FromView
{
    private array $data;
    private string $viewName;

    public function __construct(array $data, string $viewName)
    {
        $this->data = $data;
        $this->viewName = $viewName;
    }

    public function view(): View
    {
        return view($this->viewName, $this->data);
    }
}

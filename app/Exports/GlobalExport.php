<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GlobalExport implements FromCollection, WithHeadings
{
    protected $modelClass;
    protected $columns;
    protected $request;
    protected $headingsMap;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $modelName = Str::studly($request->query('model'));
        $this->modelClass = "App\\Models\\$modelName";

        $this->columns = $request->query('columns', []);
        $this->headingsMap = $request->query('headings_map', []);
    }

    public function collection()
    {
        $query = $this->modelClass::query();

        if ($search = $this->request->query('search')) {
            $query->where(function ($q) use ($search) {
                foreach ($this->columns as $col) {
                    $q->orWhere($col, 'like', "%$search%");
                }
            });
        }

        if ($sortBy = $this->request->query('sort_by')) {
            $sortDir = $this->request->query('sort_dir', 'asc');
            if (in_array($sortBy, $this->columns)) {
                $query->orderBy($sortBy, $sortDir);
            }
        }

        return $query->select($this->columns)->get();
    }

    public function headings(): array
    {
        // Mapping nama heading
        return collect($this->columns)->map(function ($col) {
            return $this->headingsMap[$col] ?? ucfirst(str_replace('_', ' ', $col));
        })->toArray();
    }
}

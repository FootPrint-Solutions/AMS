<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


// MODEL
use App\Models\Developer\AuditModel as AuditModel;

class Audit extends Controller
{
    private $title = "Audit Trail";

    public function index()
    {
        return view(
            'Developer.Audit.index',
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Get menu data (rows and count).
        $data = AuditModel::allForDataTables($request);

        $rows = [];
        $no = $request->input("start") + 1;
        foreach ($data["row"] as $key) {
            $row = [];
            $row[] = $no++;
            $row[] = $key->user_type;
            $row[] = $key->name;
            $row[] = $key->event;
            $row[] = $key->auditable_type;
            $row[] = $key->auditable_id;
            $row[] = $key->old_values;
            $row[] = $key->new_values;
            $row[] = $key->url;
            $row[] = $key->ip_address;
            $row[] = $key->user_agent;
            $row[] = formatTanggal($key->created_at);
            $row[] = formatTanggal($key->updated_at);
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $request->input("draw"),
            "recordsTotal" => AuditModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }
}

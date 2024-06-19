<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\PrintTemplateModel;
use Illuminate\Http\Request;

// MODELS
use App\Models\Settings\PrintTemplateDetailModel;

class PrintTemplate extends Controller
{
    private $title = "Print Template Settings";


    public function index()
    {
        return view(
            'Settings.PrintTemplate.WorkOrder.index',
            getIndexData(
                $this->title,
            )
        );
    }

    public function updateDetails(Request $request)
    {
        // create if not exist or update if exist
        $stepNos = $request->input('step_no', []);
        $messages = $request->input('message', []);
        $tipe = $request->input('tipe');
        $id = $request->input('id');

        try {
            $status = true;
            $printTemplates = PrintTemplateDetailModel::where('type', $tipe)->where('work_order_print_template_master_id', $id)->get();

            foreach ($printTemplates as $printTemplate) {
                $index = array_search($printTemplate->step_no, $stepNos);
                if ($index === false) {
                    $printTemplate->delete();
                } else {
                    $printTemplate->step_no = $stepNos[$index];
                    $printTemplate->work_order_print_template_master_id = $request->input('id');
                    $printTemplate->type = $request->input('tipe');
                    $printTemplate->message = $messages[$index];
                    $printTemplate->save();
                    unset($stepNos[$index]);
                    unset($messages[$index]);
                }
            }


            foreach ($stepNos as $key => $stepNo) {
                $printTemplate = $printTemplates->where('step_no', $stepNo)->where('type', $tipe)->where('work_order_print_template_master_id', $id)->first();
                if ($printTemplate) {
                    $printTemplate->message = $messages[$key];
                    $printTemplate->save();
                } else {
                    $printTemplate = new PrintTemplateDetailModel();
                    $printTemplate->step_no = $stepNo;
                    $printTemplate->work_order_print_template_master_id = $id;
                    $printTemplate->message = $messages[$key];
                    $printTemplate->type = $tipe;
                    $printTemplate->save();
                }
            }

            return getResponseData(
                $status,
                $status ? 'Print Template Updated' : 'Failed to update Print Template'
            );
        } catch (\Exception $e) {
            $status = false;
            return getResponseData($e->getMessage());
        }
    }

    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input("draw");
        $start = $request->input("start");

        // Get tax data (rows and count).
        $data = PrintTemplateModel::allForDataTables($request);

        // Set rows to be displayed in tax table.
        $rows = [];
        $no = $start + 1;
        foreach ($data["row"] as $key) {

            // Set an array for each row.
            $row = [];
            $row[] = $no++;
            $row[] = $key->name;
            $row[] = $key->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => PrintTemplateModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    /**
     * Show the form for creating resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            "Settings.PrintTemplate.WorkOrder.create",
            getIndexData(
                $this->title
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // check  // if name template-print-tokopedia-tanpa-instalasi, template-print-tokopedia, template-print-regular exist in database dont create
            $template = PrintTemplateModel::where('name', $request->input('name'))->first();
            if (
                $template && ($template->name == 'template-print-tokopedia-tanpa-instalasi' || $template->name == 'template-print-tokopedia' ||
                    $template->name == 'template-print-regular')
            ) {
                return getResponseData(false, 'Cannot create this template');
            }
            $printTemplate = new PrintTemplateModel();
            $printTemplate->name = $request->input('name');
            $status = $printTemplate->save();

            return getResponseData(
                $status,
                $status ? "The new work order print template was successfully created!" : "Failed to create the new tax!"
            );
        } catch (\Exception $e) {
            return getResponseData(false);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            // if name template-print-tokopedia-tanpa-instalasi, template-print-tokopedia, template-print-regular dont delete
            $template = PrintTemplateModel::where('id', $request->input('id'))->first();
            if (
                $template->name == 'template-print-tokopedia-tanpa-instalasi' || $template->name == 'template-print-tokopedia' ||
                $template->name == 'template-print-regular'
            ) {
                return getResponseData(false, 'Cannot delete this template');
            }
            $status = PrintTemplateModel::destroy($request->input('id'));

            return getResponseData(
                $status,
                $status ? "The work order print template was successfully deleted!" : "Failed to delete the work order print template!"
            );
        } catch (\Exception $e) {
            return getResponseData($e->getMessage());
        }
    }

    /**
     * Show the form for editing resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view(
            "Settings.PrintTemplate.WorkOrder.create",
            getIndexData(
                $this->title,
                array(
                    "template" => PrintTemplateModel::find($id)->toArray()
                )
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            // if name template-print-tokopedia-tanpa-instalasi, template-print-tokopedia, template-print-regular exist in database dont update
            $template = PrintTemplateModel::where('name', $request->input('name'))->first();
            if (
                $template && ($template->name == 'template-print-tokopedia-tanpa-instalasi' || $template->name == 'template-print-tokopedia' ||
                    $template->name == 'template-print-regular')
            ) {
                return getResponseData(false, 'Cannot update this template');
            }
            $printTemplate = PrintTemplateModel::find($request->input('id'));
            $printTemplate->name = $request->input('name');
            $status = $printTemplate->save();

            return getResponseData(
                $status,
                $status ? "The work order print template was successfully updated!" : "Failed to update the work order print template!"
            );
        } catch (\Exception $e) {
            return getResponseData(false);
        }
    }

    /**
     * Show the form for editing resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function details($id)
    {
        $template = PrintTemplateModel::find($id);

        if (!$template) {
            // Handle the case where the template is not found
            abort(404, 'Template not found');
        }

        return view(
            "Settings.PrintTemplate.WorkOrder.details",
            getIndexData(
                $this->title,
                array(
                    "template" => PrintTemplateModel::find($id)->toArray(),
                    "templateDetailsPageOne" => $template->details()->where("type", "page-one")->get(),
                    "templateDetailsPageTwo" => $template->details()->where("type", "page-two")->get()
                )
            )
        );
    }
}

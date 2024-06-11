<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\PrintTemplateModel;
use Illuminate\Http\Request;

class PrintTemplate extends Controller
{
    private $title = "Print Template Settings";


    public function index()
    {
        return view(
            'Settings.PrintTemplate.index',
            getIndexData(
                $this->title,
                array(
                    "templates" =>  PrintTemplateModel::all()
                )
            )
        );
    }

    public function update(Request $request)
    {
        // create if not exist or update if exist
        $stepNos = $request->input('step_no', []);
        $messages = $request->input('message', []);

        try {
            $status = true;
            $printTemplates = PrintTemplateModel::all();
            foreach ($printTemplates as $printTemplate) {
                $index = array_search($printTemplate->step_no, $stepNos);
                if ($index === false) {
                    $printTemplate->delete();
                } else {
                    $printTemplate->message = $messages[$index];
                    $printTemplate->save();
                    unset($stepNos[$index]);
                    unset($messages[$index]);
                }
            }

            foreach ($stepNos as $index => $stepNo) {
                $printTemplate = new PrintTemplateModel();
                $printTemplate->step_no = $stepNo;
                $printTemplate->message = $messages[$index];
                $printTemplate->save();
            }

            return getResponseData(
                $status,
                $status ? 'Print Template Updated' : 'Failed to update Print Template'
            );
        } catch (\Throwable $th) {
            return getResponseData(
                false,
                'Failed to update Print Template'
            );
        }
    }
}

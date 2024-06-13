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
                    "templatesRegularInstalasi" =>  PrintTemplateModel::Where('tipe', "regular-instalasi")->get(),
                    "templatesTokopediaInstalasi" =>  PrintTemplateModel::Where('tipe', "tokopedia-instalasi")->get(),
                    "templatesTokopediaTanpaInstalasi" =>  PrintTemplateModel::Where('tipe', "tokopedia-tanpa-instalasi")->get(),
                )
            )
        );
    }

    public function update(Request $request)
    {
        // create if not exist or update if exist
        $stepNos = $request->input('step_no', []);
        $messages = $request->input('message', []);
        $tipe = $request->input('tipe');

        try {
            $status = true;
            $printTemplates = PrintTemplateModel::where('tipe', $tipe)->get();

            foreach ($printTemplates as $printTemplate) {
                $index = array_search($printTemplate->step_no, $stepNos);
                if ($index === false) {
                    $printTemplate->delete();
                } else {
                    $printTemplate->tipe = $request->input('tipe');
                    $printTemplate->message = $messages[$index];
                    $printTemplate->save();
                    unset($stepNos[$index]);
                    unset($messages[$index]);
                }
            }


            foreach ($stepNos as $key => $stepNo) {
                $printTemplate = $printTemplates->where('step_no', $stepNo)->where('tipe', $tipe)->first();
                if ($printTemplate) {
                    $printTemplate->message = $messages[$key];
                    $printTemplate->save();
                } else {
                    $printTemplate = new PrintTemplateModel();
                    $printTemplate->step_no = $stepNo;
                    $printTemplate->message = $messages[$key];
                    $printTemplate->tipe = $tipe;
                    $printTemplate->save();
                }
            }

            return getResponseData(
                $status,
                $status ? 'Print Template Updated' : 'Failed to update Print Template'
            );
        } catch (\Exception $e) {
            $status = false;
        }
    }
}

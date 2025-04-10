<?php

namespace App\Http\Controllers\Publish;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Publish\FaqModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class Faq extends Controller
{
    private $title = "FAQ";

    /**
     * Show the FAQ index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "Publish.Faq.index",
            getIndexData(
                $this->title
            )
        );
    }

    public function create()
    {
        return view(
            "Publish.Faq.create",
            getIndexData($this->title)
        );
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'question' => 'required|string|max:255',
                'answer' => 'required|string',
                'status' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Create the FAQ
            $faq = new FaqModel();
            $faq->question = $request->input('question');
            $faq->answer = $request->input('answer');
            $faq->status = $request->input('status');
            $status = $faq->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The new FAQ was successfully created!" : "Failed to create the new FAQ!"
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return getResponseData(
                false,
                "Failed to create the new FAQ!",
                $th->getMessage()
            );
        }
    }

    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input('draw');
        $start = $request->input('start');

        $data = FaqModel::allForDataTables($request);
        $rows = [];
        $no = $start + 1;
        foreach ($data['row'] as $faq) {
            $row = [];
            $row[] = $no++;
            $row[] = $faq->question;
            $row[] = preg_replace('/<img[^>]*>/', '[Image]', $faq->answer);
            // $row[] = $faq->status ? 'Active' : 'Inactive';
            $row[] = $faq->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => FaqModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    public function edit($id)
    {
        $faq = FaqModel::findOrFail($id);
        return view(
            "Publish.Faq.create",
            getIndexData(
                $this->title,
                [
                    'profile' => $faq,
                ]
            )
        );
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'question' => 'required|string|max:255',
                'answer' => 'required|string',
                'status' => 'required|boolean',
                'id' => 'required|exists:faqs,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Update the FAQ
            $faq = FaqModel::findOrFail($request->input('id'));
            $faq->question = $request->input('question');
            $faq->answer = $request->input('answer');
            $faq->status = $request->input('status');
            $status = $faq->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The FAQ was successfully updated!" : "Failed to update the FAQ!"
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return getResponseData(
                false,
                "Failed to update the FAQ!",
                $th->getMessage()
            );
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids = $request->input('id');
            $status = FaqModel::whereIn('id', $ids)->delete();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The selected FAQs were successfully deleted!" : "Failed to delete the selected FAQs!"
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return getResponseData(
                false,
                "Failed to delete the selected FAQs!",
                $th->getMessage()
            );
        }
    }
}

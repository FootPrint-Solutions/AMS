<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\MessageTemplateModel;
use Illuminate\Http\Request;

class MessageTemplate extends Controller
{
    private $title = "Message Template Settings";
    private $menu = 5;
    private $submenu = 1;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view(
            'Settings.MessageTemplate.edit',
            getIndexData(
                $this->title,
                $this->menu,
                $this->submenu,
                array(
                    "templates" => MessageTemplateModel::all()->map(function ($template) {
                        return [
                            "message" => $template->message,
                            "name" => $template->name,
                            "opening_message" => $template->opening_message,
                            "closing_message" => $template->closing_message,
                        ];
                    })->toArray()
                )
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $status = true;

        // Personal Details
        $template = MessageTemplateModel::where('name', 'personal_details')->first();
        $template->opening_message = $request->openingpersonaldetail;
        $template->closing_message = $request->closingpersonaldetail;
        $status &= $template->save();

        // Product Recommendation
        $template = MessageTemplateModel::where('name', 'product_recommendation')->first();
        $template->opening_message = $request->openingproductrecommendation;
        $template->closing_message = $request->closingproductrecommendation;
        $status &= $template->save();

        // Checkout Page
        $template = MessageTemplateModel::where('name', 'checkout_page')->first();
        $template->opening_message = $request->openingcheckoutpage;
        $template->closing_message = $request->closingcheckoutpage;
        $status &= $template->save();

        // Payment Details
        $template = MessageTemplateModel::where('name', 'payment_details')->first();
        $template->opening_message = $request->openingpaymentdetails;
        $template->closing_message = $request->closingpaymentdetails;
        $status &= $template->save();

        // Set a new response data to be sent.
        return getResponseData(
            $status,
            $status ? "All message templates was successfully updated!" : "Failed to update message templates!"
        );
    }
}

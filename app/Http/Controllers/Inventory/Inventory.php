<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Inventory extends Controller
{
    private $title = "Battery";
    private $service;

    // Constant sheet id.
    const SHEET_ID = "1iHXJ4TlWUX1-DSL1UHv07X8JVnanPcvzaDiaL0TGiDE";

    function __construct()
    {
        // Configure the Google Client.
        $client = new \Google_Client();
        $client->setApplicationName('Google Sheets API');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $path = storage_path('app/google/credentials.json');
        $client->setAuthConfig($path);

        // Configure the Sheets Service.
        $this->service = new \Google_Service_Sheets($client);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('inventory.inventory.index', getIndexData(
            $this->title,
            array(
                'inventories' => $this->getAllInventory()
            )
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($number)
    {
        return view('inventory.inventory.edit', getIndexData(
            $this->title,
            array(
                'profile' => $this->getInventory($number)
            )
        ));
    }

    /**
     * Get all inventory from Spreadsheet.
     */
    private function getAllInventory()
    {
        $range = 'Sheet1!A2:C';
        $response = $this->service->spreadsheets_values->get(self::SHEET_ID, $range);
        $values = $response->getValues();
        return $values;
    }

    /**
     * Get an inventory from Spreadsheet.
     */
    private function getInventory($number)
    {
        $rowNumber = $number + 1;
        $range = "Sheet1!A$rowNumber:C$rowNumber";
        $response = $this->service->spreadsheets_values->get(self::SHEET_ID, $range);
        $values = $response->getValues();
        return $values[0];
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
        try {
            // Prepare the data to update.
            $updateRow = [
                $request->number,
                $request->name,
                $request->quantity
            ];
            $rows = [$updateRow];
            $valueRange = new \Google_Service_Sheets_ValueRange();
            $valueRange->setValues($rows);
            $rowNumber = $request->number + 1;
            $range = "Sheet1!A$rowNumber:C$rowNumber";
            $options = ['valueInputOption' => 'USER_ENTERED'];

            // Perform the update.
            $response = $this->service->spreadsheets_values->update(self::SHEET_ID, $range, $valueRange, $options);

            // Check if the update was successful.
            $status = 0;
            if ($response->getUpdatedCells() > 0)
                $status = 1;

            return getResponseData(
                $status,
                $status ? "The inventory was successfully updated!" : "Failed to update the inventory!"
            );
        } catch (\Exception $e) {
            // Logging error message.
            Log::error($e->getMessage());

            // Set an error response data to be sent.
            return getResponseData(false);
        }
    }
}

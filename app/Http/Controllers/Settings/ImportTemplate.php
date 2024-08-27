<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Settings\ImportTemplateModel;

class ImportTemplate extends Controller
{
    /**
     * Update the import template.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success or failure of the template update.
     */
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->id;
            $template = $request->template;

            $importTemplate = ImportTemplateModel::find($id);
            $importTemplate->template = $template;
            $importTemplate->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update template'
            ]);
        }
    }

    /**
     * Delete an import template.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success or failure of the deletion.
     */
    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->id;

            $importTemplate = ImportTemplateModel::find($id);
            $importTemplate->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete template'
            ]);
        }
    }
}

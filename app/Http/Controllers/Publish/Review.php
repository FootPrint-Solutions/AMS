<?php

namespace App\Http\Controllers\Publish;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Publish\ReviewsModel as ReviewModel;
use App\Models\MasterData\Vehicle\VehicleModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class Review extends Controller
{
    private $title = "Review";


    /**
     * Show the Vehicle index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view(
            "Publish.Review.index",
            getIndexData(
                $this->title
            )
        );
    }

    public function create()
    {
        // Get all vehicles for the dropdown
        $vehicles = VehicleModel::all();
        return view(
            "Publish.Review.create",
            getIndexData(
                $this->title,
                [
                    'vehicles' => $vehicles,
                ]
            )
        );
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'vehicle_id' => 'required|exists:vehicles,id',
                'testimonial' => 'required|string',
                'stars' => 'required|numeric|min:0|max:5',
                'user_photo' => 'nullable|image',
                'testimonial_photo' => 'nullable|image',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Handle file uploads if they exist
            if ($request->hasFile('user_photo')) {
                $userPhoto = $request->file('user_photo');
                $userPhotoPath = $userPhoto->store('reviews/user_photos', 'public');
                $request->merge(['user_photo' => basename($userPhotoPath)]);
            }

            if ($request->hasFile('testimonial_photo')) {
                $testimonialPhoto = $request->file('testimonial_photo');
                $testimonialPhotoPath = $testimonialPhoto->store('reviews/testimonial_photos', 'public');
                $request->merge(['testimonial_photo' => basename($testimonialPhotoPath)]);
            }

            // Create the review
            $review = new ReviewModel();
            $review->name = $request->input('name');
            $review->vehicle_id = $request->input('vehicle_id');
            $review->testimonial = $request->input('testimonial');
            $review->stars = $request->input('stars');
            $review->user_photo = $request->input('user_photo') ?? null;
            $review->testimonial_photo = $request->input('testimonial_photo') ?? null;
            $status = $review->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The new review was successfully created!" : "Failed to create the new review!"
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return getResponseData(
                false,
                "Failed to create the new review!",
                $th->getMessage()
            );
        }
    }

    public function show(Request $request)
    {
        // Get all DataTables requests.
        $draw = $request->input('draw');
        $start = $request->input('start');

        $data = ReviewModel::allForDataTables($request);
        $rows = [];
        $no = $start + 1;
        foreach ($data['row'] as $review) {
            $row = [];
            $row[] = $no++;
            $row[] = $review->name;
            $row[] = $review->vehicle->name;
            $row[] = $review->testimonial;
            $row[] = $review->stars;
            $row[] = $review->user_photo ?
                '<img src="' . asset('storage/reviews/user_photos/' . $review->user_photo) . '" alt="User Photo" width="50" height="50" onerror="this.onerror=null;this.src=\'https://placehold.co/50x50\'">' : 'No Photo';
            $row[] = $review->testimonial_photo ?
                '<img src="' . asset('storage/reviews/testimonial_photos/' . $review->testimonial_photo) . '" alt="Testimonial Photo" width="50" height="50" onerror="this.onerror=null;this.src=\'https://placehold.co/50x50\'">' : 'No Photo';
            $row[] = $review->id;
            $rows[] = $row;
        }

        return response()->json(array(
            "draw" => $draw,
            "recordsTotal" => ReviewModel::count(),
            "recordsFiltered" => $data["count"],
            "data" => $rows
        ));
    }

    public function edit($id)
    {
        $review = ReviewModel::findOrFail($id);
        $vehicles = VehicleModel::all();
        return view(
            "Publish.Review.create",
            getIndexData(
                $this->title,
                [
                    'profile' => $review,
                    'vehicles' => $vehicles,
                ]
            )
        );
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'vehicle_id' => 'required|exists:vehicles,id',
                'testimonial' => 'required|string',
                'stars' => 'required|numeric|min:0|max:5',
                'user_photo' => 'nullable|image',
                'testimonial_photo' => 'nullable|image',
                'id' => 'required|exists:reviews,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Handle file uploads if they exist
            if ($request->hasFile('user_photo')) {
                $userPhoto = $request->file('user_photo');
                $userPhotoPath = $userPhoto->store('reviews/user_photos', 'public');
                $request->merge(['user_photo' => basename($userPhotoPath)]);
            }

            if ($request->hasFile('testimonial_photo')) {
                $testimonialPhoto = $request->file('testimonial_photo');
                $testimonialPhotoPath = $testimonialPhoto->store('reviews/testimonial_photos', 'public');
                $request->merge(['testimonial_photo' => basename($testimonialPhotoPath)]);
            }

            // Update the review
            $review = ReviewModel::findOrFail($request->input('id'));
            $review->name = $request->input('name');
            $review->vehicle_id = $request->input('vehicle_id');
            $review->testimonial = $request->input('testimonial');
            $review->stars = $request->input('stars');
            if ($request->hasFile('user_photo')) {
                $review->user_photo = $request->input('user_photo') ?? null;
            }
            if ($request->hasFile('testimonial_photo')) {
                $review->testimonial_photo = $request->input('testimonial_photo') ?? null;
            }
            $status = $review->save();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The review was successfully updated!" : "Failed to update the review!"
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return getResponseData(
                false,
                "Failed to update the review!",
                $th->getMessage()
            );
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids = $request->input('id');
            $status = ReviewModel::whereIn('id', $ids)->delete();

            if ($status)
                DB::commit();
            else
                DB::rollBack();

            return getResponseData(
                $status,
                $status ? "The selected reviews were successfully deleted!" : "Failed to delete the selected reviews!"
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return getResponseData(
                false,
                "Failed to delete the selected reviews!",
                $th->getMessage()
            );
        }
    }
}

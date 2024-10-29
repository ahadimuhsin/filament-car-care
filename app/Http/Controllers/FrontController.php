<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\City;
use App\Models\CarStore;
use App\Models\CarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use AnourValar\EloquentSerialize\Service;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\StoreBookingPaymentRequest;
use App\Models\BookingTransaction;

class FrontController extends Controller
{

    public const BOOKING_FEE = 25_000;
    public const PPN = 0.11;
    public function index()
    {
        $cities = City::orderBy("name", "ASC")->get();
        $carServices = CarService::withCount("storeServices")->get();

        return view("front.index", compact("cities", "carServices"));
    }

    public function search (Request $request)
    {
        $cityId = $request->input("city_id");
        $serviceTypeId = $request->input("service_type");

        $carService = CarService::where('id', $serviceTypeId)->first();
        if(!$carService)
        {
            return redirect()->back()->with("error", "Service Type Not Found");
        }

        $stores = CarStore::whereHas("storeServices", function($q) use($carService){
            $q->where("car_service_id", $carService->id);
        })->withCount("storeServices")
        ->where("city_id", $cityId)->get();

        $city = City::find($cityId);

        session()->put('serviceTypeId', $request->input('service_type'));

        return view("front.stores", [
            'stores' => $stores,
            'carService' => $carService,
            'cityName' => $city ? $city->name : "Unknown City"
        ]);
    }

    public function detail(CarStore $carStore)
    {
        $carStore = $carStore->load("photos");

        $serviceTypeId = session()->get('serviceTypeId');

        $carService = CarService::where("id", $serviceTypeId)->first();

        // dd($carService);
        return view("front.details", compact("carStore", "carService"));
    }

    public function booking(CarStore $carStore)
    {
        session()->put("carStoreId", $carStore->id);

        $serviceTypeId = session()->get("serviceTypeId");
        $service = CarService::where("id", $serviceTypeId)->first();

        return view("front.booking", compact("carStore", "service"));
    }

    public function storeBooking(StoreBookingRequest $storeBookingRequest)
    {
        $validated = $storeBookingRequest->validated();

        $customerName = $validated["name"];
        $customerPhoneNumber = $validated["phone_number"];
        $customerTimeAt = $validated["time_at"];

        // save to session
        session()->put("customerName", $customerName);
        session()->put("customerPhoneNumber", $customerPhoneNumber);
        session()->put("customerTimeAt", $customerTimeAt);

        // get session
        $serviceTypeId = session()->get("serviceTypeId");
        $carStoreId = session()->get("carStoreId");

        return redirect()->route("front.booking.payment", [$carStoreId, $serviceTypeId]);
    }

    public function bookingPayment(CarStore $carStore, CarService $carService)
    {

        $totalPpn = $carService->price * self::PPN;
        $grandTotal = $totalPpn + self::BOOKING_FEE + $carService->price;

        session()->put("totalAmount", $grandTotal);

        return view("front.payment", compact("carService", "carStore", "totalPpn", "bookingFee", "grandTotal"));
    }

    public function storeBookingPayment(StoreBookingPaymentRequest $storeBookingPaymentRequest)
    {
        // get session
        $serviceTypeId = session()->get("serviceTypeId");
        $carStoreId = session()->get("carStoreId");
        $customerName = session()->get("customerName");
        $customerPhoneNumber = session()->get("customerPhoneNumber");
        $totalAmount = session()->get("totalAmount");
        $customerTimeAt = session()->get("customerTimeAt");

        $bookingTransactionId = null;



        DB::beginTransaction();

        try {
            $validated = $storeBookingPaymentRequest->validated();

            $proofPath = $storeBookingPaymentRequest->file('proof')->store('proofs', 'public');
            $validated['proof'] = $proofPath;

            $validated['name'] = $customerName;
            $validated['total_amount'] = $totalAmount;
            $validated['phone_number'] = $customerPhoneNumber;
            $validated["started_at"] = now()->tomorrow()->format("Y-m-d");
            $validated['time_at'] = $customerTimeAt;
            $validated["car_store_id"] = $carStoreId;
            $validated["car_service_id"] = $serviceTypeId;
            $validated["trx_id"] = BookingTransaction::generateUniqueTrxId();
            $validated["is_paid"] = false;

            $newBooking = BookingTransaction::create($validated);
            $bookingTransactionId = $newBooking->id;

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with("error", $e->getMessage());
        }
        // dd($bookingTransactionId);
        return redirect()->route("front.booking.success", $bookingTransactionId);
    }

    public function success(BookingTransaction $bookingTransaction)
    {
        return view("front.success_booking", compact("bookingTransaction"));
    }

    public function transactions()
    {
        return view("front.check-booking");
    }

    public function transactionDetails(Request $request)
    {
        $request->validate([
            "trx_id" => ['required', 'string', 'max:255'],
            "phone_number" => ['required', 'string', 'max:255'],
        ]);

        $trx_id = $request->input("trx_id");
        $phone_number = $request->input("phone_number");

        $details = BookingTransaction::with(["store_details", "service_details"])
        ->where("trx_id", "=", $trx_id)->where("phone_number", "=", $phone_number)
        ->first();

        if(!$details)
        {
            return back()->withErrors(["error" => "Data Tidak Ditemukan"]);
        }

        $totalPpn = $details->service_details->price * self::PPN;
        // $grandTotal = $totalPpn + self::BOOKING_FEE + $details->service_details->price;

        return view("front.booking-details", [
            "details" => $details,
            "booking_fee" => self::BOOKING_FEE,
            "total_ppn" => $totalPpn
        ]);
    }
}

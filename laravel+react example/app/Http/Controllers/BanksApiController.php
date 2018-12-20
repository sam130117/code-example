<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BanksFormRequest;
use App\Models\Banks;
use App\Models\LogUsersActions;
use Illuminate\Http\Response;

class BanksApiController extends Controller
{
    public function index()
    {
        $banks = Banks::getFilteredBanks();
        return response()->json($banks, Response::HTTP_OK);
    }

    public function show($id)
    {
        $bank = Banks::where('id', $id)->first();
        return response($bank->jsonSerialize(), Response::HTTP_OK);
    }

    public function store(BanksFormRequest $request)
    {
        $bank = Banks::create($request->all());
        LogUsersActions::saveAction("Bank <b>" . $bank->bank_name . "</b> was successfully created via API.");

        return response(null, Response::HTTP_OK);
    }

    public function update(BanksFormRequest $request, Banks $bank)
    {
        $bank->update($request->all());
        LogUsersActions::saveAction("Bank <b>" . $bank->bank_name . "</b> was successfully updated via API.");

        return response(null, Response::HTTP_OK);
    }

    public function destroy(Banks $bank)
    {
        Banks::destroy($bank->id);
        LogUsersActions::saveAction("Bank <b>" . $bank->bank_name . "</b> was successfully deleted via API.");

        return response(null, Response::HTTP_OK);
    }
}
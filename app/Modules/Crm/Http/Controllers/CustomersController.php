<?php

namespace App\Modules\Crm\Http\Controllers;


use App\DataTables\CustomersDataTable;
use App\Http\Controllers\BaseController;
use App\Modules\Crm\Models\Customers;
use App\Modules\StoreInventory\Models\Stores;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomersController extends BaseController
{
    public function index(CustomersDataTable $dataTable)
    {
        $this->setPageTitle('Customers', 'List of all customers');
        return $dataTable->render('Crm::customers.index');
    }

    public function create()
    {
        $stores = Stores::all();
        $this->setPageTitle('Create Customers', 'create new customers');
        return view('Crm::customers.create', compact('stores'));
    }

    public function store(Request $request)
    {
        //validate Form Data
        $request->validate([
            'name' => "required|min:3",
            'contact_no' => "required|min:11",
            'store_id' => "required",
            'address' => "required",
        ]);

        //Get store information by store id
        $store = Stores::findOrFail($request->store_id);
        //include Customer model
        $customers = new Customers();
        //find max sn no from customer table
        $maxSn = $customers->where('store_id', '=', $request->store_id)->max('max_sn');
        //Generate new Max SN no
        $Max_sn = $maxSn ? $maxSn + 1 : 1;
        //Assign new max_sn for user
        $customers->max_sn = $Max_sn;
        //Create and assign customer code
        $customers->code = $store->code . "-" . str_pad($Max_sn, 4, '0', STR_PAD_LEFT);
        //Assigning Form data to customer
        $customers->name = $request->name;
        $customers->contact_no = $request->contact_no;
        $customers->store_id = $request->store_id;
        $customers->address = $request->address;
        $customers->created_by = auth()->user()->id;

        //save all data to customer table
        if ($customers->save()) {
            //redirect to create customer page
            return $this->responseRedirect('crm.customers.index', 'Customer added successfully', 'success', false, false);
        } else {
            //redirect to create customer page with previous input
            return $this->responseRedirectBack('Error occurred while creating Customer.', 'error', true, true);
        }
    }

    public function edit($id)
    {
        $data = Customers::find($id);
        $stores = Stores::all();
        $this->setPageTitle('Edit Customer', 'Edit selected customer');
        return view('Crm::customers.edit', compact('data', 'stores'));
    }

    public function update(Request $req, $id)
    {
        //validate Form Data
        $req->validate([
            'name' => "required|min:3",
            'contact_no' => "required|min:11",
            'store_id' => "required",
            'address' => "required",
        ]);

        //include Customer model
        $customers = Customers::findOrFail($id);
        if ($customers->store_id != $req->store_id) {
            //Get store information by store id
            $store = Stores::findOrFail($req->store_id);
            $maxSn = $customers->where('store_id', '=', $req->store_id)->max('max_sn');
            //Generate new Max SN no
            $Max_sn = $maxSn ? $maxSn + 1 : 1;

            $customers->max_sn = $Max_sn;
            $customers->code = $store->code . "-" . str_pad($Max_sn, 4, '0', STR_PAD_LEFT);
        }
        $customers->name = $req->name;
        $customers->contact_no = $req->contact_no;
        $customers->address = $req->address;
        $customers->store_id = $req->store_id;
        $customers->updated_by = auth()->user()->id;

        if ($customers->update()) {
            //redirect to create customer page
            return $this->responseRedirect('crm.customers.index', 'Customer edited successfully', 'success', false, false);
        } else {
            //redirect to create customer page with previous input
            return $this->responseRedirectBack('Error occurred while editing Customer.', 'error', true, true);
        }

    }

    public function delete($id)
    {
        $data = Customers::find($id);
        if ($data->delete()) {
            return response()->json([
                'success' => true,
                'status_code' => 200,
                'message' => 'Record has been deleted successfully!',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status_code' => 200,
                'message' => 'Please try again!',
            ]);
        }
    }


    public function getCustomerListByName(Request $request): ?JsonResponse
    {
        $response = array();
        if ($request->has('search')) {
            $search = trim($request->search);
            $data = new Customers();
            $data = $data->select('id', 'name', 'code', 'store_id', 'contact_no');
            if ($search != '') {
                $data = $data->where('name', 'like', '%' . $search . '%');
            }
            if ($request->has('store_id')) {
                $store_id = trim($request->store_id);
                if ($store_id > 0) {
                    $data = $data->where('store_id', '=', $store_id);
                }
            }
            $data = $data->limit(20);
            $data = $data->orderby('name', 'asc');
            $data = $data->get();
            if (!$data->isEmpty()) {
                foreach ($data as $dt) {
                    $response[] = array("value" => $dt->id, "label" => $dt->name, 'name' => $dt->name, 'code' => $dt->code, 'store_id' => $dt->store_id, 'contact_no' => $dt->contact_no);
                }
            } else {
                $response[] = array("value" => '', "label" => 'No data found!', 'name' => '', 'code' => '', 'store_id' => '', 'contact_no' => '');
            }
        } else {
            $response[] = array("value" => '', "label" => 'No data found!', 'name' => '', 'code' => '', 'store_id' => '', 'contact_no' => '');
        }
        return response()->json($response);
    }

    public function getCustomerListByCode(Request $request): ?JsonResponse
    {
        $response = array();
        if ($request->has('search')) {
            $search = trim($request->search);
            $data = new Customers();
            $data = $data->select('id', 'name', 'code', 'store_id', 'contact_no');
            if ($search != '') {
                $data = $data->where('code', 'like', '%' . $search . '%');
            }
            if ($request->has('store_id')) {
                $store_id = trim($request->store_id);
                if ($store_id > 0) {
                    $data = $data->where('store_id', '=', $store_id);
                }
            }
            $data = $data->limit(20);
            $data = $data->orderby('code', 'asc');
            $data = $data->get();
            if (!$data->isEmpty()) {
                foreach ($data as $dt) {
                    $response[] = array("value" => $dt->id, "label" => $dt->code, 'name' => $dt->name, 'code' => $dt->code, 'store_id' => $dt->store_id, 'contact_no' => $dt->contact_no);
                }
            } else {
                $response[] = array("value" => '', "label" => 'No data found!', 'name' => '', 'code' => '', 'store_id' => '', 'contact_no' => '');
            }
        } else {
            $response[] = array("value" => '', "label" => 'No data found!', 'name' => '', 'code' => '', 'store_id' => '', 'contact_no' => '');
        }
        return response()->json($response);
    }

    public function getCustomerListByContactNo(Request $request): ?JsonResponse
    {
        $response = array();
        if ($request->has('search')) {
            $search = trim($request->search);
            $data = new Customers();
            $data = $data->select('id', 'name', 'contact_no', 'code', 'store_id', 'contact_no');
            if ($search != '') {
                $data = $data->where('contact_no', 'like', '%' . $search . '%');
            }
            if ($request->has('store_id')) {
                $store_id = trim($request->store_id);
                if ($store_id > 0) {
                    $data = $data->where('store_id', '=', $store_id);
                }
            }
            $data = $data->limit(20);
            $data = $data->orderby('code', 'asc');
            $data = $data->get();
            if (!$data->isEmpty()) {
                foreach ($data as $dt) {
                    $response[] = array("value" => $dt->id, "label" => $dt->contact_no, 'name' => $dt->name, 'code' => $dt->code, 'store_id' => $dt->store_id, 'contact_no' => $dt->contact_no);
                }
            } else {
                $response[] = array("value" => '', "label" => 'No data found!', 'name' => '', 'code' => '', 'store_id' => '', 'contact_no' => '');
            }
        } else {
            $response[] = array("value" => '', "label" => 'No data found!', 'name' => '', 'code' => '', 'store_id' => '', 'contact_no' => '');
        }
        return response()->json($response);
    }
}

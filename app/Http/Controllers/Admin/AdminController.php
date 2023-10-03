<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\PricingController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\Plan;

class AdminController extends Controller
{
    protected PricingController $pricingUtils;


    public function __construct(PricingController $pricingUtils){
        $this->middleware('permission:admin'); 
        $this->pricingUtils = $pricingUtils;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $users = User::where('role','admin')->where('id','!=',1)->latest()->get();
        return view('admin.admin.index', compact('users'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
        $roles  = Role::all();
        return view('admin.admin.create', compact('roles'));
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation Data
        $request->validate([
            'name' => 'required|max:50',
            'roles' => 'required',
            'email' => 'required|max:100|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        // Create New User
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = 'admin';
        $user->password = Hash::make($request->password);
        $user->save();

        if ($request->roles) {
            $user->assignRole($request->roles);
        }


        return response()->json([
            'redirect' => route('admin.admin.index'),
            'message' => __('Admin created successfully !!')
        ]);
    }

  

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
        $user = User::find($id);
        $roles  = Role::all();
        return view('admin.admin.edit', compact('user', 'roles'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Create New User
        $user = User::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|max:100|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
        ]);


        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $user->roles()->detach();
        if ($request->roles) {
            $user->assignRole($request->roles);
        }


       return response()->json([
        'message' => __('Admin Updated successfully !!')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        User::destroy($id);

       return response()->json([
        'redirect' => route('admin.admin.index'),
        'message' => __('Admin deleted successfully !!')
        ]);
    }

    function addCustomer() {
        $plan = Plan::all();
        return view('admin.customers.add_customer', compact( 'plan'));
    }

    function saveCustomer(Request $request) {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        $plans             = Plan::where('id', $request->plan)->first();
        $user              = new User;
        $user->name        = $request->name;
        $user->email       = $request->email;
        $user->role        = 'user';
        $user->status      = 1;
        $user->plan        = json_encode($plans->data);
        $user->plan_id     = $plans->id;
        $user->will_expire = '2100-01-01';
        $user->authkey     = $this->pricingUtils->generateAuthKey();
        $user->password    = Hash::make($request->password);
        $user->save();
        return response()->json([
            'redirect' => route('admin.customer.index'),
            'message' => __('Customer added successfully !!')
            ]);
    }
}

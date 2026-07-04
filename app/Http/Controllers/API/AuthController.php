<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ProviderProof;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function registerCustomer(Request $request)
    {

        $request->validate([
            'first_name'  => 'required|string',
            'second_name' => 'nullable|string',
            'last_name'   => 'required|string',
            'email'       => 'required|email|unique:users',
            'phone'       => 'required|string|unique:users',
            'address'     => 'nullable|string',
            'password'    => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'first_name'  => $request->first_name,
            'second_name' => $request->second_name,
            'last_name'   => $request->last_name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'role'        => 'customer',
            'password'    => $request->password,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }

    public function registerProvider(Request $request)
    {
        $request->validate([
            // الخطوة 1: البيانات الشخصية
            'first_name'   => 'required|string',
            'second_name'  => 'nullable|string',
            'last_name'    => 'required|string',
            'email'        => 'required|email|unique:users',
            'phone'        => 'required|string|unique:users',
            'address'      => 'nullable|string',
            'password'     => 'required|string|min:6|confirmed',
            'company_name' => 'required|string',
            'terms_subscr' => 'nullable|string',
            // الخطوة 2: بيانات المولد
            'generator_type'   => 'required|string',
            'generator_powerKW'=> 'required|numeric',
            'generator_gps'    => 'nullable|string',
            'generator_price'  => 'required|numeric',
            // الخطوة 3: صور الإثباتات
            'proofs'       => 'required|array|min:1',
            'proofs.*'     => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = User::create([
            'first_name'  => $request->first_name,
            'second_name' => $request->second_name,
            'last_name'   => $request->last_name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'role'        => 'provider',
            'password'    => $request->password,
        ]);

        $provider = Provider::create([
            'user_id'      => $user->id,
            'company_name' => $request->company_name,
            'terms_subscr' => $request->terms_subscr,
            'status'       => 'active',
        ]);

        \App\Models\Generator::create([
            'provider_id' => $provider->id,
            'type'        => $request->generator_type,
            'status'      => 'active',
            'gps'         => $request->generator_gps,
            'powerKW'     => $request->generator_powerKW,
            'price_KW'    => $request->generator_price,
        ]);

        foreach ($request->file('proofs') as $proof) {
            $path = $proof->store('provider_proofs', 'public');
            ProviderProof::create([
                'provider_id' => $provider->id,
                'image_path'  => $path,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم التسجيل بنجاح',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة'],
            ]);
        }

        if ($user->isProvider()) {
            $provider = Provider::where('user_id', $user->id)->first();
            if ($provider && $provider->status === 'pending') {
                return response()->json(['message' => 'حسابك قيد المراجعة من قبل الإدارة'], 403);
            }
            if ($provider && $provider->status === 'suspended') {
                return response()->json(['message' => 'تم تعليق حسابك'], 403);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'تعذر إرسال رابط إعادة التعيين'], 500);
        }

        return response()->json(['message' => 'تم إرسال رابط إعادة تعيين كلمة المرور على بريدك الإلكتروني']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->update(['password' => $password]);
                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json(['message' => 'الرابط غير صالح أو منتهي الصلاحية'], 400);
        }

        return response()->json(['message' => 'تم إعادة تعيين كلمة المرور بنجاح']);
    }
}

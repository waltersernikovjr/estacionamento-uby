<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Models\Customer;
use App\Infrastructure\Persistence\Models\Operator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Email Verification Controller
 *
 * Handles email verification for both customers and operators
 */
final class EmailVerificationController extends Controller
{
    /**
     * Verify email address
     */
    public function verify(Request $request, string $id, string $hash)
    {
        $type = $request->query('type', 'customer');

        $user = $type === 'operator'
            ? Operator::findOrFail($id)
            : Customer::findOrFail($id);

        if (!hash_equals($hash, sha1($user->email))) {
            return view('email-invalid');
        }

        if ($user->email_verified_at !== null) {
            return view('email-already-verified', [
                'verified_at' => $user->email_verified_at->format('d/m/Y H:i:s')
            ]);
        }

        $user->email_verified_at = now();
        $user->save();

        Log::info("Email verified successfully", [
            'type' => $type,
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return view('email-verified', [
            'verified_at' => $user->email_verified_at->format('d/m/Y H:i:s')
        ]);
    }

    /**
     * Resend verification email
     */
    public function resend(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:customer,operator'
        ]);

        $type = $request->input('type');
        $email = $request->input('email');

        $user = $type === 'operator'
            ? Operator::where('email', $email)->first()
            : Customer::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        if ($user->email_verified_at !== null) {
            return response()->json([
                'message' => 'Email já está verificado.'
            ], 400);
        }

        $mailClass = $type === 'operator'
            ? \App\Infrastructure\Mail\WelcomeOperatorMail::class
            : \App\Infrastructure\Mail\WelcomeCustomerMail::class;

        \Illuminate\Support\Facades\Mail::to($user->email)
            ->send(new $mailClass($user));

        Log::info("Verification email resent", [
            'type' => $type,
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return response()->json([
            'message' => 'Email de verificação reenviado com sucesso.'
        ], 200);
    }
}

<?php

namespace App\Http\Services\User;

use App\Models\Group;
use App\Models\User;
use App\Models\UserField;
use App\Models\UserFieldUsers;
use App\Models\UserPhoneCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

class UserService
{
    public function appendFieldsAfterCreating($user): void
    {
        $userFields = UserField::whereUserKind('single')->get();
        $map = [];
        foreach ($userFields as $userField) {
            $map[] = [
                'user_id' => $user->id,
                'user_field_id' => $userField->id,
                'value' => null,
            ];
        }
        UserFieldUsers::query()->insert($map);
    }

    public function handleAppendDefaultGroup($user): void
    {
        $defaultGroup = Group::query()->where('is_default', true)->first();
        if (isset($defaultGroup))
            $user->update (['group_id' => $defaultGroup->id]);
    }

    public function checkCode($phone, $queryCode): bool
    {
        $candidateNote = UserPhoneCode::query()
            ->where('phone_number', $phone)
            ->where('is_used', false)
            ->where('created_at', '>', Carbon::now()->subMinutes(15))
            ->latest('created_at')
            ->first();

        if (isset($candidateNote)) {
            $code = (int)$candidateNote->code;
            $isAvailable = $queryCode === $code;
            if ($isAvailable) {
                $candidateNote->update(['is_used' => true]);
                return true;
            }
            return false;
        }
        return false;
    }

//    public function generateCodeAndSend($phone): bool
//    {
//        $code = UserPhoneCode::query()->create(['phone_number' => $phone]);
//        $isSent = $code->sendCode();
//        if ($isSent) {
//            return Response::json(['message' => 'Code was sent on your phone!']);
//        } else {
//            return Response::json(['message' => 'We could not sent code!']);
//        }
//    }
}

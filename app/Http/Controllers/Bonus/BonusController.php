<?php

namespace App\Http\Controllers\Bonus;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bonus\UpdateRequest;
use App\Models\Bonus;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class BonusController extends Controller
{
    public function update(UpdateRequest $request)
    {
        $data = $request->validated();

        if (isset($data['groups'])) {
            if ($data['groups'] === 'without_groups') {
                $availableGroups = 'without_groups';
                unset($data['groups']);
            } else if ($data['groups'] === 'all') {
                $availableGroups = 'all';
                unset($data['groups']);
            } else if (count($data['groups']) > 0) {
                $requestedGroups = $data['groups'];
                $availableGroups = 'selected';
                unset($data['groups']);
            }
        }


        if (isset($data['categories'])) {
            $requestedCategories = $data['categories'];
            unset($data['categories']);
        }
        $bonus = Bonus::first();

        try {
            DB::beginTransaction();
            $bonus->update([...$data, 'available_groups' => $availableGroups]);
            if (isset($requestedCategories)) {
                $bonus->categories()->sync($requestedCategories);
            }
            if (isset($requestedGroups)) {
                $bonus->groups()->sync($requestedGroups);
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return Response::json(['error' => $error->getMessage()]);
        }
        return $bonus->load('categories', 'groups');
    }
}

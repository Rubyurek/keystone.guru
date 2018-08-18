<?php

namespace App\Http\Requests;

use App\Models\Dungeon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DungeonRouteFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // \Auth::user()->hasRole(["user", "admin"]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dungeon_route_title' => 'required|string|min:3|max:255',
            // Only active dungeons are allowed
            'dungeon_id' => ['required', Rule::exists('dungeons', 'id'), Rule::in(Dungeon::active()->pluck('id')->toArray())],
            'faction_id' => ['required', Rule::exists('factions', 'id')],

            'race' => 'nullable|array',
            'class' => 'nullable|array',

            'race.*' => 'nullable|numeric',
            'class.*' => 'nullable|numeric',

            'unlisted' => 'nullable|int',
        ];
    }
}

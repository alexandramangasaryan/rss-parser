<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'program_title' => 'required|string',
            'redmine_url' => 'required|string',
            'redmine_api_key' => 'required|string',
            'redmine_project_id' => 'required|string',
            'telegram_chat_id' => 'required|string',
            'telegram_bot_token' => 'required|string',
        ])->validate();

        $setting = Setting::create([
            'program_title' => $request->program_title,
            'redmine_url' => $request->redmine_url,
            'redmine_api_key' => $request->redmine_api_key,
            'redmine_project_id' => $request->redmine_project_id,
            'telegram_chat_id' => $request->telegram_chat_id,
            'telegram_bot_token' => $request->telegram_bot_token,
        ]);

        return ['data' => $setting];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Validator::make($request->all(), [
            'program_title' => 'required|string',
            'redmine_url' => 'required|string',
            'redmine_api_key' => 'required|string',
            'redmine_project_id' => 'required|string',
            'telegram_chat_id' => 'required|string',
            'telegram_bot_token' => 'required|string',
        ])->validate();

        $settingToUpdate = Setting::where('id', $id)->first();

        $updatedData = $settingToUpdate->update($request->all());

        return ['data' => $updatedData];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Setting::where('id', $id)->delete();

        return ['Setting deleted'];
    }
}

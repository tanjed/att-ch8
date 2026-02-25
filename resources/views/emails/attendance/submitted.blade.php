<x-mail::message>
    # Automated Attendance Report

    Hello **{{ $setting->user->name }}**,

    Your scheduled action **{{ $setting->platformAction->name }}** for
    **{{ $setting->platformAction->platform->name }}** was executed.

    **Status:** {{ ucfirst($status) }}
    **Time:** {{ \Carbon\Carbon::parse($setting->target_time)->format('h:i A') }}

    @if($status === 'success')
        <x-mail::panel>
            ### Completed Successfully
            Your attendance action was submitted without any errors.
        </x-mail::panel>
    @else
        <x-mail::panel>
            ### Execution Failed
            The server responded with the following output:

            {{ substr(is_string($response) ? $response : json_encode($response), 0, 500) }}
        </x-mail::panel>
    @endif

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
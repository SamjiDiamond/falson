<x-mail::message>
    # Hello Admin

    {!! $message !!}

    <x-mail::button :url="$url">
        Login
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>

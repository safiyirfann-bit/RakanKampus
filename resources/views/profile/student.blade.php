<x-app-layout>

<div class="p-6">

    <h1 class="text-2xl font-bold">
        Student Profile
    </h1>

    <p>
        Name: {{ auth()->user()->name }}
    </p>

    <p>
        Email: {{ auth()->user()->email }}
    </p>

</div>

</x-app-layout>
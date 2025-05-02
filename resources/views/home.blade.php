@extends('app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Champions League Seasons</h1>
                @if(!$activeSeason)
                    <a href="{{ route('seasons.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create New Season
                    </a>
                @endif
            </div>

            @if($seasons->isEmpty())
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="text-center">
                            <h3 class="text-lg font-medium text-gray-900">Welcome to Champions League Manager</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                This application helps you manage Champions League seasons, teams, and matches. 
                                Create a new season to get started!
                            </p>
                            <div class="mt-5">
                                <a href="{{ route('seasons.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Create Your First Season
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($seasons as $season)
                        <div class="bg-white overflow-hidden shadow rounded-lg {{ $season->is_active ? 'ring-2 ring-blue-500' : '' }}">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-gray-900">Season {{ $season->year }}</h3>
                                    @if($season->is_active)
                                        <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                            Active
                                        </span>
                                    @endif
                                </div>
                                @if($season->winner)
                                    <div class="mt-4">
                                        <p class="text-sm text-gray-500">Winner:</p>
                                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $season->winner->name }}</p>
                                    </div>
                                @else
                                    <div class="mt-4">
                                        <p class="text-sm text-gray-500">No winner yet</p>
                                    </div>
                                @endif
                                <div class="mt-4">
                                    <a href="{{ route('seasons.show', $season) }}" class="text-sm text-blue-600 hover:text-blue-500">
                                        View Details →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 
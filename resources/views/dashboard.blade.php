@extends('layouts.index')

@section('content')

<div class="space-y-6">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-500 mt-1">Directory analytics and insights</p>
        </div>

        <div class="flex gap-3">
            <button class="flex items-center h-11 w-[200px] justify-start text-left font-normal bg-white border border-gray-200 rounded-lg shadow-sm p-3">
                <i class="far fa-calendar-alt mr-2 h-4 w-4 text-gray-500"></i>
                <span>From Date</span>
            </button>

            <button class="flex items-center h-11 w-[200px] justify-start text-left font-normal bg-white border border-gray-200 rounded-lg shadow-sm p-3">
                <i class="far fa-calendar-alt mr-2 h-4 w-4 text-gray-500"></i>
                <span>To Date</span>
            </button>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-l-red-600">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium text-gray-700">Total Directory (Alumni)</h3>
                <i class="fas fa-users h-5 w-5 text-red-600"></i>
            </div>
            <div class="pt-2">
                <div class="text-3xl font-bold text-red-600">50</div>
                <p class="text-xs text-gray-500 mt-1">All registered alumni</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-l-green-600">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium text-gray-700">Active Alumni</h3>
                <i class="fas fa-user-check h-5 w-5 text-green-600"></i>
            </div>
            <div class="pt-2">
                <div class="text-3xl font-bold text-green-600">15</div>
                <p class="text-xs text-gray-500 mt-1">Currently active members</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-l-red-600">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium text-gray-700">Blocked Alumni</h3>
                <i class="fas fa-user-times h-5 w-5 text-red-600"></i>
            </div>
            <div class="pt-2">
                <div class="text-3xl font-bold text-red-600">20</div>
                <p class="text-xs text-gray-500 mt-1">Temporarily blocked</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Top Alumni by Connections</h2>
                <p class="text-sm text-gray-500 mt-1">Alumni with the most network connections</p>
            </div>
            <i class="fas fa-chart-line h-6 w-6 text-red-600"></i>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                // Static data placeholder for top alumni based on your design
                $staticAlumni = [
                    ['name' => 'Alumni 22', 'year' => 2018, 'connections' => 145],
                    ['name' => 'Alumni 30', 'year' => 2015, 'connections' => 120],
                    ['name' => 'Alumni 8', 'year' => 2020, 'connections' => 110],
                    ['name' => 'Alumni 28', 'year' => 2019, 'connections' => 95],
                ];
            @endphp

            @foreach($staticAlumni as $alumni)
            <div class="bg-white p-4 border border-gray-200 rounded-lg hover:shadow-lg transition-shadow cursor-pointer">
                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="h-16 w-16 rounded-full border-4 border-red-600/20 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/alumni/' . str_replace(' ', '_', $alumni['name']) . '.jpg') }}" alt="{{ $alumni['name'] }}" class="h-full w-full object-cover">
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $alumni['name'] }}</h3>
                        <p class="text-sm text-gray-500">Year {{ $alumni['year'] }}</p>
                    </div>
                    <div class="flex items-center gap-2 text-red-600">
                        <i class="fas fa-user-friends h-4 w-4"></i>
                        <span class="text-lg font-bold">{{ $alumni['connections'] }}</span>
                        <span class="text-xs text-gray-500">connections</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Forum Statistics</h2>
                <p class="text-sm text-gray-500 mt-1">Overview of forum posts and engagement</p>
            </div>
            <i class="fas fa-comments h-6 w-6 text-red-600"></i>
        </div>

        <div class="grid gap-6 md:grid-cols-3 mb-6">
            
            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-l-green-600">
                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <h3 class="text-sm font-medium">Active Posts</h3>
                    <i class="fas fa-comment-alt h-5 w-5 text-green-600"></i>
                </div>
                <div class="pt-2">
                    <div class="text-3xl font-bold text-green-600">20</div>
                    <p class="text-xs text-gray-500 mt-1">Approved and published</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-l-yellow-600">
                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <h3 class="text-sm font-medium">Waiting for Approval</h3>
                    <i class="fas fa-clock h-5 w-5 text-yellow-600"></i>
                </div>
                <div class="pt-2">
                    <div class="text-3xl font-bold text-yellow-600">5</div>
                    <p class="text-xs text-gray-500 mt-1">Pending review</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-l-red-600">
                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <h3 class="text-sm font-medium">Rejected Posts</h3>
                    <i class="fas fa-times-circle h-5 w-5 text-red-600"></i>
                </div>
                <div class="pt-2">
                    <div class="text-3xl font-bold text-red-600">5</div>
                    <p class="text-xs text-gray-500 mt-1">Not approved</p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center gap-3 mb-4 pt-4 border-t border-gray-100">
                <i class="fas fa-trophy h-6 w-6 text-red-600"></i>
                <h3 class="text-xl font-bold text-gray-900">Highest Engagement Posts</h3>
            </div>

            @php
                // Static data placeholder for top engagement posts
                $topPosts = [
                    ['alumni' => 'Alumni A', 'title' => 'The Future of Abacus', 'date' => 'Oct 25, 2024', 'likes' => 95, 'comments' => 45, 'views' => 450, 'pinned' => true],
                    ['alumni' => 'Alumni B', 'title' => 'Tips for Speed Calculation', 'date' => 'Oct 20, 2024', 'likes' => 80, 'comments' => 30, 'views' => 380, 'pinned' => false],
                    ['alumni' => 'Alumni C', 'title' => 'Networking Event Ideas', 'date' => 'Oct 15, 2024', 'likes' => 70, 'comments' => 25, 'views' => 320, 'pinned' => false],
                ];
            @endphp
            
            <div class="grid gap-6 md:grid-cols-1 lg:grid-cols-3">
                @foreach($topPosts as $post)
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-full border-2 border-red-600/20 bg-gray-200 flex items-center justify-center font-bold">
                                {{ $post['alumni'][0] }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 truncate">{{ $post['alumni'] }}</h4>
                                <p class="text-xs text-gray-500">{{ $post['date'] }}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-bold text-gray-900 mb-2 line-clamp-1">{{ $post['title'] }}</h4>
                            <p class="text-sm text-gray-500 line-clamp-2">This is a detailed description of the forum post...</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            
                            <div class="flex items-center gap-2">
                                <div class="p-2 rounded-lg bg-red-500">
                                    <i class="fas fa-heart h-3 w-3 text-white"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Likes</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $post['likes'] }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="p-2 rounded-lg bg-blue-500">
                                    <i class="fas fa-comment-dots h-3 w-3 text-white"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Comments</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $post['comments'] }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <div class="p-2 rounded-lg bg-purple-500">
                                    <i class="fas fa-eye h-3 w-3 text-white"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Views</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $post['views'] }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <div class="p-2 rounded-lg bg-yellow-500">
                                    <i class="fas fa-thumbtack h-3 w-3 text-white"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Pinned</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $post['pinned'] ? "Yes" : "No" }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection
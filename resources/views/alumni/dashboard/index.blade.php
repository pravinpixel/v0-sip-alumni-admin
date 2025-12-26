@extends('alumni.layouts.index')

@section('content')
<style>
    .dashboard-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 40px;
    }

    .community-posts-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    /* Large screens (lg) - ≥992px */
    @media (min-width: 992px) {
        .dashboard-stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .community-posts-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Medium screens (md) - 768px to 991px */
    @media (min-width: 768px) and (max-width: 991px) {
        .dashboard-stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .community-posts-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
    }

    /* Small screens (sm) - 576px to 767px */
    @media (min-width: 576px) and (max-width: 767px) {
        .dashboard-stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .community-posts-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    /* Extra small screens (xs) - <576px */
    @media (max-width: 575px) {
        .dashboard-stats-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .community-posts-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }

    /* Community Highlights Responsive */
    @media (max-width: 991px) {
        .community-highlights-card {
            padding: 24px !important;
        }

        .community-title {
            font-size: 20px !important;
        }

        .community-subtitle {
            font-size: 13px !important;
        }
    }

    @media (max-width: 767px) {
        .community-highlights-card {
            padding: 20px !important;
        }

        .community-title {
            font-size: 18px !important;
        }

        .community-subtitle {
            font-size: 12px !important;
        }

        .community-header {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .view-all-btn {
            width: 100%;
            justify-content: center !important;
        }
    }

    @media (max-width: 575px) {
        .community-highlights-card {
            padding: 16px !important;
            border-radius: 12px !important;
        }

        .community-title {
            font-size: 16px !important;
        }

        .community-subtitle {
            font-size: 11px !important;
        }
    }

    /* Post Cards Responsive */
    .post-card {
        max-width: 100%;
        box-sizing: border-box;
    }

    @media (max-width: 991px) {
        .post-card {
            padding: 20px !important;
        }

        .post-title {
            font-size: 16px !important;
            min-height: 44px !important;
        }

        .post-description {
            font-size: 13px !important;
            min-height: 55px !important;
        }

        .author-name {
            font-size: 13px !important;
        }

        .post-stats {
            font-size: 13px !important;
            gap: 12px !important;
        }
    }

    @media (max-width: 767px) {
        .post-card {
            padding: 18px !important;
            border-radius: 12px !important;
        }

        .post-title {
            font-size: 15px !important;
            min-height: 40px !important;
        }

        .post-description {
            font-size: 12px !important;
            min-height: 50px !important;
        }

        .author-name {
            font-size: 12px !important;
        }

        .post-stats {
            font-size: 12px !important;
            gap: 10px !important;
        }

        .view-thread-btn {
            font-size: 13px !important;
            padding: 8px 14px !important;
        }
    }

    @media (max-width: 575px) {
        .post-card {
            padding: 16px !important;
            border-radius: 10px !important;
        }

        .post-title {
            font-size: 14px !important;
            min-height: 38px !important;
        }

        .post-description {
            font-size: 11px !important;
            min-height: 45px !important;
            -webkit-line-clamp: 3 !important;
        }

        .post-author {
            gap: 6px !important;
        }

        .post-author img,
        .post-author div {
            height: 28px !important;
            width: 28px !important;
        }

        .author-name {
            font-size: 11px !important;
        }

        .post-stats {
            font-size: 11px !important;
            gap: 8px !important;
        }

        .view-thread-btn {
            font-size: 12px !important;
            padding: 8px 12px !important;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; background: white; padding: 20px;">
    {{-- Header --}}
    <div style="margin-bottom: 32px;">
        <h1 style=" font-weight: 700; color: #111827; margin-bottom: 8px;" class="main-title">Alumni Dashboard</h1>
        <p style="color: #6b7280;" class="sub-title">Welcome back! Here's your activity overview</p>
    </div>

    {{-- Quick Stats --}}
    @php
    $quickStats = [
    [
    'title' => 'Connections Made',
    'value' => $stats['connectionsMade'] ?? 0,
    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-6 w-6 text-white">
        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
    </svg>',
    'bgColor' => 'linear-gradient(135deg, #E2001D, #B1040E)',
    'description' => 'Total accepted invites'
    ],
    [
    'title' => 'Pending Requests',
    'value' => $stats['pendingRequests'] ?? 0,
    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-6 w-6 text-white">
        <circle cx="12" cy="12" r="10"></circle>
        <polyline points="12 6 12 12 16 14"></polyline>
    </svg>',
    'bgColor' => 'linear-gradient(135deg, #F7C744, #E2001D)',
    'description' => 'Awaiting response'
    ],
    [
    'title' => 'Posts Created',
    'value' => $stats['postsCreated'] ?? 0,
    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-6 w-6 text-white">
        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
        <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
        <path d="M10 9H8"></path>
        <path d="M16 13H8"></path>
        <path d="M16 17H8"></path>
    </svg>',
    'bgColor' => 'linear-gradient(135deg, #B1040E, #E2001D)',
    'description' => 'Community contributions'
    ],
    [
    'title' => 'Total Engagement',
    'value' => $stats['totalEngagement'] ?? 0,
    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart h-6 w-6 text-white">
        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
    </svg>',
    'bgColor' => 'linear-gradient(135deg, #E2001D, #F7C744)',
    'description' => 'Likes & replies received'
    ]
    ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach($quickStats as $stat)
        <div class="col-6 col-lg-3">
            <div class="stat-card h-100">

                {{-- Icon --}}
                <div class="d-flex mb-3 position-relative">
                    <div class="stat-icon" style="background: {{ $stat['bgColor'] }}">
                        {!! $stat['icon'] !!}
                    </div>

                    {{-- Arrow --}}
                    <div class="position-absolute top-0 end-0 mt-2 me-2">
                        <i class="fa-solid fa-arrow-trend-up text-success"></i>
                    </div>
                </div>

                {{-- Content --}}
                <h3 class="stat-value">{{ $stat['value'] }}</h3>
                <p class="stat-title">{{ $stat['title'] }}</p>
                <p class="stat-desc">{{ $stat['description'] }}</p>

            </div>
        </div>
        @endforeach
    </div>


    {{-- Community Highlights --}}

    <!-- <div class="community-highlights-card" style="background: white; border-radius: 16px; border: 2px solid #e5e7eb; padding: 32px;">
        <div class="community-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <div>
                <h2 class="community-title" style="font-size: 24px; font-weight: 700; color: #111827;">Community Highlights</h2>
                <p class="community-subtitle" style="font-size: 14px; color: #6b7280; margin-top: 4px; margin-bottom: 0;">Trending posts from the community</p>
            </div>
            <a href="{{ route('alumni.forums') }}"
                class="view-all-btn"
                style="display: inline-flex; align-items: center; padding: 10px 16px; background: white; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-weight: 500; color: #374151; text-decoration: none; transition: all 0.3s; white-space: nowrap;"
                onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='white';">
                View All
                <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </a>
        </div>

        <div class="community-posts-grid">
            @forelse($topPosts as $post)
            <div class="post-card" style="background: white; border: 2px solid #e5e7eb; border-radius: 16px; padding: 24px; transition: all 0.3s; display: flex; flex-direction: column; height: 100%; overflow: hidden;"
                onmouseover="this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)';"
                onmouseout="this.style.boxShadow='none';">

                {{-- Title - Fixed height --}}
                <h3 class="post-title" style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 12px; min-height: 48px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; word-wrap: break-word;">
                    {{ strip_tags($post['title']) }}
                </h3>

                {{-- Description - Fixed height --}}
                <p class="post-description" title="{{ strip_tags($post['description']) }}" style="font-size: 14px; color: #6b7280; margin-bottom: 16px; line-height: 1.5; min-height: 60px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; word-wrap: break-word;">
                    {{ strip_tags($post['description']) }}
                </p>

                {{-- Author --}}
                <div class="post-author" style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px; min-width: 0;">
                    @if($post['show_profile_image'])
                    {{-- Connected or Own Post - Show profile image if available, otherwise show initials --}}
                    @if($post['profile_image'])
                    <img src="{{ $post['profile_image'] }}" alt="{{ $post['author'] }}"
                        style="height: 32px; width: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 2px solid #dc2626;">
                    @else
                    <div
                        style="height: 32px; width: 32px; border-radius: 50%; background: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: white; flex-shrink: 0;">
                        {{ $post['author_initials'] }}
                    </div>
                    @endif
                    @else
                    {{-- Not connected - show default blank avatar --}}
                    <img src="{{ asset('images/avatar/blank.png') }}" alt="{{ $post['author'] }}"
                        style="height: 32px; width: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 2px solid #d1d5db;">
                    @endif

                    {{-- Always show author name --}}
                    <span class="author-name" style="font-size: 14px; color: #374151; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0;">{{ $post['author'] }}</span>
                </div>

                {{-- Stats --}}
                <div class="post-stats"
                    style="display: flex; align-items: center; gap: 16px; font-size: 14px; color: #6b7280; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <i class="far fa-eye"></i>
                        <span>{{ $post['views'] }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <i class="far fa-heart"></i>
                        <span>{{ $post['likes'] }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <i class="far fa-comment"></i>
                        <span>{{ $post['comments'] }}</span>
                    </div>
                </div>

                {{-- Button - Push to bottom --}}
                <div style="margin-top: auto;">
                    <a href="{{ route('alumni.forums') }}?post={{ $post['id'] }}"
                        class="view-thread-btn"
                        style="width: 100%; display: inline-flex; align-items: center; justify-content: center; padding: 10px 16px; border: 2px solid #d1d5db; border-radius: 8px; font-size: 14px; font-weight: 500; color: #374151; background: white; text-decoration: none; transition: all 0.3s;"
                        onmouseover="this.style.backgroundColor='#f9fafb';"
                        onmouseout="this.style.backgroundColor='white';">
                        View Thread
                    </a>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #6b7280;">
                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                <p style="font-size: 16px; font-weight: 500;">No posts available yet</p>
                <p style="font-size: 14px; margin-top: 8px;">Be the first to create a post in the community!</p>
            </div>
            @endforelse
        </div>
    </div> -->

    <div class="community-highlights-card bg-white border rounded-4 p-4">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <div>
                <h2 class="fw-bold fs-4 text-dark mb-1">Community Highlights</h2>
                <p class="text-muted mb-0" style="font-size:14px;">
                    Trending posts from the community
                </p>
            </div>

            <a href="{{ route('alumni.forums') }}"
                class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                View All
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        {{-- Posts Grid --}}
        <div class="row g-4">
            @forelse($topPosts as $post)

            {{-- Responsive columns --}}
            <div class="col-12 col-md-4 col-lg-4">

                <div class="h-100 bg-white border rounded-4 p-3 d-flex flex-column
                        transition"
                    style="transition:all .3s;"
                    onmouseover="this.style.boxShadow='0 10px 25px rgba(0,0,0,.1)'"
                    onmouseout="this.style.boxShadow='none'">

                    {{-- Title --}}
                    <h3 class="fw-bold text-dark mb-2"
                    title="{{ strip_tags($post['title']) }}"
                        style="font-size:18px; min-height:48px;
                    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ strip_tags($post['title']) }}
                    </h3>

                    {{-- Description --}}
                    <p class="text-muted mb-3"
                    title="{{ strip_tags($post['description']) }}"
                        style="font-size:14px; min-height:60px;
                   display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ strip_tags($post['description']) }}
                    </p>

                    {{-- Author --}}
                    <div class="d-flex align-items-center gap-2 mb-3">
                        @if($post['show_profile_image'])
                        @if($post['profile_image'])
                        <img src="{{ $post['profile_image'] }}"
                            class="rounded-circle border border-danger"
                            style="width:32px;height:32px;object-fit:cover;">
                        @else
                        <div class="rounded-circle bg-danger text-white fw-bold
                                        d-flex align-items-center justify-content-center"
                            style="width:32px;height:32px;font-size:12px;">
                            {{ $post['author_initials'] }}
                        </div>
                        @endif
                        @else
                        <img src="{{ asset('images/avatar/blank.png') }}"
                            class="rounded-circle border"
                            style="width:32px;height:32px;">
                        @endif

                        <span class="fw-medium text-truncate" style="font-size:14px;">
                            {{ $post['author'] }}
                        </span>
                    </div>

                    {{-- Stats --}}
                    <div class="d-flex flex-wrap gap-3 text-muted mb-3 pb-3 border-bottom"
                        style="font-size:14px;">
                        <div><i class="far fa-eye me-1"></i>{{ $post['views'] }}</div>
                        <div><i class="far fa-heart me-1"></i>{{ $post['likes'] }}</div>
                        <div><i class="far fa-comment me-1"></i>{{ $post['comments'] }}</div>
                    </div>

                    {{-- Button --}}
                    <div class="mt-auto">
                        <a href="{{ route('alumni.forums') }}?post={{ $post['id'] }}"
                            class="btn btn-outline-secondary w-100">
                            View Thread
                        </a>
                    </div>

                </div>
            </div>

            @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="fas fa-inbox fs-1 mb-3 opacity-50"></i>
                <p class="fw-medium mb-1">No posts available yet</p>
                <p class="mb-0">Be the first to create a post!</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
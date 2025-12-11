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
                    'icon' => 'fa-users',
                    'bgColor' => '#dc2626',
                    'description' => 'Total accepted invites'
                ],
                [
                    'title' => 'Pending Requests',
                    'value' => $stats['pendingRequests'] ?? 0,
                    'icon' => 'fa-clock',
                    'bgColor' => '#f59e0b',
                    'description' => 'Awaiting response'
                ],
                [
                    'title' => 'Posts Created',
                    'value' => $stats['postsCreated'] ?? 0,
                    'icon' => 'fa-file-alt',
                    'bgColor' => '#dc2626',
                    'description' => 'Community contributions'
                ],
                [
                    'title' => 'Total Engagement',
                    'value' => $stats['totalEngagement'] ?? 0,
                    'icon' => 'fa-heart',
                    'bgColor' => '#f59e0b',
                    'description' => 'Likes & replies received'
                ]
            ];
        @endphp

        <div class="dashboard-stats-grid">
            @foreach($quickStats as $stat)
                <div style="background: white; border: 2px solid #e5e7eb; border-radius: 16px; padding: 24px; transition: all 0.3s; cursor: pointer; position: relative;"
                    onmouseover="this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)';"
                    onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                    
                    

                    {{-- Icon --}}
                    <div style="margin-bottom: 20px; display:flex">
                        <div
                            style="background: {{ $stat['bgColor'] }}; padding: 12px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: inline-block;">
                            <i class="fas {{ $stat['icon'] }}" style="color: white; font-size: 20px;"></i>
                        </div>
                        {{-- Arrow in top-right corner --}}
                        <div style="position: absolute; top: 20px; right: 20px;">
                             <i class="fa-solid fa-arrow-trend-up" style="color: #10b981; font-size: 16px;"></i>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div>
                        <h3 style="font-size: 36px; font-weight: 700; color: #111827; margin-bottom: 8px;">{{ $stat['value'] }}
                        </h3>
                        <p style="font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 4px;">{{ $stat['title'] }}
                        </p>
                        <p style="font-size: 13px; color: #6b7280; margin-bottom: 0;">{{ $stat['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Community Highlights --}}

        <div class="community-highlights-card" style="background: white; border-radius: 16px; border: 2px solid #e5e7eb; padding: 32px;">
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
                        <p class="post-description" style="font-size: 14px; color: #6b7280; margin-bottom: 16px; line-height: 1.5; min-height: 60px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; word-wrap: break-word;">
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
        </div>
    </div>
@endsection
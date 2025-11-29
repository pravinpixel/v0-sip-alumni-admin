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

        @media (max-width: 1200px) {
            .dashboard-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .community-posts-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dashboard-stats-grid {
                grid-template-columns: 1fr;
            }
            .community-posts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div style="max-width: 1400px; margin: 0 auto; background: white; padding: 20px;">
        {{-- Header --}}
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">Alumni Dashboard</h1>
            <p style="color: #6b7280; font-size: 15px;">Welcome back! Here's your activity overview</p>
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
                    'description' => 'Likes received'
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
                        <p style="font-size: 13px; color: #6b7280;">{{ $stat['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Community Highlights --}}

        <div style="background: white; border-radius: 16px; border: 2px solid #e5e7eb; padding: 32px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <div>
                    <h2 style="font-size: 24px; font-weight: 700; color: #111827;">Community Highlights</h2>
                    <p style="font-size: 14px; color: #6b7280; margin-top: 4px;">Trending posts from the community</p>
                </div>
                <a href="{{ route('alumni.forums') }}"
                    style="display: inline-flex; align-items: center; padding: 10px 16px; background: white; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-weight: 500; color: #374151; text-decoration: none; transition: all 0.3s;"
                    onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='white';">
                    View All
                    <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                </a>
            </div>

            <div class="community-posts-grid">
                @forelse($topPosts as $post)
                    <div style="background: white; border: 2px solid #e5e7eb; border-radius: 16px; padding: 24px; transition: all 0.3s; display: flex; flex-direction: column; height: 100%;"
                        onmouseover="this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)';"
                        onmouseout="this.style.boxShadow='none';">
                        
                        {{-- Title - Fixed height --}}
                        <h3 style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 12px; min-height: 10px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ Str::limit(strip_tags($post['title']), 25 ) }}
                        </h3>
                        
                        {{-- Description - Fixed height --}}
                        <p style="font-size: 14px; color: #6b7280; margin-bottom: 16px; line-height: 1.5; min-height: 60px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ Str::limit(strip_tags($post['description']), 120) }}
                        </p>

                        {{-- Author --}}
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
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
                            <span style="font-size: 14px; color: #374151; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $post['author'] }}</span>
                        </div>

                        {{-- Stats --}}
                        <div
                            style="display: flex; align-items: center; gap: 16px; font-size: 14px; color: #6b7280; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
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
                            <a href=""
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
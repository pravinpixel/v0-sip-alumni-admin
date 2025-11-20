@extends('alumni.layouts.index')

@section('content')
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
                    'value' => 24,
                    'icon' => 'fa-users',
                    'bgColor' => '#dc2626',
                    'description' => 'Total accepted invites'
                ],
                [
                    'title' => 'Pending Requests',
                    'value' => 5,
                    'icon' => 'fa-clock',
                    'bgColor' => '#f59e0b',
                    'description' => 'Awaiting response'
                ],
                [
                    'title' => 'Posts Created',
                    'value' => 12,
                    'icon' => 'fa-file-alt',
                    'bgColor' => '#dc2626',
                    'description' => 'Community contributions'
                ],
                [
                    'title' => 'Total Engagement',
                    'value' => 156,
                    'icon' => 'fa-heart',
                    'bgColor' => '#f59e0b',
                    'description' => 'Likes & replies received'
                ]
            ];
        @endphp

        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
            @foreach($quickStats as $stat)
                <div style="background: white; border: 2px solid #e5e7eb; border-radius: 16px; padding: 24px; transition: all 0.3s; cursor: pointer;"
                    onmouseover="this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)';"
                    onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px;">
                        <div
                            style="background: {{ $stat['bgColor'] }}; padding: 12px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <i class="fas {{ $stat['icon'] }}" style="color: white; font-size: 20px;"></i>
                        </div>
                        <i class="fas fa-trending-up" style="color: #10b981; font-size: 18px;"></i>
                    </div>
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
        @php
            $trendingPosts = [
                [
                    'id' => 1,
                    'title' => 'Future of AI in Education',
                    'description' => 'Exploring how artificial intelligence is transforming the learning experience...',
                    'author' => 'Rajesh Kumar',
                    'views' => 245,
                    'likes' => 42,
                    'comments' => 18
                ],
                [
                    'id' => 2,
                    'title' => 'Career Transition Tips',
                    'description' => 'Sharing my journey from software development to product management...',
                    'author' => 'Priya Sharma',
                    'views' => 189,
                    'likes' => 35,
                    'comments' => 12
                ],
                [
                    'id' => 3,
                    'title' => 'Alumni Meetup 2024',
                    'description' => 'Planning our annual alumni gathering in Bangalore. Join us for networking...',
                    'author' => 'Amit Patel',
                    'views' => 156,
                    'likes' => 28,
                    'comments' => 9
                ]
            ];
        @endphp

        <div style="background: white; border-radius: 16px; border: 2px solid #e5e7eb; padding: 32px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <div>
                    <h2 style="font-size: 24px; font-weight: 700; color: #111827;">Community Highlights</h2>
                    <p style="font-size: 14px; color: #6b7280; margin-top: 4px;">Trending posts from the community</p>
                </div>
                <a href="/alumni/forums"
                    style="display: inline-flex; align-items: center; padding: 10px 16px; background: white; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-weight: 500; color: #374151; text-decoration: none; transition: all 0.3s;"
                    onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='white';">
                    View All
                    <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                @foreach($trendingPosts as $post)
                    <div style="background: white; border: 2px solid #e5e7eb; border-radius: 16px; padding: 24px; transition: all 0.3s;"
                        onmouseover="this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)';"
                        onmouseout="this.style.boxShadow='none';">
                        <h3 style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 12px;">{{ $post['title'] }}
                        </h3>
                        <p style="font-size: 14px; color: #6b7280; margin-bottom: 16px; line-height: 1.5;">
                            {{ $post['description'] }}</p>

                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                            <div
                                style="height: 32px; width: 32px; border-radius: 50%; background: #d1d5db; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: #374151;">
                                {{ substr($post['author'], 0, 1) }}
                            </div>
                            <span style="font-size: 14px; color: #374151; font-weight: 500;">{{ $post['author'] }}</span>
                        </div>

                        <div
                            style="display: flex; align-items: center; gap: 16px; font-size: 14px; color: #6b7280; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-eye"></i>
                                <span>{{ $post['views'] }}</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-heart"></i>
                                <span>{{ $post['likes'] }}</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-comment"></i>
                                <span>{{ $post['comments'] }}</span>
                            </div>
                        </div>

                        <a href="/alumni/forums"
                            style="width: 100%; display: inline-flex; align-items: center; justify-content: center; padding: 10px 16px; border: 2px solid #d1d5db; border-radius: 8px; font-size: 14px; font-weight: 500; color: #374151; background: white; text-decoration: none; transition: all 0.3s;"
                            onmouseover="this.style.backgroundColor='#f9fafb';"
                            onmouseout="this.style.backgroundColor='white';">
                            View Thread
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
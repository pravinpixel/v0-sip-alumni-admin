@extends('alumni.layouts.index')

@section('content')
    <div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
        {{-- Header --}}
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
            <div>
                <h1 style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">Forum Posts</h1>
                <p style="color: #6b7280; font-size: 15px;">Share and engage with the SIP Academy community</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <button
                    style="background: #dc2626; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;"
                    onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                    Your Activity
                </button>
                <button
                    style="background: #dc2626; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;"
                    onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'" onclick="openCreatePostModal()">
                    <i class="fas fa-plus"></i>
                    Create Post
                </button>
            </div>
        </div>

        {{-- Search and Filter --}}
        <div style="display: flex; gap: 12px; margin-bottom: 24px;">
            <div style="flex: 1; position: relative;">
                <i class="fas fa-search"
                    style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                <input type="text" placeholder="Search posts..."
                    style="width: 100%; padding: 12px 16px 12px 45px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none;"
                    onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#e5e7eb'">
            </div>
            <button
                style="background: white; color: #374151; border: 2px solid #e5e7eb; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px;"
                onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                <i class="fas fa-filter"></i>
                Filter
            </button>
        </div>

        {{-- Forum Posts --}}
        @php
            $forumPosts = [
                [
                    'id' => 1,
                    'title' => 'Introduction to Abacus Training',
                    'description' => 'Excited to share insights about our new Abacus training program for young learners.',
                    'author' => 'Rajesh Kumar',
                    'authorInitial' => 'RK',
                    'date' => 'Mar 28, 2024 at 04:00 PM',
                    'tags' => ['Abacus', 'Training', 'Education'],
                    'views' => 245,
                    'likes' => 34,
                    'comments' => 12,
                    'pinned' => true
                ],
                [
                    'id' => 2,
                    'title' => 'Career Opportunities in EdTech',
                    'description' => 'Looking for talented individuals to join our growing team in the education technology sector.',
                    'author' => 'Priya Sharma',
                    'authorInitial' => 'PS',
                    'date' => 'Mar 27, 2024 at 02:30 PM',
                    'tags' => ['Career', 'EdTech', 'Hiring'],
                    'views' => 189,
                    'likes' => 28,
                    'comments' => 9,
                    'pinned' => false
                ],
                [
                    'id' => 3,
                    'title' => 'Alumni Meetup 2024 - Bangalore',
                    'description' => 'Planning our annual alumni gathering in Bangalore. Join us for networking and fun activities.',
                    'author' => 'Amit Patel',
                    'authorInitial' => 'AP',
                    'date' => 'Mar 26, 2024 at 11:15 AM',
                    'tags' => ['Event', 'Networking', 'Meetup'],
                    'views' => 156,
                    'likes' => 42,
                    'comments' => 15,
                    'pinned' => false
                ]
            ];
        @endphp

        @foreach($forumPosts as $post)
            <div style="background: white; border: 2px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 16px; transition: all 0.3s;"
                onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">

                {{-- Post Header --}}
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h2 style="font-size: 20px; font-weight: 700; color: #dc2626; margin: 0;">{{ $post['title'] }}</h2>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        @if($post['pinned'])
                            <span
                                style="background: #fbbf24; color: #78350f; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-thumbtack"></i>
                                Pinned
                            </span>
                        @endif
                        <button
                            style="background: transparent; border: none; color: #dc2626; cursor: pointer; padding: 4px 8px; font-size: 18px;">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                </div>

                {{-- Post Description --}}
                <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin-bottom: 16px;">{{ $post['description'] }}
                </p>

                {{-- Tags --}}
                <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;">
                    @foreach($post['tags'] as $tag)
                        <span
                            style="background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">{{ $tag }}</span>
                    @endforeach
                </div>

                {{-- Post Footer --}}
                <div
                    style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div
                            style="width: 32px; height: 32px; border-radius: 50%; background: #dc2626; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">
                            {{ $post['authorInitial'] }}
                        </div>
                        <div>
                            <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0;">{{ $post['author'] }}</p>
                            <p style="font-size: 12px; color: #6b7280; margin: 0;">{{ $post['date'] }}</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 16px; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 6px; color: #6b7280; font-size: 14px;">
                            <i class="fas fa-eye"></i>
                            <span>{{ $post['views'] }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; color: #6b7280; font-size: 14px;">
                            <i class="fas fa-heart"></i>
                            <span>{{ $post['likes'] }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; color: #6b7280; font-size: 14px;">
                            <i class="fas fa-comment"></i>
                            <span>{{ $post['comments'] }}</span>
                        </div>

                        <button
                            style="background: transparent; border: 2px solid #e5e7eb; color: #374151; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 6px;"
                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                            <i class="fas fa-heart"></i>
                            Like
                        </button>
                        <button
                            style="background: transparent; border: 2px solid #e5e7eb; color: #374151; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 6px;"
                            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                            <i class="fas fa-reply"></i>
                            Reply
                        </button>
                        <button
                            style="background: #dc2626; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;"
                            onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                            <i class="fas fa-eye"></i>
                            View Thread
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Include Create Post Modal -->
    @include('alumni.modals.create-post-modal')
@endsection

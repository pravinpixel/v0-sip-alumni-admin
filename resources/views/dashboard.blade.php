@extends('layouts.index')

@section('content')

<div class="space-y-6">

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.875rem; font-weight: 700; color: #111827; margin: 0;">Admin Dashboard</h1>
            <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">Directory analytics and insights</p>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <div style="position: relative;">
                <input type="date" id="fromDate" 
                    style="height: 2.75rem; width: 200px; padding: 0.75rem; padding-left: 2.5rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white; cursor: pointer;"
                    placeholder="From Date">
                <i class="far fa-calendar-alt" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280; pointer-events: none;"></i>
            </div>

            <div style="position: relative;">
                <input type="date" id="toDate" 
                    style="height: 2.75rem; width: 200px; padding: 0.75rem; padding-left: 2.5rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white; cursor: pointer;"
                    placeholder="To Date">
                <i class="far fa-calendar-alt" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280; pointer-events: none;"></i>
            </div>
        </div>
    </div>

    <div style="display: grid; gap: 1.5rem; grid-template-columns: repeat(3, 1fr);">

        <div
            style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 6px solid #dc2626; transition: box-shadow 0.3s;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div style="flex: 1;">
                    <h3 style="font-size: 0.875rem; font-weight: 500; color: #4b5563; margin-bottom: 1rem;">Total
                        Directory (Alumni)</h3>
                    <div style="font-size: 2.25rem; font-weight: 700; color: #dc2626; margin-bottom: 0.5rem;">50</div>
                    <p style="font-size: 0.75rem; color: #9ca3af;">All registered alumni</p>
                </div>
                <div
                    style="display: flex; align-items: center; justify-content: center; height: 2.5rem; width: 2.5rem; border-radius: 0.5rem; background-color: #fef2f2;">
                    <i class="fas fa-users" style="color: #dc2626;"></i>
                </div>
            </div>
        </div>

        <div
            style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 6px solid #16a34a; transition: box-shadow 0.3s;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div style="flex: 1;">
                    <h3 style="font-size: 0.875rem; font-weight: 500; color: #4b5563; margin-bottom: 1rem;">Active
                        Alumni</h3>
                    <div style="font-size: 2.25rem; font-weight: 700; color: #16a34a; margin-bottom: 0.5rem;">15</div>
                    <p style="font-size: 0.75rem; color: #9ca3af;">Currently active members</p>
                </div>
                <div
                    style="display: flex; align-items: center; justify-content: center; height: 2.5rem; width: 2.5rem; border-radius: 0.5rem; background-color: #f0fdf4;">
                    <i class="fas fa-user-check" style="color: #16a34a;"></i>
                </div>
            </div>
        </div>

        <div
            style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 6px solid #dc2626; transition: box-shadow 0.3s;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div style="flex: 1;">
                    <h3 style="font-size: 0.875rem; font-weight: 500; color: #4b5563; margin-bottom: 1rem;">Blocked
                        Alumni</h3>
                    <div style="font-size: 2.25rem; font-weight: 700; color: #dc2626; margin-bottom: 0.5rem;">20</div>
                    <p style="font-size: 0.75rem; color: #9ca3af;">Temporarily blocked</p>
                </div>
                <div
                    style="display: flex; align-items: center; justify-content: center; height: 2.5rem; width: 2.5rem; border-radius: 0.5rem; background-color: #fef2f2;">
                    <i class="fas fa-user-times" style="color: #dc2626;"></i>
                </div>
            </div>
        </div>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-top: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0;">Top Alumni by Connections
                </h2>
                <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">Alumni with the most network
                    connections</p>
            </div>
            <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #dc2626;"></i>
        </div>

        <div style="display: grid; gap: 1rem; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
            @php
                $topAlumni = [
                    ['name' => 'Alumni 2', 'year' => 2022, 'connections' => 148, 'image' => 'alumni2.jpg'],
                    ['name' => 'Alumni 21', 'year' => 2018, 'connections' => 148, 'image' => 'alumni21.jpg'],
                    ['name' => 'Alumni 27', 'year' => 2019, 'connections' => 146, 'image' => 'alumni27.jpg'],
                    ['name' => 'Alumni 50', 'year' => 2024, 'connections' => 146, 'image' => 'alumni50.jpg'],
                    ['name' => 'Alumni 15', 'year' => 2020, 'connections' => 145, 'image' => 'alumni15.jpg'],
                    ['name' => 'Alumni 33', 'year' => 2021, 'connections' => 144, 'image' => 'alumni33.jpg'],
                    ['name' => 'Alumni 8', 'year' => 2017, 'connections' => 143, 'image' => 'alumni8.jpg'],
                    ['name' => 'Alumni 42', 'year' => 2023, 'connections' => 142, 'image' => 'alumni42.jpg'],
                ];
            @endphp

            @foreach($topAlumni as $alumni)
                <div style="background: white; padding: 1.5rem; border: 1px solid #e5e7eb; border-radius: 0.75rem; text-align: center; transition: box-shadow 0.3s; cursor: pointer;"
                    onmouseover="this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                        <div
                            style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 3px solid #f3f4f6;">
                            <img src="{{ asset('images/alumni/' . $alumni['image']) }}" alt="{{ $alumni['name'] }}"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 700; color: #111827; margin: 0;">{{ $alumni['name'] }}
                            </h3>
                            <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">Year
                                {{ $alumni['year'] }}</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; justify-content: center;">
                            <i class="fas fa-user-friends" style="color: #dc2626; font-size: 0.875rem;"></i>
                            <span
                                style="font-size: 1.125rem; font-weight: 700; color: #dc2626;">{{ $alumni['connections'] }}</span>
                            <span style="font-size: 0.75rem; color: #9ca3af;">connections</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <div style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-top: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0;">Forum Statistics</h2>
                <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">Overview of forum posts and engagement</p>
            </div>
            <i class="fas fa-comments" style="font-size: 1.5rem; color: #dc2626;"></i>
        </div>

        <div style="display: grid; gap: 1.5rem; grid-template-columns: repeat(3, 1fr); margin-bottom: 2rem;">
            
            <div style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 6px solid #16a34a;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                    <div style="flex: 1;">
                        <h3 style="font-size: 0.875rem; font-weight: 500; color: #4b5563; margin-bottom: 1rem;">Active Posts</h3>
                        <div style="font-size: 2.25rem; font-weight: 700; color: #16a34a; margin-bottom: 0.5rem;">10</div>
                        <p style="font-size: 0.75rem; color: #9ca3af;">Approved and published</p>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: center; height: 2.5rem; width: 2.5rem; border-radius: 0.5rem; background-color: #f0fdf4;">
                        <i class="fas fa-comment-alt" style="color: #16a34a;"></i>
                    </div>
                </div>
            </div>

            <div style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 6px solid #f59e0b;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                    <div style="flex: 1;">
                        <h3 style="font-size: 0.875rem; font-weight: 500; color: #4b5563; margin-bottom: 1rem;">Waiting for Approval</h3>
                        <div style="font-size: 2.25rem; font-weight: 700; color: #f59e0b; margin-bottom: 0.5rem;">10</div>
                        <p style="font-size: 0.75rem; color: #9ca3af;">Pending review</p>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: center; height: 2.5rem; width: 2.5rem; border-radius: 0.5rem; background-color: #fef3c7;">
                        <i class="fas fa-clock" style="color: #f59e0b;"></i>
                    </div>
                </div>
            </div>

            <div style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 6px solid #dc2626;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                    <div style="flex: 1;">
                        <h3 style="font-size: 0.875rem; font-weight: 500; color: #4b5563; margin-bottom: 1rem;">Rejected Posts</h3>
                        <div style="font-size: 2.25rem; font-weight: 700; color: #dc2626; margin-bottom: 0.5rem;">10</div>
                        <p style="font-size: 0.75rem; color: #9ca3af;">Not approved</p>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: center; height: 2.5rem; width: 2.5rem; border-radius: 0.5rem; background-color: #fef2f2;">
                        <i class="fas fa-times-circle" style="color: #dc2626;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div style="border-top: 1px solid #f3f4f6; padding-top: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                <i class="fas fa-trophy" style="font-size: 1.5rem; color: #dc2626;"></i>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">Highest Engagement Posts</h3>
            </div>

            @php
                $topPosts = [
                    ['alumni' => 'Alumni 4', 'title' => 'Discussion Topic 4', 'date' => 'Jun 18, 2024', 'image' => 'alumni4.jpg', 'likes' => 74, 'comments' => 10, 'views' => 482, 'pinned' => true],
                    ['alumni' => 'Alumni 3', 'title' => 'Discussion Topic 3', 'date' => 'Jul 05, 2024', 'image' => 'alumni3.jpg', 'likes' => 68, 'comments' => 8, 'views' => 395, 'pinned' => false],
                    ['alumni' => 'Alumni 6', 'title' => 'Discussion Topic 6', 'date' => 'Jul 08, 2024', 'image' => 'alumni6.jpg', 'likes' => 55, 'comments' => 12, 'views' => 320, 'pinned' => false],
                ];
            @endphp

            <div style="display: grid; gap: 1rem; grid-template-columns: repeat(3, 1fr);">
                @foreach($topPosts as $post)
                    <div style="background: #fef2f2; padding: 2rem; border-radius: 1.5rem; border: 2px solid #fecaca;">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                            <div style="width: 70px; height: 70px; border-radius: 50%; overflow: hidden; border: 3px solid #dc2626;">
                                <img src="{{ asset('images/alumni/' . $post['image']) }}" 
                                     alt="{{ $post['alumni'] }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div>
                                <h4 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">{{ $post['alumni'] }}</h4>
                                <p style="font-size: 0.875rem; color: #9ca3af; margin: 0.25rem 0 0 0;">{{ $post['date'] }}</p>
                            </div>
                        </div>
                        
                        <h4 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin: 0 0 1rem 0;">{{ $post['title'] }}</h4>
                        <p style="font-size: 0.95rem; color: #9ca3af; margin: 0 0 1.5rem 0; line-height: 1.5;">This is a detailed description of the forum post 4. It contains valuable...</p>
                        
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 48px; height: 48px; border-radius: 0.75rem; background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-heart" style="color: white; font-size: 1.25rem;"></i>
                                </div>
                                <div>
                                    <p style="font-size: 0.875rem; color: #9ca3af; margin: 0;">Likes</p>
                                    <p style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">{{ $post['likes'] }}</p>
                                </div>
                            </div>
                            
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 48px; height: 48px; border-radius: 0.75rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-comment" style="color: white; font-size: 1.25rem;"></i>
                                </div>
                                <div>
                                    <p style="font-size: 0.875rem; color: #9ca3af; margin: 0;">Comments</p>
                                    <p style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">{{ $post['comments'] }}</p>
                                </div>
                            </div>
                            
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 48px; height: 48px; border-radius: 0.75rem; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-eye" style="color: white; font-size: 1.25rem;"></i>
                                </div>
                                <div>
                                    <p style="font-size: 0.875rem; color: #9ca3af; margin: 0;">Views</p>
                                    <p style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">{{ $post['views'] }}</p>
                                </div>
                            </div>
                            
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 48px; height: 48px; border-radius: 0.75rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-thumbtack" style="color: white; font-size: 1.25rem;"></i>
                                </div>
                                <div>
                                    <p style="font-size: 0.875rem; color: #9ca3af; margin: 0;">Pinned</p>
                                    <p style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">{{ $post['pinned'] ? 'Yes' : 'No' }}</p>
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

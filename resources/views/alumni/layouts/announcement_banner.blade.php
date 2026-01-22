@if(isset($announcements) && $announcements->count() > 0)
                <div class="announcements-global-container">
                    <div class="announcement-banner position-relative overflow-hidden">
                        
                        <!-- Scrolling Content -->
                        <div class="announcement-scroll-container position-relative" style="height: 45px; overflow: hidden;">
                            <div class="announcement-scroll-content position-absolute" style="white-space: nowrap; animation: scroll-announcements 80s linear infinite;">
                                @foreach($announcements as $index => $announcement)
                                    <span class="announcement-item">
                                        <span class="announcement-content">{{ $announcement->title }} :
                                            @if($announcement->description && strlen(strip_tags($announcement->description)) > 0)
                                                {{ \Illuminate\Support\Str::limit(strip_tags($announcement->description), 100) }}
                                            @endif
                                        </span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
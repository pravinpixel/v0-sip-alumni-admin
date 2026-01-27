@if(isset($announcements) && $announcements->count() > 0)
<div class="announcements-global-container">
    <div class="announcement-banner position-relative overflow-hidden">

        <!-- Scrolling Content -->
        <div class="announcement-scroll-container position-relative" style="height: 45px; overflow: hidden;">
            <div class="announcement-scroll-content" style="white-space: nowrap;">
                @foreach($announcements as $index => $announcement)
                <span class="announcement-item">
                    <span class="announcement-content">{{ $announcement->title }} :
                        {{ strip_tags($announcement->description) }}
                    </span>
                </span>
                @endforeach
                <!-- Duplicate content for seamless loop -->
                @foreach($announcements as $index => $announcement)
                <span class="announcement-item">
                    <span class="announcement-content">{{ $announcement->title }} :
                        {{ strip_tags($announcement->description) }}
                    </span>
                </span>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scrollContent = document.querySelector('.announcement-scroll-content');

        if (scrollContent) {
            // Get the total width of the content
            const contentWidth = scrollContent.scrollWidth / 2; // Divide by 2 because content is duplicated
            // Set speed: pixels per second (adjust this value - lower = slower, higher = faster)
            const pixelsPerSecond = 50; // Change this to control speed
            // Calculate duration based on content width
            const duration = contentWidth / pixelsPerSecond;
            // Apply the calculated duration
            scrollContent.style.animationDuration = duration + 's';
        }
    });
</script>
@endif
@if(isset($announcements) && $announcements->count() > 0)
<div class="announcement-scroll-container d-flex align-items-center px-4">

    <!-- Fixed Icon -->
    <div class="announcement-icon flex-shrink-0 me-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round"
            class="text-[#E2001D]">
            <path d="m3 11 18-5v12L3 14v-3z"></path>
            <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path>
        </svg>
    </div>

    <!-- Scrolling Area -->
    <div class="announcement-marquee flex-grow-1 overflow-hidden">
        <div class="announcement-scroll-content">

            @foreach($announcements as $announcement)
                <span class="announcement-item">
                    <span class="announcement-content">
                        {{ $announcement->title }} :
                        {{ strip_tags($announcement->description) }}
                    </span>
                </span>
            @endforeach

            @foreach($announcements as $announcement)
                <span class="announcement-item">
                    <span class="announcement-content">
                        {{ $announcement->title }} :
                        {{ strip_tags($announcement->description) }}
                    </span>
                </span>
            @endforeach

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